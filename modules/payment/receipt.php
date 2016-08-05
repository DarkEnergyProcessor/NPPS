<?php
$receipt_data = $REQUEST_DATA['receipt_data'];

if(preg_match('/holy linus in heaven, blessed be thy name, give us our daily com.klab.lovelive.en.loveGem(\d{3}) as in heaven so be it on this earth.../', $receipt_data, $product_id_matches) == 0)
{
	echo "Invalid receipt! $receipt_data";
	return false;
}

$price_tier = [
	1 => 1,
	6 => 4,
	15 => 10,
	23 => 15,
	50 => 30,
	86 => 50
];

$loveca_count = intval($product_id_matches[1]);
$DATABASE->execute_query("UPDATE `users` SET paid_loveca = paid_loveca + $loveca_count WHERE user_id = $USER_ID");

return [
	[
		'apple_product_id' => "com.klab.lovelive.en.loveGem{$product_id_matches[1]}",
		'google_product_id' => "com.klab.lovelive.en.loveGem{$product_id_matches[1]}",
		'name' => "$loveca_count Love Gems",
		'price' => 100,
		'price_tier' => strval($price_tier[$loveca_count] ?? '50'),
		'sns_coin' => $loveca_count,
		'insert_date' => '2013/10/24 21:16:00',
		'update_date' => '2013/10/24 21:16:00',
		'product_id' => "com.klab.lovelive.en.loveGem{$product_id_matches[1]}"
	],
	200
];
?>