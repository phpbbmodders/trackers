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

	/** @var \phpbb\template\template */
	protected $template;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface        $container
	 * @param \phpbb\auth\auth          $auth
	 * @param \phpbb\template\template  $template
	 */
	public function __construct(ContainerInterface $container, \phpbb\auth\auth $auth, \phpbb\template\template $template)
	{
		$this->container = $container;
		$this->auth = $auth;
		$this->template = $template;

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
