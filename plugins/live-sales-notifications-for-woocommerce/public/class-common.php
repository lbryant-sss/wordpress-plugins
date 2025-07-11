<?php

class pisol_sn_common{

    public static $pi_sn_date_format;
    public static $pi_sn_time_format;

    public static function getOptions(){
        self::$pi_sn_date_format = ('Y/m/d');
		self::$pi_sn_time_format = ('G:i');
    }
    
    public static function searchReplace($product){
        
        if(empty($product) || !is_array($product)) return;

        $message = self::getMessage();
        $search = array("{product}", "{product_link}", "{time}", "{date}","{country}", "{state}", "{city}", "{first_name}");
		$replace = array(
			'<span class="pi-product">'.$product['product'].'</span>',
			'<a class="pi-product_link" href="'.$product['link'].'">'.$product['product'].'</a>',
			'<span class="pi-time">'.$product['time'].'</span>', 
			'<span class="pi-date">'.$product['date'].'</span>', 
			'<span class="pi-country">'.$product['country'].'</span>', 
			'<span class="pi-state">'.$product['state'].'</span>', 
			'<span class="pi-city">'.$product['city'].'</span>', 
			'<span class="pi-first_name">'.$product['first_name'].'</span>'
		);
		$return = str_replace($search, $replace, $message);
		return $return;
    }

    public static function getMessage(){
        $message = get_option('pi_sn_sales_message','{product_link} was purchased by {first_name} from {country}');
        $language_specific_message =  self::getLanguageMessage();
        if($language_specific_message){
            $message = $language_specific_message;
        }
        return $message;
    }

    public static function getLanguageMessage(){
        return false; // Since we are not offering translation in free version
    }

    public static function formatProductObj($product, $order){
        
        if(!is_object($product)) return;

        if(!(is_object($order) && 
        method_exists ($order, 'get_billing_first_name') && 
        method_exists ($order, 'get_billing_city') &&
        method_exists ($order, 'get_billing_state') &&
        method_exists ($order, 'get_billing_country') &&
        method_exists ($order, 'get_date_created'))) return;

        $link = self::productLink($product);

        self::getOptions();
        $thumbnail = wp_get_attachment_image_src($product->get_image_id( ), 'thumbnail');
        $image =  isset($thumbnail[0]) ? $thumbnail[0] : wp_get_attachment_url($product->get_image_id( ));
        if(empty($image)){
            $image = wc_placeholder_img_src();
        }
        
        $formated_product = array(
            'product_id'=> $product->get_id(),
            'product' => self::getTitle($product->get_id()),
            'image' => $image,
            'link' => $link,
            'first_name' => $order->get_billing_first_name(),
            'city'=> $order->get_billing_city(),
            'state' => isset(WC()->countries->countries[$order->get_billing_country()]) ? WC()->countries->get_states($order->get_billing_country())[$order->get_billing_state()] : $order->get_billing_state(),
            'country' => isset(WC()->countries->countries[$order->get_billing_country()]) ? WC()->countries->countries[$order->get_billing_country()] : $order->get_billing_country(),
            'time'=> $order->get_date_created()->date(self::$pi_sn_time_format),
            'date'=> $order->get_date_created()->date(self::$pi_sn_date_format)
        );

        return $formated_product;
    }

    static function get_preview_product(){
        $date_format = get_option('pi_sn_date_format', 'Y/m/d');
        $time_format = get_option('pi_sn_time_format', 'g:i a');
        $date = date($date_format);
        $time = date($time_format);
        $formated_product = array(
            'product_id'=> 0,
            'product' => __('Product Name','pisol-sales-notification'),
            'image' => '',
            'link' => '#',
            'first_name' => 'John',
            'city'=> 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'time'=> $time,
            'time_passed'=> 'just now',
            'date'=> $date,
            'price'=> '$99.99',
            'stock_left'=> 2,
            'quantity'=> 10
        );
        return $formated_product;
    }

    static function productLink($product){
        if($product->is_type('external')){
            $link = $product->get_product_url();
            if(empty($link)){
                $link = $product->get_permalink( );
            }
        }else{
            $link = $product->get_permalink( );
        }
        return apply_filters('pisol_sn_filter_product_link',$link, $product);
    }

    static function getTitle($product_id){
        return get_the_title($product_id);
    }
}