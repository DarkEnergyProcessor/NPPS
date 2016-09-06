<?php
// It only lists files which already available in server.
// It won't download data from prod server

$pkg_type = intval($REQUEST_DATA['package_type'] ?? 0);
$os_ver = strtolower(strval($REQUEST_DATA['os'] ?? ''));
$excluded = $REQUEST_DATA['excluded_package_ids'] ?? [];
$file_list = [];

if(!is_array($excluded))
	$excluded = [];

if(strcmp($os_ver, 'android') && strcmp($os_ver, 'ios'))
	goto output_result;

foreach((glob("dlc/data/{$pkg_type}_*_$os_ver.txt") ?: []) as $files)
{
	if(preg_match('/\d+_(\d+)_.+/', $files, $match_out) == 0)
		continue;
	
	error_log(var_export($match_out, true), 4);
	
	if(array_search(intval($match_out[1]), $excluded) === false)
	{
		// Add to list
		foreach(json_decode(file_get_contents("$files"), true) as $idx => $zipfile)
			$file_list[] = [
				'size' => filesize("dlc/cache/$zipfile"),
				'url' => $_SERVER['HTTP_HOST']."/dlc/download.php?os={$os_ver}&type={$pkg_type}&id={$match_out[1]}&index=$idx",
			];
	}
}

output_result:
return [
	$file_list,
	200
];
