-- This file contains bunch of SQL statement to initialize private server environment for the first time.

CREATE TABLE `secretbox_list` (						-- With loveca
	id INTEGER PRIMARY KEY AUTO_INCREMENT,			-- Loveca secretbox ID. ID 0 is reserved for Honor Scouting
	name TEXT DEFAULT NULL,							-- Secretbox name (default to "nil")
	banner_preview TEXT NOT NULL,					-- The banner preview image in-game
	banner_big TEXT NOT NULL,						-- The big banner image in-game
	banner_title TEXT DEFAULT NULL,					-- The text image in-game
	description TEXT DEFAULT NULL,					-- The scouting box description (defaults to "dummy")
	loveca_single_cost INTEGER NOT NULL DEFAULT 5,	-- Required loveca to soloyolo in this box. Automatically multipled by 10 for 10+1
	r_list TEXT NOT NULL,							-- Rares. It's unit ID. Example: "46,23-31" is permitted.
	sr_list TEXT NOT NULL,							-- SRs. Same as above
	ur_list TEXT NOT NULL,							-- URs. Same as above
	sr_chance DECIMAL(6,3) NOT NULL DEFAULT 9.000,	-- The chance for SR to appear. 9% by default
	ur_chance DECIMAL(6,3) NOT NULL DEFAULT 1.000,	-- The chance for UR to appear. 1% by default
	sr_guarantee BOOL NOT NULL DEFAULT 1,			-- Scout 11 times guarantee a SR? Yes by default.
	sr_new TEXT,									-- New SR list in `sr_list` for the 40% new member chance or NULL to disable it. The syntax is same as above.
	ur_new TEXT										-- Same as above, for URs
);
CREATE TABLE `coupon_secretbox_list` (				-- With scouting coupon
	id INTEGER PRIMARY KEY AUTO_INCREMENT,			-- Coupon secretbox ID. The in-game ID will be <ID> * 65536
	name TEXT NOT NULL,								-- Coupon secretbox name
	banner_big TEXT NOT NULL,						-- The big banner image in-game
	banner_title TEXT NOT NULL,						-- The text image in-game
	description TEXT DEFAULT NULL,					-- The scouting box description ("dummy" by default)
	coupon_cost INTEGER NOT NULL DEFAULT 5,			-- Cost of Blue Ticket.
	r_list TEXT DEFAULT NULL,						-- Rs. It's unit ID. Example: "46,23-31" is permitted. Empty string if r_chance is 0.0
	sr_list TEXT DEFAULT NULL,						-- SRs. Same as above.
	ur_list TEXT DEFAULT NULL,						-- URs. Same as above.
	r_chance DECIMAL(6,3) NOT NULL DEFAULT 0.000,	-- Chance R to appear. 0% = No Rs
	sr_chance DECIMAL(6,3) NOT NULL DEFAULT 80.000,	-- The chance for SR to appear. 80% by default
	ur_chance DECIMAL(6,3) NOT NULL DEFAULT 20.000	-- The chance for UR to appear. 20% by default
);
CREATE TABLE `secretbox_card_preview` (
	secretbox_id INTEGER NOT NULL,					-- Secretbox ID. For coupon, secretbox_id will be coupon_secretbox_id * 65536
	card_id INTEGER NOT NULL,						-- Target unit ID
	card_preview_path TEXT NOT NULL					-- Card preview image path in client
);
CREATE TABLE `event_list` (
	event_id INTEGER PRIMARY KEY,						-- The event ID from event_common.db_
	event_start INTEGER NOT NULL DEFAULT 0,				-- Unix timestamp when the event start
	event_end INTEGER NOT NULL DEFAULT 2147483647,		-- When the event is end.
	event_close INTEGER NOT NULL DEFAULT 2147483647,	-- When the event page is closed.
	token_image TEXT,									-- The token note image or NULL if it's not token event. Format: <token name>:<token image path>
	easy_song_list TEXT,								-- The easy song list. For token, it's the event song. For SM/MedFes, the song that available in event. Comma separated
	normal_song_list TEXT,								-- Same as above, for normal
	hard_song_list TEXT,								-- Same as above, for hard
	expert_song_list TEXT,								-- Same as above, for expert. Note for x4 event song: use "!<live id>" instead.
	technical_song_list TEXT,							-- Same as above, for technical (EXR). Score match only.
	easy_lp INTEGER NOT NULL DEFAULT 5,					-- Needed LP to play easy song. Ignored if `easy_song_list` is NULL
	normal_lp INTEGER NOT NULL DEFAULT 10,				-- Same as above (normal)
	hard_lp INTEGER NOT NULL DEFAULT 15,				-- Same as above (hard)
	expert_lp INTEGER NOT NULL DEFAULT 25,				-- Same as above (expert)
	technical_lp INTEGER NOT NULL DEFAULT 25,			-- Same as above (technical)
	event_ranking_table TEXT NOT NULL,					-- The event player ranking list table.
	event_song_table TEXT DEFAULT NULL					-- The event song ranking list table for token event or NULL
);
CREATE TABLE `logged_in` (
	login_key TEXT,											-- The associated login key or NULL if stil in "authkey"
	login_pwd TEXT,											-- The associated login password or NULL if still in "authkey"
	token TEXT NOT NULL,									-- The token.
	time INTEGER NOT NULL,									-- Last activity time.
	pseudo_unit_own_id INTEGER NOT NULL DEFAULT 2147483647	-- Pseudo unit_owning_user_id to solve some problems related to unit.
);
CREATE TABLE `users` (
	user_id INTEGER PRIMARY KEY AUTO_INCREMENT,				-- The user ID
	login_key TEXT,											-- The associated login key
	login_pwd TEXT,											-- The associated login password
	passcode TEXT DEFAULT NULL,								-- The issued passcode in format <passcode>:<platform id>
	passcode_issue INTEGER DEFAULT NULL,					-- Unix timestamp when the passcode issued or NULL.
	platform_code TEXT DEFAULT NULL,						-- The platform token. For loading accounts with Google+/Game Center. In format <code>:<platform id>
	locked BOOL NOT NULL DEFAULT 0,							-- Is the account banned?
	tos_agree INTEGER NOT NULL DEFAULT 0,					-- Tos agree number.
	create_date INTEGER NOT NULL,							-- When this account is created
	first_choosen INTEGER NOT NULL DEFAULT 0,				-- The first choosen card ID in the game.
	name VARCHAR(10) NOT NULL DEFAULT "Null",				-- Nickname
	bio VARCHAR(105) NOT NULL DEFAULT "Hello!",				-- About me section.
	invite_code VARCHAR(9),									-- Friend ID
	last_active INTEGER NOT NULL,							-- Last active in unix timestamp
	login_count INTEGER NOT NULL DEFAULT 0,					-- Last lbonus/execute execution timestamp
	background_id INTEGER NOT NULL DEFAULT 1,				-- Set background
	badge_id INTEGER NOT NULL DEFAULT 1,					-- Set badge (titles)
	current_exp INTEGER NOT NULL DEFAULT 0,					-- Current EXP
	next_exp INTEGER NOT NULL, 								-- Next EXP before level up
	level INTEGER NOT NULL DEFAULT 1,						-- The player rank
	gold INTEGER NOT NULL DEFAULT 36400,					-- Gold amount
	friend_point INTEGER NOT NULL DEFAULT 5,				-- Friend Point amount
	paid_loveca INTEGER NOT NULL DEFAULT 0,					-- Amount of loveca that bought
	free_loveca INTEGER NOT NULL DEFAULT 0,					-- Amount of loveca that came in-game (not bought)
	scouting_ticket INTEGER NOT NULL DEFAULT 0,				-- Voucher amount
	scouting_coupon INTEGER NOT NULL DEFAULT 0,				-- Blue ticket amout
	max_lp INTEGER NOT NULL DEFAULT 25,						-- Maximum LP
	max_friend INTEGER NOT NULL DEFAULT 10,					-- Max friend
	overflow_lp INTEGER NOT NULL DEFAULT 0,					-- Amount of additional LP
	full_lp_recharge INTEGER NOT NULL DEFAULT 0,			-- Unix time before the LP fully recharged.
	max_unit INTEGER NOT NULL DEFAULT 90,					-- Maximum memberlist. Including the ones that increased with loveca.
	max_unit_loveca INTEGER NOT NULL DEFAULT 0,				-- Amount of "Increase Member Limit".
	main_deck INTEGER NOT NULL DEFAULT 1,					-- Which deck is set to "Main"?
	normal_sticker INTEGER NOT NULL DEFAULT 0,				-- R stickers
	silver_sticker INTEGER NOT NULL DEFAULT 0,				-- SR stickers
	gold_sticker INTEGER NOT NULL DEFAULT 0,				-- UR stickers
	tutorial_state INTEGER NOT NULL DEFAULT 0,				-- The tutorial state.
	latest_scenario DECIMAL(8,4) NOT NULL DEFAULT 3.3000,	-- Last unlocked scenario (integral part). The fraction part is story ID that haven't viewed.
	subscenario_tracking TEXT DEFAULT NULL,					-- Unlocked subscenario ID list. Add '!' to indicate it's already viewed. Comma separated (defaults to empty string)
	unlocked_badge TEXT NOT NULL,							-- Unlocked badge. Comma separated
	unlocked_background TEXT NOT NULL,						-- Unlocked background. Comma separated
	present_table TEXT NOT NULL,							-- The present box table name
	assignment_table TEXT NOT NULL,							-- The assignment table name
	live_table TEXT NOT NULL,								-- The live information table name
	unit_table TEXT NOT NULL,								-- Unit list table name
	deck_table TEXT NOT NULL,								-- Deck list table name
	friend_list TEXT NOT NULL,								-- Friendlist in format <user ID>,<user ID>,...
	sticker_table TEXT NOT NULL,							-- List of already exchanged seals (for item with limited amount)
	login_bonus_table TEXT NOT NULL,						-- Login bonus tracking.
	album_table TEXT NOT NULL								-- Album tracking.
);
CREATE TABLE `login_bonus` (
	month INTEGER NOT NULL,					-- The month number
	day INTEGER NOT NULL,					-- The day
	item_id INTEGER NOT NULL,				-- The item ID
	card_num INTEGER,						-- Card ID (not in album) or NULL
	amount INTEGER NOT NULL,				-- Item amout
	PRIMARY KEY(month, day)
);
-- The login bonus in-order: loveca(1), gold(3000), fp(500), repeat (every month)
CREATE TABLE `special_login_bonus` (
	login_bonus_id INTEGER PRIMARY KEY,	-- Login bonus ID. You should not use 0
	message TEXT NOT NULL,				-- Message to show in-game
	banner TEXT NOT NULL,				-- Banner to show in-game
	items TEXT NOT NULL					-- Items list. Format: <item ID>:<amount>[:<more ID>],<item ID>:<amount>[:<more ID>],... MAX 7.
);
CREATE TABLE `birthday_login_bonus` (
	date VARCHAR(5) NOT NULL,	-- In DD-MM format. Login bonus ID is "(day * 12 + (month - 1)) << 16" (used when sending response only)
	message TEXT NOT NULL,		-- Message to show in-game
	banner TEXT NOT NULL,		-- Banner to show in-game
	item_id INTEGER NOT NULL,	-- The item ID
	card_num INTEGER,			-- Card ID (not in album) or NULL.
	amount INTEGER NOT NULL		-- Item amout
);
CREATE TABLE `sticker_shop_item` (
	sticker_id INTEGER PRIMARY KEY AUTO_INCREMENT,	-- Sticker ID
	item_id INTEGER NOT NULL,						-- The item
	card_num INTEGER,								-- The card internal ID or NULL
	cost VARCHAR(10) NOT NULL,						-- The cost in format: <rarity lowercase>:<cost>. Example: ur:3
	max_amount INTEGER NOT NULL DEFAULT -1,			-- Maximum amount that can be exchanged (or -1 for unlimited)
	expire INTEGER DEFAULT NULL						-- Unix timestamp when the item no longer in sticker shop (or NULL for no expiration)
);
CREATE TABLE `wip_live` (
	user_id INTEGER NOT NULL,			-- User ID who do the live.
	live_id INTEGER NOT NULL,			-- The live ID
	live_id2 INTEGER DEFAULT NULL,		-- Second live ID (MedFes)
	live_id3 INTEGER DEFAULT NULL,		-- Third live ID (MedFes)
	deck_num INTEGER NOT NULL,			-- Used deck in this live show
	event_id INTEGER DEFAULT NULL,		-- Event ID which starts this live show (like scorematch, medfes)
	guest_user_id INTEGER DEFAULT NULL,	-- Who is the guest? (non-event only)
	live_data TEXT DEFAULT NULL,		-- Live-specific related data
	started INTEGER NOT NULL			-- Used to prevent people from completing live too fast
);
CREATE TABLE `wip_scenario` (
	user_id INTEGER NOT NULL,				-- User ID who started the scenario/subscenario
	scenario_id INTEGER DEFAULT NULL,		-- Scenario ID or NULL if it's subscenario
	subscenario_id INTEGER DEFAULT NULL		-- Subscenario ID or NULL if it's scenario
);
CREATE TABLE `free_gacha_tracking` (
	user_id INTEGER NOT NULL PRIMARY KEY,	-- User ID who execute the free gacha
	next_free_gacha INTEGER NOT NULL		-- Unix timestamp when the next free gacha.
);
CREATE TABLE `notice_list` (
	notice_id INTEGER PRIMARY KEY AUTO_INCREMENT,	-- Notice ID. Auto increment.
	receiver_user_id INTEGER NOT NULL,				-- To user_id
	sender_user_id INTEGER NOT NULL,				-- From user_id/affector
	notice_filter INTEGER NOT NULL,					-- Notice filter ID
	message TEXT NOT NULL,							-- The message. Truncate to 15 character when sent to client
	is_new BOOL NOT NULL DEFAULT 1,					-- Is unread?
	is_pm BOOL NOT NULL DEFAULT 0,					-- Is private message?
	is_replied BOOL DEFAULT 0						-- Is player already replied to this message?
);
CREATE TABLE `b_side_schedule` (
	live_id INTEGER NOT NULL PRIMARY KEY,						-- The live ID
	start_available_time INTEGER NOT NULL DEFAULT 0,			-- When it comes? (Unix timestamp) default to "already exists"
	end_available_time INTEGER NOT NULL DEFAULT 2147483647		-- When it leaves? (Unix timestamp) default to "never leaves"
);
CREATE TABLE `daily_rotation` (
	live_id INTEGER NOT NULL,					-- The live ID
	daily_category INTEGER NOT NULL				-- The daily live categoy.
);
CREATE TABLE `secretbox_gauge` (
	user_id INTEGER NOT NULL PRIMARY KEY,
	gauge INTEGER NOT NULL DEFAULT 0
);
/*
You may want to add 4 users in your list first so that you can Live Show!
Table definition above is necessary for the server. Now the user-specific MySQL structure
*/

