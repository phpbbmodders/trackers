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
	'DESCRIPTION'	=> 'Description',

	'MY_TICKETS'		=> 'Tickets assigned to me',
	'MY_TICKETS_GROUPS'	=> 'Tickets assigned to me and my groups',

	'NO_PROJECTS'	=> 'There are no projects available for this tracker',

	'PROJECT_NAME'	=> 'Project name',

	'SEARCH_TRACKER'	=> 'Search this tracker',
	'SELECT_PROJECT'	=> 'Please select the project you would like to open below.',

	'TRACKER_PRIVATE'		=> 'Note: Submissions to this tracker are private; only you and team members that have access to the specified tracker will see the information.',
	'TRACKER_STATISTICS'	=> 'Tracker statistics',
]);
