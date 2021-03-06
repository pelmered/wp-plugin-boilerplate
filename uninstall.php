<?php
/**
 * Fired when the plugin is uninstalled.
 *
* @package   wc_pricefiles
 * @author    Peter Elmered <peter@elmered.com>
 * @link      http://extendwp.com
 * @copyright 2013 Peter Elmered
  */

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//Cleanup all registerd options
require_once 'index.php';

delete_option(PLUGIN_BOILERPLATE_PLUGIN_SLUG.'_options');
