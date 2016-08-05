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

if(strcmp($_SERVER['REQUEST_METHOD'], "POST") == 0)
{
	define('MODE_POST', true);
	require('Parsedown.php');
	
	$Parsedown = new Parsedown();
	$db = new SQLite3('../webview/announce/announce_list.db');
	$db->busyTimeout(5000);
	$db->exec(<<<'QUERY'
CREATE TABLE IF NOT EXISTS `announcement_list` (
	announcement_id INTEGER PRIMARY KEY AUTOINCREMENT,
	announcement_type INTEGER NOT NULL DEFAULT 0,
	title TEXT NOT NULL,
	short_detail TEXT NOT NULL,
	detail TEXT NOT NULL
);
QUERY
);

	$stmt = $db->prepare('INSERT INTO `announcement_list` (announcement_type, title, short_detail, detail) VALUES (?, ?, ?, ?)');
	$stmt->bindValue(1, intval(isset_or_die($_POST['announcement_type'])), SQLITE3_INTEGER);
	$stmt->bindValue(2, isset_or_die($_POST['title']), SQLITE3_TEXT);
	$stmt->bindValue(3, $Parsedown->text(isset_or_die($_POST['short_detail'])), SQLITE3_TEXT);
	$stmt->bindValue(4, $Parsedown->text(isset_or_die($_POST['detail'])), SQLITE3_TEXT);
	if($stmt->execute() === false)
	{
		http_response_code(500);
		die("Error occured!");
	}
	
	$stmt->close();
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Add New Announcement</title>
	</head>
	<body>
		<h1>Add new announcement to WebView</h1>
		<form action="add_announcement.php" method="POST">
			<h4>Announcemnt Type</h4>
			<input type="radio" name="announcement_type" value="0" checked>Notification<br/>
			<input type="radio" name="announcement_type" value="2">Update<br/>
			<input type="radio" name="announcement_type" value="1">Error<br/>
			<h4>Title</h4>
			<input type="text" name="title" style="width:750px;"><br/>
			<h4>Short Description</h4>
			<textarea name="short_detail" style="width:750px;min-height:160px;"></textarea>
			<h4>Announcement Detail</h4>
			<textarea name="detail" style="width:750px;min-height:320px;"></textarea><br/>
			<input type="submit">
		</form>
<?php
if(defined('MODE_POST')):
?>
		<h4>Added!</h4>
<?php
endif;
?>
	</body>
</html>