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

/**
*	EXTENSION-DEVELOPERS PLEASE NOTE
*
*	You are able to put your permission sets into your extension.
*	The permissions logic should be added via the 'core.permissions' event.
*	You can easily add new permission categories, types and permissions, by
*	simply merging them into the respective arrays.
*	The respective language strings should be added into a language file, that
*	start with 'permissions_', so they are automatically loaded within the ACP.
*/

$lang = array_merge($lang, [
	'ACL_CAT_TRACKERS'	=> 'Trackers',

	'ACL_A_TRACKERS_MANAGE'	=> 'Can manage trackers',

	'ACL_M_TRACKERS_APPROVE'	=> 'Can approve and restore tickets',
	'ACL_M_TRACKERS_CHGPOSTER'	=> 'Can change ticket author',
	'ACL_M_TRACKERS_DELETE'		=> 'Can permanently delete tickets',
	'ACL_M_TRACKERS_EDIT'		=> 'Can edit tickets',
	'ACL_M_TRACKERS_INFO'		=> 'Can view ticket details',
	'ACL_M_TRACKERS_LOCK'		=> 'Can lock tickets',
	'ACL_M_TRACKERS_MERGE'		=> 'Can merge tickets',
	'ACL_M_TRACKERS_MOVE'		=> 'Can move tickets',
	'ACL_M_TRACKERS_REPORT'		=> 'Can close and delete ticket reports',

	'ACL_U_TRACKERS_ATTACH'			=> 'Can attach files',
	'ACL_U_TRACKERS_BBCODE'			=> 'Can use BBCode',
	'ACL_U_TRACKERS_DELETE'			=> 'Can permanently delete own tickets',
	'ACL_U_TRACKERS_DOWNLOAD'		=> 'Can download files',
	'ACL_U_TRACKERS_EDIT'			=> 'Can edit own tickets',
	'ACL_U_TRACKERS_IMG'			=> 'Can use [img] BBCode tag',
	'ACL_U_TRACKERS_LIST'			=> 'Can see trackers',
	'ACL_U_TRACKERS_LIST_TICKETS'	=> 'Can see tickets',
	'ACL_U_TRACKERS_NOAPPROVE'		=> 'Can post without approval',
	'ACL_U_TRACKERS_POST'			=> 'Can start new tickets',
	'ACL_U_TRACKERS_READ'			=> 'Can read trackers',
	'ACL_U_TRACKERS_REPLY'			=> 'Can reply to tickets',
	'ACL_U_TRACKERS_REPORT'			=> 'Can report tickets',
	'ACL_U_TRACKERS_SEARCH'			=> 'Can search the trackers',
	'ACL_U_TRACKERS_SMILIES'		=> 'Can use smilies',
	'ACL_U_TRACKERS_SUBSCRIBE'		=> 'Can subscribe trackers',
]);
