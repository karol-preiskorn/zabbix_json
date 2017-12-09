<?php

/**
 * Zabbix monitoring in PHP
 *
 * @author Karol Preiskorn
 *
 * @version 2017-04-16 KP init
 * @version 17.03.2017 14:47:16 Karol Preiskorn dodanie informacji na temat Alert�w
 * @version 17.03.2017 18:07:38 Karol Preiskorn - dodanie synchronizacji
 *
 *
 *
 *
 * phpinfo ();
 *
 */
$uri = "http://zabixx";
$username = "user";
$password = "pass";

// set passwords and uri
include "passwords.php";
/**
 *
 * @param unknown $in
 * @param number $indent
 * @param string $from_array
 * @return string
 */
function json_readable_encode($in, $indent = 0, $from_array = true) {
	$_myself = __FUNCTION__;
	$_escape = function ($str) {
		return preg_replace ( "!([\b\t\n\r\f\"\\'])!", "\\\\\\1", $str );
	};

	$out = '';

	foreach ( $in as $key => $value ) {
		$out .= str_repeat ( "\t", $indent + 1 );
		$out .= "\"" . $_escape ( ( string ) $key ) . "\": ";

		if (is_object ( $value ) || is_array ( $value )) {
			$out .= "\n";
			$out .= $_myself ( $value, $indent + 1 );
		} elseif (is_bool ( $value )) {
			$out .= $value ? 'true' : 'false';
		} elseif (is_null ( $value )) {
			$out .= 'null';
		} elseif (is_string ( $value )) {
			$out .= "\"" . $_escape ( $value ) . "\"";
		} else {
			$out .= $value;
		}

		$out .= ",\n";
	}

	if (! empty ( $out )) {
		$out = substr ( $out, 0, - 2 );
	}

	$out = str_repeat ( "\t", $indent ) . "{\n" . $out;
	$out .= "\n" . str_repeat ( "\t", $indent ) . "}";

	return $out;
}

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

	/*
	 * echo "<b>CURL Debug Info:</b><br>\n";
	 * $debug = curl_getinfo ( $c );
	 * echo expand_arr ( $debug ) . "<br><hr>\n";
	 */

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
print "<h2>json_readable_encode</h2>";
json_readable_encode ( zabbix_get_hostgroups ( $uri, $authtoken ) );
print "<h1>Alerts</h1>";
expand_arr ( zabbix_get_alert ( $uri, $authtoken ) );
?>