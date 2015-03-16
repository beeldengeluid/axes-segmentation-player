<?php

function toPrettyTime($ms) {
	$ms = intval($ms);
	$hours = 0;
	$minutes = 0;
	$seconds = 0;
	while($ms >= 360000){
		$hours++;
		$ms -= 360000;
	}
	while($ms >= 60000) {
		$minutes++;
		$ms -= 60000;
	}
	while($ms >= 1000) {
		$seconds++;
		$ms -= 1000;
	}
	if($hours < 10) {$hours = '0'.$hours;}
	if($minutes < 10) {$minutes = '0'.$minutes;}
	if($seconds < 10) {$seconds = '0'.$seconds;}
	return $hours.':'.$minutes.':'.$seconds;
}

function generateList() {
	$redis = new Redis();
	$redis->connect('127.0.0.1', 6379);
	$redis->select(11);
	$ids = $redis->hkeys('savedata');
	$rec = null;
	$list = array();
	foreach($ids as $id) {
		$rec = json_decode($redis->hget('savedata', $id));
		if($rec->relevant) {
			foreach($rec->relevant as $video) {
				$item = array(
					'userID' => $rec->userID,
					'needID' => $id,
					'description' => $rec->description,
					'videoTitle' => $video->title,
					'videoUrl' => $video->videoURL,
					'videoStart' => toPrettyTime($video->start),
					'videoEnd' => toPrettyTime($video->end)
				);
				if($video->anchors) {
					foreach($video->anchors as $anchor) {
						$item['anchorTitle'] = $anchor->title;
						$item['anchorDescription'] = $anchor->description;
						$item['anchorStart'] = toPrettyTime($anchor->start);
						$item['anchorEnd'] = toPrettyTime($anchor->end);
					}
				}
				array_push($list, $item);
			}
		}
	}
	return $list;
}

$list = generateList();

//echo var_dump($redis->hget('savedata', $id));

?>

<!doctype HTML>
<html>

<head>
	<title>Action log</title>
	<link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

	<table class="table">
		<tr>
			<th>User ID</th>
			<th>Need ID</th>
			<th>Description</th>
			<th>Video</th>
			<th>Start</th>
			<th>End</th>
			<th>Anchor title</th>
			<th>Anchor description</th>
			<th>Start</th>
			<th>End</th>

		</tr>
		<?php foreach($list as $item) { ?>
			<tr>
				<td><?php echo $item['userID']; ?></td>
				<td><?php echo $item['needID']; ?></td>
				<td><?php echo $item['description']; ?></td>
				<td>
					<a href="<?php echo $item['videoUrl']; ?>" target="_video">
						<?php echo $item['videoTitle']; ?>
					</a>
				</td>
				<td><?php echo $item['videoStart']; ?></td>
				<td><?php echo $item['videoEnd']; ?></td>
				<td><?php echo $item['anchorTitle']; ?></td>
				<td><?php echo $item['anchorDescription']; ?></td>
				<td><?php echo $item['anchorStart']; ?></td>
				<td><?php echo $item['anchorEnd']; ?></td>
			</tr>
		<?php } ?>
	</table>

</body>




</html>