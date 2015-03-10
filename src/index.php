<?php

/*
http://coolestguidesontheplanet.com/get-apache-mysql-php-phpmyadmin-working-osx-10-10-yosemite/
https://discussions.apple.com/docs/DOC-3083
http://tlrobinson.net/blog/2008/06/mac-os-x-web-sharing-apache-and-symlinks/
*/


// Load the Savant3 class file and create an instance.
require_once 'Savant3.php';

$tpl = new Savant3();
$tpl->display('templates/select.tpl.php');
?>