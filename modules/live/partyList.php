<?php
$friend_list = $DATABASE->execute_query("SELECT friend_list FROM `users` WHERE user_id = $USER_ID")[0][0];
$random_people = $DATABASE->execute_query("SELECT user_id FROM `users` WHERE level > 1 AND user_id <> $USER_ID AND user_id NOT IN($friend_list) ORDER BY RANDOM() LIMIT 3");
$unit_db = new SQLite3Database('data/unit.db_');
$party_list_out = [];

$return_user_info = function(int $user_id, SQLite3Database $unit_db, bool $is_friend = false): array
{
	$basic_info = user_get_basic_info($user_id, $unit_db);
	return [
		'user_info' => [
			'user_id' => $user_id,
			'name' => $basic_info['name'],
			'level' => $basic_info['level']
		],
		'center_unit_info' => [
			'unit_id' => $basic_info['unit_info']['unit_id'],
			'level' => $basic_info['unit_info']['level'],
			'smile' => $basic_info['unit_info']['smile'],
			'cute' => $basic_info['unit_info']['pure'],
			'cool' => $basic_info['unit_info']['cool'],
			'max_hp' => $basic_info['unit_info']['hp'],
			'unit_skill_level' => $basic_info['unit_info']['skill_level'],
			'love' => $basic_info['unit_info']['bond'],
			'is_rank_max' => $basic_info['unit_info']['idolized'],
			'is_love_max' => $basic_info['unit_info']['bond_max'],
			'is_level_max' => $basic_info['unit_info']['level_max']
		],
		'setting_award_id' => $basic_info['badge'],
		'available_social_point' => $is_friend ? 10 : 5,
		'friend_status' => intval($is_friend)
	];
};

if(strlen($friend_list) > 0)
	foreach(explode(',', $friend_list) as $fuid)
		$party_list_out[] = $return_user_info(intval($fuid), $unit_db, true);

foreach($random_people as $uids)
	$party_list_out[] = $return_user_info($uids[0], $unit_db);

return [
	[
		'party_list' => $party_list_out
	],
	200
];
?>