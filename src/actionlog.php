<?php


$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->select(11);

$ids = $redis->hkeys('savedata');


foreach($ids as $id) {
	echo $id;

	echo var_dump($redis->hget('savedata', $id));



}


?>