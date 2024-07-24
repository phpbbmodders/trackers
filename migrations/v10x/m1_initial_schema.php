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

class m1_initial_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'trackers_trackers');
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	/**
	 * Update database schema
	 */
	public function update_schema()
	{
		return [
			'add_tables'		=> [
				$this->table_prefix . 'trackers_attachments'		=> [
					'COLUMNS'		=> [
						'attach_id'				=> ['UINT', null, 'auto_increment'],
						'post_msg_id'			=> ['UINT', 0],
						'ticket_id'				=> ['UINT', 0],
						'in_message'			=> ['BOOL', 0],
						'poster_id'				=> ['UINT', 0],
						'is_orphan'				=> ['BOOL', 1],
						'physical_filename'		=> ['VCHAR:255', ''],
						'real_filename'			=> ['VCHAR:255', ''],
						'download_count'		=> ['UINT', 0],
						'attach_comment'		=> ['TEXT_UNI', ''],
						'extension'				=> ['VCHAR:100', ''],
						'mimetype'				=> ['VCHAR:100', ''],
						'filesize'				=> ['UINT', 0],
						'filetime'				=> ['TIMESTAMP', 0],
						'thumbnail'				=> ['BOOL', 0],
					],
					'PRIMARY_KEY'	=> 'attach_id',
					'KEYS'			=> [
						'filetime'				=> ['INDEX', 'filetime'],
						'post_msg_id'			=> ['INDEX', 'post_msg_id'],
						'ticket_id'				=> ['INDEX', 'ticket_id'],
						'poster_id'				=> ['INDEX', 'poster_id'],
						'is_orphan'				=> ['INDEX', 'is_orphan'],
					],
				],

				$this->table_prefix . 'trackers_components'			=> [
					'COLUMNS'		=> [
						'component_id'			=> ['UINT', null, 'auto_increment'],
						'project_id'			=> ['UINT', 0],
						'component_name'		=> ['VCHAR:255', ''],
					],
					'PRIMARY_KEY'	=> 'component_id',
					'KEYS'			=> [
						'project_id'			=> ['INDEX', 'project_id'],
					],
				],

				$this->table_prefix . 'trackers_history'			=> [
					'COLUMNS'		=> [
						'history_id'			=> ['UINT', null, 'auto_increment'],
						'ticket_id'				=> ['UINT', 0],
						'poster_id'				=> ['UINT', 0],
						'history_text'			=> ['TEXT_UNI', ''],
						'history_timestamp'		=> ['TIMESTAMP', 0],
						'history_type'			=> ['TINT:3', 0],
					],
					'PRIMARY_KEY'	=> 'history_id',
				],

				$this->table_prefix . 'trackers_posts'				=> [
					'COLUMNS'		=> [
						'post_id'				=> ['UINT', null, 'auto_increment'],
						'ticket_id'				=> ['UINT', 0],
						'poster_id'				=> ['UINT', 0],
						'poster_ip'				=> ['VCHAR:40', ''],
						'post_time'				=> ['TIMESTAMP', 0],
						'enable_bbcode'			=> ['BOOL', 1],
						'enable_smilies'		=> ['BOOL', 1],
						'enable_magic_url'		=> ['BOOL', 1],
						'post_text'				=> ['TEXT_UNI', ''],
						'post_attachment'		=> ['BOOL', 0],
						'bbcode_bitfield'		=> ['VCHAR:255', ''],
						'bbcode_uid'			=> ['VCHAR:8', ''],
						'post_visibility'		=> ['BOOL', 0],
					],
					'PRIMARY_KEY'	=> 'post_id',
					'KEYS'			=> [
						'ticket_id'				=> ['INDEX', 'ticket_id'],
						'poster_ip'				=> ['INDEX', 'poster_ip'],
						'poster_id'				=> ['INDEX', 'poster_id'],
						'post_visibility'		=> ['INDEX', 'post_visibility'],
					],
				],

				$this->table_prefix . 'trackers_projects'			=> [
					'COLUMNS'		=> [
						'project_id'			=> ['UINT', null, 'auto_increment'],
						'tracker_id'			=> ['UINT', 0],
						'project_name'			=> ['VCHAR:255', ''],
						'project_desc'			=> ['TEXT_UNI', ''],
						'project_note'			=> ['TEXT_UNI', ''],
						'project_visibility'	=> ['BOOL', 0],
						'project_status'		=> ['BOOL', 0],
					],
					'PRIMARY_KEY'	=> 'project_id',
				],

				$this->table_prefix . 'trackers_projects_auth'		=> [
					'COLUMNS'		=> [
						'project_id'			=> ['UINT', 0],
						'group_id'				=> ['UINT', 0],
						'user_id'				=> ['UINT', 0],
					],
				],

				$this->table_prefix . 'trackers_severity'			=> [
					'COLUMNS'		=> [
						'severity_id'			=> ['UINT', null, 'auto_increment'],
						'tracker_id'			=> ['UINT', 0],
						'severity_name'			=> ['VCHAR:255', ''],
						'severity_colour'		=> ['VCHAR:6', ''],
						'severity_order'		=> ['TINT:3', 0],
					],
					'PRIMARY_KEY'	=> 'severity_id',
				],

				$this->table_prefix . 'trackers_status'				=> [
					'COLUMNS'		=> [
						'status_id'				=> ['UINT', null, 'auto_increment'],
						'tracker_id'			=> ['UINT', 0],
						'status_name'			=> ['VCHAR:255', ''],
						'status_desc'			=> ['TEXT_UNI', ''],
						'status_order'			=> ['TINT:3', 0],
						'ticket_new'			=> ['BOOL', 0],
						'ticket_reviewed'		=> ['BOOL', 0],
						'ticket_closed'			=> ['BOOL', 0],
						'ticket_fixed'			=> ['BOOL', 0],
						'ticket_duplicate'		=> ['BOOL', 0],
					],
					'PRIMARY_KEY'	=> 'status_id',
				],

				$this->table_prefix . 'trackers_tickets'			=> [
					'COLUMNS'		=> [
						'ticket_id'				=> ['UINT', null, 'auto_increment'],
						'project_id'			=> ['UINT', 0],
						'post_id'				=> ['UINT', 0],
						'ticket_attachment'		=> ['BOOL', 0],
						'ticket_title'			=> ['VCHAR:255', ''],
						'poster_id'				=> ['UINT', 0],
						'poster_ip'				=> ['VCHAR:40', ''],
						'ticket_time'			=> ['TIMESTAMP', 0],
						'status_id'				=> ['UINT', 0],
						'component_id'			=> ['UINT', 0],
						'severity_id'			=> ['UINT', 0],
						'duplicate_id'			=> ['UINT', 0],
						'ticket_visibility'		=> ['BOOL', 0],
						'ticket_locked'			=> ['BOOL', 0],
						'assigned_group'		=> ['UINT', 0],
						'assigned_user'			=> ['UINT', 0],
					],
					'PRIMARY_KEY'	=> 'ticket_id',
					'KEYS'			=> [
						'project_id'			=> ['INDEX', 'project_id'],
						'poster_id'				=> ['INDEX', 'poster_id'],
						'status_id'				=> ['INDEX', 'status_id'],
						'severity_id'			=> ['INDEX', 'severity_id'],
						'component_id'			=> ['INDEX', 'component_id'],
						'ticket_visibility'		=> ['INDEX', 'ticket_visibility'],
					],
				],

				$this->table_prefix . 'trackers_trackers'			=> [
					'COLUMNS'		=> [
						'tracker_id'			=> ['UINT', null, 'auto_increment'],
						'tracker_name'			=> ['VCHAR:255', ''],
						'tracker_icon'			=> ['VCHAR:255', ''],
						'tracker_email'			=> ['VCHAR:255', ''],
						'tracker_visibility'	=> ['BOOL', 0],
						'tracker_status'		=> ['BOOL', 0],
					],
					'PRIMARY_KEY'	=> 'tracker_id',
				],
			],
		];
	}

	/**
	 * Revert database schema
	 */
	public function revert_schema()
	{
		return [
			'drop_tables'		=> [
				$this->table_prefix . 'trackers_attachments',
				$this->table_prefix . 'trackers_components',
				$this->table_prefix . 'trackers_history',
				$this->table_prefix . 'trackers_posts',
				$this->table_prefix . 'trackers_projects',
				$this->table_prefix . 'trackers_projects_auth',
				$this->table_prefix . 'trackers_severity',
				$this->table_prefix . 'trackers_status',
				$this->table_prefix . 'trackers_tickets',
				$this->table_prefix . 'trackers_trackers',
			],
		];
	}
}
