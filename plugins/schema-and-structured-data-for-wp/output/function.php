<?php
/**
 * Function Page
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output/function
 * @Version 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'amp_init', 'saswp_schema_markup_hook_on_init' );
add_action( 'init', 'saswp_schema_markup_hook_on_init');
add_action( 'wp', 'saswp_wp_hook_operation',999 );

function saswp_schema_markup_hook_on_init() {
        
        if(!is_admin() ) {
            
            global $sd_data;
        
            if ( isset( $sd_data['saswp-markup-footer']) && $sd_data['saswp-markup-footer'] == 1){
                
               add_action( 'wp_footer', 'saswp_schema_markup_output');    
               add_action( 'amp_post_template_footer' , 'saswp_schema_markup_output' );
               add_action( 'amphtml_template_footer', 'saswp_schema_markup_output');
               add_action( 'amp_wp_template_footer', 'saswp_schema_markup_output');

               if ( isset( $sd_data['saswp-cmp']) && $sd_data['saswp-cmp'] == 1){
                    add_action( 'cmp-after-footer-scripts', 'saswp_schema_markup_output');  
               }
               
               
            }else{
                
               add_action( 'wp_head', 'saswp_schema_markup_output');  
               add_action( 'amp_post_template_head' , 'saswp_schema_markup_output' );
               add_action( 'amphtml_template_head', 'saswp_schema_markup_output');
               add_action( 'amp_wp_template_head', 'saswp_schema_markup_output');

               if ( isset( $sd_data['saswp-cmp']) && $sd_data['saswp-cmp'] == 1){
                    add_action( 'cmp-before-header-scripts', 'saswp_schema_markup_output');  
               }
                              
            }               
            
            add_action('cooked_amp_head', 'saswp_schema_markup_output');
            
            if(saswp_global_option() ) {

                remove_action( 'amp_post_template_head', 'amp_post_template_add_schemaorg_metadata',99,1);
                remove_action( 'amp_post_template_footer', 'amp_post_template_add_schemaorg_metadata',99,1);  
                remove_action( 'wp_footer', 'orbital_markup_site'); 
                
                add_filter( 'amp_schemaorg_metadata', '__return_empty_array' );
                add_filter( 'hunch_schema_markup', '__return_false');                 
                add_filter( 'electro_structured_data', '__return_false');
                add_filter( 'electro_woocommerce_structured_data', '__return_false');
                
            }
                                    
            if(class_exists('BSF_AIOSRS_Pro_Markup') ) {
                
                if(saswp_global_option() ) {

                    remove_action( 'wp_head', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'schema_markup' ),10);
                    remove_action( 'wp_head', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'global_schemas_markup' ),10);
                    remove_action( 'wp_footer', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'schema_markup' ),10);
                    remove_action( 'wp_footer', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'global_schemas_markup' ),10);

                }                
                
            }
            
            if ( isset( $sd_data['saswp-wp-recipe-maker']) && $sd_data['saswp-wp-recipe-maker'] == 1){
                if(saswp_global_option() ) {
                    add_filter( 'wprm_recipe_metadata', '__return_false' );            
                }                
            }
            if ( isset( $sd_data['saswp-webstories']) && $sd_data['saswp-webstories'] == 1){
                    add_action('web_stories_story_head', 'saswp_schema_markup_output');                     
            }                                               
                                                                                                           
        }                       
}

function saswp_wp_hook_operation() {
    if(!is_admin() ) {
        global $sd_data;
        if ( isset( $sd_data['saswp-microdata-cleanup']) && $sd_data['saswp-microdata-cleanup'] == 1){                
            ob_start("saswp_remove_microdata");                
        }
        ob_start('saswp_schema_markup_output_in_buffer');
    }
    
}

function saswp_schema_markup_output_in_buffer($content){
    
    global $saswp_post_reviews, $saswp_elementor_qanda, $saswp_elementor_faq, $saswp_divi_faq, $saswp_elementor_howto, $saswp_evo_json_ld, $saswp_tiny_multi_faq, $saswp_tiny_howto, $saswp_tiny_recipe;
             
     if($saswp_post_reviews || $saswp_elementor_qanda || $saswp_elementor_faq || $saswp_divi_faq || $saswp_elementor_howto || $saswp_evo_json_ld || $saswp_tiny_multi_faq || $saswp_tiny_howto || $saswp_tiny_recipe){
        
            $saswp_json_ld_escaped =  saswp_get_all_schema_markup_output();  
            
            
            if ( ! empty( $saswp_json_ld_escaped['saswp_json_ld']) ) {

                if(strpos($content, 'saswp-schema-markup-output') !== false){

                    $regex = '/<script type\=\"application\/ld\+json\" class\=\"saswp\-schema\-markup\-output\"\>(.*?)<\/script>/s'; 
                                
                    preg_match($regex, $content, $match);

                    if ( isset( $match[0]) ) {
                        $content = str_replace($match[0], $saswp_json_ld_escaped['saswp_json_ld'], $content);
                    }

                }else{

                    $content = str_replace('</head>', $saswp_json_ld_escaped['saswp_json_ld'].'</head>', $content);                    

                }                
                 
            }
         
     }
     
    return $content;
}

function saswp_schema_markup_output() {
    global $sd_data;
    $saswp_json_ld_escaped =  saswp_get_all_schema_markup_output();    
    
    if ( ! empty( $saswp_json_ld_escaped['saswp_json_ld']) ) {
        
        echo "\n";
        if ( isset( $sd_data['saswp_remove_version_tag']) && $sd_data['saswp_remove_version_tag'] != 1){
            echo "<!-- Schema & Structured Data For WP v".esc_attr(SASWP_VERSION)." - -->";
        }
        echo "\n";
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- data is already fully escaped using wp_json_encode
        echo $saswp_json_ld_escaped['saswp_json_ld'];
        echo "\n\n";
        
    }
    
    if ( ! empty( $saswp_json_ld_escaped['saswp_custom_json_ld']) ) {
        
        echo "\n";
        if ( isset( $sd_data['saswp_remove_version_tag']) && $sd_data['saswp_remove_version_tag'] != 1){
            echo '<!-- Schema & Structured Data For WP Custom Markup v'.esc_attr(SASWP_VERSION).' - -->';
        }
        echo "\n";
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- data is already fully escaped using wp_json_encode
        echo $saswp_json_ld_escaped['saswp_custom_json_ld'];
        echo "\n\n";
        
    }
    if ( ! empty( $saswp_json_ld_escaped['saswp_user_custom_json_ld']) ) {
        
        echo "\n";
        if ( isset( $sd_data['saswp_remove_version_tag']) && $sd_data['saswp_remove_version_tag'] != 1){
            echo '<!-- Schema & Structured Data For WP Custom Markup v'.esc_attr(SASWP_VERSION).' - -->';
        }
        echo "\n";
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- data is already fully escaped using wp_json_encode
        echo $saswp_json_ld_escaped['saswp_user_custom_json_ld'];
        echo "\n\n";
        
    }

    //Other schema markup compile with SASWP

    if(saswp_global_option()) {

        $wp_tasty_recipe          = saswp_wp_tasty_recipe_json_ld();

        if ( ! empty( $wp_tasty_recipe) ) {

            foreach ( $wp_tasty_recipe as $recipe) {
                if ( isset( $sd_data['saswp_remove_version_tag']) && $sd_data['saswp_remove_version_tag'] != 1){
                    echo '<!-- Schema & Structured Data For WP Other Markup v'.esc_attr(SASWP_VERSION).' - -->';                            
                }
                echo PHP_EOL;
                echo '<script type="application/ld+json" class="saswp-other-schema-markup-output">';
                //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- data is already fully escaped using wp_json_encode
                echo saswp_json_print_format( $recipe);
                echo '</script>';			
                echo PHP_EOL;

            }

        }

    }    
        
}
/**
 * This function collects all the schema markups and show them at one place either header or footer
 * @global type $sd_data
 * @global type json array
 */
