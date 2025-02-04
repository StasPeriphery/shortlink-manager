<?php
/**
 * Plugin Name: Shortlink Manager
 * Description:
 * Version: 0.0
 * Author: Barsik
 */

require_once 'vendor/autoload.php';


use ShortLinkManager\{SupportFunctions, Admin\AdminMenu};


if (!defined('ABSPATH')) {
    exit;
}


/**
 * Create new object of AdminMenu
 */

$admin_menu = new AdminMenu();
add_action('admin_menu', [$admin_menu, 'add_shortlink_manager_menu']);


$support_func = new SupportFunctions();








