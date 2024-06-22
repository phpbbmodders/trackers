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
//use phpbbmodders\trackers\constants;

class ticket
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface                 $container
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\language\language           $language
	 * @param \phpbb\user                        $user
	 * @param string                             $table_prefix
	 */
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\user $user, $table_prefix, $object_data = [])
	{
		$this->container = $container;
		$this->db = $db;
		$this->language = $language;
		$this->user = $user;
		$this->table_prefix = $table_prefix;

		$this->project = $this->container->get('phpbbmodders.trackers.project');
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
		return new ticket($this->container, $this->db, $this->language, $this->user, $this->table_prefix, $object_data);
	}

	/**
	 * Create a ticket
	 */
	public function create($project_id, $user_id, $user_ip, $title, $message)
	{
		$project = $this->project->load($project_id);

		if (!$project)
		{
			return false;
		}

		// Add the ticket
		$ticket_data = [
			'project_id'		=> (int) $project_id,
			'post_id'			=> 0,
			'poster_id'			=> (int) $user_id,
			'poster_ip'			=> $user_ip,
			'status_id'			=> 0,//$project->tracker->new_status,
			'component_id'		=> 0,
			'severity_id'		=> 0,
			'ticket_title'		=> $title,
			'ticket_time'		=> time(),
		];

		$ticket = $this->base_vars($ticket_data);
		$ticket->save($ticket_data);

		return $ticket;
	}

	/**
	 * Load a specific ticket
	 */
	public function load($ticket_id, $project_id = 0)
	{
		$sql = 'SELECT t.*, st.status_name, st.ticket_closed, st.ticket_duplicate, r.username AS ticket_user, r.user_colour AS ticket_colour,
			c.component_name, au.username AS assigned_user_name, au.user_colour AS assigned_user_colour,
			ag.group_name AS assigned_group_name, ag.group_colour AS assigned_group_colour
			FROM (' . $this->table_prefix . 'trackers_tickets t, ' . $this->table_prefix . 'users r)
			LEFT JOIN ' . $this->table_prefix . 'trackers_status st
				ON (st.status_id = t.status_id)
			LEFT JOIN ' . $this->table_prefix . 'trackers_components c
				ON (c.component_id = t.component_id)
			LEFT JOIN ' . $this->table_prefix . 'users au
				ON (au.user_id = t.assigned_user)
			LEFT JOIN ' . $this->table_prefix . 'groups ag
				ON (ag.group_id = t.assigned_group)
			WHERE r.user_id = t.poster_id
				AND t.ticket_id = ' . (int) $ticket_id;

		if ($project_id > 0)
		{
			$sql .= ' AND t.project_id = ' . (int) $project_id;
		}

		$result = $this->db->sql_query($sql);
		$row = $ths->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (empty($row)) ? null : $this->base_vars($row);
	}
}
