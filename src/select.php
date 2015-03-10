<?php

// Load the Savant3 class file and create an instance.
require_once 'Savant3.php';
$tpl = new Savant3();


/*
{
 "ID": "123124asfwr234",
 "description": "I want to find information on the Dutch royal family.",
 "relevant": [ // items marked as relevant
    {
      "videoURL": "http://axes.ch.bbc.co.uk/collections/cAXES/videos/cAXES/v20080516_100000_bbcone_to_buy_or_not_to_buy.webm",
      "keyframeURL": "",
      "start": 12123, // time in milli seconds
      "end": 12314,
    }
 ]
}
*/

$data = $_POST['data'];

$data = str_replace('\\', '', $data);

//echo $data;

$tpl->data = $data;
$tpl->display('templates/segmentvideo.tpl.php');
?>