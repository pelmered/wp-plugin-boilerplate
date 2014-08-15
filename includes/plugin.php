<?php

/**
 * Plugin Boilerplate
 * Plugin main class.
 *
 * @package   plugin-boilerplate
 * @author    Your name <name@domain.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 */
class Plugin_Boilerplate
{

    /**
     * Instance of this class.
     *
     * @since    0.1.0
     * @var      object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin by setting localization, filters, and administration functions.
     * 
     * @since    0.1.0
     */
    private function __construct()
    {
        add_action('init', array($this, 'init'), 2);

        // Load plugin text domain
        add_action('init', array($this, 'load_plugin_textdomain'));
    }

    /**
     * Return an instance of this class.
     *
     * @since     0.1.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance)
        {
            self::$instance = new self;
        }

        return self::$instance;
    }

    function check_dependencies()
    {
        //Needed for is_plugin_active() call
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        if (is_plugin_active('woocommerce/woocommerce.php'))
        {
            require_once( PLUGIN_BOILERPLATE_PLUGIN_PATH . 'includes/pricefiles.php' );

            return true;
        } else
        {
            add_action('admin_notices', array($this, 'dependencies_not_met_notice'));
        }

        return false;
    }

    function load_plugin()
    {
        if ($this->check_dependecies())
        {
            return $this->get_instance();
        } else
        {
            return false;
        }
    }

    function dependencies_not_met_notice()
    {
        
    }

    /**
     * Initialize the plugin by setting localization, filters, and administration functions.
     * 
     * @since    0.1.0
     */
    private function init()
    {
        // Load admin style sheet and JavaScript.
        add_action('enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('enqueue_scripts', array($this, 'enqueue_scripts'));
        
        if (is_admin())
        {
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

            add_action('admin_notices', array($this, 'notices'));
            
            // Add the options page and menu item.
            add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
            
            // Load admin style sheet and JavaScript.
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            
            //Include and initialize admin options
            require_once( WP_PRICEFILES_PLUGIN_PATH . 'includes/admin/options.php' );
            new Plugin_Boilerplate_Admin_Options();
        }
    }
    
    
    /**
     * Display notice in admin if not configured
     *
     * @access public
     * @return void
     */
    public function notices()
    {
        if (!get_option(PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options', FALSE))
        {
            ?>
            <div class="updated fade">
                <p><?php printf(__('The plugin needs to be configured to work. Configure it <a href="%s">here</a>', PLUGIN_BOILERPLATE_PLUGIN_SLUG), admin_url('admin.php?page=' . PLUGIN_BOILERPLATE_PLUGIN_SLUG)); ?></p>
            </div>
            <?php
        }
    }
    
    /**
     * Load the plugin text domain for translation.
     *
     * @since    0.1.0
     */
    public function load_plugin_textdomain()
    {
        $domain = $this->plugin_slug;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, FALSE, basename(dirname(__FILE__)) . '/languages');
    }

    /**
     * Fired when the plugin is activated.
     *
     * @since    0.1.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
     */
    public function activate($network_wide)
    {
        if (function_exists('is_multisite') && is_multisite())
        {
            if ($network_wide)
            {
                // Get all blog ids
                $blog_ids = $this->get_blog_ids();

                foreach ($blog_ids as $blog_id)
                {
                    switch_to_blog($blog_id);
                    $this->single_activate();
                }
                restore_current_blog();
            } else
            {
                $this->single_activate();
            }
        } else
        {
            $this->single_activate();
        }
    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since    0.1.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
     */
    public function deactivate($network_wide)
    {
        if (function_exists('is_multisite') && is_multisite())
        {
            if ($network_wide)
            {
                // Get all blog ids
                $blog_ids = $this->get_blog_ids();

                foreach ($blog_ids as $blog_id)
                {
                    switch_to_blog($blog_id);
                    $this->single_deactivate();
                }
                restore_current_blog();
            } else
            {
                $this->single_deactivate();
            }
        } else
        {
            $this->single_deactivate();
        }
    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @since    0.1.0
     *
     * @param	int	$blog_id ID of the new blog.
     */
    public function activate_new_site($blog_id)
    {
        if (1 !== did_action('wpmu_new_blog'))
        {
            return;
        }

        switch_to_blog($blog_id);
        $this->single_activate();
        restore_current_blog();
        
        //Code for setting up the plugin for a new site in a multisite installation
        
    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    0.1.0
     *
     * @return	array|false	The blog ids, false if no matches.
     */
    private function get_blog_ids()
    {
        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
                    WHERE archived = '0' AND spam = '0'
                    AND deleted = '0'";
        return $wpdb->get_col($sql);
    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    0.1.0
     */
    private function single_activate()
    {
        $this->init();

        //Code for setting up the plugin when it's activated
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    0.1.0
     */
    private function single_deactivate()
    {
        
        
        
        //Code for deactivating up the plugin
        //Typically any options and temporary data should be removed. 
        //Options can be saved and can be deleted by the unstall script /uninstall.php
    }

    /**
     * action_links function.
     *
     * @access public
     * @param mixed $links
     * @return void
     */
    public function action_links($links)
    {
        $plugin_links = array(
            '<a href="' . admin_url('admin.php?page=' . PLUGIN_BOILERPLATE_PLUGIN_SLUG) . '">' . __('Settings', PLUGIN_BOILERPLATE_PLUGIN_SLUG) . '</a>',
            //'<a href="URL">' . __( 'Docs', PLUGIN_BOILERPLATE_PLUGIN_SLUG ) . '</a>',
            '<a href="' . PLUGIN_BOILERPLATE_PLUGIN_WEBSITE_URL . '">' . __('Info & Support', PLUGIN_BOILERPLATE_PLUGIN_SLUG) . '</a>',
        );

        return array_merge($plugin_links, $links);
    }

}