/*
CREATE TABLE `present_$user_id` (
	item_pos INTEGER PRIMARY KEY AUTO_INCREMENT,	-- The item position
	item_type INTEGER NOT NULL,						-- The item type ID
	card_num INTEGER,								-- The card internal ID (can be other ID) or NULL.
	amount INTEGER NOT NULL,						-- Amount of the item
	message TEXT NOT NULL,							-- Additional message like: "Event achievement reward"
	expire INTEGER DEFAULT NULL,					-- Unix timestamp when the item expire or NULL for no expiration
	collected INTEGER DEFAULT NULL					-- Unix timestamp for when the item was collected or NULL for not collected
);
CREATE TABLE `information_$user_id` (
	info_pos INTEGER PRIMARY KEY AUTO_INCREMENT,	-- Information position
	message TEXT NOT NULL,							-- The message
	readed BOOL NOT NULL,							-- Is the notice already readed?
	replied BOOL DEFAULT NULL,						-- Reply flag.
	template INTEGER DEFAULT NULL,					-- The notice template ID. Used on friend activity
	from_user INTEGER NOT NULL						-- From user ID
);
CREATE TABLE `assignment_$user_id` (
	assignment_id INTEGER NOT NULL PRIMARY KEY,	-- The assignment id
	start_time INTEGER NOT NULL,				-- Unix timestamp when this assignment added
	end_time INTEGER,							-- Unix timestamp when this assignment end
	new_flag INTEGER NOT NULL DEFAULT 1,		-- Is new?
	count INTEGER DEFAULT 0,					-- Internal counter.
	complete_flag INTEGER NOT NULL DEFAULT 0,	-- Is complete?
	reward TEXT NOT NULL						-- Reward in format: <item ID>:<amount>[:<info ID>], ...
);
CREATE TABLE `live_$user_id` (
	live_id INTEGER NOT NULL PRIMARY KEY,		-- The live (difficulty) ID
	normal_live BOOL NOT NULL DEFAULT 1,		-- Is the live available in Hits? (used to track EX scores)
	score INTEGER NOT NULL DEFAULT 0,			-- Highest score
	combo INTEGER NOT NULL DEFAULT 0,			-- Highest combo
	times INTEGER NOT NULL DEFAULT 0			-- x times played
);
CREATE TABLE `unit_$user_id` (
	unit_id INTEGER PRIMARY KEY AUTO_INCREMENT,	-- The unit owning user ID
	card_id INTEGER NOT NULL,					-- The card internal ID
	current_exp INTEGER NOT NULL DEFAULT 0,		-- Current EXP
	next_exp INTEGER NOT NULL,					-- Next EXP before level up
	level INTEGER NOT NULL DEFAULT 1,			-- Card level
	max_level INTEGER NOT NULL,					-- Card max level
	skill_level INTEGER NOT NULL DEFAULT 1,		-- Skill level
	skill_level_exp INTEGER NOT NULL DEFAULT 0,	-- Skill level EXP. To follow JP v4.0 behaviour.
	health_points INTEGER NOT NULL,				-- Card max HP
	bond INTEGER NOT NULL DEFAULT 0,			-- Card bond
	max_bond INTEGER NOT NULL,					-- Card max bond
	favorite BOOL NOT NULL DEFAULT 0,			-- Flagged as favourite?
	added_time INTEGER NOT NULL					-- Unix timestamp when this card added
);
CREATE TABLE `deck_$user_id` (
	deck_num INTEGER NOT NULL PRIMARY KEY,	-- Deck number
	deck_name VARCHAR(10) NOT NULL,			-- Deck name
	deck_members TEXT NOT NULL				-- Deck list. In format: <unit_id>:<unit_id>. Unit id is unit_id field in `unit_$user_id` table or 0 if no unit is specificed.
);
CREATE TABLE `sticker_$user_id` (
	sticker_id INTEGER NOT NULL PRIMARY KEY,	-- The sticker ID
	amount_bought INTEGER NOT NULL DEFAULT 0	-- How much it already bought.
);
CREATE TABLE `login_bonus_$user_id` (
	login_bonus_id INTEGER NOT NULL PRIMARY KEY,	-- The login bonus ID. ID 0 is reserved for monthly logn bonus.
	counter INTEGER NOT NULL DEFAULT 0				-- The login bonus counter.
);
CREATE TABLE `album_$user_id` (
	card_id INTEGER NOT NULL PRIMARY KEY,			-- The card ID
	flags TINYINT NOT NULL DEFAULT 0,				-- Flags bit: 0 = ever have?; 1 = ever idolized?; 2 = ever max bond?; 3 = ever max level?
	total_bond INTEGER NOT NULL DEFAULT 0			-- Max total bond. To follow JP v4.0 behaviour.
);
*/

