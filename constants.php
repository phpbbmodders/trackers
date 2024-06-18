<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\trackers;

class constants
{
	// Visibility
	const ITEM_PUBLIC = 0;
	const ITEM_PRIVATE = 1;

	// Status
	const ITEM_INACTIVE = 0;
	const ITEM_ACTIVE = 1;

	// Ticket status
	const STATUS_OPEN = 0;
	const STATUS_ALL = -1;
	const STATUS_CLOSED = -2;
}
