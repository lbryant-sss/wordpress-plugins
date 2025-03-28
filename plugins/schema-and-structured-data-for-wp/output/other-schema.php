<?php
/**
 * Output Page
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output_post_specific/output_post_specific
 * @version 1.0
 */
if (! defined('ABSPATH') ) exit;

function saswp_wpfaqschemamarkup_schema() {

    $input1 = array();
    
    return apply_filters( 'saswp_modify_wpfaqschemamarkup_schema', $input1 );
}

function saswp_faqschemaforpost_schema() {

    $input1 = array();
    
    return apply_filters( 'saswp_modify_faqschemaforpost_schema', $input1 );
}

function saswp_schema_for_faqs_schema() {

    global $post, $sd_data;

    $input1    = array();
    $saswp_faq = array();

    if ( isset( $sd_data['saswp-schemaforfaqs']) && $sd_data['saswp-schemaforfaqs'] == 1 && class_exists('Schema_Faqs') && !saswp_non_amp() ) {

        $post_meta = get_post_meta($post->ID, 'schema_faqs_ques_ans_data', true);
        $post_meta = str_replace("\'","'",$post_meta);

        if ( ! empty( $post_meta) ) {

            $data_arr = json_decode($post_meta, true);

            foreach( $data_arr as $value){
                
                if ( isset( $value['question']) ) {
    
                    $saswp_faq[] =  array(
                        '@type'     => 'Question',
                        'name'      => stripslashes($value['question']),
                        'acceptedAnswer'=> array(
                            '@type' => 'Answer',
                            'text'  => stripslashes($value['answer']),
                        )
                    );

                }

            }

            if ( ! empty( $saswp_faq) ) {

                $input1['@context']   = saswp_context_url();
                $input1['@type']      = 'FAQPage';
                $input1['mainEntity'] = $saswp_faq;

            }            

        }

    }

    return $input1;
}

function saswp_wp_product_review_lite_rich_snippet() {

    global $post, $sd_data;

    $input1    = array();    

    if( is_object($post) && (isset($sd_data['saswp-wp-product-review']) && $sd_data['saswp-wp-product-review']) && class_exists('WPPR_Review_Model') ){        

        $review_object = new WPPR_Review_Model($post->ID);
        $input1        = $review_object->get_json_ld();        
    }

    return apply_filters('saswp_modify_wp_product_review_lite_default_schema', $input1);    

}

function saswp_taqyeem_review_rich_snippet() {

    global $post, $sd_data;

    $input1    = array();    

    if ( isset( $sd_data['saswp-taqyeem']) && $sd_data['saswp-taqyeem'] == 1 && function_exists('taqyeem_review_get_rich_snippet') ) {
        if(is_object($post) ) {
            $get_meta = get_post_custom( $post->ID );
            if( !empty( $get_meta['taq_review_position'][0] ) ){
                $input1 = taqyeem_review_get_rich_snippet();
            }
        }
    }

    return apply_filters('saswp_modify_taqeem_default_schema', $input1);    

}

add_action( 'amp_post_template_footer', 'saswp_wordlift_amp_schema' );

function saswp_wordlift_amp_schema() {

    global $sd_data;
    
    if ( isset( $sd_data['saswp-wordlift']) && $sd_data['saswp-wordlift'] == 1 && class_exists('Wordlift\Jsonld\Jsonld_Adapter') ) {

        if( function_exists('amp_get_schemaorg_metadata') ){

            $metadata = amp_get_schemaorg_metadata();            

            if ( ! empty( $metadata) ) {
                
                echo '<script type="application/ld+json" id="wl-jsonld">';
                echo wp_json_encode( $metadata, JSON_UNESCAPED_UNICODE);
                echo '</script>';

            }

        }
                
    } 
    
}

add_filter( 'saswp_modify_recipe_schema_output', 'saswp_wp_recipe_maker_json_ld',10,1);

function saswp_wp_recipe_maker_json_ld($input1){

    global $sd_data;

    $recipe_json = array();

    if ( isset( $sd_data['saswp-wp-recipe-maker']) && $sd_data['saswp-wp-recipe-maker'] == 1){                            
        
        $recipe_ids = saswp_get_ids_from_content_by_type('wp_recipe_maker');

        if($recipe_ids){

            foreach( $recipe_ids as $recipe){

                if(class_exists('WPRM_Recipe_Manager') ) {

                    $recipe_arr    = WPRM_Recipe_Manager::get_recipe( $recipe );

                    if($recipe_arr){
                        $recipe_json[] = saswp_wp_recipe_schema_json($recipe_arr);                                            
                    }
                    
                }

            } 
            
            if($recipe_json){
                $input1 = $recipe_json[0];
            }

         }

    }

    return $input1;

}