function saswp_get_all_schema_markup_output() {
       
        saswp_update_global_post();

        global $sd_data;
        global $post;
        
        $response_html = '';
        $custom_output = '';
        $user_custom_output = '';
       
        $custom_markup            = '';
        $user_custom_markup       = '';
        $output                   = '';        
        $schema_output            = array();
        $kb_schema_output         = array(); 
        $item_list                = array();
        $collection_page          = array(); 
        $blog_page                = array();          
        
        $tinymce_faq              = array();
        $tinymce_how_to           = array();
        $gutenberg_how_to         = array();
        $gutenberg_recipe         = array();
        $gutenberg_qanda          = array();
        $elementor_qanda          = array();
        $gutenberg_faq            = array();
        $live_blog_posting        = array();
        $elementor_faq            = array();
        $elementor_howto          = array();
        $divi_builder_faq         = array();
        $gutenberg_event          = array();
        $gutenberg_job            = array();
        $gutenberg_book           = array();
        $gutenberg_course         = array();
        $kb_website_output        = array();
        
        if( !is_home() && ( is_singular() || is_front_page() || (function_exists('ampforwp_is_front_page') && ampforwp_is_front_page())) ){
            
            $elementor_faq            = saswp_elementor_faq_schema();
            $elementor_qanda          = saswp_elementor_qanda_schema();
            $elementor_howto          = saswp_elementor_howto_schema();
            $divi_builder_faq         = saswp_divi_builder_faq_schema();
            $gutenberg_event          = saswp_gutenberg_event_schema();  
            $gutenberg_qanda          = saswp_gutenberg_qanda_schema();  
            $gutenberg_job            = saswp_gutenberg_job_schema();
            $gutenberg_book           = saswp_gutenberg_book_schema();
            $gutenberg_course         = saswp_gutenberg_course_schema();
            $gutenberg_how_to         = saswp_gutenberg_how_to_schema();
            $tinymce_faq              = saswp_tinymce_faq_schema();
            $tinymce_how_to           = saswp_tinymce_how_to_schema();
            $gutenberg_recipe         = saswp_gutenberg_recipe_schema(); 
            $gutenberg_faq            = saswp_gutenberg_faq_schema();        
            $live_blog_posting        = saswp_gutenberg_live_blog_posting_schema();        

        }        
        $taqeem_schema            = saswp_taqyeem_review_rich_snippet(); 
        $wp_product_rv            = saswp_wp_product_review_lite_rich_snippet();
        $schema_for_faqs          = saswp_schema_for_faqs_schema();
        $faqschemaforpost         = saswp_faqschemaforpost_schema(); 
        $wpfaqschemamarkup        = saswp_wpfaqschemamarkup_schema();         
        $woo_cat_schema           = saswp_woocommerce_category_schema();  
        $woo_shop_page            = saswp_woocommerce_shop_page();  
        $site_navigation          = saswp_site_navigation_output();     
        $contact_page_output      = saswp_contact_page_output();  	
        $about_page_output        = saswp_about_page_output();      
        $author_output            = saswp_author_output();
        $archive_output           = saswp_archive_output();        
        $collection_output        = saswp_fetched_reviews_json_ld();
        $default_videoObject_schema        = saswp_default_video_object_schema();
        
        if($archive_output){
            
            if(empty($woo_cat_schema) ) {
                $item_list            = $archive_output[0];
            }
            
            $collection_page          = isset($archive_output[1]) ? $archive_output[1]: array(); 
            $blog_page                = isset($archive_output[1]) ? $archive_output[2]: array(); 
        }
                     
        $schema_breadcrumb_output = saswp_schema_breadcrumb_output();                      
                         
        if((is_home() || is_front_page() || ( function_exists('ampforwp_is_home') && ampforwp_is_home())) ||  (isset($sd_data['saswp-defragment']) && $sd_data['saswp-defragment'] == 1 && is_singular()) ) {
            
               $kb_website_output        = saswp_kb_website_output();  
               $kb_schema_output         = saswp_kb_schema_output();
        }
                 
        $user_custom_markup        = saswp_fetched_user_custom_schema();

        if(saswp_global_custom_schema_option() ) {         
            $custom_markup             = saswp_taxonomy_schema_output();    
            if( is_singular() && is_object($post) ){
                $custom_markup         = get_post_meta($post->ID, 'saswp_custom_schema_field', true);            
            }

            if($custom_markup){    
                
                $cus_regex = '/\<script type\=\"application\/ld\+json\"\>/';
                preg_match( $cus_regex, $custom_markup, $match );
                
                if(empty($match) ) {
                    json_decode($custom_markup);
                    if(json_last_error() === JSON_ERROR_NONE){
                        $custom_output .= '<script type="application/ld+json" class="saswp-custom-schema-markup-output">';                            
                        $custom_output .= $custom_markup;                            
                        $custom_output .= '</script>';
                    }                                        
                }else{
                    $regex = '/<script type=\"application\/ld\+json">(.*?)<\/script>/s';
                    preg_match_all( $regex, $custom_markup, $matches );
                    if ( ! empty( $matches) && isset($matches[1]) ) {
                        foreach ( $matches[1] as $value) {
                            json_decode($value);
                            if(json_last_error() === JSON_ERROR_NONE){
                                $custom_output .= '<script type="application/ld+json" class="saswp-custom-schema-markup-output">';                            
                                $custom_output .= $value;                            
                                $custom_output .= '</script>';
                            }                            
                        }
                    } 
                    
                }
                                                                                                                                                                                                                              
            }

        }        
         
        $schema_output              = saswp_schema_output();                  
        
	if(saswp_global_option()) {
		                                  
                        if ( ! empty( $contact_page_output) ) {
                          
                            $output .= saswp_json_print_format($contact_page_output); 
                            $output .= ",";
                            $output .= "\n\n";                        
                        }			                        
                        if ( ! empty( $default_videoObject_schema) ) {
                          
                            $output .= saswp_json_print_format($default_videoObject_schema); 
                            $output .= ",";
                            $output .= "\n\n";                        
                        }			                        
                        if ( ! empty( $about_page_output) ) {
                        
                            $output .= saswp_json_print_format($about_page_output);    
                            $output .= ",";
                            $output .= "\n\n";
                        }                        
                        if ( ! empty( $author_output) ) {
                           
                            $output .= saswp_json_print_format($author_output); 
                            $output .= ",";
                            $output .= "\n\n";
                        }                                              
                        if ( ! empty( $collection_page) ) {
                        
                            $output .= saswp_json_print_format($collection_page);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $woo_shop_page['itemlist']) ) {
                        
                            $output .= saswp_json_print_format($woo_shop_page['itemlist']);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $woo_shop_page['collection']) ) {
                        
                            $output .= saswp_json_print_format($woo_shop_page['collection']);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $item_list) ) {
                        
                            $output .= saswp_json_print_format($item_list);   
                            $output .= ",";
                            $output .= "\n\n";
                        }                        
                        if ( ! empty( $woo_cat_schema) ) {
                        
                            $output .= saswp_json_print_format($woo_cat_schema);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $gutenberg_how_to) ) {
                        
                            $output .= saswp_json_print_format($gutenberg_how_to);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $tinymce_how_to) ) {
                        
                            $output .= saswp_json_print_format($tinymce_how_to);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $tinymce_faq) ) {
                        
                            $output .= saswp_json_print_format($tinymce_faq);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $gutenberg_recipe) ) {
                        
                            $output .= saswp_json_print_format($gutenberg_recipe);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $gutenberg_faq) ) {
                        
                            $output .= saswp_json_print_format($gutenberg_faq);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $live_blog_posting) ) {
                        
                            $output .= saswp_json_print_format( $live_blog_posting );   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $schema_for_faqs) ) {
                        
                            $output .= saswp_json_print_format($schema_for_faqs);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $faqschemaforpost) ) {
                        
                            $output .= saswp_json_print_format($faqschemaforpost);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $wpfaqschemamarkup) ) {
                        
                            $output .= saswp_json_print_format($wpfaqschemamarkup);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $taqeem_schema) ) {
                        
                            $output .= saswp_json_print_format($taqeem_schema);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $wp_product_rv) ) {
                        
                            $output .= saswp_json_print_format($wp_product_rv);   
                            $output .= ",";
                            $output .= "\n\n";
                        }                        
                        if ( ! empty( $elementor_faq) ) {
                        
                            $output .= saswp_json_print_format($elementor_faq);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $elementor_qanda) ) {
                        
                            $output .= saswp_json_print_format($elementor_qanda);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $elementor_howto) ) {
                        
                            $output .= saswp_json_print_format($elementor_howto);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $divi_builder_faq) ) {
                        
                            $output .= saswp_json_print_format($divi_builder_faq);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $gutenberg_course) ) {
                        
                            $output .= saswp_json_print_format($gutenberg_course);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $gutenberg_event) ) {
                        
                            $output .= saswp_json_print_format($gutenberg_event);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $gutenberg_qanda) ) {
                        
                            $output .= saswp_json_print_format($gutenberg_qanda);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $gutenberg_job) ) {
                        
                            $output .= saswp_json_print_format($gutenberg_job);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $gutenberg_book) ) {
                        
                            $output .= saswp_json_print_format($gutenberg_book);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $collection_output) ) {
                        
                            $output .= saswp_json_print_format($collection_output);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if ( ! empty( $blog_page) ) {
                        
                            $output .= saswp_json_print_format($blog_page);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                                    
            if ( isset( $sd_data['saswp-defragment']) && $sd_data['saswp-defragment'] == 1){
            
                $output_schema_type_id = array();
                
                if ( ! empty( $schema_output) ) {
                    
                foreach( $schema_output as $soutput){
            
                    $s_type         =   '';

                    if( ! empty( $soutput['@type'] ) ) {
                        $s_type                     = $soutput['@type'];
                        $output_schema_type_id[]    = $s_type;
                    }
                    
                    if($s_type == 'BlogPosting'|| $s_type == 'Article' || $s_type == 'ScholarlyArticle' || $s_type == 'TechArticle' || $s_type == 'NewsArticle'){
                        
                    
                    $final_output = array();
                    $object   = new SASWP_Output_Service();
                    $webpage  = $object->saswp_schema_markup_generator('WebPage');
                    
                        unset($soutput['@context']);                   
                        unset($schema_breadcrumb_output['@context']);
                        unset($webpage['mainEntity']);
                        unset($kb_schema_output['@context']);                        
                        unset($kb_website_output['@context']); 
                        
                        if ( isset( $sd_data['saswp_kb_type']) ) {
                           
                            $kb_schema_output['@type'] = $sd_data['saswp_kb_type'];
                            
                            if($sd_data['saswp_kb_type'] == 'Organization'){
                                $kb_schema_output['@type'] = (isset($sd_data['saswp_organization_type']) && !empty($sd_data['saswp_organization_type']) && strpos($sd_data['saswp_organization_type'], 'Organization') !== false ) ? $sd_data['saswp_organization_type'] : 'Organization';
                            }
                                                        
                        }else{
                            $kb_schema_output['@type'] = 'Organization';
                        }
                        
                     if($webpage){
                    
                         $soutput['isPartOf'] = array(
                            '@id' => $webpage['@id']
                        );
                         
                         $webpage['primaryImageOfPage'] = array(
                             '@id' => saswp_get_permalink().'#primaryimage'
                         );
                         
                         if(array_key_exists('@graph', $site_navigation) ) {                             
                             unset($site_navigation['@context']);                                                       
                             $webpage['mainContentOfPage'] = array($site_navigation['@graph']);
                         }                         
                         
                     }       
                                        
                    $soutput['mainEntityOfPage'] = $webpage['@id'];
                                        
                    if($kb_website_output){
                    
                        $webpage['isPartOf'] = array(
                        '@id' => $kb_website_output['@id']
                        );
                        
                    }
                                        
                    if($schema_breadcrumb_output){
                        $webpage['breadcrumb'] = array(
                        '@id' => $schema_breadcrumb_output['@id']
                    );
                    }
                    
                    if($kb_schema_output){
                    
                        if($kb_website_output){
                            
                            if ( ! empty( $kb_schema_output['@id']) ) {

                                    $kb_website_output['publisher'] = array(
                                        '@id' => $kb_schema_output['@id']
                                    );
                            }
                                                        
                        }
                        if($sd_data['saswp_kb_type'] == 'Organization'){                                                                             
                            
                            if ( ! empty( $kb_schema_output['@id']) ) {

                                $soutput['publisher'] = array(
                                    '@id' => $kb_schema_output['@id']
                                );

                            }
                                                        
                        }
                        
                    }
                                        
                    $final_output['@context']   = saswp_context_url();

                    $final_output['@graph'][]   = $kb_schema_output;
                    $final_output['@graph'][]   = $kb_website_output;                    

                    $final_output['@graph'][]   = $webpage;
                   
                    if($schema_breadcrumb_output){
                        $final_output['@graph'][]   = $schema_breadcrumb_output;
                    }
                    
                    $final_output['@graph'][]   = $soutput;

                    $final_output['@graph'] = array_filter($final_output['@graph']);
                    $final_output['@graph'] = array_values($final_output['@graph']);
                        
                    $schema = saswp_json_print_format($final_output);
                    $output .= $schema; 
                    $output .= ",";
                    $output .= "\n\n";     
                    
                    }else{
                        
                        $schema = saswp_json_print_format($soutput);
                        $output .= $schema; 
                        $output .= ",";
                        $output .= "\n\n"; 
                        
                    }
                                                                                                          
            }
            }                                 
                if(in_array('BlogPosting', $output_schema_type_id) || in_array('Article', $output_schema_type_id) || in_array('TechArticle', $output_schema_type_id) || in_array('NewsArticle', $output_schema_type_id) ){                                                                                            
                }else{
                    if ( ! empty( $site_navigation) ) {
                                                                            
                        $output .= saswp_json_print_format($site_navigation);   
                        $output .= ",";
                        $output .= "\n\n";                        
                    }
                    if ( ! empty( $kb_website_output) ) {
                        
                            $output .= saswp_json_print_format($kb_website_output);  
                            $output .= ",";
                            $output .= "\n\n";
                        }
                    if ( ! empty( $schema_breadcrumb_output) ) {
                        
                            $output .= saswp_json_print_format($schema_breadcrumb_output);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                    if ( ! empty( $kb_schema_output) ) {
                            
                            $output .= saswp_json_print_format($kb_schema_output);
                            $output .= ",";                        
                        }   
                }
            
                
            }else{
                          
                        if ( ! empty( $site_navigation) ) {
                                                                            
                            $output .= saswp_json_print_format($site_navigation);   
                            $output .= ",";
                            $output .= "\n\n";                        
                        }
                        
                        if ( ! empty( $kb_website_output) ) {
                        
                            $output .= saswp_json_print_format($kb_website_output);  
                            $output .= ",";
                            $output .= "\n\n";
                        }                         
                        if ( ! empty( $schema_breadcrumb_output) ) {
                        
                            $output .= saswp_json_print_format($schema_breadcrumb_output);   
                            $output .= ",";
                            $output .= "\n\n";
                        }                        
                        if ( ! empty( $schema_output) ) { 
                            
                            foreach( $schema_output as $schema){
                                
                                $schema = saswp_json_print_format($schema);
                                $output .= $schema; 
                                $output .= ",";
                                $output .= "\n\n";   
                                
                            }                            
                        }                        
                        if ( ! empty( $kb_schema_output) ) {
                            
                            $output .= saswp_json_print_format($kb_schema_output);
                            $output .= ",";                        
                        }       
                
            }            
            if($user_custom_markup){    
                
                        $cus_regex = '/\<script type\=\"application\/ld\+json\"\>/';
                        preg_match( $cus_regex, $user_custom_markup, $match );
                        
                        if(empty($match) ) {
                            json_decode($user_custom_markup);
                            if(json_last_error() === JSON_ERROR_NONE){
                                $user_custom_output .= '<script type="application/ld+json" class="saswp-user-custom-schema-markup-output">';                            
                                $user_custom_output .= $user_custom_markup;                            
                                $user_custom_output .= '</script>';   
                            }                            
                            
                        }else{
                            
                            $regex = '/<script type=\"application\/ld\+json">(.*?)<\/script>/';
                            preg_match_all( $regex, $user_custom_markup, $matches );
                            if ( ! empty( $matches) && isset($matches[1]) ) {
                                foreach ( $matches[1] as $value) {
                                    json_decode($value);
                                    if(json_last_error() === JSON_ERROR_NONE){
                                        $user_custom_output .= '<script type="application/ld+json" class="saswp-user-custom-schema-markup-output">';                            
                                        $user_custom_output .= $value;
                                        $user_custom_output .= '</script>';   
                                    }                            
                                }
                            }                            
                            
                        }
                                                                                                                                                                                                                                              
            }
                                                			              		
	}
                        
        if($output){
            
            $stroutput = '['. trim($output). ']';
            $filter_string = str_replace(',]', ']',$stroutput);               
            $response_html.= '<script type="application/ld+json" class="saswp-schema-markup-output">'; 
            $response_html.= "\n";       
            $response_html.= $filter_string;       
            $response_html.= "\n";
            $response_html.= '</script>';            
        }
        
        return array(
                    'saswp_json_ld'        => $response_html, 
                    'saswp_custom_json_ld' => $custom_output,
                    'saswp_user_custom_json_ld' => $user_custom_output,
                );
                
}

add_filter( 'the_content', 'saswp_paywall_data_for_login');

function saswp_paywall_data_for_login($content){
    
        global $wp;
        
	if( saswp_non_amp() ){
            
		return $content;
                
	}
        
	remove_filter('the_content', 'MeprAppCtrl::page_route', 60);	
	$Conditionals = saswp_get_all_schema_posts();     
        
	if(!$Conditionals){
		return $content;
	}else{
               
                $paywallenable = '';
                $className     = 'paywall';
                
                foreach( $Conditionals as $schemaConditionals){
                    
                     $schema_options = $schemaConditionals['schema_options'];    
               
                if ( isset( $schema_options['paywall_class_name']) ) {
                    
                     $className = $schema_options['paywall_class_name'];                                 
                
                }
                if ( isset( $schema_options['notAccessibleForFree']) ) {               
                    
                     $paywallenable = $schema_options['notAccessibleForFree'];
                     
                break;
                
                }    
                
                }                
                if($paywallenable){
                    
		if(strpos($content, '<!--more-->')!==false && !is_user_logged_in() ) {
                    			
			$redirect       =  home_url( $wp->request );
			$breakedContent = explode("<!--more-->", $content);
			$content        = $breakedContent[0].'<a href="'. esc_url( wp_login_url( $redirect )) .'">'.esc_html__( 'Login', 'schema-and-structured-data-for-wp' ).'</a>';
                        
		}elseif(strpos($content, '<!--more-->')!==false && is_user_logged_in() ) {
                    			
			$redirect       =  home_url( $wp->request );
			$breakedContent = explode("<!--more-->", $content);
			$content        = $breakedContent[0].'<div class="'. esc_attr( $className).'">'.$breakedContent[1].'</div>';
                        
		}
                
                }
                
	}
	return $content;
}

add_filter( 'memberpress_form_update', 'saswp_memberpress_form_update'); 
        
function saswp_memberpress_form_update($form){
    
	if( !saswp_non_amp() ){
            
		add_action('amp_post_template_css',function() {
			echo '.amp-mem-login{background-color: #fef5c4;padding: 13px 30px 9px 30px;}';
		},11); 
		global $wp;
		$redirect =  home_url( $wp->request );
		$form = '<a class="amp-mem-login" href="'. esc_url( wp_login_url( $redirect )) .'">'.esc_html__( 'Login', 'schema-and-structured-data-for-wp' ).'</a>';
	}
        
	return $form;
}

/**
 * Function to remove the undefined index notices
 * @param type $data
 * @param type $index
 * @param type $type
 * @return string
 */
function saswp_remove_warnings($data, $index, $type){     
    	
                if($type == 'saswp_array'){

                        if ( isset( $data[$index]) ) {
                                return esc_attr( $data[$index][0]);
                        }else{
                                return '';
                        }		
                }

		if($type == 'saswp_string'){
	
                        if ( isset( $data[$index]) ) {
                                return esc_attr( $data[$index]);
                        }else{
                                return '';
                        }		
	        }    
}

/**
 * Gets the total word count and expected time to read the article
 * @return type array
 */
function saswp_reading_time_and_word_count() {
    
    global $post;
    // Predefined words-per-minute rate.
    $words_per_minute = 225;
    $words_per_second = $words_per_minute / 60;

    // Count the words in the content.
    $word_count      = 0;
    $seconds         = 0;
    $text            = trim( wp_strip_all_tags( @get_the_content() ) );
    
    if(!$text && is_object($post) ) {
        $text = $post->post_content;
    }  

    if ( ! empty( $text ) ) {  
        $word_count      = substr_count( "$text ", ' ' );
    }
    if ( $word_count > 0 ) {
        // How many seconds (total)?
        $seconds = floor( $word_count / $words_per_second );
    }
    
    $timereq = '';

    if($seconds > 60){

        $minutes      = floor($seconds/60);        
        $seconds_left = $seconds % 60;
        
        $timereq = 'PT'.$minutes.'M'.$seconds_left.'S';

    }else{
        $timereq = 'PT'.$seconds.'S';
    }

    return array('word_count' => esc_attr( $word_count), 'timerequired' => esc_attr( $timereq));
}

/**
 * Extracting the value of star ratings plugins on current post
 * @global type $sd_data
 * @param type $id
 * @return type array
 */
function saswp_extract_taqyeem_ratings() {
        
    global $sd_data, $post;    
    $star_rating = array();
    
    if ( isset( $sd_data['saswp-taqyeem']) && $sd_data['saswp-taqyeem'] == 1 && function_exists('taqyeem_review_get_rich_snippet') ) {
       
        $rate  = get_post_meta( $post->ID, 'tie_user_rate', true );
		$count = get_post_meta( $post->ID, 'tie_users_num', true );
         
        if( ! empty( $rate ) && ! empty( $count ) ){

            $totla_users_score = round( $rate/$count, 2 );
			$totla_users_score = ( $totla_users_score > 5 ) ? 5 : $totla_users_score;
           
            $star_rating['@type']        = 'AggregateRating';
            $star_rating['ratingValue']  = $totla_users_score;
            $star_rating['reviewCount']  = $count;                                                                                              
            
        }else{

            $total_score = (int) get_post_meta( $post->ID, 'taq_review_score', true );

            if( ! empty( $total_score ) && $total_score > 0 ){
                $total_score = round( ($total_score*5)/100, 1 );
            }

            $star_rating['@type']        = 'AggregateRating';
            $star_rating['ratingValue']  = $total_score;
            $star_rating['reviewCount']  = 1;                                                                                              

        }
        
    } 
    return $star_rating;                       
}

function saswp_ratency_rating_box_rating() {
        
    global $sd_data, $post;    
    $result = array();

    if( isset($sd_data['saswp-ratency']) && $sd_data['saswp-ratency'] == 1 ){
                       
        $ratency_total_rv = get_post_meta($post->ID, 'progression_studios_review_total', true);
         
        if( $ratency_total_rv ){
                       
            $result['@type']       = 'AggregateRating';            
            $result['ratingCount'] = 1;
            $result['ratingValue'] = $ratency_total_rv;  
            $result['bestRating']  = 10;
            $result['worstRating'] = 1;                                                         
            
            return $result;
            
        }else{
            
            return array();    
            
        }
        
    }else{
        
        return array();
        
    }                        
}

/**
 * Extracting the value of yet another star rating plugins on current post
 * @global type $sd_data, $post
 * @param type $id
 * @return type array
 */
function saswp_extract_yet_another_stars_rating() {
    global $sd_data, $post;
    $result = array();

    if ( isset( $sd_data['saswp-yet-another-stars-rating']) && $sd_data['saswp-yet-another-stars-rating'] == 1 && method_exists('YasrDatabaseRatings', 'getVisitorVotes') ){

        $visitor_votes  = YasrDatabaseRatings::getVisitorVotes(false);

        if( $visitor_votes && ($visitor_votes['sum_votes'] != 0 && $visitor_votes['number_of_votes'] != 0) ){

            $average_rating = $visitor_votes['sum_votes'] / $visitor_votes['number_of_votes'];
            $average_rating = round($average_rating, 1);

            $result['@type']       = 'AggregateRating';            
            $result['ratingCount'] = $visitor_votes['number_of_votes'];
            $result['ratingValue'] = $average_rating;  
            $result['bestRating']  = 5;
            $result['worstRating'] = 1;

            return $result;
        } elseif ( method_exists('YasrCommentsRatingData', 'getCommentStats') ) {
            $ratingData = new YasrCommentsRatingData;
            $stats = $ratingData->getCommentStats($post->ID);
            if ( isset($stats['n_of_votes']) && $stats['n_of_votes'] != 0 ) {
                $result['@type']       = 'AggregateRating';
                $result['ratingCount'] = $stats['n_of_votes'];
                $result['ratingValue'] = $stats['average'];
                $result['bestRating']  = 5;
                $result['worstRating'] = 1;
                return $result;
            }
        }

    }
    return array();
}


/**
 * Extracting the value of wpdiscuz plugins on current post
 * @global type $sd_data
 * @param type $id
 * @return type array
 */
function saswp_extract_wpdiscuz() {
        
    global $sd_data, $post;    
    $star_rating = array();

    if ( isset( $sd_data['saswp-wpdiscuz']) && $sd_data['saswp-wpdiscuz'] == 1 && is_plugin_active('wpdiscuz/class.WpdiscuzCore.php') ){
           
        if(is_object($post) && isset($post->ID) ) {
            $rating = (float) get_post_meta($post->ID, 'wpdiscuz_post_rating', true);
            $count = (int) get_post_meta($post->ID, 'wpdiscuz_post_rating_count', true);
             
            if($rating){
               
                $star_rating['@type']        = 'AggregateRating';
                $star_rating['bestRating']   = 5;
                $star_rating['worstRating']  = 1;            
                $star_rating['ratingCount'] = $count;
                $star_rating['ratingValue'] = $rating;                                                           
                
                return $star_rating;
                
            }else{
                
                return array();    
                
            }
        }else{

            return array();   

        }
        
    }else{
        
        return array();
        
    }                        
}

/**
 * Extracting the value of rating form plugins on current post
 * @global type $sd_data
 * @param type $id
 * @return type array
 */
function saswp_extract_ratingform() {
    
    global $sd_data;    
    $star_rating = array();

    if ( isset( $sd_data['saswp-ratingform']) && $sd_data['saswp-ratingform'] == 1 && is_plugin_active('rating-form/rf-init.php') ) {                
        
        $total = get_post_meta(get_the_ID(), 'rf_total', true) ? ((int) get_post_meta(get_the_ID(), 'rf_total', true)) : 0;
        $avg   = get_post_meta(get_the_ID(), 'rf_average', true) ? ((int) get_post_meta(get_the_ID(), 'rf_average', true)) : 0;
        
         
        if( $total > 0 ){
           
            $star_rating['@type']        = 'AggregateRating';
            $star_rating['bestRating']   = 5;
            $star_rating['worstRating']  = 1;            
            $star_rating['ratingCount'] = $total;
            $star_rating['ratingValue'] = $avg;      
            
            return $star_rating;
            
        }else{
            
            return array();    
            
        }
        
    }else{
        
        return array();
        
    }                        
}

function saswp_get_elementor_testomonials() {

            global $sd_data;    
            
            if( isset($sd_data['saswp-elementor']) && $sd_data['saswp-elementor'] == 1 && is_plugin_active('elementor/elementor.php') ){
               
                $alldata    = get_post_meta( get_the_ID(),'_elementor_data', true );
                if ( ! empty( $alldata) && is_string($alldata) ) {
                    $alldata    = json_decode($alldata, true);
                }
            
                $returnData = array();
                $reviews = array();
                $ratings = array();

                if( !empty($alldata) && is_array($alldata) ) {
            
                    foreach ( $alldata as $element_data ) {
                        $returnData[] = saswp_get_elementor_widget_data($element_data, 'testimonial');
                    }
                    
                    if( !empty($returnData) && is_array($returnData) ) {

                        foreach ( $returnData as $value ) {
                        
                            if ( ! empty( $value['settings']['testimonial_name']) ) {

                                $reviews[] = array(
                                    '@type'         => 'Review',
                                    'author'        => array('@type'=> 'Person', 'name' => $value['settings']['testimonial_name']),
                                    'description'   => isset($value['settings']['testimonial_content']) ? $value['settings']['testimonial_content'] : '',
                                    'reviewRating'  => array(
                                                       '@type'	        => 'Rating',
                                                       'bestRating'	    => '5',
                                                       'ratingValue'	=> '5',
                                                       'worstRating'	=> '1',
                                          )
                                );

                            }
                                
                        }
    
                        if ( ! empty( $reviews) ) {

                            $ratings['aggregateRating'] =  array(
                                '@type'         => 'AggregateRating',
                                'ratingValue'	=> '5',
                                'reviewCount'   => count($reviews)
                            );

                        }
                        
                    }                    

                }
                
                return array('reviews' => $reviews, 'rating' => $ratings);

            }
                        
}

function saswp_extract_rmp_ratings() {
        
    global $sd_data;    
    $star_rating = array();
    if ( isset( $sd_data['saswp-rmprating']) && $sd_data['saswp-rmprating'] == 1 && is_plugin_active('rate-my-post/rate-my-post.php') ) {
       
        
        $avg   = get_post_meta(get_the_ID(), 'rmp_avg_rating', true) ? ( get_post_meta(get_the_ID(), 'rmp_avg_rating', true)) : 0;
        $votes = get_post_meta(get_the_ID(), 'rmp_vote_count', true) ? ((int) get_post_meta(get_the_ID(), 'rmp_vote_count', true)) : 0;
                 
        if($votes>0){
                        
            $star_rating['@type']       = 'AggregateRating';
            $star_rating['bestRating']  = 5;
            $star_rating['worstRating'] = 1;
            $star_rating['ratingCount'] = $votes;
            $star_rating['ratingValue'] = $avg;                                                           
            
            return $star_rating;
            
        }else{
            
            return array();    
            
        }
        
    }else{
        
        return array();
        
    }                        
}

/**
 * Extracting the value of star ratings plugins on current post
 * @global type $sd_data
 * @param type $id
 * @return type array
 */
function saswp_extract_kk_star_ratings() {
        
            global $sd_data;    
            $kk_star_rating = array();
            if ( isset( $sd_data['saswp-kk-star-raring']) && $sd_data['saswp-kk-star-raring'] == 1 && is_plugin_active('kk-star-ratings/index.php') ) {
               
                $best  = get_option('kksr_stars');
                $score = get_post_meta(get_the_ID(), '_kksr_ratings', true) ? ((int) get_post_meta(get_the_ID(), '_kksr_ratings', true)) : 0;
                $votes = get_post_meta(get_the_ID(), '_kksr_casts', true) ? ((int) get_post_meta(get_the_ID(), '_kksr_casts', true)) : 0;
                $avg   = $score && $votes ? round((float)(($score/$votes)*($best/5)), 1) : 0;                               
                 
                if($votes>0){
                   
                    $kk_star_rating['@type']       = 'AggregateRating';
                    $kk_star_rating['bestRating']  = $best;
                    $kk_star_rating['ratingCount'] = $votes;
                    $kk_star_rating['ratingValue'] = $avg;                                                           
                    
                    return $kk_star_rating;
                    
                }else{
                    
                    return array();    
                    
                }
                
            }else{
                
                return array();
                
            }                        
       }
       
/**
 * Extracting the value of wp-post-rating ratings plugins on current post
 * @global type $sd_data
 * @param type $id
 * @return type array
 */
function saswp_extract_wp_post_ratings() {
        
            global $sd_data;    
            
            $wp_post_rating_ar = array();
            
            if ( isset( $sd_data['saswp-wppostratings-raring']) && $sd_data['saswp-wppostratings-raring'] == 1 && is_plugin_active('wp-postratings/wp-postratings.php') ) {
               
                $best   = (int) get_option( 'postratings_max' );
                $avg   = get_post_meta(get_the_ID(), 'ratings_average', true);
                $votes = get_post_meta(get_the_ID(), 'ratings_users', true);                
                
                if($votes>0){
                    
                    $wp_post_rating_ar['@type']       = 'AggregateRating';
                    $wp_post_rating_ar['bestRating']  = $best;
                    $wp_post_rating_ar['ratingCount'] = $votes;
                    $wp_post_rating_ar['ratingValue'] = $avg;                                                           
                    
                    return $wp_post_rating_ar;
                }else{
                    
                    return array();    
                    
                }
                
            }else{
                
                return array();
                
            }                        
       }       

/**
 * Gets all the comments of current post
 * @param type $post_id
 * @return type array
 */       
function saswp_get_comments($post_id){
    
    /**
     * If Display comment on post is disabled in amp settings for amp post then don't add comments to schema markup
     * https://github.com/ahmedkaludi/schema-and-structured-data-for-wp/issues/2199
     * @since 1.38
     * */
    if ( ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) { 

        if ( ( function_exists( 'ampforwp_get_setting' ) && false == ampforwp_get_setting( 'ampforwp-display-on-posts' ) ) || ( function_exists( 'amp_is_legacy' ) && amp_is_legacy() ) ) {
            return array();
        }

    }

    global $sd_data;
    $wpdiscuz = false;

    if ( isset( $sd_data['saswp-wpdiscuz']) && $sd_data['saswp-wpdiscuz'] == 1 && is_plugin_active('wpdiscuz/class.WpdiscuzCore.php') ){
        $wpdiscuz = true;
    }
    
    $comments      = array();
    $post_comments = array();   
    
    $is_bbpress = false;
    
    if ( isset( $sd_data['saswp-bbpress']) && $sd_data['saswp-bbpress'] == 1 ){
        global $saswp_bb_topic;
        if ( is_object( $saswp_bb_topic ) && ! empty( $saswp_bb_topic->ID ) ) {
            $post_id    =   $saswp_bb_topic->ID;
        }
        if ( get_post_type( $post_id ) == 'topic' ) {
            $is_bbpress = true;
        }
    }
   
    if($is_bbpress){  
                                     
              $replies_query = array(                   
                 'post_type'      => 'reply',                     
              );                
                              
             if ( bbp_has_replies( $replies_query ) ) :
                 
        while ( bbp_replies() ) : bbp_the_reply();

                    $post_comments[] = (object) array(                            
                                    'comment_date'           => get_post_time( DATE_ATOM, false, bbp_get_reply_id(), true ),
                                    'comment_content'        => bbp_get_reply_content(),
                                    'comment_author'         => bbp_get_reply_author(),
                                    'comment_author_url'     => bbp_get_reply_author_url(),
                                    'comment_ID'             => bbp_get_reply_id(),
                    );
                                                                                 
            endwhile;
                    wp_reset_postdata();                                                  
                endif;
                                        
    }else{            
                    $post_comments = get_comments( array( 
                                        'post_id' => $post_id,                                            
                                        'status'  => 'approve',
                                        'type'    => 'comment' 
                                    ) 
                                );   
                                
    }                                                                                                                                                                                          
      
    if ( count( $post_comments ) ) {
        
    $permalink = get_permalink();    
        
    foreach ( $post_comments as $comment ) {
        
        $likes    = 0;
        $dislikes = 0; 
        
        if($wpdiscuz){

            $wpdiscuz_votes =  get_comment_meta($comment->comment_ID, 'wpdiscuz_votes_seperate', true);
            
            if ( isset( $wpdiscuz_votes['like']) ) {
                $likes = $wpdiscuz_votes['like'];
            }

            if ( isset( $wpdiscuz_votes['dislike']) ) {
                $dislikes = $wpdiscuz_votes['dislike'];
            }
        }
       
        $comment_id  = isset( $comment->comment_ID ) ? $comment->comment_ID : '';
        $each_comment                       =   array();
        $each_comment['@type']              =   'Comment';   
        $each_comment['id']                 =   $permalink.'#comment-'.$comment_id;
        $each_comment['dateCreated']        =   $is_bbpress ? $comment->comment_date : saswp_format_date_time($comment->comment_date);
        $each_comment['description']        =   wp_strip_all_tags($comment->comment_content);
        if ( $wpdiscuz ) {
            $each_comment['upvoteCount']    =   $likes;
            $each_comment['downvoteCount']  =   $dislikes;
        }
        $each_comment['author']             =   array (
                                                '@type' => 'Person',
                                                'name'  => esc_attr( $comment->comment_author),
                                                'url'   => isset($comment->comment_author_url) ? esc_url($comment->comment_author_url): '',
                                            );
        $comments[]                         =   $each_comment;    
    }
            
    return apply_filters( 'saswp_filter_comments', $comments );
}
    
}       
/**
 * Gets all the comments of current post
 * @param type $post_id
 * @return type array
 */       
function saswp_get_comments_with_rating() {
    
        global $post;
        
        $comments      = array();
        $ratings       = array();
        $post_comments = array();   
        $response      = array();
               
        $post_comments = get_comments( array( 
            'post_id' => $post->ID,                                            
            'status'  => 'approve',
            'type'    => 'comment',
            'parent'  => 0 
        ) 
      );                                                                                                                                                                              
      
        $starsrating        = saswp_check_starsrating_status();
        $stars_rating_moved = get_option('saswp_imported_starsrating');

        if ( count( $post_comments ) ) {

        $sumofrating = 0;
        $avg_rating  = 1;
            
		foreach ( $post_comments as $comment ) {                        

            if($starsrating || $stars_rating_moved){
                $rating = get_comment_meta($comment->comment_ID, 'rating', true);
                if($stars_rating_moved && !$rating){
                    $rating = get_comment_meta($comment->comment_ID, 'review_rating', true);
                }
            }else{
                $rating = get_comment_meta($comment->comment_ID, 'review_rating', true);
            }
            
            if($rating < 1){
                $rating = 1;
            }

            if(is_numeric($rating) ) {

                $sumofrating += $rating;

                $comments[] = array (
					'@type'         => 'Review',
					'datePublished' => saswp_format_date_time($comment->comment_date),
					'description'   => wp_strip_all_tags($comment->comment_content),
					'author'        => array (
                                            '@type' => 'Person',
                                            'name'  => esc_attr( $comment->comment_author),
                                            'url'   => isset($comment->comment_author_url) ? esc_url($comment->comment_author_url): '',
                                    ),
                    'reviewRating'  => array(
                            '@type'	        => 'Rating',
                            'bestRating'	=> '5',
                            'ratingValue'	=> $rating,
                            'worstRating'	=> '1',
               )
            );
            
            if($sumofrating> 0){
                $avg_rating = $sumofrating /  count($comments); 
            }
            if($avg_rating < 1){
                $avg_rating = 1;
            }
            
            $ratings =  array(
                    '@type'         => 'AggregateRating',
                    'ratingValue'	=> $avg_rating,
                    'reviewCount'   => count($comments)
            );

            }            			
        }                
                		
    }

    if($comments){
        $response = array('reviews' => $comments, 'ratings' => $ratings);
    }
    
    return apply_filters( 'saswp_filter_comments_with_rating',  $response);        
}       

/**
 * Function to enqueue AMP script in head
 * @param type $data
 * @return string
 */
function saswp_structure_data_access_scripts($data){
    
	if ( empty( $data['amp_component_scripts']['amp-access'] ) ) {
		$data['amp_component_scripts']['amp-access'] = 'https://cdn.ampproject.org/v0/amp-access-0.1.js';
	}
	if ( empty( $data['amp_component_scripts']['amp-analytics'] ) ) {
		$data['amp_component_scripts']['amp-analytics'] = "https://cdn.ampproject.org/v0/amp-analytics-0.1.js";
	}
	if ( empty( $data['amp_component_scripts']['amp-mustache'] ) ) {
		$data['amp_component_scripts']['amp-mustache'] = "https://cdn.ampproject.org/v0/amp-mustache-0.1.js";
	}
	return $data;
        
}
/**
 * Function generates list items for the breadcrumbs schema markup
 * @global type $sd_data
 * @return array
 */
function saswp_list_items_generator() {
    
		global $sd_data;
		$bc_titles = array();
		$bc_links  = array();
        $settings = saswp_defaultSettings(); 

        if(empty($sd_data['links']) ) {
            saswp_custom_breadcrumbs();
        }                 
        if ( isset( $sd_data['titles']) && !empty($sd_data['titles']) ) {		
			$bc_titles = $sd_data['titles'];
		}
		if ( isset( $sd_data['links']) && !empty($sd_data['links']) ) {
			$bc_links = $sd_data['links'];
        }	        
                
                $j = 1;
                $i = 0;
                $breadcrumbslist = array();
                
        if(is_single() ) {    

			if ( ! empty( $bc_titles) && !empty($bc_links) ) {      
                            
				for($i=0;$i<sizeof($bc_titles);$i++){
                                    
                                    if(array_key_exists($i, $bc_links) && array_key_exists($i, $bc_titles) ) {
                                    
                                        if($bc_links[$i] != '' && $bc_titles[$i] != '' ){
                                           
                                            if($j == 1 && !empty($settings['saswp_breadcrumb_home_page_title']) ) {
                                               $titles = $settings['saswp_breadcrumb_home_page_title'];
                                            }else{
                                               $titles = $bc_titles[$i];
                                            }

                                            $breadcrumbslist[] = array(
                                                '@type'			=> 'ListItem',
                                                'position'		=> $j,
                                                'item'			=> array(
                                                    '@id'		=> $bc_links[$i],
                                                    'name'		=> $titles,
                                                    ),
                                              );
                                            
                                            $j++;

                                        }                                                                                
                                        
                                    }
                                    					
                        }
                
                     }
               
    }
        if(is_page() ) {
                        if ( ! empty( $bc_titles) && !empty($bc_links) ) {
                            
                            for($i=0;$i<sizeof($bc_titles);$i++){
                            
                                if(array_key_exists($i, $bc_links) && array_key_exists($i, $bc_titles) ) {
        
                                    if($bc_links[$i] !='' && $bc_titles[$i] != '' ) {

                                        if($j == 1 && !empty($settings['saswp_breadcrumb_home_page_title']) ) {
                                            $titles = $settings['saswp_breadcrumb_home_page_title'];
                                        }else{
                                            $titles = $bc_titles[$i];
                                        }

                                        $breadcrumbslist[] = array(
                                            '@type'			=> 'ListItem',
                                            'position'		=> $j,
                                            'item'			=> array(
                                                '@id'		=> $bc_links[$i],
                                                'name'		=> $titles,
                                                ),
                                        );
    
                                        $j++;

                                    }                                    
                                    
                                }                                				

                            }
                        }
			

    }
        if(is_archive() ) {

         if ( ! empty( $bc_titles) && !empty($bc_links) ) {
             
             for($i=0;$i<sizeof($bc_titles);$i++){
                 
                    if(array_key_exists($i, $bc_links) && array_key_exists($i, $bc_titles) ) {
                                               
                        if($bc_links[$i] != '' && $bc_titles[$i] !='' ) {

                            if($j == 1 && !empty($settings['saswp_breadcrumb_home_page_title']) ) {
                                $titles = $settings['saswp_breadcrumb_home_page_title'];
                            }else{
                                $titles = $bc_titles[$i];
                            }

                            $breadcrumbslist[] = array(
                                '@type'		=> 'ListItem',
                                'position'	=> $j,
                                'item'		=> array(
                                        '@id'		=> $bc_links[$i],
                                        'name'		=> $titles,
                                        ),
                                );
                            $j++;

                        }                        
                
                    }
            				                
		        }
                          
         }               	
    }
        
       return $breadcrumbslist;
}

/**
 * Function to format json output
 * @global type $sd_data
 * @param type $output_array
 * @return type json 
 */
function saswp_json_print_format($output_array){
    
    global $sd_data;
    
    if ( isset( $sd_data['saswp-pretty-print']) && $sd_data['saswp-pretty-print'] == 1){
        return wp_json_encode( $output_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    }else{
        return wp_json_encode( $output_array );
    }
        
}

/**
 * @since 1.8.2
 * It removes all the microdata from the post or page
 * @param type $content
 * @return type string
 */
function saswp_remove_microdata($content){
    
    global $sd_data;
    
    if(saswp_global_option() ) {
        //Clean MicroData
        $content = preg_replace("/itemtype=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|TouristTrip|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\"?)/", "", $content);
        $content = preg_replace("/itemscope[\n|\s|]*itemtype=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|TouristTrip|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\"?)/", "", $content);
        $content = preg_replace("/itemscope[\n|\s|]*itemtype=(\'?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|TouristTrip|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\'?)/", "", $content);
        $content = preg_replace("/itemscope=(\"?)itemscope(\"?) itemtype=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|TouristTrip|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\"?)/", "", $content);    
        $content = preg_replace("/itemscope=(\"?)itemprop(\"?) itemType=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|TouristTrip|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\"?)/", "", $content);    
        $content = preg_replace("/itemscope itemprop=\"(.*?)\" itemType=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|TouristTrip|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\"?)/", "", $content);           
        $content = preg_replace("/itemprop='logo' itemscope itemtype='https:\/\/schema.org\/ImageObject'/", "", $content);
        $content = preg_replace('/itemprop="logo" itemscope="" itemtype="https:\/\/schema.org\/ImageObject"/', "", $content);
        $content = preg_replace('/itemprop=\"(worstRating|ratingValue|bestRating|aggregateRating|ratingCount|reviewBody|review|name|datePublished|author|reviewRating)\"/', "", $content);
        $content = preg_replace('/itemscope\=\"(.*?)\"/', "", $content);
        $content = preg_replace("/itemscope\='(.*?)\'/", "", $content);
        $content = preg_replace('/itemscope/', "", $content);        
        $content = preg_replace('/itemprop\=\"(.*?)\"/', "", $content);
        $content = preg_replace("/itemprop\='(.*?)\'/", "", $content);
        $content = preg_replace('/itemprop/', "", $content);
        $content = preg_replace('/itemtype\=\"(.*?)\"/', "", $content);
        $content = preg_replace("/itemtype\='(.*?)\'/", "", $content);
        $content = preg_replace('/itemtype/', "", $content);
        $content = preg_replace('/hreview-aggregate/', "", $content);
        $content = preg_replace('/hrecipe/', "", $content);
        
        if ( isset( $sd_data['saswp-ratency']) && $sd_data['saswp-ratency'] == 1 ){
            
            $regex = '/<meta property\=\"og\:image\:secure_url\" content\=\"(.*?)\" \/>(.*?)<script type\=\"application\/ld\+json\">(.*?)<\/script>/s';

            preg_match( $regex, $content, $match);

            if ( isset( $match[1]) ) {
                $content = preg_replace($regex, '<meta property="og:image:secure_url" content="'.$match[1].'" />', $content);        
            }
            
        }

        if ( isset( $sd_data['saswp-ratency']) && $sd_data['saswp-ratency'] == 1 ){
            
            $regex = '/<meta property\=\"og\:site_name\" content\="(.*?)\" \/>(.*?)<script type\=\"application\/ld\+json\">(.*?)<\/script>/s';

            preg_match( $regex, $content, $match);

            if ( isset( $match[1]) ) {
                $content = preg_replace($regex, '<meta property="og:site_name" content="'.$match[1].'" />', $content);        
            }
            
        }

        //Clean json markup
        if ( isset( $sd_data['saswp-ultimate-blocks']) && $sd_data['saswp-ultimate-blocks'] == 1 ){
            
            $regex = '/<div class\=\"ub_howto\"(.*?)<\/div><script type=\"application\/ld\+json\">(.*?)<\/script>/s';

            preg_match( $regex, $content, $match);

            if ( isset( $match[1]) ) {
                $content = preg_replace($regex, '<div class="ub_howto"'.$match[1].' </div>', $content);        
            }
            
        }

        if ( isset( $sd_data['saswp-ultimate-blocks']) && $sd_data['saswp-ultimate-blocks'] == 1 ){
            
            $regex = '/<div class\=\"ub_review_block\"(.*?)<\/div><script type=\"application\/ld\+json\">(.*?)<\/script>/s';
          
            preg_match( $regex, $content, $match);

            if ( isset( $match[1]) ) {
                $content = preg_replace($regex, '<div class="ub_review_block"'.$match[1].' </div>', $content);        
            }
            
        }

        //Clean json markup
        if ( isset( $sd_data['saswp-wpzoom']) && $sd_data['saswp-wpzoom'] == 1 ){

            $regex = '/<script type=\"application\/ld\+json\">(.*?)<\/script><div class=\"wp-block-wpzoom-recipe-card-block-recipe-card/s';

            $content = preg_replace($regex, '<div class="wp-block-wpzoom-recipe-card-block-recipe-card', $content);        
        }
        //Clean json markup
        if ( isset( $sd_data['saswp-aiosp']) && $sd_data['saswp-aiosp'] == 1 ){
            $content = preg_replace('/<script type=\"application\/ld\+json" class=\"aioseop-schema"\>(.*?)<\/script>/', "", $content);
        }
        
        //Clean json markup
        if ( isset( $sd_data['saswp-wordpress-news']) && $sd_data['saswp-wordpress-news'] == 1 ){
            $content = preg_replace("/<script type\=\'application\/ld\+json\' class\=\'wpnews-schema-graph(.*?)'\>(.*?)<\/script>/s", "", $content);
        }

        
        if ( isset( $sd_data['saswp-event-on']) && $sd_data['saswp-event-on'] == 1 ){
            $content = preg_replace("/<div class\=\"evo_event_schema\"(.*?)>(.*?)<\/script><\/div>/s", "", $content);
        }

        if ( function_exists( 'review_child_company_reviews_comments') && isset($sd_data['saswp-wp-theme-reviews']) && $sd_data['saswp-wp-theme-reviews'] == 1){

            $regex = '/<\/section>[\s\n]*<script type=\"application\/ld\+json\">(.*?)<\/script>/s';

            $content = preg_replace($regex, '</section>', $content);        
            
        }
        
        if ( isset( $sd_data['saswp-ultimatefaqs']) && $sd_data['saswp-ultimatefaqs'] == 1 ){
            $content = preg_replace('/<script type=\"application\/ld\+json" class=\"ewd-ufaq-ld-json-data"\>(.*?)<\/script>/', "", $content);
        }
        
        if ( isset( $sd_data['saswp-wp-ultimate-recipe']) && $sd_data['saswp-wp-ultimate-recipe'] == 1 ){
         
            $regex = '/<script type=\"application\/ld\+json\">(.*?)<\/script>[\s\n]*<div id=\"wpurp\-container\-recipe\-([0-9]+)\"/';
        
            preg_match( $regex, $content, $match );

            if ( isset( $match[2]) ) {
                
                $recipe_id = $match[2];

                $content = preg_replace($regex, '<div id="wpurp-container-recipe-'.$recipe_id.'"', $content);        
            
            }
                                    
        }
        
        if ( isset( $sd_data['saswp-zip-recipes']) && $sd_data['saswp-zip-recipes'] == 1 ){
            
            $regex = '/class=\"zlrecipe\-container\-border\"(.*?)>[\s\n]*<script type=\"application\/ld\+json\">(.*?)<\/script\>/sm';
            preg_match_all( $regex, $content, $matches , PREG_SET_ORDER );
            
            if($matches){
                
                foreach( $matches as $match){
                    
                    $content = preg_replace($regex, 'class="zlrecipe-container-border" '.$match[1].'>', $content);   
                    
                }
                
            }
            
        }

        if( isset($sd_data['saswp-wordlift']) && $sd_data['saswp-wordlift'] == 1 ) {

            $regex = '/<script type=\"application\/ld\+json" id=\"wl\-jsonld"\>(.*?)<\/script>/';

            preg_match( $regex, $content, $match);
            
            if ( isset( $match[1]) && is_string($match[1]) ) {
                
                $data_decode = json_decode($match[1], true);

                if($data_decode && is_array($data_decode) ) {

                    if ( isset( $data_decode[0]['@type']) && $data_decode[0]['@type'] == 'Article'){
                        $content = preg_replace($regex, '', $content);
                    }

                }                                
    
            }
            
        }
        
    }             
    
    return $content;
}

/**
 * This is a global option to hide and show all the features of this plugin.
 * @global type $sd_data
 * @return boolean
 *
 */
function saswp_global_option() {
    
            global $sd_data;
            
            $ampforwp       =   saswp_remove_warnings($sd_data, 'saswp-ampforwp', 'saswp_string');
            $bunyadamp      =   saswp_remove_warnings($sd_data, 'saswp-bunyadamp', 'saswp_string');
            $wpamp          =   saswp_remove_warnings($sd_data, 'saswp-wpamp', 'saswp_string');
            $ampwp          =   saswp_remove_warnings($sd_data, 'saswp-ampwp', 'saswp_string');

            if ( ( 0 == $ampforwp && ! saswp_non_amp() ) && ( 0 == $bunyadamp && ! saswp_non_amp() ) && ( 0 == $wpamp && ! saswp_non_amp() ) && ( 0 == $ampwp && ! saswp_non_amp() ) && empty( $sd_data['saswp-webstories'] ) && function_exists('web_stories_get_compat_instance') ) {
                
                return false;
        
            }else{
                
                return true;
                
            }  
            
}
/**
 * Function to get post tags as a comma separated string.
 * @global type $post
 * @return string
 * @since version 1.9
 */
function saswp_get_the_tags() {

    global $post, $sd_data;
    $tag_str = '';
    
    if(is_object($post) ) {
        
      $tags = get_the_tags($post->ID);
      
      if($tags){
          
          foreach( $tags as $tag){
              
            $tag_str .= $tag->name.', '; 
              
          }
          
      }
        
        
    }    


    if( isset($sd_data['saswp-metatagmanager']) && $sd_data['saswp-metatagmanager'] == 1 && class_exists('Meta_Tag_Manager') ){

        $post_meta = get_post_meta(get_the_ID(), 'mtm_data', true);

        if ( is_array( $post_meta) ) {

            $meta_tag = array_column($post_meta, 'value');
            $key      = array_search("keywords",$meta_tag);
            if ( ! empty( $key) ) {
                if(is_numeric($key) || is_string($key) ) {
                    if(array_key_exists($key, $post_meta) ) {
                        $tag_str = $post_meta[$key]['content'];
                    }
                }
            }

        }
                                
    }

    return $tag_str;
    
}

function saswp_get_the_categories() {

    global $post;

    $category_str = '';
    
    if(is_object($post) ) {
        
      $categories = get_the_category($post->ID);
      
      if($categories){
          
          foreach( $categories as $category){
              
            $category_str .= $category->name.', '; 
              
          }
          
      }
        
        
    }    
    
    return $category_str;
    
}

/**
 * Function to get shorcode ids from content by shortcode typ
 * @global type $post
 * @param type $type
 * @return type
 * @since version 1.9.3
 */
function saswp_get_ids_from_content_by_type($type){
        
    global $post;
    
    if(is_object($post) ) {
     
        $content = $post->post_content;    

        switch ($type) {

            case 'wp_recipe_maker':

                  // Gutenberg.
                    $gutenberg_matches = array();
                    $gutenberg_patern = '/<!--\s+wp:(wp\-recipe\-maker\/recipe)(\s+(\{.*?\}))?\s+(\/)?-->/';
                    preg_match_all( $gutenberg_patern, $content, $matches );

                    if ( isset( $matches[3] ) ) {
                            foreach ( $matches[3] as $block_attributes_json ) {
                                    if ( ! empty( $block_attributes_json ) ) {
                                            $attributes = json_decode( $block_attributes_json, true );
                                            if ( ! is_null( $attributes ) ) {
                                                    if ( isset( $attributes['id'] ) ) {
                                                            $gutenberg_matches[] = intval( $attributes['id'] );
                                                    }
                                            }
                                    }
                            }
                    }

                    // Classic Editor.
                    preg_match_all( '/<!--WPRM Recipe (\d+)-->.+?<!--End WPRM Recipe-->/ms', $content, $matches );
                    $classic_matches = isset( $matches[1] ) ? array_map( 'intval', $matches[1] ) : array();

                    return $gutenberg_matches + $classic_matches;  
                    
            default:
                break;
        }
        
    }
             
}
/**
 * Function to get recipe schema markup from wp_recipe_maker
 * @param type $recipe
 * @return array
 * @since version 1.9.3
 */
function saswp_wp_recipe_schema_json($recipe){
    
            global $saswp_featured_image;

            if ( 'food' === $recipe->type() ) {
                    $metadata = WPRM_Metadata::get_food_metadata( $recipe );
            } elseif ( 'howto' === $recipe->type() ) {
                    $metadata = WPRM_Metadata::get_howto_metadata( $recipe );
            } else {
                    $metadata = array();
            } 
                        
            if( isset($metadata['image'][0]) && $metadata['image'][0]  != '' ) {

                $image_size = array();
                $image_size = saswp_get_image_details($metadata['image'][0]);

                if( !empty($image_size) && is_array($image_size)) {

                    $image_arr  = array();

                    $image_arr[0] = $metadata['image'][0];
                    $image_arr[1] = $image_size[0];
                    $image_arr[2] = $image_size[1];   
                    
                    $saswp_featured_image = $image_arr;

                }
                
            }
            
            unset($metadata['image']);

            if ( isset( $metadata['video']) ) {

                if(!$metadata['video']['description']){
                 $metadata['video']['description'] = saswp_get_the_excerpt();
                }
                if(!$metadata['video']['uploadDate']){
                 $metadata['video']['uploadDate'] = get_the_date('c');
                }     

            }
                        
        return $metadata;
}

function saswp_get_testimonial_data($atts, $matche){
                      
                $reviews       = array();
                $ratings       = array();                
                $testimonial   = array();
                
                switch ($matche) {

                    case 'single_testimonial':
                        
                         $arg  = array(  
                                               'post_type'      => 'testimonial',
                                               'post_status'    => 'publish', 
                                               'post__in'       => array($atts['id']), 
                                    );    
                        
                           
                       
                        break;
                    case 'random_testimonial':
                        
                           $arg  = array(  
                                      'post_type'                 => 'testimonial',
                                      'post_status'               => 'publish', 
                                      'posts_per_page'            => $atts['count'],                                      
                                      'orderby'                   => 'rand',
                           );    
                        
                        break;
                   
                    case 'testimonials':
                    case 'testimonials_cycle':
                    case 'testimonials_grid':
                        
                        $arg  = array(  
                                      'post_type'                 => 'testimonial',
                                      'post_status'               => 'publish', 
                                      'posts_per_page'            => $atts['count'],                                                                            
                           );

                        break;
                    
                }
                
                $testimonial = get_posts( $arg);  
                 
                if ( ! empty( $testimonial) ) {
                    
                    $sumofrating = 0;
                    $avg_rating  = 1;
                    
                    foreach ( $testimonial as $value){
                                
                         $rating       = get_post_meta($value->ID, $key='_ikcf_rating', true); 
                         $author       = get_post_meta($value->ID, $key='_ikcf_client', true); 
                         
                         $sumofrating += $rating;
                             
                         $reviews[] = array(
                             '@type'         => 'Review',
                             'author'        => array('@type'=> 'Person', 'name' => $author),
                             'datePublished' => saswp_format_date_time($value->post_date),
                             'description'   => $value->post_content,
                             'reviewRating'  => array(
                                                '@type'	        => 'Rating',
                                                'bestRating'	=> '5',
                                                'ratingValue'	=> $rating,
                                                'worstRating'	=> '1',
                                   )
                         ); 
                         
                        }
                    
                        if($sumofrating> 0){
                          $avg_rating = $sumofrating /  count($reviews); 
                        }

                        $ratings['aggregateRating'] =  array(
                                                        '@type'         => 'AggregateRating',
                                                        'ratingValue'	=> $avg_rating,
                                                        'reviewCount'   => count($testimonial)
                        );
                    
                }
                                               
                return array('reviews' => $reviews, 'rating' => $ratings);
}


function saswp_get_shortcode_attrs($shortcode_str, $content){

    $attributes = array();

    $pattern = get_shortcode_regex();

    if (  preg_match_all( '/'. $pattern .'/s', $content, $matches )
            && array_key_exists( 2, $matches ) )
    {
        if(in_array( $shortcode_str, $matches[2] ) ) {
            
            foreach ( $matches[0] as $matche){
            
                $mached         = rtrim($matche, ']'); 
                $mached         = ltrim($mached, '[');
                $mached         = trim($mached);
                $attributes[]   = shortcode_parse_atts('['.$mached.' ]');  
                                
            }

        }
    }

    return $attributes;

}

function saswp_get_easy_testomonials() {
    
    $testimonial = array();
    
    global $post, $sd_data;

     if ( isset( $sd_data['saswp-easy-testimonials']) && $sd_data['saswp-easy-testimonials'] == 1){
     
        if(is_object($post) ) {
         
         $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches ) )
        {
             
           $testimo_str = ''; 
           
           if(in_array( 'single_testimonial', $matches[2] ) ) {
               $testimo_str = 'single_testimonial';
           }elseif(in_array( 'random_testimonial', $matches[2] ) ) {
               $testimo_str = 'random_testimonial';
           }elseif(in_array( 'testimonials', $matches[2] ) ) {
               $testimo_str = 'testimonials';
           }elseif(in_array( 'testimonials_cycle', $matches[2] ) ) {
               $testimo_str = 'testimonials_cycle';
           }elseif(in_array( 'testimonials_grid', $matches[2] ) ) {
               $testimo_str = 'testimonials_grid';
           }
            
        if($testimo_str){
            
            foreach ( $matches[0] as $matche){
            
                $mached = rtrim($matche, ']'); 
                $mached = ltrim($mached, '[');
                $mached = trim($mached);
                $atts   = shortcode_parse_atts('['.$mached.' ]');  
                
                $testimonial = saswp_get_testimonial_data($atts, $testimo_str);
                                
            break;
         }
            
        }    
                               
       }
         
      }
      
     }   
         
    return $testimonial;
    
}

