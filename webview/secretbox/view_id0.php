<?php
$user_agent = $REQUEST_HEADERS['user-agent'] ?? '';
$is_iphone = stripos($user_agent, 'iphone') >= 0;
$is_ipad = stripos($user_agent, 'ipad') >= 0;

$secretbox_info = $DATABASE->execute_query('SELECT name, r_list, sr_list, ur_list, sr_chance, ur_chance, sr_guarantee, sr_new, ur_new FROM `secretbox_list` WHERE id = 0')[0];
$r_chance = 100.0 - $secretbox_info[4] - $secretbox_info[5];
$secretbox_hash = hash('sha256', sprintf('%s:%d:%03d:%03d:%03d:%s:%s:%s:%s:%s',
	$secretbox_info[0],
	$secretbox_info[6],
	$r_chance,
	$secretbox_info[4],
	$secretbox_info[5],
	$secretbox_info[1],
	$secretbox_info[2],
	$secretbox_info[3],
	$secretbox_info[7] ?? '',
	$secretbox_info[8] ?? ''
));
$GLOBALS['attribute_names'] = [
	1 => 'Smile',
	2 => 'Pure',
	3 => 'Cool',
	5 => 'All'
];

if(isset($REQUEST_HEADERS['if-none-match']))
{
	
	if(strcmp($REQUEST_HEADERS['if-none-match'], $secretbox_hash) == 0)
	{
		// Client already have copy of it.
		http_response_code(304);
		exit;
	}
}

// Setup
$unit_db = new SQLite3Database('data/unit.db_');
$GLOBALS['unit_skill_names'] = [];
$r_list = [];
$sr_list = [];
$ur_list = [];

foreach($unit_db->execute_query('SELECT unit_skill_id, name FROM `unit_skill_m`') as $x)
	$GLOBALS['unit_skill_names'][$x[0]] = $x[1];

function common_unrange(array $m): string
{
    return implode(',', range($m[1], $m[2]));
}

function unraw_list(string $source, array &$dest, SQLite3Database $unit_db)
{
	$list = preg_replace_callback('/(\d+)-(\d+)/', 'common_unrange', $source);
	$unitlist = $unit_db->execute_query("SELECT unit_id, name, default_unit_skill_id, attribute_id FROM `unit_m` WHERE unit_id IN($list) ORDER BY unit_type_id, attribute_id");
	
	foreach($unitlist as $x)
		$dest[] = [
			'id' => $x[0],
			'name' => $x[1],
			'attribute' => $GLOBALS['attribute_names'][$x[3]],
			'skill' => $GLOBALS['unit_skill_names'][$x[2]],
			'new_flag' => false
		];
}

function set_new_flag(string $source, array &$unit_list)
{
	$list = explode(',', preg_replace_callback('/(\d+)-(\d+)/', 'common_unrange', $source));
	
	foreach($list as $x)
	{
		foreach($unit_list as &$y)
		{
			if($x == $y['id'])
				$y['new_flag'] = true;
		}
	}
}

unraw_list($secretbox_info[1], $r_list, $unit_db);
unraw_list($secretbox_info[2], $sr_list, $unit_db);
unraw_list($secretbox_info[3], $ur_list, $unit_db);
set_new_flag($secretbox_info[7] ?? '', $sr_list);
set_new_flag($secretbox_info[8] ?? '', $ur_list);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Honor Student and Regular Student Appearance Rates</title>
		<link rel="stylesheet" href="/resources/css1.3/bstyle.css">
		<link rel="stylesheet" href="/resources/css1.3/regulation.css">
		<style type="text/css">
			h1 {
				width:900px;
				height:78px;
				line-height:78px;
				font-size:25px;
				font-weight:bold;
				text-align:center;
				color:#fff;
				background:url(/resources/img/help/bg01_01.png)
				no-repeat;
			}
			table
			{
				text-align: left;
				margin: 20px;
				width: 90%
			}
		</style>
