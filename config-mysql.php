<?
	error_reporting(E_ALL & ~E_NOTICE);

	$_db = new mysqli("host", "user", "pw", "db");

	$_REQ = array();

	reset($_POST);
	while ( list($key,$data) = each($_POST) )
		$_REQ[$key] = addslashes($data);
	reset($_GET);
	while ( list($key,$data) = each($_GET) )
		$_REQ[$key] = addslashes($data);
?>
