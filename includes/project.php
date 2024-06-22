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

class project
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

		$this->constants = $this->container->get('phpbbmodders.trackers.constants');

		$this->ticket = $this->container->get('phpbbmodders.trackers.ticket');
		$this->tracker_cache = $this->container->get('phpbbmodders.trackers.tracker_cache');

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
		return new project($this->container, $this->db, $this->user, $this->table_prefix, $object_data);
	}

	/**
	 * Create a project
	 */
	public function create($tracker_id, $project_name, $project_visibility = $this->constants::ITEM_PUBLIC, $project_status = $this->constants::ITEM_ACTIVE)
	{
		$project_data = [
			'tracker_id'			=> (int) $tracker_id,
			'project_name'			=> $project_name,
			'project_visibility'	=> $project_visibility,
			'project_status'		=> $project_status,
		];

		$project = $this->base_vars($project_data);
		$project->save($project_data);

		$this->tracker_cache->destroy_projects();

		return $project;
	}

	/**
	 * Load a specific project
	 */
	public function load($project_id)
	{
		$project_data = $this->tracker_cache->get_project_data($project_id);

		if (!$project_data)
		{
			return null;
		}

		return $this->base_vars($project_data);
	}

	/**
	 * Save method
	 */
	public function save($project_data)
	{
		$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'trackers_projects ' . $this->db->sql_build_array('INSERT', $project_data));
		$this->project_id = $this->db->sql_nextid();

		$this->tracker_cache->destroy_project_cache($this->project_id);
	}

	/**
	 * Determine if a user has access to this project
	 */
	public function has_access($user_id = 0)
	{
		$user_id = (empty($user_id)) ? (int) $this->user->data['user_id'] : (int) $user_id;

		if ($this->project_visibility = $this->constants::ITEM_PRIVATE && !$this->is_team_user($user_id))
		{
			return false;
		}

		return true;
	}

	/**
	 * Determine if a user has 'team' permissions in this project
	 */
	public function is_team_user($user_id = 0)
	{
		if (empty($user_id))
		{
			$user_id = (int) $this->user->data['user_id'];

			if ($this->user->data['is_bot'] || $user_id == ANONYMOUS)
			{
				return false;
			}
		}

		return array_key_exists($user_id, $this->get_team_users());
	}

	/**
	 * Determine if a user has 'team' permissions in this project
	 */
	public function is_team_group($group_id)
	{
		return array_key_exists($group_id, $this->get_team_groups());
	}

	/**
	 * Add a ticket to this project
	 */
	public function add_ticket($title, $message)
	{
		$ticket = $this->ticket->create($this->project_id, $this->user->data['user_id'], $this->user->ip, $title, $message);

		return $ticket;
	}

	/**
	 * Add a user to the team member list for this project
	 */
	public function add_team_user($user_id)
	{
		$sql_data = [
			'project_id'	=> $this->project_id,
			'user_id'		=> $user_id,
		];

		$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'trackers_project_auth ' . $this->db->sql_build_array('INSERT', $sql_data));

		// Destroy the cache
		$this->tracker_cache->destroy_team_users_cache($this->project_id);
	}

	/**
	 * Add a usergroup to the team member list for this project
	 */
	public function add_team_group($group_id)
	{
		$sql_data = [
			'project_id'	=> $this->project_id,
			'group_id'		=> $group_id,
		];

		$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'trackers_project_auth ' . $this->db->sql_build_array('INSERT', $sql_data));

		// Destroy the cache
		$this->tracker_cache->destroy_team_groups_cache($this->project_id);
	}

	/**
	 * Remove a user from this project's team member list
	 */
	public function remove_team_user($user_id)
	{
		$sql = 'DELETE
			FROM ' . $this->table_prefix . 'trackers_project_auth
			WHERE project_id = ' . (int) $this->project_id . '
				AND user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);

		// Destroy the cache
		$this->tracker_cache->destroy_team_users_cache($this->project_id);

		return (bool) $this->db->sql_affectedrows($result);
	}

	/**
	 * Remove a group from this project's team member list
	 */
	public function remove_team_group($group_id)
	{
		$sql = 'DELETE
			FROM trackers_project_auth
			WHERE project_id = ' . (int) $this->project_id . '
				AND group_id = ' . (int) $group_id;
		$result = $this->db->sql_query($sql);

		// Destroy the cache
		$this->tracker_cache->destroy_team_users_cache($this->project_id);
		$this->tracker_cache->destroy_team_groups_cache($this->project_id);

		return (bool) $this->db->sql_affectedrows($result);
	}

	/**
	 * Delete a project
	 */
	public function delete($move_to_project = 0)
	{
		$tracker_cache->destroy_projects();

		//parent::delete();
	}

	/**
	 * Get the groups that have team status for this project
	 */
	public function get_team_groups()
	{
		return $this->tracker_cache->get_team_groups($this->project_id);
	}

	/**
	 * Get the users that have team status for this project
	 */
	public function get_team_users()
	{
		return $this->tracker_cache->get_team_users($this->project_id);
	}

	/**
	 * Get the available components for this project
	 */
	public function get_components()
	{
		return $this->tracker_cache->get_components($this->project_id);
	}
}
