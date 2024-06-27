<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, [
	'ALL_CLOSED'	=> 'All closed tickets',
	'ALL_OPEN'		=> 'All open tickets',
	'ALL_TICKETS'	=> 'All tickets',
	'ASSIGNED_TO'	=> 'Assigned to',

	'BUTTON_NEW_TICKET'	=> 'New Ticket',

	'COMPONENT'			=> 'Component',
	'CURRENTLY_SHOWING'	=> 'Currently showing',

	'FILTER_TICKETS'	=> 'Filter tickets',

	'NO_TICKETS'	=> 'There are no tickets to display',

	'POST_TICKET'	=> 'Post a new ticket',

	'STATUS'	=> 'Status',

	'TITLE'			=> 'Title',
	'TOTAL_TICKETS'	=> [
		0	=> '0 tickets',
		1	=> '1 ticket',
		2	=> '%d tickets',
	],
]);
