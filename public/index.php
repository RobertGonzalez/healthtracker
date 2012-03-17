<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <title></title>
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="css/style.css">

  <script src="js/libs/modernizr-2.5.3.min.js"></script>
  <style type="text/css">
    #content{
        width:990px;
        margin:0 auto;
        background:beige;
        height:100%;
    }
    html,body{
        height:100%;
        margin:0;
    }
    #container,#excerciseContainer,#measurementsContainer{
        width:49% !important;
        float:left;
    }
    #weightTable_length,#excerciseTable_length,#measurementsTable_length{
        display:none !important;
    }
    #myWeightChart,#myExcerciseChart,#myMeasurementsChart
    {
        margin-top:10px;
        float:right;
        width:49%;
    }
  </style>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script type="text/javascript" src="js/dataTables.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
        $('#weightTable').dataTable( {
            "sPaginationType": "full_numbers",
            "iDisplayLength": 7
        } );
        
        $('#excerciseTable').dataTable( {
            "sPaginationType": "full_numbers",
            "iDisplayLength": 7
        } );
        $('#measurementsTable').dataTable( {
            "sPaginationType": "full_numbers",
            "iDisplayLength": 7
        } );
    } );
  </script>
    <?php
        $measurementEntries=array(
            "02/23/2012" => array(58,59,60,19.25,30),
            "03/04/2012" => array(56,53,57,16.5,28.5),
            "03/08/2012" => array(55,52,52,16.25,29.25)
        );
        $excerciseEntries=array(
            "03/07/2012" => array(19*60+50,10,0),
            "03/08/2012" => array((9*60+40)*2,20+20,0),
            "03/09/2012" => array((18*60),6*20,0),
            "03/10/2012" => array((20*60)-(10*20),10*20,0)
        );
        $weightEntries=array(
            "01/01/2012" => 420,
            "02/23/2012" => 394.8,
            "02/24/2012" => 393,
            "02/25/2012" => 387.6,
            "02/26/2012" => 384.4,
            "02/27/2012" => 384.4,
            "02/28/2012" => 384.4,
            "02/29/2012" => 378.6,
            "03/01/2012" => 377.6,
            "03/02/2012" => 375.6,
            "03/03/2012" => 375.6,
            "03/04/2012" => 374.6,
            "03/05/2012" => 373.6,
            "03/06/2012" => 372.4,
            "03/07/2012" => 369.4,
            "03/08/2012" => 367.8,
            "03/09/2012" => 368.2,
            "03/10/2012" => 365.8,
            "03/11/2012" => 364.4
        );
        include 'testing/php-ofc-library/open-flash-chart.php';
        /* 
            begin chart 1 
        */
        $chart = new open_flash_chart();
        $weight = array();
        $date = array();
        
        foreach($weightEntries as $index => $value){
            $weight[]=$value;
            $date[]=$index;
        }reset($weightEntries);
        
        $title = new title( "Weight Loss" );
        
        // ------- LINE 2 -----
        $d = new solid_dot();
        $d->size(3)->halo_size(1)->colour('#3D5C56');
        
        $line = new line();
        $line->set_default_dot_style($d);
        $line->set_values( $weight );
        $line->set_width( 2 );
        $line->set_colour( '#3D5C56' );
        
        
        $x_labels = new x_axis_labels();
        $x_labels->set_steps( 2 );
        $x_labels->set_vertical();
        $x_labels->set_colour( '#000' );
        $x_labels->set_labels( $date );
        
        $x = new x_axis();
        $x->set_colour( '#000' );
        $x->set_grid_colour( '#D7E4A3' );
        $x->set_offset( false );
        $x->set_steps(4);
        // Add the X Axis Labels to the X Axis
        $x->set_labels( $x_labels );
        
        $chart->set_x_axis( $x );
        
        $y = new y_axis();
        $y->set_range( 250, 430, 20 );
        
        
        
        $chart->set_title( $title );
        $chart->add_element( $line );
        $chart->set_y_axis( $y );
        
        /*
            End Chart 1
        */
        /* 
            Begin Chart 2
        */

        $title2 = new title( 'Excercise Chart');
        $title2->set_style( "{font-size: 20px; color: #F24062; text-align: center;}" );
        
        $bar_stack = new bar_stack();
        
        // set a cycle of 3 colours:
        $bar_stack->set_colours( array( '#0033CC', '#50284A', '#FF6633' ) );
        
        foreach($excerciseEntries as $date => $valueArray){
            // add 3 bars:
            $bar_stack->append_stack(  $valueArray  );
        }reset($excerciseEntries);
        
        
        $bar_stack->set_keys(
            array(
                new bar_stack_key( '#0033CC', 'Walking in seconds', 13 ),
                new bar_stack_key( '#50284A', 'Jogging in seconds', 13 ),
                new bar_stack_key( '#FF6633', 'Running in seconds', 13 ),
                )
            );
        $bar_stack->set_tooltip( 'Date: #x_label#  Time: #val# seconds' );
        
        
        
        $y2 = new y_axis();
        $y2->set_range( 0, 1200, 60 );
        
        $x2 = new x_axis();
        
        $dateValueArray=array();
        foreach($excerciseEntries as $date => $valueArray){
            // add 3 bars:
            $dateValueArray[]="$date";
        }reset($excerciseEntries);
        
        $x2->set_labels_from_array( $dateValueArray );
        
        $tooltip2 = new tooltip();
        $tooltip2->set_hover();
        
        $chart2 = new open_flash_chart();
        $chart2->set_title( $title2 );
        $chart2->add_element( $bar_stack );
        $chart2->set_x_axis( $x2 );
        $chart2->add_y_axis( $y2 );
        $chart2->set_tooltip( $tooltip2 );
        
        /*
            End Chart 2
        */
    ?>
    <script type="text/javascript" src="testing/swfobject.js"></script>
    <script type="text/javascript">
    swfobject.embedSWF("testing/open-flash-chart.swf", "myWeightChart", "350", "320", "9.0.0", "expressInstall.swf",{"get-data":"get_data_1"});
    swfobject.embedSWF("testing/open-flash-chart.swf", "myExcerciseChart", "350", "320", "9.0.0", "expressInstall.swf",{"get-data":"get_data_2"});
    </script>
    
    <script type="text/javascript">
    
    function ofc_ready()
    {
        
    }
    
    function get_data_1()
    {
        return JSON.stringify(data1);
    }
    
    function get_data_2()
    {
        return JSON.stringify(data2);
    }
    
    function findSWF(movieName) {
      if (navigator.appName.indexOf("Microsoft")!= -1) {
        return window[movieName];
      } else {
        return document[movieName];
      }
    }
        
    var data1 = <?php echo $chart->toPrettyString(); ?>;
    var data2 = <?php echo $chart2->toPrettyString(); ?>;
    </script>
  
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <header>

  </header>
  <div id="content">
    <div id="container" width="50%">
        <table id="weightTable" width="100%" class="weightTable">
            <thead>
                <tr>
                    <th>
                        Date 
                    </th>
                    <th>
                        Weight in lbs
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($weightEntries as $date => $weight){
            		?>
                    <tr>
                        <td>
                            <?php echo $date;?>
                        </td>
                        <td>
                            <?php echo number_format($weight,2,".",",");?>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <div id="myWeightChart"></div>
    <br style="clear: both;" />
    <hr />
    <div id="excerciseContainer" width="50%">
        <table id="excerciseTable" width="100%" class="excerciseTable">
            <thead>
                <tr>
                    <th>
                        Date 
                    </th>
                    <th>
                        Walking in minutes
                    </th>
                    <th>
                        Jogging in minutes
                    </th>
                    <th>
                        Running in minutes
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($excerciseEntries as $date => $array){
            		?>
                    <tr>
                        <td>
                            <?php echo $date;?>
                        </td>
                        <?php
                            foreach($array as $value){
                        ?>
                            <td>
                                <?php echo number_format(($value/60),2,".",",");?>
                            </td>
                        <?php
                            }
                        ?>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <div id="myExcerciseChart"></div>
    
    <br style="clear: both;" />
    <hr />
    <p>
        On 03/08/12 I adjusted where I was measuring to be as follows to ensure consistency:<br />
        <strong>Chest</strong> - Across the Mammary papillas (code for nipples).
        <strong>Waist</strong> - 1 inch above my belly button.
        <strong>Hips</strong> - Pants line<br />
        <strong>Arm</strong> - Midway point between my elbow and shoulder.
        <strong>Leg</strong> - Midway point between my knee and hip.<br />
    </p>
    <div id="measurementsContainer" width="50%">
        <table id="measurementsTable" width="100%" class="measurementsTable">
            <thead>
                <tr>
                    <th>
                        Date 
                    </th>
                    <th>
                        Chest in inches
                    </th>
                    <th>
                        Waist in inches
                    </th>
                    <th>
                        Hips in inches
                    </th>
                    <th>
                        Arm in inches
                    </th>
                    <th>
                        Leg in inches
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($measurementEntries as $date => $array){
            		?>
                    <tr>
                        <td>
                            <?php echo $date;?>
                        </td>
                        <?php
                            foreach($array as $value){
                        ?>
                            <td>
                                <?php echo number_format(($value),2,".",",");?>
                            </td>
                        <?php
                            }
                        ?>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
  </div>
  <footer>

  </footer>
</body>
</html>
