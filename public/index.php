<?php
/*
    Configuration
*/
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include("class/database.php");
include("class/user.php");
include("class/activeUser.php");
session_start();

$dbh=database::get_instance();

if(!empty($_SESSION['activeUser']) && $_SESSION['activeUser'] instanceof activeUser && $_SESSION['activeUser']->online()){
    /* process all user forms here so no additional refreshes are needed.
    eventually move off page and ajax the entire thing.
    */
    if(isset($_POST['displayName'])){
        $displayName=$_POST['displayName'];
        $goalWeight=$_POST['goalWeight'];
        $public=$_POST['public'];
        
        $_SESSION['activeUser']->setGoalWeight($goalWeight);
        $_SESSION['activeUser']->setDisplayName($displayName);
        $_SESSION['activeUser']->setPublic($public);
    }
    if(isset($_POST['walkingExcercise'])){
        if($_POST['newDate']>''){
            $error=false;
            $date=explode("-",$_POST['newDate']);
            if(sizeof($date)!=3){
                $errorMsg[]="Please enter in a valid date.";
                $error=true;
            }
            if(!checkdate($date[1],$date[2],$date[0])){
                $errorMsg[]="Please enter in a valid date.";
                $error=true;
            }
            $date=implode("-",$date);
            $newWalking=(int)$_POST['newWalking'];
            $newJogging=(int)$_POST['newJogging'];
            $newRunning=(int)$_POST['newRunning'];
            if(!$error){
                $measurementEntries=$dbh->prepare("
                INSERT INTO `WalkingExcercise`(`userID`,`date`,`walking`,`jogging`,`running`)
                VALUES(:userID, :date, :walking, :jogging, :running)
                ");
                $measurementEntries->execute(array("userID" => $_SESSION['activeUser']->getUserID(),"date"=>$date,
                "walking"=>$newWalking,"jogging"=>$newJogging,"running"=>$newRunning));
            }
        }
        if(isset($_POST['id'])){
            //print_r($_POST);
            $count=sizeof($_POST['id']);
            for($i=0;$i<$count;$i++){
                $error=false;
                $id=(int)$_POST['id'][$i];
                $date=explode("-",$_POST['date'][$i]);
                if(sizeof($date)!=3){
                    $errorMsg[]="Please enter in a valid date.";
                    $error=true;
                }
                if(!checkdate($date[1],$date[2],$date[0])){
                    $errorMsg[]="Please enter in a valid date.";
                    $error=true;
                }
                $date=implode("-",$date);
                $newWalking=(int)$_POST['walking'][$i];
                $newJogging=(int)$_POST['jogging'][$i];
                $newRunning=(int)$_POST['running'][$i];
                if(!$error){
                    $excerciseEntries=$dbh->prepare("
                    UPDATE `WalkingExcercise`
                    SET
                        `date`= :date,
                        `walking`= :walking,
                        `jogging`= :jogging,
                        `running`= :running
                    WHERE `id`= :id
                    ");
                    $excerciseEntries->execute(array("date"=>$date,
                    "walking"=>$newWalking,"jogging"=>$newJogging,"running"=>$newRunning,"id"=>$id));
                }
            }
        }
    }
    if(isset($_POST['deleteExcercise'])){
        $id=(int)$_POST['id'];
        $excerciseEntries=$dbh->prepare("DELETE FROM `WalkingExcercise` WHERE `id`= :id");
        $excerciseEntries->execute(array("id"=>$id));
        echo "true";
        die();
    }
    
    if(isset($_POST['weightEntries'])){
        if($_POST['newDate']>''){
            $error=false;
            $date=explode("-",$_POST['newDate']);
            if(sizeof($date)!=3){
                $errorMsg[]="Please enter in a valid date.";
                $error=true;
            }
            if(!checkdate($date[1],$date[2],$date[0])){
                $errorMsg[]="Please enter in a valid date.";
                $error=true;
            }
            $date=implode("-",$date);
            $newEntry=(float)$_POST['newEntry'];
            if(!$error){
                $weightEntries=$dbh->prepare("
                INSERT INTO `Weight`(`userID`,`date`,`entry`)
                VALUES(:userID, :date, :entry)
                ");
                $weightEntries->execute(array("userID" => $_SESSION['activeUser']->getUserID(),"date"=>$date,
                "entry"=>$newEntry));
            }
        }
        if(isset($_POST['id'])){
            //print_r($_POST);
            $count=sizeof($_POST['id']);
            for($i=0;$i<$count;$i++){
                $error=false;
                $id=(int)$_POST['id'][$i];
                $date=explode("-",$_POST['date'][$i]);
                if(sizeof($date)!=3){
                    $errorMsg[]="Please enter in a valid date.";
                    $error=true;
                }
                if(!checkdate($date[1],$date[2],$date[0])){
                    $errorMsg[]="Please enter in a valid date.";
                    $error=true;
                }
                $date=implode("-",$date);
                $entry=(float)$_POST['entry'][$i];
                if(!$error){
                    $weightEntries=$dbh->prepare("
                    UPDATE `Weight`
                    SET
                        `date`= :date,
                        `entry`= :entry
                    WHERE `id`= :id
                    ");
                    $weightEntries->execute(array("id" => $id,"date"=>$date,
                    "entry"=>$entry));
                }
            }
        }
    }
    if(isset($_POST['deleteWeight'])){
        $id=(int)$_POST['id'];
        $excerciseEntries=$dbh->prepare("DELETE FROM `Weight` WHERE `id`= :id");
        $excerciseEntries->execute(array("id"=>$id));
        echo "true";
        die();
    }
    
    if(isset($_POST['measurements'])){
        if($_POST['newDate']>''){
            $error=false;
            $date=explode("-",$_POST['newDate']);
            if(sizeof($date)!=3){
                $errorMsg[]="Please enter in a valid date.";
                $error=true;
            }
            if(!checkdate($date[1],$date[2],$date[0])){
                $errorMsg[]="Please enter in a valid date.";
                $error=true;
            }
            $date=implode("-",$date);
            $newChest=(float)$_POST['newChest'];
            $newWaist=(float)$_POST['newWaist'];
            $newHips=(float)$_POST['newHips'];
            $newArm=(float)$_POST['newArm'];
            $newLeg=(float)$_POST['newLeg'];
            
            if(!$error){
                $weightEntries=$dbh->prepare("
                INSERT INTO `Measurements`(`userID`,`date`,`chest`,`waist`,`hips`,`arm`,`leg`)
                VALUES(:userID, :date, :chest, :waist, :hips, :arm, :leg)");
                $weightEntries->execute(array("userID" => $_SESSION['activeUser']->getUserID(),"date"=>$date,
                "chest"=>$newChest,"waist"=>$newWaist,"hips"=>$newHips,"arm"=>$newArm,"leg"=>$newLeg));
            }
        }
        if(isset($_POST['id'])){
            //print_r($_POST);
            $count=sizeof($_POST['id']);
            for($i=0;$i<$count;$i++){
                $error=false;
                $id=(int)$_POST['id'][$i];
                $date=explode("-",$_POST['date'][$i]);
                if(sizeof($date)!=3){
                    $errorMsg[]="Please enter in a valid date.";
                    $error=true;
                }
                if(!checkdate($date[1],$date[2],$date[0])){
                    $errorMsg[]="Please enter in a valid date.";
                    $error=true;
                }
                $date=implode("-",$date);
                $entry=(float)$_POST['entry'][$i];
                $chest=(float)$_POST['chest'][$i];
                $waist=(float)$_POST['waist'][$i];
                $hips=(float)$_POST['hips'][$i];
                $arm=(float)$_POST['arm'][$i];
                $leg=(float)$_POST['leg'][$i];
                if(!$error){
                    $measurementsEntries=$dbh->prepare("
                    UPDATE `Measurements`
                    SET
                        `date`= :date,
                        `chest`= :chest,
                        `waist`= :waist,
                        `hips`= :hips,
                        `arm`= :arm,
                        `leg`= :leg
                    WHERE `id`= :id
                    ");
                    $measurementsEntries->execute(array("id" => $id,"date"=>$date,
                    "chest"=>$chest,"waist" => $waist,"hips" => $hips,"arm" => $arm,"leg" => $leg));
                }
            }
        }
    }
    if(isset($_POST['deleteMeasurements'])){
        $id=(int)$_POST['id'];
        $measurementsEntries=$dbh->prepare("DELETE FROM `Measurements` WHERE `id`= :id");
        $measurementsEntries->execute(array("id"=>$id));
        echo "true";
        die();
    }
}
if(!isset($_SESSION['activeUser'])){
    $_SESSION['activeUser']=null;
}
if(isset($_GET['logout'])){
    $_SESSION['activeUser']=null;
}
if((isset($_GET['login']) && ($_SESSION['activeUser']==null)) || (isset($_GET['state']) && $_GET['state'])){
    $app_id = "315302831857063";
    $app_secret = "7194e1b0ba11c5d02d8be698565251d9";
    $my_url = "http://www.charlesdthompson.com/weight/index.php";
    $code = $_REQUEST["code"];
    
    if(empty($code)) {
     $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
     $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
       . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
       . $_SESSION['state'];
    
     echo("<script> top.location.href='" . $dialog_url . "'</script>");
    }
    
    if($_REQUEST['state'] == $_SESSION['state']) {
     $token_url = "https://graph.facebook.com/oauth/access_token?"
       . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
       . "&client_secret=" . $app_secret . "&code=" . $code;
    
     $response = @file_get_contents($token_url);
     $params = null;
     parse_str($response, $params);
    
     $graph_url = "https://graph.facebook.com/me?access_token=" 
       . $params['access_token'];
    
     $fbInfo = json_decode(file_get_contents($graph_url));
     // the below line registers this person if they are not , otherwise it just logs them in.
     
     $_SESSION['activeUser']=new activeUser(
        $fbInfo->id,
        $fbInfo->link,
        $fbInfo->name,$params->access_token,$fbInfo->username
     );
     header('Location: index.php');
     //print_r($_SESSION);
    
    }
    else {
     echo("The state does not match. You may be a victim of CSRF.");
    }
}



if(!isset($_GET['id'])){
    if($_SESSION['activeUser']!=null){
        $_GET['id']=$_SESSION['activeUser']->getUserID();
    }else{
        $_GET['id']=1;
    }
}
$user=user::get_instance($_GET['id']);
if(!$user->getInitializationFailed()){
    $modules=array("Weight","WalkingExcercise","Measurements");
    $fields=array(
                "Weight" => array("Date" => "date","Weight in Lbs" => "entry"),
                "WalkingExcercise" => array("Date" => "date","Walking" => "walking","Jogging" => "jogging","Running" => "running"),
                "Measurements"=>
                    array("Date" => "date", "Chest" => "chest", "Waist" => "waist", "Hips" => "hips",
                        "Arm" => "arm", "Leg" => "leg"
                        )
                );
    foreach($modules as $name){
        $selectFields=implode(",",$fields[$name]);
        $$name=$dbh->prepare("SELECT $selectFields FROM $name WHERE `userID` = :userID");
    }reset($modules);
    $getLabels=$dbh->prepare("SELECT `label`,`chartType` FROM `labels` WHERE `table`= :table");
}
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <title></title>
  <meta name="description" content="">
  <style type="text/css">
    #content{
        width:990px;
        margin:0 auto;
        background:beige;
        height:100%;
    }
    #controls{
        text-align:center;
        padding:10px;
    }
    #userTab{
        display:none;
    }
    #ui-datepicker-div{
        display:none;
    }
    html,body{
        height:100%;
        margin:0;
    }
    <?php
        if(sizeof($modules)>0){
            foreach($modules as $name){
                ?>
                    #<?php echo $name;?>Container{
                        width:49% !important;
                        float:left;
                    }
                    #<?php echo $name;?>Table_length{
                        display:none !important;
                    }
                    #<?php echo $name;?>Chart{
                        margin-top:10px;
                        float:right;
                        width:49%;
                    }
                <?php
            }reset($modules);
        }
    ?>
    .fb-login-button{
        padding:5px;
        text-align:right;
    }
  </style>
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="css/style.css">
  <script src="js/libs/modernizr-2.5.3.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script><script type="text/javascript" src="js/dataTables.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.js"></script>
  <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-lightness/jquery-ui.css" />
  <?php include 'testing/php-ofc-library/open-flash-chart.php'; ?>
  <script type="text/javascript">
    $(document).ready(function() {
        <?php 
        if(sizeof($modules)>0){
        foreach($modules as $name){?>
        $('#<?php echo $name; ?>Table').dataTable( {
            "sPaginationType": "full_numbers",
            "iDisplayLength": 7
        } );
        <?php }reset($modules);}?>
        
        $("#viewGraphs").click(function(event) {
          event.preventDefault();
          $('#userTab').hide();
          $('.chart').show();
          $('object').show();
          $('hr').show();
        });
        $("#enterData").click(function(event) {
          event.preventDefault();
          $('.chart').hide();
          $('object').hide();
          $('hr').hide();
          $('#userTab').show();
        });
        <?php
        if($_SESSION['activeUser']!=null && $_SESSION['activeUser']->newUser()){
            ?>
                $('.chart').hide();
                $('object').hide();
                $('hr').hide();
                $('#userTab').show();
            <?php
        }
        ?>
        $( ".date" ).datepicker();
        $( ".date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
        $('.date').each(function(index,element){
            $(this).val($(this).attr('rel'));
        });
        $(".deleteExcercise").click(function(event){
            event.preventDefault();
            var deleteID=$(this).parent().parent().attr('id');
            $.post("index2.php", { deleteExcercise: "true", id: deleteID },function(data){
                window.location.reload();
            } );
        });
        $(".deleteWeight").click(function(event){
            event.preventDefault();
            var deleteID=$(this).parent().parent().attr('id');
            $.post("index2.php", { deleteWeight: "true", id: deleteID },function(data){
                window.location.reload();
            } );
        });
        //
        $(".deleteMeasurements").click(function(event){
            event.preventDefault();
            var deleteID=$(this).parent().parent().attr('id');
            $.post("index2.php", { deleteMeasurements: "true", id: deleteID },function(data){
                window.location.reload();
            } );
        });
    } );
  </script>
    <?php
        include("class/function.generateChartData.php");
        if(sizeof($modules)>0){
            foreach($modules as $module){
                $$module=get_chart_data($module,$fields[$module],$$module,$getLabels);
            }
        }
    ?>
    <script type="text/javascript" src="testing/swfobject.js"></script>
    <script type="text/javascript">
        <?php
            if(sizeof($modules)>0){
                foreach($modules as $module){
                    echo "
                    swfobject.embedSWF('testing/open-flash-chart.swf', '".$module."Chart', '350', '320', '9.0.0', 'expressInstall.swf',{'get-data':'get_$module'});
                    ";
                }
            }
        ?>
        function ofc_ready(){}
        function findSWF(movieName) {
          if (navigator.appName.indexOf("Microsoft")!= -1) {
            return window[movieName];
          } else {
            return document[movieName];
          }
        }
        <?php
            if(sizeof($modules)>0){
                foreach($modules as $module){
                    $result=$$module;
                    echo "var $module = ".$result.";";
                    echo "
                    function get_$module(){
                        return JSON.stringify($module)
                    }";
                }
            }
        ?>
    </script>
  
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <header>

  </header>
  <div id="content">
    <?php 
    if($_SESSION['activeUser']!=null && $_SESSION['activeUser']->online()){
        ?>
            <div id="controls">
                <a href="" id="viewGraphs">View Graphs</a> | <a href="" id="enterData">Enter Data</a> | <a href="?logout">Logout</a>
            </div>
            <div id="userTab">
                <form name="basicUserInfo" method="POST">
                    Greetings <input type="text" name="displayName" value="<?php echo htmlentities($_SESSION['activeUser']->getDisplayName());?>" size="20" />. Your goal weight is currently
                    listed at <input type="text" name="goalWeight" size="5" value="<?php echo htmlentities($_SESSION['activeUser']->getGoalWeight());?>" /> lbs. In addition,
                    your profile is currently set to 
                    <select name="public">
                        <option value="0" <?php if($_SESSION['activeUser']->getPublic()<1){ echo "selected='selected'";} ?>>Hidden</option>
                        <option value="1" <?php if($_SESSION['activeUser']->getPublic()>0){ echo "selected='selected'";} ?>>Public</option>
                    </select>. <input type="submit" value="Update Settings" />
                </form>
                <div style="float: left;margin-right:20px;" style="width: 48%;">
                    <form name="excerciseEntries" method="POST">
                        <table>
                            <thead>
                                <th>
                                    Date
                                </th>
                                <th>
                                    Walking In Seconds
                                </th>
                                <th>
                                    Jogging
                                </th>
                                <th>
                                    Running
                                </th>
                                <th></th>
                            </thead>
                            <tr>
                                <td colspan="5">
                                    <div style="text-align: right;">
                                        <input type="submit" name="walkingExcercise" value="Add/Edit Records" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" name="newDate" size="10" class="date" value="" />
                                </td>
                                <td>
                                    <input type="text" name="newWalking" size="10" value="" />
                                </td>
                                <td>
                                    <input type="text" name="newJogging" size="10" value="" />
                                </td>
                                <td>
                                    <input type="text" name="newRunning" size="10" value="" />
                                </td>
                                <td>
                                </td>
                            </tr>
                            <?php
                                $excerciseEntries=$dbh->prepare("SELECT `id`,`date`,`walking`,`jogging`,`running` FROM `WalkingExcercise` WHERE `userID` = :userID ORDER BY `date` DESC");
                                $excerciseEntries->execute(array("userID" => $_SESSION['activeUser']->getUserID()));
                                foreach($excerciseEntries->fetchAll(PDO::FETCH_CLASS) as $entry){
                                    ?>
                                        <tr id="<?php echo $entry->id;?>">
                                            <td>
                                                <input type="hidden" name="id[]" value="<?php echo $entry->id;?>" />
                                                <input type="text" class="date" name="date[]" size="10" value="<?php echo $entry->date;?>" rel="<?php echo $entry->date;?>" />
                                            </td>
                                            <td>
                                                <input type="text" name="walking[]" size="10" value="<?php echo $entry->walking;?>" />
                                            </td>
                                            <td>
                                                <input type="text" name="jogging[]" size="10" value="<?php echo $entry->jogging;?>" />
                                            </td>
                                            <td>
                                                <input type="text" name="running[]" size="10" value="<?php echo $entry->running;?>" />
                                            </td>
                                            <td>
                                                <a class="deleteExcercise" href="">Delete Entry</a>
                                            </td>
                                        </tr>
                                    <?php
                                }
                            ?>
                        </table>
                    </form>
                </div>
                <div style="float: left;margin-left:20px;" style="width: 48%;">
                    <form name="weightEntries" method="POST">
                        <table>
                            <thead>
                                <th>
                                    Date
                                </th>
                                <th>
                                    Entry in Lbs
                                </th>
                                <th></th>
                            </thead>
                            <tr>
                                <td colspan="3">
                                    <div style="text-align: right;">
                                        <input type="submit" name="weightEntries" value="Add/Edit Records" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" name="newDate" size="10" class="date" value="" />
                                </td>
                                <td>
                                    <input type="text" name="newEntry" size="10" value="" />
                                </td>
                                <td>
                                </td>
                            </tr>
                            <?php
                                $weightEntries=$dbh->prepare("SELECT `id`,`date`,`entry` FROM `Weight` WHERE `userID` = :userID ORDER BY `date` DESC");
                                $weightEntries->execute(array("userID" => $_SESSION['activeUser']->getUserID()));
                                foreach($weightEntries->fetchAll(PDO::FETCH_CLASS) as $entry){
                                    ?>
                                        <tr id="<?php echo $entry->id;?>">
                                            <td>
                                                <input type="hidden" name="id[]" value="<?php echo $entry->id;?>" />
                                                <input type="text" class="date" name="date[]" size="10" value="<?php echo $entry->date;?>" rel="<?php echo $entry->date;?>" />
                                            </td>
                                            <td>
                                                <input type="text" name="entry[]" size="10" value="<?php echo $entry->entry;?>" />
                                            </td>
                                            <td>
                                                <a class="deleteWeight" href="">Delete Entry</a>
                                            </td>
                                        </tr>
                                    <?php
                                }
                            ?>
                        </table>
                    </form>
                </div>
                <br style="clear: both;" />
                <div style="float: left;" style="width: 48%;">
                    <form name="measurementEntries" method="POST">
                        <table>
                            <thead>
                                <th>
                                    Date
                                </th>
                                <th>
                                    Chest in Inches
                                </th>
                                <th>
                                    Waist
                                </th>
                                <th>
                                    Hips
                                </th>
                                <th>
                                    Left Arm
                                </th>
                                <th>
                                    Left Leg
                                </th>
                                <th></th>
                            </thead>
                            <tr>
                                <td colspan="7">
                                    <div style="text-align: right;">
                                        <input type="submit" name="measurements" value="Add/Edit Records" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" name="newDate" size="10" class="date" value="" />
                                </td>
                                <td>
                                    <input type="text" name="newChest" size="10" value="" />
                                </td>
                                <td>
                                    <input type="text" name="newWaist" size="10" value="" />
                                </td>
                                <td>
                                    <input type="text" name="newHips" size="10" value="" />
                                </td>
                                <td>
                                    <input type="text" name="newArm" size="10" value="" />
                                </td>
                                <td>
                                    <input type="text" name="newLeg" size="10" value="" />
                                </td>
                                <td>
                                </td>
                            </tr>
                            <?php
                                $measurementEntries=$dbh->prepare("SELECT `id`,`date`,`chest`,`waist`,`hips`,`arm`,`leg` FROM `Measurements` WHERE `userID` = :userID ORDER BY `date` DESC");
                                $measurementEntries->execute(array("userID" => $_SESSION['activeUser']->getUserID()));
                                foreach($measurementEntries->fetchAll(PDO::FETCH_CLASS) as $entry){
                                    ?>
                                        <tr id="<?php echo $entry->id;?>">
                                            <td>
                                                <input type="hidden" name="id[]" value="<?php echo $entry->id;?>" />
                                                <input type="text" class="date" name="date[]" size="10" value="<?php echo $entry->date;?>" rel="<?php echo $entry->date;?>" />
                                            </td>
                                            <td>
                                                <input type="text" name="chest[]" size="10" value="<?php echo $entry->chest;?>" />
                                            </td>
                                            <td>
                                                <input type="text" name="waist[]" size="10" value="<?php echo $entry->waist;?>" />
                                            </td>
                                            <td>
                                                <input type="text" name="hips[]" size="10" value="<?php echo $entry->hips;?>" />
                                            </td>
                                            <td>
                                                <input type="text" name="arm[]" size="10" value="<?php echo $entry->arm;?>" />
                                            </td>
                                            <td>
                                                <input type="text" name="leg[]" size="10" value="<?php echo $entry->leg;?>" />
                                            </td>
                                            <td>
                                                <a class="deleteMeasurements" href="">Delete Entry</a>
                                            </td>
                                        </tr>
                                    <?php
                                }
                            ?>
                        </table>
                    </form>
                </div>
                <br style="clear: both;" />
            </div>
        <?php
    }
    else{
        ?>
        <div class="fb-login-button"><a href="?login">Login with Facebook</a></div>
        <?php
    }
    if($user->getInitializationFailed()){
        echo "<p>The user you are trying to view does not appear to be valid.</p>";    
    }
    if(sizeof($modules)){
     foreach($modules as $name){
        //if($count%3==0){break;}
        ?>
    <div id="<?php echo $name;?>Container" class="chart" width="50%">
        <table id="<?php echo $name; ?>Table" width="100%" class="<?php echo $name; ?>Table">
            <thead>
                <tr>
                    <?php
                        foreach($fields[$name] as $index => $value){
                            ?>
                                <th style="padding-top: 20px;">
                                    <?php echo $index; ?>
                                </th>
                            <?php
                            if(!isset($values))$values=json_decode($$name);
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i=0;
                    /* todo: show in descending order on date */
                    foreach($values->x_axis->labels->labels as $date){
            		?>
                    <tr>
                        <td style="text-align: center;">
                            <?php echo $date;?>
                        </td>
                        <?php
                            $columns=$values->elements[0]->values[$i];
                            if(is_array($columns)){
                                foreach($columns as $value){
                                    if(is_numeric($value))echo "<td style='text-align: center;'>$value</td>";
                                    //print_r($value);
                                    //echo"</td>";
                                }
                            }else{
                                if($name=="Measurements"){
                                    for($y=0;$y<sizeof($values->elements);$y++){
                                        if(isset($values->elements[$y]->values[$i]) && is_numeric($values->elements[$y]->values[$i]))echo "<td style='text-align: center;'>".$values->elements[$y]->values[$i]."</td>";
                                    }
                                }else{
                                    echo "<td style='text-align: center;'>$columns</td>";
                                }
                            }
                        ?>
                    </tr>
                <?php
                        $i++;
                    }
                unset($values);
                ?>
            </tbody>
        </table>
    </div>
    <div id="<?php echo $name;?>Chart" class="chart"></div>
    <br style="clear: both;" />
    <hr />
    <?php //$count++; 
    }
    }
    ?>
    
  </div>
  <footer>

  </footer>
</body>
</html>
