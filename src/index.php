<?php

// Load the Savant3 class file and create an instance.
require_once 'Savant3.php';
$tpl = new Savant3();


// Display a template using the assigned values.
$tpl->display('templates/index.tpl.php');
?>