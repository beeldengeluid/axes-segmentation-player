
<?php

$id = $_GET['id'];

if($id) {
	$redis = new Redis();
	$redis->connect('127.0.0.1', 6379);
	$redis->select(11);


	echo $redis->hget('savedata', $id);
	$redis->close();
} else {
	echo "Please supply a valid data id";
}

?>