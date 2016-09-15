<?php
define(DB_HOST, 'localhost');
define(DB_USER, 'root');
define(DB_PASS, '');
define(DB_TABLE, 'face');

class Sql{
  private static $connector;
  
  private static function getConnector(){
    if (self::$connector == null){
      self::$connector =  new mysqli(DB_HOST, DB_USER, DB_PASS, DB_TABLE);
    }
    return self::$connector;
  }
  
  public static function saveFrame($id, $faceId){
    if($faceId){
      $sql = "INSERT INTO `face`.`frame` (`id`, `have_face`, `face_id`) VALUES ('".$id."', '1', '".$faceId."');";
    }
    else{
      $sql = "INSERT INTO `face`.`frame` (`id`, `have_face`, `face_id`) VALUES ('".$id."', '0', '');";
    }
    $mysql = self::getConnector();

    $mysql->query($sql);
    
  } 
  
  public static function saveEmotion($frame,$emotion,$value){
    $sql="INSERT INTO `face`.`emotion` (`frame_id`, `emotion`, `value`) VALUES ('".$frame."', '".$emotion."', '".$value."');";
    $mysql = self::getConnector();
    $mysql->query($sql);
  }
  
  public static function getEmotion($emotion){
    $sql = "SELECT * FROM `emotion` where emotion = '".$emotion."' order by frame_id";
    $mysql = self::getConnector();
    $result = $mysql->query($sql);
  
    $object = [];
    
    while($obj = $result->fetch_object()){ 
      $object[]=['x'=>date('Y-m-d',time()+$obj->frame_id*86400),'y'=>$obj->value]; 
    }
    return json_encode($object);
  }
}