add_filter( 'saswp_modify_recipe_schema_output', 'saswp_recipress_json_ld',10,1);

function saswp_recipress_json_ld($input1){

    global $sd_data, $post;

    if( (isset($sd_data['saswp-recipress']) && $sd_data['saswp-recipress'] == 1) && function_exists('has_recipress_recipe') && has_recipress_recipe() && function_exists('recipress_recipe') ) {

        if(recipress_recipe('title') ) {
            $input1['name']          = recipress_recipe('title');
        }
        if(recipress_recipe('summary') ) {
            $input1['description']   = recipress_recipe('summary');    
        }                        
        if(recipress_recipe('cook_time','iso') ) {
            $input1['cookTime'] = recipress_recipe('cook_time','iso');
        }        
        if(recipress_recipe('prep_time', 'iso') ) {
            $input1['prepTime'] = recipress_recipe('prep_time', 'iso');
        }        
        if(recipress_recipe('ready_time','iso') ) {
            $input1['totalTime'] = recipress_recipe('ready_time','iso');
        }

        $cuisines = wp_strip_all_tags( get_the_term_list( $post->ID, 'cuisine', '', ', ') );

        if($cuisines){
              $input1['recipeCuisine'] = $cuisines;
        }
        if(recipress_recipe('yield') ) {
            $input1['recipeYield'] = recipress_recipe('yield');
        }        
        $ingredients     = recipress_recipe('ingredients');
        $ingredients_arr = array();

        if($ingredients){
            foreach( $ingredients as $ing){
                $ingredients_arr[] = $ing['ingredient'];
            }
            $input1['recipeIngredient'] = $ingredients_arr;
        }

        $instructions     = recipress_recipe('instructions');
        
        $instructions_arr = array();

        if($instructions){
            foreach( $instructions as $ing){
                $instructions_arr[] = $ing['description'];
            }
            $input1['recipeInstructions'] = $instructions_arr;
        }
        
        if(saswp_get_the_categories() ) {
            $input1['recipeCategory'] = saswp_get_the_categories();    
        }                
        
    }
   
    return $input1;
}

function saswp_wp_tasty_recipe_json_ld() {

    if ( ! is_singular() ) {
        return array();
    }
    global $sd_data;
    $resposne = array();

    if( isset($sd_data['saswp-wptastyrecipe']) && $sd_data['saswp-wptastyrecipe'] == 1 && class_exists('Tasty_Recipes') && class_exists('Tasty_Recipes\Distribution_Metadata') ){

        $recipes = Tasty_Recipes::get_recipes_for_post(
            get_queried_object()->ID,
            array(
                'disable-json-ld' => false,
            )
        );
        if ( empty( $recipes ) ) {
            return array();
        }
                    
            foreach ( $recipes as $recipe ) {
                $resposne[] = Tasty_Recipes\Distribution_Metadata::get_enriched_google_schema_for_recipe( $recipe, get_queried_object() );                
            }
                        
    }
    
    return $resposne;

}

add_filter( 'saswp_modify_video_object_schema_output', 'saswp_featured_video_plus_schema',10,1);

function saswp_featured_video_plus_schema($input1){

    global $sd_data;

    if( isset($sd_data['saswp-featured-video-plus']) && $sd_data['saswp-featured-video-plus'] == 1 && function_exists('get_the_post_video_url') ){

        if(has_post_video() ) {

            $input1['contentUrl']   = get_the_post_video_url();
            $input1['embedUrl']     = get_the_post_video_url();
            $input1['thumbnailUrl'] = get_the_post_video_image_url();
            
        }
        
    }

    return $input1;
}

add_filter( 'saswp_modify_product_schema_output', 'saswp_classpress_ads_schema',10,1);

function saswp_classpress_ads_schema($input1){

    global $sd_data, $post;
    
    if(is_object($post) && $post->post_type == 'ad_listing' && isset($sd_data['saswp-classipress']) && $sd_data['saswp-classipress'] == 1 ){        

        $post_meta = get_post_meta($post->ID);

        $input1['identifier']  = $post_meta['cp_sys_ad_conf_id'];

        $input1['url']         = saswp_get_permalink();
        $input1['name']        = saswp_get_the_title();
        $input1['identifier']  = $post_meta['cp_sys_ad_conf_id'];
        $input1['description'] = saswp_get_the_excerpt();
        
       
        $input1['offers']['@type']         = 'Offer';
        $input1['offers']['url']           = saswp_get_permalink();
        $input1['offers']['price']         = $post_meta['cp_price'][0] ? $post_meta['cp_price'][0] : 0;
        $input1['offers']['priceCurrency'] = 'USD';
        $input1['offers']['availability']  = 'InStock';
        $input1['offers']['validFrom']     = get_the_modified_date('c');
        $input1['offers']['priceValidUntil']     = $post_meta['cp_sys_expire_date'][0];

        if( $post_meta['cp_ad_sold'][0] == 'yes') {
            $input1['offers']['availability']  = 'OutOfStock';   
        }
      
        
    }

    return $input1;
}

