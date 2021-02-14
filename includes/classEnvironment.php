<?php
require_once  dirname(__FILE__) . "/classVault.php";
class Environment
{
    private $vault = null;
    private $shelfname = null;
    private $primaryDBParams = null;

    function __construct($shelf=null,$vault_id=null)
    {
        if ($shelf && strlen($shelf) > 0)
            $this->shelfname = $shelf;
        else
            $this->shelfname = getenv("VAULT_SHELF");
        if (! $this->shelfname || strlen($this->shelfname) == 0)
            error_log("Environment::_construct No INSTALLATION_NAME defined as an apache environment variable check apache2 site conf file for SetEnv");

        if ($vault_id)
            $vaultid = $vault_id;
        else
            $vaultid = intval(getenv("VAULTID"));
        if (! $vaultid)
            error_log("Environment::_construct No VAULTID defined as an apache environment variable check apache2 site conf file for SetEnv");
        $this->vault = new devt\vault\vault($vaultid);
        if ($this->vault)
        {
            $this->primaryDBParams = array();
            $this->primaryDBParams['dbname'] = $this->getkey("DATABASE_NAME");
            $this->primaryDBParams['username'] = $this->getkey("DATABASE_USER");
            $this->primaryDBParams['password'] = $this->getkey("DATABASE_PW");
            $this->primaryDBParams['hostname'] = $this->getkey("DATABASE_HOST");
        }
    }

    public function getkey($keyname)
    {
        return $this->vault->getKey($this->shelfname,$keyname);
    }

    public function getDatabaseParameters()
    {
        return $this->primaryDBParams;
    }

    public function dumpAll()
    {
        echo "Gloabl Conf:\n";
        echo "Vault\n";
        $this->vault->dumpAll();
    }
}
$devt_environment = new Environment();
