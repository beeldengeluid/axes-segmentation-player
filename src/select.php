<?php
header("Access-Control-Allow-Origin: *");

require_once 'Savant3.php';
$tpl = new Savant3();

$data = $_POST['data'];
$data = str_replace('\\', '', $data);//somehow data is passed along differently on some servers

$tpl->data = $data;

$tpl->vd = json_decode($data);
$tpl->display('templates/segmentvideo.tpl.php');
?>