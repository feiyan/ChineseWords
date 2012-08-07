<?php
/**
 * @author: FeiYan.info
 */

if (!defined('IM_FEIYAN'))
{
    die('Hacking Attempt');
}

#root path
define("ROOTPATH",ereg_replace("[/\\]{1,}", '/', dirname(__FILE__))."/");
#DB (sqlite,mysql)
define( 'DB_TYPE', 'sqlite' );

#mysql configuration
define( 'MYSQL_SERVER', 'localhost' );
define( 'MYSQL_DATABASE', 'dbname' );
define( 'MYSQL_USER', 'dbuser' );
define( 'MYSQL_PASS', 'dbpass' );

#sqlite configuration
define("SQLITE_DSN","sqlite:".ROOTPATH."db/words.db");   		//数据库服务器路径
define("SQLITE_USERNAME","");    		//数据库用户名
define("SQLITE_PASSWD","");        	//数据库密码

#load functions
require_once ROOTPATH . 'functions.php';

$class = DB_TYPE;
$db = new $class();
unset($class);

/*init $db
if( defined(DB_TYPE)  ) {
	switch ( DB_TYPE )
	{
		case "sqlite": $db = new sqlite(); break;
		case "mysql": $db = new mysql(); break;
		default: $db = new sqlite();
	}
} else {
	$db = new sqlite();
}
**/
?>