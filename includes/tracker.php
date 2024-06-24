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

class tracker
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface        $container
	 * @param \phpbb\language\language  $language
	 * @param \phpbb\user               $user
	 */
	public function __construct(ContainerInterface $container, \phpbb\language\language $language, \phpbb\user $user, $object_data = [])
	{
		$this->container = $container;
		$this->language	= $language;
		$this->user = $user;

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
		return new tracker($this->container, $this->language, $this->user, $object_data);
	}

	/**
	 * Load a specific tracker
	 */
	public function load($tracker_id)
	{
		// Determine which tracker the user requested
		$tracker_data = $this->tracker_cache->get_tracker_data($tracker_id);

		if (!$tracker_data)
		{
			return null;
		}

		return $this->base_vars($tracker_data);
	}

	/**
	 * Get this tracker's available projects
	 */
	public function get_projects($check_team_status = true, $team_user_id = 0)
	{
		$projects = [];
		$available_projects = $this->tracker_cache->get_projects();

		foreach ($available_projects as $project_data)
		{
			if (!isset($projects[$project_data['project_id']]) && $project_data['tracker_id'] == $this->tracker_id)
			{
				$project = $this->project->base_vars($project_data);
				$project_data['team_user'] = $project->is_team_user($team_user_id);
				unset($project);

				if ($check_team_status && $project_data['project_visibility'] == constants::ITEM_PRIVATE && !$project_data['team_user'])
				{
					continue;
				}

				$projects[$project_data['project_id']] = $project_data;
			}
		}

		return $projects;
	}

	/**
	 * Check if the given user is a team member in one of the projects listed in this tracker
	 */
	public function is_team_user($user_id = 0)
	{
		$user_id = (empty($user_id)) ? (int) $this->user->data['user_id'] : (int) $user_id;

		$projects = $this->get_projects(false, $user_id);

		foreach ($projects as $project_data)
		{
			if ($project_data['team_user'])
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Add a new project
	 */
	public function add_project($project_name, $project_visibility = constants::ITEM_PUBLIC)
	{
		return $this->project->create($this->tracker_id, $project_name, $project_visibility);
	}

	/**
	 * Get the status_id that belongs to a new ticket
	 */
	public function get_new_status()
	{
		$statuses = $this->tracker_cache->get_statuses($this->tracker_id);

		foreach ($statuses as $status_id => $status_data)
		{
			if ($status_data['ticket_new'])
			{
				return $status_id;
			}
		}

		throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_NEW_STATUS'));
	}

	/**
	 * Get the status_id that belongs to a reviewed ticket
	 */
	public function get_reviewed_status()
	{
		$statuses = $this->tracker_cache->get_statuses($this->tracker_id);

		foreach ($statuses as $status_id => $status_data)
		{
			if ($status_data['ticket_reviewed'])
			{
				return $status_id;
			}
		}

		throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_REVIEWED_STATUS'));
	}

	/**
	 * Get the available ticket statuses for this tracker
	 */
	public function get_statuses()
	{
		return $this->tracker_cache->get_statuses($this->tracker_id);
	}

	/**
	 * Get the available severities for this tracker
	 */
	public function get_severities()
	{
		return $this->tracker_cache->get_severities($this->tracker_id);
	}
}
