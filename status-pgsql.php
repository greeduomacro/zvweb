<?php
function GetStatus() {
	require 'config-pgsql.php';
	pg_prepare($_db,"status","select clients, items, mobiles, updated from status");

	$res = pg_execute($_db,"status", array());
	if ($res === false)
		return;

	return pg_fetch_row($res);
}

function PrintStatus() {
	$status = GetStatus();

	if (!$status) {
		echo "<span style=\"color: red;\">Unknown</span>";
		return;
	}

	list($clients, $items, $mobiles, $updated) = $status;

	$lastcheck = time() - strtotime($updated);

	if ($lastcheck > 70)
		echo "<span style=\"color: red;\">Unknown</span>";
	else
		echo "<span style=\"color:green;\">Online</span> - $clients Users";
}
?>

<?php	header("Access-Control-Allow-Origin: https://zenvera.com");
	PrintStatus();
?>
