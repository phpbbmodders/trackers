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

	/** @var \phpbb\template\template */
	protected $template;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface        $container
	 * @param \phpbb\template\template  $template
	 */
	public function __construct(ContainerInterface $container, \phpbb\template\template $template)
	{
		$this->container = $container;
		$this->template = $template;
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
