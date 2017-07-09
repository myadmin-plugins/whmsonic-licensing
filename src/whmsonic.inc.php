<?php
/**
 * Licensing Functionality
 * Last Changed: $LastChangedDate: 2017-05-25 13:24:31 -0400 (Thu, 25 May 2017) $
 * @author detain
 * @copyright 2017
 * @package MyAdmin
 * @category Licenses
 */

/**
 * activate_whmsonic()
 * activate a whmsonic license on the given ip.
 *
 * license can be one of the following:
 * 		Yearly License
 * 		Monthly License
 * 		LifeTime License
 *
 * @param string $licenseip ip address to license
 * @param string $license the license type
 * @param integer $orderid order id associated with the purchase
 * @param string $clientName clients full name
 * @param string $clientEmail client email address
 * @return string "success" if ok , otherwise it returns the error
 */
function activate_whmsonic($licenseip, $license, $orderid, $clientName, $clientEmail) {
	/*
	* // License Type & Client Details
	* $license = ""; // License Type    Example; $license = "$ipAddress_from_my_website_form";
	* $licenseip = ""; // LicenseIP, mainserverip of the dedicated server or VPS.
	* $clientEmail = ""; // Must be filled
	* $clientName = ""; // Must be filled
	*/
	$resellerusername = WHMSONIC_USERNAME;
	$resellerpassword = WHMSONIC_PASSWORD;
	//    $orderid = ""; // The orderID or saleID, this is very useful for manual updates etc.. It will be stored on our side in the database as well.

	// Code to perform action goes here...

	$url = 'http://www.whmsonic.com/api/action.php?';
	$fieldstring = "cmd=create&username=$resellerusername&upass=$resellerpassword&license=$license&licenseip=$licenseip&client_email=$clientEmail&client_name=$clientName&orderid=$orderid&spamprotection=654a65z4a9AAQZloqe";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 59);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$retval = curl_exec($ch);
	if (curl_errno($ch)) {
		$retval = 'CURL Error: '.curl_errno($ch).' - '.curl_error($ch);
	}
	curl_close($ch);

	if ($retval == 'Complete') {
		$result = 'success';
	} else {
		$result = "<br>$retval";
	}
	return $result;
}

/**
 * whmsonic_terminate()
 * terminate a whmsonic license
 *
 * @param string $licenseip ip address to terminate
 * @return string "success" if ok , otherwise it returns the error
 */
function whmsonic_terminate($licenseip) {
// License Type & Client Details
	//    $licenseip = ""; // LicenseIP, mainserverip of the dedicated server or VPS.
	$resellerusername = WHMSONIC_USERNAME;
	$resellerpassword = WHMSONIC_PASSWORD;

	// Code to perform action goes here...

	$url = 'http://www.whmsonic.com/api/action.php?';
	$fieldstring = "cmd=terminate&username=$resellerusername&upass=$resellerpassword&licenseip=$licenseip&spamprotection=654a65z4a9AAQZloqe";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 59);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$retval = curl_exec($ch);
	if (curl_errno($ch)) {
		$retval = 'CURL Error: '.curl_errno($ch).' - '.curl_error($ch);
	}
	curl_close($ch);

	if ($retval == 'Complete') {
		$result = 'success';
	} else {
		$result = "<br>$retval";
	}
	return $result;
}

/**
 * whmsonic_suspend()
 * suspend a whmsonic license
 *
 * @param string $licenseip ip address to suspend
 * @return string "success" if ok , otherwise it returns the error
 */
function whmsonic_suspend($licenseip) {
// License Type & Client Details
	//    $licenseip = ""; // LicenseIP, mainserverip of the dedicated server or VPS.
	$resellerusername = WHMSONIC_USERNAME;
	$resellerpassword = WHMSONIC_PASSWORD;
	// Code to perform action goes here...

	$url = 'http://www.whmsonic.com/api/action.php?';
	$fieldstring = "cmd=suspend&username=$resellerusername&upass=$resellerpassword&licenseip=$licenseip&spamprotection=654a65z4a9AAQZloqe";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 59);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$retval = curl_exec($ch);
	if (curl_errno($ch)) {
		$retval = 'CURL Error: '.curl_errno($ch).' - '.curl_error($ch);
	}
	curl_close($ch);

	if ($retval == 'Complete') {
		$result = 'success';
	} else {
		$result = "<br>$retval";
	}
	return $result;
}

