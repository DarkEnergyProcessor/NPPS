<?php
require('dlc/sifemu.php');

/* Blocking call */
function gain_sifemu_lock()
{
	$f = false;
	
	while(($f = fopen('.lock_sifemu', 'w')) == false)
		usleep(2000);	// 2ms
	
	return $f;
}

function release_sifemu_lock($f)
{
	fclose($f);
}

$os_target = strval($REQUEST_DATA['os'] ?? '');
$pkg_type = intval($REQUEST_DATA['package_type'] ?? -1);
$pkg_id = intval($REQUEST_DATA['package_id'] ?? -1);
$os_ver = strtolower($os_target);

if(strlen($os_ver) == 0 || $pkg_type == -1 || $pkg_id == -1)
{
	echo "Invalid 'type', 'id', or 'os'";
	return false;
}

if(strcmp($os_ver, 'android') && strcmp($os_ver, 'ios'))
{
	echo 'Invalid os';
	return false;
}

if($pkg_type == 0)
{
	echo 'type 0 is unimplemented';
	return false;
}

$filename_list = "data/{$pkg_type}_{$pkg_id}_$os_ver.txt";

/* If file not exists, download from prod server */
if(file_exists("dlc/$filename_list") == false)
{
	$lock = gain_sifemu_lock();
	$sif = SifEmu::load_new();
	$dl_links = $sif->download_additional($os_target, 0, $pkg_type, $pkg_id);
	
	if($dl_links == NULL)
	{
		$sif->save();
		release_sifemu_lock($lock);
		
		echo 'Failed to download data from server';
		http_response_code(500);
		return false;
	}
	
	$sif->save();
	release_sifemu_lock($lock);
	
	$json_hashes = [];
	$tempfile = tempnam('dlc/', 'zip');
	
	foreach($dl_links as $x)
	{
		file_put_contents($tempfile, fopen($x['url'], 'rb'));
		
		$file_sha256 = hash_file('sha256', $tempfile);
		$json_hashes[] = "$file_sha256.zip";
		
		rename($tempfile, "dlc/cache/$file_sha256.zip");
	}
	
	file_put_contents("dlc/$filename_list", json_encode($json_hashes));
}

$download_out = [];
$file_list = json_decode(file_get_contents("dlc/$filename_list"), true);

foreach($file_list as $z => $x)
	$download_out[] = [
		'download_additional_id' => $z,
		'url' => $_SERVER['HTTP_HOST']."/dlc/download.php?os={$os_ver}&type={$pkg_type}&id={$pkg_id}&index=$z",
		'size' => filesize("dlc/cache/$x")
	];

return [
	$download_out,
	200
];
?>