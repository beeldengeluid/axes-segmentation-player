<?php
header("Access-Control-Allow-Origin: *");

require_once 'Savant3.php';
$tpl = new Savant3();

$data = $_POST['data'];
$data = str_replace('\\', '', $data);//somehow data is passed along differently on some servers

//connect to the redis store
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->select(11);
$id = json_decode($data)->ID;

//only save if the need ID does not exist
if(!$redis->hexists('savedata', $id)) {
	$redis->hset('savedata' , $id, $data);
}

//always return the data from Redis, to make sure it works correctly
$data = $redis->hget('savedata', $id);
$redis->close();

//add the data to the template
$tpl->data = $data;
$tpl->vd = json_decode($data);
$tpl->display('templates/segmentvideo.tpl.php');

?>