add_filter( 'saswp_modify_product_schema_output', 'saswp_wpecommerce_product_schema',10,1);

function saswp_wpecommerce_product_schema($input1){

    global $sd_data, $post;

    if( isset($sd_data['saswp-wpecommerce']) && $sd_data['saswp-wpecommerce'] == 1 && function_exists('wpsc_the_product_description') && get_post_type() == 'wpsc-product' ){

            $price = get_post_meta( get_the_ID(), '_wpsc_special_price', true );
            $cal_price = wpsc_calculate_price(get_the_ID());
			$currargs = array(
				'display_currency_symbol' => false,
				'display_decimal_point'   => false,
				'display_currency_code'   => true,
				'display_as_html'         => false
			);
			$cal_price = wpsc_currency_display($cal_price, $currargs);
            $currency  = chop($cal_price," 0");

            $availability = 'InStock';

            if(!wpsc_product_has_stock() ) {
                $availability = 'OutOfStock';
            }

            $single_offer = array(
                '@type'         => 'Offer',
                'price'         => $price,   
                'url'           => get_permalink(),       
                'priceCurrency' => $currency, 
                'priceValidUntil' => gmdate( 'Y-12-31', time() + YEAR_IN_SECONDS ),                   
                'availability'  => $availability,                                        
              );

            $input1['sku']         = wpsc_product_sku();
            $input1['description'] = wpsc_the_product_description();
            $input1['name']        = wpsc_the_product_title();
           
            $input1['offers']      = $single_offer;
           
        
    }

    return $input1;

}

add_filter( 'saswp_modify_book_schema_output', 'saswp_add_novelist_schema',10,1);

function saswp_add_novelist_schema( $input1 ){

    global $sd_data, $post, $wpdb;

    if( isset($sd_data['saswp-novelist']) && $sd_data['saswp-novelist'] == 1 ){

        if(get_post_type() != 'book'){
            return $input1;
        }

        $genres     =  wp_get_post_terms( $post->ID , 'novelist-genre');
        $genres_str = '';      

        if ( ! is_wp_error( $genres) ) {
        
            if(count($genres)>0){
                                                
                foreach ( $genres as $genre) {
                    
                    $genres_str .= $genre->name.', '; 
                
                } 
                
            }

        }

        $post_meta = get_post_meta($post->ID);        

        $input1['headline']                 = saswp_get_the_title();    
        $input1['genre']                    = $genres_str;         
        $input1['datePublished']            = get_the_date("c");   
        $input1['dateModified']             = get_the_modified_date("c"); 
        $input1['editor']                   = saswp_get_author_details();   
        $input1['author']                   = saswp_get_author_details();         
        
        if ( ! empty( $post_meta['novelist_excerpt'][0]) ) {
            $input1['description']              = $post_meta['novelist_excerpt'][0];               
        }
        
        if ( ! empty( $post_meta['novelist_isbn'][0]) ) {
            $input1['isbn']                     = $post_meta['novelist_isbn'][0]; 
        }                

        if ( ! empty( $post_meta['novelist_pages'][0]) ) {
            $input1['numberOfPages']            = $post_meta['novelist_pages'][0];        
        }                                

        if ( ! empty( $post_meta['novelist_publisher'][0]) ) {
            $input1['publisher']                = array(
                        '@type' => 'Organization',
                        'name'  => $post_meta['novelist_publisher'][0]
            );
        }
        
        if ( ! empty( $post_meta['novelist_contributors'][0]) ) {
            $input1['contributor']                = array(
                '@type' => 'Organization',
                'name'  => $post_meta['novelist_contributors'][0]
            );                                        
        }        
    }

    return $input1;
}

add_filter( 'saswp_modify_book_schema_output', 'saswp_add_mooberrybm_schema',10,1);

