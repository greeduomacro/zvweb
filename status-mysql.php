<?php
function GetStatus() {
	require 'config.php';
	$stmt = $_db->prepare("select clients, items, mobiles, updated from status");
	if (!$stmt->execute())
		return;

	$out_clients = NULL;
	$out_items = NULL;
	$out_mobiles = NULL;
	$out_updated = NULL;
	$stmt->bind_result($out_clients, $out_items, $out_mobiles, $out_updated);
	if (!$stmt->fetch())
        	return;

	$stmt->close();
	return array($out_clients, $out_items, $out_mobiles, $out_updated);
}

function PrintStatus() {
	$status = GetStatus();

	if (!$status)
		echo "<span style=\"color: red;\">Unknown</span>";

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
