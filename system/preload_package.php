<?php
function isset_or_die(&$var)
{
	if(!isset($var))
	{
		http_response_code(500);
		die("Error occured!");
	}
	
	return $var;
}

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

if(strcmp($_SERVER['REQUEST_METHOD'], "POST") == 0)
{
	assert(chdir('../'));
	
	include('dlc/sifemu.php');
	
	$pkg_type = intval($_POST['pkg_type']);
	$pkg_id = intval($_POST['pkg_id']);
	$os_target = $_POST['os_ver'];
	$os_ver = strtolower($os_target);
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
		
		define('PRELOAD_POST', 'Preloaded successfully');
	}
	else
		define('PRELOAD_POST', 'Already preloaded');
}

?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Preload Package</title>
	</head>
	<body>
		<h1>Preload Package to Server</h1>
		<p>
			pkg_type can be:<br/>
			0 = main download. pkg_id must be 0<br/>
			1 = live song. pkg_id is the live_track_id in live.db_</br>
			2 = main story. pkg_id is the scenario_chapter_id in scenario.db_</br>
			3 = side story. pkg_id is the unit_id in subscenario.db_<br/>
		</p>
		<form method="post">
			pkg_type: <input type="number" name="pkg_type" value="0"/><br/>
			pkg_id: <input type="number" name="pkg_id" value="0"/><br/>
			os: <select name="os_ver">
					<option value="Android" selected>Android</option>
					<option value="iOS">iOS</option>
				</select><br/>
			<input type="submit"><br/<
		</form>
<?php
if(defined('PRELOAD_POST')):
?>
		<p><?=PRELOAD_POST?></p>
<?php
endif;
?>
	</body>
</html>