function saswp_add_mooberrybm_schema( $input1 ){

    global $sd_data, $post, $wpdb;

    if( isset($sd_data['saswp-mooberrybm']) && $sd_data['saswp-mooberrybm'] == 1 ){

        if(get_post_type() != 'mbdb_book'){
            return $input1;
        }

        $tags    =  wp_get_post_terms( $post->ID , 'mbdb_tag');
        $tag_str = '';    

        if ( ! is_wp_error( $tags) ) {
        
            if(count($tags)>0){
                                                
                foreach ( $tags as $tag) {
                    
                    $tag_str .= $tag->name.', '; 
                
                } 
                
            }

        }

        $genres     =  wp_get_post_terms( $post->ID , 'mbdb_genre');
        $genres_str = '';      
        if ( ! is_wp_error( $genres) ) {
        
            if(count($genres)>0){
                                                
                foreach ( $genres as $genre) {
                    
                    $genres_str .= $genre->name.', '; 
                
                } 
                
            }

        }

        $illustrators     =  wp_get_post_terms( $post->ID , 'mbdb_illustrator');
        $illustrator_arr  = array();

        if ( ! is_wp_error( $illustrators) ) {
        
            if(count($illustrators)>0){
                                                
                foreach ( $illustrators as $illu) {
                    
                    $illustrator_arr[] = array(
                        '@type' => 'Person',
                        'name'  => $illu->name,
                    );
                
                } 
                
            }

        }

        $editors       =  wp_get_post_terms( $post->ID , 'mbdb_editor');
        $editors_arr   = array();

        if ( ! is_wp_error( $editors) ) {
        
            if(count($editors)>0){
                                                
                foreach ( $editors as $editor) {
                    
                    $editors_arr[] = array(
                        '@type' => 'Person',
                        'name'  => $editor->name,
                    );
                
                } 
                
            }

        }

        $editions = get_post_meta($post->ID, '_mbdb_editions', true);   
        
        $editions_arr = array();

        $format = array('Hardcover', 'Paperback', 'ePub', 'Kindle', 'PDF', 'Audiobook');

        if ( ! empty( $editions) ) {

            foreach ( $editions as $value) {

                $editions_arr[] = array(
                    '@type'         => 'Book',
                    'isbn'          => !empty($value['_mbdb_isbn']) ? $value['_mbdb_isbn'] : '',
                    'bookEdition'   => !empty($value['_mbdb_edition_title']) ? $value['_mbdb_edition_title'] : '',
                    'bookFormat'    => !empty($format[$value['_mbdb_format']]) ? $format[$value['_mbdb_format']] : '',
                    'inLanguage'    => !empty($value['_mbdb_language']) ? $value['_mbdb_language'] : '',
                    'numberOfPages' => !empty($value['_mbdb_length']) ? $value['_mbdb_length'] : '',
                    'offers'      => array(
                                        '@type'         => 'Offer',
                                        'price'         => !empty($value['_mbdb_retail_price'])? $value['_mbdb_retail_price'] : '',
                                        'priceCurrency' => !empty($value['_mbdb_currency']) ? $value['_mbdb_currency'] : ''
                                    ),
                );                    
            }
        }
        
        $publisher  = array();
        $imprint    = array();
        $cache_key  = 'saswp_mbdb_books_cache_key_'.$post->ID;
        $book_table = wp_cache_get( $cache_key );  
        if ( false === $book_table ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Custom table wp_mbdb_books
            $book_table = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mbdb_books WHERE book_id = %d",trim($post->ID)), 'ARRAY_A');  
            wp_cache_set( $cache_key, $book_table );
        }
                
        if ( ! empty( $book_table) ) {

        $mbdb_options   = get_option('mbdb_options');                        

        if ( ! empty( $mbdb_options['publishers']) ) {

            foreach ( $mbdb_options['publishers'] as $value) {

                if($value['uniqueID'] == $book_table['publisher_id']){
                    $publisher['@type'] = 'Organization';
                    $publisher['name']  = $value['name'];
                    $publisher['url']   = $value['website'];
                    break;
                }

            }

        }

        if ( ! empty( $mbdb_options['imprints']) ) {

            foreach ( $mbdb_options['imprints'] as $value) {

                if($value['uniqueID'] == $book_table['imprint_id']){
                    $imprint['@type'] = 'Organization';
                    $imprint['name']  = $value['name'];
                    $imprint['url']   = $value['website'];
                    break;
                }

            }

        }

        }
                        
        $input1['headline']                 = saswp_get_the_title();
        $input1['alternativeHeadline']      = $book_table['subtitle'];
        $input1['description']              = $book_table['summary'];               
        $input1['genre']                    = $genres_str;
        $input1['keywords']                 = $tag_str; 
        $input1['illustrator']              = $illustrator_arr; 
        $input1['editor']                   = $editors_arr;   
        $input1['author']                   = saswp_get_author_details();         
        $input1['datePublished']            = get_the_date("c");   
        $input1['dateModified']             = get_the_modified_date("c"); 

        if ( ! empty( $editions_arr) ) {
            $input1['workExample']   = $editions_arr; 
        }

        if ( ! empty( $publisher) ) {
            $input1['publisher']            = $publisher;   
        }
        if ( ! empty( $imprint) ) {
            $input1['publisherImprint']     = $imprint;   
        }                       
           
    }
    
    return $input1;
}