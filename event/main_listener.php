<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\trackers\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Trackers event listener
 */
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\language\language           $language
	 * @param \phpbb\controller\helper           $helper
	 * @param \phpbb\template\template           $template
	 * @param string                             $root_path
	 * @param string                             $php_ext
	 * @param string                             $table_prefix
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\template\template $template, $root_path, $php_ext, $table_prefix)
	{
		$this->db = $db;
		$this->language = $language;
		$this->helper = $helper;
		$this->template = $template;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.user_setup'	=> 'user_setup',
			'core.page_header'	=> 'page_header',

			'core.viewonline_overwrite_location'	=> 'viewonline_page',

			'core.permissions'	=> 'add_permissions',
		];
	}

	/**
	 * Load common language files
	 */
	public function user_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'phpbbmodders/trackers',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Add a link to the controller in the forum navbar
	 */
	public function page_header()
	{
		$sql = 'SELECT tracker_id, tracker_name
			FROM ' . $this->table_prefix . 'trackers_trackers';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('trackers', [
				'TRACKER_NAME'	=> $this->language->lang($row['tracker_name']),
				'U_VIEWTRACKER'	=> $this->helper->route('phpbbmodders_trackers_controller', ['page' => 'viewtracker', 't' => (int) $row['tracker_id']]),
			]);
		}
	}

	/**
	 * Show users viewing Trackers page on the Who Is Online page
	 */
	public function viewonline_page($event)
	{
		if ($event['on_page'][1] === 'app' && strrpos($event['row']['session_page'], 'app.' . $this->php_ext . '/trackers') === 0)
		{
			$event['location'] = $this->language->lang('VIEWING_TRACKERS');
		}
	}

	/**
	 * Add permissions to the ACP -> Permissions settings page
	 * This is where permissions are assigned language keys and
	 * categories (where they will appear in the Permissions table):
	 * actions|content|forums|misc|permissions|pm|polls|post
	 * post_actions|posting|profile|settings|topic_actions|user_group
	 */
	public function add_permissions($event)
	{
		$event->update_subarray('categories', 'trackers', 'ACL_CAT_TRACKERS');

		$permissions = [
			'a_trackers_manage'	=> ['lang' => 'ACL_A_TRACKERS_MANAGE', 'cat' => 'trackers'],
		];

		foreach ($permissions as $key => $value)
		{
			$event->update_subarray('permissions', $key, $value);
		}
	}
}
