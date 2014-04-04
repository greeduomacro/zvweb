<?php
include_once 'config-pgsql.php';
pg_prepare($_db,"stats","select clients, items, mobiles, updated from status");
$res = pg_execute($_db,"stats",array());
if ($res === false)
	die();

$s = pg_fetch_row($res);

echo "$s[0] $s[1] $s[2] $s[3]";
?>