function saswp_get_testimonial_pro_data($shortcode_data, $testimo_str){
        
            $reviews       = array();
            $ratings       = array();            
            
            if ( $shortcode_data['display_testimonials_from'] == 'specific_testimonials' && ! empty( $shortcode_data['specific_testimonial'] ) ) {
		    $specific_testimonial_ids = $shortcode_data['specific_testimonial'];
            } else {
                    $specific_testimonial_ids = null;
            }
            
            if ( $shortcode_data['layout'] == 'grid' && $shortcode_data['grid_pagination'] == 'true' || $shortcode_data['layout'] == 'masonry' && $shortcode_data['grid_pagination'] == 'true' || $shortcode_data['layout'] == 'list' && $shortcode_data['grid_pagination'] == 'true' ) {
				if ( is_front_page() ) {
					$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
				} else {
					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				}
				$args = array(
					'post_type'      => 'spt_testimonial',
					'orderby'        => $shortcode_data['testimonial_order_by'],
					'order'          => $shortcode_data['testimonial_order'],
					'posts_per_page' => $shortcode_data['number_of_total_testimonials'],
					'post__in'       => $specific_testimonial_ids,
					'paged'          => $paged,
				);
			} else {
				$args = array(
					'post_type'      => 'spt_testimonial',
					'orderby'        => $shortcode_data['testimonial_order_by'],
					'order'          => $shortcode_data['testimonial_order'],
					'posts_per_page' => $shortcode_data['number_of_total_testimonials'],
					'post__in'       => $specific_testimonial_ids,
				);
			}

			if ( $shortcode_data['display_testimonials_from'] == 'category' && ! empty( $shortcode_data['category_list'] ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'testimonial_cat',
					'field'    => 'term_id',
					'terms'    => $shortcode_data['category_list'],
					'operator' => $shortcode_data['category_operator'],
				);
			}
            
            
            

            $testimonial = get_posts( $args );
                             
            if ( ! empty( $testimonial) ) {

                $sumofrating = 0;
                $avg_rating  = 1;

                foreach ( $testimonial as $value){

                     $meta_option = get_post_meta($value->ID, 'sp_tpro_meta_options', true);
                     
                     $tpro_rating_star  = $meta_option['tpro_rating']; 
                                          
                     switch ( $tpro_rating_star ) {
                         
                            case 'five_star':
                                    $rating = 5;
                                    break;
                            case 'four_star':
                                    $rating = 4;
                                    break;
                            case 'three_star':
                                    $rating = 3;
                                    break;
                            case 'two_star':
                                    $rating = 2;
                                    break;
                            case 'one_star':
                                    $rating = 1;
                                    break;
                            default:
                                    $rating = 1;
                        }
                     
                     $author       = $meta_option['tpro_name'];  

                     $sumofrating += $rating;

                     $reviews[] = array(
                         '@type'         => 'Review',
                         'author'        => array('@type'=> 'Person', 'name' => $author),
                         'datePublished' => saswp_format_date_time($value->post_date),
                         'description'   => $value->post_content,
                         'reviewRating'  => array(
                                            '@type'	        => 'Rating',
                                            'bestRating'	=> '5',
                                            'ratingValue'	=> $rating,
                                            'worstRating'	=> '1',
                               )
                     ); 

                    }

                    if($sumofrating> 0){
                      $avg_rating = $sumofrating /  count($reviews); 
                    }

                    $ratings['aggregateRating'] =  array(
                                                    '@type'         => 'AggregateRating',
                                                    'ratingValue'	=> $avg_rating,
                                                    'reviewCount'   => count($testimonial)
                    );

            }

            return array('reviews' => $reviews, 'rating' => $ratings);
    
}

