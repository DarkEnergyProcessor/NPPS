<?php
if(!isset($REQUEST_DATA['client_version']) || !is_string($REQUEST_DATA['client_version']))
{
	echo 'Invalid client_version';
	return false;
}

if(strpos(EXPECTED_CLIENT, '*') !== false)
{
	return [[], 200];
}

$download_list = [];

if(version_compare($REQUEST_DATA['client_version'], '7.3.64') < 0)
{
	// 7.3.64
	$download_list[] = [
		'download_update_id' => 0,
		'url' => 'http://127.0.0.1/dlc/data/7.3.64_1.zip',
		'size' => filesize('dlc/data/7.3.64_1.zip')
	];
	$download_list[] = [
		'download_update_id' => 1,
		'url' => 'http://127.0.0.1/dlc/data/7.3.64_2.zip',
		'size' => filesize('dlc/data/7.3.64_2.zip')
	];
}

if(version_compare($REQUEST_DATA['client_version'], '7.3.65') < 0)
{
	// 7.3.65
	$download_list[] = [
		'download_update_id' => 2,
		'url' => 'http://127.0.0.1/dlc/data/7.3.65_1.zip',
		'size' => filesize('dlc/data/7.3.65_1.zip')
	];
	$download_list[] = [
		'download_update_id' => 3,
		'url' => 'http://127.0.0.1/dlc/data/7.3.65_2.zip',
		'size' => filesize('dlc/data/7.3.65_2.zip')
	];
}
return [
	$download_list,
	200
];
