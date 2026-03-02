<?php
/**
 * Custom Walker class for the Ideal Mega Menu.
 *
 * @package Ideal_Mega_Menu
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Mega Menu Walker Nav Menu class.
 */
class Ideal_Mega_Menu_Walker extends Walker_Nav_Menu {

    /**
     * Track whether mega menu is enabled for the current top-level item.
     *
     * @var bool
     */
    private $mega_menu_enabled = false;

    /**
     * Track current mega menu columns.
     *
     * @var int
     */
    private $mega_menu_columns = 4;

    /**
     * Starts the list before the elements are added.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );

        if ( 0 === $depth && $this->mega_menu_enabled ) {
            $columns_class = 'imm-columns-' . intval( $this->mega_menu_columns );
            $output .= "\n{$indent}<div class=\"imm-mega-menu-panel {$columns_class}\">\n";
            $output .= "{$indent}\t<ul class=\"imm-mega-menu-row\">\n";
        } else {
            $classes = array( 'sub-menu' );
            if ( $depth > 0 ) {
                $classes[] = 'imm-sub-menu-nested';
            }
            $class_names = implode( ' ', $classes );
            $output .= "\n{$indent}<ul class=\"{$class_names}\">\n";
        }
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );

        if ( 0 === $depth && $this->mega_menu_enabled ) {
            $output .= "{$indent}\t</ul>\n";
            $output .= "{$indent}</div>\n";
        } else {
            $output .= "{$indent}</ul>\n";
        }
    }

    /**
     * Starts the element output.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     * @param int      $id     Current item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = str_repeat( "\t", $depth );

        // Get mega menu meta data.
        $is_mega_menu  = get_post_meta( $item->ID, '_imm_mega_menu', true );
        $columns       = get_post_meta( $item->ID, '_imm_mega_columns', true );
        $icon          = get_post_meta( $item->ID, '_imm_icon', true );
        $image         = get_post_meta( $item->ID, '_imm_image', true );
        $hide_text     = get_post_meta( $item->ID, '_imm_hide_text', true );
        $badge_text    = get_post_meta( $item->ID, '_imm_badge_text', true );
        $badge_color   = get_post_meta( $item->ID, '_imm_badge_color', true );
        $widget_area   = get_post_meta( $item->ID, '_imm_widget_area', true );
        $disable_link  = get_post_meta( $item->ID, '_imm_disable_link', true );

        // Set mega menu state for child processing.
        if ( 0 === $depth ) {
            $this->mega_menu_enabled = ( 'on' === $is_mega_menu );
            $this->mega_menu_columns = ! empty( $columns ) ? intval( $columns ) : 4;
        }

        // Build CSS classes.
        $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        if ( 0 === $depth && $this->mega_menu_enabled ) {
            $classes[] = 'imm-has-mega-menu';
        }

        if ( 1 === $depth && $this->mega_menu_enabled ) {
            $classes[] = 'imm-mega-menu-column';
        }

        if ( ! empty( $icon ) ) {
            $classes[] = 'imm-has-icon';
        }

        if ( ! empty( $image ) ) {
            $classes[] = 'imm-has-image';
        }

        $class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $li_id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
        $li_id = $li_id ? ' id="' . esc_attr( $li_id ) . '"' : '';

        $output .= $indent . '<li' . $li_id . $class_names . '>';

        // Build link attributes.
        $atts           = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target ) ? $item->target : '';
        $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
        $atts['href']   = ! empty( $item->url ) ? $item->url : '';

        if ( 'on' === $disable_link ) {
            $atts['href'] = '#';
            $atts['class'] = 'imm-disabled-link';
        }

        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        // Build the menu item output.
        $title = apply_filters( 'the_title', $item->title, $item->ID );
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

        $item_output = '';

        if ( isset( $args->before ) ) {
            $item_output .= $args->before;
        }

        $item_output .= '<a' . $attributes . '>';

        // Add icon.
        if ( ! empty( $icon ) ) {
            $item_output .= '<span class="imm-icon"><i class="' . esc_attr( $icon ) . '"></i></span>';
        }

        // Add image.
        if ( ! empty( $image ) ) {
            $item_output .= '<span class="imm-image"><img src="' . esc_url( $image ) . '" alt="' . esc_attr( $title ) . '" /></span>';
        }

        if ( isset( $args->link_before ) ) {
            $item_output .= $args->link_before;
        }

        // Add title (optionally hidden).
        if ( 'on' !== $hide_text ) {
            $item_output .= '<span class="imm-item-title">' . $title . '</span>';
        }

        // Add badge.
        if ( ! empty( $badge_text ) ) {
            $badge_style = ! empty( $badge_color ) ? ' style="background-color:' . esc_attr( $badge_color ) . '"' : '';
            $item_output .= '<span class="imm-badge"' . $badge_style . '>' . esc_html( $badge_text ) . '</span>';
        }

        // Add dropdown arrow for items with children.
        if ( in_array( 'menu-item-has-children', $classes, true ) ) {
            $item_output .= '<span class="imm-arrow"></span>';
        }

        if ( isset( $args->link_after ) ) {
            $item_output .= $args->link_after;
        }

        $item_output .= '</a>';

        if ( isset( $args->after ) ) {
            $item_output .= $args->after;
        }

        // Add widget area if set.
        if ( ! empty( $widget_area ) && is_active_sidebar( $widget_area ) ) {
            $item_output .= '<div class="imm-widget-area">';
            ob_start();
            dynamic_sidebar( $widget_area );
            $item_output .= ob_get_clean();
            $item_output .= '</div>';
        }

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * Ends the element output.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}