function saswp_get_strong_testimonials_data($testimonial){
    
            $reviews = array();
            $ratings = array();
    
            if ( ! empty( $testimonial) ) {

                $sumofrating = 0;
                $avg_rating  = 1;

                foreach ( $testimonial as $value){
                    
                     $rating       = get_post_meta($value->ID, $key='star_rating', true);
                     if ( ! is_numeric( $rating ) ) {
                        $rating    = 5;      
                     }  
                     $author       = get_post_meta($value->ID, $key='client_name', true);
                     
                     // User specific condition, user has named the label in polish language
                     if ( empty( $author ) ) {
                        $author       = get_post_meta($value->ID, $key='imie', true);
                     }
                     $sumofrating += $rating;

                     $reviews[] = array(
                         '@type'         => 'Review',
                         'author'        => array('@type'=> 'Person', 'name' => $author),
                         'datePublished' => saswp_format_date_time($value->post_date),
                         'description'   => $value->post_content,
                         'reviewRating'  => array(
                                            '@type'	        => 'Rating',
                                            'bestRating'	=> '5',
                                            'ratingValue'	=> $rating,
                                            'worstRating'	=> '1',
                               )
                     ); 

                    }

                    if($sumofrating> 0){
                      $avg_rating = $sumofrating /  count($reviews); 
                    }

                    $ratings['aggregateRating'] =  array(
                                                    '@type'         => 'AggregateRating',
                                                    'ratingValue'	=> $avg_rating,
                                                    'reviewCount'   => count($testimonial)
                    );

            }

            return array('reviews' => $reviews, 'rating' => $ratings);
    
}

