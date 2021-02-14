<?php
namespace devt\vault;
/**
*
* Class vault to manage a server side vault that stores keys in memory
*
* Class vault is used to retireve keys from a vault that is stored in the memory.  The
* same class is alos used to load a vault at startup, create keys and interface to the HSM
* on a seperate host which contains the master key.
* @author Tim Hogan
*/
class vault
{
    /** @var integer Contains the vault id */
    private $vaultid = null;
    /** @var string JSON content of the complete vault keys */
    private $encryptedkeys = null;
    /** @var array The vault data as loaded from the memory store */
    private $vault = null;
    /** @var string JSON content of access to the HSM */
    private $hsm_details = null;
    /** @var string of last error */
    private $lasterror = null;
    /** @var integer of number of hsm hosts */
    private $numhosts = null;

    /**
    *
    * The class constructor
    *
    * @param integer $vaultid
    * @param string  $hsmjson (optional) Provides a JSON string of the HSM parameters required to access the Master Key for
    *   encoding and decoding local keys.  Note this paraemter is only required on server startup to load the keys and when
    *   new keys are being intsalled.
    *   <code>
    *   {
    *      "accessType":  cert | key,       defines the access type to the HSM <br />
    *      "host": [HSM acess URL],         defines the pirmary host to the HSM <br />
    *      "host2": [HSM acess URL],        defines the secondary host to the HSM <br />
    *      "apikey": [HSM api key],         only used if accesstype = key <br/>
    *      "s": [HSM api key secret],       secret used to authenticate with HSM, only used with accesstype = key <br/>
    *      "keyfile": [private key file],   full path name to x.509 private key, only used with accesstype = cert <br/>
    *      "certfile": [certificate file],  full path name to x.509 signed sertificate, only used with accesstype = cert <br/>
    *   }
    *  </code>
    *
    */
    function __construct($vaultid,$hsmjson=null)
    {
        $this->vaultid = intval($vaultid);
        if ($hsmjson)
        {
            $this->hsm_details = json_decode($hsmjson,true);
            if (!isset($this->hsm_details['accessType']))
                error_log("vault::__construct Error: No accessType specified in hsm parameters");
            if (!isset($this->hsm_details['host']))
                error_log("vault::__construct Error: No host specified in hsm parameters");
            $this->numhosts = 1;
            if (isset($this->hsm_details['host2']))
                $this->numhosts = 2;
        }

        if ($r = @shmop_open ($this->vaultid,"a" ,0 , 0 ))
        {
            $str = shmop_read($r, 0,shmop_size ($r));
            shmop_close ($r);
            $this->vault = json_decode ($str,true);
        }
        else
            error_log("vault::__construct Cannot open vault for vaultid: {$vaultid}");
    }

    /**
    *
    * Checks to see if we have a vault
    *
    * @return true if a vault is loaded else false
    */
    public function haveVault()
    {
        if ($this->vault)
            return true;
        return false;
    }

    /**
    *
    * Creates a complete new vault
    *
    * Only called form addShelf when no vault exists.
    */
    private function createNewVault()
    {
        $this->lasterror = null;
        $dt = new \DateTime();
        $this->vault = ['vault' => [
                            'version' => 1.0,
                            'shelves' => [
                            ]
                      ]
            ];
        $this->vault['vault'] ['timestamp'] = $dt->format('Y-m-d H:i:s');
    }

