<?php
/**
 * Plugin Name: Ideal Mega Menu
 * Plugin URI: https://github.com/hostway254/ideal-mega-menu
 * Description: A powerful and customizable mega menu plugin for WordPress. Create beautiful, responsive mega menus with multi-column layouts, widgets, and icons.
 * Version: 1.0.0
 * Author: Hostway254
 * Author URI: https://github.com/hostway254
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: ideal-mega-menu
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin constants.
 */
define( 'IMM_VERSION', '1.0.0' );
define( 'IMM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IMM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'IMM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Include required files.
 */
require_once IMM_PLUGIN_DIR . 'includes/class-mega-menu-walker.php';
require_once IMM_PLUGIN_DIR . 'includes/class-mega-menu-admin.php';
require_once IMM_PLUGIN_DIR . 'includes/class-mega-menu-frontend.php';

/**
 * Main plugin class.
 */
class Ideal_Mega_Menu {

    /**
     * Single instance of the class.
     *
     * @var Ideal_Mega_Menu|null
     */
    private static $instance = null;

    /**
     * Admin instance.
     *
     * @var Ideal_Mega_Menu_Admin
     */
    public $admin;

    /**
     * Frontend instance.
     *
     * @var Ideal_Mega_Menu_Frontend
     */
    public $frontend;

    /**
     * Get single instance of the class.
     *
     * @return Ideal_Mega_Menu
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->init_hooks();
        $this->admin    = new Ideal_Mega_Menu_Admin();
        $this->frontend = new Ideal_Mega_Menu_Frontend();
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'load_textdomain' ) );
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'ideal-mega-menu',
            false,
            dirname( IMM_PLUGIN_BASENAME ) . '/languages'
        );
    }

    /**
     * Plugin activation.
     */
    public function activate() {
        $defaults = array(
            'enabled_locations' => array(),
            'menu_style'        => 'default',
            'animation'         => 'fade',
            'animation_speed'   => 300,
            'trigger'           => 'hover',
            'mobile_breakpoint' => 768,
            'enable_icons'      => true,
            'enable_images'     => true,
        );

        if ( false === get_option( 'ideal_mega_menu_settings' ) ) {
            add_option( 'ideal_mega_menu_settings', $defaults );
        }
    }

    /**
     * Plugin deactivation.
     */
    public function deactivate() {
        // Clean up if needed.
    }

    /**
     * Get plugin settings.
     *
     * @param string $key     Optional. Setting key.
     * @param mixed  $default Optional. Default value.
     * @return mixed
     */
    public static function get_setting( $key = '', $default = '' ) {
        $settings = get_option( 'ideal_mega_menu_settings', array() );

        if ( empty( $key ) ) {
            return $settings;
        }

        return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
    }
}

/**
 * Initialize the plugin.
 *
 * @return Ideal_Mega_Menu
 */
function ideal_mega_menu() {
    return Ideal_Mega_Menu::get_instance();
}

// Initialize.
ideal_mega_menu();
