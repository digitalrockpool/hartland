<?php
/* Includes: Woocommerce

@package	Hartland
@author		Digital Rockpool
@link		https://digitalrockpool.com
@copyright	Copyright (c) 2021, Digital Rockpool LTD
@license	GPL-2.0+

# Table of Contents
  - Add product revisions
  - Display 'Out of Stock' on archive pages
  - Set endpoint for email preferences
  - Set tabs on product pages
	- Change woocommerce text
	- Change woocommerce backorder text
  - Add woocommerce price prefix
  - Remove product links and thumbnails from basket
  - Remove phone number checkout field
  - Add plus & minus to quantity
  - Hide shipping when free is available
  - Display product toggle content shortcode
  - Display product ingredients shortcode
  - Display product packaging and recycling shortcode
*/


//*** Add product revisions
add_filter( 'woocommerce_register_post_type_product', 'modify_product_post_type' );

function modify_product_post_type( $args ) {
     $args['supports'][] = 'revisions';

     return $args;
}

//*** Display 'Out of Stock' on archive pages
add_action( 'woocommerce_before_shop_loop_item_title', 'display_sold_out_loop_woocommerce' );
function display_sold_out_loop_woocommerce() {
    global $product;
    if ( ! $product->is_in_stock() ) {
        echo '<div class="soldout">Sold Out</div>';
    }
}

/*** Set custom endpoint
add_filter ( 'woocommerce_account_menu_items', 'add_custom_menu_item', 40 );
function add_custom_menu_item( $menu_links ){

	$menu_links = array_slice( $menu_links, 0, 4, true )
	+ array( 'custom-page' => 'Custom Page' )
	+ array_slice( $menu_links, 4, NULL, true );

	return $menu_links;

}

add_action( 'init', 'add_custom_page' );
function add_custom_page() {

	add_rewrite_endpoint( 'custom-page', EP_PAGES );

}

add_action( 'woocommerce_account_custom-page_endpoint', 'custom_page_endpoint_content' );
function custom_page_endpoint_content() {

	echo 'Add content';

}  */

//*** Set tabs on product pages
add_filter( 'woocommerce_product_tabs', 'woo_custom_product_tabs' );
function woo_custom_product_tabs( $tabs ) {

    unset( $tabs['description'] ); // remove description tab
    // unset( $tabs['reviews'] ); // remove the reviews tab
    unset( $tabs['additional_information'] ); // remove the additional information tab

    /* Set a custom tab
    $tabs['product_specification_tab'] = array(
			'title'     => __( 'Product Specification', 'woocommerce' ),
			'callback'  => 'woo_product_specification_tab_content',
			'priority'  => 5
		); */

    return $tabs;
}


//*** Change woocommerce text
add_filter('gettext', function ($translated_text, $text, $domain) {
  if ($domain == 'woocommerce') :
		switch ($translated_text) {
			case 'Cart totals':
				$translated_text = __('Order summary', 'woocommerce');
				break;
			case 'Update cart':
				$translated_text = __('Update bag', 'woocommerce');
			break;
			case 'Add to cart':
				$translated_text = __('Add to bag', 'woocommerce');
			break;
			case 'View cart':
				$translated_text = __('View bag', 'woocommerce');
			break;
			case 'verified owner':
				$translated_text = __('verified customer', 'woocommerce');
			break;
		}
	endif;
  return $translated_text;
}, 20, 3);


function change_sale_text() {
  return '<span class="onsale">Pre-order</span>';
}
add_filter('woocommerce_sale_flash', 'change_sale_text');

//*** Change woocommerce backorder text
function change_backorder_message( $text, $product ) {
  if( $product->is_on_backorder( 1 ) ) :
      $text = __( 'Pre-orders will be shipped after the 30th September', 'hartland' );
  endif;
  return $text;
}
add_filter( 'woocommerce_get_availability_text', 'change_backorder_message', 10, 2 );


//*** Add woocommerce price prefix
function add_price_prefix( $price, $product ){
    $price = '<span class="price-prefix">Pre-order before 30th September and get 20% off</span>' . $price;
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'add_price_prefix', 99, 2 );


//*** Remove product links and thumbnails from basket
// add_filter( 'woocommerce_cart_item_permalink', '__return_null' );
// add_filter( 'woocommerce_cart_item_thumbnail', '__return_false' );


//*** Remove shipping weight from products
// add_filter( 'wc_product_enable_dimensions_display', '__return_false' );


