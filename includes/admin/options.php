<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Plugin_Boilerplate_Admin_Options
{
    function __construct()
    {
        $this->plugin_options = get_option(PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options', $this::get_default_options());

        add_action('admin_menu', array($this, 'add_plugin_menu'));

        add_action('admin_enqueue_scripts', array($this, 'admin_options_styles'));

        add_action('admin_init', array($this, 'initialize_pricefile_options'));
    }

    function admin_options_styles()
    {
        wp_enqueue_style( PLUGIN_BOILERPLATE_PLUGIN_SLUG.'-admin-option-styles', PLUGIN_BOILERPLATE_PLUGIN_URL . 'assets/css/admin-options.css', '', '');
    }

    /**
     * 
     */
    function add_plugin_menu()
    {
        add_submenu_page(
                'woocommerce', __('Pricefiles', PLUGIN_BOILERPLATE_PLUGIN_SLUG), __('Pricefiles', PLUGIN_BOILERPLATE_PLUGIN_SLUG), 'manage_woocommerce', PLUGIN_BOILERPLATE_PLUGIN_SLUG, array($this, 'display_settings_page')
        );
    }

    /**
     * Renders a simple page to display for the theme menu defined above.
     */
    function display_settings_page()
    {
        ?>
        <div class="section panel">
            <h1><?php _e('Plugin Boilerplate Options', PLUGIN_BOILERPLATE_PLUGIN_SLUG); ?></h1>
            
            <?php settings_errors(); ?>
            <form method="post" enctype="multipart/form-data" action="options.php">
                <?php 
                    settings_fields(PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options');
                    do_settings_sections(PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options_section');

                    $this->submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Provides default values for the Display Options.
     */
    static function get_default_options()
    {
        $defaults = array(
            'demo_option' => 'test'
        );

        return apply_filters(PLUGIN_BOILERPLATE_PLUGIN_SLUG.'_default_options', $defaults);
    }

    /* ------------------------------------------------------------------------ *
     * Setting Registration
     * ------------------------------------------------------------------------ */

    function initialize_pricefile_options()
    {
        register_setting(
                PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options', PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options', array($this, 'validate_input')
        );

        /*
         * Options page
         */
        // First, we register a section. This is necessary since all future options must belong to a 
        add_settings_section(
                $this->plugin_slug . '_options', // ID used to identify this section and with which to register options
                __('Plugin Boilerplate Options', PLUGIN_BOILERPLATE_PLUGIN_SLUG), // Title to be displayed on the administration page
                array($this, 'display_options_section_callback'), // Callback used to render the description of the section
                PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options_section' // Page on which to add this section of options
        );
        

        // Next, we'll introduce the fields for toggling the visibility of content elements.
        add_settings_field(
                'demo_option', // ID used to identify the field
                __('Demo option', PLUGIN_BOILERPLATE_PLUGIN_SLUG), // The label to the left of the option
                array($this, 'demo_option_callback'), // The name of the function responsible for rendering the option fields
                PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options_section', // The page on which this option will be displayed
                PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options', // The name of the section to which this field belongs
                array(// The array of arguments to pass to the callback. In this case, just a description.
                    __('.', PLUGIN_BOILERPLATE_PLUGIN_SLUG),
                )
        );
        add_settings_field(
                'demo_checkbox_option',
                __('Demo checkbox option', PLUGIN_BOILERPLATE_PLUGIN_SLUG),
                array($this, 'demo_checkbox_option_callback'),
                PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options_section',
                PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options',
                array(
                    __('.', PLUGIN_BOILERPLATE_PLUGIN_SLUG),
                )
        );
        
        //Add more fields

    }

    /* ------------------------------------------------------------------------ *
     * Section Callbacks
     * ------------------------------------------------------------------------ */

    function display_options_section_callback()
    {
        echo 'Demo section description';
    }

    /* ------------------------------------------------------------------------ *
     * Field Callbacks
     * ------------------------------------------------------------------------ */

    function demo_option_callback($args)
    {
        $demo_option = $this->plugin_options['demo_option'];

        echo '<label class="shipping-method"> ';
        echo '<input type="text" name="' . PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options[demo_option]" id="' . PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options_demo_option" value="'.$demo_option.'" ' . ($demo_option == 1 ? 'checked="checked"' : '') . '/>';
        echo '<span>' . __('Use debug mode') . '</span>';
        echo '</label>';
    }
    function demo_checkbox_option_callback($args)
    {
        $demo_checkbox_option = $this->plugin_options['demo_checkbox_option'];

        echo '<label class="shipping-method"> ';
        echo '<input type="checkbox" name="' . PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options[demo_checkbox_option]" id="' . PLUGIN_BOILERPLATE_PLUGIN_SLUG . '_options_demo_option" value="1" ' . ($demo_checkbox_option == 1 ? 'checked="checked"' : '') . '/>';
        echo '<span>' . __('Use debug mode') . '</span>';
        echo '</label>';
    }

    /* ------------------------------------------------------------------------ *
     * Setting Callbacks
     * ------------------------------------------------------------------------ */

    function validate_input($input)
    {
        if (!is_array($input))
            return false;

        $output = $input;

        //Apply filter_input on all values
        array_walk_recursive($output, array($this, 'filter_input'));

        // Return the array processing any additional functions filtered by this action
        return apply_filters($this->plugin_slug . '_validate_input', $output, $input);
    }

    function filter_input(&$input)
    {
        $input = strip_tags(stripslashes($input));
    }

}
