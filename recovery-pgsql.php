<?php
function RenderRecovery() {
	include_once 'config-pgsql.php';
	$account = $_REQ['account'];
	$email = $_REQ['email'];
	$reqtype = $_REQ['reqtype'];

	if ( empty( $_REQ ) ) {
	echo <<<EOF
	<div>
	<fieldset>
	<legend>Password Reset</legend>
		<form action="" method="POST">
		<table>
			<tr><td align="right"><label for="account">Account Name:</label></td><td align="left"><input id="account" type="text" name="account" size="20" maxlength="32"/></td></tr>
			<tr><td align="right"><label for="email">E-Mail Address:</label></td><td align="left"><input id="email" type="text" name="email" length="20" maxlength="320"/></td></tr>
			<tr><td colspan="2" align="center"><input id="reqtype" type="hidden" name="reqtype" value="passwordreset"><input type="submit" value="Submit"/></td></tr>
		</table>
		</form>
		<h3>Please Note:</h3>
		<ul>
			<li>After submitting this form you will receive an e-mail with instructions on how to validate your request.
			<li>Only accounts with a registered e-mail address can be reset.
			<li>Accounts that have decayed due to inactivity are unrecoverable and cannot be reset.
			<li>Banned accounts cannot be reset.
			<li>You are limited to three reset requests every two hours, even if they are unsuccessful.
			<li>Your request will be silently ignored if invalid information is submitted, if you have any pending account requests, or if you exceed the number of allowed attempts.
			<li>Requests must be validated within 2 hours of receipt and automatically expire at server wars.
			<li>If your validation expires, submit a new request.
			<li>Any requests received when the server is down or during server wars are processed upon restart.
			<li>This system is heavily monitored. The IP address of every request and validation (successful or not) is logged.
			<li>Abuse of this system will result in being firewalled across the network.
		</ul>
	</fieldset>
	</div>
	<div>
	<fieldset>
	<legend>Forgot Account</legend>
		<form action="" method="POST">
		<table>
			<tr><td align="right"><label for="email">E-Mail Address:</label></td><td align="left"><input id="email" type="text" name="email" length="20" maxlength="320"/></td></tr>
			<tr><td colspan="2" align="center"><input id="reqtype" type="hidden" name="reqtype" value="accountnames"><input type="submit" value="Submit"/></td></tr>
		</table>
		</form>
	  <h3>Please Note:</h3>
		<ul>
			<li>After submitting this form you will receive an e-mail containing a list of account names registered with your e-mail address.
			<li>You are limited to one request per e-mail address and five requests in total every two hours.
			<li>Your request will be silently ignored if invalid information is submitted or if you exceed the number of allowed attempts.
			<li>Any requests received when the server is down or during server wars are processed upon restart.
			<li>This system is heavily monitored. The IP address of every request is logged.
			<li>Abuse of this system will result in being firewalled across the network.
		</ul>
	</fieldset>
	</div>
EOF;
		}
		elseif ( empty( $email ) || ( $reqtype == "passwordreset" && empty( $account ) ) ) {
	echo <<<EOF
	<div>
			<h2>Failure</h2>
			<ul>
				<li>All entries must be completed to process your request.
			</ul>
	</div>
EOF;
		}
		elseif ( strlen( $account ) > 32 || strlen( $email ) > 320 || !preg_match( '/^[a-z0-9.+_-]+@([a-z0-9-]+.)+[a-z]+$/i', $email ) ) {
	echo <<<EOF
	<div>
			<h2>Failure</h2>
			<ul>
				<li>The account name or e-mail address you entered is invalid.
				<li>Please ensure the entries are correct and try again.
			</ul>
	</div>
EOF;
		} elseif ( $reqtype == "passwordreset" &&
    ( !prepare($_db, "pwreset", 'SELECT Account FROM ResetValidation WHERE Account = $1 LIMIT 1') ||
    ($res = pg_execute("pwreset", array($account))) === false ||
    pg_num_rows($res) != 0 ) {
	echo <<<EOF
	<div>
			<h2>Failure</h2>
			<ul>
				<li>There is a validation pending for the account you entered.
				<li>Validate your request before making another.
			</ul>
	</div>
EOF;
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
      if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
        $ip = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
      }

			if ( $reqtype == "passwordreset" &&
      ( !pg_prepare($_db, "reset", 'INSERT INTO ResetRequest (Account, EMail, RequestedBy) VALUES($1, $2, $3)') ||
      ($res = pg_execute("reset", array($account, $email, $ip))) === false)) {
	echo <<<EOF
	<div>
				<h2>Failure</h2>
				<ul>
					<li>Your request could not be submitted.
				</ul>
	</div>
EOF;
			} elseif ( $reqtype == "accountnames" &&
      ( !pg_prepare($_db, "forgot", 'INSERT INTO ForgotAccount (EMail, RequestedBy) VALUES ($1, $2)') ||
      ($res = pg_execute("forgot", array($email, $ip))) === false)) {
	echo <<<EOF
	<div>
	<h2>Failure</h2>
	<ul>
		<li>Your request could not be submitted.
	</ul>
	</div>
EOF;
			} else {
	echo <<<EOF
	<div>
		<h2>Success</h2>
		<ul>
			<li>The request was successfully entered into the queue.
			<li>You will receive an e-mail shortly with instructions on how to validate your request if the information your provided is correct.
		</ul>
	</div>
EOF;
		}
	}
}

header("Access-Control-Allow-Origin: https://zenvera.com");
RenderRecovery();
?>
