<?php

/*
https://github.com/phpredis/phpredis#class-redis
http://brew.sh/
*/

$saveData = file_get_contents('php://input');

$id = json_decode($saveData)->ID;

if($id) {
	//echo $id;

	//Connect to Redis
	$redis = new Redis();
	$redis->connect('127.0.0.1', 6379);
	$redis->auth('somepass');
	$redis->select(11);


	$redis->hset('savedata' , $id, $saveData);

	//return the record from record to make sure it is accessible
	echo $redis->hget('savedata', $id);
	$redis->close();
} //else the javascript will trigger an error

?>