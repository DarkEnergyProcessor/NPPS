<?php
// Usage: Send POST request to http://<npps>/system/convert_beatmap.php?live_setting_id=<live_setting_id> where "notes_data" form is JSON-encoded notes data
$is_post = strcmp($_SERVER['REQUEST_METHOD'], 'POST') == 0;

if($is_post)
{
	$live_setting_id = $_GET['live_setting_id'] ?? 0;
	$notes_data = $_POST['notes_data'] ?? '';

	if($live_setting_id == 0 || strlen($notes_data) == 0)
		exit("Invalid data passed\nUsage: Send POST request to http://<npps>/system/convert_beatmap.php?live_setting_id=<live_setting_id> where \"notes_data\" form is JSON-encoded notes data");

	$notes_list = json_decode($notes_data, true);
	$filename = sprintf('../data/notes/Live_s%04d.note', $live_setting_id);
	$notes_dist = [0, 0, 0, 0, 0, 0, 0, 0, 0];

	if(file_exists($filename))
		file_put_contents($filename, '');

	$note_db = new SQLite3($filename);
	$note_db->exec('CREATE TABLE `notes_list` (
		timing_sec REAL NOT NULL,
		notes_attribute INTEGER NOT NULL,
		effect INTEGER NOT NULL,
		effect_value REAL DEFAULT NULL,
		position INTEGER NOT NULL
	)');
	$note_db->exec('BEGIN');
	$stmt = $note_db->prepare('INSERT INTO `notes_list` VALUES (?, ?, ?, ?, ?)');

	foreach($notes_list as $x)
	{
		$new_effect_val = NULL;
		
		if($x['effect'] == 3)
			$new_effect_val = floatval($x['effect_value']);
		
		$notes_dist[$x['position'] - 1]++;
		
		$stmt->bindValue(1, floatval($x['timing_sec']), SQLITE3_FLOAT);
		$stmt->bindValue(2, $x['notes_attribute'], SQLITE3_INTEGER);
		$stmt->bindValue(3, $x['effect'], SQLITE3_INTEGER);
		$stmt->bindValue(4, $new_effect_val, $new_effect_val == NULL ? SQLITE3_NULL : SQLITE3_FLOAT);
		$stmt->bindValue(5, $x['position'], SQLITE3_INTEGER);
		
		$stmt->execute();
		$stmt->clear();
	}

	$note_db->exec('COMMIT');
	$note_db->close();
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>NPPS Internal Beatmap Converter</title>
<?php
if(!$is_post):
?>
		<script>
			var hello_form = null;
			var live_setting_form = null
			var current_file = document.URL.split(/[\\/]/).pop().split("?")[0];
			
			function set_live_setting()
			{
				hello_form.setAttribute("action", current_file + "?live_setting_id=" + live_setting_form.value.toString())
			}
			
			function get_form_id()
			{
				hello_form = document.getElementById("hello_form");
				live_setting_form = document.getElementById("live_setting_id");
				
				set_live_setting();
			}
			
		</script>
<?php
endif;
?>
	</head>
<?php
if($is_post):
?>
	<body>
		<h1>Statistics</h1>
		<h4>Total Notes: <?=count($notes_list);?> note(s)</h4>
		<h4>Notes distribution</h4>
		<table border="1">
			<thead>
				<tr>
					<th>R4</th><th>R3</th><th>R2</th><th>R1</th><th>C</th><th>L1</th><th>L2</th><th>L3</th><th>L4</th>
				</tr>
			</thead>
			<tbody>
				<tr>
<?php
for($i = 8; $i >= 0; $i--)
	printf("<td>%d</td>", $notes_dist[$i]);
?>
				</tr>
			</tbody>
		</table>
	</body>
<?php
else:
?>
	<body onload="get_form_id()">
		<h1>Convert JSON Beatmap to SQLite (for NPPS server)</h1>
		<form id="hello_form" action="" method="post">
			live_setting_id: <input id="live_setting_id" name="live_setting_id" type="number" oninput="set_live_setting()" value="0"></input><br/>
			JSON-encoded notes data<br/>
			<textarea name="notes_data"></textarea><br/>
			<input type="submit"></input>
		</form>
	</body>
<?php
endif;
?>
</html>
