<?php
header("Access-Control-Allow-Origin: *");

require_once 'Savant3.php';
$tpl = new Savant3();

//connect to the redis store
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->select(11);

//if the id is supplied via GET use that
$id = $_GET['id'];

if(!$id) {
	$data = $_POST['data'];
	$data = str_replace('\\', '', $data);//somehow data is passed along differently on some servers
	$id = json_decode($data)->ID;

	//only save if the need ID does not exist
	if(!$redis->hexists('savedata', $id)) {
		$redis->hset('savedata' , $id, $data);
	}
}


if($redis->hexists('savedata', $id)) {
	//always return the data from Redis, to make sure it works correctly
	$data = $redis->hget('savedata', $id);
	$redis->close();

	//add the data to the template
	$tpl->data = $data;
	$tpl->vd = json_decode($data);
	$tpl->display('templates/segmentvideo.tpl.php');
} else {
	echo 'That ID does not exist in the evaluation database';
}

?>