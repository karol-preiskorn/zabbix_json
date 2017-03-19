<?php
/**
 * ZabbixAPI_class.php Copyright 2017 by Karol Preiskorn
 *
 * @version 18.03.2017 21:35:17 Karol Preiskorn Init
 * @version 19.03.2017 16:37:59 KPreiskorn ZabbixAPI_class.php Export to BB
 *
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
/**
 *
 * Generate bootstrap table with headers and coun of elements
 *
 * @param string $title
 * @param unknown $count
 * @param unknown $id
 */
function print_table_header($title, $count, $headers, $id) {
	print ('<div class="panel panel-default">' . "\n") ;
	print ('<div class="panel-heading">' . $title . ' <a href="#"> <span class="badge">' . $count . '</span></a></div>' . "\n") ;
	print ("<table id='" . $id . "' class='table table-striped table-bordered'>" . "\n") ;

	print ("<thead><tr>" . "\n") ;
	foreach ( $headers as $header ) {
		print ("<th>" . $header . "</th>" . "\n") ;
	}
	print ("</tr>\n</thead>\n<tbody>" . "\n") ;
}

try {
	// connect to Zabbix API
	$api = new ZabbixApi ( $uri, $username, $password );

	// get all graphs
	$alerts = $api->alertGet ();
	// print_r2 ( $graphs [0] );

	arsort ( $alerts );

	$headers = array (
			"Alert Id",
			"Event Id",
			"Date time",
			"Sent to",
			"Subject",
			"Message"
	);

	print_table_header ( 'Alerts', count ( $alerts ), $headers, 'alarms' );

	// print all alerts
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
			printf ( "<tr id='%s' %s><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>" . "\n", $alert->eventid, $alerttype, $alert->alertid, $alert->eventid, date ( "Y-m-d h:i", $alert->clock ), $alert->sendto, $alert->subject, $alert->message . " " . $eventid_org );
		}
	}
	print ("</tbody>\n</table>\n</div>\n") ;

	// get all graphs
	$graphs = $api->graphGet ();
	$header_graph = array (
			"Graph Id",
			"Description"
	);
	print_table_header ( 'Graph', count ( $graphs ), $header_graph, 'graph' );

	// print all graph IDs
	foreach ( $graphs as $graph ) {
		printf ( "<tr><td>%8d</td><td>%s</td></tr>\n", $graph->graphid, $graph->name );
	}
	print ("</tbody>\n</table>\n</div>\n") ;

	print ('<div class="panel-footer">

        <p>&copy; T-Mobile Polska Company | OSS development powered by Karol Preiskorn | internal use</p>

      </div>') ;
	print ("</div></body></html>") ; // end of container

	// print "<h1>Alerts... agian in raw</h2>";
		                                 // obsafe_print_r ( $alerts, FALSE, TRUE );
} catch ( Exception $e ) {
	// Exception in ZabbixApi catched
	echo $e->getMessage ();
}
?>