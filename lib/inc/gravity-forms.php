<?php
/* Includes: Gravity Forms

@package	Hartland
@author		Digital Rockpool
@link		https://digitalrockpool.com
@copyright	Copyright (c) 2021, Digital Rockpool LTD
@license	GPL-2.0+

# Table of Contents
  - Auto login stockists
  - Product purchase dropdown
	- Review submission
*/

add_action( 'gform_user_registered', 'gf_registration_autologin',  10, 4 );
function vc_gf_registration_autologin( $user_id, $user_config, $entry, $password ) {
	$user = get_userdata( $user_id );
	$user_login = $user->user_login;
	$user_password = $password;

	wp_signon( array(
		'user_login' => $user_login,
		'user_password' =>  $user_password,
		'remember' => false
	) );
}



//*** Product purchase dropdown
add_filter( 'gform_pre_render_5', 'populate_product_purchase' );
function populate_product_purchase( $form ) {

	global $wpdb;
  $user_id = wp_get_current_user();

	$results = $wpdb->get_results( "SELECT post_title, product_id, num_items_sold FROM wp_wc_order_product_lookup INNER JOIN wp_posts ON wp_wc_order_product_lookup.product_id=wp_posts.ID INNER JOIN wp_wc_order_stats ON wp_wc_order_product_lookup.order_id=wp_wc_order_stats.order_id WHERE wp_wc_order_stats.customer_id=13 AND status='wc-completed' AND wp_wc_order_stats.date_created >= NOW() - INTERVAL 6 DAY" );

	foreach( $results as $rows ) :
		$choices[] = array( 'text' => $rows->post_title, 'value' => $rows->product_id );
	endforeach;

	foreach( $form['fields'] as &$field ) :
		if( $field['id'] == 4 ) :
			$field['choices'] = $choices;
		endif;
	endforeach;

	return $form;
}
