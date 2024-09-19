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

/**
 * Trackers
 */
class viewticket
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\auth\auth */
	protected $auth;

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
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface                 $container
	 * @param \phpbb\auth\auth                   $auth
	 * @param \phpbb\config\config               $config
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\language\language           $language
	 * @param \phpbb\controller\helper           $helper
	 * @param \phpbb\request\request             $request
	 * @param \phpbb\template\template           $template
	 * @param \phpbb\user                        $user
	 * @param string                             $root_path
	 * @param string                             $php_ext
	 * @param string                             $table_prefix
	 */
	public function __construct(ContainerInterface $container, \phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $root_path, $php_ext, $table_prefix)
	{
		$this->container = $container;
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->helper = $helper;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;

		$this->functions = $this->container->get('phpbbmodders.trackers.functions');

		$this->tracker = $this->container->get('phpbbmodders.trackers.tracker');
		$this->project = $this->container->get('phpbbmodders.trackers.project');
		$this->ticket = $this->container->get('phpbbmodders.trackers.ticket');
	}

	public function display()
	{
		$tracker_id = $this->request->variable('t', 0);
		$project_id = $this->request->variable('p', 0);
		$ticket_id = $this->request->variable('ticket', 0);

		$this->language->add_lang('viewticket', 'phpbbmodders/trackers');

		$tracker = $this->tracker->load($tracker_id);
		$project = $this->project->load($project_id);
		$ticket = $this->ticket->load($ticket_id);

		$pagination = $this->container->get('pagination');

		$start = $this->request->variable('start', 0);
		$group_helper = $this->container->get('group_helper');

		if (!$ticket || !$ticket->has_access())
		{
			throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_TICKET'));
		}

		// Ticket details
		$ticket_details = [];
		$ticket_details[$this->language->lang('TICKET_ID')] = $ticket->ticket_id;
		$ticket_details[$this->language->lang('PROJECT')] = $project->project_name;
		$ticket_details[$this->language->lang('STATUS')] = $this->language->lang($ticket->status_name);

		if ($ticket->ticket_duplicate && $ticket->duplicate_id > 0)
		{
			$ticket_details[$this->language->lang('STATUS')] .= ' » <a href="' . $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket->duplicate_id]) . '" style="color: #ff0000">#' . $ticket->duplicate_id . '</a>';
		}

		if ($ticket->severity_name)
		{
			$ticket_details[$this->language->lang('SEVERITY')] = $ticket->severity_name ?? $this->language->lang('UNCATEGORISED');
		}

		if ($ticket->component_name)
		{
			$ticket_details[$this->language->lang('COMPONENT')] = $ticket->component_name ?? $this->language->lang('UNKNOWN');
		}

		$reported_by = get_username_string('full', $ticket->poster_id, $ticket->ticket_user, $ticket->ticket_colour);
		$reported_by .= ' (<a href="' . append_sid("{$this->root_path}ucp.$this->php_ext, i=pm&amp;mode=compose&amp;u=" . $ticket->poster_id) . '">' . $this->language->lang('SEND_PM') . '</a>)';
		$ticket_details[$this->language->lang('REPORTED_BY')] = $reported_by;

		$assigned_to = '';

		if (!empty($ticket->assigned_username))
		{
			$assigned_to = get_username_string('full', $ticket->assigned_user_id, $ticket->assigned_username, $ticket->assigned_user_colour);
		}
		else if (!empty($ticket->assigned_group_name))
		{
			$assigned_to = $group_helper->get_name_string('full', $ticket->assigned_group_id, $ticket->assigned_group_name, $ticket->assigned_group_colour);
		}

		$ticket_details[$this->language->lang('ASSIGNED')] = $assigned_to === '' ? $this->language->lang('UNASSIGNED') : $assigned_to;

		// Show reporter's IP address to help with log files
		if (!empty($ticket->poster_ip) && $project->is_team_user())
		{
			$ticket_details[$this->language->lang('REPORTED_IP')] = $ticket->poster_ip;
		}

		$ticket_details[$this->language->lang('REPORTED_ON')] = $this->user->format_date($ticket->ticket_time);

		// Statuses
		$statuses = $tracker->get_statuses();

		foreach ($statuses as $tmp_status_id => $status_data)
		{
			$this->template->assign_block_vars('statuses', [
				'ID'	=> $tmp_status_id,
				'NAME'	=> $this->language->lang($status_data['status_name']),
			]);
		}

		// Severities
		$severities = $tracker->get_severities();

		foreach ($severities as $tmp_severity_id => $severity_data)
		{
			$this->template->assign_block_vars('severities', [
				'ID'	=> $tmp_severity_id,
				'NAME'	=> $this->language->lang($severity_data['severity_name']),
			]);
		}

		// Status description
		$status_description = null;

		if ($ticket->poster_id == $this->user->data['user_id'] && isset($statuses[$ticket->status_id]['status_desc']))
		{
			$status_description = $this->language->lang('STATUS_DESC', $this->language->lang($statuses[$ticket->status_id]['status_name']), $this->language->lang($statuses[$ticket->status_id]['status_desc']));
		}

		// Get all duplicates of this ticket
		foreach ($this->functions->get_duplicate_tickets($ticket->ticket_id) as $_ticket_id => $row)
		{
			$this->template->assign_block_vars('duplicates', [
				'ID'	=> $_ticket_id,
				'TITLE'	=> $row['ticket_title'],

				'U_VIEWTICKET'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $_ticket_id]),
			]);
		}

		// This ticket is a duplicate of another ticket
		if ($ticket->ticket_duplicate && $ticket->duplicate_id > 0)
		{
			// Get all duplicates of the duplicate of this ticket
			foreach ($this->functions->get_duplicate_tickets($ticket->duplicate_id) as $_ticket_id => $row)
			{
				$this->template->assign_block_vars('duplicates_other', [
					'ID'	=> $_ticket_id,
					'TITLE'	=> $row['ticket_title'],

					'U_VIEWTICKET'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $_ticket_id]),
				]);
			}

			$this->template->assign_var('DUPLICATE_ID', $ticket->duplicate_id);
		}

		// Get the total number of comments for this ticket
		$sql = 'SELECT COUNT(post_id) AS posts_total
			FROM ' . $this->table_prefix . 'trackers_posts
			WHERE ticket_id = ' . (int) $ticket->ticket_id;

		if (!$project->is_team_user())
		{
			// Do not show pagination to normal users if there are private comments
			$sql .= ' AND post_visibility = 0';
		}

		$result = $this->db->sql_query($sql);
		$posts_total = $this->db->sql_fetchfield('posts_total', 0, $result) - 1;
		$this->db->sql_freeresult($result);

		// Handle pagination
		$start = $pagination->validate_start($start, $this->config['posts_per_page'], $posts_total);
		$base_url = $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id]);
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $posts_total, $this->config['posts_per_page'], $start);

		// Grab the posts
		$sql = 'SELECT p.*, u.username, u.user_colour
			FROM (' . $this->table_prefix . 'trackers_posts p, ' . $this->table_prefix . 'users u)
			WHERE p.poster_id = u.user_id
				AND p.post_id <> ' . (int) $ticket->post_id . '
				AND p.ticket_id = ' . (int) $ticket_id;

		if (!$project->is_team_user())
		{
			$sql .= ' AND p.post_visibility = 0';
		}

		$sql .= ' GROUP BY p.post_id
			ORDER BY p.post_time';
		$posts_result = $this->db->sql_query_limit($sql, $this->config['posts_per_page'] + 1, $start);
		$posts = [];
		while ($post_data = $this->db->sql_fetchrow($posts_result))
		{
			$posts[] = $post_data;
		}
		$this->db->sql_freeresult($posts_result);

		$post_min_timestamp = 0;
		$post_max_timestamp = PHP_INT_MAX;

		if (count($posts))
		{
			$post_min_timestamp = $posts[0]['post_time'];
			$post_max_timestamp = $posts[count($posts) - 1]['post_time'];
		}

		if (count($posts) == $this->config['posts_per_page'] + 1)
		{
			array_pop($posts);
		}
		else
		{
			$post_max_timestamp = PHP_INT_MAX;
		}

		if (!$start)
		{
			$post_min_timestamp = 0;
		}

		$history_type = $project->is_team_user() ? 8 : 0;

		// Get the total number of history entries for this ticket
		$sql = 'SELECT COUNT(history_id) AS history_total
			FROM ' . $this->table_prefix . 'trackers_history
			WHERE ticket_id = ' . (int) $ticket_id . '
				AND history_type <= ' . (int) $history_type;
		$result = $this->db->sql_query($sql);
		$history_total = $this->db->sql_fetchfield('history_total', 0, $result);
		$this->db->sql_freeresult($result);

		// Get this ticket's history
		$sql = 'SELECT h.*, u.username, u.user_colour, r.rank_title
			FROM ' . $this->table_prefix . 'trackers_history h, ' . $this->table_prefix . 'users u
			LEFT JOIN ' . $this->table_prefix . 'ranks r
				ON r.rank_id = u.user_rank
			WHERE h.ticket_id = ' . (int) $ticket_id . '
				AND h.poster_id = u.user_id
				AND h.history_timestamp < ' . (int) $post_max_timestamp . '
				AND h.history_timestamp >= ' . (int) $post_min_timestamp . '
				AND h.history_type <= ' . (int) $history_type . '
			ORDER BY h.history_timestamp ASC';
		$history_result = $this->db->sql_query($sql);
		$history_entries = [];
		while ($history_data = $this->db->sql_fetchrow($history_result))
		{
			$history_entries[] = $history_data;
		}
		$this->db->sql_freeresult($history_result);

		$post_data = $history_data = false;

		while (count($posts) || count($history_entries) || $post_data || $history_data)
		{
			if (!$post_data)
			{
				$post_data = array_shift($posts);
			}

			if (!$history_data)
			{
				$history_data = array_shift($history_entries);
			}

			if (!$history_data || ($post_data && ($post_data['post_time'] < $history_data['history_timestamp'])))
			{
				$s_cannot_edit = !$this->auth->acl_get('u_trackers_edit') || $this->user->data['user_id'] != $post_data['poster_id'];
				$s_cannot_edit_locked = $ticket->ticket_locked && !$this->auth->acl_get('m_trackers_lock');

				$s_cannot_delete = $this->user->data['user_id'] != $post_data['poster_id'] || (
					!$this->auth->acl_get('u_trackers_delete')
				);
				$s_cannot_delete_locked = $ticket->ticket_locked;

				$edit_allowed = ($this->user->data['is_registered'] && ($this->auth->acl_get('m_trackers_edit') || (
					!$s_cannot_edit &&
					!$s_cannot_edit_locked
				)));

				$quote_allowed = $this->auth->acl_get('m_trackers_edit') || (!$ticket->ticket_locked &&
					($this->user->data['user_id'] == ANONYMOUS || $this->auth->acl_get('u_trackers_reply'))
				);

				$delete_allowed = ($this->user->data['is_registered'] && (
					$this->auth->acl_get('m_trackers_delete') ||
					(!$s_cannot_delete && !$s_cannot_delete_locked)
				));

				$post_data['bbcode_options'] = (($post_data['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
					(($post_data['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) +
					(($post_data['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);

				$post_text = generate_text_for_display($post_data['post_text'], $post_data['bbcode_uid'], $post_data['bbcode_bitfield'], $post_data['bbcode_options']);

				$this->template->assign_block_vars('ticket_posts', [
					'S_TYPE'	=> 'POST',
					'S_PRIVATE'	=> $post_data['post_visibility'],

					'U_EDIT'	=> ($edit_allowed) ? $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'posting', 'mode' => 'edit', 'post' => (int) $post_data['post_id']]) : '',
					'U_QUOTE'	=> ($quote_allowed) ? $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'posting', 'mode' => 'quote', 'post' => (int) $post_data['post_id']]) : '',
					'U_INFO'	=> ($this->auth->acl_get('m_trackers_info')) ? $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'mcp', 'i' => 'main', 'mode' => 'post_details', 'post' => (int) $post_data['post_id']]) : '',
					'U_DELETE'	=> ($delete_allowed) ? $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'posting', 'mode' => 'delete', 'post' => (int) $post_data['post_id']]) : '',

					'U_MINI_POST'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id, 'post' => (int) $post_data['post_id']]),

					'ID'				=> $post_data['post_id'],
					'TEXT'				=> $post_text,
					'POST_AUTHOR_FULL'	=> get_username_string('full', $post_data['poster_id'], $post_data['username'], $post_data['user_colour']),
					'POST_DATE'			=> $this->user->format_date($post_data['post_time']),
					'POST_DATE_RFC3339'	=> gmdate(DATE_RFC3339, $post_data['post_time']),
					'MINI_POST'			=> $this->language->lang('POST'),
				]);

				$post_data = false;
			}
			else
			{
				$this->template->assign_block_vars('ticket_posts', [
					'S_TYPE'	=> 'HISTORY',

					'ID'				=> $history_data['history_id'],
					'TEXT'				=> $history_data['history_text'],
					'POST_AUTHOR_FULL'	=> get_username_string('full', $history_data['poster_id'], $history_data['username'], $history_data['user_colour']),
					'USER_RANK'			=> $history_data['rank_title'],
					'POST_DATE'			=> $this->user->format_date($history_data['history_timestamp']),
					'POST_DATE_RFC3339'	=> gmdate(DATE_RFC3339, $history_data['history_timestamp']),
				]);

				$history_data = false;
			}
		}

		// TODO - ticket options (set status, set severity, etc)
		$viewticket_url = $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id]);

		$s_quickmod_action = $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'mcp', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id, 'start' => $start, 'quickmod' => 1, 'redirect' => urlencode(str_replace('&amp;', '&', $viewticket_url))]);

		$ticket_options = [];

		$sql = 'SELECT group_name
			FROM ' . $this->table_prefix . 'groups
			WHERE group_id = ' . (int) $this->user->data['group_id'];
		$result = $this->db->sql_query($sql, 3600);
		$group_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$ticket_options = [
			'lock'				=> ['LOCK_TICKET', !$ticket->ticket_locked && $this->auth->acl_get('m_trackers_lock')],
			'unlock'			=> ['UNLOCK_TICKET', $ticket->ticket_locked && $this->auth->acl_get('m_trackers_lock')],
			'delete_ticket'		=> ['DELETE_TICKET', $this->auth->acl_get('m_trackers_delete')],
			'move'				=> ['MOVE_TICKET', $this->auth->acl_get('m_trackers_move')],
			'set_reviewed'		=> ['SET_REVIEWED', $project->is_team_user()],
			'change_status'		=> ['CHANGE_STATUS', $project->is_team_user()],
			'change_severity'	=> ['CHANGE_SEVERITY', $project->is_team_user()],
			'assign'			=> ['ASSIGN', $project->is_team_user()],
			'assign_me'			=> ['ASSIGN_ME', ($ticket->assigned_user != (int) $this->user->data['user_id']) && $project->is_team_user()],
			// This option only checks primary group
			'assign_my_group'	=> [$this->language->lang('ASSIGN_MY_GROUP', $group_helper->get_name_string('group_name', (int) $this->user->data['group_id'], $group_data['group_name'])), ($ticket->assigned_group != $this->user->data['group_id'] && $project->is_team_group($this->user->data['group_id'])) && $project->is_team_user()],
		];

		foreach ($ticket_options as $option => $qm_ary)
		{
			if (!empty($qm_ary[1]))
			{
				phpbb_add_quickmod_option($s_quickmod_action, $option, $qm_ary[0]);
			}
		}

		// Assign global tpl variables
		$this->template->assign_vars([
			'S_ALLOW_SET_SEVERITY'	=> $this->functions->can_set_severity() || $project->is_team_user(),
			'S_CLOSED'				=> $ticket->ticket_closed,
			'S_TICKET_PRIVATE'		=> $ticket->ticket_visibility,
			'S_TICKET_OPTIONS'		=> (count($ticket_options) > 0) ? true : false,
			'S_TICKET_FIXED'		=> (!empty($statuses[$ticket->status_id]['ticket_fixed'])) ? true : false,
			'S_TICKET_LOCKED'		=> $ticket->ticket_locked,
			'S_PROJECT_TEAM'		=> $project->is_team_user(),
			'S_POST_REPLY'			=> $tracker->tracker_status && $this->auth->acl_get('u_trackers_reply') ? true : false,

			'U_POST_REPLY_TICKET'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'posting', 'mode' => 'reply', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id]),
			'U_VIEWTICKET'			=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id]),

			'TICKET_ID'		=> $ticket->ticket_id,
			'STATUS_ID'		=> $ticket->status_id,
			'SEVERITY_ID'	=> $ticket->severity_id,
			'TICKET_TITLE'	=> $ticket->ticket_title,
			'STATUS_NAME'	=> strtolower($ticket->status_name),
			'STATUS_DESC'	=> $status_description,
			'TICKET_DESC'	=> $ticket->get_description(),
			'TOTAL_POSTS'	=> $this->language->lang('TICKET_TOTAL_POSTS', $posts_total),
		]);

		/**
		 * Generate ticket details/options
		 */
		if (isset($ticket_details))
		{
			foreach ($ticket_details as $detail_name => $detail_value)
			{
				$this->template->assign_block_vars('ticket_details', [
					'NAME'	=> $detail_name,
					'VALUE'	=> $detail_value,
				]);
			}
		}

		$navlinks = [
			[
				'FORUM_NAME'	=> $this->language->lang($tracker->tracker_name),
				'U_VIEW_FORUM'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewtracker', 't' => (int) $tracker_id]),
			],
			[
				'FORUM_NAME'	=> $project->project_name,
				'U_VIEW_FORUM'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project_id]),
			],
			[
				'FORUM_NAME'	=> $ticket->ticket_title,
				'U_VIEW_FORUM'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id]),
			],
		];

		$this->functions->generate_navlinks($navlinks);

		return $this->helper->render('viewticket_body.html', $this->language->lang($tracker->tracker_name));
	}
}
