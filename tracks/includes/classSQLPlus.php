<?php
class SQLPlus extends mysqli
{
    private $_sqlerr;
    private $_params;
    public $version = 1.0;

    function __construct($params)
    {
        $this->_params = $params;
        $connected = false;
        while (!$connected)
        {
            parent::__construct($params['hostname'],$params['username'],$params['password'],$params['dbname']);
            if ($this->connect_error)
            {
                if ($this->connect_errno == 2006)
                    sleep(1000);
                else
                {
                    error_log("Unable to connect to database " . $params['dbname']);
                    throw new Exception("SQL Connect error {$this->connect_error} [{$this->connect_errno}]");
                }
            }
            else
                $connected = true;
        }

    }

    function __destruct()
    {
        $this->close();
    }

    private function p_var_error_log( $object=null ,$additionaltext='')
    {
        ob_start();                    // start buffer capture
        var_dump( $object );           // dump the values
        $contents = ob_get_contents(); // put the buffer into a variable
        ob_end_clean();                // end capture
        error_log("{$additionaltext} {$contents}");        // log contents of the result of var_dump( $object )
    }

    protected function sqlError($q)
    {
        $this->_sqlerr = true;
        error_log("SQL Error in class SQLPlus: " . $this->error .  " Q: " . $q);
    }

    protected function sqlPrepareError($q)
    {
        $this->_sqlerr = true;
        error_log("SQL Prepare Error in class SQLPlus: " . $this->error .  " Q: " . $q);
    }

    public function singlequery($q)
    {
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r->fetch_array(MYSQLI_ASSOC);
    }

    public function p_singlequery($q,$types,...$params)
    {
        if ($s = $this->prepare($q) )
        {
            $s->bind_param($types,...$params);
            $s->execute();
            $r = $s->get_result();
            if (!$r) {$this->sqlError($q); return null;}
            return $r->fetch_array(MYSQLI_ASSOC);
        }
        else
            $this->sqlPrepareError($q);
        return null;
    }

