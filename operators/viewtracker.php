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
class viewtracker
{
	/** @var ContainerInterface */
	protected $container;

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

	/**
	 * Constructor
	 *
	 * @param ContainerInterface        $container
	 * @param \phpbb\language\language  $language
	 * @param \phpbb\controller\helper  $helper
	 * @param \phpbb\request\request    $request
	 * @param \phpbb\template\template  $template
	 * @param \phpbb\user               $user
	 */
	public function __construct(ContainerInterface $container, \phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->container = $container;
		$this->language = $language;
		$this->helper = $helper;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;

		$this->functions = $this->container->get('phpbbmodders.trackers.functions');

		$this->tracker = $this->container->get('phpbbmodders.trackers.tracker');
		$this->project = $this->container->get('phpbbmodders.trackers.project');
	}

	public function display()
	{
		$tracker_id = $this->request->variable('t', 0);

		$this->language->add_lang('viewtracker', 'phpbbmodders/trackers');

		$tracker = $this->tracker->load($tracker_id);

		if ($tracker->tracker_visibility == constants::ITEM_PRIVATE && $this->user->data['user_id'] == ANONYMOUS)
		{
			login_box('', $this->language->lang('LOGIN_REQUIRED'));
		}

		$my_tickets_link = false;

		$projects = $tracker->get_projects();

		if (count($projects) == 0)
		{
			$this->template->assign_var('S_NO_PROJECTS', true);
		}
		else
		{
			foreach ($projects as $project_data)
			{
				$project = $this->project->load($project_data['project_id']);

				if ($project->project_status == constants::ITEM_ACTIVE)
				{
					if ($project->is_team_user())
					{
						$my_tickets_link = true;
					}

					$this->template->assign_block_vars('projects', [
						'U_VIEWPROJECT'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project->project_id]),

						'NAME'			=> $project->project_name,
						'DESCRIPTION'	=> $project->project_desc,
					]);
				}
			}
		}

		$this->template->assign_vars([
			'S_TRACKER_PRIVATE' => $tracker->tracker_visibility == constants::ITEM_PRIVATE ? true : false,
			'S_MY_TICKETS'		=> $my_tickets_link,

			'U_TRACKER_SEARCH'				=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'search', 't' => (int) $tracker_id]),
			'U_TRACKER_MY_TICKETS'			=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'search', 't' => (int) $tracker_id, 'assigned_user' => (int) $this->user->data['user_id']]),
			'U_TRACKER_MY_TICKETS_GROUPS'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'search', 't' => (int) $tracker_id, 'assigned_user' => (int) $this->user->data['user_id'], 'include_groups' => 1]),
			'U_STATISTICS'					=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'statistics', 't' => (int) $tracker_id]),

			'TRACKER_NAME'	=> $this->language->lang($tracker->tracker_name),
		]);

		$navlinks = [
			[
				'FORUM_NAME'	=> $this->language->lang($tracker->tracker_name),
				'U_VIEW_FORUM'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewtracker', 't' => (int) $tracker_id]),
			],
		];

		$this->functions->generate_navlinks($navlinks);

		return $this->helper->render('viewtracker_body.html', $this->language->lang($tracker->tracker_name));
	}
}
