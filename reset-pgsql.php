<html>
<head>
<title>Zenvera Password Reset</title>
</head>
<body>
<?php
	include_once "config-pgsql.php";

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
		$account = urldecode($account);
		$ip = $_SERVER['REMOTE_ADDR'];
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
    			$ip = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
		}

		pg_prepare($_db,"validate","UPDATE ResetValidation SET Validated = 1, ValidatedBy = $1 WHERE Account = $2 AND ValidationKey = $3 AND (NOW() < Expiration) LIMIT 1");

		$res = pg_execute("validate", array($ip, $account, $key));
		if ( $res === false || pg_affected_rows($res) != 1 ) {
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
	}
?>
</body>
</html>
