<?
	error_reporting(E_ALL & ~E_NOTICE);

	$_db = pg_connect("host= port= dbname= user= password= sslmode=require options='--client_encoding=UTF8'") or die('Could not connect: ' . pg_last_error());

	$_REQ = array();

	reset($_POST);
	while ( list($key,$data) = each($_POST) )
		$_REQ[$key] = addslashes($data);
	reset($_GET);
	while ( list($key,$data) = each($_GET) )
		$_REQ[$key] = addslashes($data);
?>
