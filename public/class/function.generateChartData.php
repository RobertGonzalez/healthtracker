<?php
function get_chart_data($model,$fields,$tableValues,$getLabels,$ignoreFields=array('date')){
    $dbh=database::get_instance();
    $user=user::get_instance(); // instantiated at the top of th file
    $chart = new open_flash_chart();
    foreach($fields as $fieldName){
        $$fieldName=array();
    }
    
    
    $tableValues->execute(array(":userID" => $user->getUserID()));
    $results=$tableValues->fetchAll(PDO::FETCH_ASSOC);
    
    // declare our arrays so we can push into them.
    foreach($fields as $index => $value){
        if(!isset($$index))$$index=array();
    }
    
    // fill up the arrays with the values
    foreach($results as $index => $value){
        foreach($fields as $index2 => $value2){
            if(is_numeric($value[$value2])){
                array_push($$index2,(float)$value[$value2]);
            }else{
                array_push($$index2,$value[$value2]);
            }
        }
    }
    
    $getLabels->execute(array("table" => $model));
    $labelInfo=$getLabels->fetchAll(PDO::FETCH_ASSOC);
    
    $title = new title( $labelInfo[0]['label'] );
    
    switch($labelInfo[0]['chartType']){
        case "multiLine": // measurements charts
            $getWeightColors=$dbh->prepare("SELECT `dot`,`line`,`xLabel`,`xAxis`,`xGrid` FROM `weightColorPreferences` WHERE `userID`= :userID");
            $getWeightColors->execute(array("userID" => $user->getUserID()));
            $weightColors=$getWeightColors->fetchAll(PDO::FETCH_OBJ);
            $weightColor=$weightColors[0];
            
            $dot= new solid_dot();
            $dot->size(3)->halo_size(1)->colour('#'.$weightColor->dot);
            $holder=array();
            foreach($fields as $index => $value){
            	$indexArray = $$index; // For the love of God can we set some freaking variables?!?!?!
                if(!in_array($value,$ignoreFields)){
                    if(!isset($$value)){$$value=array();}
                    $$value=$$index; // set to walking, running, jogging
                    $holder[]=$$value;
    	            if(!isset($max)){$max=is_array($indexArray) && !empty($indexArray) ? max($indexArray) : 0;}
                    else if(is_array($indexArray) && !empty($indexArray) && max($indexArray)>$max){$max=max($indexArray);}
                }else{
                    $x_labels = new x_axis_labels();
                    $x_labels->set_steps( 2 );
                    $x_labels->set_vertical();
                    $x_labels->set_colour( '#'.$weightColor->xLabel );
                    $x_labels->set_labels( $indexArray );
                }
            }
            // we have arrays with each fields values. we need each column from each row in it's own array so that's what this next section does
            for($y=0;$y<=(sizeof($fields)-sizeof($ignoreFields));$y++){
                $stack=array();
                for($i=0;$i<sizeof($holder[0]);$i++){
                    if(isset($holder[$y][$i]) && is_numeric($holder[$y][$i]))array_push($stack,$holder[$y][$i]);
                }
                $line[$y] = new line();
                $line[$y]->set_default_dot_style($dot);
                $line[$y]->set_values( $stack );
                $line[$y]->set_width( 2 );
                $line[$y]->set_colour( '#'.$weightColor->line );
                unset($stack);
            }
            $x = new x_axis();
            $x->set_colour( '#'.$weightColor->xAxis );
            $x->set_grid_colour( '#'.$weightColor->xGrid );
            $x->set_offset( false );
            $x->set_steps(2);
            
            // Add the X Axis Labels to the X Axis
            $x->set_labels( $x_labels );
            $chart->set_x_axis( $x );
            $y = new y_axis();
            $y->set_range( 0, $max*1.05, floor((($max*1.10)/10)) );
            $chart->set_title( $title );
            foreach($line as $element){
                $chart->add_element($element);   
            }
            $chart->set_y_axis( $y );
            break;
        case "line":
            $getExcerciseColors=$dbh->prepare("SELECT `color1`,`color2`,`color3` FROM `excerciseColorPreferences` WHERE `userID`= :userID");
            $getExcerciseColors->execute(array("userID" => $user->getUserID()));
            $excerciseColors=$getExcerciseColors->fetchAll(PDO::FETCH_OBJ);
            $excerciseColor=$excerciseColors[0];
            
            $title->set_style( "{font-size: 20px; color: #F24062; text-align: center;}" );
            
            $bar_stack = new bar_stack();
            $bar_stack->set_colours( array( '#'.$excerciseColor->color1, '#'.$excerciseColor->color2, '#'.$excerciseColor->color3 ) );
            $holder=array();
            foreach($fields as $index => $value){
                $indexArray = $$index; // For the love of God can we set some freaking variables?!?!?!
            	if(!in_array($value,$ignoreFields)){
                	if(!isset($$value)){$$value=array();}
                    $$value=$indexArray; // set to walking, running, jogging
                    $holder[]=$$value;
                    if(!isset($max)){$max=is_array($indexArray) && !empty($indexArray) ? max($indexArray) : 0;}
                    else if(is_array($indexArray) && !empty($indexArray) && max($indexArray)>$max){$max=max($indexArray);}
                    
                    //if(!isset($min)){$min=min($$index);}
                    //else if(min($$index)>$min){$min=min($$index);}
                    //$bar_stack->append_stack(  $$index  );
                }else{
                    $x = new x_axis();
                    $x->set_labels_from_array( $indexArray );
                }
            }
            
            // we have arrays with each fields values. we need each column from each row in it's own array so that's what this next section does
            for($i=0;$i<sizeof($holder[0]);$i++){
                $stack=array();
                for($y=0;$y<=(sizeof($fields)-sizeof($ignoreFields));$y++){
                    if (isset($holder[$y][$i])) {
	                	array_push($stack,$holder[$y][$i]);
                    }
                }
                $bar_stack->append_stack($stack);
            }
            $bar_stack->set_keys(
                array(
                    new bar_stack_key( '#'.$excerciseColor->color1, 'Walking in seconds', 13 ),
                    new bar_stack_key( '#'.$excerciseColor->color2, 'Jogging in seconds', 13 ),
                    new bar_stack_key( '#'.$excerciseColor->color3, 'Running in seconds', 13 ),
                    )
                );
            $bar_stack->set_tooltip( 'Date: #x_label#  Time: #val# seconds' );
            $y = new y_axis();
            $y->set_range( 0, $max*1.05, ceil($max/12) );
            $tooltip = new tooltip();
            $tooltip->set_hover();
            
            $chart->set_title( $title );
            $chart->add_element( $bar_stack );
            $chart->set_x_axis( $x );
            $chart->add_y_axis( $y );
            $chart->set_tooltip( $tooltip );
            break;
        case "solidDot": // weight chart
            $getWeightColors=$dbh->prepare("SELECT `dot`,`line`,`xLabel`,`xAxis`,`xGrid` FROM `weightColorPreferences` WHERE `userID`= :userID");
            $getWeightColors->execute(array("userID" => $user->getUserID()));
            $weightColors=$getWeightColors->fetchAll(PDO::FETCH_OBJ);
            $weightColor=$weightColors[0];
            
            $dot= new solid_dot();
            $dot->size(3)->halo_size(1)->colour('#'.$weightColor->dot);
            foreach($fields as $index => $value){
            	$tmpArray = $$index; // To fix empty/non-array values from being passed into array functions
            	if(!in_array($value,$ignoreFields)){
                    $max=is_array($tmpArray) && !empty($tmpArray) ? max($tmpArray) : 0; // Set to a reasonable value
                    $min=is_array($tmpArray) && !empty($tmpArray) ? min($tmpArray) : 0;
                    $line = new line();
                    $line->set_default_dot_style($dot);
                    $line->set_values( $tmpArray );
                    $line->set_width( 2 );
                    $line->set_colour( '#'.$weightColor->line );
                }else{
                    $x_labels = new x_axis_labels();
                    $x_labels->set_steps( 2 );
                    $x_labels->set_vertical();
                    $x_labels->set_colour( '#'.$weightColor->xLabel );
                    $x_labels->set_labels( $tmpArray );
                }
            }
            $x = new x_axis();
            $x->set_colour( '#'.$weightColor->xAxis );
            $x->set_grid_colour( '#'.$weightColor->xGrid );
            $x->set_offset( false );
            $x->set_steps(4);
            
            // Add the X Axis Labels to the X Axis
            $x->set_labels( $x_labels );
            $chart->set_x_axis( $x );
            $y = new y_axis();
            $y->set_range( $user->getGoalWeight(), $max*1.05, floor(((($max*1.10)-$user->getGoalWeight())/10)) );
            $chart->set_title( $title );
            $chart->add_element( $line );
            $chart->set_y_axis( $y );
            break;
    }
    return $chart->toPrettyString();
}