    private function curlIt($url,$str,$usecert=true,$forceTLS1_2=false,$ignore_ssl=false)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$str);
        if ($ignore_ssl)
        {
            error_log("Curl attempt ignoring https SSL self signed cert");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        }
        if ($usecert)
        {
            echo "Using key file: {$this->hsm_details['keyfile']}\n";
            echo "Using cert file: {$this->hsm_details['certfile']}\n";

            curl_setopt($ch, CURLOPT_SSLCERT, $this->hsm_details['certfile']);
            curl_setopt($ch, CURLOPT_SSLKEY, $this->hsm_details['keyfile']);
            if ($forceTLS1_2)
                curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $result = curl_exec($ch);

        if (curl_errno($ch))
        {
            error_log("classVault:: Curl error code " . curl_errno($ch) . " " . curl_error($ch));
            return false;
        }
        curl_close($ch);

        if (!$result)
            return null;
        $result = json_decode($result,true);
        if (gettype($result) != "array")
        {
            error_log("classVault:: Error returned from HSM: Invalid JSON data");
            return null;
        }
        if (! isset($result['meta']) )
        {
            error_log("classVault:: Error returned from HSM: No meta");
            return null;
        }
        if (! isset($result['meta'] ['status']) )
        {
            error_log("classVault:: Error returned from HSM: No status in meta");
            return null;
        }
        if ($result['meta'] ['status'] != "OK")
        {
            $meta = $result['meta'];
            error_log("classVault:: Error returned from HSM: Request: {$meta['req']} Error Code: {$meta['errorcode']} Error Message: {$meta['errormsg']}");
            return null;
        }

        return $result;
    }

    /**
    *
    * Purges the current vault
    *
    * This routine purges the current vault from memory.
    * Note that it will not remove the ebcryted_keys which can be reloaded.
    */
    public function purgeVault()
    {
        $this->lasterror = null;
        if ($r = @shmop_open ($this->vaultid,"a" ,0 , 0 ))
        {
            shmop_delete($r);
        }
        $this->vault = null;
        $this->encryptedkeys = null;
    }

    /**
    *
    * Called to load the vault from the encryted keys
    *
    * This function takes the raw encrypted keys , decoed them vai the HSM
    * and loads them into the memory.
    *
    * @param string $encryptedkeys An encryted string that can only bde decoded by the HSM
    * @return bool True on success
    */
    public function load($encryptedkeys)
    {
        $this->lasterror = null;
        if ($this->vaultid && $encryptedkeys)
        {
            $this->rawkeys = $encryptedkeys;
            $keysjson = $this->decode($encryptedkeys);
            if ($keysjson)
            {
                $this->vault = json_decode ($keysjson,true);
                //Check to see if memory is available, if so we need to delete and re-create
                if ($r = @shmop_open ($this->vaultid,"a" ,0 , 0 ))
                {
                    shmop_delete($r);
                }

                if ($r = shmop_open ($this->vaultid,"c" , 0644 , strlen($keysjson) ))
                {
                    $rslt  = shmop_write($r,$keysjson,0);
                    return true;
                }
                else
                    echo "nValuate vault::load failed could not create mem file\n";
            }
            else
                echo "nValuate vault::load failed no decrypted keys\n";
        }
        else
            echo "nValuate vault::load failed with either no vaultid or encrypted keys\n";
        return false;
    }

    /**
    *
    * Called to encode keys with the HSM
    *
    * This function takes the keys , and encodes them with the HSM
    *
    *
    * @param string  $keys This is typically a stringafied JSON sey of key pairs
    * @return string $whatHSM 0 - Any: 1 - Primary: 2 - Secondary
    * @return string Returns an encrypted string or null on failure.
    */
    public function encode($keys,$whatHSM=0)
    {
        $this->lasterror = null;
        $postparam = array();
        $postparam['hsmdata'] = array();
        $postparam['hsmdata'] ['method'] = "encode";
        if ($this->hsm_details['accessType'] != "cert")
            $postparam['hsmdata'] ['secret'] = $this->hsm_details['s'];
        $postparam['hsmdata'] ['data'] = $keys;
        $str = json_encode($postparam);
        $usecert = $this->hsm_details['accessType'] == "cert";

        for ($hostidx = 0; $hostidx < $this->numhosts; $hostidx++)
        {
            $ignore_ssl = 0;
            $host = '';

            if ($whatHSM != 0)
            {
                if ($whatHSM == 1)
                    $host = $this->hsm_details['host'];
                if ($whatHSM == 2)
                    $host = $this->hsm_details['host2'];
                $hostidx = $this->numhosts;
            }
            else
            {
                switch ($hostidx)
                {
                    case 0:
                        $host = $this->hsm_details['host'];
                        break;
                    case 1:
                        $host = $this->hsm_details['host2'];
                        break;
                }
            }

            if ($this->hsm_details['accessType'] == "cert")
            {
                $url = $host . "/encode";
                $ignore_ssl = 1;
            }
            else
                $url = $host . "/" . $this->hsm_details['apikey'] . "/encode";

            $result = $this->curlIt($url,$str,$usecert,true,$ignore_ssl);

            if ($result)
            {
                if (isset($result['data']))
                {
                    $data = $result['data'];
                    if (isset($data['encodeddata']))
                        return $data['encodeddata'];
                    else
                        error_log("classVault:: No encodeddata in HSM response");
                }
                else
                    error_log("classVault:: No data in HSM response");

            }
        }
        return null;
    }

    private function decode($encryptedkeys,$whatHSM=0)
    {
        $this->lasterror = null;
        $postparam = array();
        $postparam['hsmdata'] = array();
        $postparam['hsmdata'] ['method'] = "decode";
        if ($this->hsm_details['accessType'] != "cert")
            $postparam['hsmdata'] ['secret'] = $this->hsm_details['s'];
        $postparam['hsmdata'] ['data'] = $encryptedkeys;
        $str = json_encode($postparam);
        $usecert = $this->hsm_details['accessType'] == "cert";

        for ($hostidx = 0; $hostidx < $this->numhosts; $hostidx++)
        {
            $ignore_ssl = 0;
            $host = '';

            if ($whatHSM != 0)
            {
                if ($whatHSM == 1)
                    $host = $this->hsm_details['host'];
                if ($whatHSM == 2)
                    $host = $this->hsm_details['host2'];
                $hostidx = $this->numhosts;
            }
            else
            {
                switch ($hostidx)
                {
                    case 0:
                        $host = $this->hsm_details['host'];
                        break;
                    case 1:
                        $host = $this->hsm_details['host2'];
                        break;
                }
            }

            if ($this->hsm_details['accessType'] == "cert")
            {
                $url = $host . "/decode";
                $ignore_ssl = 1;
            }
            else
                $url = $host . "/" . $this->hsm_details['apikey'] . "/decode";

            $result = $this->curlIt($url,$str,$usecert,true,$ignore_ssl);

            if ($result)
            {
                if (isset($result['data']))
                {
                    $data = $result['data'];
                    if (isset($data['decodeddata']))
                        return $data['decodeddata'];
                    else
                        error_log("classVault:: No decodeddata in HSM response");
                }
                else
                    error_log("classVault:: No data in HSM response");
            }
        }
        return null;
    }

    public function addShelf($shelf,$keys=null)
    {
        $this->lasterror = null;
        if (!$this->vault)
        {
            $this->createNewVault();
        }
        if (!isset($this->vault['vault'] ['shelves']))
            $this->vault['vault'] ['shelves'] = array();
        if ($keys)
            $this->vault['vault'] ['shelves'] [$shelf] = $keys;
        else
            $this->vault['vault'] ['shelves'] [$shelf] = array();

        if ($encodeddata = $this->encode($this->vault) )
        {
            if ($this->load($encodeddata) )
                return $encodeddata;
        }
        return null;
    }

    public function deleteShelf($shelf)
    {
        $this->lasterror = null;
        if (!$this->vault)
            return false;
        if (isset($this->vault['vault'] ['shelves']))
        {
            if (isset($this->vault['vault'] ['shelves'] [$shelf]))
            {
                unset($this->vault['vault'] ['shelves'] [$shelf]);
            }
            if ($encodeddata = $this->encode($this->vault) )
            {
                if ($this->load($encodeddata) )
                    return $encodeddata;
            }
        }
        return null;
    }

    public function listShelves()
    {
        $this->lasterror = null;
        if (!$this->vault)
            return null;
        if (isset($this->vault['vault'] ['shelves'] ))
        {
            $shelves = $this->vault['vault'] ['shelves'];
            return array_keys($shelves);
        }
        return null;
    }

    public function listKeys($shelf)
    {
        $this->lasterror = null;
        if (!$this->vault)
            return null;
        if (isset($this->vault['vault'] ['shelves'] ))
        {
            if (isset($this->vault['vault'] ['shelves'] [$shelf] ))
            {
                $list = $this->vault['vault'] ['shelves'] [$shelf];
                return array_keys($list);
            }
        }
        return null;
    }


    public function getVersion()
    {
        $this->lasterror = null;
        if (!$this->vault)
            return null;
        if (isset($this->vault['version']))
            return $this->vault['version'];
    }

    public function getKey($shelf,$key_name)
    {
        $this->lasterror = null;
        if (!$this->vault)
            return null;
        if (isset($this->vault['vault'] ['shelves'] ))
        {
            $shelves = $this->vault['vault'] ['shelves'];
            if (isset($shelves[$shelf]))
            {
                $theshelf = $shelves[$shelf];
                if (isset($theshelf[$key_name]))
                    return $theshelf[$key_name];
            }
        }
        return null;
    }

    public function addKey($shelf,$key_name,$value)
    {
        $this->lasterror = null;
        if (!$this->vault)
        {
            $this->lasterror = "classVault::addkey No vault has been loaded";
            error_log($this->lasterror);
            return null;
        }
        if (isset($this->vault['vault'] ['shelves'] ))
        {
            $shelves = $this->vault['vault'] ['shelves'];
            if (isset($shelves[$shelf]))
            {
                $theshelf = $shelves[$shelf];
                if (!isset($theshelf[$key_name]))
                {
                    $this->vault['vault'] ['shelves'] [$shelf] [$key_name] = $value;
                    if ($encodeddata = $this->encode($this->vault) )
                    {
                        if ($this->load($encodeddata) )
                            return $encodeddata;
                    }
                }
                else
                {
                    $this->lasterror = "classVault::addkey key already exists {$key_name}";
                    error_log($this->lasterror);
                }
            }
            else
            {
                $this->lasterror = "classVault::addkey shelf does not exist {$shelf}";
                error_log($this->lasterror);
            }
        }
        return null;
    }

    public function updateKey($shelf,$key_name,$value)
    {
        $this->lasterror = null;
        if (!$this->vault)
        {
            $this->lasterror = "classVault::updateKey No vault has been loaded";
            error_log($this->lasterror);
            return null;
        }
        if (isset($this->vault['vault'] ['shelves'] ))
        {
            $shelves = $this->vault['vault'] ['shelves'];
            if (isset($shelves[$shelf]))
            {
                $theshelf = $shelves[$shelf];
                if (isset($theshelf[$key_name]))
                {
                    $this->vault['vault'] ['shelves'] [$shelf] [$key_name] = $value;
                    if ($encodeddata = $this->encode($this->vault) )
                    {
                        if ($this->load($encodeddata) )
                            return $encodeddata;
                    }
                }
                else
                {
                    $this->lasterror = "classVault::updateKey key does not exist {$key_name}";
                    error_log($this->lasterror);
                }
            }
            else
            {
                $this->lasterror = "classVault::updatekey shelf does not exist {$shelf}";
                error_log($this->lasterror);
            }
        }
        return null;
    }

    public function deleteKey($shelf,$key_name)
    {
         $this->lasterror = null;
        if (!$this->vault)
        if (!$this->vault)
        {
            $this->lasterror = "classVault::deleteKey No vault has been loaded";
            error_log($this->lasterror);
            return null;
        }
        if (isset($this->vault['vault'] ['shelves'] ))
        {
            $shelves = $this->vault['vault'] ['shelves'];
            if (isset($shelves[$shelf]))
            {
                $theshelf = $shelves[$shelf];
                if (isset($theshelf[$key_name]))
                {
                    unset($this->vault['vault'] ['shelves'] [$shelf] [$key_name]);
                    if ($encodeddata = $this->encode($this->vault) )
                    {
                        if ($this->load($encodeddata) )
                            return $encodeddata;
                    }
                }
                else
                {
                    $this->lasterror = "classVault::deleteKey No key in shelf: {$key_name}";
                    error_log($this->lasterror);
               }
            }
            else
            {
                $this->lasterror = "classVault::deleteKey Invalid shelf in vault: {$shelf}";
                error_log($this->lasterror);
            }
        }
        else
        {
            $this->lasterror = "classVault::deleteKey No shelves in vault";
            error_log($this->lasterror);
        }
        return null;
    }

    private function checkTestData($d)
    {
        if ($d['test1'] != 'test1')
            return false;
        if ($d['test2'] ['sub1'] != 'sub1')
            return false;
        if ($d['test2'] ['sub2'] != 'sub2')
            return false;
        return true;
    }


    public function testHSM()
    {
        $testArray = [
                "test1" => 'test1',
                "test2" => [
                    "sub1" => 'sub1',
                    "sub2" => 'sub2'
                ]
            ];

        $keys = $this->encode($testArray);
        $rslt = $this->decode($keys);
        $rslt=json_decode($rslt,true);

        //Check the results

        if (!$rslt)
            return false;
        if ($rslt['test1'] != 'test1')
            return false;
        if ($rslt['test2'] ['sub1'] != 'sub1')
            return false;
        if ($rslt['test2'] ['sub2'] != 'sub2')
            return false;

        return true;

    }

    public function dumpAll()
    {
        $this->lasterror = null;
        if ($this->vault)
            var_dump($this->vault);
    }

    public function getLastError()
    {
        return $this->lasterror;
    }
}
?>