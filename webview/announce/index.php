<?php
// Maximum page that can be shown. Change if necessary
$MAX_PAGE_SIZE = 10;

$user_agent = $REQUEST_HEADERS['user-agent'] ?? '';
$is_iphone = stripos($user_agent, 'iphone') !== false;
$is_ipad = stripos($user_agent, 'ipad') !== false;
$db = new SQLite3('webview/announce/announce_list.db');
$db->busyTimeout(5000);
/*
The SQL table:
announcement_id - ID of the announcement
announcement_type - The announce type. 0 = Notifications (default), 1 = Errors, 2 = Updates.
title - The announcement title
short_detail - Short info of the announcement. HTML-formatted.
detail - More detail of the announcement. HTML formatted.
*/
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

$announce_display_location = $_GET['disp_faulty'] ?? 0;
$page_location = $_GET['page'] ?? 1;
$first_ten = [];

if(!is_numeric($announce_display_location))
	$announce_display_location = 0;

if(!is_numeric($page_location))
	$page_location = 1;

$page_offset = $page_location * $MAX_PAGE_SIZE - $MAX_PAGE_SIZE;
$show_more = ($db->querySingle("SELECT COUNT(announcement_id) FROM `announcement_list` WHERE announcement_type = $announce_display_location LIMIT 0 OFFSET $page_offset") ?: 0) > $MAX_PAGE_SIZE;

if($res = $db->query("SELECT announcement_id, title, short_detail FROM `announcement_list` WHERE announcement_type = $announce_display_location ORDER BY announcement_id DESC LIMIT $MAX_PAGE_SIZE OFFSET $page_offset"))
{
	while($row = $res->fetchArray())
		$first_ten[] = $row;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Notices</title>
		<link href="/resources/css1.3/bstyle.css" rel="stylesheet">
		<link href="/resources/css1.3/news.css" rel="stylesheet">
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
		<script type="text/javascript">
			function getUrlParamFromKey(key) {
				var temp_params = window.location.search.substring(1).split('&');
				var vars =  new Array();
				for(var i = 0; i <temp_params.length; i++) {
					params = temp_params[i].split('=');
					vars[params[0]] = params[1];
				}
				return vars[key];
			}


			window.onload = function() {
				var disp_faulty = getUrlParamFromKey("disp_faulty");
				if (disp_faulty == 1) {
					tabItem = document.getElementById("newstab3");
				} else if (disp_faulty == 2) {
					tabItem = document.getElementById("newstab2");
				} else {
					tabItem = document.getElementById("newstab1");
				}
				tabItem.getElementsByTagName('a')[0].onclick=function(){return false;};
				tabItem.className += " open";
			}
		</script>
		<style type="text/css">
			.note img
			{
				margin-left: -12px;
			}
		</style>
	</head>
	<body>
		<div id="wrapper_news">
<?php
if($is_iphone || $is_ipad):
?>
			<div class="title_news_all_tab" style="position: fixed; top:0px; width:960px; z-index:20; background-color:white; height: 82px;">
<?php
else:
?>
			<div class="title_news_all_tab">
<?php
endif;

$style_position = $is_iphone || $is_ipad ? "fixed" : "absolute";

$last_notif = ($db->querySingle('SELECT announcement_id FROM `announcement_list` WHERE announcement_type == 0') ?: 0);
$last_update = ($db->querySingle('SELECT announcement_id FROM `announcement_list` WHERE announcement_type == 2') ?: 0);
$last_error = ($db->querySingle('SELECT announcement_id FROM `announcement_list` WHERE announcement_type == 1') ?: 0);
if(
	($_COOKIE['last_notif_id'] ?? 0) != $last_notif
):
?>
				<img src="/resources/img/help/new.png" style="position: <?=$style_position?>; top:0px; left:280px; z-index:25; ">
<?php
	setcookie('last_notif_id', strval($last_notif), 2147483647);
endif;

if(
	($_COOKIE['last_update_id'] ?? 0) != $last_update
):
?>
				<img src="/resources/img/help/new.png" style="position: <?=$style_position?>; top:0px; left:600px; z-index:25; ">
<?php
	setcookie('last_update_id', strval($last_update), 2147483647);
endif;

if(
	($_COOKIE['last_error_id'] ?? 0) != $last_error
):
?>
				<img src="/resources/img/help/new.png" style="position: <?=$style_position?>; top:0px; left:920px; z-index:25; ">
<?php
	setcookie('last_error_id', strval($last_error), 2147483647);
endif;
?>
				<ul id="tabs">
					<li class="fs30" id="newstab1" name="box1">
						<a href="index?disp_faulty=0" style="color: #fff;">Notifications</a>
					</li>
					<li class="fs30" id="newstab2" name="box2">
						<a href="index?disp_faulty=2" style="color: #fff;">Updates</a>
					</li>
					<li class="fs30" id="newstab3" name="box3">
						<a href="index?disp_faulty=1" style="color: #fff;">Errors</a>
					</li>
				</ul>
			</div>
			<div class="content_news_all">
<?php
if($is_iphone || $is_ipad):
?>
				<div class="note" style="margin-top:100px;">
<?php
else:
?>
				<div class="note">
<?php
endif;
?>
					<div id="box1">
<?php
foreach($first_ten as $val):
?>
						<a class="big-link" data-animation="fade" data-reveal-id="readlist01" href="/webview.php/announce/detail?announce_id=<?=$val[0]?>&disp_faulty=<?=$announce_display_location?>">
							<div class="title_news_all fs30">
								<span class="ml40"><?=$val[1]?></span>
							</div>
							<div class="content_all">
								<div class="note">
									<?=$val[2]?>
								</div>
							</div>
						</a>
<?php
endforeach;

if($show_more):
?>
						<a href="/webview.php/announce/index?disp_faulty=<?=$announce_display_location?>&page=<?=$page_location+1?>">
							Show more...
						</a>
<?php
endif;
?>
					</div>
				</div>
			</div>
			<div class="footer_news_all">
				<img src="/resources/img/help/bg03.png" width="100%">
			</div>
		</div>
	</body>
</html>
<?php
$db->close();
