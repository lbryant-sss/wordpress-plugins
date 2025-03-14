<?php
/**
 * Class ThemesView_bwg
 */
class ThemesView_bwg extends AdminView_bwg {
  public function __construct() {
    parent::__construct();
  	wp_enqueue_script(BWG()->prefix . '_jscolor');
    wp_enqueue_script(BWG()->prefix . '_fontselect');
  }

  /**
   * Display page.
   *
   * @param $params
   */
  public function display( $params = array() ) {
    ob_start();
    echo $this->body($params);
    // Pass the content to form.
    $form_attr = array(
      'id' => BWG()->prefix . '_themes',
      'name' => BWG()->prefix . '_themes',
      'class' => BWG()->prefix . '_themes wd-form',
      'action' => add_query_arg(array( 'page' => 'themes_' . BWG()->prefix ), 'admin.php'),
    );
    echo $this->form(ob_get_clean(), $form_attr);
  }

  /**
   * Generate page body.
   *
   * @param $params
   */
  public function body( $params = array() ) {
    $order = $params['order'];
    $orderby = $params['orderby'];
    $actions = $params['actions'];
    $page = $params['page'];
    $total = $params['total'];
    $items_per_page = $params['items_per_page'];
    $rows_data = $params['rows_data'];
    $page_url = add_query_arg(array(
                                'page' => $page,
                                BWG()->nonce => wp_create_nonce(BWG()->nonce),
                              ), admin_url('admin.php'));
    echo $this->title(array(
                        'title' => $params['page_title'],
                        'title_class' => 'wd-header',
                        'add_new_button' => array(
							          'href' => add_query_arg(array( 'page' => $page, 'task' => 'edit' ), admin_url('admin.php')),
                        ),
                        'add_new_button_text' => __('Add new theme', 'photo-gallery'),
                      ));
    echo $this->search();
    ?>
    <div class="tablenav top">
      <?php
      echo $this->bulk_actions($actions, TRUE);
      echo $this->pagination($page_url, $total, $items_per_page);
      ?>
    </div>
    <table class="adminlist table table-striped wp-list-table widefat fixed pages">
      <thead>
      <tr>
        <td id="cb" class="column-cb check-column">
          <label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select all', 'photo-gallery'); ?></label>
          <input id="check_all" type="checkbox" />
        </td>
        <?php echo WDWLibrary::ordering('name', $orderby, $order, __('Title', 'photo-gallery'), $page_url, 'column-primary'); ?>
        <?php echo WDWLibrary::ordering('default_theme', $orderby, $order, __('Default', 'photo-gallery'), $page_url, 'column-primary'); ?>
      </tr>
      </thead>
      <tbody>
      <?php
      if ( $rows_data ) {
        foreach ( $rows_data as $row_data ) {
          $alternate = (!isset($alternate) || $alternate == '') ? 'class="alternate"' : '';

          $edit_url = add_query_arg(array( 'page' => $page, 'task' => 'edit', 'current_id' => $row_data->id ), admin_url('admin.php'));
          $duplicate_url = add_query_arg(array( 'task' => 'duplicate', 'current_id' => $row_data->id ), $page_url);
          $delete_url = add_query_arg(array( 'task' => 'delete', 'current_id' => $row_data->id ), $page_url);
          $default_url = add_query_arg(array( 'task' => 'setdefault', 'current_id' => $row_data->id ), $page_url);
          ?>
          <tr id="tr_<?php echo $row_data->id; ?>" <?php echo $alternate; ?>>
            <th class="check-column">
              <input id="check_<?php echo $row_data->id; ?>" name="check[<?php echo $row_data->id; ?>]" type="checkbox" onclick="spider_check_all(this)" />
            </th>
            <td class="column-primary" data-colname="<?php _e('Title', 'photo-gallery'); ?>">
              <strong>
                <a href="<?php echo $edit_url; ?>"><?php echo $row_data->name; ?></a>
              </strong>
              <div class="row-actions">
                <span><a href="<?php echo $edit_url; ?>"><?php _e('Edit', 'photo-gallery'); ?></a> |</span>
                <span><a href="<?php echo $duplicate_url; ?>"><?php _e('Duplicate', 'photo-gallery'); ?></a> |</span>
                <span class="trash"><a onclick="if (!confirm('<?php echo addslashes(__('Do you want to delete selected item?', 'photo-gallery')); ?>')) {return false;}" href="<?php echo $delete_url; ?>"><?php _e('Delete', 'photo-gallery'); ?></a></span>
              </div>
              <button class="toggle-row" type="button">
                <span class="screen-reader-text"><?php _e('Show more details', 'photo-gallery'); ?></span>
              </button>
            </td>
            <td class="col_default" data-colname="<?php _e('Default', 'photo-gallery'); ?>">
              <?php
              $default = ($row_data->default_theme) ? 1 : 0;
              if (!$default) {
                ?>
              <a href="<?php echo $default_url; ?>">
               <?php
              }
              ?>
                <span class="dashicons dashicons-star-filled <?php echo ($default ? 'wd-yellow' : 'wd-grey'); ?>"></span>
              <?php
              if ($default) {
              ?>
              </a>
               <?php
              }
              ?>
            </td>
          </tr>
          <?php
        }
      }
      else {
        echo WDWLibrary::no_items('themes', 3);
      }
      ?>
      </tbody>
    </table>
    <?php
  }

  /**
   * Generate row for font styles google fonts
   *
   * @param $saved_style
   * @param $font_style
   * @param $label_text
   * @param $radio_name
   */
  public function font_style_row( $saved_style, $font_style, $label_text, $radio_name) {
    $google_fonts = WDWLibrary::get_google_fonts();
    $font_families = array(
      'arial' => 'Arial',
      'lucida grande' => 'Lucida grande',
      'segoe ui' => 'Segoe ui',
      'tahoma' => 'Tahoma',
      'trebuchet ms' => 'Trebuchet ms',
      'verdana' => 'Verdana',
      'cursive' =>'Cursive',
      'fantasy' => 'Fantasy',
      'monospace' => 'Monospace',
      'serif' => 'Serif',
    );
    $is_google_fonts = (in_array($saved_style, $google_fonts)) ? true : false;
    ?>
      <td class="spider_label"><label for="<?php $font_style ?>"><?php  echo $label_text ?> </label></td>
      <td>
        <input value="<?php echo $is_google_fonts ?  $saved_style : 'Ubuntu'; ?>" name="<?php echo $font_style; ?>" id="<?php echo $font_style; ?>" class="google_font" type="text">
        <select  name="<?php echo $font_style . '_default'; ?>" id="<?php echo $font_style . '_default'; ?>" class="default-font" style="display:<?php echo $is_google_fonts ? 'none' : 'block'; ?>; font-family:<?php echo $saved_style; ?>" >
          <?php
          foreach ( $font_families as $key => $font_family ) {
            ?>
            <option value="<?php echo esc_attr($key); ?>" <?php echo (($saved_style == $key) ? 'selected="selected"' : ''); ?> style="font-family:<?php echo $font_family; ?>"><?php echo esc_html($font_family); ?></option>
            <?php
          }
          ?>
        </select>
        <div class="radio_google_fonts">
          <input type="radio" name="<?php echo $radio_name; ?>" id="<?php echo $radio_name . '1'; ?>" onchange="bwg_change_fonts('<?php echo $font_style; ?>', jQuery(this).attr('id'))" value="1" <?php if ($is_google_fonts == true) echo 'checked="checked"'; ?> />
          <label for="<?php echo $radio_name . '1'; ?>" id="<?php echo $radio_name . '1_lbl'; ?>"><?php echo __('Google fonts', 'photo-gallery'); ?></label>
          <input type="radio" name="<?php echo $radio_name; ?>" id="<?php echo $radio_name . '0'; ?>" onchange="bwg_change_fonts('<?php echo $font_style; ?>', jQuery(this).attr('id') )" value="0" <?php if ($is_google_fonts == false) echo 'checked="checked"'; ?> />
          <label for="<?php echo $radio_name . '0'; ?>" id="<?php echo $radio_name . '0_lbl'; ?>"><?php echo __('Default', 'photo-gallery'); ?></label>
        </div>
      </td>
    <?php
  }

	/**
    * Edit.
	*
    * @param  array  $params.
    * @return string html.
	*/
	public function edit( $params = array() ) {
		ob_start();
		if ( $params['reset'] ) {
			echo WDWLibrary::message_id(17);
		}
		echo $this->edit_body($params);
		// Pass the content to form.
		$form_attr = array(
		  'id' => BWG()->prefix . '_themes',
		  'name' => BWG()->prefix . '_themes',
		  'class' => BWG()->prefix . '_themes wd-form',
		  'action' => $params['form_action'],
		  'current_id' => $params['id'],
		);
		echo $this->form(ob_get_clean(), $form_attr);
	}

