<?php
/**
 * Plugin Name: Plugin boilerplate
 * Plugin URI: https://github.com/bootpress-io/wp-plugin-boilerplate
 * Description: A Wordpress plugin boilerplate
 * Version: 0.0.0
 * Author: Your name
 * Author URI: http://example.com/
 * Text Domain: plugin-boilerplate
 * Domain Path: /languages
 * License: MIT
 */

// If this file is called directly, abort.
if (!defined('WPINC'))
{
    die;
}

//Define plugin constants

//Plugin version, used for autoatic updates and for cache-busting of style and script file references.
define('PLUGIN_BOILERPLATE_PLUGIN_VERSION', '0.1.0');

define('PLUGIN_BOILERPLATE_PLUGIN_NAME', untrailingslashit(plugin_basename(__FILE__)));
define('PLUGIN_BOILERPLATE_PLUGIN_SLUG', 'plugin-boilerplate');

define('PLUGIN_BOILERPLATE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_BOILERPLATE_PLUGIN_URL', plugins_url('', __FILE__) . '/');

if (!class_exists('Plugin_Boilerplate'))
{
    require_once( PLUGIN_BOILERPLATE_PLUGIN_PATH . 'includes/plugin.php' );
}

//Loads the plugin at the right hook(after all plugin files has been inlcuded by WordPress)
add_action('plugins_loaded', array('Plugin_Boilerplate', 'load_plugin'));

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook(__FILE__, array('Plugin_Boilerplate', 'activate'));

//Deletes all data if plugin deactivated
register_deactivation_hook(__FILE__, array('Plugin_Boilerplate', 'deactivate'));

function Plugin_Boilerplate()
{
    return Plugin_Boilerplate::get_instance();
}
