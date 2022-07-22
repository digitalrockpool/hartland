<?php
/* Includes: Custom Post Types

@package	Hartland
@author		Digital Rockpool
@link		https://digitalrockpool.com
@copyright	Copyright (c) 2021, Digital Rockpool LTD
@license	GPL-2.0+

# Table of Contents
  - Register toggle content post types
	- Register landing page post types
  - Remove slug from landing page permalinks
*/

//*** Register toggle content post type
function custom_post_type_toggle() {

    $labels = array(
        'name'                => _x( 'Toggle Content', 'Post Type General Name', 'hartland' ),
        'singular_name'       => _x( 'Toggle Content', 'Post Type Singular Name', 'hartland' ),
        'menu_name'           => __( 'Toggle Content', 'hartland' ),
        'parent_item_colon'   => __( 'Parent Toggle Content', 'hartland' ),
        'all_items'           => __( 'All Toggle Content', 'hartland' ),
        'view_item'           => __( 'View Toggle Content', 'hartland' ),
    		'add_new_item'        => __( 'Add New Toggle Content', 'hartland' ),
    		'add_new'             => __( 'Add New', 'hartland' ),
        'edit_item'           => __( 'Edit Toggle Content', 'hartland' ),
        'update_item'         => __( 'Update Toggle Content', 'hartland' ),
        'search_items'        => __( 'Search Toggle Content', 'hartland' ),
        'not_found'           => __( 'Not Found', 'hartland' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'hartland' ),
    );

    $args = array(
        'label'               => __( 'Toggle Content', 'hartland' ),
        'description'         => __( 'Shared information for the product pages', 'hartland' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'revisions', 'custom-fields' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 17,
    		'menu_icon'           => 'dashicons-palmtree',
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'capability_type'     => 'page',
        'show_in_rest' => true,

    );

    register_post_type( 'toggle', $args );
}
add_action( 'init', 'custom_post_type_toggle', 0 );

//*** Register landing page post type
function custom_post_type_landing() {

    $labels = array(
        'name'                => _x( 'Landing Page', 'Post Type General Name', 'hartland' ),
        'singular_name'       => _x( 'Landing Page', 'Post Type Singular Name', 'hartland' ),
        'menu_name'           => __( 'Landing Page', 'hartland' ),
        'parent_item_colon'   => __( 'Parent Landing Page', 'hartland' ),
        'all_items'           => __( 'All Landing Pages', 'hartland' ),
        'view_item'           => __( 'View Landing Page', 'hartland' ),
    		'add_new_item'        => __( 'Add New Landing Page', 'hartland' ),
    		'add_new'             => __( 'Add New', 'hartland' ),
        'edit_item'           => __( 'Edit Landing Page', 'hartland' ),
        'update_item'         => __( 'Update Landing Page', 'hartland' ),
        'search_items'        => __( 'Search Landing Page', 'hartland' ),
        'not_found'           => __( 'Not Found', 'hartland' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'hartland' ),
    );

    $args = array(
        'label'               => __( 'Landing Pages', 'hartland' ),
        'description'         => __( 'Landing pages', 'hartland' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 4,
    		'menu_icon'           => 'dashicons-money-alt',
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
        'show_in_rest' => true,

    );

    register_post_type( 'l', $args );
}
add_action( 'init', 'custom_post_type_landing', 0 );
