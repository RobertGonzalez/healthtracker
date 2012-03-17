<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
class activeUser{
    //private static $instance;
    
    // refactor userID to be fbID or something more descriptive.
    
    private $name,$goalWeight,$userID,$public,$newUser,$localUserID;
    
    public function __construct($userID,$link,$name,$token,$username){
        $dbh=database::get_instance();
        $this->newUser=false;
        $user=$dbh->prepare("SELECT
            `users`.`userID` as `localUserID`,
            `users`.`name` as `name`
            , `users`.`goalWeight` as `goalWeight`
            , `users`.`public` as `public`
            , `facebookData`.`fbID`
        FROM
            `charlesd_weight`.`facebookData`
            INNER JOIN `charlesd_weight`.`users` 
                ON (`facebookData`.`userID` = `users`.`userID`)
        WHERE (`facebookData`.`fbID` = :userID);");
        $user->execute(array("userID" => $userID));
        $userInfo=$user->fetchAll(PDO::FETCH_OBJ);
        if(!isset($userInfo[0]->name)){
            // user doesn't exist, let's create them
            $insertUser=$dbh->prepare("INSERT INTO `users`(`name`,`goalWeight`) VALUES(:name, :goalWeight)");
            $insertUser->execute(array("name" => $name, "goalWeight" => 180));
            $ourUserID=$dbh->lastInsertId();
            
            $insertFBData=$dbh->prepare("INSERT INTO `facebookData`(`fbID`,`userID`,`fbName`,`fbLink`,`fbUsername`,`fbToken`) 
            VALUES(:fbID,:userID,:fbName,:fbLink,:fbUsername,:fbToken)");
            $insertFBData->execute(array("fbID" => $userID,"userID" => $ourUserID,"fbName" => $name,"fbLink" => $link,"fbUsername" => $username,"fbToken" => $token));
            
            $insertColors=$dbh->prepare("INSERT INTO `weightColorPreferences`(`userID`,`dot`,`line`,`xLabel`,`xAxis`,`xGrid`) 
            VALUES(:userID,:dot,:line,:xLabel,:xAxis,:xGrid)");
            $insertColors->execute(array("userID" => $ourUserID,"dot" => "3D5C56","line" => "3D5C56","xLabel" => "000","xAxis" => "000", "xGrid" => "D7E4A3"));
            
            $insertColorsPreferences=$dbh->prepare("INSERT INTO `excerciseColorPreferences`(`userID`,`color1`,`color2`,`color3`) 
            VALUES(:userID,:color1,:color2,:color3)");
            $insertColorsPreferences->execute(array("userID" => $ourUserID,"color1" => "0033CC","color2" => "50284A","color3" => "FF6633"));
            
            $this->newUser=true;
            $this->__construct($userID,$link,$name,$token,$username);
            return;
        }
        $insertFBData=$dbh->prepare("
        UPDATE `facebookData`
        SET `fbToken`= :fbToken
        WHERE `fbID`= :fbID ");
        $insertFBData->execute(array("fbID" => $userID,"fbToken" => $token));
        
        $userInfo=$userInfo[0];
        $this->public=$userInfo->public;
        $this->name=$userInfo->name;
        $this->goalWeight=$userInfo->goalWeight;
        $this->userID=$userID;
        $this->localUserID=$userInfo->localUserID;
    }
    
    public function getGoalWeight(){
        return (double)$this->goalWeight;
    }
    
    public function setGoalWeight($weight){
        $weight=(double)$weight;
        $dbh=database::get_instance();
        $user=$dbh->prepare("
        UPDATE `charlesd_weight`.`users` 
        SET
            `users`.`goalWeight` = :goalWeight
        WHERE
            `users`.`userID`= :userID");
        $user->execute(array("userID" => $this->localUserID,"goalWeight" => $weight));
        $this->goalWeight=$weight;
    }
    
    
    public function getDisplayName(){
        return $this->name;
    }
    
    public function setDisplayName($name){
        $dbh=database::get_instance();
        $user=$dbh->prepare("
        UPDATE `charlesd_weight`.`users` 
        SET
            `users`.`name` = :name
        WHERE
            `users`.`userID`= :userID");
        $user->execute(array("userID" => $this->localUserID,"name" => $name));
        $this->name=$name;
    }
    
    public function getPublic(){
        return $this->public;
    }
    
    public function setPublic($public){
        $public=(int)$public;
        $dbh=database::get_instance();
        $user=$dbh->prepare("
        UPDATE `charlesd_weight`.`users` 
        SET
            `users`.`public` = :public
        WHERE
            `users`.`userID`= :userID");
        $user->execute(array("userID" => $this->localUserID,"public" => $public));
        $this->public=$public;
    }
    
    public function newUser(){
        return $this->newUser;
    }
    
    public function online(){
        return true;
    }
    
    public function getUserID(){
        return (int)$this->localUserID;
    }
    
    /*public function get_instance($userID=0,$link,$name,$token,$username){
        if(!isset(self::$instance)){
            if($userID==0){
                print("This user ID is invalid");
                die();
            }
            self::$instance=new user($userID,$link,$name,$token,$username);
        }
        return self::$instance;
    }*/
}