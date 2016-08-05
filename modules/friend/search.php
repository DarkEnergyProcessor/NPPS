<?php
$result = $DATABASE->execute_query('SELECT user_id, name, level, max_unit, max_lp, max_friend, unit_table, last_active, bio FROM `users` WHERE invite_code = ?', 's', $REQUEST_DATA['invite_code']);

if(count($result) == 0)
	return [
		[
			'error_code' => 2400
		],
		600
	];

$result = $result[0];
$user_info = user_get_basic_info($result[0], new SQLite3Database('data/unit.db_'));
$is_friend = false;

if($result[0] != $USER_ID)
{
	$friend_list = explode(',', $DATABASE->execute_query("SELECT friend_list FROM `users` WHERE user_id = $USER_ID")[0][0]) ?? [];
	
	foreach($friend_list as $id)
	{
		if($id == $result[0])
		{
			$is_friend = true;
			break;
		}
	}
}

user_set_last_active($USER_ID, $TOKEN);

$bio_trunc = str_replace('\r', '', explode('\n', $result[8])[0]);
$bio_trunc = strlen($bio_trunc) > 10 ? substr($bio_trunc, 7).'...' : $bio_trunc;

return [
	[
		'user_info' => [
			'user_id' => $result[0],
			'name' => $result[1],
			'level' => $result[2],
			'cost_max' => 100,			// TODO
			'unit_max' => $result[3],
			'energy_max' => $result[4],
			'friend_max' => $result[5],
			'cost' => 0,
			'unit_cnt' => $DATABASE->execute_query("SELECT COUNT(unit_id) FROM `{$result[6]}`"),
			'elapsed_time_from_login' => time_elapsed_string($result[7]),
			'comment' => $bio_trunc
		],
		'center_unit_info' => [
			'unit_id' => $user_info['unit_info']['unit_id'],
			'level' => $user_info['unit_info']['level'],
			'rank' => $user_info['unit_info']['idolized'] ? 2 : 1,
			'max_hp' => $user_info['unit_info']['hp'],
			'smile' => $user_info['unit_info']['smile'],
			'cute' => $user_info['unit_info']['pure'],
			'cool' => $user_info['unit_info']['cool'],
			'is_rank_max' => $user_info['unit_info']['idolized'],
			'is_love_max' => $user_info['unit_info']['bond_max'],
			'is_level_max' => $user_info['unit_info']['level_max']
		],
		'setting_award_id' => $user_info['badge'],
		'is_alliance' => $is_friend,
		'friend_status' => $is_friend ? 1 : 0
	],
	200
];
?>