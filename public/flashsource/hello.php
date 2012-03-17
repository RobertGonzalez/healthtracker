<?php

//
// This is the MODEL section:
//

include 'php-ofc-library/open-flash-chart.php';

$title = new title( date("D M d Y") );

$bar = new bar();
$bar->set_values( array(9,8,7,6,5,4,3,2,1) );

$chart = new open_flash_chart();
$chart->set_title( $title );
$chart->add_element( $bar );

//
// This is the VIEW section:
//

?>

<html>
<head>

<script type="text/javascript" src="../js/json/json2.js"></script>
<script type="text/javascript" src="swfobject.js"></script>
<script type="text/javascript">
swfobject.embedSWF("open-flash-chart.swf", "my_chart", "350", "200", "9.0.0");
</script>

<script type="text/javascript">

function ofc_ready()
{
    
}

function open_flash_chart_data()
{
    return JSON.stringify(data);
}

function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
    return window[movieName];
  } else {
    return document[movieName];
  }
}
    
var data = <?php echo $chart->toPrettyString(); ?>;

</script>


</head>
<body>

<p>Hello World</p>


<div id="my_chart"></div>
     

</body>
</html>