//*** Remove phone number checkout field
function woocommerce_remove_checkout_fields( $fields ) {
  unset( $fields['billing']['billing_phone'] );
  return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'woocommerce_remove_checkout_fields' );


//*** Add plus & minus to quantity
add_action( 'woocommerce_after_add_to_cart_quantity', 'woo_quantity_plus_sign' );
function woo_quantity_plus_sign() {
  echo '<button type="button" class="woo-quanitiy-plus" ><i class="fas fa-plus"></i></button>';
}

add_action( 'woocommerce_before_add_to_cart_quantity', 'woo_quantity_minus_sign' );
function woo_quantity_minus_sign() {
  echo '<button type="button" class="woo-quanitiy-minus" ><i class="fas fa-minus"></i></button>';
}

add_action( 'wp_footer', 'woo_quantity_plus_minus' );
function woo_quantity_plus_minus() {

  if( is_product() || is_shop() ) ?>
    <script type="text/javascript">
      jQuery(document).ready(function($){

        $('form.cart').on( 'click', 'button.woo-quanitiy-plus, button.woo-quanitiy-minus', function() {

          // Get current quantity values
          var qty = $( this ).closest( 'form.cart' ).find( '.qty' );
          var val = parseFloat(qty.val());
          var max = parseFloat(qty.attr( 'max' ));
          var min = parseFloat(qty.attr( 'min' ));
          var step = parseFloat(qty.attr( 'step' ));

          // Change the value if plus or minus
          if ( $( this ).is( '.woo-quanitiy-plus' ) ) {
            if ( max && ( max <= val ) ) {
              qty.val( max );
            }
            else {
              qty.val( val + step );
            }
          }
          else {
            if ( min && ( min >= val ) ) {
              qty.val( min );
            }
            else if ( val > 1 ) {
              qty.val( val - step );
            }
          }

        });
      });
  </script> <?php
}


//*** Hide shipping when free is available
function hide_shipping_when_free( $rates ) {
	$free = array();
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$free[ $rate_id ] = $rate;
			break;
		}
	}
	return ! empty( $free ) ? $free : $rates;
}
add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free', 100 );


//*** Display product toggle content shortcode
function display_additional_product_content() {

  $toggle_content = get_field('toggle_content');

  if( $toggle_content ):

    $custom_field = '<table class="woocommerce-product-attributes shop_attributes">';

    if( have_rows( 'product_dimensions', $toggle_content ) ):
      while( have_rows( 'product_dimensions', $toggle_content ) ) : the_row();

        $product_container_label = get_sub_field('product_container_label');

        $height_field = get_sub_field('height');
        if( $height_field ) : $height = 'H'.$height_field.'cm | '; endif;

        $width_field = get_sub_field('width');
        if( $width_field ) : $width = 'W'.$width_field.'cm | '; endif;

        $length_field = get_sub_field('length');
        if( $length_field ) : $length = 'L'.$length_field.'cm'; endif;

        $custom_field .= '<tr class="woocommerce-product-attributes-item"><th class="woocommerce-product-attributes-item__label">'.$product_container_label.'</th><td class="woocommerce-product-attributes-item__value"><p>'.$height.$width.$length.'</p></td></tr>';
        endwhile;
      endif;

      $product_quantity_size = get_field('product_quantity_size', $toggle_content );
      $product_quantity_size_label = $product_quantity_size['product_quantity_size_label'];
      $quantity = $product_quantity_size['quantity'];
      $unit = $product_quantity_size['unit'];

      if( $product_quantity_size_label ) : $custom_field .= '<tr class="woocommerce-product-attributes-item"><th class="woocommerce-product-attributes-item__label">'.$product_quantity_size_label.'</th><td class="woocommerce-product-attributes-item__value"><p>'.$quantity.$unit.'</p></td></tr>'; endif;

      $product_reeds = get_field('reeds', $toggle_content );
      $reed_label = $product_reeds['reed_label'];
      $reed_type = $product_reeds['type'];
      $reed_height = $product_reeds['reed_height'];

      if( $reed_label ) : $custom_field .= '<tr class="woocommerce-product-attributes-item"><th class="woocommerce-product-attributes-item__label">'.$reed_label.'</th><td class="woocommerce-product-attributes-item__value"><p>'.$reed_type.' '.$reed_height.'cm high</p></td></tr>'; endif;

      $product_notes = get_field('product_notes', $toggle_content );
      $product_notes_label = $product_notes['product_notes_label'];
      $product_notes = $product_notes['product_notes'];

      if( $product_notes_label ) : $custom_field .= '<tr class="woocommerce-product-attributes-item"><th class="woocommerce-product-attributes-item__label">'.$product_notes_label.'</th><td class="woocommerce-product-attributes-item__value"><p>'.$product_notes.'</p></td></tr>'; endif;

      $ad_hoc_content = get_field('ad-hoc_content', $toggle_content );
      if( $ad_hoc_content ) : $custom_field .= '<tr class="woocommerce-product-attributes-item"><td colspan="2" class="woocommerce-product-attributes-item__label">'.$ad_hoc_content.'</p></td></tr>'; endif;

      $custom_field .='</table>';

      return $custom_field;

    endif;

}
add_shortcode('display_additional_product_content', 'display_additional_product_content');


