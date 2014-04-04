<html>
<head>
<title>Zenvera Password Reset</title>
</head>
<body>
<?php
	include_once "config.php";

	$account = $_REQ['account'];
	$key     = $_REQ['key'];

	if ( empty( $_REQ ) || empty( $account ) || empty( $key ) || strlen( $account ) > 32 || strlen( $key ) != 32 ) {
?>
		<h2>Validation Failed</h2>
		<ul>
			<li>Please ensure the URL is the same as the one provided in the e-mail you received.
		</ul>
<?
	}
	else {
		$account = urldecode( $account );
		$ip = $_SERVER['REMOTE_ADDR'];

		$stmt = $_db->prepare("UPDATE ResetValidation SET Validated = 1, ValidatedBy = ? WHERE Account = ? AND ValidationKey = ? AND (NOW() < Expiration) LIMIT 1");

		$stmt->bind_param("sss", $ip, $account, $key);
		if ( !$stmt->execute() || $_db->affected_rows != 1 ) {
?>
			<h2>Validation Failed</h2>
			<ul>
				<li>Either the information provided is incorrect, the request has expired, or the request has already been validated.
				<li>Please ensure the URL is the same as the one provided in the e-mail you received.
			</ul>
<?
		}
		else {
?>
			<h2>Validation Successful</h2>
			<ul>
				<li>Your new password will be sent to you shortly.
			</ul>
<?
		}
		$stmt->close();
	}
?>
</body>
</html>
