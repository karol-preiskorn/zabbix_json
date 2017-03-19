<?php
/**
 * ZabbixAPI_class.php Copyright 2017 by Karol Preiskorn
 *
 * @varsion 18.03.2017 21:35:17 Karol Preiskorn Init
 */
// load ZabbixApi
require_once 'PhpZabbixApi-2.4.5/build/ZabbixApi.class.php';
include 'header.php';

use ZabbixApi\ZabbixApi;

$uri = "http://wrex.oss.t-mobile.pl/zabbix/api_jsonrpc.php";
$username = "inact_ro";
$password = "Karol!123";
/**
 * poor print_r function
 *
 * @param unknown $val
 */
function print_r2($val) {
	echo '<pre>';
	print_r ( $val );
	echo '</pre>';
}
/**
 * Better print_r function
 */
function obsafe_print_r($var, $return = false, $html = true, $level = 0) {
	$spaces = "";
	$space = $html ? "&nbsp;" : " ";
	$newline = $html ? "<br />" : "\n";
	for($i = 1; $i <= 6; $i ++) {
		$spaces .= $space;
	}
	$tabs = $spaces;
	for($i = 1; $i <= $level; $i ++) {
		$tabs .= $spaces;
	}
	if (is_array ( $var )) {
		$title = "Array";
	} elseif (is_object ( $var )) {
		$title = get_class ( $var ) . " Object";
	}
	$output = "<b>" . $title . "</b>" . $newline . $newline;
	foreach ( $var as $key => $value ) {
		if (is_array ( $value ) || is_object ( $value )) {
			$level ++;
			$value = obsafe_print_r ( $value, true, $html, $level );
			$level --;
		}
		$output .= $tabs . "[" . $key . "] => [" . $value . "]" . $newline;
	}
	if ($return)
		return $output;
	else
		echo "<pre>" . $output . "</pre>";
}
function print_table($title, $count) {
	print ('<div class="panel panel-default">') ;
	print ('<div class="panel-heading">' . $title . ' <a href="#"> <span class="badge">' . $count . '</span></a></div>') ;
	print ("<table class='table table-condensed table-hover'>") ;
}

try {
	// connect to Zabbix API
	$api = new ZabbixApi ( $uri, $username, $password );

	// get all graphs
	$graphs = $api->graphGet ();
	// print_r2 ( $graphs [0] );
	print_table ( 'Graph', count ( $graphs ) );

	// print all graph IDs
	foreach ( $graphs as $graph ) {
		printf ( "<tr><td>%8d</td><td>%s</td><tr>\n", $graph->graphid, $graph->name );
	}
	print ("</table></div>") ;

	// get all graphs
	$alerts = $api->alertGet ();
	// print_r2 ( $graphs [0] );

	arsort ( $alerts );

	print_table ( 'Alerts', count ( $alerts ) );
	$headers = array (
			"Alert Id",
			"Event Id",
			"Date time",
			"Sent to",
			"Subject",
			"Message"
	);
	print ("<thead><tr>") ;
	foreach ( $headers as $header ) {
		print ("<th>" . $header . "</th>") ;
	}
	print ("</thead></tr>") ;
	// print all graph IDs
	foreach ( $alerts as $alert ) {
		if ($alert->sendto = "karol.preiskorn@t-mobile.pl") {
			if (strpos ( $alert->subject, "PROBLEM:" ) !== false) {
				$alerttype = "class='warning'";
			} else {
				$alerttype = "class='normal'";
			}
			if (preg_match ( "/Original event ID: ([0-9]{4,12})/", $alert->message, $regs )) {
				$eventid_org = "[link: <a href='#" . $regs [1] . "'>" . $regs [1] . "</a>]";
			} else {
				$eventid_org = "Not found Original event ID";
			}
			printf ( "<tr id='%s' %s><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><tr>", $alert->eventid, $alerttype, $alert->alertid, $alert->eventid, date ( "Y-m-d h:i", $alert->clock ), $alert->sendto, $alert->subject, $alert->message . " " . $eventid_org );
		}
	}
	print ("</table></div>") ;

	print "<h1>Alerts... agian in raw</h2>";
	obsafe_print_r ( $alerts, FALSE, TRUE );
} catch ( Exception $e ) {
	// Exception in ZabbixApi catched
	echo $e->getMessage ();
}
?>