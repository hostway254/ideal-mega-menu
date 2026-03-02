<?php
/**
 * Admin functionality for the Ideal Mega Menu.
 *
 * @package Ideal_Mega_Menu
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Mega Menu Admin class.
 */
class Ideal_Mega_Menu_Admin {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Menu item custom fields.
        add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'menu_item_custom_fields' ), 10, 5 );
        add_action( 'wp_update_nav_menu_item', array( $this, 'save_menu_item_custom_fields' ), 10, 3 );

        // Register mega menu widget areas.
        add_action( 'widgets_init', array( $this, 'register_widget_areas' ) );
    }

    /**
     * Add admin menu page.
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Ideal Mega Menu', 'ideal-mega-menu' ),
            __( 'Mega Menu', 'ideal-mega-menu' ),
            'manage_options',
            'ideal-mega-menu',
            array( $this, 'render_settings_page' ),
            'dashicons-menu-alt3',
            61
        );
    }

    /**
     * Register plugin settings.
     */
    public function register_settings() {
        register_setting(
            'ideal_mega_menu_settings_group',
            'ideal_mega_menu_settings',
            array( $this, 'sanitize_settings' )
        );

        add_settings_section(
            'imm_general_section',
            __( 'General Settings', 'ideal-mega-menu' ),
            array( $this, 'render_general_section' ),
            'ideal-mega-menu'
        );

        add_settings_field(
            'menu_style',
            __( 'Menu Style', 'ideal-mega-menu' ),
            array( $this, 'render_menu_style_field' ),
            'ideal-mega-menu',
            'imm_general_section'
        );

        add_settings_field(
            'animation',
            __( 'Animation', 'ideal-mega-menu' ),
            array( $this, 'render_animation_field' ),
            'ideal-mega-menu',
            'imm_general_section'
        );

        add_settings_field(
            'animation_speed',
            __( 'Animation Speed (ms)', 'ideal-mega-menu' ),
            array( $this, 'render_animation_speed_field' ),
            'ideal-mega-menu',
            'imm_general_section'
        );

        add_settings_field(
            'trigger',
            __( 'Trigger Event', 'ideal-mega-menu' ),
            array( $this, 'render_trigger_field' ),
            'ideal-mega-menu',
            'imm_general_section'
        );

        add_settings_field(
            'mobile_breakpoint',
            __( 'Mobile Breakpoint (px)', 'ideal-mega-menu' ),
            array( $this, 'render_mobile_breakpoint_field' ),
            'ideal-mega-menu',
            'imm_general_section'
        );
    }

    /**
     * Sanitize settings.
     *
     * @param array $input Raw settings input.
     * @return array Sanitized settings.
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();

        $sanitized['menu_style'] = isset( $input['menu_style'] )
            ? sanitize_text_field( $input['menu_style'] )
            : 'default';

        $sanitized['animation'] = isset( $input['animation'] )
            ? sanitize_text_field( $input['animation'] )
            : 'fade';

        $sanitized['animation_speed'] = isset( $input['animation_speed'] )
            ? absint( $input['animation_speed'] )
            : 300;

        $sanitized['trigger'] = isset( $input['trigger'] )
            ? sanitize_text_field( $input['trigger'] )
            : 'hover';

        $sanitized['mobile_breakpoint'] = isset( $input['mobile_breakpoint'] )
            ? absint( $input['mobile_breakpoint'] )
            : 768;

        $sanitized['enable_icons'] = ! empty( $input['enable_icons'] );
        $sanitized['enable_images'] = ! empty( $input['enable_images'] );

        return $sanitized;
    }

    /**
     * Enqueue admin assets.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        // Load on nav-menus.php and our settings page.
        if ( 'nav-menus.php' !== $hook && 'toplevel_page_ideal-mega-menu' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'ideal-mega-menu-admin',
            IMM_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            IMM_VERSION
        );

        wp_enqueue_script(
            'ideal-mega-menu-admin',
            IMM_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            IMM_VERSION,
            true
        );

        wp_enqueue_media();
    }

    /**
     * Render the settings page.
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap imm-settings-wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <div class="imm-settings-header">
                <p><?php esc_html_e( 'Configure your Ideal Mega Menu settings below.', 'ideal-mega-menu' ); ?></p>
            </div>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'ideal_mega_menu_settings_group' );
                do_settings_sections( 'ideal-mega-menu' );
                submit_button( __( 'Save Settings', 'ideal-mega-menu' ) );
                ?>
            </form>

            <div class="imm-settings-info">
                <h3><?php esc_html_e( 'How to Use', 'ideal-mega-menu' ); ?></h3>
                <ol>
                    <li><?php esc_html_e( 'Go to Appearance → Menus to manage your navigation menus.', 'ideal-mega-menu' ); ?></li>
                    <li><?php esc_html_e( 'Expand any top-level menu item to see mega menu options.', 'ideal-mega-menu' ); ?></li>
                    <li><?php esc_html_e( 'Enable "Mega Menu" to turn a dropdown into a mega menu panel.', 'ideal-mega-menu' ); ?></li>
                    <li><?php esc_html_e( 'Add sub-items — each one becomes a column in the mega menu.', 'ideal-mega-menu' ); ?></li>
                    <li><?php esc_html_e( 'Customize with icons, images, badges, and widgets.', 'ideal-mega-menu' ); ?></li>
                </ol>
            </div>
        </div>
        <?php
    }

    /**
     * Render general section description.
     */
    public function render_general_section() {
        echo '<p>' . esc_html__( 'Configure the general mega menu behavior and appearance.', 'ideal-mega-menu' ) . '</p>';
    }

    /**
     * Render menu style field.
     */
    public function render_menu_style_field() {
        $value = Ideal_Mega_Menu::get_setting( 'menu_style', 'default' );
        ?>
        <select name="ideal_mega_menu_settings[menu_style]">
            <option value="default" <?php selected( $value, 'default' ); ?>><?php esc_html_e( 'Default', 'ideal-mega-menu' ); ?></option>
            <option value="dark" <?php selected( $value, 'dark' ); ?>><?php esc_html_e( 'Dark', 'ideal-mega-menu' ); ?></option>
            <option value="light" <?php selected( $value, 'light' ); ?>><?php esc_html_e( 'Light', 'ideal-mega-menu' ); ?></option>
            <option value="minimal" <?php selected( $value, 'minimal' ); ?>><?php esc_html_e( 'Minimal', 'ideal-mega-menu' ); ?></option>
        </select>
        <?php
    }

    /**
     * Render animation field.
     */
    public function render_animation_field() {
        $value = Ideal_Mega_Menu::get_setting( 'animation', 'fade' );
        ?>
        <select name="ideal_mega_menu_settings[animation]">
            <option value="none" <?php selected( $value, 'none' ); ?>><?php esc_html_e( 'None', 'ideal-mega-menu' ); ?></option>
            <option value="fade" <?php selected( $value, 'fade' ); ?>><?php esc_html_e( 'Fade', 'ideal-mega-menu' ); ?></option>
            <option value="slide" <?php selected( $value, 'slide' ); ?>><?php esc_html_e( 'Slide Down', 'ideal-mega-menu' ); ?></option>
            <option value="zoom" <?php selected( $value, 'zoom' ); ?>><?php esc_html_e( 'Zoom', 'ideal-mega-menu' ); ?></option>
        </select>
        <?php
    }

    /**
     * Render animation speed field.
     */
    public function render_animation_speed_field() {
        $value = Ideal_Mega_Menu::get_setting( 'animation_speed', 300 );
        ?>
        <input type="number" name="ideal_mega_menu_settings[animation_speed]" value="<?php echo esc_attr( $value ); ?>" min="0" max="2000" step="50" />
        <p class="description"><?php esc_html_e( 'Animation duration in milliseconds.', 'ideal-mega-menu' ); ?></p>
        <?php
    }

    /**
     * Render trigger field.
     */
    public function render_trigger_field() {
        $value = Ideal_Mega_Menu::get_setting( 'trigger', 'hover' );
        ?>
        <select name="ideal_mega_menu_settings[trigger]">
            <option value="hover" <?php selected( $value, 'hover' ); ?>><?php esc_html_e( 'Hover', 'ideal-mega-menu' ); ?></option>
            <option value="click" <?php selected( $value, 'click' ); ?>><?php esc_html_e( 'Click', 'ideal-mega-menu' ); ?></option>
        </select>
        <?php
    }

    /**
     * Render mobile breakpoint field.
     */
    public function render_mobile_breakpoint_field() {
        $value = Ideal_Mega_Menu::get_setting( 'mobile_breakpoint', 768 );
        ?>
        <input type="number" name="ideal_mega_menu_settings[mobile_breakpoint]" value="<?php echo esc_attr( $value ); ?>" min="320" max="1200" step="1" />
        <p class="description"><?php esc_html_e( 'Screen width (in pixels) below which the mobile menu is displayed.', 'ideal-mega-menu' ); ?></p>
        <?php
    }

    /**
     * Add custom fields to menu items.
     *
     * @param int      $item_id Menu item ID.
     * @param WP_Post  $item    Menu item data object.
     * @param int      $depth   Depth of menu item.
     * @param stdClass $args    An object of wp_nav_menu() arguments.
     * @param int      $id      Nav menu ID.
     */
    public function menu_item_custom_fields( $item_id, $item, $depth, $args, $id ) {
        $mega_menu  = get_post_meta( $item_id, '_imm_mega_menu', true );
        $columns    = get_post_meta( $item_id, '_imm_mega_columns', true );
        $icon       = get_post_meta( $item_id, '_imm_icon', true );
        $image      = get_post_meta( $item_id, '_imm_image', true );
        $hide_text  = get_post_meta( $item_id, '_imm_hide_text', true );
        $badge_text = get_post_meta( $item_id, '_imm_badge_text', true );
        $badge_color = get_post_meta( $item_id, '_imm_badge_color', true );
        $disable_link = get_post_meta( $item_id, '_imm_disable_link', true );
        ?>
        <div class="imm-menu-item-settings" data-depth="<?php echo esc_attr( $depth ); ?>">
            <p class="description imm-field-title">
                <strong><?php esc_html_e( 'Ideal Mega Menu Settings', 'ideal-mega-menu' ); ?></strong>
            </p>

            <?php if ( 0 === $depth ) : ?>
            <p class="field-imm-mega-menu description">
                <label for="edit-menu-item-imm-mega-menu-<?php echo esc_attr( $item_id ); ?>">
                    <input type="checkbox" id="edit-menu-item-imm-mega-menu-<?php echo esc_attr( $item_id ); ?>"
                           name="menu-item-imm-mega-menu[<?php echo esc_attr( $item_id ); ?>]"
                           value="on" <?php checked( $mega_menu, 'on' ); ?> />
                    <?php esc_html_e( 'Enable Mega Menu', 'ideal-mega-menu' ); ?>
                </label>
            </p>

            <p class="field-imm-columns description">
                <label for="edit-menu-item-imm-columns-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Columns', 'ideal-mega-menu' ); ?>
                    <select id="edit-menu-item-imm-columns-<?php echo esc_attr( $item_id ); ?>"
                            name="menu-item-imm-columns[<?php echo esc_attr( $item_id ); ?>]">
                        <?php for ( $i = 2; $i <= 6; $i++ ) : ?>
                        <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $columns, $i ); ?>><?php echo esc_html( $i ); ?></option>
                        <?php endfor; ?>
                    </select>
                </label>
            </p>
            <?php endif; ?>

            <p class="field-imm-icon description">
                <label for="edit-menu-item-imm-icon-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Icon Class', 'ideal-mega-menu' ); ?>
                    <input type="text" id="edit-menu-item-imm-icon-<?php echo esc_attr( $item_id ); ?>"
                           class="widefat"
                           name="menu-item-imm-icon[<?php echo esc_attr( $item_id ); ?>]"
                           value="<?php echo esc_attr( $icon ); ?>"
                           placeholder="e.g., dashicons-admin-home" />
                </label>
            </p>

            <p class="field-imm-image description">
                <label for="edit-menu-item-imm-image-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Image URL', 'ideal-mega-menu' ); ?>
                    <input type="url" id="edit-menu-item-imm-image-<?php echo esc_attr( $item_id ); ?>"
                           class="widefat imm-image-url"
                           name="menu-item-imm-image[<?php echo esc_attr( $item_id ); ?>]"
                           value="<?php echo esc_url( $image ); ?>" />
                </label>
                <button type="button" class="button imm-upload-image"><?php esc_html_e( 'Upload Image', 'ideal-mega-menu' ); ?></button>
            </p>

            <p class="field-imm-badge description">
                <label for="edit-menu-item-imm-badge-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Badge Text', 'ideal-mega-menu' ); ?>
                    <input type="text" id="edit-menu-item-imm-badge-<?php echo esc_attr( $item_id ); ?>"
                           class="widefat"
                           name="menu-item-imm-badge[<?php echo esc_attr( $item_id ); ?>]"
                           value="<?php echo esc_attr( $badge_text ); ?>"
                           placeholder="e.g., New" />
                </label>
            </p>

            <p class="field-imm-badge-color description">
                <label for="edit-menu-item-imm-badge-color-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Badge Color', 'ideal-mega-menu' ); ?>
                    <input type="color" id="edit-menu-item-imm-badge-color-<?php echo esc_attr( $item_id ); ?>"
                           name="menu-item-imm-badge-color[<?php echo esc_attr( $item_id ); ?>]"
                           value="<?php echo esc_attr( $badge_color ? $badge_color : '#e74c3c' ); ?>" />
                </label>
            </p>

            <p class="field-imm-hide-text description">
                <label for="edit-menu-item-imm-hide-text-<?php echo esc_attr( $item_id ); ?>">
                    <input type="checkbox" id="edit-menu-item-imm-hide-text-<?php echo esc_attr( $item_id ); ?>"
                           name="menu-item-imm-hide-text[<?php echo esc_attr( $item_id ); ?>]"
                           value="on" <?php checked( $hide_text, 'on' ); ?> />
                    <?php esc_html_e( 'Hide Menu Item Text', 'ideal-mega-menu' ); ?>
                </label>
            </p>

            <p class="field-imm-disable-link description">
                <label for="edit-menu-item-imm-disable-link-<?php echo esc_attr( $item_id ); ?>">
                    <input type="checkbox" id="edit-menu-item-imm-disable-link-<?php echo esc_attr( $item_id ); ?>"
                           name="menu-item-imm-disable-link[<?php echo esc_attr( $item_id ); ?>]"
                           value="on" <?php checked( $disable_link, 'on' ); ?> />
                    <?php esc_html_e( 'Disable Link', 'ideal-mega-menu' ); ?>
                </label>
            </p>
        </div>
        <?php
    }

    /**
     * Save custom fields for menu items.
     *
     * @param int   $menu_id         ID of the updated menu.
     * @param int   $menu_item_db_id ID of the updated menu item.
     * @param array $args            An array of arguments used to update a menu item.
     */
    public function save_menu_item_custom_fields( $menu_id, $menu_item_db_id, $args ) {
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            return;
        }

        // Verify nonce (WordPress handles this via the menu save process).
        $fields = array(
            '_imm_mega_menu'   => 'menu-item-imm-mega-menu',
            '_imm_mega_columns' => 'menu-item-imm-columns',
            '_imm_icon'        => 'menu-item-imm-icon',
            '_imm_image'       => 'menu-item-imm-image',
            '_imm_hide_text'   => 'menu-item-imm-hide-text',
            '_imm_badge_text'  => 'menu-item-imm-badge',
            '_imm_badge_color' => 'menu-item-imm-badge-color',
            '_imm_disable_link' => 'menu-item-imm-disable-link',
        );

        foreach ( $fields as $meta_key => $post_key ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce handled by WP menu save.
            if ( isset( $_POST[ $post_key ][ $menu_item_db_id ] ) ) {
                $value = sanitize_text_field( wp_unslash( $_POST[ $post_key ][ $menu_item_db_id ] ) );
                update_post_meta( $menu_item_db_id, $meta_key, $value );
            } else {
                delete_post_meta( $menu_item_db_id, $meta_key );
            }
        }
    }

    /**
     * Register widget areas for mega menu columns.
     */
    public function register_widget_areas() {
        for ( $i = 1; $i <= 6; $i++ ) {
            register_sidebar(
                array(
                    /* translators: %d: widget area number */
                    'name'          => sprintf( __( 'Mega Menu Widget Area %d', 'ideal-mega-menu' ), $i ),
                    'id'            => 'imm-widget-area-' . $i,
                    'description'   => sprintf(
                        /* translators: %d: widget area number */
                        __( 'Widget area for Mega Menu column %d.', 'ideal-mega-menu' ),
                        $i
                    ),
                    'before_widget' => '<div id="%1$s" class="widget imm-widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h4 class="imm-widget-title">',
                    'after_title'   => '</h4>',
                )
            );
        }
    }
}