function saswp_get_bne_testimonials_data($atts, $testimo_str){
        
            $reviews       = array();
            $ratings       = array();            
            $arg  = array(  
                'post_type' 		=>	'bne_testimonials',		
		        'order'			=> 	$atts['order'],
		        'orderby' 		=> 	$atts['orderby'],
		        'posts_per_page'	=> 	$atts['limit'],
             );    

            $testimonial = get_posts( $arg); 
                             
            if ( ! empty( $testimonial) ) {

                $sumofrating = 0;
                $avg_rating  = 1;

                foreach ( $testimonial as $value){

                     $rating       = get_post_meta($value->ID, $key='rating', true); 
                     $author       = get_post_meta($value->ID, $key='tagline', true); 

                     $sumofrating += $rating;

                     $reviews[] = array(
                         '@type'         => 'Review',
                         'author'        => array('@type'=> 'Person', 'name' => $author),
                         'datePublished' => saswp_format_date_time($value->post_date),
                         'description'   => $value->post_content,
                         'reviewRating'  => array(
                                            '@type'	        => 'Rating',
                                            'bestRating'	=> '5',
                                            'ratingValue'	=> $rating,
                                            'worstRating'	=> '1',
                               )
                     ); 

                    }

                    if($sumofrating> 0){
                      $avg_rating = $sumofrating /  count($reviews); 
                    }

                    $ratings['aggregateRating'] =  array(
                                                    '@type'         => 'AggregateRating',
                                                    'ratingValue'	=> $avg_rating,
                                                    'reviewCount'   => count($testimonial)
                    );

            }

            return array('reviews' => $reviews, 'rating' => $ratings);
    
}

