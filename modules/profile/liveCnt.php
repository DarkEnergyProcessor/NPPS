<?php
$target_user_id = intval($REQUEST_DATA['user_id']);

if($target_user_id == 0)
{
	echo 'Invalid user ID!';
	return false;
}

$live_db = npps_get_database('live');

$get_live_setting_id = function(int $live_difficulty_id) use($live_db): int
{
	$temp = $live_db->execute_query("SELECT live_setting_id FROM `normal_live_m` WHERE live_difficulty_id = $live_difficulty_id");
	if(count($temp) > 0)
		return $temp[0][0];
	
	$temp = $live_db->execute_query("SELECT live_setting_id FROM `special_live_m` WHERE live_difficulty_id = $live_difficulty_id");
	if(count($temp) > 0)
		return $temp[0][0];
	
	return 0;
};

$easy = 0;
$normal = 0;
$hard = 0;
$expert = 0;
$live_table = $DATABASE->execute_query("SELECT live_table FROM `users` WHERE user_id = $target_user_id")[0][0];

foreach($DATABASE->execute_query("SELECT live_id FROM `$live_table` WHERE times > 0") as $lid)
{
	$setting_id = $get_live_setting_id($lid[0]);
	$diff = $live_db->execute_query("SELECT difficulty FROM `live_setting_m` WHERE live_setting_id = $setting_id")[0][0];
	
	switch($diff)
	{
		case 1:
		{
			$easy++;
			break;
		}
		case 2:
		{
			$normal++;
			break;
		}
		case 3:
		{
			$hard++;
			break;
		}
		case 4:
		case 5:
		{
			$expert++;
			break;
		}
		default:
		{
			break;
		}
	}
}

return [
	[
		[
			'difficulty' => 1,
			'clear_cnt' => $easy
		],
		[
			'difficulty' => 2,
			'clear_cnt' => $normal
		],
		[
			'difficulty' => 3,
			'clear_cnt' => $hard
		],
		[
			'difficulty' => 4,
			'clear_cnt' => $expert
		]
	],
	200
];
?>