<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\trackers\migrations\v10x;

class m2_initial_data extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		/*$sql = 'SELECT module_id
			FROM ' . $this->table_prefix . "modules
			WHERE module_class = 'acp'
				AND module_langname = 'ACP_TRACKERS_TITLE'";
		$result = $this->db->sql_query($sql);
		$module_id = $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);

		return $module_id !== false;*/
		return $this->config->offsetExists('tickets_per_page');
	}

	public static function depends_on()
	{
		return ['\phpbbmodders\trackers\migrations\v10x\m1_initial_schema'];
	}

	/**
	 * Add, update or delete data
	 */
	public function update_data()
	{
		return [
			// Add config table settings
			['config.add', ['tickets_per_page', 25]],

			// Add permissions
			['permission.add', ['a_trackers_manage']],

			// Set permissions
			['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_trackers_manage']],

			// Call a custom callable function to perform more complex operations
			['custom', [[$this, 'install_data']]],
		];
	}

	/**
	 * A custom function for making more complex database changes
	 * during extension installation. Must be declared as public.
	 */
	public function install_data()
	{
		$severity_data = [
			[
				'severity_id'		=> 5,
				'tracker_id'		=> 2,
				'severity_name'		=> 'SEVERE',
				'severity_colour'	=> 'ECD5D8',
				'severity_order'	=> 1,
			],

			[
				'severity_id'		=> 15,
				'tracker_id'		=> 3,
				'severity_name'		=> 'SEVERE',
				'severity_colour'	=> 'ECD5D8',
				'severity_order'	=> 1,
			],

			[
				'severity_id'		=> 25,
				'tracker_id'		=> 2,
				'severity_name'		=> 'POSSIBLY_INVALID',
				'severity_colour'	=> 'FDE8A2',
				'severity_order'	=> 2,
			],

			[
				'severity_id'		=> 35,
				'tracker_id'		=> 3,
				'severity_name'		=> 'POSSIBLY_INVALID',
				'severity_colour'	=> 'FDE8A2',
				'severity_order'	=> 2,
			],
		];

		$status_data = [
			[
				'status_id'			=> 1,
				'tracker_id'		=> 1,
				'status_name'		=> 'NEW',
				'status_desc'		=> 'NEW_EXPLAIN',
				'status_order'		=> 1,
				'ticket_new'		=> 1,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 2,
				'tracker_id'		=> 1,
				'status_name'		=> 'POSSIBLE_BUG',
				'status_desc'		=> '',
				'status_order'		=> 2,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 3,
				'tracker_id'		=> 1,
				'status_name'		=> 'POSSIBLE_SECURITY',
				'status_desc'		=> '',
				'status_order'		=> 3,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 4,
				'tracker_id'		=> 1,
				'status_name'		=> 'REVIEWED',
				'status_desc'		=> 'REVIEWED_EXPLAIN',
				'status_order'		=> 4,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 1,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 5,
				'tracker_id'		=> 1,
				'status_name'		=> 'AWAITING_INFO',
				'status_desc'		=> 'AWAITING_INFO_EXPLAIN',
				'status_order'		=> 5,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 6,
				'tracker_id'		=> 1,
				'status_name'		=> 'SUPPORT_REQUEST',
				'status_desc'		=> 'SUPPORT_REQUEST_EXPLAIN',
				'status_order'		=> 6,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 7,
				'tracker_id'		=> 1,
				'status_name'		=> 'AWAITING_TEAM',
				'status_desc'		=> '',
				'status_order'		=> 7,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 8,
				'tracker_id'		=> 1,
				'status_name'		=> 'CLOSED',
				'status_desc'		=> '',
				'status_order'		=> 8,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 9,
				'tracker_id'		=> 2,
				'status_name'		=> 'NEW',
				'status_desc'		=> 'NEW_EXPLAIN',
				'status_order'		=> 1,
				'ticket_new'		=> 1,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 10,
				'tracker_id'		=> 2,
				'status_name'		=> 'DUPLICATE',
				'status_desc'		=> 'DUPLICATE_EXPLAIN',
				'status_order'		=> 2,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 1,
			],

			[
				'status_id'			=> 11,
				'tracker_id'		=> 2,
				'status_name'		=> 'FIXED',
				'status_desc'		=> '',
				'status_order'		=> 3,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 1,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 12,
				'tracker_id'		=> 2,
				'status_name'		=> 'BUG',
				'status_desc'		=> '',
				'status_order'		=> 4,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 13,
				'tracker_id'		=> 2,
				'status_name'		=> 'SUPPORT_REQUEST',
				'status_desc'		=> 'SUPPORT_REQUEST_EXPLAIN',
				'status_order'		=> 5,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 14,
				'tracker_id'		=> 2,
				'status_name'		=> 'INVALID',
				'status_desc'		=> '',
				'status_order'		=> 6,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 15,
				'tracker_id'		=> 2,
				'status_name'		=> 'REVIEWED',
				'status_desc'		=> 'REVIEWED_EXPLAIN',
				'status_order'		=> 7,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 1,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 16,
				'tracker_id'		=> 2,
				'status_name'		=> 'AWAITING_INFO',
				'status_desc'		=> 'AWAITING_INFO_EXPLAIN',
				'status_order'		=> 8,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 17,
				'tracker_id'		=> 2,
				'status_name'		=> 'AWAITING_TEAM',
				'status_desc'		=> '',
				'status_order'		=> 9,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 18,
				'tracker_id'		=> 2,
				'status_name'		=> 'PATCH_IN_PROGRESS',
				'status_desc'		=> 'PATCH_IN_PROGRESS_EXPLAIN',
				'status_order'		=> 10,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 19,
				'tracker_id'		=> 2,
				'status_name'		=> 'PATCH_COMPLETED',
				'status_desc'		=> 'PATCH_COMPLETED_EXPLAIN',
				'status_order'		=> 11,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 20,
				'tracker_id'		=> 2,
				'status_name'		=> 'CLOSED',
				'status_desc'		=> '',
				'status_order'		=> 12,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 21,
				'tracker_id'		=> 3,
				'status_name'		=> 'NEW',
				'status_desc'		=> 'NEW_EXPLAIN',
				'status_order'		=> 1,
				'ticket_new'		=> 1,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 22,
				'tracker_id'		=> 3,
				'status_name'		=> 'NOT_BUG',
				'status_desc'		=> 'NOT_BUG_EXPLAIN',
				'status_order'		=> 2,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 23,
				'tracker_id'		=> 3,
				'status_name'		=> 'SUPPORT_REQUEST',
				'status_desc'		=> 'SUPPORT_REQUEST_EXPLAIN',
				'status_order'		=> 3,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 24,
				'tracker_id'		=> 3,
				'status_name'		=> 'DUPLICATE',
				'status_desc'		=> 'DUPLICATE_EXPLAIN',
				'status_order'		=> 4,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 1,
			],

			[
				'status_id'			=> 25,
				'tracker_id'		=> 3,
				'status_name'		=> 'ALREADY_FIXED',
				'status_desc'		=> 'ALREADY_FIXED_EXPLAIN',
				'status_order'		=> 5,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 26,
				'tracker_id'		=> 3,
				'status_name'		=> 'REVIEWED',
				'status_desc'		=> 'REVIEWED_EXPLAIN',
				'status_order'		=> 6,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 1,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 27,
				'tracker_id'		=> 3,
				'status_name'		=> 'REVIEW_LATER',
				'status_desc'		=> 'REVIEW_LATER_EXPLAIN',
				'status_order'		=> 7,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 28,
				'tracker_id'		=> 3,
				'status_name'		=> 'AWAITING_INFO',
				'status_desc'		=> 'AWAITING_INFO_EXPLAIN',
				'status_order'		=> 8,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 29,
				'tracker_id'		=> 3,
				'status_name'		=> 'AWAITING_TEAM',
				'status_desc'		=> '',
				'status_order'		=> 9,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 30,
				'tracker_id'		=> 3,
				'status_name'		=> 'PENDING',
				'status_desc'		=> '',
				'status_order'		=> 10,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 31,
				'tracker_id'		=> 3,
				'status_name'		=> 'WILL_NOT_FIX',
				'status_desc'		=> 'WILL_NOT_FIX_EXPLAIN',
				'status_order'		=> 11,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 32,
				'tracker_id'		=> 3,
				'status_name'		=> 'PATCH_IN_PROGRESS',
				'status_desc'		=> 'PATCH_IN_PROGRESS_EXPLAIN',
				'status_order'		=> 12,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 33,
				'tracker_id'		=> 3,
				'status_name'		=> 'PATCH_COMPLETED',
				'status_desc'		=> 'PATCH_COMPLETED_EXPLAIN',
				'status_order'		=> 13,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 34,
				'tracker_id'		=> 3,
				'status_name'		=> 'UNREPRODUCIBLE',
				'status_desc'		=> 'UNREPRODUCIBLE_EXPLAIN',
				'status_order'		=> 14,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 35,
				'tracker_id'		=> 4,
				'status_name'		=> 'NEW',
				'status_desc'		=> 'NEW_EXPLAIN',
				'status_order'		=> 1,
				'ticket_new'		=> 1,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 36,
				'tracker_id'		=> 4,
				'status_name'		=> 'SUPPORT_REQUEST',
				'status_desc'		=> 'SUPPORT_REQUEST_EXPLAIN',
				'status_order'		=> 2,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 37,
				'tracker_id'		=> 4,
				'status_name'		=> 'INVALID',
				'status_desc'		=> '',
				'status_order'		=> 3,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 38,
				'tracker_id'		=> 4,
				'status_name'		=> 'DUPLICATE',
				'status_desc'		=> 'DUPLICATE_EXPLAIN',
				'status_order'		=> 4,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 1,
			],

			[
				'status_id'			=> 39,
				'tracker_id'		=> 4,
				'status_name'		=> 'IMPLEMENTING',
				'status_desc'		=> '',
				'status_order'		=> 5,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 40,
				'tracker_id'		=> 4,
				'status_name'		=> 'WILL_NOT_IMPLEMENT',
				'status_desc'		=> '',
				'status_order'		=> 6,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 41,
				'tracker_id'		=> 4,
				'status_name'		=> 'IMPLEMENTED_VCS',
				'status_desc'		=> '',
				'status_order'		=> 7,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 1,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 42,
				'tracker_id'		=> 4,
				'status_name'		=> 'RESEARCHING',
				'status_desc'		=> '',
				'status_order'		=> 8,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 43,
				'tracker_id'		=> 4,
				'status_name'		=> 'REVIEWED',
				'status_desc'		=> 'REVIEWED_EXPLAIN',
				'status_order'		=> 9,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 1,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 44,
				'tracker_id'		=> 4,
				'status_name'		=> 'REVIEW_LATER',
				'status_desc'		=> 'REVIEW_LATER_EXPLAIN',
				'status_order'		=> 10,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 45,
				'tracker_id'		=> 4,
				'status_name'		=> 'AWAITING_INFO',
				'status_desc'		=> 'AWAITING_INFO_EXPLAIN',
				'status_order'		=> 11,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 46,
				'tracker_id'		=> 4,
				'status_name'		=> 'AWAITING_TEAM',
				'status_desc'		=> '',
				'status_order'		=> 12,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],

			[
				'status_id'			=> 47,
				'tracker_id'		=> 4,
				'status_name'		=> 'PENDING',
				'status_desc'		=> '',
				'status_order'		=> 13,
				'ticket_new'		=> 0,
				'ticket_reviewed'	=> 0,
				'ticket_closed'		=> 0,
				'ticket_fixed'		=> 0,
				'ticket_duplicate'	=> 0,
			],
		];

		$tracker_data = [
			[
				'tracker_id'			=> 1,
				'tracker_name'			=> 'INCIDENT_TRACKER',
				'tracker_icon'			=> 'life-ring',
				'tracker_email'			=> '',
				'tracker_visibility'	=> 1,
				'tracker_status'		=> 1,
			],

			[
				'tracker_id'			=> 2,
				'tracker_name'			=> 'SECURITY_TRACKER',
				'tracker_icon'			=> 'shield',
				'tracker_email'			=> '',
				'tracker_visibility'	=> 1,
				'tracker_status'		=> 1,
			],

			[
				'tracker_id'			=> 3,
				'tracker_name'			=> 'BUG_TRACKER',
				'tracker_icon'			=> 'bug',
				'tracker_email'			=> '',
				'tracker_visibility'	=> 0,
				'tracker_status'		=> 1,
			],

			[
				'tracker_id'			=> 4,
				'tracker_name'			=> 'FEATURE_TRACKER',
				'tracker_icon'			=> 'gift',
				'tracker_email'			=> '',
				'tracker_visibility'	=> 0,
				'tracker_status'		=> 1,
			],
		];

		$this->db->sql_multi_insert($this->table_prefix . 'trackers_severity', $severity_data);
		$this->db->sql_multi_insert($this->table_prefix . 'trackers_status', $status_data);
		$this->db->sql_multi_insert($this->table_prefix . 'trackers_trackers', $tracker_data);
	}
}
