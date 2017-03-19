<?php

/**
 * Zabbix monitoring in PHP
 *
 * @author Karol Preiskorn
 *
 * @version 2017-04-16 KP init
 * @version 17.03.2017 14:47:16 Karol Preiskorn dodanie informacji na temat Alertów
 *
 * phpinfo ();
 */
$uri = "http://wrex.oss.t-mobile.pl/zabbix/api_jsonrpc.php";
$username = "inact_ro";
$password = "Karol!123";
/**
 *
 * @param unknown $array
 */
function expand_arr($array) {
	foreach ( $array as $key => $value ) {
		if (is_array ( $value )) {
			echo "<i>" . $key . "</i>:<br>";
			expand_arr ( $value );
			echo "<br>\n";
		} else {
			echo "<i>" . $key . "</i>: " . $value . "<br>\n";
		}
	}
}
/**
 *
 * @param unknown $uri
 * @param unknown $data
 * @return mixed
 */
function json_request($uri, $data) {
	$json_data = json_encode ( $data );
	$c = curl_init ();
	curl_setopt ( $c, CURLOPT_URL, $uri );
	curl_setopt ( $c, CURLOPT_CUSTOMREQUEST, "POST" );
	curl_setopt ( $c, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $c, CURLOPT_POST, $json_data );
	curl_setopt ( $c, CURLOPT_POSTFIELDS, $json_data );
	curl_setopt ( $c, CURLOPT_HTTPHEADER, array (
			'Content-Type: application/json',
			'Content-Length: ' . strlen ( $json_data )
	) );
	curl_setopt ( $c, CURLOPT_SSL_VERIFYPEER, false );
	$result = curl_exec ( $c );

	echo "<b>JSON Request:</b><br>\n";
	echo $json_data . "<br><br>\n";

	echo "<b>JSON Answer:</b><br>\n";
	echo $result . "<br><br>\n";

	echo "<b>CURL Debug Info:</b><br>\n";
	$debug = curl_getinfo ( $c );
	echo expand_arr ( $debug ) . "<br><hr>\n";

	return json_decode ( $result, true );
}
/**
 *
 * @param unknown $uri
 * @param unknown $username
 * @param unknown $password
 * @return unknown
 */
function zabbix_auth($uri, $username, $password) {
	$data = array (
			'jsonrpc' => "2.0",
			'method' => "user.login",
			'params' => array (
					'user' => $username,
					'password' => $password
			),
			'id' => "1"
	);
	$response = json_request ( $uri, $data );
	return $response ['result'];
}

/**
 *
 * @param unknown $uri
 * @param unknown $authtoken
 * @return unknown
 */
function zabbix_get_hostgroups($uri, $authtoken) {
	$data = array (
			'jsonrpc' => "2.0",
			'method' => "hostgroup.get",
			'params' => array (
					'output' => "extend",
					'sortfield' => "name"
			),
			'id' => "2",
			'auth' => $authtoken
	);
	$response = json_request ( $uri, $data );
	return $response ['result'];
}
/**
 *
 * @param unknown $uri
 * @param unknown $authtoken
 */
function zabbix_get_alert($uri, $authtoken) {
	$data = array (
			"jsonrpc" => "2.0",
			"method" => "alert.get",
			"params" => array (
					"output" => "extend",
					"actionids" => "3"
			),
			"auth" => $authtoken,
			"id" => 1
	);
	$response = json_request ( $uri, $data );
	return $response ['result'];
}

/**
 *
 * @var Ambiguous $authtoken
 */
print "<h1>Auth</h1>";
$authtoken = zabbix_auth ( $uri, $username, $password );
print "<h1>HostGrups</h1>";
expand_arr ( zabbix_get_hostgroups ( $uri, $authtoken ) );
print "<h1>Alerts</h1>";
expand_arr ( zabbix_get_alert ( $uri, $authtoken ) );
?>