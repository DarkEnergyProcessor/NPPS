<?php
if(!isset($_GET['announce_id']) || !is_numeric($_GET['announce_id']))
	exit;

$user_agent = $REQUEST_HEADERS['user-agent'] ?? '';
$display_index = $_GET['disp_faulty'] ?? 0;
$is_iphone = stripos($user_agent, 'iphone') !== false;
$is_ipad = stripos($user_agent, 'ipad') !== false;

if(!is_numeric($display_index))
	$display_index = 0;

$announce_id = $_GET['announce_id'];

$db = new SQLite3('webview/announce/announce_list.db');
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

$announce_data = $db->query("SELECT title, detail FROM `announcement_list` WHERE announcement_id = $announce_id")->fetchArray(SQLITE3_ASSOC);

if($announce_data === false)
	exit;

$db->close();
?>
<!DOCTYPE html> 
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="apple-mobile-web-app-capable" content="yes">

		<title><?=$announce_data['title']?></title>
		<link rel="stylesheet" href="/resources/css1.3/bstyle.css">
		<link rel="stylesheet" href="/resources/css1.3/news.css">
<?php
if($is_iphone):
?>
		<meta name="viewport" content="width=960px, minimum-scale=0.45, maximum-scale=0.45, user-scalable=no" />
<?php
elseif($is_ipad):
?>
		<meta name="viewport" content="width=1024px, minimum-scale=0.9, maximum-scale=0.9, user-scalable=no" />
<?php
else:
?>
		<meta name="viewport" content="width=960px, user-scalable=no, initial-scale=1, width=device-width" />
<?php
endif;
?>
		<style>
		html, body {
			background-color: transparent;
		}
		p {     
			background-image: url(/resources/img/help/bug_trans.png); 
		}
		li {
			list-style-type: circle;
			margin-left: 30px;
		}
		blockquote
		{
			color: grey;
			padding-left: 20px;
			border-left: 10px solid grey;
		}
		</style>
	</head>
	<body>
		<div style="background: url(/resources/img/help/com_button_01se.png) no-repeat -9999px -9999px;"></div>
		<div id="wrapper">
			<div class="title_news fs28">
				<span class="ml30"><?=$announce_data['title']?></span>
				<a href="/webview.php/announce/index?disp_faulty=<?=$display_index?>" id="back">
					<div class="topback" style="width: 86px; height: 58px;">
					</div>   
				</a> 
			</div>

			<div class="content_news">
				<div class="note">
					<?=$announce_data['detail']?>
					<a href="/webview.php/announce/index?disp_faulty=<?=$display_index?>" id="back">
						<div class="bottomback" style="width: 86px; height: 58px;"></div>
					</a> 
				</div>
			</div>
			<div class="footer_news fs34">
				<img src="/resources/img/help/bg03.png" width="100%" >
			</div>
		</div>
	</body>
</html>
