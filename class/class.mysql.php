<?php
if (!defined('IM_FEIYAN'))
{
    die('Hacking Attempt');
}

class mysql {
	
	protected $dbConnid = 0;
	    
    function __construct(){
    	$this->connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS, MYSQL_DATABASE);
    }
    
    function __destruct()
    {
        @mysql_close();
    }
    
    function connect($dbhost,$dbuser,$dbpw,$dbname = '',$charset='utf8',$pconnect = 0)
	{
		if ($pconnect) 
		{
			if (!($this->dbConnid=mysql_pconnect($dbhost,$dbuser,$dbpw))) {
				$this->err_msg('can not to pconnect mysql server "'.$dbhost.'"');
			}
		}
		else
		{
			if (!($this->dbConnid=mysql_connect($dbhost,$dbuser,$dbpw))) {
				$this->err_msg('can not to connect mysql server "'.$dbhost.'"');
			}
		}
		
		if ($charset&&$this->version()>'4.1') 
		{
			mysql_query("SET NAMES '".$charset."'" , $this->dbConnid);
		}
		
		if ($this->version()>'5.0.1') 
		{
			 mysql_query("SET sql_mode=''", $this->dbConnid);
		}
		
		if ($dbname) 
		{
			if (!mysql_select_db($dbname, $this->dbConnid) )
			{
				$this->err_msg('can not select db:'.$dbname);
			}
		}
		
		return $this->dbConnid;
	}
	
	function getone($sql)
    {
    	$res=$this->query($sql);
    	if ($res) 
    	{
    		$row=$this->fetch_array($res,MYSQL_NUM);
    		if ($row!==false) 
    		{
    			return $row["0"];
    		}
    		else 
    		{
    			return "";
    		}
    	}
    	else
    	{
    		return false;
    	}
    }

    function getrow( $sql )
    {
    	$res=$this->query($sql);
    	if ($res) 
    	{
    		$row=$this->fetch_array($res);
    		if ($row!==false) 
    		{
    			return $row;
    		}
    		else 
    		{
    			return '';
    		}
    	}
    	else
    	{
    		return false;
    	}
    }

    function getall( $sql )
    {
    	$res=$this->query($sql);
    	if ($res) 
    	{
    		$arr=array();
    		while ($row=$this->fetch_array($res)) 
    		{
    			$arr[]=$row;
    		}
    		return $arr;
    	}
    	else
    	{
    		return false;
    	}
    }
	
    function select_db($dbname)
    {
        return mysql_select_db($dbname, $this->dbConnid);
    }
    
    function query($sql,$type='')
    {
    	if (!($query=mysql_query($sql,$this->dbConnid))&&$type!='SILENT') 
    	{
    		$this->err_msg('there has a wrong when query sql:'.$sql);
    	}
    	$this->dbQueryCount++;
    	return $query;
    }
    
    function fetch_array($query,$result_type=MYSQL_ASSOC)
    {
    	return mysql_fetch_array($query,$result_type);
    }
    
    function affected_rows()
    {
        return mysql_affected_rows($this->dbConnid);
    }

    function error()
    {
        return mysql_error($this->dbConnid);
    }

    function errno()
    {
        return mysql_errno($this->dbConnid);
    }

    function result($query, $row)
    {
        return @mysql_result($query, $row);
    }

    function num_rows($query)
    {
        return mysql_num_rows($query);
    }

    function num_fields($query)
    {
        return mysql_num_fields($query);
    }

    function free_result($query)
    {
        return mysql_free_result($query);
    }
    
    function insert_id()
    {
    	return mysql_insert_id($this->dbConnid);
    }
    
    function fetchRow($query)
    {
        return mysql_fetch_assoc($query);
    }
    
	function version() 
	{
  		return mysql_get_server_info($this->dbConnid);
	}
	
	function close()
    {
        return mysql_close($this->dbConnid);
    }

	function err_msg($msg)
	{
		if ($msg) 
		{
			die($msg.'<br /> error:'.$this->error().'<br /> errno:'.$this->errno());
		}
		else 
		{
			die('mysql error');
		}
	}
	
}

?>