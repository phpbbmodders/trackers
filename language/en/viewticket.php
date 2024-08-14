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
	'ACTION_BY'			=> 'Action performed by',
	'ASSIGN'			=> 'Assign to another user/group',
	'ASSIGN_ME'			=> 'Assign to me',
	'ASSIGN_MY_GROUP'	=> 'Assign to %1$s',
	'ASSIGNED'			=> 'Assigned to',

	'BUTTON_DELETE'         => 'Delete',
	'BUTTON_EDIT'			=> 'Edit',
	'BUTTON_INFORMATION'    => 'Information',
	'BUTTON_POST_REPLY'		=> 'Post Reply',
	'BUTTON_QUOTE'			=> 'Quote',
	'BUTTON_REPORT'         => 'Report',
	'BUTTON_TICKET_LOCKED'	=> 'Locked',

	'CHANGE_SEVERITY'	=> 'Change severity',
	'CHANGE_STATUS'		=> 'Change status',

	'DELETE_POST'		=> 'Delete post',
	'DELETE_TICKET'		=> 'Delete ticket',
	'DUPLICATES_OTHER'	=> 'Duplicates of #',
	'DUPLICATES_TICKET'	=> 'Duplicates of this ticket',

	'EDIT_POST'	=> 'Edit post',

	'INFORMATION'	=> 'Information',

	'LOCK_TICKET'	=> 'Lock ticket',

	'MOVE_TICKET'	=> 'Move ticket',

	'NO_TICKET'		=> 'This specified ticket does not exist',
	'NO_COMMENTS'	=> 'No comments have been made and there are no history entries.',

	'POST_BY_AUTHOR'	=> 'by',
	'POST_REPLY'		=> 'Post a reply',
	'PROJECT'			=> 'Project',

	'QUICK_MOD'	=> 'Quick-mod tools',

	'REPLY_WITH_QUOTE'	=> 'Reply with quote',
	'REPORT_POST'		=> 'Report this post',
	'REPORTED_BY'		=> 'Reported by',
	'REPORTED_IP'		=> 'Reported from IP',
	'REPORTED_ON'		=> 'Reported on',

	'SEND_PM'		=> 'Send PM',
	'SET_REVIEWED'	=> 'Set to "Reviewed"',
	'STATUS'		=> 'Status',
	'STATUS_DESC'	=> '<strong>Your ticket\'s status is "%1$s"</strong><br /><br />%2$s',

	'TICKET_DETAILS'		=> 'Ticket details',
	'TICKET_ID'				=> 'Ticket ID',
	'TICKET_LOCKED'			=> 'This ticket is locked, you cannot edit posts or make further replies.',
	'TICKET_OPTIONS'		=> 'Ticket options',
	'TICKET_POSTS'			=> 'Comments & History',
	'TICKET_TOTAL_POSTS'	=> [
		0	=> '0 posts',
		1	=> '1 post',
		2	=> '%d posts',
	],

	'UNLOCK_TICKET'	=> 'Unlock ticket',
]);
