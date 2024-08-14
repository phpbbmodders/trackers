<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\trackers\includes;

use Symfony\Component\DependencyInjection\ContainerInterface;
use phpbbmodders\trackers\constants;

class ticket
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface                 $container
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\user                        $user
	 * @param string                             $table_prefix
	 */
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, $table_prefix, $object_data = [])
	{
		$this->container = $container;
		$this->db = $db;
		$this->user = $user;
		$this->table_prefix = $table_prefix;

		$this->functions = $this->container->get('phpbbmodders.trackers.functions');

		if (!empty($object_data))
		{
			foreach ($object_data as $key => $value)
			{
				$this->$key = $value;
			}
		}
	}

	public function base_vars($object_data)
	{
		return new ticket($this->container, $this->db, $this->user, $this->table_prefix, $object_data);
	}

	/**
	 * Load a specific ticket
	 */
	public function load($ticket_id)
	{
		$sql = 'SELECT t.*, st.status_name, st.ticket_closed, st.ticket_duplicate, sv.severity_name, c.component_name,
				r.username AS ticket_user, r.user_colour AS ticket_colour,
				au.user_id AS assigned_user_id, au.username AS assigned_username, au.user_colour AS assigned_user_colour,
				ag.group_id AS assigned_group_id, ag.group_name AS assigned_group_name, ag.group_colour AS assigned_group_colour
			FROM (' . $this->table_prefix . 'trackers_tickets t, ' . $this->table_prefix . 'users r)
			LEFT JOIN ' . $this->table_prefix . 'trackers_status st
				ON (st.status_id = t.status_id)
			LEFT JOIN ' . $this->table_prefix . 'trackers_severity sv
				ON (sv.severity_id = t.severity_id)
			LEFT JOIN ' . $this->table_prefix . 'trackers_components c
				ON (c.component_id = t.component_id)
			LEFT JOIN ' . $this->table_prefix . 'users au
				ON (au.user_id = t.assigned_user)
			LEFT JOIN ' . $this->table_prefix . 'groups ag
				ON (ag.group_id = t.assigned_group)
			WHERE r.user_id = t.poster_id
				AND t.ticket_id = ' . (int) $ticket_id;

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (empty($row)) ? null : $this->base_vars($row);
	}

	/**
	 * Get the ticket description
	 */
	public function get_description($decode_message = false)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_prefix . 'trackers_posts
			WHERE post_visibility = 0
				AND post_id = ' . (int) $this->post_id;
		$result = $this->db->sql_query($sql);
		$post_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$post_text = $post_data['post_text'];

		if (!$decode_message)
		{
			$post_data['bbcode_options'] = (($post_data['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
				(($post_data['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) +
				(($post_data['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);

			$post_text = generate_text_for_display($post_text, $post_data['bbcode_uid'], $post_data['bbcode_bitfield'], $post_data['bbcode_options']);
		}
		else
		{
			decode_message($post_text, $post_data['bbcode_uid']);
		}

		return $post_text;
	}

	/**
	 * Determing if the user has access to this ticket
	 */
	public function has_access($user_id = 0)
	{
		$user_id = (empty($user_id)) ? (int) $this->user->data['user_id'] : (int) $user_id;

		// Private tickets
		if ($this->ticket_visibility && !$this->functions->can_report_private())
		{
			return false;
		}

		// The reporter always has access
		if ($user_id == $this->poster_id)
		{
			return true;
		}

		// Anyone has access to a ticket in a tracker that has public visibility
		if ($this->tracker->tracker_visibility = constants::ITEM_PUBLIC)
		{
			return true;
		}

		// Team members have access to all tickets
		if ($this->project->is_team_user($user_id))
		{
			return true;
		}

		return false;
	}
}