	/**
	* Generate page edit body.
	*
	* @param $params
	*/
	public function edit_body( $params = array() ) {
		extract($params);
    ?>
		<div class="bwg-page-header">
			<div class="wd-page-title wd-header">
				<h1 class="wp-heading-inline"><?php _e('Theme title', 'photo-gallery'); ?></h1>
				<input type="text" id="name" name="name" value="<?php echo !empty( $row->name ) ? esc_attr($row->name) : ''; ?>" class="spider_text_input bwg_requried">
        <div class="bwg-page-actions">
					<button class="tw-button-primary button-large" onclick="if (spider_check_required('name', 'Title')) {return false;}; spider_set_input_value('task', 'save')">
					<?php echo !empty($row->name) ? __('Update', 'photo-gallery') :  __('Save', 'photo-gallery'); ?>
					</button>
					<?php if ( $id ) { ?>
					<input title="<?php _e('Reset to default theme', 'photo-gallery'); ?>"
						class="tw-button-secondary preview-button button-large wd-btn-reset" type="submit"
						onclick="if (confirm('<?php echo addslashes(__('Do you want to reset to default?', 'photo-gallery')); ?>')) {
																spider_set_input_value('task', 'reset');
															} else {
																return false;
															}"
						value="<?php echo __('Reset', 'photo-gallery'); ?>"/>
					<?php } ?>
				</div>
			</div>
			<div class="bwg-clear"></div>
		</div>
		<ul class="bwg-tabs">
			<?php foreach($tabs as $key => $value) { ?>
			<li data-id="<?php echo $key ?>" class="bwg-tab-item <?php echo ($params['active_tab'] == $key) ? 'active': ''; ?>"><?php echo $value; ?></li>
			<?php } ?>
		</ul>
		<div>
			<fieldset id="Thumbnail" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Thumbnail_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
						  <div class="wd-box-content">
							<table style="clear:both;">
								<tbody>
								<tr>
								  <td class="spider_label"><label for="thumb_margin"><?php _e('Distance between pictures:', 'photo-gallery'); ?> </label></td>
								  <td>
									  <input type="text" name="thumb_margin" id="thumb_margin" value="<?php echo esc_attr($row->thumb_margin); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
                <tr>
                  <td class="spider_label"><label><?php _e('Distance from container frame:', 'photo-gallery'); ?> </label></td>
                  <td>
                    <input type="radio" name="container_margin" id="container_margin1" value="1"<?php if ($row->container_margin == 1) echo 'checked="checked"'; ?> />
                    <label for="container_margin1"><?php _e('Yes', 'photo-gallery'); ?></label>
                    <input type="radio" name="container_margin" id="container_margin0" value="0"<?php if ($row->container_margin == 0) echo 'checked="checked"'; ?> />
                    <label for="container_margin0"><?php _e('No', 'photo-gallery'); ?></label>
                    <div class="spider_description"><?php _e('Enable this option to add distance between the parent container and the thumbnails grid.', 'photo-gallery'); ?></div>
                  </td>
                </tr>
								<tr>
								  <td class="spider_label"><label for="thumb_padding"><?php echo __('Padding:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="thumb_padding" id="thumb_padding" value="<?php echo esc_attr($row->thumb_padding); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="thumb_border_width"><?php echo __('Border width:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="thumb_border_width" id="thumb_border_width" value="<?php echo esc_attr($row->thumb_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="thumb_border_style"><?php echo __('Border style:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="thumb_border_style" id="thumb_border_style">
									  <?php
									  foreach ($border_styles as $key => $border_style) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->thumb_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="thumb_border_color"><?php echo __('Border color:', 'photo-gallery'); ?></label></td>
								  <td>
									<input type="text" name="thumb_border_color" id="thumb_border_color" value="<?php echo esc_attr($row->thumb_border_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="thumb_border_radius"><?php echo __('Border radius:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="thumb_border_radius" id="thumb_border_radius" value="<?php echo esc_attr($row->thumb_border_radius); ?>" class="spider_char_input" />
									<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="thumb_box_shadow"><?php echo __('Shadow:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="thumb_box_shadow" id="thumb_box_shadow" value="<?php echo esc_attr($row->thumb_box_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
									<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="thumb_hover_effect"><?php echo __('Hover effect:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="thumb_hover_effect" id="thumb_hover_effect">
									  <?php
									  foreach ($thumbnail_hover_effects as $key => $hover_effect) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->thumb_hover_effect == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($hover_effect, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="thumb_hover_effect_value"><?php echo __('Hover effect value:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="thumb_hover_effect_value" id="thumb_hover_effect_value" value="<?php echo esc_attr($row->thumb_hover_effect_value); ?>" class="spider_char_input"/>
									<div class="spider_description"><?php echo __('E.g. Rotate: 10deg, Scale/Zoom: 1.5, Skew: 10deg.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label><?php echo __('Transition:', 'photo-gallery'); ?> </label></td>
								  <td id="thumb_transition">
									<input type="radio" name="thumb_transition" id="thumb_transition1" value="1"<?php if ($row->thumb_transition == 1) echo 'checked="checked"'; ?> />
									<label for="thumb_transition1" id="thumb_transition1_lbl"><?php echo __('Yes', 'photo-gallery'); ?></label>
									<input type="radio" name="thumb_transition" id="thumb_transition0" value="0"<?php if ($row->thumb_transition == 0) echo 'checked="checked"'; ?> />
									<label for="thumb_transition0" id="thumb_transition0_lbl"><?php echo __('No', 'photo-gallery'); ?></label>
								  </td>
								</tr>
							  </tbody>
							</table>
						</div>
						</div>
					</div>
					<div id="Thumbnail_2" class="wd-table-col wd-table-col-30">
						<div class="wd-box-section">
							<div class="wd-box-content">
							<table style="clear:both;">
							  <tbody>
								<tr>
								  <td class="spider_label">
									  <label for="thumb_bg_color"><?php echo __('Thumbnail background color:', 'photo-gallery'); ?> </label>
								  </td>
								  <td>
                    <input type="text" name="thumb_bg_color" id="thumb_bg_color" value="<?php echo esc_attr($row->thumb_bg_color); ?>" class="jscolor" />
								  </td>
								</tr>
                <tr>
                  <td class="spider_label"><label for="thumb_bg_transparency"><?php echo __('Thumbnail background transparency:', 'photo-gallery'); ?> </label></td>
                  <td>
                    <input type="text" name="thumb_bg_transparency" id="thumb_bg_transparency" value="<?php echo esc_attr($row->thumb_bg_transparency); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
                    <div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
                  </td>
                </tr>
								<tr>
								  <td class="spider_label"><label for="thumb_transparent"><?php echo __('Thumbnail transparency:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="thumb_transparent" id="thumb_transparent" value="<?php echo esc_attr($row->thumb_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
									<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="thumbs_bg_color"><?php echo __('Full background color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="thumbs_bg_color" id="thumbs_bg_color" value="<?php echo esc_attr($row->thumbs_bg_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="thumb_bg_transparent"><?php echo __('Full background transparency:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="thumb_bg_transparent" id="thumb_bg_transparent" value="<?php echo esc_attr($row->thumb_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
									<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="thumb_align"><?php echo __('Alignment:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="thumb_align" id="thumb_align">
									  <?php
									  foreach ($aligns as $key => $align) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->thumb_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
							  </tbody>
							</table>
							</div>
						</div>
					</div>
					<div id="Thumbnail_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
							<div class="wd-box-section">
								<div class="wd-box-content">
									<table style="clear:both;">
										<tbody>
										<tr>
										  <td class="spider_label"><label><?php echo __('Title position:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="radio" name="thumb_title_pos" id="thumb_title_pos1" value="top" <?php if ($row->thumb_title_pos == "top") echo 'checked="checked"'; ?> />
											<label for="thumb_title_pos1" id="thumb_title_pos1_lbl"><?php echo __('Top', 'photo-gallery'); ?></label>
											<input type="radio" name="thumb_title_pos" id="thumb_title_pos0" value="bottom" <?php if ($row->thumb_title_pos == "bottom") echo 'checked="checked"'; ?> />
											<label for="thumb_title_pos0" id="thumb_title_pos0_lbl"><?php echo __('Bottom', 'photo-gallery'); ?></label>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="thumb_title_font_size"><?php echo __('Title font size:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="thumb_title_font_size" id="thumb_title_font_size" value="<?php echo esc_attr($row->thumb_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="thumb_title_font_color"><?php echo __('Title font color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="thumb_title_font_color" id="thumb_title_font_color" value="<?php echo esc_attr($row->thumb_title_font_color); ?>" class="jscolor" />
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="thumb_title_font_color_hover"><?php echo __('Title font color (Show on hover):', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="thumb_title_font_color_hover" id="thumb_title_font_color_hover" value="<?php echo esc_attr($row->thumb_title_font_color_hover); ?>" class="jscolor" />
										  </td>
										</tr>
                    <tr>
                      <!--generate font style with google fonts -->
                      <?php $this->font_style_row( $row->thumb_title_font_style, 'thumb_title_font_style', __('Title font family:', 'photo-gallery'), 'thumb_title_google_fonts' ); ?>
                    </tr>
                    <tr>
										  <td class="spider_label"><label for="thumb_title_font_weight"><?php echo __('Title font weight:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="thumb_title_font_weight" id="thumb_title_font_weight">
											  <?php
											  foreach ($font_weights as $key => $font_weight) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->thumb_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="thumb_title_shadow"><?php echo __('Title box shadow:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="thumb_title_shadow" id="thumb_title_shadow" value="<?php echo esc_attr($row->thumb_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="thumb_title_margin"><?php echo __('Title margin:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="thumb_title_margin" id="thumb_title_margin" value="<?php echo esc_attr($row->thumb_title_margin); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
                    <tr>
                      <td class="spider_label"><label for="thumb_description_font_size"><?php echo __('Thumb description font size:', 'photo-gallery'); ?> </label></td>
                      <td>
                        <input type="text" name="thumb_description_font_size" id="thumb_description_font_size" value="<?php echo
                        esc_attr($row->thumb_description_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
                      </td>
                    </tr>
                    <tr>
                      <td class="spider_label"><label for="thumb_description_font_color"><?php echo __('Thumb description font color:', 'photo-gallery'); ?> </label></td>
                      <td>
                        <input type="text" name="thumb_description_font_color" id="thumb_description_font_color" value="<?php echo esc_attr($row->thumb_description_font_color); ?>" class="jscolor" />
                      </td>
                    </tr>
                    <tr>
                      <!--generate font style with google fonts -->
                      <?php $this->font_style_row( $row->thumb_description_font_style, 'thumb_description_font_style', __('Description font family:', 'photo-gallery'), 'thumb_description_google_fonts' ); ?>
                    </tr>
                    <tr>
										  <td class="spider_label"><label for="thumb_gal_title_font_size"><?php echo __('Gallery title/description font size:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="thumb_gal_title_font_size" id="thumb_gal_title_font_size" value="<?php echo
                                            esc_attr($row->thumb_gal_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="thumb_gal_title_font_color"><?php echo __('Gallery title/description font color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="thumb_gal_title_font_color" id="thumb_gal_title_font_color" value="<?php echo esc_attr($row->thumb_gal_title_font_color); ?>" class="jscolor" />
										  </td>
										</tr>
                    <tr>
                      <!--generate font style with google fonts -->
                      <?php $this->font_style_row( $row->thumb_gal_title_font_style, 'thumb_gal_title_font_style', __('Gallery title/description font family:', 'photo-gallery'), 'thumb_gal_title_google_fonts' ); ?>
                    </tr>
                    <tr>
										  <td class="spider_label"><label for="thumb_gal_title_font_weight"><?php echo __('Gallery title/description font weight:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="thumb_gal_title_font_weight" id="thumb_gal_title_font_weight">
											  <?php
											  foreach ($font_weights as $key => $font_weight) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->thumb_gal_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="thumb_gal_title_shadow"><?php echo __('Gallery title/description box shadow:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="thumb_gal_title_shadow" id="thumb_gal_title_shadow" value="<?php echo esc_attr($row->thumb_gal_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="thumb_gal_title_margin"><?php echo __('Gallery title/description margin:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="thumb_gal_title_margin" id="thumb_gal_title_margin" value="<?php echo esc_attr($row->thumb_gal_title_margin); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="thumb_gal_title_align"><?php echo __('Gallery title alignment:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="thumb_gal_title_align" id="thumb_gal_title_align">
											  <?php
											  foreach ($aligns as $key => $align) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->thumb_gal_title_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
									  </tbody>
									</table>
								</div>
							</div>
						</div>
				</div>
			</fieldset>
			<fieldset id="Masonry" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Masonry_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_padding"><?php echo __('Distance between pictures:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_thumb_padding" id="masonry_thumb_padding" value="<?php echo esc_attr($row->masonry_thumb_padding); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
										<td class="spider_label"><label><?php _e('Distance from container frame:', 'photo-gallery'); ?> </label></td>
										<td>
										  <input type="radio" name="masonry_container_margin" id="masonry_container_margin1" value="1"<?php if ($row->masonry_container_margin == 1) echo 'checked="checked"'; ?> />
										  <label for="masonry_container_margin1"><?php _e('Yes', 'photo-gallery'); ?></label>
										  <input type="radio" name="masonry_container_margin" id="masonry_container_margin0" value="0"<?php if ($row->masonry_container_margin == 0) echo 'checked="checked"'; ?> />
										  <label for="masonry_container_margin0"><?php _e('No', 'photo-gallery'); ?></label>
										  <div class="spider_description"><?php _e('Enable this option to add distance between the parent container and the thumbnails grid.', 'photo-gallery'); ?></div>
										</td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_border_width"><?php echo __('Border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_thumb_border_width" id="masonry_thumb_border_width" value="<?php echo esc_attr($row->masonry_thumb_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_border_style"><?php echo __('Border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="masonry_thumb_border_style" id="masonry_thumb_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->masonry_thumb_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_border_color"><?php echo __('Border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_thumb_border_color" id="masonry_thumb_border_color" value="<?php echo esc_attr($row->masonry_thumb_border_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_border_radius"><?php echo __('Border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_thumb_border_radius" id="masonry_thumb_border_radius" value="<?php echo esc_attr($row->masonry_thumb_border_radius); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
																		<tr>
									  <td class="spider_label"><label for="masonry_thumb_hover_effect"><?php echo __('Hover effect:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="masonry_thumb_hover_effect" id="masonry_thumb_hover_effect">
										  <?php
										  foreach ($thumbnail_hover_effects as $key => $hover_effect) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->masonry_thumb_hover_effect == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($hover_effect, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_hover_effect_value"><?php echo __('Hover effect value:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_thumb_hover_effect_value" id="masonry_thumb_hover_effect_value" value="<?php echo esc_attr($row->masonry_thumb_hover_effect_value); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('E.g. Rotate: 10deg, Scale: 1.5, Skew: 10deg.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label><?php echo __('Transition:', 'photo-gallery'); ?> </label></td>
									  <td id="masonry_thumb_transition">
										<input type="radio" name="masonry_thumb_transition" id="masonry_thumb_transition1" value="1"<?php if ($row->masonry_thumb_transition == 1) echo 'checked="checked"'; ?> />
										<label for="masonry_thumb_transition1" id="masonry_thumb_transition1_lbl"><?php echo __('Yes', 'photo-gallery'); ?></label>
										<input type="radio" name="masonry_thumb_transition" id="masonry_thumb_transition0" value="0"<?php if ($row->masonry_thumb_transition == 0) echo 'checked="checked"'; ?> />
										<label for="masonry_thumb_transition0" id="masonry_thumb_transition0_lbl"><?php echo __('No', 'photo-gallery'); ?></label>
									  </td>
									</tr>									
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Masonry_2" class="wd-table-col wd-table-col-30">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
										<td class="spider_label">
											<label for="masonry_thumb_bg_color"><?php echo __('Thumbnail background color:', 'photo-gallery'); ?> </label>
										</td>
										<td>
											<input type="text" name="masonry_thumb_bg_color" id="masonry_thumb_bg_color" value="<?php echo esc_attr($row->masonry_thumb_bg_color); ?>" class="jscolor" />
										</td>
									</tr>
                  <tr>
                    <td class="spider_label"><label for="masonry_thumb_bg_transparency"><?php echo __('Thumbnail background transparency:', 'photo-gallery'); ?> </label></td>
                    <td>
                      <input type="text" name="masonry_thumb_bg_transparency" id="masonry_thumb_bg_transparency" value="<?php echo esc_attr($row->masonry_thumb_bg_transparency); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
                      <div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
                    </td>
                  </tr>
									<tr>
										<td class="spider_label"><label for="masonry_thumb_transparent"><?php echo __('Transparency:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="masonry_thumb_transparent" id="masonry_thumb_transparent" value="<?php echo esc_attr($row->masonry_thumb_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
											<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="masonry_thumbs_bg_color"><?php echo __('Full Background color:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="masonry_thumbs_bg_color" id="masonry_thumbs_bg_color" value="<?php echo esc_attr($row->masonry_thumbs_bg_color); ?>" class="jscolor" />
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="masonry_thumb_bg_transparent"><?php echo __('Background transparency:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="masonry_thumb_bg_transparent" id="masonry_thumb_bg_transparent" value="<?php echo esc_attr($row->masonry_thumb_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
											<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="masonry_thumb_align0"><?php echo __('Alignment:', 'photo-gallery'); ?> </label></td>
										<td>
											<select name="masonry_thumb_align" id="masonry_thumb_align">
											<?php foreach ($aligns as $key => $align) { ?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->masonry_thumb_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php } ?>
										</select>
										</td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Masonry_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
										<tr>
											<td class="spider_label"><label for="masonry_thumb_title_font_size"><?php echo __('Title font size:', 'photo-gallery'); ?> </label></td>
											<td>
												<input type="text" name="masonry_thumb_title_font_size" id="masonry_thumb_title_font_size" value="<?php echo esc_attr($row->masonry_thumb_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
											</td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="masonry_thumb_title_font_color"><?php echo __('Title font color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="masonry_thumb_title_font_color" id="masonry_thumb_title_font_color" value="<?php echo esc_attr($row->masonry_thumb_title_font_color); ?>" class="jscolor" />
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="masonry_thumb_title_font_color_hover"><?php echo __('Title font color (Show on hover):', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="masonry_thumb_title_font_color_hover" id="masonry_thumb_title_font_color_hover" value="<?php echo esc_attr($row->masonry_thumb_title_font_color_hover); ?>" class="jscolor" />
										  </td>
										</tr>
                    <tr>
                      <!--generate font style with google fonts -->
                      <?php $this->font_style_row( $row->masonry_thumb_title_font_style, 'masonry_thumb_title_font_style', __('Title font family:', 'photo-gallery'), 'masonry_thumb_title_google_fonts' ); ?>
                    </tr>
										<tr>
										  <td class="spider_label"><label for="masonry_thumb_title_font_weight"><?php echo __('Title font weight:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="masonry_thumb_title_font_weight" id="masonry_thumb_title_font_weight">
											  <?php
											  foreach ($font_weights as $key => $font_weight) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->masonry_thumb_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
											<td class="spider_label"><label for="masonry_thumb_title_margin"><?php echo __('Title margin:', 'photo-gallery'); ?> </label></td>
											<td>
												<input type="text" name="masonry_thumb_title_margin" id="masonry_thumb_title_margin" value="<?php echo esc_attr($row->masonry_thumb_title_margin); ?>" class="spider_char_input" />
												<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
											</td>
										</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_description_font_size"><?php echo __('Description font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_description_font_size" id="masonry_description_font_size" value="<?php echo esc_attr($row->masonry_description_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_description_color"><?php echo __('Description font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_description_color" id="masonry_description_color" value="<?php echo esc_attr($row->masonry_description_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->masonry_description_font_style, 'masonry_description_font_style', __('Description font family:', 'photo-gallery'), 'masonry_description_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_gal_title_font_size"><?php echo __('Gallery title/description font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_thumb_gal_title_font_size" id="masonry_thumb_gal_title_font_size" value="<?php echo esc_attr($row->masonry_thumb_gal_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_gal_title_font_color"><?php echo __('Gallery title/description font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_thumb_gal_title_font_color" id="masonry_thumb_gal_title_font_color" value="<?php echo esc_attr($row->masonry_thumb_gal_title_font_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->masonry_thumb_gal_title_font_style, 'masonry_thumb_gal_title_font_style', __('Gallery title/description font family:', 'photo-gallery'), 'masonry_thumb_gal_title_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_gal_title_font_weight"><?php echo __('Gallery title/description font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="masonry_thumb_gal_title_font_weight" id="masonry_thumb_gal_title_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->masonry_thumb_gal_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_gal_title_shadow"><?php echo __('Gallery title/description box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_thumb_gal_title_shadow" id="masonry_thumb_gal_title_shadow" value="<?php echo esc_attr($row->masonry_thumb_gal_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_gal_title_margin"><?php echo __('Gallery title/description margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="masonry_thumb_gal_title_margin" id="masonry_thumb_gal_title_margin" value="<?php echo esc_attr($row->masonry_thumb_gal_title_margin); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="masonry_thumb_gal_title_align"><?php echo __('Gallery title alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="masonry_thumb_gal_title_align" id="masonry_thumb_gal_title_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->masonry_thumb_gal_title_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<fieldset id="Mosaic" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Mosaic_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="mosaic_thumb_padding"><?php echo __('Distance between pictures:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="mosaic_thumb_padding" id="mosaic_thumb_padding" value="<?php echo esc_attr($row->mosaic_thumb_padding); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
                  <tr>
                    <td class="spider_label"><label><?php _e('Distance from container frame:', 'photo-gallery'); ?> </label></td>
                    <td>
                      <input type="radio" name="mosaic_container_margin" id="mosaic_container_margin1" value="1"<?php if ($row->mosaic_container_margin == 1) echo 'checked="checked"'; ?> />
                      <label for="mosaic_container_margin1"><?php _e('Yes', 'photo-gallery'); ?></label>
                      <input type="radio" name="mosaic_container_margin" id="mosaic_container_margin0" value="0"<?php if ($row->mosaic_container_margin == 0) echo 'checked="checked"'; ?> />
                      <label for="mosaic_container_margin0"><?php _e('No', 'photo-gallery'); ?></label>
                      <div class="spider_description"><?php _e('Enable this option to add distance between the parent container and the thumbnails grid.', 'photo-gallery'); ?></div>
                    </td>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="mosaic_thumb_border_width"><?php echo __('Border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="mosaic_thumb_border_width" id="mosaic_thumb_border_width" value="<?php echo esc_attr($row->mosaic_thumb_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="mosaic_thumb_border_style"><?php echo __('Border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="mosaic_thumb_border_style" id="mosaic_thumb_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->mosaic_thumb_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="mosaic_thumb_border_color"><?php echo __('Border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="mosaic_thumb_border_color" id="mosaic_thumb_border_color" value="<?php echo esc_attr($row->mosaic_thumb_border_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="mosaic_thumb_border_radius"><?php echo __('Border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="mosaic_thumb_border_radius" id="mosaic_thumb_border_radius" value="<?php echo esc_attr($row->mosaic_thumb_border_radius); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
										<td class="spider_label"><label for="mosaic_thumb_hover_effect"><?php echo __('Hover effect:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="mosaic_thumb_hover_effect" id="mosaic_thumb_hover_effect">
											  <?php
											  foreach ($thumbnail_hover_effects as $key => $hover_effect) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->mosaic_thumb_hover_effect == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($hover_effect, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_hover_effect_value"><?php echo __('Hover effect value:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="mosaic_thumb_hover_effect_value" id="mosaic_thumb_hover_effect_value" value="<?php echo esc_attr($row->mosaic_thumb_hover_effect_value); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('E.g. Rotate: 10deg, Scale: 1.5, Skew: 10deg.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label><?php echo __('Transition:', 'photo-gallery'); ?> </label></td>
										  <td id="mosaic_thumb_transition">
											<input type="radio" name="mosaic_thumb_transition" id="mosaic_thumb_transition1" value="1"<?php if ($row->mosaic_thumb_transition == 1) echo 'checked="checked"'; ?> />
											<label for="mosaic_thumb_transition1" id="mosaic_thumb_transition1_lbl"><?php echo __('Yes', 'photo-gallery'); ?></label>
											<input type="radio" name="mosaic_thumb_transition" id="mosaic_thumb_transition0" value="0"<?php if ($row->mosaic_thumb_transition == 0) echo 'checked="checked"'; ?> />
											<label for="mosaic_thumb_transition0" id="mosaic_thumb_transition0_lbl"><?php echo __('No', 'photo-gallery'); ?></label>
										  </td>
										</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Mosaic_2" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="mosaic_thumb_bg_color"><?php echo __('Thumbnail background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="mosaic_thumb_bg_color" id="mosaic_thumb_bg_color" value="<?php echo esc_attr($row->mosaic_thumb_bg_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <td class="spider_label"><label for="mosaic_thumb_bg_transparency"><?php echo __('Thumbnail background transparency:', 'photo-gallery'); ?> </label></td>
                    <td>
                      <input type="text" name="mosaic_thumb_bg_transparency" id="mosaic_thumb_bg_transparency" value="<?php echo esc_attr($row->mosaic_thumb_bg_transparency); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
                      <div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
                    </td>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="mosaic_thumb_transparent"><?php echo __('Transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="mosaic_thumb_transparent" id="mosaic_thumb_transparent" value="<?php echo esc_attr($row->mosaic_thumb_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="mosaic_thumbs_bg_color"><?php echo __('Full Background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="mosaic_thumbs_bg_color" id="mosaic_thumbs_bg_color" value="<?php echo esc_attr($row->mosaic_thumbs_bg_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="mosaic_thumb_bg_transparent"><?php echo __('Background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="mosaic_thumb_bg_transparent" id="mosaic_thumb_bg_transparent" value="<?php echo esc_attr($row->mosaic_thumb_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="mosaic_thumb_align0"><?php echo __('Alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="mosaic_thumb_align" id="mosaic_thumb_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->mosaic_thumb_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Mosaic_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
									<tbody>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_title_font_size"><?php echo __('Title font size:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="mosaic_thumb_title_font_size" id="mosaic_thumb_title_font_size" value="<?php echo esc_attr($row->mosaic_thumb_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_title_font_color"><?php echo __('Title font color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="mosaic_thumb_title_font_color" id="mosaic_thumb_title_font_color" value="<?php echo esc_attr($row->mosaic_thumb_title_font_color); ?>" class="jscolor" />
										  </td>
										</tr>
                    <tr>
                      <td class="spider_label"><label for="mosaic_thumb_title_font_color_hover"><?php echo __('Title font color (Show on hover):', 'photo-gallery'); ?> </label></td>
                      <td>
                        <input type="text" name="mosaic_thumb_title_font_color_hover" id="mosaic_thumb_title_font_color_hover" value="<?php echo esc_attr($row->mosaic_thumb_title_font_color_hover); ?>" class="jscolor" />
                      </td>
                    </tr>
                    <tr>
                      <!--generate font style with google fonts -->
                      <?php $this->font_style_row( $row->mosaic_thumb_title_font_style, 'mosaic_thumb_title_font_style', __('Title font family:', 'photo-gallery'), 'mosaic_thumb_title_google_fonts' ); ?>
                    </tr>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_title_font_weight"><?php echo __('Title font weight:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="mosaic_thumb_title_font_weight" id="mosaic_thumb_title_font_weight">
											  <?php
											  foreach ($font_weights as $key => $font_weight) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->mosaic_thumb_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_title_shadow"><?php echo __('Title box shadow:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="mosaic_thumb_title_shadow" id="mosaic_thumb_title_shadow" value="<?php echo esc_attr($row->mosaic_thumb_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_title_margin"><?php echo __('Title margin:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="mosaic_thumb_title_margin" id="mosaic_thumb_title_margin" value="<?php echo esc_attr($row->mosaic_thumb_title_margin); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_gal_title_font_size"><?php echo __('Gallery title/description font size:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="mosaic_thumb_gal_title_font_size" id="mosaic_thumb_gal_title_font_size" value="<?php echo esc_attr($row->mosaic_thumb_gal_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_gal_title_font_color"><?php echo __('Gallery title/description font color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="mosaic_thumb_gal_title_font_color" id="mosaic_thumb_gal_title_font_color" value="<?php echo esc_attr($row->mosaic_thumb_gal_title_font_color); ?>" class="jscolor" />
										  </td>
										</tr>
                    <tr>
                      <!--generate font style with google fonts -->
                      <?php $this->font_style_row( $row->mosaic_thumb_gal_title_font_style, 'mosaic_thumb_gal_title_font_style', __('Gallery title/description font family:', 'photo-gallery'), 'mosaic_thumb_gal_title_google_fonts' ); ?>
                    </tr>
                    <tr>
										  <td class="spider_label"><label for="mosaic_thumb_gal_title_font_weight"><?php echo __('Gallery title/description font weight:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="mosaic_thumb_gal_title_font_weight" id="mosaic_thumb_gal_title_font_weight">
											  <?php
											  foreach ($font_weights as $key => $font_weight) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->mosaic_thumb_gal_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_gal_title_shadow"><?php echo __('Gallery title/description box shadow:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="mosaic_thumb_gal_title_shadow" id="mosaic_thumb_gal_title_shadow" value="<?php echo esc_attr($row->mosaic_thumb_gal_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_gal_title_margin"><?php echo __('Gallery title/description margin:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="mosaic_thumb_gal_title_margin" id="mosaic_thumb_gal_title_margin" value="<?php echo esc_attr($row->mosaic_thumb_gal_title_margin); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="mosaic_thumb_gal_title_align"><?php echo __('Gallery title alignment:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="mosaic_thumb_gal_title_align" id="mosaic_thumb_gal_title_align">
											  <?php
											  foreach ($aligns as $key => $align) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->mosaic_thumb_gal_title_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<fieldset id="Slideshow" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Slideshow_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="slideshow_cont_bg_color"><?php echo __('Background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_cont_bg_color" id="slideshow_cont_bg_color" value="<?php echo esc_attr($row->slideshow_cont_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_size"><?php echo __('Right, left buttons size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_rl_btn_size" id="slideshow_rl_btn_size" value="<?php echo esc_attr($row->slideshow_rl_btn_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_play_pause_btn_size"><?php echo __('Play, pause buttons size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_play_pause_btn_size" id="slideshow_play_pause_btn_size" value="<?php echo esc_attr($row->slideshow_play_pause_btn_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_color"><?php echo __('Buttons color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_rl_btn_color" id="slideshow_rl_btn_color" value="<?php echo esc_attr($row->slideshow_rl_btn_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_close_btn_transparent"><?php echo __('Buttons transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_close_btn_transparent" id="slideshow_close_btn_transparent" value="<?php echo esc_attr($row->slideshow_close_btn_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_close_rl_btn_hover_color"><?php echo __('Buttons hover color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_close_rl_btn_hover_color" id="slideshow_close_rl_btn_hover_color" value="<?php echo esc_attr($row->slideshow_close_rl_btn_hover_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_width"><?php echo __('Right, left buttons width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_rl_btn_width" id="slideshow_rl_btn_width" value="<?php echo esc_attr($row->slideshow_rl_btn_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_height"><?php echo __('Right, left buttons height:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_rl_btn_height" id="slideshow_rl_btn_height" value="<?php echo esc_attr($row->slideshow_rl_btn_height); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_bg_color"><?php echo __('Right, left buttons background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_rl_btn_bg_color" id="slideshow_rl_btn_bg_color" value="<?php echo esc_attr($row->slideshow_rl_btn_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_border_width"><?php echo __('Right, left buttons border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_rl_btn_border_width" id="slideshow_rl_btn_border_width" value="<?php echo esc_attr($row->slideshow_rl_btn_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_border_style"><?php echo __('Right, left buttons border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="slideshow_rl_btn_border_style" id="slideshow_rl_btn_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->slideshow_rl_btn_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_border_color"><?php echo __('Right, left buttons border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_rl_btn_border_color" id="slideshow_rl_btn_border_color" value="<?php echo esc_attr($row->slideshow_rl_btn_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_border_radius"><?php echo __('Right, left buttons border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_rl_btn_border_radius" id="slideshow_rl_btn_border_radius" value="<?php echo esc_attr($row->slideshow_rl_btn_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_style"><?php echo __('Right, left buttons style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="slideshow_rl_btn_style" id="slideshow_rl_btn_style">
										  <?php
										  foreach ($button_styles as $key => $button_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->slideshow_rl_btn_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($button_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_rl_btn_box_shadow"><?php echo __('Right, left buttons box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_rl_btn_box_shadow" id="slideshow_rl_btn_box_shadow" value="<?php echo esc_attr($row->slideshow_rl_btn_box_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Slideshow_2" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label><?php echo __('Filmstrip/Slider bullet position:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="slideshow_filmstrip_pos" id="slideshow_filmstrip_pos">
										  <option value="top" <?php echo (($row->slideshow_filmstrip_pos == "top") ? 'selected="selected"' : ''); ?>><?php echo __("Top", 'photo-gallery'); ?></option>
										  <option value="right" <?php echo (($row->slideshow_filmstrip_pos == "right") ? 'selected="selected"' : ''); ?>><?php echo __("Right", 'photo-gallery'); ?></option>
										  <option value="bottom" <?php echo (($row->slideshow_filmstrip_pos == "bottom") ? 'selected="selected"' : ''); ?>><?php echo __("Bottom", 'photo-gallery'); ?></option>
										  <option value="left" <?php echo (($row->slideshow_filmstrip_pos == "left") ? 'selected="selected"' : ''); ?>><?php echo __("Left", 'photo-gallery'); ?></option>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_filmstrip_thumb_margin"><?php echo __('Filmstrip margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_filmstrip_thumb_margin" id="slideshow_filmstrip_thumb_margin" value="<?php echo esc_attr($row->slideshow_filmstrip_thumb_margin); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_filmstrip_thumb_border_width"><?php echo __('Filmstrip border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_filmstrip_thumb_border_width" id="slideshow_filmstrip_thumb_border_width" value="<?php echo esc_attr($row->slideshow_filmstrip_thumb_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_filmstrip_thumb_border_style"><?php echo __('Filmstrip border style:', 'photo-gallery'); ?> </label>
									  </td>
									  <td>
										<select name="slideshow_filmstrip_thumb_border_style" id="slideshow_filmstrip_thumb_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->slideshow_filmstrip_thumb_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_filmstrip_thumb_border_color"><?php echo __('Filmstrip border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_filmstrip_thumb_border_color" id="slideshow_filmstrip_thumb_border_color" value="<?php echo esc_attr($row->slideshow_filmstrip_thumb_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_filmstrip_thumb_border_radius"><?php echo __('Filmstrip border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_filmstrip_thumb_border_radius" id="slideshow_filmstrip_thumb_border_radius" value="<?php echo esc_attr($row->slideshow_filmstrip_thumb_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_filmstrip_thumb_active_border_width"><?php echo __('Filmstrip active border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_filmstrip_thumb_active_border_width" id="slideshow_filmstrip_thumb_active_border_width" value="<?php echo esc_attr($row->slideshow_filmstrip_thumb_active_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/>px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_filmstrip_thumb_active_border_color"><?php echo __('Filmstrip active border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_filmstrip_thumb_active_border_color" id="slideshow_filmstrip_thumb_active_border_color" value="<?php echo esc_attr($row->slideshow_filmstrip_thumb_active_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="tr_appWidth">
									  <td class="spider_label"><label for="slideshow_filmstrip_thumb_deactive_transparent"><?php echo __('Filmstrip deactive transparency: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_filmstrip_thumb_deactive_transparent" id="slideshow_filmstrip_thumb_deactive_transparent" value="<?php echo esc_attr($row->slideshow_filmstrip_thumb_deactive_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_filmstrip_rl_bg_color"><?php echo __('Filmstrip right, left buttons background color: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_filmstrip_rl_bg_color" id="slideshow_filmstrip_rl_bg_color" value="<?php echo esc_attr($row->slideshow_filmstrip_rl_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_filmstrip_rl_btn_color"><?php echo __('Filmstrip right, left buttons color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_filmstrip_rl_btn_color" id="slideshow_filmstrip_rl_btn_color" value="<?php echo esc_attr($row->slideshow_filmstrip_rl_btn_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_filmstrip_rl_btn_size"><?php echo __('Filmstrip right, left buttons size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_filmstrip_rl_btn_size" id="slideshow_filmstrip_rl_btn_size" value="<?php echo esc_attr($row->slideshow_filmstrip_rl_btn_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)" /> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_dots_width"><?php echo __('Slider bullet width: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_dots_width" id="slideshow_dots_width" value="<?php echo esc_attr($row->slideshow_dots_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_dots_height"><?php echo __('Slider bullet height:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_dots_height" id="slideshow_dots_height" value="<?php echo esc_attr($row->slideshow_dots_height); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_dots_border_radius"><?php echo __('Slider bullet border radius: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_dots_border_radius" id="slideshow_dots_border_radius" value="<?php echo esc_attr($row->slideshow_dots_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_dots_background_color"><?php echo __('Slider bullet background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_dots_background_color" id="slideshow_dots_background_color" value="<?php echo esc_attr($row->slideshow_dots_background_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_dots_margin"><?php echo __('Slider bullet margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_dots_margin" id="slideshow_dots_margin" value="<?php echo esc_attr($row->slideshow_dots_margin); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_dots_active_background_color"><?php echo __('Slider bullet active background color: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_dots_active_background_color" id="slideshow_dots_active_background_color" value="<?php echo esc_attr($row->slideshow_dots_active_background_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_dots_active_border_width"><?php echo __('Slider bullet active border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_dots_active_border_width" id="slideshow_dots_active_border_width" value="<?php echo esc_attr($row->slideshow_dots_active_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_dots_active_border_color"><?php echo __('Slider bullet active border color: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_dots_active_border_color" id="slideshow_dots_active_border_color" value="<?php echo esc_attr($row->slideshow_dots_active_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Slideshow_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="slideshow_title_background_color"><?php echo __('Title background color: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_title_background_color" id="slideshow_title_background_color" value="<?php echo esc_attr($row->slideshow_title_background_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_title_opacity"><?php echo __('Title transparency: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_title_opacity" id="slideshow_title_opacity" value="<?php echo esc_attr($row->slideshow_title_opacity); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_title_border_radius"><?php echo __('Title border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_title_border_radius" id="slideshow_title_border_radius" value="<?php echo esc_attr($row->slideshow_title_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_title_padding"><?php echo __('Title padding: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_title_padding" id="slideshow_title_padding" value="<?php echo esc_attr($row->slideshow_title_padding); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_title_font_size"><?php echo __('Title font size: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_title_font_size" id="slideshow_title_font_size" value="<?php echo esc_attr($row->slideshow_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)" /> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_title_color"><?php echo __('Title color: ', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="slideshow_title_color" id="slideshow_title_color" value="<?php echo esc_attr($row->slideshow_title_color); ?>" class="jscolor"/>
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->slideshow_title_font, 'slideshow_title_font', __('Title font family:', 'photo-gallery'), 'slideshow_title_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_description_background_color"><?php echo __('Description background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_description_background_color" id="slideshow_description_background_color" value="<?php echo esc_attr($row->slideshow_description_background_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_description_opacity"><?php echo __('Description transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_description_opacity" id="slideshow_description_opacity" value="<?php echo esc_attr($row->slideshow_description_opacity); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_description_border_radius"><?php echo __('Description border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_description_border_radius" id="slideshow_description_border_radius" value="<?php echo esc_attr($row->slideshow_description_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_description_padding"><?php echo __('Description padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_description_padding" id="slideshow_description_padding" value="<?php echo esc_attr($row->slideshow_description_padding); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_description_font_size"><?php echo __('Description font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_description_font_size" id="slideshow_description_font_size" value="<?php echo esc_attr($row->slideshow_description_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="slideshow_description_color"><?php echo __('Description color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="slideshow_description_color" id="slideshow_description_color" value="<?php echo esc_attr($row->slideshow_description_color); ?>" class="jscolor"/>
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->slideshow_description_font, 'slideshow_description_font', __('Description font family:', 'photo-gallery'), 'slideshow_description_google_fonts' ); ?>
                  </tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<fieldset id="Image_browser" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Image_browser_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="image_browser_full_padding"><?php echo __('Full padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_full_padding" id="image_browser_full_padding" value="<?php echo esc_attr($row->image_browser_full_padding); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_full_bg_color"><?php echo __('Full background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_full_bg_color" id="image_browser_full_bg_color" value="<?php echo esc_attr($row->image_browser_full_bg_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_full_transparent"><?php echo __('Full background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_full_transparent" id="image_browser_full_transparent" value="<?php echo esc_attr($row->image_browser_full_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_full_border_radius"><?php echo __('Full border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_full_border_radius" id="image_browser_full_border_radius" value="<?php echo esc_attr($row->image_browser_full_border_radius); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_full_border_width"><?php echo __('Full border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_full_border_width" id="image_browser_full_border_width" value="<?php echo esc_attr($row->image_browser_full_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_full_border_style"><?php echo __('Full border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="image_browser_full_border_style" id="image_browser_full_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->image_browser_full_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_full_border_color"><?php echo __('Full border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_full_border_color" id="image_browser_full_border_color" value="<?php echo esc_attr($row->image_browser_full_border_color); ?>" class="jscolor" />
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Image_browser_2" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
									<tbody>
										<tr>
										  <td class="spider_label"><label for="image_browser_align0"><?php echo __('Alignment:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="image_browser_align" id="image_browser_align">
											  <?php
											  foreach ($aligns as $key => $align) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->image_browser_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="image_browser_margin"><?php echo __('Margin:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="image_browser_margin" id="image_browser_margin" value="<?php echo esc_attr($row->image_browser_margin); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="image_browser_padding"><?php echo __('Padding:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="image_browser_padding" id="image_browser_padding" value="<?php echo esc_attr($row->image_browser_padding); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="image_browser_border_width"><?php echo __('Border width:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="image_browser_border_width" id="image_browser_border_width" value="<?php echo esc_attr($row->image_browser_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="image_browser_border_style"><?php echo __('Border style:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="image_browser_border_style" id="image_browser_border_style">
											  <?php
											  foreach ($border_styles as $key => $border_style) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->image_browser_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="image_browser_border_color"><?php echo __('Border color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="image_browser_border_color" id="image_browser_border_color" value="<?php echo esc_attr($row->image_browser_border_color); ?>" class="jscolor" />
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="image_browser_border_radius"><?php echo __('Border radius:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="image_browser_border_radius" id="image_browser_border_radius" value="<?php echo esc_attr($row->image_browser_border_radius); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="image_browser_bg_color"><?php echo __('Background color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="image_browser_bg_color" id="image_browser_bg_color" value="<?php echo esc_attr($row->image_browser_bg_color); ?>" class="jscolor" />
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="image_browser_transparent"><?php echo __('Background transparency:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="image_browser_transparent" id="image_browser_transparent" value="<?php echo esc_attr($row->image_browser_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
											<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="image_browser_box_shadow"><?php echo __('Box shadow:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="image_browser_box_shadow" id="image_browser_box_shadow" value="<?php echo esc_attr($row->image_browser_box_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
									  </tbody>
									</table>
							</div>
						</div>
					</div>
					<div id="Image_browser_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label><?php _e('Title position:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="radio" name="image_browser_image_title_align" id="image_browser_image_title_align1" value="top" <?php if ($row->image_browser_image_title_align == "top") echo 'checked="checked"'; ?> />
										<label for="image_browser_image_title_align1" id="image_browser_image_title_align1_lbl"><?php _e('Top', 'photo-gallery'); ?></label>
										<input type="radio" name="image_browser_image_title_align" id="image_browser_image_title_align0" value="bottom" <?php if ($row->image_browser_image_title_align == "bottom") echo 'checked="checked"'; ?> />
										<label for="image_browser_image_title_align0" id="image_browser_image_title_align0_lbl"><?php _e('Bottom', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_image_description_align0"><?php echo __('Title alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="image_browser_image_description_align" id="image_browser_image_description_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->image_browser_image_description_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_img_font_size"><?php echo __('Font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_img_font_size" id="image_browser_img_font_size" value="<?php echo esc_attr($row->image_browser_img_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_img_font_color"><?php echo __('Font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_img_font_color" id="image_browser_img_font_color" value="<?php echo esc_attr($row->image_browser_img_font_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->image_browser_img_font_family, 'image_browser_img_font_family', __('Font family:', 'photo-gallery'), 'image_browser_img_google_fonts' ); ?>
                  </tr>
                  <tr>
									  <td class="spider_label"><label for="image_browser_image_description_margin"><?php echo __('Description margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_image_description_margin" id="image_browser_image_description_margin" value="<?php echo esc_attr($row->image_browser_image_description_margin); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_image_description_padding"><?php echo __('Description padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_image_description_padding" id="image_browser_image_description_padding" value="<?php echo esc_attr($row->image_browser_image_description_padding); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_image_description_border_width"><?php echo __('Description border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_image_description_border_width" id="image_browser_image_description_border_width" value="<?php echo esc_attr($row->image_browser_image_description_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)" /> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_image_description_border_style"><?php echo __('Description border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="image_browser_image_description_border_style" id="image_browser_image_description_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->image_browser_image_description_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_image_description_border_color"><?php echo __('Description border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_image_description_border_color" id="image_browser_image_description_border_color" value="<?php echo esc_attr($row->image_browser_image_description_border_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_image_description_border_radius"><?php echo __('Description border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_image_description_border_radius" id="image_browser_image_description_border_radius" value="<?php echo esc_attr($row->image_browser_image_description_border_radius); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_image_description_bg_color"><?php echo __('Description background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_image_description_bg_color" id="image_browser_image_description_bg_color" value="<?php echo esc_attr($row->image_browser_image_description_bg_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_gal_title_font_size"><?php echo __('Gallery title/description font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_gal_title_font_size" id="image_browser_gal_title_font_size" value="<?php echo
                                        esc_attr($row->image_browser_gal_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_gal_title_font_color"><?php echo __('Gallery title/description font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_gal_title_font_color" id="image_browser_gal_title_font_color" value="<?php echo esc_attr($row->image_browser_gal_title_font_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->image_browser_gal_title_font_style, 'image_browser_gal_title_font_style', __('Gallery title/description font family:', 'photo-gallery'), 'image_browser_gal_title_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_gal_title_font_weight"><?php echo __('Gallery title/description font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="image_browser_gal_title_font_weight" id="image_browser_gal_title_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->image_browser_gal_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_gal_title_shadow"><?php echo __('Gallery title/description box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_gal_title_shadow" id="image_browser_gal_title_shadow" value="<?php echo esc_attr($row->image_browser_gal_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_gal_title_margin"><?php echo __('Gallery title/description margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="image_browser_gal_title_margin" id="image_browser_gal_title_margin" value="<?php echo esc_attr($row->image_browser_gal_title_margin); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="image_browser_gal_title_align"><?php echo __('Gallery title alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="image_browser_gal_title_align" id="image_browser_gal_title_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->image_browser_gal_title_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<fieldset id="Compact_album" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Compact_album_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
									<tbody>
                    <tr>
                      <td class="spider_label"><label for="album_compact_thumb_margin"><?php _e('Distance between pictures:', 'photo-gallery'); ?> </label></td>
                      <td>
                        <input type="text" name="album_compact_thumb_margin" id="album_compact_thumb_margin" value="<?php echo esc_attr($row->album_compact_thumb_margin); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
                      </td>
                    </tr>
                    <tr>
                      <td class="spider_label"><label><?php _e('Distance from container frame:', 'photo-gallery'); ?> </label></td>
                      <td>
                        <input type="radio" name="compact_container_margin" id="compact_container_margin1" value="1"<?php if ($row->compact_container_margin == 1) echo 'checked="checked"'; ?> />
                        <label for="compact_container_margin1"><?php _e('Yes', 'photo-gallery'); ?></label>
                        <input type="radio" name="compact_container_margin" id="compact_container_margin0" value="0"<?php if ($row->compact_container_margin == 0) echo 'checked="checked"'; ?> />
                        <label for="compact_container_margin0"><?php _e('No', 'photo-gallery'); ?></label>
                        <div class="spider_description"><?php _e('Enable this option to add distance between the parent container and the thumbnails grid.', 'photo-gallery'); ?></div>
                      </td>
                    </tr>
										<tr>
										  <td class="spider_label"><label for="album_compact_thumb_padding"><?php echo __('Padding:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_compact_thumb_padding" id="album_compact_thumb_padding" value="<?php echo esc_attr($row->album_compact_thumb_padding); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_compact_thumb_border_width"><?php echo __('Border width:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_compact_thumb_border_width" id="album_compact_thumb_border_width" value="<?php echo esc_attr($row->album_compact_thumb_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_compact_thumb_border_style"><?php echo __('Border style:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="album_compact_thumb_border_style" id="album_compact_thumb_border_style">
											  <?php
											  foreach ($border_styles as $key => $border_style) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_compact_thumb_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_compact_thumb_border_color"><?php echo __('Border color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_compact_thumb_border_color" id="album_compact_thumb_border_color" value="<?php echo esc_attr($row->album_compact_thumb_border_color); ?>" class="jscolor" />
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_compact_thumb_border_radius"><?php echo __('Border radius:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_compact_thumb_border_radius" id="album_compact_thumb_border_radius" value="<?php echo esc_attr($row->album_compact_thumb_border_radius); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_compact_thumb_box_shadow"><?php echo __('Shadow:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_compact_thumb_box_shadow" id="album_compact_thumb_box_shadow" value="<?php echo esc_attr($row->album_compact_thumb_box_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_compact_thumb_hover_effect"><?php echo __('Hover effect:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="album_compact_thumb_hover_effect" id="album_compact_thumb_hover_effect">
											  <?php
											  foreach ($thumbnail_hover_effects as $key => $hover_effect) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_compact_thumb_hover_effect == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($hover_effect, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_compact_thumb_hover_effect_value"><?php echo __('Hover effect value:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_compact_thumb_hover_effect_value" id="album_compact_thumb_hover_effect_value" value="<?php echo esc_attr($row->album_compact_thumb_hover_effect_value); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('E.g. Rotate: 10deg, Scale: 1.5, Skew: 10deg.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label><?php echo __('Thumbnail transition:', 'photo-gallery'); ?> </label></td>
										  <td id="album_compact_thumb_transition">
											<input type="radio" name="album_compact_thumb_transition" id="album_compact_thumb_transition1" value="1"<?php if ($row->album_compact_thumb_transition == 1) echo 'checked="checked"'; ?> />
											<label for="album_compact_thumb_transition1" id="album_compact_thumb_transition1_lbl"><?php echo __('Yes', 'photo-gallery'); ?></label>
											<input type="radio" name="album_compact_thumb_transition" id="album_compact_thumb_transition0" value="0"<?php if ($row->album_compact_thumb_transition == 0) echo 'checked="checked"'; ?> />
											<label for="album_compact_thumb_transition0" id="album_compact_thumb_transition0_lbl"><?php echo __('No', 'photo-gallery'); ?></label>
										  </td>
										</tr>
									  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Compact_album_2" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
									<tbody>
									<tr>
									  <td class="spider_label"><label for="album_compact_thumb_bg_color"><?php echo __('Thumbnail background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_thumb_bg_color" id="album_compact_thumb_bg_color" value="<?php echo esc_attr($row->album_compact_thumb_bg_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <td class="spider_label"><label for="album_compact_thumb_bg_transparency"><?php echo __('Thumbnail background transparency:', 'photo-gallery'); ?> </label></td>
                    <td>
                      <input type="text" name="album_compact_thumb_bg_transparency" id="album_compact_thumb_bg_transparency" value="<?php echo esc_attr($row->album_compact_thumb_bg_transparency); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
                      <div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
                    </td>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_thumb_transparent"><?php echo __('Thumbnail transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_thumb_transparent" id="album_compact_thumb_transparent" value="<?php echo esc_attr($row->album_compact_thumb_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_thumbs_bg_color"><?php echo __('Full background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_thumbs_bg_color" id="album_compact_thumbs_bg_color" value="<?php echo esc_attr($row->album_compact_thumbs_bg_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_thumb_bg_transparent"><?php echo __('Full background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_thumb_bg_transparent" id="album_compact_thumb_bg_transparent" value="<?php echo esc_attr($row->album_compact_thumb_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_thumb_align0"><?php echo __('Alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_compact_thumb_align" id="album_compact_thumb_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_compact_thumb_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Compact_album_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label><?php echo __('Title position:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="radio" name="album_compact_thumb_title_pos" id="album_compact_thumb_title_pos1" value="top" <?php if ($row->album_compact_thumb_title_pos == "top") echo 'checked="checked"'; ?> />
										<label for="album_compact_thumb_title_pos1" id="album_compact_thumb_title_pos1_lbl"><?php echo __('Top', 'photo-gallery'); ?></label>
										<input type="radio" name="album_compact_thumb_title_pos" id="album_compact_thumb_title_pos0" value="bottom" <?php if ($row->album_compact_thumb_title_pos == "bottom") echo 'checked="checked"'; ?> />
										<label for="album_compact_thumb_title_pos0" id="album_compact_thumb_title_pos0_lbl"><?php echo __('Bottom', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_title_font_size"><?php echo __('Title font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_title_font_size" id="album_compact_title_font_size" value="<?php echo esc_attr($row->album_compact_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_title_font_color"><?php echo __('Title font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_title_font_color" id="album_compact_title_font_color" value="<?php echo esc_attr($row->album_compact_title_font_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <td class="spider_label"><label for="album_compact_title_font_color_hover"><?php echo __('Title font color (Show on hover):', 'photo-gallery'); ?> </label></td>
                    <td>
                      <input type="text" name="album_compact_title_font_color_hover" id="album_compact_title_font_color_hover" value="<?php echo esc_attr($row->album_compact_title_font_color_hover); ?>" class="jscolor" />
                    </td>
                  </tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->album_compact_title_font_style, 'album_compact_title_font_style', __('Title font family:', 'photo-gallery'), 'album_compact_title_google_fonts' ); ?>
                  </tr>
                  <tr>
									  <td class="spider_label"><label for="album_compact_title_font_weight"><?php echo __('Title font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_compact_title_font_weight" id="album_compact_title_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_compact_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_title_shadow"><?php echo __('Title box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_title_shadow" id="album_compact_title_shadow" value="<?php echo esc_attr($row->album_compact_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_title_margin"><?php echo __('Title margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_title_margin" id="album_compact_title_margin" value="<?php echo esc_attr($row->album_compact_title_margin); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_back_font_size"><?php echo __('Back Font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_back_font_size" id="album_compact_back_font_size" value="<?php echo esc_attr($row->album_compact_back_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_back_font_color"><?php echo __('Back Font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_back_font_color" id="album_compact_back_font_color" value="<?php echo esc_attr($row->album_compact_back_font_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->album_compact_back_font_style, 'album_compact_back_font_style', __('Back Font family:', 'photo-gallery'), 'album_compact_back_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_back_font_weight"><?php echo __('Back Font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_compact_back_font_weight" id="album_compact_back_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_compact_back_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_back_padding"><?php echo __('Back padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_back_padding" id="album_compact_back_padding" value="<?php echo esc_attr($row->album_compact_back_padding); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_gal_title_font_size"><?php echo __('Gallery title/description font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_gal_title_font_size" id="album_compact_gal_title_font_size" value="<?php echo esc_attr($row->album_compact_gal_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_gal_title_font_color"><?php echo __('Gallery title/description font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_gal_title_font_color" id="album_compact_gal_title_font_color" value="<?php echo esc_attr($row->album_compact_gal_title_font_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->album_compact_gal_title_font_style, 'album_compact_gal_title_font_style', __('Gallery title/description font family:', 'photo-gallery'), 'album_compact_gal_title_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_gal_title_font_weight"><?php echo __('Gallery title/description font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_compact_gal_title_font_weight" id="album_compact_gal_title_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_compact_gal_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_gal_title_shadow"><?php echo __('Gallery title/description box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_gal_title_shadow" id="album_compact_gal_title_shadow" value="<?php echo esc_attr($row->album_compact_gal_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_gal_title_margin"><?php echo __('Gallery title/description margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_compact_gal_title_margin" id="album_compact_gal_title_margin" value="<?php echo esc_attr($row->album_compact_gal_title_margin); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_compact_gal_title_align"><?php echo __('Gallery title alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_compact_gal_title_align" id="album_compact_gal_title_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_compact_gal_title_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<fieldset id="Extended_album" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Extended_album_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
							   <table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_margin"><?php echo __('Thumbnail margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumb_margin" id="album_extended_thumb_margin" value="<?php echo esc_attr($row->album_extended_thumb_margin); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_padding"><?php echo __('Thumbnail padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumb_padding" id="album_extended_thumb_padding" value="<?php echo esc_attr($row->album_extended_thumb_padding); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_border_width"><?php echo __('Thumbnail border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumb_border_width" id="album_extended_thumb_border_width" value="<?php echo esc_attr($row->album_extended_thumb_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_border_style"><?php echo __('Thumbnail border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_extended_thumb_border_style" id="album_extended_thumb_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_thumb_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_border_color"><?php echo __('Thumbnail border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumb_border_color" id="album_extended_thumb_border_color" value="<?php echo esc_attr($row->album_extended_thumb_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_border_radius"><?php echo __('Thumbnail border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumb_border_radius" id="album_extended_thumb_border_radius" value="<?php echo esc_attr($row->album_extended_thumb_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_box_shadow"><?php echo __('Thumbnail box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumb_box_shadow" id="album_extended_thumb_box_shadow" value="<?php echo esc_attr($row->album_extended_thumb_box_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label><?php echo __('Thumbnail transition:', 'photo-gallery'); ?> </label></td>
									  <td id="album_extended_thumb_transition">
										<input type="radio" name="album_extended_thumb_transition" id="album_extended_thumb_transition1" value="1"<?php if ($row->album_extended_thumb_transition == 1) echo 'checked="checked"'; ?> />
										<label for="album_extended_thumb_transition1" id="album_extended_thumb_transition1_lbl"><?php echo __('Yes', 'photo-gallery'); ?></label>
										<input type="radio" name="album_extended_thumb_transition" id="album_extended_thumb_transition0" value="0"<?php if ($row->album_extended_thumb_transition == 0) echo 'checked="checked"'; ?> />
										<label for="album_extended_thumb_transition0" id="album_extended_thumb_transition0_lbl"><?php echo __('No', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_align0"><?php echo __('Description alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_extended_thumb_align" id="album_extended_thumb_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_thumb_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_transparent"><?php echo __('Thumbnail transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumb_transparent" id="album_extended_thumb_transparent" value="<?php echo esc_attr($row->album_extended_thumb_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_hover_effect"><?php echo __('Thumbnail hover effect:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_extended_thumb_hover_effect" id="album_extended_thumb_hover_effect">
										  <?php
										  foreach ($thumbnail_hover_effects as $key => $hover_effect) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_thumb_hover_effect == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($hover_effect, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_hover_effect_value"><?php echo __('Hover effect value:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumb_hover_effect_value" id="album_extended_thumb_hover_effect_value" value="<?php echo esc_attr($row->album_extended_thumb_hover_effect_value); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('E.g. Rotate: 10deg, Scale: 1.5, Skew: 10deg.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_bg_color"><?php echo __('Thumbnail background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumb_bg_color" id="album_extended_thumb_bg_color" value="<?php echo esc_attr($row->album_extended_thumb_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumbs_bg_color"><?php echo __('Thumbnails background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumbs_bg_color" id="album_extended_thumbs_bg_color" value="<?php echo esc_attr($row->album_extended_thumbs_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_extended_thumb_bg_transparent"><?php echo __('Thumbnail background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_extended_thumb_bg_transparent" id="album_extended_thumb_bg_transparent" value="<?php echo esc_attr($row->album_extended_thumb_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
								  </tbody>
								</table>

							</div>
						</div>
					</div>
					<div id="Extended_album_2" class="wd-table-col wd-table-col-30">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
									<tbody>
										<tr>
										  <td class="spider_label"><label for="album_extended_thumb_div_padding"><?php echo __('Thumbnail div padding:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_thumb_div_padding" id="album_extended_thumb_div_padding" value="<?php echo esc_attr($row->album_extended_thumb_div_padding); ?>" class="spider_char_input"/>
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_thumb_div_bg_color"><?php echo __('Thumbnail div background color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_thumb_div_bg_color" id="album_extended_thumb_div_bg_color" value="<?php echo esc_attr($row->album_extended_thumb_div_bg_color); ?>" class="jscolor"/>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_thumb_div_border_width"><?php echo __('Thumbnail div border width:', 'photo-gallery'); ?> </label>
										  </td>
										  <td>
											<input type="text" name="album_extended_thumb_div_border_width" id="album_extended_thumb_div_border_width" value="<?php echo esc_attr($row->album_extended_thumb_div_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_thumb_div_border_style">T<?php echo __('humbnail div border style:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="album_extended_thumb_div_border_style" id="album_extended_thumb_div_border_style">
											  <?php
											  foreach ($border_styles as $key => $border_style) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_thumb_div_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_thumb_div_border_color"><?php echo __('Thumbnail div border color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_thumb_div_border_color" id="album_extended_thumb_div_border_color" value="<?php echo esc_attr($row->album_extended_thumb_div_border_color); ?>" class="jscolor"/>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_thumb_div_border_radius"><?php echo __('Thumbnail div border radius:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_thumb_div_border_radius" id="album_extended_thumb_div_border_radius" value="<?php echo esc_attr($row->album_extended_thumb_div_border_radius); ?>" class="spider_char_input"/>
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_div_margin"><?php echo __('Margin:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_div_margin" id="album_extended_div_margin" value="<?php echo esc_attr($row->album_extended_div_margin); ?>" class="spider_char_input"/>
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_div_padding"><?php echo __('Padding:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_div_padding" id="album_extended_div_padding" value="<?php echo esc_attr($row->album_extended_div_padding); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_div_bg_color"><?php echo __('Background color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_div_bg_color" id="album_extended_div_bg_color" value="<?php echo esc_attr($row->album_extended_div_bg_color); ?>" class="jscolor"/>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_div_bg_transparent"><?php echo __('Background transparency:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_div_bg_transparent" id="album_extended_div_bg_transparent" value="<?php echo esc_attr($row->album_extended_div_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
											<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_div_border_radius"><?php echo __('Border radius:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_div_border_radius" id="album_extended_div_border_radius" value="<?php echo esc_attr($row->album_extended_div_border_radius); ?>" class="spider_char_input"/>
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_div_separator_width"><?php echo __('Separator width:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_div_separator_width" id="album_extended_div_separator_width" value="<?php echo esc_attr($row->album_extended_div_separator_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_div_separator_style"><?php echo __('Separator style:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="album_extended_div_separator_style" id="album_extended_div_separator_style">
											  <?php
											  foreach ($border_styles as $key => $border_style) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_div_separator_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_div_separator_color"><?php echo __('Separator color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_div_separator_color" id="album_extended_div_separator_color" value="<?php echo esc_attr($row->album_extended_div_separator_color); ?>" class="jscolor"/>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_back_padding"><?php echo __('Back padding:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_back_padding" id="album_extended_back_padding" value="<?php echo esc_attr($row->album_extended_back_padding); ?>" class="spider_char_input" />
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_back_font_size"><?php echo __('Back font size:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_back_font_size" id="album_extended_back_font_size" value="<?php echo esc_attr($row->album_extended_back_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										  </td>
										</tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_back_font_color"><?php echo __('Back font color:', 'photo-gallery'); ?> </label></td>
										  <td>
											<input type="text" name="album_extended_back_font_color" id="album_extended_back_font_color" value="<?php echo esc_attr($row->album_extended_back_font_color); ?>" class="jscolor"/>
										  </td>
										</tr>
                    <tr>
                      <!--generate font style with google fonts -->
                      <?php $this->font_style_row( $row->album_extended_back_font_style, 'album_extended_back_font_style', __('Back font family:', 'photo-gallery'), 'album_extended_back_google_fonts' ); ?>
                    </tr>
										<tr>
										  <td class="spider_label"><label for="album_extended_back_font_weight"><?php echo __('Back font weight:', 'photo-gallery'); ?> </label></td>
										  <td>
											<select name="album_extended_back_font_weight" id="album_extended_back_font_weight">
											  <?php
											  foreach ($font_weights as $key => $font_weight) {
												?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_back_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
												<?php
											  }
											  ?>
											</select>
										  </td>
										</tr>
									</tbody>
									</table>
							</div>
						</div>
					</div>
					<div id="Extended_album_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
					<div class="wd-box-section">
						<div class="wd-box-content">
						   <table style="clear:both;">
							  <tbody>
                <tr>
                  <td class="spider_label"><label for="album_extended_title_desc_alignment"><?php _e('Title/description alignment:', 'photo-gallery'); ?></label></td>
                  <td>
                    <select name="album_extended_title_desc_alignment" id="album_extended_title_desc_alignment">
                      <?php
                      foreach ( array('top', 'center', 'bottom') as $val ) {
                        ?>
                        <option value="<?php echo esc_attr($val); ?>" <?php echo (($row->album_extended_title_desc_alignment == $val) ? 'selected="selected"' : ''); ?>><?php echo ucfirst( esc_html__($val, 'photo-gallery') ); ?></option>
                        <?php
                      }
                      ?>
                    </select><div class="spider_description"></div>
                  </td>
                </tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_text_div_padding"><?php echo __('Text div padding:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_text_div_padding" id="album_extended_text_div_padding" value="<?php echo esc_attr($row->album_extended_text_div_padding); ?>" class="spider_char_input" />
									<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_text_div_border_width"><?php echo __('Text div border width:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_text_div_border_width" id="album_extended_text_div_border_width" value="<?php echo esc_attr($row->album_extended_text_div_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_text_div_border_style"><?php echo __('Text border style:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="album_extended_text_div_border_style" id="album_extended_text_div_border_style">
									  <?php
									  foreach ($border_styles as $key => $border_style) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_text_div_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_text_div_border_color"><?php echo __('Text border color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_text_div_border_color" id="album_extended_text_div_border_color" value="<?php echo esc_attr($row->album_extended_text_div_border_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_text_div_border_radius"><?php echo __('Text div border radius:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_text_div_border_radius" id="album_extended_text_div_border_radius" value="<?php echo esc_attr($row->album_extended_text_div_border_radius); ?>" class="spider_char_input"/>
									<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_text_div_bg_color"><?php echo __('Text background color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_text_div_bg_color" id="album_extended_text_div_bg_color" value="<?php echo esc_attr($row->album_extended_text_div_bg_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_title_margin_bottom"><?php echo __('Title margin:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_title_margin_bottom" id="album_extended_title_margin_bottom" value="<?php echo esc_attr($row->album_extended_title_margin_bottom); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_title_padding"><?php echo __('Title padding:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_title_padding" id="album_extended_title_padding" value="<?php echo esc_attr($row->album_extended_title_padding); ?>" class="spider_char_input"/>
									<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_title_span_border_width"><?php echo __('Title border width:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_title_span_border_width" id="album_extended_title_span_border_width" value="<?php echo esc_attr($row->album_extended_title_span_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_title_span_border_style"><?php echo __('Title border style:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="album_extended_title_span_border_style" id="album_extended_title_span_border_style">
									  <?php
									  foreach ($border_styles as $key => $border_style) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_title_span_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_title_span_border_color"><?php echo __('Title border color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_title_span_border_color" id="album_extended_title_span_border_color" value="<?php echo esc_attr($row->album_extended_title_span_border_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_title_font_size"><?php echo __('Title font size:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_title_font_size" id="album_extended_title_font_size" value="<?php echo esc_attr($row->album_extended_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_title_font_color"><?php echo __('Title font color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_title_font_color" id="album_extended_title_font_color" value="<?php echo esc_attr($row->album_extended_title_font_color); ?>" class="jscolor"/>
								  </td>
								</tr>
                <tr>
                  <!--generate font style with google fonts -->
                  <?php $this->font_style_row( $row->album_extended_title_font_style, 'album_extended_title_font_style', __('Title font family:', 'photo-gallery'), 'album_extended_title_google_fonts' ); ?>
                </tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_title_font_weight"><?php echo __('Title font weight:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="album_extended_title_font_weight" id="album_extended_title_font_weight">
									  <?php
									  foreach ($font_weights as $key => $font_weight) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_desc_padding"><?php echo __('Description padding:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_desc_padding" id="album_extended_desc_padding" value="<?php echo esc_attr($row->album_extended_desc_padding); ?>" class="spider_char_input"/>
									<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_desc_span_border_width"><?php echo __('Description border width:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_desc_span_border_width" id="album_extended_desc_span_border_width" value="<?php echo esc_attr($row->album_extended_desc_span_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_desc_span_border_style"><?php echo __('Description border style:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="album_extended_desc_span_border_style" id="album_extended_desc_span_border_style">
									  <?php
									  foreach ($border_styles as $key => $border_style) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_desc_span_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_desc_span_border_color"><?php echo __('Description border color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_desc_span_border_color" id="album_extended_desc_span_border_color" value="<?php echo esc_attr($row->album_extended_desc_span_border_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_desc_font_size"><?php echo __('Description font size:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_desc_font_size" id="album_extended_desc_font_size" value="<?php echo esc_attr($row->album_extended_desc_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_desc_font_color"><?php echo __('Description font color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_desc_font_color" id="album_extended_desc_font_color" value="<?php echo esc_attr($row->album_extended_desc_font_color); ?>" class="jscolor"/>
								  </td>
								</tr>
                <tr>
                  <!--generate font style with google fonts -->
                  <?php $this->font_style_row( $row->album_extended_desc_font_style, 'album_extended_desc_font_style', __('Description font family:', 'photo-gallery'), 'album_extended_desc_google_fonts' ); ?>
                </tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_desc_font_weight"><?php echo __('Description font weight:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="album_extended_desc_font_weight" id="album_extended_desc_font_weight">
									  <?php
									  foreach ($font_weights as $key => $font_weight) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_desc_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_desc_more_size"><?php echo __('Description more size:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_desc_more_size" id="album_extended_desc_more_size" value="<?php echo esc_attr($row->album_extended_desc_more_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_desc_more_color"><?php echo __('Description more color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_desc_more_color" id="album_extended_desc_more_color" value="<?php echo esc_attr($row->album_extended_desc_more_color); ?>" class="jscolor"/>
								  </td>
								</tr>
												<tr>
								  <td class="spider_label"><label for="album_extended_gal_title_font_size"><?php echo __('Gallery title/description font size:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_gal_title_font_size" id="album_extended_gal_title_font_size" value="<?php echo esc_attr($row->album_extended_gal_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_gal_title_font_color"><?php echo __('Gallery title/description font color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_gal_title_font_color" id="album_extended_gal_title_font_color" value="<?php echo esc_attr($row->album_extended_gal_title_font_color); ?>" class="jscolor" />
								  </td>
								</tr>
                <tr>
                  <!--generate font style with google fonts -->
                  <?php $this->font_style_row( $row->album_extended_gal_title_font_style, 'album_extended_gal_title_font_style', __('Gallery title/description font family:', 'photo-gallery'), 'album_extended_gal_title_google_fonts' ); ?>
                </tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_gal_title_font_weight"><?php echo __('Gallery title/description font weight:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="album_extended_gal_title_font_weight" id="album_extended_gal_title_font_weight">
									  <?php
									  foreach ($font_weights as $key => $font_weight) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_gal_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_gal_title_shadow"><?php echo __('Gallery title/description box shadow:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_gal_title_shadow" id="album_extended_gal_title_shadow" value="<?php echo esc_attr($row->album_extended_gal_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
									<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="album_extended_gal_title_margin"><?php echo __('Gallery title/description margin:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="album_extended_gal_title_margin" id="album_extended_gal_title_margin" value="<?php echo esc_attr($row->album_extended_gal_title_margin); ?>" class="spider_char_input" />
									<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								 <tr>
								  <td class="spider_label"><label for="album_extended_gal_title_align"><?php echo __('Gallery title alignment:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="album_extended_gal_title_align" id="album_extended_gal_title_align">
									  <?php
									  foreach ($aligns as $key => $align) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_extended_gal_title_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
							  </tbody>
							</table>
						</div>
					</div>
				</div>
				</div>
			</fieldset>
			<fieldset id="Masonry_album" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Masonry_album_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>

                  <tr>
                    <td class="spider_label"><label for="album_masonry_thumb_padding"><?php echo __('Distance between pictures:', 'photo-gallery'); ?> </label></td>
                    <td>
                      <input type="text" name="album_masonry_thumb_padding" id="album_masonry_thumb_padding" value="<?php echo esc_attr($row->album_masonry_thumb_padding); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
                    </td>
                  </tr>
                  <tr>
                    <td class="spider_label"><label><?php _e('Distance from container frame:', 'photo-gallery'); ?> </label></td>
                    <td>
                      <input type="radio" name="album_masonry_container_margin" id="album_masonry_container_margin1" value="1"<?php if ($row->album_masonry_container_margin == 1) echo 'checked="checked"'; ?> />
                      <label for="album_masonry_container_margin1"><?php _e('Yes', 'photo-gallery'); ?></label>
                      <input type="radio" name="album_masonry_container_margin" id="album_masonry_container_margin0" value="0"<?php if ($row->album_masonry_container_margin == 0) echo 'checked="checked"'; ?> />
                      <label for="album_masonry_container_margin0"><?php _e('No', 'photo-gallery'); ?></label>
                      <div class="spider_description"><?php _e('Enable this option to add distance between the parent container and the thumbnails grid.', 'photo-gallery'); ?></div>
                    </td>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_thumb_border_width"><?php echo __('Border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_thumb_border_width" id="album_masonry_thumb_border_width" value="<?php echo esc_attr($row->album_masonry_thumb_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_thumb_border_style"><?php echo __('Border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_masonry_thumb_border_style" id="album_masonry_thumb_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_masonry_thumb_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_thumb_border_color"><?php echo __('Border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_thumb_border_color" id="album_masonry_thumb_border_color" value="<?php echo esc_attr($row->album_masonry_thumb_border_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_thumb_border_radius"><?php echo __('Border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_thumb_border_radius" id="album_masonry_thumb_border_radius" value="<?php echo esc_attr($row->album_masonry_thumb_border_radius); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_thumb_hover_effect"><?php echo __('Hover effect:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_masonry_thumb_hover_effect" id="album_masonry_thumb_hover_effect">
										  <?php
										  foreach ($thumbnail_hover_effects as $key => $hover_effect) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_masonry_thumb_hover_effect == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($hover_effect, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_thumb_hover_effect_value"><?php echo __('Hover effect value:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_thumb_hover_effect_value" id="album_masonry_thumb_hover_effect_value" value="<?php echo esc_attr($row->album_masonry_thumb_hover_effect_value); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('E.g. Rotate: 10deg, Scale: 1.5, Skew: 10deg.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label><?php echo __('Thumbnail transition:', 'photo-gallery'); ?> </label></td>
									  <td id="album_masonry_thumb_transition">
										<input type="radio" name="album_masonry_thumb_transition" id="album_masonry_thumb_transition1" value="1"<?php if ($row->album_masonry_thumb_transition == 1) echo 'checked="checked"'; ?> />
										<label for="album_masonry_thumb_transition1" id="album_masonry_thumb_transition1_lbl"><?php echo __('Yes', 'photo-gallery'); ?></label>
										<input type="radio" name="album_masonry_thumb_transition" id="album_masonry_thumb_transition0" value="0"<?php if ($row->album_masonry_thumb_transition == 0) echo 'checked="checked"'; ?> />
										<label for="album_masonry_thumb_transition0" id="album_masonry_thumb_transition0_lbl"><?php echo __('No', 'photo-gallery'); ?></label>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="masonry_album_2" class="wd-table-col wd-table-col-30">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
                  <tr>
                    <td class="spider_label">
                      <label for="album_masonry_thumb_bg_color"><?php echo __('Thumbnail background color:', 'photo-gallery'); ?> </label>
                    </td>
                    <td>
                      <input type="text" name="album_masonry_thumb_bg_color" id="album_masonry_thumb_bg_color" value="<?php echo esc_attr($row->album_masonry_thumb_bg_color); ?>" class="jscolor" />
                    </td>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_thumb_transparent"><?php echo __('Thumbnail transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_thumb_transparent" id="album_masonry_thumb_transparent" value="<?php echo esc_attr($row->album_masonry_thumb_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_thumbs_bg_color"><?php echo __('Full background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_thumbs_bg_color" id="album_masonry_thumbs_bg_color" value="<?php echo esc_attr($row->album_masonry_thumbs_bg_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_thumb_bg_transparent"><?php echo __('Full background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_thumb_bg_transparent" id="album_masonry_thumb_bg_transparent" value="<?php echo esc_attr($row->album_masonry_thumb_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_thumb_align0"><?php echo __('Alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_masonry_thumb_align" id="album_masonry_thumb_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_masonry_thumb_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="masonry_album_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="album_masonry_title_font_size"><?php echo __('Title font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_title_font_size" id="album_masonry_title_font_size" value="<?php echo esc_attr($row->album_masonry_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_title_font_color"><?php echo __('Title font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_title_font_color" id="album_masonry_title_font_color" value="<?php echo esc_attr($row->album_masonry_title_font_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <td class="spider_label"><label for="album_masonry_thumb_title_font_color_hover"><?php echo __('Title font color (Show on hover):', 'photo-gallery'); ?> </label></td>
                    <td>
                      <input type="text" name="album_masonry_thumb_title_font_color_hover" id="album_masonry_thumb_title_font_color_hover" value="<?php echo esc_attr($row->album_masonry_thumb_title_font_color_hover); ?>" class="jscolor" />
                    </td>
                  </tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->album_masonry_title_font_style, 'album_masonry_title_font_style', __('Title font family:', 'photo-gallery'), 'album_masonry_title_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_title_font_weight"><?php echo __('Title font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_masonry_title_font_weight" id="album_masonry_title_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_masonry_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_title_shadow"><?php echo __('Title box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_title_shadow" id="album_masonry_title_shadow" value="<?php echo esc_attr($row->album_masonry_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_back_font_size"><?php echo __('Back Font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_back_font_size" id="album_masonry_back_font_size" value="<?php echo esc_attr($row->album_masonry_back_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_back_font_color"><?php echo __('Back Font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_back_font_color" id="album_masonry_back_font_color" value="<?php echo esc_attr($row->album_masonry_back_font_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->album_masonry_back_font_style, 'album_masonry_back_font_style', __('Back Font family:', 'photo-gallery'), 'album_masonry_back_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_back_font_weight"><?php echo __('Back Font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_masonry_back_font_weight" id="album_masonry_back_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_masonry_back_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_back_padding"><?php echo __('Back padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_back_padding" id="album_masonry_back_padding" value="<?php echo esc_attr($row->album_masonry_back_padding); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_gal_title_font_size"><?php echo __('Gallery title/description font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_gal_title_font_size" id="album_masonry_gal_title_font_size" value="<?php echo esc_attr($row->album_masonry_gal_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_gal_title_font_color"><?php echo __('Gallery title/description font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_gal_title_font_color" id="album_masonry_gal_title_font_color" value="<?php echo esc_attr($row->album_masonry_gal_title_font_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->album_masonry_gal_title_font_style, 'album_masonry_gal_title_font_style', __('Gallery title/description font family:', 'photo-gallery'), 'album_masonry_gal_title_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_gal_title_font_weight"><?php echo __('Gallery title/description font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_masonry_gal_title_font_weight" id="album_masonry_gal_title_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_masonry_gal_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_gal_title_shadow"><?php echo __('Gallery title/description box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_gal_title_shadow" id="album_masonry_gal_title_shadow" value="<?php echo esc_attr($row->album_masonry_gal_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_gal_title_margin"><?php echo __('Gallery title/description margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="album_masonry_gal_title_margin" id="album_masonry_gal_title_margin" value="<?php echo esc_attr($row->album_masonry_gal_title_margin); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="album_masonry_gal_title_align"><?php echo __('Gallery title alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="album_masonry_gal_title_align" id="album_masonry_gal_title_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->album_masonry_gal_title_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<fieldset id="Blog_style" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Blog_style_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
									<tbody>
									<tr>
									  <td class="spider_label"><label for="blog_style_bg_color"><?php echo __('Background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_bg_color" id="blog_style_bg_color" value="<?php echo esc_attr($row->blog_style_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_transparent"><?php echo __('Background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_transparent" id="blog_style_transparent" value="<?php echo esc_attr($row->blog_style_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_align0"><?php echo __('Alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="blog_style_align" id="blog_style_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->blog_style_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_margin"><?php echo __('Margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_margin" id="blog_style_margin" value="<?php echo esc_attr($row->blog_style_margin); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_padding"><?php echo __('Padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_padding" id="blog_style_padding" value="<?php echo esc_attr($row->blog_style_padding); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_box_shadow"><?php echo __('Box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_box_shadow" id="blog_style_box_shadow" value="<?php echo esc_attr($row->blog_style_box_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Blog_style_2" class="wd-table-col wd-table-col-30">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->blog_style_img_font_family, 'blog_style_img_font_family', __('Font family:', 'photo-gallery'), 'blog_style_img_google_fonts' ); ?>
                  </tr>
                  <tr>
									  <td class="spider_label"><label for="blog_style_img_font_size"><?php echo __('Font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_img_font_size" id="blog_style_img_font_size" value="<?php echo esc_attr($row->blog_style_img_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_img_font_color"><?php echo __('Font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_img_font_color" id="blog_style_img_font_color" value="<?php echo esc_attr($row->blog_style_img_font_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_border_width"><?php echo __('Border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_border_width" id="blog_style_border_width" value="<?php echo esc_attr($row->blog_style_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_border_style"><?php echo __('Border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="blog_style_border_style" id="blog_style_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->blog_style_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_border_color"><?php echo __('Border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_border_color" id="blog_style_border_color" value="<?php echo esc_attr($row->blog_style_border_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_border_radius"><?php echo __('Border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_border_radius" id="blog_style_border_radius" value="<?php echo esc_attr($row->blog_style_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Blog_style_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="blog_style_share_buttons_margin"><?php echo __('Buttons and title margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_share_buttons_margin" id="blog_style_share_buttons_margin" value="<?php echo esc_attr($row->blog_style_share_buttons_margin); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_share_buttons_font_size"><?php echo __('Buttons size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_share_buttons_font_size" id="blog_style_share_buttons_font_size" value="<?php echo esc_attr($row->blog_style_share_buttons_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_share_buttons_color"><?php echo __('Buttons color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_share_buttons_color" id="blog_style_share_buttons_color" value="<?php echo esc_attr($row->blog_style_share_buttons_color); ?>" class="jscolor"/>
									  </td>
									</tr>
								   <tr>
									  <td class="spider_label"><label for="blog_style_share_buttons_border_width"><?php echo __('Buttons and title border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_share_buttons_border_width" id="blog_style_share_buttons_border_width" value="<?php echo esc_attr($row->blog_style_share_buttons_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_share_buttons_border_style"><?php echo __('Buttons and title border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="blog_style_share_buttons_border_style" id="blog_style_share_buttons_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->blog_style_share_buttons_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_share_buttons_border_color"><?php echo __('Buttons and title border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_share_buttons_border_color" id="blog_style_share_buttons_border_color" value="<?php echo esc_attr($row->blog_style_share_buttons_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_share_buttons_border_radius"><?php echo __('Buttons and title border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_share_buttons_border_radius" id="blog_style_share_buttons_border_radius" value="<?php echo esc_attr($row->blog_style_share_buttons_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_share_buttons_bg_color"><?php echo __('Buttons and title background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_share_buttons_bg_color" id="blog_style_share_buttons_bg_color" value="<?php echo esc_attr($row->blog_style_share_buttons_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_share_buttons_bg_transparent"><?php echo __('Buttons and title background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_share_buttons_bg_transparent" id="blog_style_share_buttons_bg_transparent" value="<?php echo esc_attr($row->blog_style_share_buttons_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_share_buttons_align0"><?php echo __('Buttons or title alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="blog_style_share_buttons_align" id="blog_style_share_buttons_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->blog_style_share_buttons_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_gal_title_font_size"><?php echo __('Gallery title/description font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_gal_title_font_size" id="blog_style_gal_title_font_size" value="<?php echo
                                        esc_attr($row->blog_style_gal_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_gal_title_font_color"><?php echo __('Gallery title/description font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_gal_title_font_color" id="blog_style_gal_title_font_color" value="<?php echo esc_attr($row->blog_style_gal_title_font_color); ?>" class="jscolor" />
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->blog_style_gal_title_font_style, 'blog_style_gal_title_font_style', __('Gallery title/description font family:', 'photo-gallery'), 'blog_style_gal_title_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_gal_title_font_weight"><?php echo __('Gallery title/description font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="blog_style_gal_title_font_weight" id="blog_style_gal_title_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->blog_style_gal_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_gal_title_shadow"><?php echo __('Gallery title/description box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_gal_title_shadow" id="blog_style_gal_title_shadow" value="<?php echo esc_attr($row->blog_style_gal_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_gal_title_margin"><?php echo __('Gallery title/description margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="blog_style_gal_title_margin" id="blog_style_gal_title_margin" value="<?php echo esc_attr($row->blog_style_gal_title_margin); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="blog_style_gal_title_align"><?php echo __('Gallery title alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="blog_style_gal_title_align" id="blog_style_gal_title_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->blog_style_gal_title_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<fieldset id="Lightbox" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Lightbox_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr id="lightbox_overlay_bg">
									  <td class="spider_label"><label for="lightbox_overlay_bg_color"><?php echo __('Overlay background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_overlay_bg_color" id="lightbox_overlay_bg_color" value="<?php echo esc_attr($row->lightbox_overlay_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_overlay">
									  <td class="spider_label"><label for="lightbox_overlay_bg_transparent"><?php echo __('Overlay background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_overlay_bg_transparent" id="lightbox_overlay_bg_transparent" value="<?php echo esc_attr($row->lightbox_overlay_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_bg">
									  <td class="spider_label"><label for="lightbox_bg_color"><?php echo __('Lightbox background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_bg_color" id="lightbox_bg_color" value="<?php echo esc_attr($row->lightbox_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_transparency">
									  <td class="spider_label"><label for="lightbox_bg_transparent"><?php echo __('Lightbox background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_bg_transparent" id="lightbox_bg_transparent" value="<?php echo esc_attr($row->lightbox_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_cntrl1">
									  <td class="spider_label"><label for="lightbox_ctrl_btn_height"><?php echo __('Control buttons height:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_ctrl_btn_height" id="lightbox_ctrl_btn_height" value="<?php echo esc_attr($row->lightbox_ctrl_btn_height); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_cntrl2">
									  <td class="spider_label"><label for="lightbox_ctrl_btn_margin_top"><?php echo __('Control buttons margin (top):', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_ctrl_btn_margin_top" id="lightbox_ctrl_btn_margin_top" value="<?php echo esc_attr($row->lightbox_ctrl_btn_margin_top); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_cntrl3">
									  <td class="spider_label"><label for="lightbox_ctrl_btn_margin_left"><?php echo __('Control buttons margin (left):', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_ctrl_btn_margin_left" id="lightbox_ctrl_btn_margin_left" value="<?php echo esc_attr($row->lightbox_ctrl_btn_margin_left); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_cntrl9">
									  <td class="spider_label"><label><?php echo __('Control buttons position:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="radio" name="lightbox_ctrl_btn_pos" id="lightbox_ctrl_btn_pos1" value="top"<?php if ($row->lightbox_ctrl_btn_pos == "top") echo 'checked="checked"'; ?> />
										<label for="lightbox_ctrl_btn_pos1" id="lightbox_ctrl_btn_pos1_lbl"><?php echo __('Top', 'photo-gallery'); ?></label>
										<input type="radio" name="lightbox_ctrl_btn_pos" id="lightbox_ctrl_btn_pos0" value="bottom"<?php if ($row->lightbox_ctrl_btn_pos == "bottom") echo 'checked="checked"'; ?> />
										<label for="lightbox_ctrl_btn_pos0" id="lightbox_ctrl_btn_pos0_lbl"><?php echo __('Bottom', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr id="lightbox_cntrl8">
									  <td class="spider_label"><label for="lightbox_ctrl_cont_bg_color"><?php echo __('Control buttons background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_ctrl_cont_bg_color" id="lightbox_ctrl_cont_bg_color" value="<?php echo esc_attr($row->lightbox_ctrl_cont_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_cntrl5">
									  <td class="spider_label"><label for="lightbox_ctrl_cont_border_radius"><?php echo __('Control buttons container border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_ctrl_cont_border_radius" id="lightbox_ctrl_cont_border_radius" value="<?php echo esc_attr($row->lightbox_ctrl_cont_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_cntrl6">
									  <td class="spider_label"><label for="lightbox_ctrl_cont_transparent"><?php echo __('Control buttons container background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_ctrl_cont_transparent" id="lightbox_ctrl_cont_transparent" value="<?php echo esc_attr($row->lightbox_ctrl_cont_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_cntrl10">
									  <td class="spider_label"><label for="lightbox_ctrl_btn_align0"><?php echo __('Control buttons alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_ctrl_btn_align" id="lightbox_ctrl_btn_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_ctrl_btn_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr id="lightbox_cntrl7">
									  <td class="spider_label"><label for="lightbox_ctrl_btn_color"><?php echo __('Control buttons color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_ctrl_btn_color" id="lightbox_ctrl_btn_color" value="<?php echo esc_attr($row->lightbox_ctrl_btn_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_cntrl4">
									  <td class="spider_label"><label for="lightbox_ctrl_btn_transparent"><?php echo __('Control buttons transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_ctrl_btn_transparent" id="lightbox_ctrl_btn_transparent" value="<?php echo esc_attr($row->lightbox_ctrl_btn_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_toggle1">
									  <td class="spider_label"><label for="lightbox_toggle_btn_height"><?php echo __('Toggle button height:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_toggle_btn_height" id="lightbox_toggle_btn_height" value="<?php echo esc_attr($row->lightbox_toggle_btn_height); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_toggle2">
									  <td class="spider_label"><label for="lightbox_toggle_btn_width"><?php echo __('Toggle button width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_toggle_btn_width" id="lightbox_toggle_btn_width" value="<?php echo esc_attr($row->lightbox_toggle_btn_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_close1">
									  <td class="spider_label"><label for="lightbox_close_btn_border_radius"><?php echo __('Close button border radius:', 'photo-gallery'); ?> </label>
									  </td>
									  <td>
										<input type="text" name="lightbox_close_btn_border_radius" id="lightbox_close_btn_border_radius" value="<?php echo esc_attr($row->lightbox_close_btn_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_close2">
									  <td class="spider_label"><label for="lightbox_close_btn_border_width"><?php echo __('Close button border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_border_width" id="lightbox_close_btn_border_width" value="<?php echo esc_attr($row->lightbox_close_btn_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_close12">
									  <td class="spider_label"><label for="lightbox_close_btn_border_style"><?php echo __('Close button border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_close_btn_border_style" id="lightbox_close_btn_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_close_btn_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr id="lightbox_close13">
									  <td class="spider_label"><label for="lightbox_close_btn_border_color"><?php echo __('Close button border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_border_color" id="lightbox_close_btn_border_color" value="<?php echo esc_attr($row->lightbox_close_btn_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_close3">
									  <td class="spider_label"><label for="lightbox_close_btn_box_shadow"><?php echo __('Close button box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_box_shadow" id="lightbox_close_btn_box_shadow" value="<?php echo esc_attr($row->lightbox_close_btn_box_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_close11">
									  <td class="spider_label"><label for="lightbox_close_btn_bg_color"><?php echo __('Close button background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_bg_color" id="lightbox_close_btn_bg_color" value="<?php echo esc_attr($row->lightbox_close_btn_bg_color); ?>" class="jscolor"/>
                    <div class="spider_description"><?php echo __('The option does not apply to Full-width lightbox.', 'photo-gallery'); ?></div>
                    </td>
									</tr>
									<tr id="lightbox_close9">
									  <td class="spider_label"><label for="lightbox_close_btn_transparent"><?php echo __('Close button transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_transparent" id="lightbox_close_btn_transparent" value="<?php echo esc_attr($row->lightbox_close_btn_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
									  </td>
									</tr>
									<tr id="lightbox_close5">
									  <td class="spider_label"><label for="lightbox_close_btn_width"><?php echo __('Close button width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_width" id="lightbox_close_btn_width" value="<?php echo esc_attr($row->lightbox_close_btn_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
                    <div class="spider_description"><?php echo __('The option does not apply to Full-width lightbox.', 'photo-gallery'); ?></div>
                    </td>
									</tr>
									<tr id="lightbox_close6">
									  <td class="spider_label"><label for="lightbox_close_btn_height"><?php echo __('Close button height:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_height" id="lightbox_close_btn_height" value="<?php echo esc_attr($row->lightbox_close_btn_height); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
                    <div class="spider_description"><?php echo __('The option does not apply to Full-width lightbox.', 'photo-gallery'); ?></div>
                    </td>
									</tr>
									<tr id="lightbox_close7">
									  <td class="spider_label"><label for="lightbox_close_btn_top"><?php echo __('Close button top:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_top" id="lightbox_close_btn_top" value="<?php echo esc_attr($row->lightbox_close_btn_top); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
                    <div class="spider_description"><?php echo __('The option does not apply to Full-width lightbox.', 'photo-gallery'); ?></div>
                    </td>
									</tr>
									<tr id="lightbox_close8">
									  <td class="spider_label"><label for="lightbox_close_btn_right"><?php echo __('Close button right:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_right" id="lightbox_close_btn_right" value="<?php echo esc_attr($row->lightbox_close_btn_right); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
                    <div class="spider_description"><?php echo __('The option does not apply to Full-width lightbox.', 'photo-gallery'); ?></div>
                    </td>
									</tr>
									<tr id="lightbox_close4">
									  <td class="spider_label"><label for="lightbox_close_btn_size"><?php echo __('Close button size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_size" id="lightbox_close_btn_size" value="<?php echo esc_attr($row->lightbox_close_btn_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_close14">
									  <td class="spider_label"><label for="lightbox_close_btn_color"><?php echo __('Close button color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_color" id="lightbox_close_btn_color" value="<?php echo esc_attr($row->lightbox_close_btn_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_close10">
									  <td class="spider_label"><label for="lightbox_close_btn_full_color"><?php echo __('Fullscreen close button color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_btn_full_color" id="lightbox_close_btn_full_color" value="<?php echo esc_attr($row->lightbox_close_btn_full_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_comment24">
									  <td class="spider_label"><label for="lightbox_comment_share_button_color"><?php echo __('Share buttons color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_share_button_color" id="lightbox_comment_share_button_color" value="<?php echo esc_attr($row->lightbox_comment_share_button_color); ?>" class="jscolor" />
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Lightbox_2" class="wd-table-col wd-table-col-30">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr id="lightbox_right_left11">
									  <td class="spider_label"><label for="lightbox_rl_btn_style"><?php echo __('Right, left buttons style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_rl_btn_style" id="lightbox_rl_btn_style" class="spider_int_input select_icon_them">
										  <?php
										  foreach ($button_styles as $key => $button_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_rl_btn_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($button_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr id="lightbox_right_left7">
									  <td class="spider_label"><label for="lightbox_rl_btn_bg_color"><?php echo __('Right, left buttons background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rl_btn_bg_color" id="lightbox_rl_btn_bg_color" value="<?php echo esc_attr($row->lightbox_rl_btn_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_rl_btn_transparent"><?php echo __('Right, left buttons transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rl_btn_transparent" id="lightbox_rl_btn_transparent" value="<?php echo esc_attr($row->lightbox_rl_btn_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
									  </td>
									</tr>
									<tr id="lightbox_right_left3">
									  <td class="spider_label"><label for="lightbox_rl_btn_box_shadow"><?php echo __('Right, left buttons box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rl_btn_box_shadow" id="lightbox_rl_btn_box_shadow" value="<?php echo esc_attr($row->lightbox_rl_btn_box_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_right_left4">
									  <td class="spider_label"><label for="lightbox_rl_btn_height"><?php echo __('Right, left buttons height:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rl_btn_height" id="lightbox_rl_btn_height" value="<?php echo esc_attr($row->lightbox_rl_btn_height); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_right_left5">
									  <td class="spider_label"><label for="lightbox_rl_btn_width"><?php echo __('Right, left buttons width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rl_btn_width" id="lightbox_rl_btn_width" value="<?php echo esc_attr($row->lightbox_rl_btn_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_right_left6">
									  <td class="spider_label"><label for="lightbox_rl_btn_size"><?php echo __('Right, left buttons size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rl_btn_size" id="lightbox_rl_btn_size" value="<?php echo esc_attr($row->lightbox_rl_btn_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_close15">
									  <td class="spider_label"><label for="lightbox_close_rl_btn_hover_color"><?php echo __('Right, left, close buttons hover color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_close_rl_btn_hover_color" id="lightbox_close_rl_btn_hover_color" value="<?php echo esc_attr($row->lightbox_close_rl_btn_hover_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr id="lightbox_right_left10">
									  <td class="spider_label"><label for="lightbox_rl_btn_color"><?php echo __('Right, left buttons color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rl_btn_color" id="lightbox_rl_btn_color" value="<?php echo esc_attr($row->lightbox_rl_btn_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_right_left1">
									  <td class="spider_label"><label for="lightbox_rl_btn_border_radius"><?php echo __('Right, left buttons border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rl_btn_border_radius" id="lightbox_rl_btn_border_radius" value="<?php echo esc_attr($row->lightbox_rl_btn_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_right_left2">
									  <td class="spider_label"><label for="lightbox_rl_btn_border_width"><?php echo __('Right, left buttons border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rl_btn_border_width" id="lightbox_rl_btn_border_width" value="<?php echo esc_attr($row->lightbox_rl_btn_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_right_left8">
									  <td class="spider_label"><label for="lightbox_rl_btn_border_style"><?php echo __('Right, left buttons border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_rl_btn_border_style" id="lightbox_rl_btn_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_rl_btn_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr id="lightbox_right_left9">
									  <td class="spider_label"><label for="lightbox_rl_btn_border_color"><?php echo __('Right, left buttons border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rl_btn_border_color" id="lightbox_rl_btn_border_color" value="<?php echo esc_attr($row->lightbox_rl_btn_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_filmstrip12">
									  <td class="spider_label"><label><?php echo __('Filmstrip position:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_filmstrip_pos" id="lightbox_filmstrip_pos">
										  <option value="top" <?php echo (($row->lightbox_filmstrip_pos == "top") ? 'selected="selected"' : ''); ?>><?php echo __('Top', 'photo-gallery'); ?></option>
										  <option value="right" <?php echo (($row->lightbox_filmstrip_pos == "right") ? 'selected="selected"' : ''); ?>><?php echo __('Right', 'photo-gallery'); ?></option>
										  <option value="bottom" <?php echo (($row->lightbox_filmstrip_pos == "bottom") ? 'selected="selected"' : ''); ?>><?php echo __('Bottom', 'photo-gallery'); ?></option>
										  <option value="left" <?php echo (($row->lightbox_filmstrip_pos == "left") ? 'selected="selected"' : ''); ?>><?php echo __('Left', 'photo-gallery'); ?></option>
										</select>
									  </td>
									</tr>
									<tr id="lightbox_filmstrip2">
									  <td class="spider_label"><label for="lightbox_filmstrip_thumb_margin"><?php echo __('Filmstrip thumbnail margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_filmstrip_thumb_margin" id="lightbox_filmstrip_thumb_margin" value="<?php echo esc_attr($row->lightbox_filmstrip_thumb_margin); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_filmstrip3">
									  <td class="spider_label"><label for="lightbox_filmstrip_thumb_border_width"><?php echo __('Filmstrip thumbnail border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_filmstrip_thumb_border_width" id="lightbox_filmstrip_thumb_border_width" value="<?php echo esc_attr($row->lightbox_filmstrip_thumb_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_filmstrip9">
									  <td class="spider_label"><label for="lightbox_filmstrip_thumb_border_style"><?php echo __('Filmstrip thumbnail border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_filmstrip_thumb_border_style" id="lightbox_filmstrip_thumb_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_filmstrip_thumb_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr id="lightbox_filmstrip10">
									  <td class="spider_label"><label for="lightbox_filmstrip_thumb_border_color"><?php echo __('Filmstrip thumbnail border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_filmstrip_thumb_border_color" id="lightbox_filmstrip_thumb_border_color" value="<?php echo esc_attr($row->lightbox_filmstrip_thumb_border_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr id="lightbox_filmstrip4">
									  <td class="spider_label"><label for="lightbox_filmstrip_thumb_border_radius"><?php echo __('Filmstrip thumbnail border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_filmstrip_thumb_border_radius" id="lightbox_filmstrip_thumb_border_radius" value="<?php echo esc_attr($row->lightbox_filmstrip_thumb_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_filmstrip6">
									  <td class="spider_label"><label for="lightbox_filmstrip_thumb_active_border_width"><?php echo __('Filmstrip thumbnail active border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_filmstrip_thumb_active_border_width" id="lightbox_filmstrip_thumb_active_border_width" value="<?php echo esc_attr($row->lightbox_filmstrip_thumb_active_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_filmstrip11">
									  <td class="spider_label"> <label for="lightbox_filmstrip_thumb_active_border_color"><?php echo __('Filmstrip thumbnail active border color:', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="lightbox_filmstrip_thumb_active_border_color" id="lightbox_filmstrip_thumb_active_border_color" value="<?php echo esc_attr($row->lightbox_filmstrip_thumb_active_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_filmstrip5">
									  <td class="spider_label"><label for="lightbox_filmstrip_thumb_deactive_transparent"><?php echo __('Filmstrip thumbnail deactive transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_filmstrip_thumb_deactive_transparent" id="lightbox_filmstrip_thumb_deactive_transparent" value="<?php echo esc_attr($row->lightbox_filmstrip_thumb_deactive_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_filmstrip1">
									  <td class="spider_label"><label for="lightbox_filmstrip_rl_btn_size"><?php echo __('Filmstrip right, left buttons size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_filmstrip_rl_btn_size" id="lightbox_filmstrip_rl_btn_size" value="<?php echo esc_attr($row->lightbox_filmstrip_rl_btn_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_filmstrip7">
									  <td class="spider_label"><label for="lightbox_filmstrip_rl_btn_color"><?php echo __('Filmstrip right, left buttons color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_filmstrip_rl_btn_color" id="lightbox_filmstrip_rl_btn_color" value="<?php echo esc_attr($row->lightbox_filmstrip_rl_btn_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_filmstrip8">
									  <td class="spider_label"><label for="lightbox_filmstrip_rl_bg_color"><?php echo __('Filmstrip right, left button background color:', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="lightbox_filmstrip_rl_bg_color" id="lightbox_filmstrip_rl_bg_color" value="<?php echo esc_attr($row->lightbox_filmstrip_rl_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_rate_pos1"><?php echo __('Rating position:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="radio" name="lightbox_rate_pos" id="lightbox_rate_pos1" value="top" <?php if ($row->lightbox_rate_pos == "top") echo 'checked="checked"'; ?> />
										<label for="lightbox_rate_pos1" id="lightbox_rate_pos1_lbl"><?php echo __('Top', 'photo-gallery'); ?></label>
										<input type="radio" name="lightbox_rate_pos" id="lightbox_rate_pos0" value="bottom" <?php if ($row->lightbox_rate_pos == "bottom") echo 'checked="checked"'; ?> />
										<label for="lightbox_rate_pos0" id="lightbox_rate_pos0_lbl"><?php echo __('Bottom', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_rate_align"><?php echo __('Rating alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_rate_align" id="lightbox_rate_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_rate_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_rate_icon"><?php echo __('Rating icon:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_rate_icon" id="lightbox_rate_icon">
										  <?php
										  foreach ($rate_icons as $key => $rate_icon) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_rate_icon == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($rate_icon, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_rate_color"><?php echo __('Rating color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rate_color" id="lightbox_rate_color" value="<?php echo esc_attr($row->lightbox_rate_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_rate_hover_color"><?php echo __('Rating hover color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rate_hover_color" id="lightbox_rate_hover_color" value="<?php echo esc_attr($row->lightbox_rate_hover_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_rate_size"><?php echo __('Rating size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rate_size" id="lightbox_rate_size" value="<?php echo esc_attr($row->lightbox_rate_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_rate_stars_count"><?php echo __('Rating icon count:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rate_stars_count" id="lightbox_rate_stars_count" value="<?php echo esc_attr($row->lightbox_rate_stars_count); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_rate_padding"><?php echo __('Rating padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_rate_padding" id="lightbox_rate_padding" value="<?php echo esc_attr($row->lightbox_rate_padding); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label><?php echo __('Hit counter position:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="radio" name="lightbox_hit_pos" id="lightbox_hit_pos1" value="top" <?php if ($row->lightbox_hit_pos == "top") echo 'checked="checked"'; ?> />
										<label for="lightbox_hit_pos1" id="lightbox_hit_pos1_lbl"><?php echo __('Top', 'photo-gallery'); ?></label>
										<input type="radio" name="lightbox_hit_pos" id="lightbox_hit_pos0" value="bottom" <?php if ($row->lightbox_hit_pos == "bottom") echo 'checked="checked"'; ?> />
										<label for="lightbox_hit_pos0" id="lightbox_hit_pos0_lbl"><?php echo __('Bottom', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_align"><?php echo __('Hit counter alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_hit_align" id="lightbox_hit_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_hit_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_bg_color"><?php echo __('Hit counter background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_hit_bg_color" id="lightbox_hit_bg_color" value="<?php echo esc_attr($row->lightbox_hit_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_bg_transparent"><?php echo __('Hit counter background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_hit_bg_transparent" id="lightbox_hit_bg_transparent" value="<?php echo esc_attr($row->lightbox_hit_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_border_width"><?php echo __('Hit counter border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_hit_border_width" id="lightbox_hit_border_width" value="<?php echo esc_attr($row->lightbox_hit_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_border_style"><?php echo __('Hit counter border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_hit_border_style" id="lightbox_hit_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_hit_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_border_color"><?php echo __('Hit counter border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_hit_border_color" id="lightbox_hit_border_color" value="<?php echo esc_attr($row->lightbox_hit_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_border_radius"><?php echo __('Hit counter border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_hit_border_radius" id="lightbox_hit_border_radius" value="<?php echo esc_attr($row->lightbox_hit_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_padding"><?php echo __('Hit counter padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_hit_padding" id="lightbox_hit_padding" value="<?php echo esc_attr($row->lightbox_hit_padding); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_margin"><?php echo __('Hit counter margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_hit_margin" id="lightbox_hit_margin" value="<?php echo esc_attr($row->lightbox_hit_margin); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_color"><?php echo __('Hit counter font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_hit_color" id="lightbox_hit_color" value="<?php echo esc_attr($row->lightbox_hit_color); ?>" class="jscolor"/>
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->lightbox_hit_font_style, 'lightbox_hit_font_style', __('Hit counter font family:', 'photo-gallery'), 'lightbox_hit_google_fonts' ); ?>
                  </tr>
                  <tr>
									  <td class="spider_label"><label for="lightbox_hit_font_weight"><?php echo __('Hit counter font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_hit_font_weight" id="lightbox_hit_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_hit_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_hit_font_size"><?php echo __('Hit counter font size:', 'photo-gallery'); ?> </label>
									  </td>
									  <td>
										<input type="text" name="lightbox_hit_font_size" id="lightbox_hit_font_size" value="<?php echo esc_attr($row->lightbox_hit_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Lightbox_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
						<div class="wd-box-section">
							<div class="wd-box-content">
							   <table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label><?php echo __('Info position:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="radio" name="lightbox_info_pos" id="lightbox_info_pos1" value="top" <?php if ($row->lightbox_info_pos == "top") echo 'checked="checked"'; ?> />
										<label for="lightbox_info_pos1" id="lightbox_info_pos1_lbl"><?php echo __('Top', 'photo-gallery'); ?></label>
										<input type="radio" name="lightbox_info_pos" id="lightbox_info_pos0" value="bottom" <?php if ($row->lightbox_info_pos == "bottom") echo 'checked="checked"'; ?> />
										<label for="lightbox_info_pos0" id="lightbox_info_pos0_lbl"><?php echo __('Bottom', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_info_align"><?php echo __('Info alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_info_align" id="lightbox_info_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_info_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_info_bg_color"><?php echo __('Info background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_info_bg_color" id="lightbox_info_bg_color" value="<?php echo esc_attr($row->lightbox_info_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_info_bg_transparent"><?php echo __('Info background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_info_bg_transparent" id="lightbox_info_bg_transparent" value="<?php echo esc_attr($row->lightbox_info_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_info_border_width"><?php echo __('Info border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_info_border_width" id="lightbox_info_border_width" value="<?php echo esc_attr($row->lightbox_info_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_info_border_style"><?php echo __('Info border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_info_border_style" id="lightbox_info_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_info_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_info_border_color"><?php echo __('Info border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_info_border_color" id="lightbox_info_border_color" value="<?php echo esc_attr($row->lightbox_info_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_info_border_radius"><?php echo __('Info border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_info_border_radius" id="lightbox_info_border_radius" value="<?php echo esc_attr($row->lightbox_info_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_info_padding"><?php echo __('Info padding:', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="lightbox_info_padding" id="lightbox_info_padding" value="<?php echo esc_attr($row->lightbox_info_padding); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_info_margin"><?php echo __('Info margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_info_margin" id="lightbox_info_margin" value="<?php echo esc_attr($row->lightbox_info_margin); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_title_color"><?php echo __('Title font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_title_color" id="lightbox_title_color" value="<?php echo esc_attr($row->lightbox_title_color); ?>" class="jscolor"/>
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->lightbox_title_font_style, 'lightbox_title_font_style', __('Title font family:', 'photo-gallery'), 'lightbox_title_google_fonts' ); ?>
                  </tr>
                  <tr>
									  <td class="spider_label"><label for="lightbox_title_font_weight"><?php echo __('Title font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_title_font_weight" id="lightbox_title_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_title_font_size"><?php echo __('Title font size:', 'photo-gallery'); ?> </label>
									  </td>
									  <td>
										<input type="text" name="lightbox_title_font_size" id="lightbox_title_font_size" value="<?php echo esc_attr($row->lightbox_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_description_color"><?php echo __('Description font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_description_color" id="lightbox_description_color" value="<?php echo esc_attr($row->lightbox_description_color); ?>" class="jscolor"/>
									  </td>
									</tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->lightbox_description_font_style, 'lightbox_description_font_style', __('Description font family:', 'photo-gallery'), 'lightbox_description_google_fonts' ); ?>
                  </tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_description_font_weight"><?php echo __('Description font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_description_font_weight" id="lightbox_description_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_description_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_description_font_size"><?php echo __('Description font size:', 'photo-gallery'); ?> </label>
									  </td>
									  <td>
										<input type="text" name="lightbox_description_font_size" id="lightbox_description_font_size" value="<?php echo esc_attr($row->lightbox_description_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="lightbox_comment_width"><?php echo __('Comments Width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_width" id="lightbox_comment_width" value="<?php echo esc_attr($row->lightbox_comment_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_comment25">
									  <td class="spider_label"><label><?php echo __('Comments position:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="radio" name="lightbox_comment_pos" id="lightbox_comment_pos1" value="left"<?php if ($row->lightbox_comment_pos == "left") echo 'checked="checked"'; ?> />
										<label for="lightbox_comment_pos1" id="lightbox_comment_pos1_lbl"><?php echo __('Left', 'photo-gallery'); ?></label>
										<input type="radio" name="lightbox_comment_pos" id="lightbox_comment_pos0" value="right"<?php if ($row->lightbox_comment_pos == "right") echo 'checked="checked"'; ?> />
										<label for="lightbox_comment_pos0" id="lightbox_comment_pos0_lbl"><?php echo __('Right', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr id="lightbox_comment13">
									  <td class="spider_label"><label for="lightbox_comment_bg_color"><?php echo __('Comments background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_bg_color" id="lightbox_comment_bg_color" value="<?php echo esc_attr($row->lightbox_comment_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_comment2">
									  <td class="spider_label"><label for="lightbox_comment_font_size"><?php echo __('Comments font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_font_size" id="lightbox_comment_font_size" value="<?php echo esc_attr($row->lightbox_comment_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_comment14">
									  <td class="spider_label"><label for="lightbox_comment_font_color"><?php echo __('Comments font color:', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="lightbox_comment_font_color" id="lightbox_comment_font_color" value="<?php echo esc_attr($row->lightbox_comment_font_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_comment15">
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->lightbox_comment_font_style, 'lightbox_comment_font_style', __('Comments font family:', 'photo-gallery'), 'lightbox_comment_google_fonts' ); ?>
									</tr>
									<tr id="lightbox_comment10">
									  <td class="spider_label"><label for="lightbox_comment_author_font_size"><?php echo __('Comments author font size:', 'photo-gallery'); ?> </label>
									  </td>
									  <td>
										<input type="text" name="lightbox_comment_author_font_size" id="lightbox_comment_author_font_size" value="<?php echo esc_attr($row->lightbox_comment_author_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_comment11">
									  <td class="spider_label"><label for="lightbox_comment_date_font_size"><?php echo __('Comments date font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_date_font_size" id="lightbox_comment_date_font_size" value="<?php echo esc_attr($row->lightbox_comment_date_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_comment12">
									  <td class="spider_label"><label for="lightbox_comment_body_font_size"><?php echo __('Comments body font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_body_font_size" id="lightbox_comment_body_font_size" value="<?php echo esc_attr($row->lightbox_comment_body_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_comment6">
									  <td class="spider_label"><label for="lightbox_comment_input_border_width"><?php echo __('Comment input border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_input_border_width" id="lightbox_comment_input_border_width" value="<?php echo esc_attr($row->lightbox_comment_input_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_comment21">
									  <td class="spider_label"><label for="lightbox_comment_input_border_style">C<?php echo __('omment input border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_comment_input_border_style" id="lightbox_comment_input_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_comment_input_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr id="lightbox_comment20">
									  <td class="spider_label"><label for="lightbox_comment_input_border_color"><?php echo __('Comment input border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_input_border_color" id="lightbox_comment_input_border_color" value="<?php echo esc_attr($row->lightbox_comment_input_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_comment7">
									  <td class="spider_label"><label for="lightbox_comment_input_border_radius"><?php echo __('Comment input border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_input_border_radius" id="lightbox_comment_input_border_radius" value="<?php echo esc_attr($row->lightbox_comment_input_border_radius); ?>" class="spider_char_input"/>
									  </td>
									</tr>
									<tr id="lightbox_comment8">
									  <td class="spider_label"><label for="lightbox_comment_input_padding"><?php echo __('Comment input padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_input_padding" id="lightbox_comment_input_padding" value="<?php echo esc_attr($row->lightbox_comment_input_padding); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_comment19">
									  <td class="spider_label"><label for="lightbox_comment_input_bg_color"><?php echo __('Comment input background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_input_bg_color" id="lightbox_comment_input_bg_color" value="<?php echo esc_attr($row->lightbox_comment_input_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_comment16">
									  <td class="spider_label"><label for="lightbox_comment_button_bg_color"><?php echo __('Comment button background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_button_bg_color" id="lightbox_comment_button_bg_color" value="<?php echo esc_attr($row->lightbox_comment_button_bg_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_comment5">
									  <td class="spider_label"><label for="lightbox_comment_button_padding"><?php echo __('Comment button padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_button_padding" id="lightbox_comment_button_padding" value="<?php echo esc_attr($row->lightbox_comment_button_padding); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_comment3">
									  <td class="spider_label"><label for="lightbox_comment_button_border_width"><?php echo __('Comment button border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_button_border_width" id="lightbox_comment_button_border_width" value="<?php echo esc_attr($row->lightbox_comment_button_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_comment18">
									  <td class="spider_label"><label for="lightbox_comment_button_border_style"><?php echo __('Comment button border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_comment_button_border_style" id="lightbox_comment_button_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_comment_button_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr id="lightbox_comment17">
									  <td class="spider_label"><label for="lightbox_comment_button_border_color"><?php echo __('Comment button border color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_button_border_color" id="lightbox_comment_button_border_color" value="<?php echo esc_attr($row->lightbox_comment_button_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr id="lightbox_comment4">
									  <td class="spider_label"><label for="lightbox_comment_button_border_radius">C<?php echo __('omment button border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_button_border_radius" id="lightbox_comment_button_border_radius" value="<?php echo esc_attr($row->lightbox_comment_button_border_radius); ?>" class="spider_char_input" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr id="lightbox_comment9">
									  <td class="spider_label"><label for="lightbox_comment_separator_width"><?php echo __('Comment separator width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_separator_width" id="lightbox_comment_separator_width" value="<?php echo esc_attr($row->lightbox_comment_separator_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr id="lightbox_comment22">
									  <td class="spider_label"><label for="lightbox_comment_separator_style"><?php echo __('Comment separator style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="lightbox_comment_separator_style" id="lightbox_comment_separator_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->lightbox_comment_separator_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr id="lightbox_comment23">
									  <td class="spider_label"><label for="lightbox_comment_separator_color"><?php echo __('Comment separator color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="lightbox_comment_separator_color" id="lightbox_comment_separator_color" value="<?php echo esc_attr($row->lightbox_comment_separator_color); ?>" class="jscolor"/>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<fieldset id="Navigation" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Navigation_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="page_nav_font_size"><?php echo __('Font size:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="page_nav_font_size" id="page_nav_font_size" value="<?php echo esc_attr($row->page_nav_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_font_color"><?php echo __('Font color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="page_nav_font_color" id="page_nav_font_color" value="<?php echo esc_attr($row->page_nav_font_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->page_nav_font_style, 'page_nav_font_style', __('Font family:', 'photo-gallery'), 'page_nav_google_fonts' ); ?>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_font_weight"><?php echo __('Font weight:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="page_nav_font_weight" id="page_nav_font_weight">
										  <?php
										  foreach ($font_weights as $key => $font_weight) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->page_nav_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_border_width"><?php echo __('Border width:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="page_nav_border_width" id="page_nav_border_width" value="<?php echo esc_attr($row->page_nav_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_border_style"><?php echo __('Border style:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="page_nav_border_style" id="page_nav_border_style">
										  <?php
										  foreach ($border_styles as $key => $border_style) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->page_nav_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_border_color"><?php echo __('Border color:', 'photo-gallery'); ?></label></td>
									  <td>
										<input type="text" name="page_nav_border_color" id="page_nav_border_color" value="<?php echo esc_attr($row->page_nav_border_color); ?>" class="jscolor"/>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_border_radius"><?php echo __('Border radius:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="page_nav_border_radius" id="page_nav_border_radius" value="<?php echo esc_attr($row->page_nav_border_radius); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Navigation_2" class="wd-table-col wd-table-col-30">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label for="page_nav_margin"><?php echo __('Margin:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="page_nav_margin" id="page_nav_margin" value="<?php echo esc_attr($row->page_nav_margin); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_padding"><?php echo __('Padding:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="page_nav_padding" id="page_nav_padding" value="<?php echo esc_attr($row->page_nav_padding); ?>" class="spider_char_input"/>
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_button_bg_color"><?php echo __('Button background color:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="page_nav_button_bg_color" id="page_nav_button_bg_color" value="<?php echo esc_attr($row->page_nav_button_bg_color); ?>" class="jscolor" />
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_button_bg_transparent"><?php echo __('Button background transparency:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="page_nav_button_bg_transparent" id="page_nav_button_bg_transparent" value="<?php echo esc_attr($row->page_nav_button_bg_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
										<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label><?php echo __('Button transition:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="radio" name="page_nav_button_transition" id="page_nav_button_transition1" value="1"<?php if ($row->page_nav_button_transition == 1) echo 'checked="checked"'; ?> />
										<label for="page_nav_button_transition1" id="page_nav_button_transition1_lbl"><?php echo __('Yes', 'photo-gallery'); ?></label>
										<input type="radio" name="page_nav_button_transition" id="page_nav_button_transition0" value="0"<?php if ($row->page_nav_button_transition == 0) echo 'checked="checked"'; ?> />
										<label for="page_nav_button_transition0" id="page_nav_button_transition0_lbl"><?php echo __('No', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_box_shadow"><?php echo __('Box shadow:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="text" name="page_nav_box_shadow" id="page_nav_box_shadow" value="<?php echo esc_attr($row->page_nav_box_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
										<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Navigation_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
								  <tbody>
									<tr>
									  <td class="spider_label"><label><?php echo __('Position:', 'photo-gallery'); ?> </label></td>
									  <td id="page_nav_position">
										<input type="radio" name="page_nav_position" id="page_nav_position1" value="top"<?php if ($row->page_nav_position == "top") echo 'checked="checked"'; ?> />
										<label for="page_nav_position1" id="page_nav_position1_lbl"><?php echo __('Top', 'photo-gallery'); ?></label>
										<input type="radio" name="page_nav_position" id="page_nav_position0" value="bottom"<?php if ($row->page_nav_position == "bottom") echo 'checked="checked"'; ?> />
										<label for="page_nav_position0" id="page_nav_position0_lbl"><?php echo __('Bottom', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label for="page_nav_align0"><?php echo __('Alignment:', 'photo-gallery'); ?> </label></td>
									  <td>
										<select name="page_nav_align" id="page_nav_align">
										  <?php
										  foreach ($aligns as $key => $align) {
											?>
											<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->page_nav_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
											<?php
										  }
										  ?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label><?php echo __('Numbering:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="radio" name="page_nav_number" id="page_nav_number1" value="1"<?php if ($row->page_nav_number == 1) echo 'checked="checked"'; ?> />
										<label for="page_nav_number1" id="page_nav_number1_lbl"><?php echo __('Yes', 'photo-gallery'); ?></label>
										<input type="radio" name="page_nav_number" id="page_nav_number0" value="0"<?php if ($row->page_nav_number == 0) echo 'checked="checked"'; ?> />
										<label for="page_nav_number0" id="page_nav_number0_lbl"><?php echo __('No', 'photo-gallery'); ?></label>
									  </td>
									</tr>
									<tr>
									  <td class="spider_label"><label><?php echo __('Button text:', 'photo-gallery'); ?> </label></td>
									  <td>
										<input type="radio" name="page_nav_button_text" id="page_nav_button_text1" value="1"<?php if ($row->page_nav_button_text == 1) echo 'checked="checked"'; ?> />
										<label for="page_nav_button_text1" id="page_nav_button_text1_lbl"><?php echo __('Text', 'photo-gallery'); ?></label>
										<input type="radio" name="page_nav_button_text" id="page_nav_button_text0" value="0"<?php if ($row->page_nav_button_text == 0) echo 'checked="checked"'; ?> />
										<label for="page_nav_button_text0" id="page_nav_button_text0_lbl"><?php echo __('Arrow', 'photo-gallery'); ?></label>
										<div class="spider_description"><?php echo __('Next, previous buttons values.', 'photo-gallery'); ?></div>
									  </td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<fieldset id="Carousel" class="spider_type_fieldset">
			<div class="wd-table">
				<div id="Carousel_1" class="wd-table-col wd-table-col-30 wd-table-col-left">
					<div class="wd-box-section">
						<div class="wd-box-content">
							<table style="clear:both;">
							  <tbody>
								<tr>
								  <td class="spider_label"><label for="carousel_cont_bg_color"><?php echo __('Background color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_cont_bg_color" id="carousel_cont_bg_color" value="<?php echo esc_attr($row->carousel_cont_bg_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								 <tr>
								  <td class="spider_label"><label for="carousel_cont_btn_transparent"><?php echo __('Container opacity:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_cont_btn_transparent" id="carousel_cont_btn_transparent" value="<?php echo esc_attr($row->carousel_cont_btn_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
									<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_rl_btn_size"><?php echo __('Right, left buttons size:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_rl_btn_size" id="carousel_rl_btn_size" value="<?php echo esc_attr($row->carousel_rl_btn_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								 <tr>
								  <td class="spider_label"><label for="carousel_play_pause_btn_size"><?php echo __('Play, pause buttons size:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_play_pause_btn_size" id="carousel_play_pause_btn_size" value="<?php echo esc_attr($row->carousel_play_pause_btn_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_rl_btn_color"><?php echo __('Buttons color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_rl_btn_color" id="carousel_rl_btn_color" value="<?php echo esc_attr($row->carousel_rl_btn_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_close_btn_transparent"><?php echo __('Buttons transparency:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_close_btn_transparent" id="carousel_close_btn_transparent" value="<?php echo esc_attr($row->carousel_close_btn_transparent); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
									<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_close_rl_btn_hover_color"><?php echo __('Buttons hover color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_close_rl_btn_hover_color" id="carousel_close_rl_btn_hover_color" value="<?php echo esc_attr($row->carousel_close_rl_btn_hover_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_rl_btn_width"><?php echo __('Right, left buttons width:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_rl_btn_width" id="carousel_rl_btn_width" value="<?php echo esc_attr($row->carousel_rl_btn_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_rl_btn_height"><?php echo __('Right, left buttons height:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_rl_btn_height" id="slideshow_rl_btn_height" value="<?php echo esc_attr($row->carousel_rl_btn_height); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_rl_btn_bg_color"><?php echo __('Right, left buttons background color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_rl_btn_bg_color" id="carousel_rl_btn_bg_color" value="<?php echo esc_attr($row->carousel_rl_btn_bg_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_rl_btn_border_width"><?php echo __('Right, left buttons border width:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_rl_btn_border_width" id="carousel_rl_btn_border_width" value="<?php echo esc_attr($row->carousel_rl_btn_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_rl_btn_border_style"><?php echo __('Right, left buttons border style:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="carousel_rl_btn_border_style" id="carousel_rl_btn_border_style">
									  <?php
									  foreach ($border_styles as $key => $border_style) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->carousel_rl_btn_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_rl_btn_border_color"><?php echo __('Right, left buttons border color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_rl_btn_border_color" id="slideshow_rl_btn_border_color" value="<?php echo esc_attr($row->carousel_rl_btn_border_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_rl_btn_border_radius"><?php echo __('Right, left buttons border radius:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_rl_btn_border_radius" id="carousel_rl_btn_border_radius" value="<?php echo esc_attr($row->carousel_rl_btn_border_radius); ?>" class="spider_char_input"/>
									<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_rl_btn_style"><?php echo __('Right, left buttons style:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="carousel_rl_btn_style" id="carousel_rl_btn_style">
									  <?php
									  foreach ($button_styles as $key => $button_style) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->carousel_rl_btn_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($button_style, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
							  </tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="Carousel_2" class="wd-table-col wd-table-col-30">
					<div class="wd-box-section">
						<div class="wd-box-content">
							<table style="clear:both;">
								<tbody>
								   <tr>
										<td class="spider_label"><label for="carousel_mergin_bottom"><?php echo __('Carousel margin:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="carousel_mergin_bottom" id="carousel_mergin_bottom" value="<?php echo esc_attr($row->carousel_mergin_bottom); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										</td>
									</tr>
								 <tr>
								  <td class="spider_label"><label for="carousel_feature_border_width"><?php echo __('Image border width:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_feature_border_width" id="carousel_feature_border_width" value="<?php echo esc_attr($row->carousel_feature_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/>px
								  </td>
								</tr>

								<tr>
								  <td class="spider_label"><label for="carousel_feature_border_style"><?php echo __('Image border style:', 'photo-gallery'); ?> </label>
								  </td>
								  <td>
									<select name="carousel_feature_border_style" id="carousel_feature_border_style">
									  <?php
									  foreach ($border_styles as $key => $border_style) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->carousel_feature_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
								 <tr>
								  <td class="spider_label"><label for="carousel_feature_border_color"><?php echo __('Image border color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_feature_border_color" id="carousel_feature_border_color" value="<?php echo esc_attr($row->carousel_feature_border_color); ?>" class="jscolor"/>
								  </td>
								</tr>
							  </tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="Carousel_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
					<div class="wd-box-section">
						<div class="wd-box-content">
							<table style="clear:both;">
							  <tbody>
								<tr>
								  <td class="spider_label"><label for="carousel_caption_background_color"><?php echo __('Title background color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_caption_background_color" id="carousel_caption_background_color" value="<?php echo esc_attr($row->carousel_caption_background_color); ?>" class="jscolor"/>
								  </td>
								</tr>

								<tr>
								  <td class="spider_label"><label for="carousel_title_opacity"><?php echo __('Title opacity:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_title_opacity" id="carousel_title_opacity" value="<?php echo esc_attr($row->carousel_title_opacity); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> %
									<div class="spider_description"><?php echo __('Value must be between 0 to 100.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_title_border_radius"><?php echo __('Title border radius:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_title_border_radius" id="carousel_title_border_radius" value="<?php echo esc_attr($row->carousel_title_border_radius); ?>" class="spider_char_input"/>
									<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_caption_p_mergin"><?php echo __('Title margin:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_caption_p_mergin" id="carousel_caption_p_mergin" value="<?php echo esc_attr($row->carousel_caption_p_mergin); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)" /> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_caption_p_pedding"><?php echo __('Title padding:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_caption_p_pedding" id="carousel_caption_p_pedding" value="<?php echo esc_attr($row->carousel_caption_p_pedding); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)" /> px
								  </td>
								</tr>
								<tr>
                  <tr>
                    <!--generate font style with google fonts -->
                    <?php $this->font_style_row( $row->carousel_font_family, 'carousel_font_family', __('Title Font family:', 'photo-gallery'), 'carousel_google_fonts' ); ?>
                  </tr>
								  <td class="spider_label"><label for="carousel_caption_p_font_size"><?php echo __('Title font size:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_caption_p_font_size" id="carousel_caption_p_font_size" value="<?php echo esc_attr($row->carousel_caption_p_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)" /> px
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_caption_p_color"><?php echo __('Title color:', 'photo-gallery'); ?> </label></td>
								  <td>
									<input type="text" name="carousel_caption_p_color" id="carousel_caption_p_color" value="<?php echo esc_attr($row->carousel_caption_p_color); ?>" class="jscolor"/>
								  </td>
								</tr>
								<tr>
								  <td class="spider_label"><label for="carousel_caption_p_font_weight"><?php echo __('Title font weight:', 'photo-gallery'); ?> </label></td>
								  <td>
									<select name="carousel_caption_p_font_weight" id="carousel_caption_p_font_weight">
									  <?php
									  foreach ($font_weights as $key => $font_weight) {
										?>
										<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->carousel_caption_p_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
										<?php
									  }
									  ?>
									</select>
								  </td>
								</tr>
                <tr>
                  <td class="spider_label"><label for="carousel_gal_title_font_size"><?php echo __('Gallery title/description font size:', 'photo-gallery'); ?> </label></td>
                  <td>
                    <input type="text" name="carousel_gal_title_font_size" id="carousel_gal_title_font_size" value="<?php echo
                    esc_attr($row->carousel_gal_title_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
                  </td>
                </tr>
                <tr>
                  <td class="spider_label"><label for="carousel_gal_title_font_color"><?php echo __('Gallery title/description font color:', 'photo-gallery'); ?> </label></td>
                  <td>
                    <input type="text" name="carousel_gal_title_font_color" id="carousel_gal_title_font_color" value="<?php echo esc_attr($row->carousel_gal_title_font_color); ?>" class="jscolor" />
                  </td>
                </tr>
                <tr>
                  <!--generate font style with google fonts -->
                  <?php $this->font_style_row( $row->carousel_gal_title_font_style, 'carousel_gal_title_font_style', __('Gallery title/description font family:', 'photo-gallery'), 'carousel_gal_title_google_fonts' ); ?>
                </tr>
                <tr>
                  <td class="spider_label"><label for="carousel_gal_title_font_weight"><?php echo __('Gallery title/description font weight:', 'photo-gallery'); ?> </label></td>
                  <td>
                    <select name="carousel_gal_title_font_weight" id="carousel_gal_title_font_weight">
                      <?php
                      foreach ($font_weights as $key => $font_weight) {
                        ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php echo (($row->carousel_gal_title_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
                        <?php
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="spider_label"><label for="carousel_gal_title_shadow"><?php echo __('Gallery title/description box shadow:', 'photo-gallery'); ?> </label></td>
                  <td>
                    <input type="text" name="carousel_gal_title_shadow" id="carousel_gal_title_shadow" value="<?php echo esc_attr($row->carousel_gal_title_shadow); ?>" class="spider_box_input" placeholder="10px 10px 10px #888888" />
                    <div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
                  </td>
                </tr>
                <tr>
                  <td class="spider_label"><label for="carousel_gal_title_margin"><?php echo __('Gallery title/description margin:', 'photo-gallery'); ?> </label></td>
                  <td>
                    <input type="text" name="carousel_gal_title_margin" id="carousel_gal_title_margin" value="<?php echo esc_attr($row->carousel_gal_title_margin); ?>" class="spider_char_input" />
                    <div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
                  </td>
                </tr>
                <tr>
                  <td class="spider_label"><label for="carousel_gal_title_align"><?php echo __('Gallery title alignment:', 'photo-gallery'); ?> </label></td>
                  <td>
                    <select name="carousel_gal_title_align" id="carousel_gal_title_align">
                      <?php
                      foreach ($aligns as $key => $align) {
                        ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php echo (($row->carousel_gal_title_align == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($align, 'photo-gallery'); ?></option>
                        <?php
                      }
                      ?>
                    </select>
                  </td>
                </tr>
							  </tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</fieldset>
			<fieldset id="Tags" class="spider_type_fieldset">
				<div class="wd-table">
					<div id="Tags_1" class="wd-table-col wd-table-col-20 wd-table-col-left">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
									<tbody>
									<tr>
										<td class="spider_label"><label><?php echo __('Tags filter type:', 'photo-gallery'); ?> </label></td>
										<td id="tags_view">
											<input type="radio" name="tags_view" id="tags_view1" value="1"<?php if ($row->tags_view == "1") echo 'checked="checked"'; ?> />
												<label for="tags_view1" id="tags_view1_lbl"><?php echo __('Select Box', 'photo-gallery'); ?></label>
											<input type="radio" name="tags_view" id="tags_view0" value="0"<?php if ($row->tags_view == "0") echo 'checked="checked"'; ?> />
												<label for="tags_view0" id="tags_view0_lbl"><?php echo __('Buttons', 'photo-gallery'); ?></label>
										</td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Tags_2" class="wd-table-col wd-table-col-30">
						<div class="wd-box-section">
							<div class="wd-box-content">
								<table style="clear:both;">
									<tbody>
									<tr>
										<td class="spider_label"><label for="tags_but_font_size"><?php echo __('Font size:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="tags_but_font_size" id="tags_but_font_size" value="<?php echo esc_attr($row->tags_but_font_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_font_color"><?php echo __('Font color:', 'photo-gallery'); ?> </label></td>
										<td><input type="text" name="tags_but_font_color" id="tags_but_font_color" value="<?php echo esc_attr($row->tags_but_font_color); ?>" class="jscolor"/></td>
									</tr>
									<tr>
										<!--generate font style with google fonts -->
										<?php $this->font_style_row( $row->tags_but_font_style, 'tags_but_font_style', __('Font family:', 'photo-gallery'), 'tags_but_google_fonts' ); ?>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_font_weight"><?php echo __('Font weight:', 'photo-gallery'); ?> </label></td>
										<td>
											<select name="tags_but_font_weight" id="tags_but_font_weight">
												<?php foreach ($font_weights as $key => $font_weight) { ?>
													<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->tags_but_font_weight == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($font_weight, 'photo-gallery'); ?></option>
											 <?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_margin"><?php echo __('Margin:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="tags_but_margin" id="tags_but_margin" value="<?php echo esc_attr($row->tags_but_margin); ?>" class="spider_char_input"/>
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_padding"><?php echo __('Padding:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="tags_but_padding" id="tags_but_padding" value="<?php echo esc_attr($row->tags_but_padding); ?>" class="spider_char_input"/>
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_border_width"><?php echo __('Border width:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="tags_but_border_width" id="tags_but_border_width" value="<?php echo esc_attr($row->tags_but_border_width); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_border_style"><?php echo __('Border style:', 'photo-gallery'); ?> </label></td>
										<td>
											<select name="tags_but_border_style" id="tags_but_border_style">
												<?php foreach ($border_styles as $key => $border_style) { ?>
													<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->tags_but_border_style == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
											  <?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_border_color"><?php echo __('Border color:', 'photo-gallery'); ?></label></td>
										<td><input type="text" name="tags_but_border_color" id="tags_but_border_color" value="<?php echo esc_attr($row->tags_but_border_color); ?>" class="jscolor"/></td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_border_radius"><?php echo __('Border radius:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="tags_but_border_radius" id="tags_but_border_radius" value="<?php echo esc_attr($row->tags_but_border_radius); ?>" class="spider_char_input"/>
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_bg_color"><?php echo __('Button background color:', 'photo-gallery'); ?> </label></td>
										<td><input type="text" name="tags_but_bg_color" id="tags_but_bg_color" value="<?php echo esc_attr($row->tags_but_bg_color); ?>" class="jscolor" /></td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_active_bg_color"><?php echo __('Active Button background color:', 'photo-gallery'); ?> </label></td>
										<td><input type="text" name="tags_but_active_bg_color" id="tags_but_active_bg_color" value="<?php echo esc_attr($row->tags_but_active_bg_color); ?>" class="jscolor" /></td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_active_color"><?php echo __('Active Button font color:', 'photo-gallery'); ?> </label></td>
										<td><input type="text" name="tags_but_active_color" id="tags_but_active_color" value="<?php echo esc_attr($row->tags_but_active_color); ?>" class="jscolor" /></td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_see_all_size"><?php echo __('See All Button Font size:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="tags_but_see_all_size" id="tags_but_see_all_size" value="<?php echo esc_attr($row->tags_but_see_all_size); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_see_all_color"><?php echo __('See All Button Font color:', 'photo-gallery'); ?> </label></td>
										<td><input type="text" name="tags_but_see_all_color" id="tags_but_see_all_color" value="<?php echo esc_attr($row->tags_but_see_all_color); ?>" class="jscolor"/></td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_see_all_back_color"><?php echo __('See All Button background color:', 'photo-gallery'); ?> </label></td>
										<td><input type="text" name="tags_but_see_all_back_color" id="tags_but_see_all_back_color" value="<?php echo esc_attr($row->tags_but_see_all_back_color); ?>" class="jscolor" /></td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_see_all_border_w"><?php echo __('See All Button Border width:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="tags_but_see_all_border_w" id="tags_but_see_all_border_w" value="<?php echo esc_attr($row->tags_but_see_all_border_w); ?>" class="spider_int_input" onkeypress="return spider_check_isnum(event)"/> px
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_see_all_border_s"><?php echo __('See All Button Border style:', 'photo-gallery'); ?> </label></td>
										<td>
											<select name="tags_but_see_all_border_s" id="tags_but_see_all_border_s">
												<?php foreach ($border_styles as $key => $border_style) { ?>
																			<option value="<?php echo esc_attr($key); ?>" <?php echo (($row->tags_but_see_all_border_s == $key) ? 'selected="selected"' : ''); ?>><?php echo esc_html__($border_style, 'photo-gallery'); ?></option>
												<?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_see_all_border_c"><?php echo __('See All Button Border color:', 'photo-gallery'); ?></label></td>
										<td><input type="text" name="tags_but_see_all_border_c" id="tags_but_see_all_border_c" value="<?php echo esc_attr($row->tags_but_see_all_border_c); ?>" class="jscolor"/></td>
									</tr>
									<tr>
										<td class="spider_label"><label for="tags_but_see_all_border_r"><?php echo __('See All Button Border radius:', 'photo-gallery'); ?> </label></td>
										<td>
											<input type="text" name="tags_but_see_all_border_r" id="tags_but_see_all_border_r" value="<?php echo esc_attr($row->tags_but_see_all_border_r); ?>" class="spider_char_input"/>
											<div class="spider_description"><?php echo __('Use CSS type values.', 'photo-gallery'); ?></div>
										</td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="Tags_3" class="wd-table-col wd-table-col-30 wd-table-col-right">
					</div>
				</div>
			</fieldset>
		</div>
		<input type="hidden" id="default_theme" name="default_theme" value="<?php echo esc_attr($row->default_theme); ?>" />
		<input type="hidden" id="active_tab" name="active_tab"  value="<?php echo esc_attr($params['active_tab']); ?>" />
    <?php
	}
}
