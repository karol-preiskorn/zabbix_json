<?php
/**
 *
 * HTML 5
 *
 */
?>
<!DOCTYPE html>
<html>
<head>
<title>Zabbix</title>

<link rel="stylesheet" type="text/css"
	href="https://cdn.datatables.net/v/bs-3.3.7/jq-2.2.4/dt-1.10.13/af-2.1.3/b-1.2.4/b-colvis-1.2.4/fh-3.1.2/kt-2.2.0/r-2.1.1/rr-1.2.0/sc-1.4.2/se-1.2.0/datatables.min.css" />
<script type="text/javascript"
	src="https://cdn.datatables.net/v/bs-3.3.7/jq-2.2.4/dt-1.10.13/af-2.1.3/b-1.2.4/b-colvis-1.2.4/fh-3.1.2/kt-2.2.0/r-2.1.1/rr-1.2.0/sc-1.4.2/se-1.2.0/datatables.min.js"></script>

<script>
$(document).ready(function() {
    $('#alarms').DataTable( {
        deferRender:    true,
        scrollY:        400,
        scrollCollapse: false,
        scroller:       false
    } );
} );


$(document).ready(function() {
	$('#graph').DataTable({
        deferRender:    true,
        scrollY:        400,
        scrollCollapse: false,
        scroller:       false
    } );
} );
</script>

</head>

<body>
	<div class="container">
		<div class="jumbotron">
			<h1>Zabbix Alarm monitoring</h1>
			<p>Aplikacja monitoruje alarmy z Zabbix.</p>
			<p>
				<a class="btn btn-primary btn-lg"
					href="http://wrex/zabbix/zabbix.php?action=dashboard.view"
					role="button">Zabbix Dashboard</a> <a class="btn btn-lg"
					href="#alarms" role="button">Alarms</a> <a class="btn btn-lg"
					href="#graph" role="button">Graph</a> <a class="btn btn-lg"
					href="simple-json.php" role="button">Simple JSON</a>


<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
  About
</button>
<div class="collapse" id="collapseExample">
  <div class="well">
    Aplikacja monitoruje alarmy z OSS Zabbix na serwerze wrex. Dostêp do API poprzez klasê PHP -> ZabbixApi. Opis API https://www.zabbix.com/documentation/2.2/manual/api/reference/history/get
  </div>
</div>
			</p>
		</div>
