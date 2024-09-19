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
// use phpbbmodders\trackers\constants; NOT IN USE YET!

/**
 * Trackers
 */
class posting
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\auth\auth */
	protected $auth;

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
	public function __construct(ContainerInterface $container, \phpbb\auth\auth $auth, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $root_path, $php_ext, $table_prefix)
	{
		$this->container = $container;
		$this->auth = $auth;
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
		if (!class_exists('parse_message'))
		{
			include($this->root_path . 'includes/message_parser.' . $this->php_ext);
		}

		$tracker_id = $this->request->variable('t', 0);
		$project_id = $this->request->variable('p', 0);
		$ticket_id = $this->request->variable('ticket', 0);

		$this->language->add_lang('posting', 'phpbbmodders/trackers');

		$tracker = $this->tracker->load($tracker_id);
		$project = $this->project->load($project_id);
		$ticket = $this->ticket->load($ticket_id);

		$preview = $this->request->is_set_post('preview') ? true : false;
		$cancel = $this->request->is_set_post('cancel') ? true : false;
		$submit = $this->request->is_set_post('post') && !$preview;

		$mode = $this->request->variable('mode', '');

		switch ($mode)
		{
			case 'post':
				if (!$project_id)
				{
					trigger_error('NO_PROJECT');
				}
			break;

			case 'reply':
				if ($ticket_id)
				{
					$sql = 'SELECT project_id
						FROM ' . $this->table_prefix . "trackers_tickets
						WHERE ticket_id = $ticket_id";
					$result = $this->db->sql_query($sql);
					$project_id = (int) $this->db->sql_fetchfield('project_id');
					$this->db->sql_freeresult($result);
				}

				if (!$ticket_id || !$project_id)
				{
					throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_TICKET'));
				}
			break;

			case 'edit':
			case 'delete':
			case 'quote':
				$post_id = $this->request->variable('post', 0);

				if ($post_id)
				{
					$sql = 'SELECT t.ticket_id
						FROM ' . $this->table_prefix . 'trackers_tickets t, ' . $this->table_prefix . 'trackers_posts post
						WHERE post.post_id = ' . $post_id . '
							AND t.ticket_id = post.ticket_id';
					$result = $this->db->sql_query($sql);
					$ticket_id = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);
				}

				if (!$post_id || !$ticket_id)
				{
					throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_POST'));
				}
			break;
		}

		$error = $post_data = [];
		$current_time = time();

		// Was cancel pressed? If so then redirect to the appropriate page
		if ($cancel)
		{
			$redirect = $post_id ? $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id, 'post' => (int) $post_id] . '#p' . $post_id) : $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id]);
			redirect($redirect);
		}

		// Grab basic information for all modes
		switch ($mode)
		{
			case 'post':
				$sql = 'SELECT *
					FROM ' . $this->table_prefix . "trackers_projects
					WHERE project_id = $project_id";
			break;

			case 'reply':
				$sql = 'SELECT p.*, t.*
					FROM ' . $this->table_prefix . 'trackers_tickets t, ' . $this->table_prefix . "trackers_projects p
					WHERE t.ticket_id = $ticket_id
						AND p.project_id = t.project_id";
//						AND " . $phpbb_content_visibility->get_visibility_sql('topic', $forum_id, 't.');
			break;

			case 'quote':
			case 'edit':
			case 'delete':
				$sql = 'SELECT p.*, t.*, post.*
					FROM ' . $this->table_prefix . 'trackers_posts post, ' . $this->table_prefix . 'trackers_tickets t, ' . $this->table_prefix . "trackers_projects p
					WHERE post.post_id = $post_id
						AND t.ticket_id = post.ticket_id
						AND p.ticket_id = t.ticket_id"
//						AND " . $phpbb_content_visibility->get_visibility_sql('post', $forum_id, 'p.');
			break;

			default:
				$sql = '';
			break;
		}

		if (!$sql)
		{
			throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_POST_MODE'));
		}

		$result = $db->sql_query($sql);
		$post_data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$post_data)
		{
			throw new \phpbb\exception\http_exception(404, ($mode == 'post' || $mode == 'reply') ? $this->language->lang('NO_TICKET') : $this->language->lang('NO_POST'));
		}

		/**
		 * TODO: More code
		 */

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

		return $this->helper->render('posting_body.html', $this->language->lang($tracker->tracker_name));
	}
}
