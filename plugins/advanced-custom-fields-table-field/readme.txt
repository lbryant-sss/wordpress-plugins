=== Table Field Add-on for ACF and SCF ===
Contributors: jonua
Tags: acf, table, scf, advanced custom fields, secure custom fields
Requires at least: 5.3
Tested up to: 6.8
Stable tag: 1.3.29
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A Table Field Add-on for the Advanced Custom Fields and Secure Custom Fields Plugin.

== Description ==

The Table Field plugin is an Add-on and enhances the functionality of the [Advanced Custom Fields (ACF) plugin](https://www.advancedcustomfields.com) and the [Secure Custom Fields plugin](https://wordpress.org/plugins/secure-custom-fields/) with easy-to-edit tables.

This plugin requires the [Advanced Custom Fields plugin](https://www.advancedcustomfields.com) or the [Secure Custom Fields plugin](https://wordpress.org/plugins/secure-custom-fields/)!

The table field works also with the ACF repeater and flexible field types and supports the [ACF Blocks for Gutenberg](https://www.advancedcustomfields.com/blog/acf-5-8-introducing-acf-blocks-for-gutenberg/)

= Features =
* Table Header (Option)
* Table Caption (Option)
* Support for ACF Gutenberg blocks
* Add and remove table columns and rows
* Change order of columns and rows by dragging
* To move to the next cells editor press key: tab
* To move to the previous cells editor press key: shift + tab

== Frequently Asked Questions ==

= How to output the table html? =

To render the table fields data as an html table in one of your template files (page.php, single.php) you can start with the following basic code example:

`
$table = get_field( 'your_table_field_name' );

if ( ! empty ( $table ) ) {

	echo '<table>';

		if ( ! empty( $table['caption'] ) ) {

			echo '<caption>' . $table['caption'] . '</caption>';
		}

		if ( ! empty( $table['header'] ) ) {

			echo '<thead>';

				echo '<tr>';

					foreach ( $table['header'] as $th ) {

						echo '<th>';
							echo $th['c'];
						echo '</th>';
					}

				echo '</tr>';

			echo '</thead>';
		}

		echo '<tbody>';

			foreach ( $table['body'] as $tr ) {

				echo '<tr>';

					foreach ( $tr as $td ) {

						echo '<td>';
							echo $td['c'];
						echo '</td>';
					}

				echo '</tr>';
			}

		echo '</tbody>';

	echo '</table>';
}
`

= Table field returns no data on get_field()? =

If the table has only one empty cell, then `get_field()` returns `FALSE`. `get_field()` returns NULL when a field is not stored in the database. That happens when a page is copied but not their fields content. You can check both with `empty()`…

`$table = get_field( 'your_table_field_name' );

if ( ! empty( $table ) ) {
	// $table is not FALSE and not NULL.
	// Field exists in database and has content.
}`

= How to handle line breaks? =

This is about displaying line breaks in the admin tables and getting line breaks as `<br>` when outputting the tables HTML.

**Converting Line Breaks for HTML Output**

To convert line breaks to `<br>` in tables HTML output the PHP function `nl2br()` can be used:

For line breaks in **table header cells** replace…
`
echo $th['c'];
`
with…
`
echo nl2br( $th['c'] );
`

For line breaks in **table body cells** replace…
`
echo $td['c'];
`
with…
`
echo nl2br( $td['c'] );
`

**Displaying Line Breaks in Editing Tables**

To display natural line breaks in the editing tables in the admin area, add the following styles to the admin area.

`
.acf-table-header-cont,
.acf-table-body-cont {
    white-space: pre-line;
}
`

One way to add these styles to the WordPress admin area is adding the following code to your functions.php file of the theme.

`
add_action('admin_head', 'acf_table_styles');

function acf_table_styles() {
  echo '<style>
    .acf-table-header-cont,
    .acf-table-body-cont {
        white-space: pre-line;
    }
  </style>';
}
`

= How to use the table field in Elementor or other Page Builders? =

In general, its up to the page builder plugins to support ACF field types. But because the table field is not a native ACF field, the support for this field may never happen in page builders.

For now the way to go is using a shortcode. Elementor provides for example a shortcode widget. Before you can use a shortcode to display a table fields table, you have to setup a shortcode in functions.php. The following code does this. You can modify the table html output for your needs.

`function shortcode_acf_tablefield( $atts ) {

    $param = shortcode_atts( array(
        'field-name' => false,
        'subfield-name' => false,
        'post-id' => false,
        'table-class' => '',
    ), $atts );

    $class = new class( $param ) {

        public $atts;

        public $field_names;

        public $field_data = '';

        public $html = '';

        public function __construct( $atts ) {

            if ( is_string( $atts['subfield-name'] ) ) {

                $atts['field-name'] = $atts['subfield-name'];
            }

            if ( ! is_string( $atts['field-name'] ) ) {

                return '';
            }

            $this->atts = $atts;

            $this->field_names = explode( '/', $this->atts['field-name'] );

            $this->subfield( 0 );

        }

        private function may_get_table_html( $data ) {

            if ( isset( $data['body'] ) ) {

                $return = '<table class="' . $this->atts['table-class'] . '">';

                    if ( ! empty( $data['caption'] ) ) {

                        echo '<caption>' . $data['caption'] . '</caption>';
                    }

                    if ( $data['header'] ) {

                        $return .= '<thead>';

                            $return .= '<tr>';

                                foreach ( $data['header'] as $th ) {

                                    $return .= '<th>';
                                        $return .= $th['c'];
                                    $return .= '</th>';
                                }

                            $return .= '</tr>';

                        $return .= '</thead>';
                    }

                    $return .= '<tbody>';

                        foreach ( $data['body'] as $tr ) {

                            $return .= '<tr>';

                                foreach ( $tr as $td ) {

                                    $return .= '<td>';
                                        $return .= $td['c'];
                                    $return .= '</td>';
                                }

                            $return .= '</tr>';
                        }

                    $return .= '</tbody>';

                $return .= '</table>';

                $this->html .= $return;
            }
        }

        private function subfield( $level = 0, $data = null ) {

            if ( isset( $data['body'] ) ) {

                $this->may_get_table_html( $data );

                return;
            }
            else if ( ! isset( $this->field_names[ $level ] ) ) {

                return;
            }
            else if ( $data === null ) {

                if ( $this->atts['subfield-name'] === false ) {

                    $data = get_field(  $this->field_names[0],  $this->atts['post-id'] );
                }
                else {

                    $data = get_sub_field( $this->field_names[0] );
                }
            }
            else if ( isset( $data[ $this->field_names[ $level ] ] ) ) {

                $data = $data[ $this->field_names[ $level ] ];
            }

            // repeater/group field
            if (
                is_array( $data ) &&
                isset( $data[0] ) &&
                ! isset( $data[0]['acf_fc_layout'] )
                ) {

                if ( is_numeric( $this->field_names[ $level + 1 ] )  ) {

                    if ( isset( $data[  $this->field_names[ $level + 1 ] ] ) ) {

                        $this->subfield( $level + 1, $data[  $this->field_names[ $level + 1 ] ] );
                    }
                }
                else {

                    foreach( $data as $key => $item ) {

                        $this->subfield( $level + 1, $item );
                    }
                }
            }

            // flexible content field
            else if (
                is_array( $data ) &&
                isset( $data[0] ) &&
                isset( $data[0]['acf_fc_layout'] )
                ) {

                foreach( $data as $key => $item ) {

                    if (
                        $item['acf_fc_layout'] === $this->field_names[ $level + 1 ] &&
                        isset( $item[ $this->field_names[ $level + 2 ] ] )
                    ) {

                        $this->subfield( $level + 2, $item[ $this->field_names[ $level + 2 ] ] );
                    }
                }
            }

            // table field
            else {

                $this->subfield( $level + 1, $data );
            }
        }
    };

    return $class->html;
}

add_shortcode( 'tablefield', 'shortcode_acf_tablefield' );`

Then use the shortcode with the corresponding shortcode options of the Page Builder.

Getting a table field from the **current page or post**…

`[tablefield field-name="table_field_name"]`

Getting a table field from a **group field**…

`[tablefield field-name="group_field_name/table_field_name"]`

Getting a table field from a **repeater field**…

`[tablefield field-name="repeater_field_name/table_field_name"]`

Getting a table field from a specific **repeater field item**…

`[tablefield field-name="repeater_field_name/item_index/table_field_name"]`

Getting a table field from a **flexible content field**…

`[tablefield field-name="flexible_content_field_name/layout_name/table_field_name"]`

You can also **get a table field from any kind of nested fields** like in this example, where the table field is part of a layout of an flexible content field, that is a subfield of a repeater field, that is a subfield of a group field…

`[tablefield field-name="group_field_name/repeater_field_name/flexible_content_field_name/layout_name/table_field_name"]`

Getting a table field from inside a **group**, **repeater** or **flexible content field** loop…

`[tablefield subfield-name="table_field_name"]`

Getting a table field from **another page or post**…

`[tablefield field-name="table_field_name" post-id="123"]`

Getting a table field from a **ACF option page**…

`[tablefield field-name="table_field_name" post-id="option"]`

Adding a **CSS class to the table HTML element**…

`[tablefield field-name="table_field_name" table-class="my-table-style"]`

= Updating a table using update_field() =

You can use the ACF PHP function `update_field()` to change a tables data.

__Notice__

- Make sure that the number of entries in the header array matches the number of cells in the body rows.
- The array key 'c' stands for the content of the cells to have the option of adding other cell setting in future development.
- The table data obtained by get_field() are formatted and differ by the original database data obtained by get_post_meta().

__Example of changing table data using get_field() and update_field()__

`
// the post ID where to update the table field
$post_id = 123;

$table_data = get_field( 'my_table', $post_id );

$table_data = array(
	'use_header' => true, // boolean true/false
	'caption' => 'My Caption',
	'header' => array(
		0 => array(
			'c' => 'A',
		),
		1 => array(
			'c' => 'B',
		),
	),
	'body' => array(
		0 => array(
			0 => array(
				'c' => 'The content of first cell of first row',
			),
			1 => array(
				'c' => 'The content of second cell of first row',
			),
		),
		1 => array(
			0 => array(
				'c' => The content of first cell of second row',
			),
			1 => array(
				'c' => 'The content of second cell of second row',
			),
		),
	)
);

update_field( 'my_table', $table_data, $post_id );
`

__Example of adding a new row__
`
// the post ID where to update the table field
$post_id = 123;

// gets the table data
$table_data = get_field( 'my_table', $post_id );

// defines the new row and its columns
$new_row = array(

	// must define the same amount of columns as exists in the table

	// column 1
	array(
		// the 'c' stands for content of the cell
		'c' => 'Cell Content of Column 1',
	),

	// column 2
	array(
		'c' => 'Cell Content of Column 2',
	)
);

// adds the new row to the table body data
array_push( $table_data['body'], $new_row );

// saves the new table data
update_field( 'my_table', $table_data, $post_id );
`

= Third party plugins issues =

Since version 1.3.1 of the table plugin, the storing format of the table data changes from JSON string to serialized array for new or updated tables. The issue with JSON is because of third party plugins that do not properly applying `wp_slash()` to a post_meta value before updating with `update_post_metadata()`. This can break JSON strings because `update_post_metadata()` removes backslashes by default. Backslashes are part of the JSON string syntax escaping quotation marks in content.

The table field plugin prevents broken JSON strings to save as a table field data and throws an error message that explains the issue. But this may also breaks the functionality of the third party plugin trying to update the table data. You could disable the JSON string check in the table field plugin using the following code in the wp-config.php file. But then the table JSON data are no longer protected from destroing by `update_post_metadata()`. Use the following code in wp-config.php only, if you understand the risk…

`define( "ACF_TABLEFIELD_FILTER_POSTMETA", false );`

== Installation ==

This software can be used as both a WP plugin and a theme include.
However, only when activated as a plugin will updates be available.

= Plugin =
1. Copy the "advanced-custom-fields-table-field" folder into your plugins folder.
2. Activate the plugin via the Plugins admin page.


== Screenshots ==

1. The Field Settings

2. The Field Content Editing

2. Grab the rows and columns in the grey area and drag them.


== Translations ==

* English - default, always included
* German: Deutsch - immer dabei!
* Danish: Dansk - altid der!
* Polish: Polski - zawsze tam jest!

*Note:* Please [contribute your language](https://translate.wordpress.org/projects/wp-plugins/advanced-custom-fields-table-field) to the plugin to make it even more useful.

== PRO ==

There is also a [Table Field Pro](https://www.acf-table-field.com) Add-on for the Advanced Custom Fields and Secure Custom Fields plugins.

* Setup custom default table
* Setup minimum and maximum amount of rows and columns
* Setup style choice for the table and parts of the table
* Restrict table editing functionalities
* Configurable optional WordPress editor for cell content editing
* Table head and foot rows
* Stub column
* Rowspan and colspan
* Improved way for moving rows and columns
* Disable moving individual columns
* Support for REST-API
* Support for WP GraphQL
* Support for third-party plugins

The Pro plugin can run in parallel and you can change an existing field with field type "Table" to the field type "Table Pro" as required.

[to the plugin website](https://www.acf-table-field.com)

== Upgrade Notice ==


== Changelog ==

= 1.3.29 =
* Fixes missing field preview image.

= 1.3.28 =
* Fixes loading translations from translate.wordpress.org.

= 1.3.27 =
* Fixes loading plugin textdomain to early.

= 1.3.26 =
* Adds auto repair table. Fixes table data when loading from database and on editing table.

= 1.3.24 =
* Fixes Rest API scheme

= 1.3.23 =
* Adds Rest API scheme

= 1.3.22 =
* Fixes an issue where the trigger changed was executed to the field without any actual change to the field.
* Fixes an issue where changes to the order of rows and columns and new rows and columns were not saved.

= 1.3.21 =
* Adds update_field() support for option pages
* Fixes missing "use_header" property on get_field()

= 1.3.20 =
* Fixes multiple registering events on Gutenberg
* Fixes issue not initial loading editing table

= 1.3.19=
* Fixes issue not initial loading editing tables in Gutenberg blocks.

= 1.3.18 =
* Fixes issue with Gutenberg block containing a table field. If the same block was used multiple times on a page, the changes were not saved.

= 1.3.17 =
* Changes registering the ACF field type using the acf_register_field_type methode.
* Removes support for ACF version 4.

= 1.3.16 =
* Fixes an logical error causing PHP warnings

= 1.3.15 =
* Fixes an issue with count() in PHP8

= 1.3.14 =
* Prevents the font-size and line-height in the blue editor window of the table cells from being overwritten by other styles.
* Fixes an issue in update_field() where setting the "use_header" option to false did not work.

= 1.3.13 =
* Fixes missing sortable columns and rows in ACF Gutenberg blocks
* Updates depricated jQuery functionalities

= 1.3.12 =
* Updates styles of acf buttons plus and minus

= 1.3.11 =
* Adds support for updating term type by update_field()

= 1.3.10 =
* Fixes table cell content and caption update issue on ACF Gutenberg blocks
* Replaces jQuery depricated size() methode by .length property

= 1.3.9 =
* Fixes broken ACF select field styles in WordPress 5.3.
* Fixes an issue when adding or removing columns using update_field().

= 1.3.8 =
* Fixes an issue where the option "use header" was not applied on updating a field with update_field().
* Fixes an issue where percent characters in a table field content causes an JavaScript error.

= 1.3.7 =
* Fixes an issue where the table header was not displayed on a page preview.

= 1.3.6 =
* Fixes an issue when changing the field type to table of a field that already has content in the database from another field type.

= 1.3.5 =
* Fixes an issue that removes table header content using update_field() while option "use header" is set to "no".
* Fixes an issue with the update_post_metadata filter

= 1.3.4 =
* Fixes an issue that prevents the removal of table contents

= 1.3.3 =
* Fixes returning empty table after saving content containing a single quote.

= 1.3.2 =
* Fixes returning empty table after saving content containing quotes
* Fixes an issue using update_field() on a table field

= 1.3.1 =
* Changes table data storing format from JSON string to serialized array. This is due to an issue caused by third party plugins using update_post_meta() without providing wp_slash() to the value before. Existing table data values in JSON string format in the database will still exists and be compatible. When a field is saved again, the storage format changes from JSON to serialized array.
* Fixes an PHP error of table caption

= 1.3.0 =
* Adds support for table caption
* Fixes an JavaScript issue for ACF version 4

= 1.2.7 =
* Adds PHP constant ACF_TABLEFIELD_FILTER_POSTMETA. Setting this constant to false prevents an update_post_metadata filter looking for tablefield JSON strings destroyed by update_post_meta().

= 1.2.6 =
* Replaces jQuery.noConflict methode
* Prevents PHP error if table fields value is from a previous fieldtype

= 1.2.5 =
* Adds danish translation, thanks to Jeppe Skovsgaard

= 1.2.4 =
* Fixes backslashes on using update_field();

= 1.2.3 =
* Adds support for the ACF update_field() function. If you get the table fields data array by get_field(), you can change the table data array and using update_field() to save the changes to the field.

= 1.2.2 =
* Adds plugin version to table data for handling structural changes.
* Fixes loosing table data containing quotes on third party update_post_meta() actions to table field values. Limited to new fields or fields value changed since plugin version 1.2.2.
* Fixes an PHP warning since PHP 7.2 when body data is null

= 1.2.1 =
* Fixes not using user locale for translation
* Adds description for handling line breaks to plugins page

= 1.2 =
* Adds support for tab navigation. Uses shift + tab for backward navigation.
* Minor code improvements

= 1.1.16 =
* Keeps the WordPress admin area working, if tablefields value is not a valid JSON string. Logs the invalid value in the console for debugging.

= 1.1.15 =
* Adds polish translation by Pawel Golka

= 1.1.14 =
* Fixes table does not appear under certain field groups location rules

= 1.1.13 =
* Fixes an XSS issue within /wp-admin/ pages

= 1.1.12 =
* Adds support for field groups post taxonomy rule

= 1.1.11 =
* Fixes rerendering of tables while changing other content

= 1.1.10 =
* Fixed table functionality with respect to the ACF rules

= 1.1.9 =
* Fixes returning false on single empty table cell for ACF version 4

= 1.1.8 =
* Fixes support for user edit pages

= 1.1.7 =
* Fixes support for user profile pages

= 1.1.6 =
* UI: Fixes table header switch off problem

= 1.1.5 =
* Fixes issue occured after database migration with plugin "WP Migrate DB"

= 1.1.4 =
* Takes over icon class changes in ACF-Pro since version 5.3.2

= 1.1.3 =
* Fixes wrong function name 'change_template'

= 1.1.2 =
* Fixes missing table on page template change

= 1.1.1 =
* Compatibility to icon changes of ACF Pro version 5.2.8
* Fixes table top legend height in FireFox
* Fixes delete column icon position in IE

= 1.1 =
* Improved User Experience when deleting all columns and rows.
* Compatibility to changes of ACF Pro version 5.2.7.

= 1.0.7 =
* Use wp_json_encode() instead of json_encode(). This may fix issues in rare enviroments.

= 1.0.6 =
* If the table has only a single empty cell (this is by default), no table data will return now.

= 1.0.5 =
* Fixes javascript issue in IE 8.
* Fixes missing table borders and table header´s height in FireFox.

= 1.0.4 =
* Fixes an uri problem on some hosts.

= 1.0.3 =
* Fixes an php error on HTTP_REFFERER.

= 1.0.2 =
* Fixes error when including the plugin from inside a theme.

= 1.0.1 =
* Fixes ACF validation error "required" when header option "use table header" was used and unchecked.

= 1.0 =
* Official Release of the free version
