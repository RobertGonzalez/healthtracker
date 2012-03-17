<?php

include 'php-ofc-library/open-flash-chart.php';

// generate some random data
srand((double)microtime()*1000000);

$max = 20;
$tmp = array();
for( $i=0; $i<9; $i++ )
{
  $tmp[] = rand(0,$max);
}

$title = new title( date("D M d Y") );

$bar = new bar();
$bar->set_values( array(1,2,3,4,5,6,7,8,9) );

$chart = new open_flash_chart();
$chart->set_title( $title );
$chart->add_element( $bar );
                    
echo $chart->toString();


?>