/**
 * whmsonic_unsuspend()
 * unsuspend a whmsonic license
 *
 * @param string $licenseip ip address to unsuspend
 * @return string "success" if ok , otherwise it returns the error
 */
function whmsonic_unsuspend($licenseip) {

	// License Type & Client Details
	//    $licenseip = ""; // LicenseIP, mainserverip of the dedicated server or VPS.
	$resellerusername = WHMSONIC_USERNAME;
	$resellerpassword = WHMSONIC_PASSWORD;

	// Code to perform action goes here...

	$url = 'http://www.whmsonic.com/api/action.php?';
	$fieldstring = "cmd=unsuspend&username=$resellerusername&upass=$resellerpassword&licenseip=$licenseip&spamprotection=654a65z4a9AAQZloqe";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 59);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$retval = curl_exec($ch);
	if (curl_errno($ch)) {
		$retval = 'CURL Error: '.curl_errno($ch).' - '.curl_error($ch);
	}
	curl_close($ch);

	if ($retval == 'Complete') {
		$result = 'success';
	} else {
		$result = "<br>$retval";
	}
	return $result;
}

/**
 * whmsonic_list()
 * list the whmsonic licenses
 *
 * @todo possibly return the output instead of just echoing it
 *
 * type can be any of the following:
 * 		1=Yearly Licenses
 * 		2=LifeTime Licenses
 * 		3=Monthly Licenses
 * 		4=All Licenses (default)
 *
 * @param integer $type optional type of license list to get, defaults to 4 (all licenses)
 * @return void
 */
function whmsonic_list($type = 4) {
	// SECURITY WARNING: You may not run this script directly under your website,
	// you may use it under the password protected folder, encode the script or change the file name in order to protect your clients serverips.

	$resellerusername = WHMSONIC_USERNAME; // Your WHMSonic reseller username
	$resellerpassword = WHMSONIC_PASSWORD; // Your WHMSonic reseller password

	$url = 'http://www.whmsonic.com/api/list.php?';
	$fieldstring = "cmd=list&username=$resellerusername&upass=$resellerpassword&type=$type&spamprotection=654a65z4a9AAQZloqe";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 59);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$retval = curl_exec($ch);
	if (curl_errno($ch)) {
		$retval = 'CURL Error: '.curl_errno($ch).' - '.curl_error($ch);
	}
	curl_close($ch);
	if ($retval == 'You dont have a registered  license')
		$licenses = [];
	else {
		$licenses = implode("\n", $retval);
	}
	return $licenses;
}

/**
 * whmsonic_verify()
 * verifys a whmsonic license,
 * it seems $clientserverIP somehow needs to be known and global before its called??
 * @todo rewrite this to use arguments and return stuff instead of echo
 *
 * @return void
 */
function whmsonic_verify($clientserverIP) {
	// Request this variable $clientserverIP from the client to check if the license is valid or not.
	// This must be a mainIP of the server, let the client submit their licensed mainserverIP via form from your website
	// and send the form data to this page to verify.

	$url = 'http://www.whmsonic.com/verify2.php'; // Manual Test: http://www.whmsonic.com/verify2.php?ip=67.228.53.90 will return yes. Change the ip to no.
	$fieldstring = "ip=$clientserverIP";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 59);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$retval = curl_exec($ch);
	if (curl_errno($ch)) {
		$retval = 'CURL Error: '.curl_errno($ch).' - '.curl_error($ch);
	}
	curl_close($ch);

	if ($retval == 'yes') {
		echo 'ok'; // Do the things here you need. The client has a valid license.
	} else {
		echo 'failed'; // Tell the client that they don't have a valid license.
	}
}
