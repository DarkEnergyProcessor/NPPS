<?php
require('sifemu.php');

$os_ver = strtolower($_GET['os'] ?? '');
$pkg_type = intval($_GET['type'] ?? -1);
$pkg_id = intval($_GET['id'] ?? -1);
$file_index = intval($_GET['index'] ?? -1);

if(strlen($os_ver) == 0 || $pkg_type == -1 || $pkg_id == -1 || $file_index == -1)
{
	header("HTTP/1.1 404 Invalid 'type', 'id', 'os', or 'index'");
	exit;
}

if(strcmp($os_ver, 'android') && strcmp($os_ver, 'ios'))
{
	header("HTTP/1.1 403 Invalid os");
	exit;
}

if($pkg_type == 0)
{
	header('HTTP/1.1 403 type 0 is unimplemented');
	exit;
}

$filename_target = "data/{$pkg_type}_{$pkg_id}_$os_ver.txt";

if(file_exists($filename_target) == false)
{
	header('HTTP/1.1 404 No such package');
	exit;
}

$download_id_list = json_decode(file_get_contents($filename_target), true);

if(isset($download_id_list[$file_index]) == false)
{
	header('HTTP/1.1 404 Invalid index');
	exit;
}

$zip_file = "cache/{$download_id_list[$file_index]}";

if(file_exists($zip_file) == false)
{
	header('HTTP/1.1 500 Zip file not found');
	exit;
}

header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename={$pkg_type}_{$pkg_id}_{$os_ver}_$file_index.zip");
header('Content-Length: '.filesize($zip_file));

readfile($zip_file);
exit;
