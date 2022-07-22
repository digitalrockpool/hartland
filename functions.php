<?php
/* Core Functions

@package	Hartland
@author		Digital Rockpool
@link		https://digitalrockpool.com
@copyright	Copyright (c) 2021, Digital Rockpool LTD
@license	GPL-2.0+

# Table of Contents
  - Enqueue script and styles for child theme
	- Add includes
  - Register sidebars
*/


//*** Enqueue script and styles for child theme
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles' );
function child_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri().'/style.css' );
}

add_action( 'admin_enqueue_scripts', 'admin_enqueue_styles' );
function admin_enqueue_styles() {
    wp_enqueue_style( 'admin_css', get_stylesheet_directory_uri().'/lib/css/admin-style.css', false, '1.0.0' );
}

//*** Add includes
require_once get_stylesheet_directory().'/lib/inc/custom-post.php';
require_once get_stylesheet_directory().'/lib/inc/gravity-forms.php';
require_once get_stylesheet_directory().'/lib/inc/woocommerce.php';


//*** Register sidebars
add_action( 'widgets_init', 'facebook_reviews_sidebar' );
function facebook_reviews_sidebar() {
  $args = array(
    'name'          => 'Facebook Reviews',
    'id'            => 'facebook-reviews-sidebar',
    'description'   => '',
    'class'         => '',
    'before_widget' => '<li id="%1$s" class="widget %2$s">',
    'after_widget'  => '</li>',
    'before_title'  => '<h5 class="widgettitle">',
    'after_title'   => '</h5>'
  );

  register_sidebar( $args );
}

//*** Adding extra user meta data
function extra_user_profile_fields( $user ) { ?>
  <h2><?php _e("Shop / Business details", "blank"); ?></h2>

  <table class="form-table">
    <tr>
      <th><label for="business-name"><?php _e(" Shop / Business name"); ?></label></th>
      <td>
        <input type="text" name="business-name" id="business-name" value="<?php echo esc_attr( get_the_author_meta( 'business-name', $user->ID ) ); ?>" class="regular-text" placeholder="Shop / Business Name" />
        <br />
      </td>
    </tr>
    <tr>
      <th><label for="business-address"><?php _e("Shop / Business address"); ?></label></th>
      <td>
        <input type="text" name="business-street-address" id="business-street-address" value="<?php echo esc_attr( get_the_author_meta( 'business-street-address', $user->ID ) ); ?>" class="regular-text" placeholder="Business Street Address" /><br />
        <input type="text" name="business-address-line-2" id="business-address-line-2" value="<?php echo esc_attr( get_the_author_meta( 'business-address-line-2', $user->ID ) ); ?>" class="regular-text" placeholder="Address Line 2" /><br />
        <input type="text" name="business-address-city" id="business-address-city" value="<?php echo esc_attr( get_the_author_meta( 'business-address-city', $user->ID ) ); ?>" class="regular-text" placeholder="City" /><br />
        <input type="text" name="business-address-county" id="business-address-county" value="<?php echo esc_attr( get_the_author_meta( 'business-address-county', $user->ID ) ); ?>" class="regular-text" placeholder="County" /><br />
        <input type="text" name="business-address-postcode" id="business-address-postcode" value="<?php echo esc_attr( get_the_author_meta( 'business-address-postcode', $user->ID ) ); ?>" class="regular-text" placeholder="Postcode" /><br />
        <input type="text" name="business-address-country" id="business-address-country" value="<?php echo esc_attr( get_the_author_meta( 'business-address-country', $user->ID ) ); ?>" class="regular-text" placeholder="Country" />
        <br />
      </td>
    </tr>
  </table> <?php
}
add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    update_user_meta( $user_id, 'business-name', $_POST['business-name'] );
    update_user_meta( $user_id, 'business-address', $_POST['business-address'] );
    update_user_meta( $user_id, 'business-address-line-2', $_POST['business-address-line-2'] );
    update_user_meta( $user_id, 'business-address-city', $_POST['business-address-city'] );
    update_user_meta( $user_id, 'business-address-county', $_POST['business-address-county'] );
    update_user_meta( $user_id, 'business-address-postcode', $_POST['business-address-postcode'] );
    update_user_meta( $user_id, 'business-address-country', $_POST['business-address-country'] );
}
add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );
