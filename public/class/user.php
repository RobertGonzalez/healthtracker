<?php
class user{
    private static $instance,$initialized;
    private $name,$goalWeight,$userID,$public,$online,$initializationFailed;
    
    private function __construct($userID){
        $this->initializationFailed=false;
        $dbh=database::get_instance();
        $user=$dbh->prepare("SELECT `name`,`goalWeight`,`public` FROM `users` WHERE `userID`= :userID");
        $user->execute(array("userID" => $userID));
        $userInfo=$user->fetchAll(PDO::FETCH_OBJ);
        $userInfo=$userInfo[0];
        if(!isset($userInfo->name)){
            $this->initializationFailed=true;
            return;
        }
        if($userInfo->public<1){
            $this->initializationFailed=true;
            return;
        }
        
        $this->public=$userInfo->public;
        
        if(!$this->public){
            if($_SESSION['activeUser']!=null && $_SESSION['activeUser']->getUserID()!=$userID)
            {
                print("This user ID is marked private");
                die();
            }
        }
        $this->name=$userInfo->name;
        $this->goalWeight=$userInfo->goalWeight;
        $this->userID=$userID;
        $this->online=true;
    }
    
    public function getGoalWeight(){
        return (double)$this->goalWeight;
    }
    
    public function getInitializationFailed(){
        if(self::$initialized==true){
            return $this->initializationFailed;
        }
        return self::$initialized;
    }
    
    public function getUserID(){
        return (int)$this->userID;
    }
    
    public static function get_instance($userID=0){
        self::$initialized=true;
        if(!isset(self::$instance)){
            if($userID==0){
                self::$initialized=false;
                return;
            }
            self::$instance=new user($userID);
        }
        return self::$instance;
    }
}