/*** Display product ingredients shortcode */
function display_product_ingredients() {

  global $product;

  $essential_oils_list = '<p>We source the majority of our ingredients from a family-owned supplier in the Southwest of England. These ingredients are NOT tested on animals and where ever possible are supplied in reusable materials.</p>';
  $essential_oils = wc_get_product_terms( $product->id, 'pa_essential-oils', array( 'fields' => 'all') );

  if( $essential_oils ) :

    $essential_oils_list .= '<h3>Our key essential oils:</h3>';

    foreach( $essential_oils as $essential_oil ) :
      $essential_oils_list .= '<h4>'.$essential_oil->name.'</h4>';
      $essential_oils_list .= '<p>'.term_description( $essential_oil->term_id ).'</p>';
    endforeach;

  endif;
  $contains = wc_get_product_terms( $product->id, 'pa_contains', array( 'fields' => 'all') );

    if( $contains ) :

      $contains_list = 'Contains: ';

      foreach( $contains as $contain ) :
        $contains_list .= $contain->name.', ';
      endforeach;

    endif;

  return $essential_oils_list.substr($contains_list, 0, -2);

}
add_shortcode('display_product_ingredients', 'display_product_ingredients');


//*** Display product how to use shortcode
function display_product_how_to_use() {

  $toggle_content = get_field('toggle_content');
  if( $toggle_content ):

      $custom_field = get_field( 'how_to_use', $toggle_content );

    endif;

  return $custom_field;

}
add_shortcode('display_product_how_to_use', 'display_product_how_to_use');


//*** Display product packaging and recycling shortcode
function display_product_packaging_recycling() {

  $toggle_content = get_field('toggle_content');
  if( $toggle_content ):

      $custom_field = get_field( 'packaging_recycling', $toggle_content );

    endif;

  return $custom_field;

}
add_shortcode('display_product_packaging_recycling', 'display_product_packaging_recycling');





/* add_filter( 'woocommerce_add_to_cart_redirect', 'wp_get_referer' );

add_filter( 'woocommerce_add_to_cart_redirect', 'custom_redirect_function' );
    function custom_redirect_function() {
    return get_permalink( wc_get_page_id( 'shop' ) );
} */



add_action( 'woocommerce_before_shop_loop_item_title', 'add_on_hover_shop_loop_image' ) ;

function add_on_hover_shop_loop_image() {

    $image_id = wc_get_product()->get_gallery_image_ids()[0] ;

    if ( $image_id ) {

        echo wp_get_attachment_image( $image_id, $size = 'woocommerce_thumbnail' ) ;

    } else {  //assuming not all products have galleries set

        echo wp_get_attachment_image( wc_get_product()->get_image_id() ) ;

    }

}


add_filter( 'wc_shipment_tracking_get_providers', 'custom_shipment_tracking' );
function custom_shipment_tracking( $providers ) {

    unset($providers['Australia']);
    unset($providers['Austria']);
    unset($providers['Brazil']);
    unset($providers['Belgium']);
    unset($providers['Canada']);
    unset($providers['Czech Republic']);
    unset($providers['Finland']);
    unset($providers['France']);
    unset($providers['Germany']);
    unset($providers['Ireland']);
    unset($providers['Italy']);
    unset($providers['India']);
    unset($providers['Netherlands']);
    unset($providers['New Zealand']);
    unset($providers['Poland']);
    unset($providers['Romania']);
    unset($providers['South African']);
    unset($providers['Sweden']);
    // unset($providers['United Kingdom']);
    unset($providers['United States']); // Removes all US options
    unset($providers['United States']['Fedex']);
    unset($providers['United States']['FedEx Sameday']);
    unset($providers['United States']['UPS']);
    unset($providers['United States']['USPS']);
    unset($providers['United States']['OnTrac']);
    unset($providers['United States']['DHL US']);

    return $providers;
}
