<?php
include_once 'config.php';
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
echo "$out_clients $out_items $out_mobiles $out_updated";
?>
