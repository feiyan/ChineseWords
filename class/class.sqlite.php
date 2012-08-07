<?php

if (!defined('IM_FEIYAN'))
{
    die('Hacking Attempt');
}

class sqlite {
	private $db;
	
	function __construct() {
		$this->db = new PDO(SQLITE_DSN, SQLITE_USERNAME, SQLITE_PASSWD);
	}
	
	public function query($sql) {
		return $this->db->exec($sql);
	}
	
	public function getone( $sql )
	{
		$result = $this->db->query($sql)->fetch();
		if( count($result)>0 )
		{
			return $result[0];
		}
		return false;
	}
	
	public function getrow( $sql )
	{
		$result = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
		if( $result!==false )
		{
			return $result;
		}
		return false;
	}
	
	public function getall( $sql )
	{
		$result = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		if( $result!==false )
		{
			return $result;
		}
		return false;
	}
}

?>