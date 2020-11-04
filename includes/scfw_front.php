<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('SCFW_front')) {

    class SCFW_front {

        protected static $instance;

        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
             return self::$instance;
        }

        function init() {
            add_action('wp_head', array( $this, 'SCFW_craete_cart' ));
            add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'SCFW_cart_count_fragments' ), 10, 1 );
            add_action( 'wp_ajax_change_qty', array( $this, 'SCFW_change_qty_cust') );
            add_action( 'wp_ajax_nopriv_change_qty', array( $this, 'SCFW_change_qty_cust') );
            add_action( 'wp_ajax_product_remove', array( $this, 'SCFW_ajax_product_remove') );
            add_action( 'wp_ajax_nopriv_product_remove', array( $this, 'SCFW_ajax_product_remove') );
            add_action( 'wp_ajax_coupon_ajax_call', array( $this, 'SCFW_coupon_ajax_call_func') );
            add_action( 'wp_ajax_nopriv_coupon_ajax_call', array( $this, 'SCFW_coupon_ajax_call_func') );
            add_action( 'wp_ajax_remove_applied_coupon_ajax_call', array( $this, 'SCFW_remove_applied_coupon_ajax_call_func') );
            add_action( 'wp_ajax_nopriv_remove_applied_coupon_ajax_call', array( $this, 'SCFW_remove_applied_coupon_ajax_call_func') );
            add_action( 'wp_footer', array($this, 'SCFW_single_added_to_cart_event'));
            add_action( 'wp_ajax_wfc_prod_slider_ajax_atc', array( $this, 'SCFW_prod_slider_ajax_atc') );
            add_action( 'wp_ajax_nopriv_wfc_prod_slider_ajax_atc', array( $this, 'SCFW_prod_slider_ajax_atc') );
            add_action( 'wp_ajax_wfc_get_refresh_fragments', array( $this, 'SCFW_get_refreshed_fragments' ) );
            add_action( 'wp_ajax_nopriv_wfc_get_refresh_fragments', array( $this, 'SCFW_get_refreshed_fragments' ) );
        }


        function SCFW_get_refreshed_fragments(){
            WC_AJAX::get_refreshed_fragments();
        }        


        function SCFW_cart_create() {
            WC()->cart->calculate_totals();
            WC()->cart->maybe_set_cart_cookies();
            global $woocommerce;
            $wfc_sidecart_width = get_option( 'wfc_sidecart_width', '350').'px';

            ?>
            <div class="wfc_container" style="width: <?php echo $wfc_sidecart_width; ?>; right: -<?php echo $wfc_sidecart_width; ?>;">
                <div class="wfc_header">
                    <span class="wfc_cart_icon">
                        <img src="<?php echo SCFW_PLUGIN_DIR . '/images/shopping-cart.svg'; ?>">
                    </span>
                    <h3 class="wfc_header_title" style="color: <?php echo get_option( 'wfc_head_ft_clr', '#000000') ?>;font-size: <?php echo get_option( 'wfc_head_ft_size', '20' )."px" ?>"><?php echo get_option( 'wfc_head_title', 'Your Cart' ); ?></h3>
                    <span class="wfc_close_cart">
                        <img src="<?php echo SCFW_PLUGIN_DIR . '/images/cancel-music.png'; ?>">
                    </span>
                </div>
                <div class="wfc_body">
                    <?php
                        $html = ''; 
                        $html .= '<div class="wfc_body">';
                        if ( ! WC()->cart->is_empty() ) :
                                    $html .= "<div class='wfc_cust_mini_cart'>";
                                    global $woocommerce;
                                    $items = WC()->cart->get_cart();
                                        foreach($items as $item => $values) {
                                            $html .= "<div class='wfc_cart_prods' product_id='".$values['product_id']."' c_key='".$values['key']."'>";
                                            $html .= "<div class='wfc_cart_prods_inner'>";
                                            $_product =  wc_get_product( $values['data']->get_id() );
                                            $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $values['product_id'], $values, $item );
                                            $getProductDetail = wc_get_product( $values['product_id'] );
                                            $html .= "<div class='image_div'>";
                                            $html .= $getProductDetail->get_image('thumbnail');
                                            $html .= '</div>';
                                            $html .= "<div class='description_div'>";
                                            $html .= "<div class='wfc_prcdel_div'>";
                                            $html .= apply_filters('woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="wfc_remove"  aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s"><img src="'.SCFW_PLUGIN_DIR.'/images/delete.svg"></a>', 
                                                    esc_url(wc_get_cart_remove_url($item)), 
                                                    esc_html__('Remove this item', 'evolve'),
                                                    esc_attr( $product_id ),
                                                    esc_attr( $_product->get_sku() ),
                                                    esc_attr( $item )
                                                    ), $item);
                                            $html .= "</div>";
                                            
                                            $html .= "<div class='wfc_prodline_title' style='color:". get_option( 'wfc_product_ft_clr', '#000000') .";font-size: ".get_option( 'wfc_product_ft_size', '16')."px;'>";
                                            $html .= $_product->get_name();
                                            $html .= "</div>";
                                            
                                            $html .= "<div class='wfc_prodline_qty'>";
                                            $html .= '<div class="wfc_qupdiv">';
                                            
                                            $html .= 'Qty: '.$values['quantity'];
                                            
                                            $html .= '</div>';

                                            $html .= "<div class='wfc_prodline_price'>";
                                            
                                            $wfc_product = $values['data'];
                                            $wfc_product_subtotal = WC()->cart->get_product_subtotal( $wfc_product, $values['quantity'] );

                                            $html .= $wfc_product_subtotal;
                                            $html .= "</div>";

                                            $html .= "</div>";
                                            
                                            $html .= "</div>";
                                            $html .= "</div>";
                                            $html .= "</div>";
                                        }
                                        
                                    $html .= "</div>";

                                else :
                                        $html .= "<h3 class='empty_cart_text'>Cart is empty</h3>";
                                endif;
                        $html .= '</div>';
                        echo $html;
                    ?>
                </div>


                <?php
                $showCouponField = 'true';
                if(wp_is_mobile()) {
                    if(get_option( 'wfc_coupon_field_mobile', 'yes') == 'no') {
                        $showCouponField = 'false';
                    }
                }

                if($showCouponField == 'true') {
                ?>
                <div class="wfc_trcpn">
                    <div class='wfc_total_tr'>
                        <div class='wfc_total_label'>
                            <span><?php echo get_option( 'wfc_subtotal_txt', 'Subtotal'); ?></span>
                        </div>

                        <?php
                        $wfc_get_totals = WC()->cart->get_totals();
                        $wfc_cart_total = $wfc_get_totals['subtotal'];
                        $wfc_cart_discount = $wfc_get_totals['discount_total'];
                        $wfc_final_subtotal = $wfc_cart_total - $wfc_cart_discount;
                        ?>

                        <div class='wfc_total_amount'>
                            <span class='wfc_fragtotal'><?php echo get_woocommerce_currency_symbol().number_format($wfc_final_subtotal, 2); ?></span>
                        </div>
                    </div>
                    <div class='wfc_coupon'>
                        <div class='wfc_apply_coupon_link' style="color: <?php echo get_option( 'wfc_apply_cpn_ft_clr', '#000000'); ?>">
                            <a href='#' id='wfc_apply_coupon'><?php echo get_option( 'wfc_apply_cpn_txt', 'Apply Coupon'); ?></a>
                        </div>
                        <div class="wfc_coupon_field">
                            <input type="text" id="wfc_coupon_code" placeholder="<?php echo get_option( 'wfc_apply_cpn_plchldr_txt', "Enter your promo code" ); ?>">
                            <span class="wfc_coupon_submit" style="background-color: <?php echo get_option( 'wfc_applybtn_cpn_bg_clr', '#000000' ); ?>; color: <?php echo get_option( 'wfc_applybtn_cpn_ft_clr', '#ffffff' ); ?>;"><?php echo get_option( 'wfc_apply_cpn_apbtn_txt', 'APPLY'); ?></span>
                        </div>

                        <?php
                        $applied_coupons = WC()->cart->get_applied_coupons();
                        if(!empty($applied_coupons)) {
                        ?>
                            <ul class='wfc_applied_cpns'>
                        <?php
                            foreach($applied_coupons as $cpns ) {
                            ?>    
                            <li class='wfc_remove_cpn' cpcode='<?php echo $cpns; ?>'><?php echo $cpns; ?><span class='dashicons dashicons-no'></span></li>
                            
                            <?php
                            }
                        ?>    
                            </ul>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
                }
                ?>

                <div class="wfc_footer">
                    <div class="wfc_ship_txt" style="color: <?php echo get_option( 'wfc_ship_ft_clr', '#000000') ?>;font-size: <?php echo get_option( 'wfc_ship_ft_size', '16')."px" ?>;"><?php echo get_option( 'wfc_ship_txt', 'To find out your shipping cost , Please proceed to checkout.' ); ?></div>
                    <?php  if(get_option( 'wfc_cart_option', 'yes' ) == "yes") { ?>
                        <a href="<?php echo wc_get_cart_url();?>" style="background-color: <?php echo get_option( 'wfc_ft_btn_clr', '#766f6f') ?>;margin: <?php echo get_option( 'wfc_ft_btn_mrgin', '5')."px" ?>;color: <?php echo get_option( 'wfc_ft_btn_txt_clr', '#ffffff') ?>;">
                            <?php echo get_option( 'wfc_cart_txt', 'View Cart'); ?>
                        </a>
                    <?php } ?>
                    <?php  if(get_option( 'wfc_checkout_option', 'yes' ) == "yes"){ ?>
                        <a href="<?php echo wc_get_checkout_url(); ?>" style="background-color: <?php echo get_option( 'wfc_ft_btn_clr', '#766f6f') ?>;margin: <?php echo get_option( 'wfc_ft_btn_mrgin', '5')."px" ?>;color: <?php echo get_option( 'wfc_ft_btn_txt_clr', '#ffffff') ?>;">
                            <?php echo get_option( 'wfc_checkout_txt', 'Checkout'); ?>
                        </a>
                    <?php } ?>
                    <?php  if(get_option( 'wfc_conshipping_option', 'yes' ) == "yes"){ ?>
                        <a href="<?php echo get_option( 'wfc_conshipping_link', '#' ) ?>" style="background-color: <?php echo get_option( 'wfc_ft_btn_clr', '#766f6f') ?>;margin: <?php echo get_option( 'wfc_ft_btn_mrgin', '5')."px" ?>;color: <?php echo get_option( 'wfc_ft_btn_txt_clr', '#ffffff') ?>;">
                            <?php echo get_option( 'wfc_conshipping_txt', 'Continue Shopping'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="wfc_container_overlay">
            </div>
            <?php if(get_option( 'wfc_show_cart_icn', 'yes' ) == "yes"){ ?>
                <div class="wfc_cart_basket" style="<?php if(get_option('wfc_basket_position') == "top"){ ?>top: 15px;<?php }elseif (get_option('wfc_basket_position', 'bottom') == "bottom") { ?>bottom: 15px;<?php } ?>;height: <?php echo get_option( 'wfc_basket_icn_size', '60')."px" ?>;width: <?php echo get_option( 'wfc_basket_icn_size', '60')."px" ?>;background-color: <?php echo get_option( 'wfc_basket_bg_clr', '#cccccc') ?>;">
                    <div class="cart_box">
                        <img src="<?php echo SCFW_PLUGIN_DIR . '/images/ShoppingCart2-512.png'; ?>">
                    </div>
                    <?php if(get_option( 'wfc_product_cnt', 'yes' ) == "yes"){ ?>
                        <div class="wfc_item_count" style="background-color: <?php echo get_option( 'wfc_cnt_bg_clr', '#000000') ?>;color: <?php echo get_option( 'wfc_cnt_txt_clr', '#ffffff') ?>;font-size: <?php echo get_option( 'wfc_cnt_txt_size', '15')."px" ?>;">
                            <span class="float_countc"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        </div>
                    <?php } ?>
                </div>
                <?php
            }
        }
     
        function SCFW_craete_cart() {
            $wcf_page_ids = explode(",",get_option( 'wfc_on_pages' ));
            $wcf_crnt_page = get_the_ID();
            if (!in_array($wcf_crnt_page, $wcf_page_ids)) {
                if(wp_is_mobile() ){
                    if(get_option( 'wfc_mobile', 'yes' ) == "yes") {
                        if(is_checkout() || is_cart()){
                        } else {
                            add_filter( 'wp_footer', array($this, 'SCFW_cart_create'));
                        }
                    }
                } else {
                    if(is_checkout() || is_cart()) {
                    } else {
                        add_filter( 'wp_footer', array($this, 'SCFW_cart_create'));
                    }
                }
            }
        }
      
        function SCFW_cart_count_fragments( $fragments ) {
            WC()->cart->calculate_totals();
            WC()->cart->maybe_set_cart_cookies();
            $html = '<div class="wfc_body">';
            if ( ! WC()->cart->is_empty() ) :

                        $html .= "<div class='wfc_cust_mini_cart'>";
                        global $woocommerce;
                        $items = WC()->cart->get_cart(); 
                            foreach($items as $item => $values) { 
                                $html .= "<div class='wfc_cart_prods' product_id='".$values['product_id']."' c_key='".$values['key']."'>";
                                $html .= "<div class='wfc_cart_prods_inner'>";
                                $_product =  wc_get_product( $values['data']->get_id() );
                                $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $values['product_id'], $values, $item );
                                $getProductDetail = wc_get_product( $values['product_id'] );
                                $html .= "<div class='image_div'>";
                                $html .= $getProductDetail->get_image('thumbnail');
                                $html .= '</div>';
                                $html .= "<div class='description_div'>";
                                $html .= "<div class='wfc_prcdel_div'>";
                                $html .= apply_filters('woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="wfc_remove"  aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s"><img src="'.SCFW_PLUGIN_DIR.'/images/delete.svg"></a>', 
                                        esc_url(wc_get_cart_remove_url($item)), 
                                        esc_html__('Remove this item', 'evolve'),
                                        esc_attr( $product_id ),
                                        esc_attr( $_product->get_sku() ),
                                        esc_attr( $item )
                                        ), $item);
                                $html .= "</div>";
                                
                                $html .= "<div class='wfc_prodline_title' style='color:". get_option( 'wfc_product_ft_clr', '#000000') .";font-size: ".get_option( 'wfc_product_ft_size', '16')."px;'>";
                                $html .= $_product->get_name();
                                $html .= "</div>";
                                
                                $html .= "<div class='wfc_prodline_qty'>";
                                $html .= '<div class="wfc_qupdiv">';
                                
                                $html .= 'Qty: '.$values['quantity'];
                                
                                $html .= '</div>';

                                $html .= "<div class='wfc_prodline_price'>";

                                $wfc_product = $values['data'];
                                $wfc_product_subtotal = WC()->cart->get_product_subtotal( $wfc_product, $values['quantity'] );

                                $html .= $wfc_product_subtotal;

                                $html .= "</div>";

                                $html .= "</div>";
                                
                                $html .= "</div>";
                                $html .= "</div>";
                                $html .= "</div>";
                            }

                        $html .= "</div>";

                    else :
                            $html .= "<h3 class='empty_cart_text'>Cart is empty</h3>";
                    endif;
            $html .= '</div>';
            $fragments['div.wfc_body'] = $html;
            $html='<span class="float_countc">'.WC()->cart->get_cart_contents_count().'</span>';
            $fragments['span.float_countc'] = $html;


            $wfc_get_totals = WC()->cart->get_totals();
            $wfc_cart_total = $wfc_get_totals['subtotal'];
            $wfc_cart_discount = $wfc_get_totals['discount_total'];
            $wfc_final_subtotal = $wfc_cart_total - $wfc_cart_discount;
            
            $wfc_fragtotal = "<div class='wfc_total_amount'>".get_woocommerce_currency_symbol().number_format($wfc_final_subtotal, 2)."</div>";

            $fragments['div.wfc_total_amount'] = $wfc_fragtotal;


            $wfc_coupon_html = "<div class='wfc_coupon'>";
            $wfc_coupon_html .= "<div class='wfc_apply_coupon_link'>";
            $wfc_coupon_html .= "<a href='#' style='color:".get_option( 'wfc_apply_cpn_ft_clr', '#000000' )."' id='wfc_apply_coupon'>".get_option( 'wfc_apply_cpn_txt', 'Apply Coupon' )."</a>";
            $wfc_coupon_html .= "</div>";
            $wfc_coupon_html .= '<div class="wfc_coupon_field">';
            $wfc_coupon_html .= '<input type="text" id="wfc_coupon_code" placeholder="'.get_option( 'wfc_apply_cpn_plchldr_txt', 'Enter your promo code' ).'">';
            $wfc_coupon_html .= '<span class="wfc_coupon_submit" style="background-color: '.get_option( 'wfc_applybtn_cpn_bg_clr', '#000000' ).'; color: '.get_option( 'wfc_applybtn_cpn_ft_clr', '#ffffff' ).';">'.get_option( 'wfc_apply_cpn_apbtn_txt', 'APPLY' ).'</span>';
            $wfc_coupon_html .= '</div>';

            $applied_coupons = WC()->cart->get_applied_coupons();
            if(!empty($applied_coupons)) {
                $wfc_coupon_html .= "<ul class='wfc_applied_cpns'>";

                foreach($applied_coupons as $cpns ) {
                    $wfc_coupon_html .= "<li class='wfc_remove_cpn' cpcode='".$cpns."'>".$cpns." <span class='dashicons dashicons-no'></span></li>";
                }

                $wfc_coupon_html .= "</ul>";
            }

            $wfc_coupon_html .= "</div>";

            $fragments['div.wfc_coupon'] = $wfc_coupon_html;

            return $fragments;
        }


        function SCFW_ajax_product_remove() {
            ob_start();
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                if($cart_item['product_id'] == $_POST['product_id'] && $cart_item_key == $_POST['cart_item_key'] )
                {
                    WC()->cart->remove_cart_item($cart_item_key);
                }
            }

            WC()->cart->calculate_totals();
            WC()->cart->maybe_set_cart_cookies();

            woocommerce_mini_cart();

            $mini_cart = ob_get_clean();

            // Fragments and mini cart are returned
            $data = array(
                'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
                        'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
                    )
                ),
                'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
            );

            wp_send_json( $data );

            die();
        }
        
        function SCFW_change_qty_cust() {

            $c_key = sanitize_text_field($_REQUEST['c_key']);
            $qty = sanitize_text_field($_REQUEST['qty']);
            WC()->cart->set_quantity($c_key, $qty, true);
            WC()->cart->set_session();
            exit();
        }

        function SCFW_coupon_ajax_call_func() {
            
            $code = $_REQUEST['coupon_code'];
            $code = strtolower($code);

            // Check coupon code to make sure is not empty
            if( empty( $code ) || !isset( $code ) ) {

                $wfc_cpnfield_empty_txt = get_option( 'wfc_cpnfield_empty_txt', 'Coupon Code Field is Empty!' );
                // Build our response
                $response = array(
                    'result'    => 'empty',
                    'message'   => $wfc_cpnfield_empty_txt
                );

                header( 'Content-Type: application/json' );
                echo json_encode( $response );

                // Always exit when doing ajax
                WC()->cart->calculate_totals();
                WC()->cart->maybe_set_cart_cookies();
                WC()->cart->set_session();
                exit();
            }

            // Create an instance of WC_Coupon with our code
            $coupon = new WC_Coupon( $code );

            if (in_array($code, WC()->cart->get_applied_coupons())) {

                $wfc_cpn_alapplied_txt = get_option( 'wfc_cpn_alapplied_txt', 'Coupon Code Already Applied.' );

                $response = array(
                    'result'    => 'already applied',
                    'message'   => $wfc_cpn_alapplied_txt
                );

                header( 'Content-Type: application/json' );
                echo json_encode( $response );

                // Always exit when doing ajax
                WC()->cart->calculate_totals();
                WC()->cart->maybe_set_cart_cookies();
                WC()->cart->set_session();
                exit();

            } elseif( !$coupon->is_valid() ) {

                $wfc_invalid_coupon_txt = get_option( 'wfc_invalid_coupon_txt', 'Invalid code entered. Please try again.' );
                // Build our response
                $response = array(
                    'result'    => 'not valid',
                    'message'   => $wfc_invalid_coupon_txt
                );

                header( 'Content-Type: application/json' );
                echo json_encode( $response );

                // Always exit when doing ajax
                WC()->cart->calculate_totals();
                WC()->cart->maybe_set_cart_cookies();
                WC()->cart->set_session();
                exit();

            } else {
                
                WC()->cart->apply_coupon( $code );

                $wfc_coupon_applied_suc_txt = get_option( 'wfc_coupon_applied_suc_txt', 'Coupon Applied Successfully.' );
                // Build our response
                $response = array(
                    'result'    => 'success',
                    'message'      => $wfc_coupon_applied_suc_txt
                );

                header( 'Content-Type: application/json' );
                echo json_encode( $response );

                // Always exit when doing ajax
                WC()->cart->calculate_totals();
                WC()->cart->maybe_set_cart_cookies();
                WC()->cart->set_session();
                wc_clear_notices();
                exit();

            }
        }


        function SCFW_remove_applied_coupon_ajax_call_func() {
            $code = $_REQUEST['remove_code'];
            
            $wfc_coupon_removed_suc_txt = get_option( 'wfc_coupon_removed_suc_txt', 'Coupon Removed Successfully.' );

            if(WC()->cart->remove_coupon( $code )) {
                echo $wfc_coupon_removed_suc_txt;
            }
            WC()->cart->calculate_totals();
            WC()->cart->maybe_set_cart_cookies();
            WC()->cart->set_session();
            exit();
        }


        function SCFW_single_added_to_cart_event()
        {
            if( isset($_POST['add-to-cart']) && isset($_POST['quantity']) ) {
                ?>
                <script>
                jQuery(function($){
                    jQuery('.wfc_cart_basket').click();
                });
                </script>
                <?php
            }

            ?>
            <div class="wfc_coupon_response">
                <span id="wfc_cpn_resp"></span>
            </div>
            <?php
        }


        function SCFW_prod_slider_ajax_atc() {

            $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
            $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
            $variation_id = absint($_POST['variation_id']);
            $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
            $product_status = get_post_status($product_id);

            if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {

                do_action('woocommerce_ajax_added_to_cart', $product_id);

                if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
                    wc_add_to_cart_message(array($product_id => $quantity), true);
                }

                WC_AJAX :: get_refreshed_fragments();
            } else {

                $data = array(
                    'error' => true,
                    'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

                echo wp_send_json($data);
            }

            wp_die();
        }

    }
    SCFW_front::instance();
}