<?php
if(!isset($_GET['id']) || !is_numeric($_GET['id']))
	exit;

$filename = "webview/static/{$_GET['id']}.html";

if(file_exists($filename))
	echo file_get_contents($filename);
else
	echo 'Invalid ID';
?>