/*
The event ranking table
*/
/*
CREATE TABLE `event_player_ranking_$event_id` (
	user_id INTEGER NOT NULL PRIMARY KEY,		-- Player user ID
	total_points INTEGER NOT NULL,				-- Total event points
	current_token INTEGER DEFAULT NULL			-- Current token OR null if it's not token event
);
CREATE TABLE `event_song_ranking_{$event_id}_$live_id` (	-- Token event only
	user_id INTEGER NOT NULL PRIMARY KEY,					-- Player user ID
	high_score INTEGER NOT NULL								-- Highest score
);
*/
/*
Insert empty deck list. SIF EN currently supports 7 deck.
*/
/*
INSERT INTO `deck_$user_id` VALUES (1, 'Team A', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (2, 'Team B', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (3, 'Team C', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (4, 'Team D', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (5, 'Team E', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (6, 'Team F', '0:0:0:0:0:0:0:0:0');
INSERT INTO `deck_$user_id` VALUES (7, 'Team G', '0:0:0:0:0:0:0:0:0');
*/
/*
Then, insert birthday login bonus ;)
*/

INSERT INTO `birthday_login_bonus` VALUES("17-01", CONCAT("January 17 is Hanayo Koizumi's birthday!", CHAR(10), "To celebrate the occasion, we are giving away 5 Love Gems", CHAR(10), "as a Login Bonus today."), "assets/image/ui/login_bonus_extra/birthday_8_1.png", 3001, NULL, 5);	-- Hanayo
INSERT INTO `birthday_login_bonus` VALUES("15-03", CONCAT("March 15 is Umi Sonoda's birthday!", CHAR(10), "To celebrate the occasion, we are giving away 5 Love Gems", CHAR(10), "as a Login Bonus today."), "assets/image/ui/login_bonus_extra/birthday_9_1.png", 3001, NULL, 5);		-- Umi
INSERT INTO `birthday_login_bonus` VALUES("19-04", CONCAT("April 19 is Maki Nishikino's birthday!", CHAR(10), "To celebrate the occasion, we are giving away 5 Love Gems", CHAR(10), "as a Login Bonus today."), "assets/image/ui/login_bonus_extra/birthday_1_1.png", 3001, NULL, 5);		-- Maki
INSERT INTO `birthday_login_bonus` VALUES("09-06", CONCAT("June 9 is Nozomi Tojo's birthday!", CHAR(10), "To celebrate the occasion, we are giving away 5 Love Gems", CHAR(10), "as a Login Bonus today."), "assets/image/ui/login_bonus_extra/birthday_2_1.png", 3001, NULL, 5);	-- Nozomi
INSERT INTO `birthday_login_bonus` VALUES("22-07", CONCAT("July 22 is Nico Yazawa's birthday!", CHAR(10), "To celebrate the occasion, we are giving away 5 Love Gems", CHAR(10), "as a Login Bonus today."), "assets/image/ui/login_bonus_extra/birthday_3_1.png", 3001, NULL, 5);		-- Nico
INSERT INTO `birthday_login_bonus` VALUES("03-08", CONCAT("August 3 is Honoka Kosaka's birthday!", CHAR(10), "To celebrate the occasion, we are giving away 5 Love Gems", CHAR(10), "as a Login Bonus today."), "assets/image/ui/login_bonus_extra/birthday_4_1.png", 3001, NULL, 5);	-- Honoka
INSERT INTO `birthday_login_bonus` VALUES("12-09", CONCAT("September 12 is Kotori Minami's birthday!", CHAR(10), "To celebrate the occasion, we are giving away 5 Love Gems", CHAR(10), "as a Login Bonus today."), "assets/image/ui/login_bonus_extra/birthday_5_1.png", 3001, NULL, 5);	-- Kotori
INSERT INTO `birthday_login_bonus` VALUES("21-10", CONCAT("October 21 is Eli Ayase's birthday!", CHAR(10), "To celebrate the occasion, we are giving away 5 Love Gems", CHAR(10), "as a Login Bonus today."), "assets/image/ui/login_bonus_extra/birthday_6_1.png", 3001, NULL, 5);		-- Eli
INSERT INTO `birthday_login_bonus` VALUES("01-11", CONCAT("November 1 is Rin Hoshizora's birthday!", CHAR(10), "To celebrate the occasion, we are giving away 5 Love Gems", CHAR(10), "as a Login Bonus today."), "assets/image/ui/login_bonus_extra/birthday_7_1.png", 3001, NULL, 5);		-- Rin

