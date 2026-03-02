<?php
/**
 * Frontend functionality for the Ideal Mega Menu.
 *
 * @package Ideal_Mega_Menu
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Mega Menu Frontend class.
 */
class Ideal_Mega_Menu_Frontend {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_filter( 'wp_nav_menu_args', array( $this, 'modify_nav_menu_args' ) );
        add_filter( 'nav_menu_css_class', array( $this, 'add_menu_item_classes' ), 10, 4 );
        add_action( 'wp_footer', array( $this, 'output_mobile_toggle' ) );
    }

    /**
     * Enqueue frontend assets.
     */
    public function enqueue_assets() {
        wp_enqueue_style(
            'ideal-mega-menu',
            IMM_PLUGIN_URL . 'assets/css/mega-menu.css',
            array(),
            IMM_VERSION
        );

        wp_enqueue_script(
            'ideal-mega-menu',
            IMM_PLUGIN_URL . 'assets/js/mega-menu.js',
            array(),
            IMM_VERSION,
            true
        );

        // Pass settings to JavaScript.
        $settings = Ideal_Mega_Menu::get_setting();
        wp_localize_script(
            'ideal-mega-menu',
            'idealMegaMenu',
            array(
                'animation'       => isset( $settings['animation'] ) ? $settings['animation'] : 'fade',
                'animationSpeed'  => isset( $settings['animation_speed'] ) ? intval( $settings['animation_speed'] ) : 300,
                'trigger'         => isset( $settings['trigger'] ) ? $settings['trigger'] : 'hover',
                'mobileBreakpoint' => isset( $settings['mobile_breakpoint'] ) ? intval( $settings['mobile_breakpoint'] ) : 768,
                'menuStyle'       => isset( $settings['menu_style'] ) ? $settings['menu_style'] : 'default',
            )
        );
    }

    /**
     * Modify wp_nav_menu arguments to use our custom walker.
     *
     * @param array $args wp_nav_menu arguments.
     * @return array Modified arguments.
     */
    public function modify_nav_menu_args( $args ) {
        // Only replace walker if a theme location is assigned and we haven't already set one.
        if ( ! empty( $args['theme_location'] ) && ! ( $args['walker'] instanceof Ideal_Mega_Menu_Walker ) ) {
            // Check if this menu location has mega menu enabled.
            $enabled_locations = Ideal_Mega_Menu::get_setting( 'enabled_locations', array() );

            // Apply to all menus by default, or only selected ones.
            $args['walker']    = new Ideal_Mega_Menu_Walker();
            $args['container'] = 'nav';
            $args['container_class'] = 'imm-mega-menu-container imm-style-' . sanitize_html_class( Ideal_Mega_Menu::get_setting( 'menu_style', 'default' ) );
            $args['menu_class'] = isset( $args['menu_class'] ) ? $args['menu_class'] . ' imm-mega-menu' : 'imm-mega-menu';
        }

        return $args;
    }

    /**
     * Add custom CSS classes to menu items.
     *
     * @param array    $classes CSS classes applied to the menu item.
     * @param WP_Post  $item   The current menu item.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     * @param int      $depth  Depth of menu item.
     * @return array Modified CSS classes.
     */
    public function add_menu_item_classes( $classes, $item, $args, $depth ) {
        if ( isset( $args->walker ) && $args->walker instanceof Ideal_Mega_Menu_Walker ) {
            $classes[] = 'imm-menu-item';
            $classes[] = 'imm-depth-' . $depth;
        }
        return $classes;
    }

    /**
     * Output mobile menu toggle button.
     */
    public function output_mobile_toggle() {
        ?>
        <script>
            (function() {
                var containers = document.querySelectorAll('.imm-mega-menu-container');
                containers.forEach(function(container) {
                    if (!container.querySelector('.imm-mobile-toggle')) {
                        var toggle = document.createElement('button');
                        toggle.className = 'imm-mobile-toggle';
                        toggle.setAttribute('aria-label', 'Toggle Menu');
                        toggle.setAttribute('aria-expanded', 'false');
                        toggle.innerHTML = '<span class="imm-hamburger"><span></span><span></span><span></span></span>';
                        container.insertBefore(toggle, container.firstChild);
                    }
                });
            })();
        </script>
        <?php
    }
}
