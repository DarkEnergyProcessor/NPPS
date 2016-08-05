<?php
$login_bonus_table = $DATABASE->execute_query("SELECT login_bonus_table, login_count, create_date FROM `users` WHERE user_id = $USER_ID")[0];

/* Initialize login bonus table */
$last_login = $login_bonus_table[1] - ($login_bonus_table[1] % 86400);
$current_day = $UNIX_TIMESTAMP - ($UNIX_TIMESTAMP % 86400);
$first_login = ($current_day - ($login_bonus_table[2] - ($login_bonus_table[2] % 86400))) / 86400;

/* Configure login bonus if not exists */
{
	$lbonus = $DATABASE->execute_query("SELECT login_bonus_id FROM `{$login_bonus_table[0]}` WHERE login_bonus_id = 0");
	
	if(count($lbonus) == 0)
		$DATABASE->execute_query("INSERT INTO `{$login_bonus_table[0]}` VALUES(0, 0)");
	
	foreach($DATABASE->execute_query("SELECT login_bonus_id FROM `special_login_bonus`") as $list_lbonus)
	{
		$lbonus = $DATABASE->execute_query("SELECT login_bonus_id FROM `{$login_bonus_table[0]}` WHERE login_bonus_id = {$list_lbonus[0]}");
		
		if(count($lbonus) == 0)
			$DATABASE->execute_query("INSERT INTO `{$login_bonus_table[0]}` VALUES({$list_lbonus[0]}, 0)");
	}
}

$main_lbonus_item = [];
$main_lbonus_counter = [0, 0];	// {before, after}
$main_lbonus_itemlist = [];
$special_lbonus = [];
$login_bonus_ok = $last_login < $current_day;