/*
Insert secretbox
*/

-- Honour scouting. Uses ID 0
INSERT INTO `secretbox_list` (id, banner_preview, banner_big, r_list, sr_list, ur_list) VALUES (
	0,
	"assets/image/secretbox/icon/s_ba_3_1.png",
	"assets/image/secretbox/top/s_con_n_3_1.png",
	"31-57,286-294,339-347,430-438,494-502,561-569,681-689,751-759,788-796",
	"58-66,78-80,83-85,88,93-95,100,101,103,106,107,111,112,128-131,135-137,139,145-148,151,153-155,158,161-164,167,169-171,174,176-179,183,185-187,190,192-195,198,200-202,206,209-212,215,219-221,224,245-248,252,254-256,259,261-264,267,269-271,274,278-281,284,295-297,309,311-314,317,319-321,324,326-329,332,335-337,349,354-357,360,364-366,371,374-377,392,394-396,399,404-407,410,412-414,417,421-424,428,439-441,444,452-455,458,460-462,467,472-475,478,480-482,486,503-506,509,511-513,525,527-530,533,535-537,540,545-548,551,553-555,559,570-573,576,578-580,583,588-591,594,599-601,604,607-610,622,634-636,639,641-644,647,649-651,654,657-660,663,665-667,670,673-676,679,690-692,696,699-702,705,707-709,712,724-727,730,732-734,737,742-745,748,760-762,765,770-773,776,778-780",
	"67-69,71,72,81,96,108,113,132,138,149,156,165,172,180,188,196,203,213,222,249,257,265,272,282,298,315,322,330,338,358,367,378,397,408,415,425,442,456,463,476,483,507,514,531,538,549,556,574,581,592,602,611,637,645,652,661,668,677,693,703,710,728,735,746,763,774,781"
);
UPDATE `secretbox_list` SET id = 0 WHERE id = 1;	-- MySQL fix.