<?php
if($is_iphone):
?>
		<meta name="viewport" content="width=880px, minimum-scale=0.45, maximum-scale=0.45" />
<?php
elseif($is_ipad):
?>
		<meta name="viewport" content="width=1024px, minimum-scale=0.9, maximum-scale=0.9" />
<?php
else:
?>
		<meta name="viewport" content="width=880px, user-scalable=no, initial-scale=1, width=device-width" />
<?php
endif;
?>
	</head>
	<body>
		<div class="container">
			<h1>
				Honor Student Appearance Rates
			</h1>
			<div id="box1" style="visibility:visible; display:block;">
				<div class="content_regu" style="width:900px;">
					<div class="note">
						<span style="color:black;">
							<div style="text-align: left">
							   <p>
									<span style="text-align: center !important">■■■■■■■■■Scouting Honor Students■■■■■■■■■</span>
									<br />
									<br />
									■Rates by Rarity
									<br />
									UR   <?=$secretbox_info[5];?>%
									<br />
									SR   <?=$secretbox_info[4];?>%
									<br />
									R    <?=$r_chance;?>%
									<br />
									<br />
									■Attention
									<br />
									・Rates are rounded to the nearest third decimal place, so there are cases when the total is not a perfect 100%.
									<br />
									・A player may Scout the same Member more than once.
<?php
if($secretbox_info[7] || $secretbox_info[8]):
?>
									<br />
									・Members marked with <span style="color: blue">blue</span> have 40% chance to appear for particular rarity
<?php
endif;
if(!$secretbox_info[6]):
?>
									<br />
									・<span style="color: red">Scouting 11 times does not guarantee an SR!</span>
<?php
endif;
?>
									<br />
									<br />
									■Scoutable Members (<?=count($r_list)+count($sr_list)+count($ur_list);?> total)
									<br />
									UR (<?=count($ur_list);?> total)
									<br />
								</p>
							</div>
						</span>
						<table>
							<tr>
								<th colspan="2"  style="width:75%">Name</th>
								<th style="width:25%">Attribute</th>
							</tr>
<?php
foreach($ur_list as $x)
{
	$new_str = $x['new_flag'] ? 'style="color:blue"' : '';
?>
							<tr <?=$new_str;?>>
								<td>【<?=$x['skill'];?>】</td>
								<td><?=$x['name'];?></td>
								<td><?=$x['attribute'];?></td>
							</tr>
<?php
}
?>
						</table>
						<br />
						<br />
						SR (<?=count($sr_list);?> total)
						<br />
						<table>
							<tr>
									<th colspan="2" style="width:75%">Name</th>
									<th style="width:25%">Attribute</th>
							</tr>
<?php
foreach($sr_list as $x)
{
	$new_str = $x['new_flag'] ? 'style="color:blue"' : '';
?>
							<tr <?=$new_str;?>>
								<td>【<?=$x['skill'];?>】</td>
								<td><?=$x['name'];?></td>
								<td><?=$x['attribute'];?></td>
							</tr>
<?php
}
?>
						</table>
						<br />
						<br />
						R (<?=count($r_list);?> total)
						<br />
						<table>
							<tr>
								<th colspan="2" style="width:75%">Name</th>
								<th style="width:25%">Attribute</th>
							</tr>
<?php
foreach($r_list as $x)
{
	$new_str = $x['new_flag'] ? 'style="color:blue"' : '';
?>
							<tr <?=$new_str;?>>
								<td>【<?=$x['skill'];?>】</td>
								<td><?=$x['name'];?></td>
								<td><?=$x['attribute'];?></td>
							</tr>
<?php
}
?>
						</table>
					</div>
				</div>
				<div style="width: 960px">
					<img src="/resources/img/help/bg03.png">
				</div>
			</div>
		</div>
	</body>
</html>
<?php
header("ETag: $secretbox_hash");
?>