function saswp_get_brb_reviews() {

    global $post, $sd_data;
    $ratings = array();
    $reviews = array();
    
    if ( isset( $sd_data['saswp-brb']) && $sd_data['saswp-brb'] == 1 && class_exists('WP_Business_Reviews_Bundle\Includes\Core') ) {
        
        if(is_object($post) ) {

            $pattern = get_shortcode_regex();

            if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
                && array_key_exists( 2, $matches ) )
            {

                $testimo_str = ''; 
           
                if(in_array( 'brb_collection', $matches[2] ) ) {
                    $testimo_str = 'brb_collection';
                }

                if($testimo_str){
                    
                    foreach ( $matches[0] as $match){

                        $mached = rtrim($match, ']'); 
                        $mached = ltrim($mached, '[');
                        $mached = trim($mached);
                        $atts   = shortcode_parse_atts('['.$mached.' ]'); 
                                                
                        if( isset($atts['id']) ){

                            $args = array(
                                'post_type'      => 'brb_collection',
                                'p'              => $atts['id'],
                                'posts_per_page' => 1,
                                'no_found_rows'  => true,
                            );
                    
                            $post_data	= get_posts($args);                
                                
                            if( isset($post_data[0]) ){

                                $post_data[0];
                                $core_obj     = new WP_Business_Reviews_Bundle\Includes\Core;
                                $reviews_data = $core_obj->get_reviews($post_data[0]);

                                if ( ! empty( $reviews_data) ) {

                                    $sumofrating = 0;
                                    $avg_rating  = 1;

                                    foreach ( $reviews_data as $value){                                        
                   
                                        $sumofrating += 5;
                   
                                        $reviews[] = array(
                                            '@type'         => 'Review',
                                            'author'        => array('@type'=> 'Person', 'name' => $value->name),
                                            'datePublished' => saswp_format_date_time($value->time),
                                            'description'   => $value->text,
                                            'reviewRating'  => array(
                                                               '@type'	        => 'Rating',
                                                               'bestRating'	    => '5',
                                                               'ratingValue'	=> '5',
                                                               'worstRating'	=> '1',
                                                  )
                                        ); 
                   
                                       }

                                     if($sumofrating> 0){
                                        $avg_rating = $sumofrating /  count($reviews); 
                                      }
                  
                                      $ratings['aggregateRating'] =  array(
                                                                      '@type'         => 'AggregateRating',
                                                                      'ratingValue'	  => $avg_rating,
                                                                      'reviewCount'   => count($reviews)
                                                                    );
                                }
                                
                            }                                                

                        }                        
                            break;
                    }

                }

            }

        }

        return array('reviews' => $reviews, 'rating' => $ratings);

    }
}
function saswp_get_strong_testimonials() {
    
    $testimonial = array();
    
    global $post, $sd_data;

     if ( isset( $sd_data['saswp-strong-testimonials']) && $sd_data['saswp-strong-testimonials'] == 1){
     
        if(is_object($post) ) {
         
         $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches ) )
        {
             
           $testimo_str = ''; 
           
           if(in_array( 'testimonial_view', $matches[2] ) ) {
               $testimo_str = 'testimonial_view';
           }else{
                if ( function_exists( 'is_plugin_active' ) &&  is_plugin_active('fusion-builder/fusion-builder.php') ) {
                    $content = preg_replace_callback('/\[fusion_code\](.*?)\[\/fusion_code\]/s', function ($matches) {
                            $decoded = base64_decode($matches[1]);
                            return $decoded;
                    }, $post->post_content);

                    preg_match_all('/\[testimonial_view[^\]]*\]/', $content, $matches);
                    if ( is_array( $matches ) && ! empty( $matches[0] ) && is_array($matches[0]) && count( $matches[0] ) > 0 ) {
                        $testimo_str = 'testimonial_view';    
                    }
                }
           }
           
        if($testimo_str){
            
            foreach ( $matches[0] as $matche){
             
                $mached = rtrim($matche, ']'); 
                $mached = ltrim($mached, '[');
                $mached = trim($mached);
               
                $atts   = shortcode_parse_atts('['.$mached.' ]'); 
                $atts   = array('id' => $atts['id']);
                
               
                $out = shortcode_atts(
			array(),
			$atts,
			'testimonial_view'
		);                                
                
                if(class_exists('Strong_View_Form') && class_exists('Strong_View_Slideshow') && class_exists('Strong_View_Display') ) {
                                    
                    switch ( $out['mode'] ) {
			case 'form' :
				$view = new Strong_View_Form( $out );
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading to check success condition.
				if ( isset( $_GET['success'] ) ) {
				    $view->success();
				} else {
					$view->build();
				}
				break;
			case 'slideshow' :
				$view = new Strong_View_Slideshow( $out );
		        $view->build();
				break;
			default :
				$view = new Strong_View_Display( $out );
        		$view->build();
		        }                 
                        if(is_object($view) ) {
                            $testimonial = saswp_get_strong_testimonials_data($view->query->posts);
                        }
                                        
                }
                
            break;
         }
            
        }    
                               
       }
         
      }
      
     }   
         
    return $testimonial;
    
    //tomorrow will do it
    
}

function saswp_get_testomonial_pro() {
    
    $testimonial = array();
    
    global $post, $sd_data;

     if ( isset( $sd_data['saswp-testimonial-pro']) && $sd_data['saswp-testimonial-pro'] == 1){
     
        if(is_object($post) ) {
         
         $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches ) )
        {
             
           $testimo_str = ''; 
           
           if(in_array( 'testimonial_pro', $matches[2] ) ) {
               $testimo_str = 'testimonial_pro';
           }
           
        if($testimo_str){
            
            foreach ( $matches[0] as $matche){
            
                $mached = rtrim($matche, ']'); 
                $mached = ltrim($mached, '[');
                $mached = trim($mached);
                $atts   = shortcode_parse_atts('['.$mached.' ]'); 
                
                $shortcode_data = get_post_meta( $atts['id'], 'sp_tpro_shortcode_options', true );
                                                
                if($shortcode_data){
                                
                    $testimonial = saswp_get_testimonial_pro_data($shortcode_data, $testimo_str);
                    
                }
                                                                
            break;
         }
            
        }    
                               
       }
         
      }
      
     }   
         
    return $testimonial;
    
}

function saswp_get_bne_testomonials() {
    
    $testimonial = array();
    
    global $post, $sd_data;

     if ( isset( $sd_data['saswp-bne-testimonials']) && $sd_data['saswp-bne-testimonials'] == 1){
     
        if(is_object($post) ) {
         
         $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches ) )
        {
             
           $testimo_str = ''; 
           
           if(in_array( 'bne_testimonials', $matches[2] ) ) {
               $testimo_str = 'bne_testimonials';
           }
            
        if($testimo_str){
            
            foreach ( $matches[0] as $matche){
            
                $mached = rtrim($matche, ']'); 
                $mached = ltrim($mached, '[');
                $mached = trim($mached);
                $atts   = shortcode_parse_atts('['.$mached.' ]'); 
                
                $id = get_post_meta( $atts['custom'], '_bne_testimonials_sg_shortcode', true );
                
                if($id){
                    
                    $atts   = shortcode_parse_atts($id); 
                                
                    $testimonial = saswp_get_bne_testimonials_data($atts, $testimo_str);
                    
                }
                                                                
            break;
         }
            
        }    
                               
       }
         
      }
      
     }   
         
    return $testimonial;
    
}

function saswp_append_fetched_reviews($input1, $schema_post_id = null){
    
        global $saswp_post_reviews;
        
        $service = new SASWP_Reviews_Service();
        
        if ( $saswp_post_reviews ){
                  
          $rv_markup = saswp_get_reviews_schema_markup(array_unique($saswp_post_reviews, SORT_REGULAR));
      
          $input1 = array_merge($input1, $rv_markup);
        
        }else{
        
          if($schema_post_id){
          
          $attached_col       = get_post_meta($schema_post_id, 'saswp_attached_collection', true);     
          $attached_rv        = get_post_meta($schema_post_id, 'saswp_attahced_reviews', true); 
          $append_reviews     = get_post_meta($schema_post_id, 'saswp_enable_append_reviews', true);
         
         if($append_reviews == 1 && ($attached_rv || $attached_col) ) {
             
             $total_rv = array();
             
             if($attached_rv && is_array($attached_rv) ) {

                foreach( $attached_rv as $review_id){
                 
                    $attr['id'] =  $review_id;                  
                    $reviews = $service->saswp_get_reviews_list_by_parameters($attr);                                                      
                    $total_rv = array_merge($total_rv, $reviews);    
                   
               }

             }             
             
             if($attached_col && is_array($attached_col) ) {
                 
                 $total_col_rv = array();
                 
                 foreach( $attached_col as $col_id){
                     
                     $collection_data = get_post_meta($col_id);
                     
                     if ( isset( $collection_data['saswp_total_reviews'][0]) ) {
                        $total_review_ids  = unserialize($collection_data['saswp_total_reviews'][0]);                
                        if ( ! empty( $total_review_ids) && is_array($total_review_ids) ) {
                            $attr = array();
                            $attr['in'] = $total_review_ids;
                            $reviews_list = $service->saswp_get_reviews_list_by_parameters($attr);                                                      
                            $total_col_rv = array_merge($total_col_rv, $reviews_list);
                        }
                                                
                     }
                     
                 }
                
                 $total_rv = array_merge($total_rv ,$total_col_rv);
             }
                    
             if($total_rv){
                 
                $rv_markup = saswp_get_reviews_schema_markup(array_unique($total_rv, SORT_REGULAR));
                
                if($rv_markup){
                    
                    if ( isset( $input1['review']) ) {

                    $input1['review']          = $rv_markup['review'];
                    $input1['aggregateRating'] = $rv_markup['aggregateRating'];

                    }else{
                       $input1 = array_merge($input1, $rv_markup);
                    }
                    
                }
                 
             }
                          
            }
              
          }                        
            
        }
           
    return $input1;
}

function saswp_get_mainEntity($schema_id){

        if( (function_exists('ampforwp_is_front_page') && ampforwp_is_front_page()) || is_front_page() ){
            return array();
        }
    
        global $post;
        
        $post_content = '';                
        $response  = array();
                
        $item_list_enable     = get_post_meta($schema_id, 'saswp_enable_itemlist_schema', true);
        $item_list_tags       = get_post_meta($schema_id, 'saswp_item_list_tags', true);
        $item_list_custom     = get_post_meta($schema_id, 'saswp_item_list_custom', true); 
        
        if($item_list_enable){

            if(is_object($post) ) {
                if ( isset( $post->post_type) && $post->post_type == 'al_product'){
                    /**
                     * If product is created using  eCommerce Product Catalog Plugin for WordPress plugin
                     * then get product description using get_product_description() function of that plugin
                     * */
                    if ( function_exists( 'get_product_description') ) {
                        $post_content = get_product_description( $post->ID );
                    }else{
                        $post_content = apply_filters('the_content', $post->post_content);
                    }
                }else{
                    $post_content = apply_filters('the_content', $post->post_content);
                }
            }
            
            $listitem = array();
            
            if($item_list_tags == 'custom'){
                
                $regex = '/<([0-9a-z]*)\sclass="'.$item_list_custom.'"[^>]*>(.*?)<\/\1>/';
                
                preg_match_all( $regex, $post_content, $matches , PREG_SET_ORDER );
                                
                foreach( $matches as $match){
                    $listitem[] = $match[2];
                }
                                                
            }else{
                                
                $regex = '/<'.$item_list_tags.'>(.*?)<\/'.$item_list_tags.'>/';
                
                preg_match_all( $regex, $post_content, $matches , PREG_SET_ORDER );
                
                if($matches){
                    foreach( $matches as $match){
                        $listitem[] = wp_strip_all_tags($match[1]);
                    }
                }
            }
               
            if($listitem){
                             
                    $response['@type'] = 'ItemList';
                    $response['itemListElement'] = $listitem;                 
                    $response['itemListOrder'] = 'http://schema.org/ItemListOrderAscending ';
                    $response['name']          = saswp_get_the_title();
                
            }
                                    
        }
                
        return $response;
        
}

function saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options){
            
            if ( isset( $schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] == 1){

                if ( isset( $schema_options['saswp_modify_method']) ) {

                    if($schema_options['saswp_modify_method'] == 'automatic'){

                        $service = new SASWP_Output_Service();
                        $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);                                    
                    }

                    if($schema_options['saswp_modify_method'] == 'manual'){
                        
                        $all_post_meta = get_post_meta($schema_post_id); 
                        
                        switch ($schema_type) {
                            
                            case 'local_business':
                                                               
                                $data          = saswp_local_business_schema_markup($schema_post_id, $schema_post_id, $all_post_meta);
                                $input1        = array_merge($input1, $data);

                                break;
                            
                            case 'HowTo':
                                                                                                   
                                $data          = saswp_howto_schema_markup($schema_post_id, $schema_post_id, $all_post_meta);
                                $input1        = array_merge($input1, $data);
                            
                                break;
                            
                            case 'FAQ':
                                                                                                   
                                $data          = saswp_faq_schema_markup($schema_post_id, $schema_post_id, $all_post_meta);
                                $input1        = array_merge($input1, $data);
                            
                                break;

                            case 'Service':
                                                                                                
                                $data          = saswp_service_schema_markup($schema_post_id, $schema_post_id, $all_post_meta);
                                $input1        = array_merge($input1, $data);
                            
                                break;

                            case 'qanda':
                                                                                                
                                $data          = saswp_qanda_schema_markup($schema_post_id, $schema_post_id, $all_post_meta);
                                $input1        = array_merge($input1, $data);
                            
                                break;    
                                
                            case 'Course':
                                                                                                
                                $data          = saswp_course_schema_markup($schema_post_id, $schema_post_id, $all_post_meta);
                                $input1        = array_merge($input1, $data);
                            
                                break;    
                                
                            case 'ProductGroup':
                                                                                                
                                $data          = saswp_product_group_schema_markup($schema_post_id, $schema_post_id, $all_post_meta);
                                $input1        = array_merge($input1, $data);
                            
                                break;    
                                
                            default:
                                break;
                        }
                        
                    }

                }else{
                    $service = new SASWP_Output_Service();
                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);                                    
                }

            }
        
    return $input1;
        
}

function saswp_explod_by_semicolon($data){
    
    $response = array();
    
    if($data){
        
        $explod = explode(';', $data);  
                   
        if($explod){

            foreach ( $explod as $val){

                $response[] = wp_strip_all_tags($val);  

            }

        }         
    }    
    return $response;    
}
function saswp_get_wp_customer_reviews() {

    global $post, $sd_data, $response_rv;
    
    $reviews = array();
    $ratings = array();

    if(!$response_rv && isset($sd_data['saswp-wp-customer-reviews']) && $sd_data['saswp-wp-customer-reviews'] == 1){

        $queryOpts = array(
            'orderby'          => 'date',
            'order'            => 'DESC',        
            'post_type'        => 'wpcr3_review',
            'post_status'      => 'publish',    
            'posts_per_page'   => -1,    
        );

        if ($post->ID != -1) {
			// if $postid is not -1 (all reviews from all posts), need to filter by meta value for post id
			$meta_query = array('relation' => 'AND');
			$meta_query[] = array(
				'key' => "wpcr3_review_post",
				'value' => $post->ID,
				'compare' => '='
			);
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            $queryOpts['meta_query'] = $meta_query;
		}
        
        $reviews_post = new WP_Query($queryOpts);    
        
        if($reviews_post->posts){
    
            $sumofrating = 0;
            $avg_rating  = 1;
    
            foreach ( $reviews_post->posts as $value){
                
                 $meta = get_post_custom($value->ID);
                 
                 $rating       = $meta['wpcr3_review_rating'][0];              
                 
                 $sumofrating += $rating;
                    
                 $reviews[] = array(
                     '@type'         => 'Review',
                     'author'        => array('@type'=> 'Person', 'name' => $meta['wpcr3_review_name'][0]),
                     'datePublished' => saswp_format_date_time($value->post_date),
                     'description'   => $value->post_content,
                     'reviewRating'  => array(
                                        '@type'	        => 'Rating',
                                        'bestRating'	=> '5',
                                        'ratingValue'	=> $rating,
                                        'worstRating'	=> '1',
                           )
                 ); 
    
                }
    
                if($sumofrating> 0){
                  $avg_rating = $sumofrating /  count($reviews); 
                }
    
                $ratings =  array(
                                                '@type'         => 'AggregateRating',
                                                'ratingValue'	=> $avg_rating,
                                                'reviewCount'   => count($reviews)
                );
    
        }
        $response_rv =  array('reviews' => $reviews, 'AggregateRating' => $ratings);

    }    

    return $response_rv;

}
function saswp_get_reviews_wp_theme() {

    global $post, $sd_data, $response_rv;
    
    $reviews = array();
    $ratings = array();

    if(!$response_rv && function_exists('review_child_company_reviews_comments') && isset($sd_data['saswp-wp-theme-reviews']) && $sd_data['saswp-wp-theme-reviews'] == 1){

        $reviews_post     = get_approved_comments( $post->ID );
        
        if($reviews_post){
    
            $sumofrating = 0;
            $avg_rating  = 1;
    
            foreach ( $reviews_post as $review){

                $comment_meta = get_comment_meta( $review->comment_ID, 'review', true );
                $comment_meta = explode( ',', $comment_meta );

                $user_overall = 0;
                $user_rates   = 0;                

                $criterias = get_post_meta( get_the_ID(), 'reviews_score' );
                $rate_criterias = array();
                if( !empty( $criterias ) ){
                    foreach( $criterias as $criteria ){
                        $rate_criterias[] = $criteria['review_criteria'];
                    }
                }
                                                                    
                for( $i=0; $i<sizeof($comment_meta); $i++ ){
                    if( !empty( $rate_criterias[$i] ) ){
                        $temp = explode( '|', $comment_meta[$i] );										
                        $user_overall += $temp[1];
                        $user_rates++;

                    }
                }
                
                $user_overall = $user_overall / $user_rates;
                $rating       = round( $user_overall, 1 );                                  
                $sumofrating += round( $user_overall, 1 );
                    
                 $reviews[] = array(
                     '@type'         => 'Review',
                     'author'        => array('@type'=> 'Person', 'name' => $review->comment_author ? $review->comment_author : 'Anonymous'),
                     'datePublished' => saswp_format_date_time($review->comment_date),
                     'description'   => $review->comment_content,
                     'reviewRating'  => array(
                                        '@type'	        => 'Rating',
                                        'bestRating'	=> '5',
                                        'ratingValue'	=> $rating,
                                        'worstRating'	=> '1',
                           )
                 ); 
    
                }
    
                if($sumofrating> 0){
                  $avg_rating = $sumofrating /  count($reviews); 
                }
    
                $ratings =  array(
                                                '@type'         => 'AggregateRating',
                                                'ratingValue'	=> $avg_rating,
                                                'reviewCount'   => count($reviews)
                );
    
        }else{

            $author_average = get_post_meta( get_the_ID(), 'author_average', true );
            
            $ratings =  array(
                '@type'         => 'AggregateRating',
                'ratingValue'	=> $author_average,
                'reviewCount'   => 1
            );

            $reviews[] = array(
                '@type'         => 'Review',
                'author'        => array('@type'=> 'Person', 'name' => saswp_get_the_author_name()),
                'datePublished' => get_the_date("c"),
                'description'   => saswp_get_the_excerpt(),
                'reviewRating'  => array(
                                   '@type'	        => 'Rating',
                                   'bestRating'	    => '5',
                                   'ratingValue'	=> $author_average,
                                   'worstRating'	=> '1',
                      )
            ); 
            
        }
        
        $response_rv =  array('reviews' => $reviews, 'AggregateRating' => $ratings);

    }    
    
    return $response_rv;

}

add_filter( 'the_excerpt_rss', 'saswp_featured_image_in_feed_excerpt' );

function saswp_featured_image_in_feed_excerpt( $content ) {

    global $post, $sd_data;
    
    if( is_feed() ) {

        $use_excerpt = get_option('rss_use_excerpt');

        if( $use_excerpt == 1 && (isset($sd_data['saswp-rss-feed-image']) && $sd_data['saswp-rss-feed-image'] == 1) ){

            if ( has_post_thumbnail( $post->ID ) ){
                $image  = get_the_post_thumbnail( $post->ID, 'full', array( 'style' => 'float:right; margin:0 0 10px 10px;' ) );
                $content = $image . $content;
            }

        }
        
    }

    return $content;
    
}

add_filter( 'the_content', 'saswp_featured_image_in_feed_content' );

function saswp_featured_image_in_feed_content( $content ) {

    global $post, $sd_data;

    if( is_feed() ) {

        $use_excerpt = get_option('rss_use_excerpt');

        if( $use_excerpt != 1 && (isset($sd_data['saswp-rss-feed-image']) && $sd_data['saswp-rss-feed-image'] == 1) ){

            if ( has_post_thumbnail( $post->ID ) ){
                $image  = get_the_post_thumbnail( $post->ID, 'full', array( 'style' => 'float:right; margin:0 0 10px 10px;' ) );
                $content = $image . $content;
            }

        }
        
    }

    return $content;
    
}
function saswp_get_loop_markup($i) {

    global $sd_data;

    $response           = array();
    $schema_properties  = array();

    $schema_type        =  $sd_data['saswp_archive_schema_type'];    
    $service_object     = new SASWP_Output_Service();    
    $publisher_info     = $service_object->saswp_get_publisher();   
    $feature_image      = $service_object->saswp_get_featured_image();             
                                                                                                                                                                                                                                                                              
    $schema_properties['@type']            = esc_attr( $schema_type);
    $schema_properties['headline']         = saswp_get_the_title();
    $schema_properties['url']              = get_the_permalink();                                                                                                
    $schema_properties['datePublished']    = get_the_date('c');
    $schema_properties['dateModified']     = get_the_modified_date('c');
    $schema_properties['mainEntityOfPage'] = get_the_permalink();
    $schema_properties['author']           = saswp_get_author_details();

    if( isset($publisher_info['publisher']) ){
        $schema_properties['publisher']        = $publisher_info['publisher'];                                
    }
          
    if ( ! empty( $feature_image) ) {                            
        $schema_properties = array_merge($schema_properties, $feature_image);        
    }

    $itemlist_arr = array(
                '@type' 		    => 'ListItem',
                'position' 		    => $i,
                'url' 		        => get_the_permalink(),                
        );    
    $response = array('schema_properties' => $schema_properties, 'itemlist' => $itemlist_arr);

    return $response;
}

function saswp_get_ryviu_reviews ($product_id){
    
    $domain   = '';
    $shop_url = site_url();
    $domain   = str_replace(array('https://', 'http://'), '', $shop_url);    
    $handle   = get_post_field( 'post_name', get_post() );
    
    $response = array();

    if ( ! empty( $domain) ) {

        $i           = 1;
        $loop_count  = 1; 
        $sumofrating = 0;
        $avg_rating  = 1;

        do{
            
            $url  = esc_url( "https://app.ryviu.io/frontend/client/get-more-reviews?domain=".$domain );
            $body = array(
                "domain" 	    => $domain,                
                "handle" 	    => $handle,                
                "page" 		    => $i,
                "product_id"    => $product_id,
                "type"          => "load-more",                
            );            
            
            $result = wp_remote_post(
                $url, [
                    'headers'   => [ 'Content-Type' => 'application/json' ],
                    'body'       => wp_json_encode($body),
                ]
            );
            
            if(wp_remote_retrieve_response_code($result) == 200 && wp_remote_retrieve_body($result) ) {
                
                $reviews = json_decode(wp_remote_retrieve_body($result),true);
                
                if($reviews['more_reviews']){
                    
                    foreach ( $reviews['more_reviews'] as  $value) {

                        $response['reviews'][] = array(
                            'author'        => $value['author'],
                            'datePublished' => $value['created_at'],
                            'description'   => $value['body_text'],
                            'reviewRating'  => $value['rating'],
                        ) ;

                        $sumofrating += $value['rating'];

                    }
                    
                    if( $sumofrating> 0 ){
                       $avg_rating = $sumofrating /  $reviews['total']; 
                    }

                    $response['average'] = $avg_rating;
                    $response['total']   = $reviews['total'];
                    
                    if($response['total'] > 10){
                        $loop_count = ceil($response['total'] / 10);
                    }

                    
                }
            }

            $i++;

        } while ($i <= $loop_count);
        
    }
    
    return $response;

}
function saswp_get_yotpo_reviews($product_id){

    $yotpo_settings = get_option('yotpo_settings');
    $response = array();

    if ( isset( $yotpo_settings['app_key']) ) {

        $i          = 1;
        $loop_count = 1; 

        do{
            
            $url  = esc_url('https://api.yotpo.com/v1/widget/'.$yotpo_settings['app_key'].'/products/'.$product_id.'/reviews.json?per_page=150&page='.$i);
            $result = wp_remote_get($url);

            if(wp_remote_retrieve_response_code($result) == 200 && wp_remote_retrieve_body($result) ) {
                
                $reviews = json_decode(wp_remote_retrieve_body($result),true);

                if($reviews['response']['reviews']){

                    $response['average'] = $reviews['response']['bottomline']['average_score'];
                    $response['total']   = $reviews['response']['bottomline']['total_review'];
                    
                    if($response['total'] > 150){
                        $loop_count = ceil($response['total'] / 150);
                    }

                    foreach ( $reviews['response']['reviews'] as  $value) {

                        $response['reviews'][] = array(
                            'author'        => $value['user']['display_name'],
                            'datePublished' => $value['created_at'],
                            'description'   => $value['content'],
                            'reviewRating'  => $value['score'],
                        ) ;

                    }
                    
                }
            }

            $i++;

        } while ($i <= $loop_count);
        
    }
    
    return $response;
}

