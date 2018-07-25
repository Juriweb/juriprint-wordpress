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

			// on retire le script checkout.js appelé par woocommerce
			wp_dequeue_script( 'wc_checkout');
			// on ajoute juriprint.js qui est une reprise de woocommerce checkout.js avec des modifs
			wp_enqueue_script( 'customjuriprintcheckout', get_stylesheet_directory_uri() . '/juriprintcheckout.js', array( 'jquery' ));
			wp_enqueue_script( 'customjuriprint', get_stylesheet_directory_uri() . '/juriprint.js', array( 'jquery' ));
	
	}

//}


/* 
function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );
*/



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
//  après le prix classique "A partir de" sur pages listing produits
/*
add_filter( 'wc_price', 'ajout_suffixe_get_price_html', 10, 2 );
function ajout_suffixe_get_price_html( $price, $product ){
	if ( is_page('8632') || is_page('8634') ) { // page produits huissier ou page produits avocat
	   	return $price.' <small class="woocommerce-price-suffix">HT</small>';
	} else {
		return $price;
	}
}
*/

// dans le panier Afficher préfixe TTC total
/*
add_filter( 'woocommerce_cart_total', 'total_custom_price_message' ); 
function total_custom_price_message( $price ) {
    $afterPriceSymbol = ' TTC';
    return $price . $afterPriceSymbol;
}

// dans le panier Afficher préfixe HT sous total
add_filter( 'woocommerce_cart_subtotal', 'subtotal_custom_price_message' );
function subtotal_custom_price_message( $price ) {
    $afterPriceSymbol = ' HT';
    return $price . $afterPriceSymbol;
}
*/

// redefinition du nombre de variations
function wpse_rest_batch_items_limit( $limit ) {
    $limit = 200;
    return $limit;
}
add_filter( 'woocommerce_rest_batch_items_limit', 'wpse_rest_batch_items_limit' );

//--------------Suppression nombre d'exemplaires
function wc_remove_all_quantity_fields( $return, $product ) {
    return true;
}
add_filter( 'woocommerce_is_sold_individually', 'wc_remove_all_quantity_fields', 10, 2 );

// Juriprint page produit -> modification mise en page short description sous photo produit

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_excerpt', 30 );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Retour boutique sur la page Mon Compte > Commandes
function wc_empty_cart_redirect_url() {
    return '/boutique/';
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );

// Rajout d'un label sur le complément d'adresse
add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );
function custom_override_default_address_fields( $address_fields ) {
     $address_fields['address_2']['label'] = 'Compl&eacute;ment d&rsquo;adresse';
     return $address_fields;
}

// Ajout Tab création - retirage
add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
function woo_new_product_tab( $tabs ) {	
	$tabs['creation_retirage'] = array(
		'title' 	=> __( 'Cr&eacute;ation / Retirage', 'woocommerce' ),
		'priority' 	=> 20,
		'callback' 	=> 'woo_new_product_tab_content'
	);
	return $tabs;
}
function woo_new_product_tab_content() {
	echo '<h2>Qu\'est-ce que la <strong>Cr&eacute;ation</strong></h2>';
	echo '<p>La cr&eacute;ation signifie que nous utilisons un logiciel graphique afin de concevoir votre visuel ou pour en modifier un d&eacute;j&agrave; existant.</p>';
	echo '<br/>';
	echo '<h2>Qu\'est-ce que le  <strong>Retirage</strong></h2>';
	echo '<p>Le retirage signifie que nous utilisons le dernier fichier en notre possession, ou celui que vous nous fournirez, sans avoir recours &agrave; un logiciel graphique tiers, afin d\'imprimer vos visuels.</p>';	
}

// Bouton retirage dans la liste des commandes
function cs_add_order_again_to_my_orders_actions( $actions, $order ) {
	if ( $order->has_status( 'completed' ) ) {
		$actions['order-again'] = array(
			'url'  => wp_nonce_url( add_query_arg( 'order_again', $order->id ) , 'woocommerce-order_again' ),
			'name' => __( 'Retirage' )
		);
	}
	return $actions;
}
add_filter( 'woocommerce_my_account_my_orders_actions', 'cs_add_order_again_to_my_orders_actions', 50, 2 );


// traduction articles dans le panier
function action_woocommerce_before_cart_table( $args ) {
	global $woocommerce;
	?>
			<?php if ( 1 == $woocommerce->cart->get_cart_contents_count() ) : ?>
				<h2><?php printf( esc_attr__( 'Vous avez %d article dans votre panier' ), $woocommerce->cart->get_cart_contents_count() );  ?></h2>
			<?php else : ?>
				<h2><?php printf( esc_attr__( 'Vous avez %d articles dans votre panier' ), $woocommerce->cart->get_cart_contents_count() );  ?></h2>
			<?php endif; 
}
add_action( 'woocommerce_before_cart_table', 'action_woocommerce_before_cart_table', 10, 0 ); 

// Override add-to-cart plugin
// ajout d'une redirection sur le bouton "continue shopping" vers la page catalogue : ID = 44
function xoo_cp_popup_override(){
	global $woocommerce;
	$cart_url 		= $woocommerce->cart->get_cart_url();
	$checkout_url 	= $woocommerce->cart->get_checkout_url();
	?>
	<div class="xoo-cp-opac"></div>
	<div class="xoo-cp-modal">
		<div class="xoo-cp-container">
			<div class="xoo-cp-outer">
				<div class="xoo-cp-cont-opac"></div>
				<i class="xcp-icon xcp-icon-spinner2 xcp-outspin"></i>
			</div>
			<i class="xcp-icon-cross xcp-icon xoo-cp-close"></i>

			<div class="xoo-cp-atcn"></div>

			<div class="xoo-cp-content"></div>
				
			<div class="xoo-cp-btns">
				<a class="xoo-cp-btn-vc xcp-btn" href="<?php echo $cart_url; ?>"><?php _e('View Cart','added-to-cart-popup-woocommerce'); ?></a>
				<a class="xoo-cp-btn-ch xcp-btn" href="<?php echo $checkout_url; ?>"><?php _e('Checkout','added-to-cart-popup-woocommerce'); ?></a>
				<a class="xoo-cp-btn-cs xcp-btn" href="<?php echo esc_url( get_page_link( 44 ) ); ?>"><?php _e('Continue Shopping','added-to-cart-popup-woocommerce'); ?></a>
			</div>
		</div>
	</div>
	<?php
}
add_action('wp_footer','xoo_cp_popup_override');