    public function create($q)
    {
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return false;}
        return true;
    }

    public function p_create($q,$types,...$params)
    {
        if($s = $this->prepare($q))
        {
            if($s->bind_param($types,...$params) )
            {
                if (!$s->execute() )
                {
                    $this->sqlError($q);
                    return null;
                }
                return true;
            }
        }
        else
            $this->sqlPrepareError($q);
        return false;
    }

    public function update($q)
    {
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return false;}
        return true;
    }

    public function p_update($q,$types,...$params)
    {
        if($s = $this->prepare($q))
        {
            if($s->bind_param($types,...$params) )
            {
                if (!$s->execute() )
                {
                    $this->sqlError($q);
                    return null;
                }
                return true;
            }
        }
        else
            $this->sqlPrepareError($q);
        return false;
    }

    public function delete($q)
    {
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return false;}
        return true;
    }

    public function p_delete($q,$types,...$params)
    {
        if($s = $this->prepare($q))
        {
            if($s->bind_param($types,...$params) )
            {
                if (!$s->execute() )
                {
                    $this->sqlError($q);
                    return null;
                }
                return true;
            }
        }
        else
            $this->sqlPrepareError($q);
        return false;
    }

    public function p_all($q,$types,...$params)
    {
        if($s = $this->prepare($q))
        {
            if($s->bind_param($types,...$params) )
            {
                if ($s->execute() )
                {
                    $r = $s->get_result();
                    if (!$r) {$this->sqlError($q); return null;}
                    return $r;
                }
            }
        }
        return null;
    }

    public function alter($q)
    {
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return false;}
        return true;
    }

    public function getFromTable($table,$key,$id)
    {
        return $this->singlequery("select * from {$table} where {$key} = " . intval($id));
    }

    public function deleteFromTable($table,$key,$id)
    {
        return $this->delete("delete from {$table} where {$key} = " . intval($id));
    }

    public function allFromTable($table,$where='',$order='')
    {
       $q = "select * from ".$table." " . $where . " " . $order;
       $r = $this->query($q);
       if (!$r) {$this->sqlError($q); return null;}
       return $r;
    }

    public function update_from_array($table,$a,$whereclause)
    {
        $bstart = true;
        $q = "update " . $table . " set ";

        $keys = array_keys ($a);
        for($idx = 0;$idx < count($keys);$idx++)
        {
            if (!is_numeric($keys[$idx]) )
            {
                if (isset($a[$keys[$idx]]))
                {
                    if (!$bstart)
                        $q .= ",";
                    if (gettype($a[$keys[$idx]]) == 'boolean')
                    {
                        $q .= $keys[$idx] . " = ";
                        if ($a[$keys[$idx]])
                            $q .= "true";
                        else
                            $q .= "false";
                    }
                    else
                        $q .=  $keys[$idx] . " = '".$this->real_escape_string($a[$keys[$idx]])."'";
                    $bstart = false;
                }
            }
        }
        $q .= " " . $whereclause;
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return false;}
        return true;
    }

    public function p_update_from_array($table,$a,$whereclause)
    {
        $bstart = true;
        $q = "update " . $table . " set ";

        $types = '';
        $val = array();
        $cnt = 0;

        $keys = array_keys ($a);
        for($idx = 0;$idx < count($keys);$idx++)
        {
            if (!is_numeric($keys[$idx]) )
            {
                if (isset($a[$keys[$idx]]))
                {
                    if (!$bstart)
                        $q .= ",";
                    if (gettype($a[$keys[$idx]]) == 'boolean')
                    {
                        $q .= $keys[$idx] . " = ";
                        if ($a[$keys[$idx]])
                            $q .= "true";
                        else
                            $q .= "false";
                    }
                    else
                        $q .=  $keys[$idx] . " = ?";
                    switch (gettype($a[$keys[$idx]]))
                    {
                        case "double":
                            $types .= "d";
                            $val[$cnt] = floatval($a[$keys[$idx]]);
                            $cnt++;
                            break;
                        case "integer":
                            $types .= "i";
                            $val[$cnt] = intval($a[$keys[$idx]]);
                            $cnt++;
                            break;
                        case "string":
                            $types .= "s";
                            $val[$cnt] = $this->real_escape_string($a[$keys[$idx]]);
                            $cnt++;
                            break;
                        case "boolean":
                            break;
                        default:
                            $types .= "s";
                            $val[$cnt] = $this->real_escape_string($a[$keys[$idx]]);
                            $cnt++;
                            break;
                    }

                    $bstart = false;
                }
            }
        }


        $q .= " " . $whereclause;

        if ($cnt != strlen($types))
        {
            error_log("classSQLPlus ERROR in p_update_from_array count of bind params different from types");
            return false;
        }

        if (!$s = $this->prepare($q))
        {
            error_log("classSQLPlus ERROR in p_update_from_array Prepare error Q: {$q}");
            return false;
        }

        switch ($cnt)
        {
            case 0:
                break;
            case 1:
                $s->bind_param($types,$val[0]);
                break;
            case 2:
                $s->bind_param($types,$val[0],$val[1]);
                break;
            case 3:
                $s->bind_param($types,$val[0],$val[1],$val[2]);
                break;
            case 4:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3]);
                break;
            case 5:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4]);
                break;
            case 6:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5]);
                break;
            case 7:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6]);
                break;
            case 8:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7]);
                break;
            case 9:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8]);
                break;
            case 10:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9]);
                break;
            case 11:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10]);
                break;
            case 12:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11]);
                break;
            case 13:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12]);
                break;
            case 14:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13]);
                break;
            case 15:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14]);
                break;
            case 16:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15]);
                break;
            case 17:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16]);
                break;
            case 18:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17]);
                break;
            case 19:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18]);
                break;
            case 20:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19]);
                break;
            case 21:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19],$val[20]);
                break;
            case 22:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19],$val[20],$val[21]);
                break;
            case 23:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19],$val[20],$val[21],$val[22]);
                break;
            case 24:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19],$val[20],$val[21],$val[22],$val[23]);
                break;
            case 25:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19],$val[20],$val[21],$val[22],$val[23],$val[24]);
                break;
            default:
                error_log("classSQLPlus ERROR in p_update_from_array count too many parameters {$cnt}");
                return false;
                break;
        }

        if (!$s->execute())
        {
            error_log("classSQLPlus ERROR in p_update_from_array failed to execute count = {$cnt} types={$types}");
            $this->sqlError($q);
            return null;
        }
        return true;
    }

    public function create_from_array($table,$a)
    {
        $bstart = true;
        $q = "insert into " . $table . "(";

        $keys = array_keys ($a);
        for($idx = 0;$idx < count($keys);$idx++)
        {
            if (!is_numeric($keys[$idx]) )
            {
                if (isset($a[$keys[$idx]]))
                {
                    if (!$bstart)
                        $q .= ",";
                    $q .=  $keys[$idx];
                    $bstart = false;
                }
            }
        }

        $q .= ") values (";
        $bstart = true;
        for($idx = 0;$idx < count($keys);$idx++)
        {
            if (!is_numeric($keys[$idx]) )
            {
                if (isset($a[$keys[$idx]]))
                {
                    if (!$bstart)
                        $q .= ",";
                    if (gettype($a[$keys[$idx]]) == 'boolean')
                    {
                        if ($a[$keys[$idx]])
                            $q .= "true";
                        else
                            $q .= "false";
                    }
                    else
                        $q .=  "'".$this->real_escape_string($a[$keys[$idx]])."'";
                    $bstart = false;
                }
            }
        }
        $q .= ")";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return false;}
        return true;
    }

    public function p_create_from_array($table,$a)
    {
        $bstart = true;
        $q = "insert into " . $table . "(";

        $keys = array_keys ($a);
        for($idx = 0;$idx < count($keys);$idx++)
        {
            if (!is_numeric($keys[$idx]) )
            {
                if (isset($a[$keys[$idx]]))
                {
                    if (!$bstart)
                        $q .= ",";
                    $q .=  $keys[$idx];
                    $bstart = false;
                }
            }
        }

        $q .= ") values (";

        $bstart = true;
        $types = '';
        $val = array();
        $cnt = 0;

        for($idx = 0;$idx < count($keys);$idx++)
        {
            if (!is_numeric($keys[$idx]) )
            {
                if (isset($a[$keys[$idx]]))
                {
                    if (!$bstart)
                        $q .= ",";

                    if (gettype($a[$keys[$idx]]) == 'boolean')
                    {
                        if ($a[$keys[$idx]])
                            $q .= "true";
                        else
                            $q .= "false";
                    }
                    else
                    {
                        switch (gettype($a[$keys[$idx]]))
                        {
                            case "double":
                                $q .= "?";
                                $types .= "d";
                                $val[$cnt] = floatval($a[$keys[$idx]]);
                                $cnt++;
                                break;
                            case "integer":
                                $q .= "?";
                                $types .= "i";
                                $val[$cnt] = intval($a[$keys[$idx]]);
                                $cnt++;
                                break;
                            case "string":
                                $q .= "?";
                                $types .= "s";
                                $val[$cnt] = $this->real_escape_string($a[$keys[$idx]]);
                                $cnt++;
                                break;
                            case "boolean":
                                break;
                            default:
                                $q .= "?";
                                $types .= "s";
                                $val[$cnt] = $this->real_escape_string($a[$keys[$idx]]);
                                $cnt++;
                                break;
                        }
                    }
                    $bstart = false;
                }
            }
        }
        $q .= ")";

        if ($cnt != strlen($types))
        {
            error_log("classSQLPlus ERROR in p_create_from_array count of bind params different from types");
            return false;
        }

        if (!$s = $this->prepare($q))
        {
            error_log("classSQLPlus ERROR in p_create_from_array Prepare error Q: {$q}");
            return false;
        }

        switch ($cnt)
        {
            case 0:
                break;
            case 1:
                $s->bind_param($types,$val[0]);
                break;
            case 2:
                $s->bind_param($types,$val[0],$val[1]);
                break;
            case 3:
                $s->bind_param($types,$val[0],$val[1],$val[2]);
                break;
            case 4:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3]);
                break;
            case 5:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4]);
                break;
            case 6:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5]);
                break;
            case 7:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6]);
                break;
            case 8:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7]);
                break;
            case 9:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8]);
                break;
            case 10:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9]);
                break;
            case 11:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10]);
                break;
            case 12:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11]);
                break;
            case 13:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12]);
                break;
            case 14:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13]);
                break;
            case 15:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14]);
                break;
            case 16:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15]);
                break;
            case 17:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16]);
                break;
            case 18:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17]);
                break;
            case 19:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18]);
                break;
            case 20:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19]);
                break;
            case 21:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19],$val[20]);
                break;
            case 22:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19],$val[20],$val[21]);
                break;
            case 23:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19],$val[20],$val[21],$val[22]);
                break;
            case 24:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19],$val[20],$val[21],$val[22],$val[23]);
                break;
            case 25:
                $s->bind_param($types,$val[0],$val[1],$val[2],$val[3],$val[4],$val[5],$val[6],$val[7],$val[8],$val[9],$val[10],$val[11],$val[12],$val[13],$val[14],$val[15],$val[16],$val[17],$val[18],$val[19],$val[20],$val[21],$val[22],$val[23],$val[24]);
                break;
            default:
                error_log("classSQLPlus ERROR in p_create_from_array count too many parameters {$cnt}");
                return false;
                break;
        }

        if (!$s->execute())
        {
            error_log("classSQLPlus ERROR in p_create_from_array failed to execute count = {$cnt} types={$types}");
            $this->sqlError($q);
            return null;
        }
        return true;
    }


    public function BeginTransaction()
    {
        $this->_sqlerr = false;
        $this->autocommit(false);
    }

    public function TransactionError()
    {
        $this->_sqlerr = true;
    }

    public function EndTransaction()
    {
        if (!$this->_sqlerr)
            $this->commit();
        else
            $this->rollback();

        $this->autocommit(true);
    }

    public function BackupToFile($dir)
    {
        $ret = array();
        $filename = $dir;
        if (substr($dir, -1) != "/" )
            $filename .= "/";
        $filename .= date('YmdHis') . ".sql";
        $fred = 'crap';
        $return_var = 0;
        $ouput = array();

        $ret['dumprslt'] = exec("mysqldump --user={$this->_params['username']} --password='{$this->_params['password']}' --host={$this->_params['hostname']} {$this->_params['dbname']} > ".$filename);
        $ret['dir'] = $dir;
        $ret['file'] = $filename;
        $ret['status'] = $return_var;
        $ret['output'] = $ouput;
        return $ret;
    }

    public function loadFromSQL($filename)
    {
        $ret = array();
        $ret['rslt'] = exec("mysql -u{$this->_params['username']} -p{$this->_params['password']} -h{$this->_params['hostname']} {$this->_params['dbname']} < {$filename}");
        return $ret;
    }
}
?>