function saswp_get_stamped_reviews($product_id){

    $public_api   = Woo_stamped_api::get_public_keys();
    $store_url    = Woo_stamped_api::get_site_url();
    
    $response = array();

    if($public_api && $store_url){

        $i          = 1;
        $loop_count = 1; 

        do{
            
            $url  = "https://stamped.io/api/widget/reviews?productId={$product_id}&apiKey={$public_api}&storeUrl={$store_url}&per_page=100&page={$i}";
            
            $result = wp_remote_get($url);

            if(wp_remote_retrieve_response_code($result) == 200 && wp_remote_retrieve_body($result) ) {
                
                $reviews = json_decode(wp_remote_retrieve_body($result),true);

                if($reviews['data']){

                    $response['average'] = $reviews['ratingAll'];
                    $response['total']   = $reviews['totalAll'];
                    
                    if($response['total'] > 100){
                        $loop_count = ceil($response['total'] / 100);
                    }

                    foreach ( $reviews['data'] as  $value) {

                        $response['reviews'][] = array(
                            'author'        => $value['author'],
                            'datePublished' => $value['dateCreated'],
                            'description'   => $value['reviewMessage'],
                            'reviewRating'  => $value['reviewRating'],
                        ) ;

                    }
                    
                }
            }

            $i++;

        } while ($i <= $loop_count);
        
    }
    
    return $response;
}

function saswp_get_ampforwp_story_images() {

    $image_arr = array();
    
    if(class_exists('Ampforwp_Stories_Post_Type') ) {

        $amp_story_meta = get_post_meta( get_the_ID(), 'ampforwp_stories', true );
        $post_type      = get_post_type(get_the_ID());

        if( !empty($amp_story_meta) && is_array($amp_story_meta) && $post_type == 'ampforwp_story' ) {
                                                    
            foreach ( $amp_story_meta as $value) {
                    
                if( isset($value['design_type']) ){

                    if( $value['design_type'] == 'design1'){
                        $image_arr[] = saswp_get_image_by_url($value['dsg1_image_url']);
                    }
                    if($value['design_type'] == 'design2'){
                        $image_arr[] = saswp_get_image_by_url($value['dsg2_image_url']);
                    }
                    if($value['design_type'] == 'design3'){
                        $image_arr[] = saswp_get_image_by_url($value['dsg3_image_url']);
                    }

                }
                
            }
                                                            
        }

    }

    return $image_arr;
    
}

add_shortcode('saswp-breadcrumbs', 'saswp_render_breadcrumbs_html');

function saswp_render_breadcrumbs_html($atts){

    global $sd_data,$post;
    $attr = shortcode_atts(
        array(
            'hide_post_title' => '',
        ), $atts, 'saswp-breadcrumbs' );

    $hide_post_title = ($attr['hide_post_title']) ? $attr['hide_post_title'] : 0;
    $get_current_post_title = get_post_field('post_title',$post->ID);
    $breadcrumbs = '';    
    if ( ! empty( $sd_data['titles']) ) {

        $breadcrumbs .= '<style>';

        $breadcrumbs .= '.saswp-breadcrumbs-li{            
            display: inline-block;
            list-style-type: none;
            font-size: 12px;
            text-transform: uppercase;
            margin-right: 5px;
        }
        .saswp-breadcrumbs-li a:after{
            content: "\25BA";
            font-family: "icomoon";
            font-size: 12px;
            display: inline-block;
            color: #bdbdbd;
            padding-left: 5px;
            position: relative;
            top: 1px;
        }
        ';
        $breadcrumbs .= '</style>';
        $breadcrumbs .= '<ul class="saswp-breadcrumbs-ul">';

            foreach ( $sd_data['titles'] as $key => $value) {
                if($hide_post_title == 1 && $value == $get_current_post_title){
                    // do nothing
                }else{
                    $breadcrumbs .= '<li class="saswp-breadcrumbs-li" ><a href="'. esc_url( $sd_data['links'][$key]).'">'.esc_html( $value).'</a></li>';

                }
                
            }

        $breadcrumbs .= '</ul>';

    }

    return $breadcrumbs;
}

function saswp_default_video_object_schema() {

    global $sd_data;

    if ( isset( $sd_data['saswp-default-videoobject']) && $sd_data['saswp-default-videoobject'] == 0){
        return array();
    }    

    $input1 = array();
    $video_links      = saswp_get_video_metadata();  
    
    if ( ! empty( $video_links) ) {
        $Conditionals = saswp_get_all_schema_posts(); 
        $countVideoObjSchema = [];
        if ( ! empty( $Conditionals) ) {
            foreach( $Conditionals as $schemaConditionals){
                if($schemaConditionals['schema_type'] == 'VideoObject'){
                    $countVideoObjSchema[] = $schemaConditionals['schema_type'];
                }        
            }
            if(count( $countVideoObjSchema) > 0){
                return $input1;
            } 
        }    

        $input1['@context'] = saswp_context_url(); 
        $description = saswp_get_the_excerpt();

        if(!$description){
            $description = get_bloginfo('description');
        }  
        $date 		        = get_the_date("c");
        $modified_date 	    = get_the_modified_date("c"); 
          
        if(count($video_links) > 1){
                

            $input1['@type'] = "ItemList"; 
            $i = 1; 
            foreach( $video_links as $vkey => $v_val){
                if ( isset( $v_val['video_url']) && !empty($v_val['video_url']) ) {
                    $vnewarr = array(
                        '@type'				            => 'VideoObject',
                        "position"                      => $vkey+1,
                        "@id"                           => saswp_get_permalink().'#'.$i++,
                        'name'				            => isset($v_val['title'])? $v_val['title'] : saswp_get_the_title(),
                        'datePublished'                 => esc_html( $date),
                        'dateModified'                  => esc_html( $modified_date),
                        'url'				            => isset($v_val['video_url'])?saswp_validate_url($v_val['video_url']):saswp_get_permalink(),
                        'interactionStatistic'          => array(
                            "@type" => "InteractionCounter",
                            "interactionType" => array("@type" => "WatchAction" ),
                            "userInteractionCount" => isset($v_val['viewCount'])? $v_val['viewCount'] : '0', 
                            ),    
                        'thumbnailUrl'                  => isset($v_val['thumbnail_url'])? $v_val['thumbnail_url'] : saswp_get_thumbnail(),
                        'author'			            => saswp_get_author_details(),
                    );

                    if ( isset( $v_val['uploadDate']) ) {                                                                        
                        $vnewarr['uploadDate']   = $v_val['uploadDate'];                                    
                    }else{
                        $vnewarr['uploadDate']   = $date;     
                    }

                    if ( isset( $v_val['duration']) ) {                                                                        
                        $vnewarr['duration']   = $v_val['duration'];                                    
                    }

                    if ( isset( $v_val['video_url']) ) {                                                                        
                        $vnewarr['contentUrl']  = saswp_validate_url($v_val['video_url']);                                    
                    }

                    if ( isset( $v_val['video_url']) ) {                                                                        
                        $vnewarr['embedUrl']   = saswp_validate_url($v_val['video_url']);                                 
                    }

                    if ( isset( $v_val['description']) ) {                                                                        
                        $vnewarr['description']   = $v_val['description'];                                    
                    }else{
                        $vnewarr['description']   = $description; 
                    }
                    
                    $input1['itemListElement'][] = $vnewarr;
                }
            }
        }else{
          
            if ( isset( $video_links[0]['video_url']) && !empty($video_links[0]['video_url']) ) {  
                $input1 = array(
                    '@context'			            => saswp_context_url(),
                    '@type'				            => 'VideoObject',
                    '@id'                           => saswp_get_permalink().'#videoobject',        
                    'url'				            => saswp_get_permalink(),
                    'headline'			            => saswp_get_the_title(),
                    'datePublished'                 => esc_html( $date),
                    'dateModified'                  => esc_html( $modified_date),
                    'description'                   => $description,
                    'transcript'                    => saswp_get_the_content(),
                    'name'				            => saswp_get_the_title(),
                    'uploadDate'                    => esc_html( $date),
                    'thumbnailUrl'                  => isset($video_links[0]['thumbnail_url'])? $video_links[0]['thumbnail_url'] : saswp_get_thumbnail(),
                    'author'			            => saswp_get_author_details()						                                                                                                      
                );
                
                if ( isset( $video_links[0]['duration']) ) {                                                                        
                    $input1['duration']   = $video_links[0]['duration'];                                    
                }
                if ( isset( $video_links[0]['video_url']) ) {
                    
                    $input1['contentUrl'] = saswp_validate_url($video_links[0]['video_url']);
                    $input1['embedUrl']   = saswp_validate_url($video_links[0]['video_url']);
                    
                }
            }
        }
    }
    return $input1;
}

/**
 * Render the custom schema markup only if option is enabled in schema settings
 * @since 1.33
 * */
function saswp_global_custom_schema_option() {
    global $sd_data;       
    if( saswp_remove_warnings($sd_data, 'saswp-for-cschema', 'saswp_string') == '' || saswp_remove_warnings($sd_data, 'saswp-for-cschema', 'saswp_string') == 1 ) {
        return true;

    }else{
        return false;
        
    } 
}

/**
 * Modify the $post variable and assign the buddypress group topic object to $post variable
 * as schema is not getting displayed on a buddypress group assigned topic to tackle this this filter
 * is written #2256
 * @param   $post   WP_Post
 * @return  $post   WP_Post
 * @since   1.43
 * */
add_filter( 'saswp_modify_bbpress_group_topic_object', 'saswp_modify_bbpress_group_topic_object_clbk' );
function saswp_modify_bbpress_group_topic_object_clbk( $post ) {
    
    global $sd_data, $wp, $saswp_bb_topic;
    if ( isset( $sd_data['saswp-bbpress'] ) && $sd_data['saswp-bbpress'] == 1 && function_exists( 'bp_get_current_group_id' ) && function_exists( 'groups_get_groupmeta' ) && function_exists( 'bbp_get_topic_post_type' ) ) {

        $topic_slug   = '';
        if ( is_object( $wp ) && ! empty( $wp->request ) ) {
            $topic_slug   = explode( '/', $wp->request );
            $topic_slug   = trim( end( $topic_slug ) );
        }
                  
        $bp_group_id  =   bp_get_current_group_id();
        if ( $bp_group_id > 0 && ! empty( $topic_slug ) ) {
            $forum_ids    =   groups_get_groupmeta( $bp_group_id, 'forum_id', true );

            if ( ! empty( $forum_ids ) && is_array( $forum_ids ) ) {

                foreach ( $forum_ids as $forum_id) {

                    if ( $forum_id > 0 ) {

                        // Get all topics under that forum
                        $args = array(
                            'post_type'      => bbp_get_topic_post_type(),
                            'post_parent'    => $forum_id,
                            'posts_per_page' => -1,
                            'fields'         => 'ids', // Only get IDs
                        );

                        $topics = get_posts( $args );

                        if ( ! empty( $topics ) && is_array( $topics ) ) {
                            
                            foreach ( $topics as $topic_id ) {
                              $topic_post   =  get_post( $topic_id );
                              if ( is_object( $topic_post ) && ! empty( $topic_post->post_name ) && $topic_slug == $topic_post->post_name ) {
                                $post       = $topic_post;
                                $saswp_bb_topic = $post;
                                break;
                              }

                            }
                          
                        }

                    }

                }

            }
        }
    }

    return $post;
}

/**
 * Modify the buddypress group topic id
 * @param   $bbp_topic_id   integer
 * @param   $topic_id       integer
 * @return  $bbp_topic_id   integer
 * @since   1.43
 * */
add_filter( 'bbp_get_topic_id', 'saswp_modify_bb_topic_id', 10, 2 );
function saswp_modify_bb_topic_id( $bbp_topic_id, $topic_id ) {

    global $saswp_bb_topic, $sd_data;

    if ( ! empty( $sd_data['saswp-bbpress'] ) &&  is_object( $saswp_bb_topic ) && ! empty( $saswp_bb_topic->ID ) && $bbp_topic_id == 0 && $topic_id == 0 ) {
        $bbp_topic_id = $saswp_bb_topic->ID;
    }
    return $bbp_topic_id;

}

/**
 * Modify the discussion forum schema on Buddypress group topic
 * @param   $input1     array
 * @return  $input1     array
 * @since   1.43
 * */
add_filter( 'saswp_modify_d_forum_posting_schema_output', 'saswp_modify_bbpress_group_topic_markup' );
function saswp_modify_bbpress_group_topic_markup( $input1 ){
    
    global $saswp_bb_topic, $sd_data;    
    if ( ! empty( $sd_data['saswp-bbpress'] == 1 ) && is_object( $saswp_bb_topic ) && ! empty( $saswp_bb_topic->post_content ) ) {

        $excerpt        =   wp_strip_all_tags(strip_shortcodes( $saswp_bb_topic->post_content ) ); 
        $excerpt        =   preg_replace( '/\[.*?\]/','', $excerpt );

        $content        =   wp_strip_all_tags( $saswp_bb_topic->post_content );   
        $content        =   preg_replace( '/\[.*?\]/','', $content );            
        $content        =   str_replace( '=', '', $content ); 
        $content        =   str_replace( array("\n","\r\n","\r" ), ' ', $content );

        $author_name    =   '';
        $author_url     =   '';
        if ( empty( $input1['author']['name'] ) ) {
            $input1['author']['name']    =   get_the_author_meta( 'display_name', $saswp_bb_topic->post_author );
        }
        if ( empty( $input1['author']['url'] ) ) {
            $input1['author']['url']     =   get_the_author_meta( 'user_url', $saswp_bb_topic->post_author );
        }
        
        $input1['description']      =   $excerpt;
        $input1['articleSection']   =   isset( $saswp_bb_topic->post_title ) ? $saswp_bb_topic->post_title : $input1['articleSection'];  
        $input1['articleBody']      =   $content;
        $input1['datePublished']    =   get_post_time( DATE_ATOM, false, $saswp_bb_topic->ID , true );
        $input1['dateModified']     =   get_the_modified_date( "c", $saswp_bb_topic->ID );

        if ( ! empty( $input1['comment'] ) && is_array( $input1['comment'] ) ) {

            foreach ( $input1['comment'] as $key => $comment ) {

                if ( isset( $comment['id'] ) && ! filter_var( $comment['id'], FILTER_VALIDATE_URL ) ) {
                    
                    $comment_id     =   bbp_get_topic_permalink() . $comment['id'];
                    $comment_id     =   str_replace( '#comment-', '#post-', $comment_id );
                    $input1['comment'][$key]['id']  =   $comment_id;
                    if ( isset( $comment['description'] ) ) {
                        $input1['comment'][$key]['text']    =   $comment['description'];
                    }

                    if ( isset( $comment['author']['url'] ) ) {
                        $reply_id   =   explode( '#post-', $comment_id );
                        if ( is_array( $reply_id ) && ! empty( $reply_id[1] ) && $reply_id[1] > 0 ) {
                            $input1['comment'][$key]['author']['url']   =   bbp_get_reply_author_url( $reply_id[1] );
                        }    
                    }
                    
                }

            }

        }

    }

    return $input1;

}