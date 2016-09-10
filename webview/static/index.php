<?php
if(!isset($_GET['id']) || !is_numeric($_GET['id']))
	exit;

$filename = "webview/static/{$_GET['id']}.html";
$file_modified = strval(filemtime($filename) ?: '');
 
if(isset($REQUEST_HEADERS['if-modified-since']) && strcmp($REQUEST_HEADERS['if-modified-since'], $file_modified) == 0)
{
	http_response_code(304);
	exit;
}

if(file_exists($filename))
{
	header("Last-Modified: $file_modified"); 
	echo file_get_contents($filename);
}
else
	echo 'Invalid ID';
?>