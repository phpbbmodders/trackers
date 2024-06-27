<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\trackers\operators;

use Symfony\Component\DependencyInjection\ContainerInterface;
use phpbbmodders\trackers\constants;

/**
 * Trackers
 */
class viewproject
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface                 $container
	 * @param \phpbb\config\config               $config
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\language\language           $language
	 * @param \phpbb\controller\helper           $helper
	 * @param \phpbb\request\request             $request
	 * @param \phpbb\template\template           $template
	 * @param \phpbb\user                        $user
	 * @param string                             $table_prefix
	 */
	public function __construct(ContainerInterface $container, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $table_prefix)
	{
		$this->container = $container;
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->helper = $helper;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->table_prefix = $table_prefix;

		$this->functions = $this->container->get('phpbbmodders.trackers.functions');

		$this->tracker = $this->container->get('phpbbmodders.trackers.tracker');
		$this->project = $this->container->get('phpbbmodders.trackers.project');
	}

	public function display()
	{
		$tracker_id = $this->request->variable('t', 0);
		$project_id = $this->request->variable('p', 0);

		$this->language->add_lang('viewproject', 'phpbbmodders/trackers');

		$tracker = $this->tracker->load($tracker_id);
		$project = $this->project->load($project_id);

		if (is_null($project))
		{
			throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_PROJECT'));
		}

		$status_ary = $tracker->get_statuses();

		foreach ($status_ary as $status_id => $status_data)
		{
			$this->template->assign_block_vars('ticket_status', [
				'ID'	=> $status_id,
				'NAME'	=> $this->language->lang($status_data['status_name']),
			]);
		}

		$pagination = $this->container->get('pagination');
		$s_hidden_fields = build_hidden_fields(['t' => (int) $tracker_id, 'p' => (int) $project_id]);

		$start = $this->request->variable('start', 0);
		$user_id = $this->request->variable('user_id', 0);
		$ticket_status = $this->request->variable('ticket_status', 0);
		$assigned_user = $this->request->variable('assigned_user', 0);
		$assigned_group = $this->request->variable('assigned_group', 0);
		$component = $this->request->variable('component', 0);
		$severity = $this->request->variable('severity', -1);

		$sql = 'FROM (' . $this->table_prefix . 'trackers_tickets t, ' . $this->table_prefix . 'trackers_status st)
			LEFT JOIN ' . $this->table_prefix . 'users r
			ON r.user_id = t.poster_id
			LEFT JOIN ' . $this->table_prefix . 'users au
				ON au.user_id = t.assigned_user
			LEFT JOIN ' . $this->table_prefix . 'groups ag
				ON ag.group_id = t.assigned_group
			LEFT JOIN ' . $this->table_prefix . 'trackers_severity se
				ON se.severity_id = t.severity_id
			LEFT JOIN ' . $this->table_prefix . 'trackers_components c
				ON c.component_id = t.component_id
			LEFT JOIN ' . $this->table_prefix . 'trackers_posts tp
				ON tp.ticket_id = t.ticket_id
			WHERE st.status_id = t.status_id
				AND t.project_id = ' . (int) $project->project_id;

		if ($ticket_status == constants::STATUS_OPEN)
		{
			$sql .= ' AND st.ticket_closed = 0';
		}
		else if ($ticket_status == constants::STATUS_CLOSED)
		{
			$sql .= ' AND st.ticket_closed = 1';
		}
		else if ($ticket_status != constants::STATUS_ALL && ($tracker->tracker_visibility || $project->is_team_user()))
		{
			$sql .= ' AND t.status_id = ' . (int) $ticket_status;
		}

		// Restrict visible tickets
		if (!$tracker->tracker_visibility && !$project->is_team_user())
		{
			$sql .= ' AND t.poster_id = ' . $this->user->data['user_id'];
		}

		// Verify this user is allowed to see all tickets a user reported
		else if ($user_id > 0 && ($user_id == $this->user->data['user_id'] || $tracker->tracker_visibility || $project->is_team_user()))
		{
			$sql .= ' AND t.poster_id = ' . $user_id;
		}

		// Hide private tickets from non-teamies
		if (!$project->is_team_user() && !$this->functions->can_report_private())
		{
			$sql .= ' AND (t.ticket_visibility = 0 OR t.poster_id = ' . $this->user->data['user_id'] . ')';
		}

		// Show only the tickets that have been assigned to the current user
		if ($assigned_user > 0 && ($assigned_user == $this->user->data['user_id'] || $project->is_team_user()))
		{
			$sql .= ' AND t.assigned_user = ' . (int) $assigned_user;
		}

		// Show only the tickets that have been assigned to this user's group(s)
		if ($assigned_group != 0 && $project->is_team_user())
		{
			if ($assigned_group == -1)
			{
				$sql .= ' AND t.assigned_group IN (
					SELECT group_id
					FROM ' . $this->table_prefix . 'USER_GROUP
					WHERE user_pending = 0
						AND user_id = ' . (int) $this->user->data['user_id'] . ')';
			}
			else
			{
				$sql .= ' AND t.assigned_group = ' . (int) $assigned_group;
			}
		}
		else
		{
			$assigned_group = 0;
		}

		// Filter by components
		if ($component > 0)
		{
			$sql .= ' AND t.component_id = ' . (int) $component;
		}

		// Filter by severity
		if ($severity > -1)
		{
			$sql .= ' AND t.severity_id = ' . (int) $severity;
		}

		$sql .= ' GROUP BY t.ticket_id';

		// Get the total number of tickets, needed for pagination
		$result = $this->db->sql_query('SELECT t.poster_id AS ticket_user_id ' . $sql);
		$tickets_total = $this->db->sql_affectedrows();
		$this->db->sql_freeresult($result);

		// Handle pagination
		$start = $pagination->validate_start($start, $this->config['tickets_per_page'], $tickets_total);
		$base_url = $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket_status' => (int) $ticket_status]);
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $tickets_total, $this->config['tickets_per_page'], $start);

		$sql = 'SELECT t.ticket_id, t.status_id, t.ticket_visibility, t.ticket_title, t.duplicate_id, st.status_name, st.status_id, st.ticket_duplicate, se.severity_colour, c.component_name, t.ticket_time,
			au.user_id AS assigned_user_id, au.username AS assigned_username, au.user_colour AS assigned_user_colour,
			ag.group_id AS assigned_group_id, ag.group_name AS assigned_group_name, ag.group_colour AS assigned_group_colour,
			t.poster_id AS reporter_id, r.username AS reporter_username, r.user_colour AS reporter_colour ' . $sql;
		$result = $this->db->sql_query_limit($sql, $this->config['tickets_per_page'], $start);
		while ($ticket_data = $this->db->sql_fetchrow($result))
		{
			// Determine who this ticket was assigned to
			$assigned_to = '';

			if (!empty($ticket_data['assigned_username']))
			{
				$assigned_to = get_username_string('full', $ticket_data['assigned_user_id'], $ticket_data['assigned_username'], $ticket_data['assigned_user_colour']);
			}
			else if (!empty($ticket_data['assigned_group_name']))
			{
				$group_helper = $this->container->get('group_helper');
				$assigned_to = $group_helper->get_name_string('full', $ticket_data['assigned_group_id'], $ticket_data['assigned_group_name'], $ticket_data['assigned_group_colour']);
			}

			$this->template->assign_block_vars('tickets', [
				'S_PRIVATE'	=> $ticket_data['ticket_visibility'],

				'U_DUPLICATE'	=> ($ticket_data['ticket_duplicate'] && $ticket_data['duplicate_id']) ? $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_data['duplicate_id']]) : '',
				'U_VIEWTICKET'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_data['ticket_id']]),

				'ID'			=> $ticket_data['ticket_id'],
				'REPORTER'		=> get_username_string('full', $ticket_data['reporter_id'], $ticket_data['reporter_username'], $ticket_data['reporter_colour']),
				'COMPONENT'		=> $ticket_data['component_name'],
				'ASSIGNED'		=> $assigned_to,
				'TITLE'			=> $ticket_data['ticket_title'],
				'STATUS'		=> $this->language->lang($ticket_data['status_name']),
				'DUPLICATE_ID'	=> ($ticket_data['ticket_duplicate']) ? $ticket_data['duplicate_id'] : false,
				'TIME'			=> $this->user->format_date($ticket_data['ticket_time']),
				'SEV_COLOUR'	=> $ticket_data['severity_colour'],
			]);
		}
		$this->db->sql_freeresult($result);

		switch ($ticket_status)
		{
			case 0:
				$status = $this->language->lang('ALL_OPEN');
			break;

			case -1:
				$status = $this->language->lang('ALL_TICKETS');
			break;

			case -2:
				$status = $this->language->lang('ALL_CLOSED');
			break;

			default:
				$status = $tracker->get_statuses();
				$status = $this->language->lang($status[$ticket_status]['status_name']);
			break;
		}

		// Some fields should be left when moving between different pages
		$query_parts = [
			'user_id'			=> $user_id,
			'ticket_status'		=> $ticket_status,
			'assigned_user'		=> $assigned_user,
			'assigned_group'	=> $assigned_group,
			'component'			=> $component,
			'severity'			=> $severity,
		];

		foreach ($query_parts as $field_name => $field_value)
		{
			if ($field_name == 'ticket_status')
			{
				continue;
			}

			$this->template->assign_block_vars('filter_hidden', [
				'NAME'	=> $field_name,
				'VALUE'	=> $field_value,
			]);
		}

		$this->template->assign_vars([
			'TRACKER_NAME'	=> $this->language->lang($tracker->tracker_name),

			'S_SHOW_ALL_TICKETS'	=> $tracker->tracker_visibility == constants::ITEM_PUBLIC,
			'S_PROJECT_TEAM'		=> $project->is_team_user(),

			'STATUS_ID'	=> $ticket_status,
			'STATUS'	=> $status,

			'TOTAL_TICKETS' => $this->language->lang('TOTAL_TICKETS', $tickets_total),

			'U_ACTION'			=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project_id]),
			'U_POST_NEW_TICKET'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'posting', 'mode' => 'post', 't' => (int) $tracker_id, 'p' => (int) $project_id]),

			'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
		]);

		$navlinks = [
			[
				'FORUM_NAME'	=> $this->language->lang($tracker->tracker_name),
				'U_VIEW_FORUM'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewtracker', 't' => (int) $tracker_id]),
			],
			[
				'FORUM_NAME'	=> $project->project_name,
				'U_VIEW_FORUM'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project_id]),
			],
		];

		$this->functions->generate_navlinks($navlinks);

		return $this->helper->render('viewproject_body.html', $this->language->lang($tracker->tracker_name));
	}
}