{
	/* Execute login bonus */
	$last_day_month = intval(date('t'));
	$current_month = intval(date('n'));
	
	/* login_bonus_id = 0 */
	{
		$lbonus_count = 0;
		$reset_lbonus = strcmp(date('Y-m', $last_login), date('Y-m')) != 0;
		
		if($reset_lbonus == false)
			$lbonus_count = $DATABASE->execute_query("SELECT counter FROM `{$login_bonus_table[0]}` WHERE login_bonus_id = 0")[0][0];
		
		$main_lbonus_counter[0] = $lbonus_count;
		
		foreach($DATABASE->execute_query("SELECT item_id, card_num, amount, day FROM `login_bonus` WHERE month = $current_month ORDER BY day LIMIT $last_day_month") as $itemlist)
		{
			$item_translated = item_translate_to_array($itemlist[0], $itemlist[2], $itemlist[1]);
			$main_lbonus_itemlist[] = [
				'lbonus_point' => $itemlist[3],
				'incentive_item_id' => $item_translated['item_id'],
				'amount' => $itemlist[2],
				'add_type' => $item_translated['add_type']
			];
		}
		
		if($login_bonus_ok)
		{
			$itemdata = $main_lbonus_itemlist[$lbonus_count++];
			$main_lbonus_item[] = [
				'incentive_id' => item_add_present_box($USER_ID, $itemdata['add_type'], [
					'message' => strftime("%B Login Bonus: Day $lbonus_count!"),
					'expire' => item_default_expiration($itemdata['add_type'], $itemdata['incentive_item_id'])
				], $itemdata['amount'], $itemdata['incentive_item_id']),
				'incentive_item_id' => $itemdata['incentive_item_id'],
				'amount' => $itemdata['amount'],
				'add_type' => $itemdata['add_type']
			];
			
			if($reset_lbonus)
				/* Reset counter instead */
				$DATABASE->execute_query("UPDATE `{$login_bonus_table[0]}` SET counter = 1 WHERE login_bonus_id = 0");
			else
				/* Increment */
				$DATABASE->execute_query("UPDATE `{$login_bonus_table[0]}` SET counter = counter + 1 WHERE login_bonus_id = 0");
		}
		
		$main_lbonus_counter[1] = $lbonus_count;
	}
	
	/* special login bonus */
	foreach($DATABASE->execute_query("SELECT * FROM `special_login_bonus` WHERE login_bonus_id > 0") as $lbonus_data)
	{
		$has_error = false;
		$itemlist = [];
		$lbonus_counter = $DATABASE->execute_query("SELECT counter FROM `{$login_bonus_table[0]}` WHERE login_bonus_id = {$lbonus_data[0]}")[0][0];
		$stamp_num = $lbonus_counter;
		$get_item = [
			'amount' => 0,
			'add_type' => 0,
			'incentive_item_id' => NULL
		];
		
		/* Process itemlist */
		foreach(explode(',', $lbonus_data[3]) as $n => $rewards)
		{
			$reward_data = explode(':', $rewards);
			$item_translate = NULL;
			
			if(isset($reward_data[2]))
				$item_translate = item_translate_to_array($reward_data[0], $reward_data[1], $reward_data[2]);
			else
				$item_translate = item_translate_to_array($reward_data[0], $reward_data[1]);
			
			if($item_translate == NULL)
			{
				echo "Cannot translate item ID. Entry: $rewards", PHP_EOL;
				$has_error = true;
				break;
			}
			
			$itemlist[] = [
				'nlbonus_item_id' => $n + 1,
				'seq' => $n + 1,
				'amount' => $item_translate['amount'],
				'add_type' => $item_translate['add_type'],
				'incentive_item_id' => $item_translate['item_id']
			];
		}
		
		if($has_error)
			continue;
		
		if($login_bonus_ok && count($itemlist) < $lbonus_counter)
		{
			/* Execute special login bonus */
			$reward_current = $itemlist[$lbonus_counter++];
			$get_item['amount'] = $reward_current['amount'];
			$get_item['add_type'] = $reward_current['add_type'];
			$get_item['incentive_item_id'] = $reward_current['incentive_item_id'];
			item_add_present_box($USER_ID, $reward_current['add_type'], [
				'message' => 'Special Login Bonus: '.$TEXT_TIMESTAMP
			], $reward_current['amount'], $reward_current['incentive_item_id'] ?? 0);
			
			$DATABASE->execute_query("UPDATE `{$login_bonus_table[0]}` SET counter = counter + 1 WHERE login_bonus_id = {$lbonus_data[0]}");
		}
		
		$special_lbonus[] = [
			'nlbonus_id' => $lbonus_data[0],
			'nlbonus_item_num' => count($itemlist),
			'detail_text' => $lbonus_data[1],
			'bg_asset' => $lbonus_data[2],
			'show_next_item' => $lbonus_counter > $stamp_num,
			'items' => $itemlist,
			'get_item' => $get_item,
			'stamp_num' => $stamp_num
		];
	}
	
	/* birthday login bonus */
	{
		$current_day = date('d-m');
		$lbonus_id = NULL;
		$lbonus_birthday = $DATABASE->execute_query("SELECT * FROM `birthday_login_bonus` WHERE date = ?", 's', $current_day);
		
		{
			$temp = explode('-', $current_day);
			$lbonus_id = ($temp[0] * 12 + ($temp[1] - 1)) << 16;
		}
		
		if(count($lbonus_birthday) > 0)
		{
			$lbonus_birthday = $lbonus_birthday[0];
			$get_item = [
				'amount' => 0,
				'add_type' => 0,
				'incentive_item_id' => NULL
			];
			$item_translate = NULL;
			
			if($lbonus_birthday[4] !== NULL)
				$item_translate = item_translate_to_array($lbonus_birthday[3], $lbonus_birthday[5], $lbonus_birthday[4]);
			else
				$item_translate = item_translate_to_array($lbonus_birthday[3], $lbonus_birthday[5]);
			
			if($login_bonus_ok)
			{
				/* Execute login bonus */
				$get_item['amount'] = $item_translate['amount'];
				$get_item['add_type'] = $item_translate['add_type'];
				$get_item['incentive_item_id'] = $item_translate['item_id'];
				item_add_present_box($USER_ID, $item_translate['add_type'], [
					'message' => 'Birthday Login Bonus: '.date('F jS')
				], $item_translate['amount'], $item_translate['item_id'] ?? 0);
			}
			
			$special_lbonus[] = [
				'nlbonus_id' => $lbonus_id,
				'nlbonus_item_num' => 1,
				'detail_text' => $lbonus_birthday[1],
				'bg_asset' => $lbonus_birthday[2],
				'show_next_item' => $login_bonus_ok,
				'items' => [
					[
						'nlbonus_item_id' => $lbonus_id + 1,
						'seq' => 1,
						'amount' => $item_translate['amount'],
						'add_type' => $item_translate['add_type'],
						'incentive_item_id' => $item_translate['item_id']
					]
				],
				'get_item' => $get_item,
				'stamp_num' => $login_bonus_ok ? 0 : 1
			];
		}
	}
}

if(count($main_lbonus_item) == 0)
	$main_lbonus_item = NULL;

$start_date = strtotime('first day of this month');
$end_date = strtotime('first day of next month');

$DATABASE->execute_query("UPDATE `users` SET login_count = $UNIX_TIMESTAMP WHERE user_id = $USER_ID");

return [
	[
		'login_count' => $first_login,
		'days_from_first_login' => $first_login,
		'before_lbonus_point' => $main_lbonus_counter[0],
		'after_lbonus_point' => $main_lbonus_counter[1],
		'last_login_date' => to_datetime($login_bonus_table[1]),
		'show_next_item' => date('d') != intval(date('t')),
		'items' => [
			'point' => $main_lbonus_item
		],
		'card_info' => [
			'start_date' => to_datetime($start_date - ($start_date % 86400)),
			'end_date' => to_datetime($end_date - ($end_date % 86400) - 1),
			'lbonus_count' => count($main_lbonus_itemlist),
			'items' => $main_lbonus_itemlist
		],
		'sheets' => $special_lbonus,
		'bushimo_reward_info' => []
	],
	200
];
?>
