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

class functions
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface                 $container
	 * @param \phpbb\auth\auth                   $auth
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\template\template           $template
	 * @param string                             $table_prefix
	 */
	public function __construct(ContainerInterface $container, \phpbb\auth\auth $auth, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, $table_prefix)
	{
		$this->container = $container;
		$this->auth = $auth;
		$this->db = $db;
		$this->template = $template;
		$this->table_prefix = $table_prefix;

		$this->project = $this->container->get('phpbbmodders.trackers.project');
	}

	/**
	 * Returns whether the current user can report/see private
	 * tickets; this is anyone with a_ or m_ permissions
	 */
	public function can_report_private()
	{
		if ($this->auth->acl_get('a_') || $this->auth->acl_getf_global('m_'))
		{
			return true;
		}

		if ($this->project->is_team_user())
		{
			return true;
		}

		return false;
	}

	/**
	 * Returns whether the current user is able to set the severity
	 * of a ticket, this is anyone with m_ permissions
	 */
	public function can_set_severity()
	{
		if ($this->auth->acl_getf_global('m_'))
		{
			return true;
		}

		return false;
	}

	/**
	 * Get array of duplicates of ticket
	 */
	public function get_duplicate_tickets($ticket_id)
	{
		$tickets = [];

		$sql = 'SELECT t.ticket_id, t.ticket_title
			FROM ' . $this->table_prefix . 'trackers_tickets t
			LEFT JOIN ' . $this->table_prefix . 'trackers_status st
				ON (st.status_id = t.status_id)
			WHERE st.ticket_duplicate = 1
				AND t.duplicate_id = ' . (int) $ticket_id;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$id = (int) $row['ticket_id'];
			$tickets[$id] = $row;
		}
		$this->db->sql_freeresult($result);

		return $tickets;
	}

	public function generate_navlinks($navlinks)
	{
		if ($navlinks)
		{
			foreach ($navlinks as $navlink)
			{
				$this->template->assign_block_vars('navlinks', [
					'FORUM_NAME'	=> $navlink['FORUM_NAME'],
					'U_VIEW_FORUM'	=> $navlink['U_VIEW_FORUM'],
				]);
			}
		}
	}
}