-- 60% R, 30% SR, 10% UR. Support card.
INSERT INTO `coupon_secretbox_list` (name, banner_big, banner_title, description, coupon_cost, r_list, sr_list, ur_list, r_chance, sr_chance, ur_chance) VALUES (
	"Supporting Member Scouting",
	"assets/image/secretbox/top/s_con_n_23_1.png",
	"assets/image/secretbox/title/23.png",
	CONCAT("Scout Supporting Members with Scouting", CHAR(10), "Coupons! Using a Supporting Member as a", CHAR(10), "Practice partner for a member with", CHAR(10), "matching Attribute and Rarity has", CHAR(10), "a chance to level up their Skill."),
	1,
	'379-381',
	'383-386',
	'387-390',
	60.0,
	30.0,
	10.0
);

-- 80% SR, 20% UR coupon scouting. Usable card.
INSERT INTO `coupon_secretbox_list` (name, banner_big, banner_title, description, sr_list, ur_list) VALUES (
	"SR/UR Scouting",
	"assets/image/secretbox/top/s_con_n_22_1.png",
	"assets/image/secretbox/title/22.png",
	CONCAT("Scout SR and UR Club Members with Scouting", CHAR(10), "Coupons! Club members previously", CHAR(10), "distributed in events have", CHAR(10), "a lower appearance rate."),
	"58-66,78-80,83-85,88,93-95,100,101,103,106,107,111,112,128-131,135-137,139,145-148,151,153-155,158,161-164,167,169-171,174,176-179,183,185-187,190,192-195,198,200-202,206,207,209-212,215,216,219-221,224,245-248,252,254-256,259,261-264,267,269-271,274,278-281,284,295-297,309,311-314,317,319-321,324,326-329,332,335-337,349,354-357,360,364-366,371,374-377,392,394-396,399,404-407,410,412-414,417,421-424,428,439-441,444,452-455,458,460-462,467,472-475,478,480-482,486,503-506,509,511-513,525,527-530,533,535-537,540,545-548,551,553-555,559,570-573,576,578-580,583,588-591,594,599-601,604,607-610,622,634-636,639,641-644,647,649-651,654,657-660,663,665-667,670,673-676,679,690-692,696,699-702,705,707-709,712,724-727,730,732-734,737,742-745,748,760-762,765,770-773,776,778-780",
	"67-69,71,72,81,96,108,113,132,138,149,156,165,172,180,188,196,203,204,213,222,249,257,265,272,282,298,315,322,330,338,358,367,372,378,397,408,415,425,442,456,463,476,483,484,507,514,531,538,549,556,574,581,586,592,602,611,637,645,652,661,668,677,693,703,710,722,728,735,746,763,774,781"
);

