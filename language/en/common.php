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
	// Common
	'NO_NEW_STATUS'			=> 'This tracker does not have a "new" status',
	'NO_PAGE_MODE'			=> 'Invalid or no page mode specified.',
	'NO_REVIEWED_STATUS'	=> 'This tracker does not have a "reviewed" status',

	'TRACKERS'	=> 'Trackers',

	'UNASSIGNED'	=> 'Unassigned',
	'UNCATEGORISED'	=> 'Uncategorised',
	'UNKNOWN'		=> 'Unknown',

	'VIEWING_TRACKERS'	=> 'Viewing trackers',

	// Severity
	'POSSIBLY_INVALID'	=> 'Possibly invalid',
	'SEVERE'			=> 'Severe',

	// Status
	'ALREADY_FIXED'			=> 'Already fixed',
	'AWAITING_INFO'			=> 'Awaiting information',
	'AWAITING_TEAM'			=> 'Awaiting team input',
	'BUG'					=> 'Bug',
	'CLOSED'				=> 'Closed',
	'DUPLICATE'				=> 'Duplicate',
	'FIXED'					=> 'Fixed',
	'IMPLEMENTED_VCS'		=> 'Implemented in VCS',
	'IMPLEMENTING'			=> 'Implementing',
	'INVALID'				=> 'Invalid',
	'NEW'					=> 'New',
	'NOT_BUG'				=> 'Not a bug',
	'PATCH_IN_PROGRESS'		=> 'Patching in progress',
	'PATCH_COMPLETED'		=> 'Patch completed',
	'PENDING'				=> 'Pending',
	'POSSIBLE_BUG'			=> 'Possible bug',
	'POSSIBLE_SECURITY'		=> 'Possible security issue',
	'RESEARCHING'			=> 'Researching',
	'REVIEWED'				=> 'Reviewed',
	'REVIEW_LATER'			=> 'Review later',
	'SUPPORT_REQUEST'		=> 'Support request',
	'UNREPRODUCIBLE'		=> 'Unreproducible',
	'WILL_NOT_FIX'			=> 'Will not fix',
	'WILL_NOT_IMPLEMENT'	=> 'Will not implement',

	// Status descriptions
	'ALREADY_FIXED_EXPLAIN'		=> 'The issue you\'re reporting has already been fixed. If you believe that the problem still exists, please reply to the report with a detailed step-by-step guide on how to reproduce it.',
	'AWAITING_INFO_EXPLAIN'		=> 'A developer has reviewed your report and determined that information is missing or incomplete. Please provide the information requested by team members in the report.',
	'DUPLICATE_EXPLAIN'			=> 'A developer has reviewed your report and determined that a similar report has already been filed.',
	'NEW_EXPLAIN'				=> 'Your report has not yet been reviewed. Don\'t worry, we won\'t forget about it.',
	'NOT_BUG_EXPLAIN'			=> 'A developer has reviewed your report and determined that you are describing intended behavior. If you believe that the issue is in fact a bug, please reply to the report with further details and a detailed step-by-step guide on how to reproduce it.',
	'PATCH_IN_PROGRESS_EXPLAIN'	=> 'A developer is currently working on a fix to the issue that you reported.',
	'PATCH_COMPLETED_EXPLAIN'	=> 'A fix for the issue that you reported has been committed to the VCS. Please verify that the patch has completely addressed the issue.',
	'REVIEWED_EXPLAIN'			=> 'Your report has been reviewed and the issue has been identified. If you have a working patch, now is an excellent time to provide it.',
	'REVIEW_LATER_EXPLAIN'		=> 'A developer has reviewed your report and determined the issue to be nonessential. The report has been placed on hold and will be revisited at a later time. If you believe the issue to be critical, please reply to the report with a concise argument. If you have a working patch, now is an excellent time to provide it.',
	'SUPPORT_REQUEST_EXPLAIN'	=> 'A developer has reviewed your report and determined that you would be better assisted in the forums. The problem you are experiencing may be related to a specific configuration/setting, or the ticket may not have been sufficiently researched. Please search the board for your problem and, if necessary, start a new topic with a link to your report. Team members are able to re-open reports if the issue is in fact a bug. If you have additional information about the issue, please reply to the report.',
	'UNREPRODUCIBLE_EXPLAIN'	=> 'A developer has reviewed your report but was unable to reproduce the issue. If you are able to reproduce the issue, please provide detailed step-by-step instructions for doing so in your report.',
	'WILL_NOT_FIX_EXPLAIN'		=> 'A developer has reviewed your report and determined the issue to be nonessential. A patch will not be provided at this time. If you believe the issue to be critical, please reply to the report with a concise argument. If you have a working patch, now is an excellent time to provide it. Further information may be available in a response to your report.',

	// Trackers
	'BUG_TRACKER'		=> 'Bug Tracker',
	'FEATURE_TRACKER'	=> 'Feature Tracker',
	'INCIDENT_TRACKER'	=> 'Incident Tracker',
	'SECURITY_TRACKER'	=> 'Security Tracker',
]);
