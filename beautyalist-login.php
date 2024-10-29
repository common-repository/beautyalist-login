<?php
/**
 * Plugin Name: BeautyAList Login
 * Description: Sell directly to verified, licensed beauty professionals.
 * Author: beauticianlist
 * License: GPLv3
 * Version: 3.1.7
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/vendor/minininc/beautyalist-sdk-openid/init.php';

use BeautyAListLogin\Controllers\Settings;
use BeautyAListLogin\Controllers\Front;

( new Settings );
( new Front );