/*
Insert daily songs
*/

INSERT INTO `daily_rotation` VALUES (46, 1);	-- Mermaid Festa vol.2 Easy
INSERT INTO `daily_rotation` VALUES (47, 2);	-- Mermaid Festa vol.2 Normal
INSERT INTO `daily_rotation` VALUES (48, 3);	-- Mermaid Festa vol.2 Hard
INSERT INTO `daily_rotation` VALUES (458, 4);	-- Mermaid Festa vol.2 Expert
INSERT INTO `daily_rotation` VALUES (455, 5);	-- Nawatobi Easy
INSERT INTO `daily_rotation` VALUES (456, 6);	-- Nawatobi Normal
INSERT INTO `daily_rotation` VALUES (457, 7);	-- Nawatobi Hard
INSERT INTO `daily_rotation` VALUES (568, 8);	-- Nawatobi Expert
INSERT INTO `daily_rotation` VALUES (52, 1);	-- Kokuhaku Biyori, desu Easy
INSERT INTO `daily_rotation` VALUES (53, 2);	-- Kokuhaku Biyori, desu Normal
INSERT INTO `daily_rotation` VALUES (54, 3);	-- Kokuhaku Biyori, desu Hard
INSERT INTO `daily_rotation` VALUES (463, 4);	-- Kokuhaku Biyori, desu Expert
INSERT INTO `daily_rotation` VALUES (443, 5);	-- Anemone Heart Easy
INSERT INTO `daily_rotation` VALUES (444, 6);	-- Anemone Heart Normal
INSERT INTO `daily_rotation` VALUES (445, 7);	-- Anemone Heart Hard
INSERT INTO `daily_rotation` VALUES (567, 8);	-- Anemone Heart Expert
INSERT INTO `daily_rotation` VALUES (55, 1);	-- Soldier Game Easy
INSERT INTO `daily_rotation` VALUES (56, 2);	-- Soldier Game Normal
INSERT INTO `daily_rotation` VALUES (57, 3);	-- Soldier Game Hard
INSERT INTO `daily_rotation` VALUES (459, 4);	-- Soldier Game Expert
INSERT INTO `daily_rotation` VALUES (440, 5);	-- Yume naki Yume wa Yume jyanai Easy
INSERT INTO `daily_rotation` VALUES (441, 6);	-- Yume naki Yume wa Yume jyanai Normal
INSERT INTO `daily_rotation` VALUES (442, 7);	-- Yume naki Yume wa Yume jyanai Hard
INSERT INTO `daily_rotation` VALUES (566, 8);	-- Yume naki Yume wa Yume jyanai Expert
INSERT INTO `daily_rotation` VALUES (49, 1);	-- Otomeshiki Renai Juku Easy
INSERT INTO `daily_rotation` VALUES (50, 2);	-- Otomeshiki Renai Juku Normal
INSERT INTO `daily_rotation` VALUES (51, 3);	-- Otomeshiki Renai Juku Hard
INSERT INTO `daily_rotation` VALUES (446, 4);	-- Otomeshiki Renai Juku Expert
INSERT INTO `daily_rotation` VALUES (485, 5);	-- Garasu no Hanazono Easy
INSERT INTO `daily_rotation` VALUES (486, 6);	-- Garasy no Hanazono Normal
INSERT INTO `daily_rotation` VALUES (487, 7);	-- Garasu no Hanazono Hard
INSERT INTO `daily_rotation` VALUES (594, 8);	-- Garasu no Hanazono Expert
INSERT INTO `daily_rotation` VALUES (482, 5);	-- Nico Puri Easy
INSERT INTO `daily_rotation` VALUES (483, 6);	-- Nico Puri Normal
INSERT INTO `daily_rotation` VALUES (484, 7);	-- Nico Puri Hard
INSERT INTO `daily_rotation` VALUES (593, 8);	-- Nico Puri Expert
INSERT INTO `daily_rotation` VALUES (479, 5);	-- Beat in Angel Easy
INSERT INTO `daily_rotation` VALUES (480, 6);	-- Beat in Angel Normal
INSERT INTO `daily_rotation` VALUES (481, 7);	-- Beat in Angel Hard
INSERT INTO `daily_rotation` VALUES (569, 8);	-- Beat in Angel Expert
