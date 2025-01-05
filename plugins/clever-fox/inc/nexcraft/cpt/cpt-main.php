<?php
// code for custom post type  Project
		function nexcraft_project() {
	
			$project_slug = get_theme_mod('project_slug','project'); 
			register_post_type( 'nexcraft_project',
				array(
					'labels' => array(
						'name' => __('Project', 'nexcraft'),
						'singular_name' => __('Project', 'nexcraft'),
						'add_new' => __('Add New', 'nexcraft'),
						'add_new_item' => __('Add New Project','nexcraft'),
						'edit_item' => __('Edit Project','nexcraft'),
						'new_item' => __('New Facebook link ','nexcraft'),
						'all_items' => __('All Project','nexcraft'),
						'view_item' => __('View Link','nexcraft'),
						'search_items' => __('Search Links','nexcraft'),
						'not_found' =>  __('No Links found','nexcraft'),
						'not_found_in_trash' => __('No Links found in Trash','nexcraft'), 
						
					),
						'supports' => array('title','thumbnail','comments'),
						'show_in_nav_menus' => false,
						'public' => true,
						'menu_position' => 20,
						'rewrite' => array('slug' => $project_slug),
						'menu_icon' => 'dashicons-schedule',
					
				)
			);
		}
		add_action( 'init', 'nexcraft_project' );


		// Project Meta Box

		function nexcraft_project_init()
		{
							
			add_meta_box('project_all_meta', 'Project Description', 'nexcraft_meta_project','nexcraft_project', 'normal', 'high');
			
			add_action('save_post','nexcraft_meta_project_save');
		}


		add_action('admin_init','nexcraft_project_init');		
						

						
		function nexcraft_meta_project()
		{
			global $post;
			
			$project_button_link 			= sanitize_text_field( get_post_meta( get_the_ID(),'project_button_link', true ));
			$project_button_link_target 	= sanitize_text_field( get_post_meta( get_the_ID(),'project_button_link_target', true ));
		?>	
			
			<h3><i><?php esc_html_e('Project Single View Detail','nexcraft'); ?></i></h3>

			
			<div class="inside">				
				<p><label><?php _e('Project URL','nexcraft');?></label></p>
				<p><input style="width:100%; height:40px; padding: 10px;"  name="project_button_link" placeholder="<?php _e('Project URL','nexcraft');?>" type="text" value="<?php if (!empty($project_button_link)) echo esc_attr($project_button_link);?>">&nbsp;</input></p>
				<p> <input name="project_button_link_target" type="checkbox" <?php if(!empty($project_button_link_target)) echo "checked"; ?> > </input>
				<label><?php _e('Open link in a new tab','nexcraft'); ?> </label> </p>
			</div>				
			
		<?php 	
		}


		function nexcraft_meta_project_save($post_id) 
			{
				if (isset($_POST['post_ID'])) 
				{ 	
					$post_ID = $_POST['post_ID'];				
					$post_type = get_post_type($post_ID);
					
					if ($post_type == 'nexcraft_project') 
					{	
						update_post_meta($post_ID, 'project_button_link', sanitize_text_field($_POST['project_button_link']));
						
						$project_button_link_target = isset($_POST['project_button_link_target']) ? '1' : '0';
						update_post_meta($post_ID, 'project_button_link_target', $project_button_link_target);
					}
				}
			}

		
		// Project Category Texonomy
		
		function nexcraft_project_taxonomy() {
		
		$texo_project_slug = get_theme_mod('texo_project_slug','project_category'); 
		register_taxonomy('project_categories', 'nexcraft_project',
			array(
				'hierarchical' => true,
				'label' => 'Project Categories',
				'show_in_nav_menus' => true,
				'query_var' => true,
				'rewrite' => array('slug' => $texo_project_slug )
			)
		);
	
	
		//Default category id		
		$defualt_tex_id = get_option('custom_texo_project_id');
		//quick update category
		if((isset($_POST['action'])) && (isset($_POST['taxonomy']))){		
			wp_update_term($_POST['tax_ID'], 'project_categories', array(
			  'name' => $_POST['name'],
			  'slug' => $_POST['slug']
			));	
			update_option('custom_texo_project_id', $defualt_tex_id);
		}
		else 
		{ 	//insert default category 
			if(!$defualt_tex_id){
				wp_insert_term('All','project_categories', array('description'=> 'Default Category','slug' => 'All'));
				$Current_text_id = term_exists('All', 'project_categories');
				update_option('custom_texo_project_id', $Current_text_id['term_id']);
			}
		}
		//update category
		if(isset($_POST['submit']) && isset($_POST['action']) )
		{	wp_update_term(isset($_POST['tag_ID']), 'project_categories', array(
			  'name' => isset($_POST['name']),
			  'slug' => isset($_POST['slug']),
			  'description' => isset($_POST['description'])
			));
		}
		// Delete default category
		if(isset($_POST['action']) && isset($_POST['tag_ID']) )
		{	if(($_POST['tag_ID'] == $defualt_tex_id) && $_POST['action']	 =="delete-tag")
			{	
				delete_option('custom_texo_project_id'); 
			} 
		}
	}
	add_action( 'init', 'nexcraft_project_taxonomy' );

?>