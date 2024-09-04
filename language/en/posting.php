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
	'ADD_TICKET_EXPLAIN'	=> 'Thank you for taking time to submit a ticket about the %1$s project to our %2$s. Complete the form below to submit your ticket and please try to include as much information as possible when describing the issue you are sending a ticket about.<br /><br />Please allow at least 24 hours for a team member to respond to your ticket. You will be notified whenever a team member makes a change to your ticket or posts a reply.',
	'ASSIGNEE'				=> 'Assignee',

	'COMPONENT'	=> 'Component',

	'DESCRIPTION_EXPLAIN'	=> 'Your actual report. Please try to be as detailed as possible; the more information you provide, the better',

	'EDIT_TICKET'	=> 'Edit ticket',

	'LOCK_TICKET'	=> 'Lock ticket',

	'MOVE_LINK'		=> 'Move ticket to another tracker/project',
	'MOVE_TICKET'	=> 'Move ticket',

	'NO_DESCRIPTION'	=> 'Please enter a ticket description',
	'NO_TITLE'			=> 'Please enter a title for your ticket',

	'PREVIEW'			=> 'Preview',
	'PRIVATE_TICKET'	=> 'Private ticket',
	'PROJECT_INFO'		=> 'Project information',
	'PROJECT'			=> 'Project',

	'SELECT_COMPONENT'	=> 'Please select the affected component',
	'SEVERITY'			=> 'Severity',
	'SUBMIT_TICKET'		=> 'Submit a ticket',

	'TICKET'		=> 'Ticket',
	'TITLE_EXPLAIN'	=> 'A short, descriptive title for your ticket',
	'TRACKER'		=> 'Tracker',

	'USERGROUP'	=> 'Usergroup',
	'USERNAME'	=> 'Username',
]);
