<?php
//if( !is_admin() ) {
	// fichiers css
	
	function theme_enqueue_styles() {
			wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
	}
	add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );


	// fichiers js
	add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );
	function theme_enqueue_scripts() {
		
			// On annule jQuery installer par WordPress 
			wp_deregister_script( 'jquery' );
			wp_deregister_script( 'jquery-migrate' );

			// On declare un nouveau jQuery dernière version grace au CDN de Google
			wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js','',false,true);
	
	}

//}


function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );


// modifier l'expéditeur des mails recu par les utilisateurs
add_filter('wp_mail_from', 'new_mail_from');
add_filter('wp_mail_from_name', 'new_mail_from_name');
 
function new_mail_from() { return 'contact@juriprint.com'; }
function new_mail_from_name() { return 'Juriprint'; }

// Utiliser les variables pour le format des prix WC 2.0
add_filter( 'woocommerce_variable_sale_price_html', 'wc_wc20_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_wc20_variation_price_format', 10, 2 );
function wc_wc20_variation_price_format( $price, $product ) {
	$min_price = $product->get_variation_price( 'min', true );
	$max_price = $product->get_variation_price( 'max', true );
	if ($min_price != $max_price){
		$price = sprintf( __( 'A partir de %1$s', 'woocommerce' ), wc_price( $min_price ) );
		return $price;
	} else {
		$price = sprintf( __( '%1$s', 'woocommerce' ), wc_price( $min_price ) );
		return $price;
	}
}

// masquer UGS (Unité de gestion de stock) 
add_filter( 'wc_product_sku_enabled', '__return_false' );


// -------------------------Woocommerce --------------------------

//--------------- Afficher préfixe
//  après le prix classique "A partir de"
/*add_filter( 'wc_price', 'ajout_suffixe_get_price_html', 10, 2 );
function ajout_suffixe_get_price_html( $price, $product ){
    return $price.' <small class="woocommerce-price-suffix">HT</small>';
}*/


// dans le panier Afficher préfixe TTC totale
add_filter( 'woocommerce_cart_totals_order_total_html', 'ajout_suffixe_cart_totals_order_total_html', 100, 2 );
function ajout_suffixe_cart_totals_order_total_html( $value, $product ){
    return $value.' TTC';
}




//--------------Suppression nombre d'exemplaires
function wc_remove_all_quantity_fields( $return, $product ) {
    return true;
}
add_filter( 'woocommerce_is_sold_individually', 'wc_remove_all_quantity_fields', 10, 2 );

// Juriprint page produit -> modification mise en page short description sous photo produit

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_excerpt', 30 );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );