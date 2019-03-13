<?php
class SQLPlus extends mysqli 
{ 
    private $_sqlerr;
    
    function __construct($params)
    {
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
        echo "Clode DB";
        $this->close();
    }
    
    protected function sqlError($q)
    {
        $this->_sqlerr = true;
        error_log("SQL Error in class SQLPlus: " . $this->error .  " Q: " . $q);
    }
    
    public function singlequery($q)
    {
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r->fetch_array();
    }
    
    public function create($q)
    {
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return false;}
        return true;
    }
    
    public function update($q)
    {
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return false;}
        return true;
    }
    
    public function delete($q)
    {
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return false;}
        return true;
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
} 
?>