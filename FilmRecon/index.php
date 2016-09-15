<?php
define(FACE_API, '6586af9d06c14c8988f0dfc53d15f5c0');
define(EMOTION_API,'72bb2e979fd049fda69060bd039176b1');
define (GROUP, 'main');
define(PERSON, '093893da-b0fb-488f-a78a-cda2d73943b2');
require('sql.php');


$count = file_get_contents('count.txt');

$step = 50;
$i=0;
for($i=1;$i<=$step;$i++){
  $count++;
  var_dump($count);
  $url = 'http://narod.test4you.biz/study/out/out'.$count.'.png';
  $object = FaceDetect($url);
  
  $hero = false;
  
  $found = false;
  if(count($object)){
    foreach($object as $face){
      $verify = VerifyFace($face->faceId);
      var_dump($verify);
      if ($verify->isIdentical){
        $hero = $face;
        $found = true;
        $rectangle = $face->faceRectangle->left.','.$face->faceRectangle->top.','.$face->faceRectangle->width.','.$face->faceRectangle->height;
        $emotions = GetEmotion($url,$rectangle);
        foreach($emotions[0]->scores as $key=>$score){
          Sql::saveEmotion($count,$key,$score);
          
        }
        break;
      }
    } 
  }
  
  if($found){
    Sql::saveFrame($count,$hero->faceId);
  }
  else{
    Sql::saveFrame($count,false);
  }
  
  file_put_contents('count.txt',$count);
}


/*
$face_list = [];
for($i=1;$i<=10;$i++){
  $face = AddPersonFace('http://narod.test4you.biz/study/example/ex'.$i.'.png');
  $face_list[] = $face->persistedFaceId;
}
var_dump($face_list);
*/

function GetEmotion($url,$faceRectangle){
  return CurlRequest('https://api.projectoxford.ai/emotion/v1.0/recognize?faceRectangles='.$faceRectangle, EMOTION_API, ['url'=>$url]);
}

function VerifyFace($faceId){
  return CurlRequest('https://api.projectoxford.ai/face/v1.0/verify', FACE_API, ['faceId'=>$faceId,'personId'=>PERSON,'personGroupId'=>GROUP]);
}

function FaceDetect($url){
  return CurlRequest('https://api.projectoxford.ai/face/v1.0/detect', FACE_API,['url'=>$url]);
}

function AddPersonFace($url){
  return CurlRequest('https://api.projectoxford.ai/face/v1.0/persongroups/'.GROUP.'/persons/'.PERSON.'/persistedFaces', FACE_API, ['url'=>$url]);
}

function CreatePersonGroup($id,$name){
   $object = CurlRequest('https://api.projectoxford.ai/face/v1.0/persongroups/'.$id,FACE_API,
                       ['name'=>$name]);
  return $object;
}


function CurlRequest($url, $key, $object){
  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Ocp-Apim-Subscription-Key: '.$key,
    'Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($object));
  $output = curl_exec($ch);
  $output =  json_decode($output);
  var_dump($output);
  
  sleep(1);
  if(isset($output->error)){
    $output = CurlRequest($url, $key, $object);
  }
  return $output;
}
?>
<script>
//Auto page part. To make step by step
function refresh() { 
 window.location.reload(true);
}

setTimeout(refresh, 1000);
</script>

