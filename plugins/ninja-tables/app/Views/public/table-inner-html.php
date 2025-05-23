<?php
$table_columns = array_reverse($table_columns);
$header_row = '';
$counter = 1;
$hasImageFunction = function_exists('nt_parse_image_column');
?>
<thead>
<tr class="footable-header">
    <?php foreach ($table_columns as $index => $table_column) : ?>
        <?php
        if (strip_tags($table_column['title']) == '#colspan#') {
            $header_row = '<td class="ninja_temp_cell"></td>' . $header_row;
            $counter++;
            continue;
        }
        $colspan = '';
        if ($counter > 1) {
            $colspan = 'colspan="' . $counter . '"';
        }
        $header_row = '<th scope="col" ' . $colspan . ' class="' . implode(' ', (array)$table_column['classes']) . ' ' . $table_column['breakpoints'] . '">' . do_shortcode($table_column['title']) . '</th>' . $header_row;
        ?>
        <?php $counter = 1; endforeach; ?>
    <?php ninjaTablesPrintSafeVar($header_row); // the $header_row html attributes from admins are already escaped and sanitized ?>
</tr>
</thead>
<tbody>

<?php
if ($table_rows && count($table_columns)):
    $columnLength = count($table_columns) - 1;
    foreach ($table_rows as $row_index => $table_row) :
        $row = '';
        $rowId = '';
        if (isset($table_row['___id___'])) {
            $rowId = $table_row['___id___'];
        } else {
            $rowId = $row_index;
        }

        $row_class = 'ninja_table_row_' . $row_index;
        $row_class .= ' nt_row_id_' . $rowId;
        ?>
        <tr data-row_id="<?php echo esc_attr($rowId); ?>" class="<?php echo esc_attr($row_class); ?>">
            <?php
            $colSpanCounter = 1; // Make the colspan counter 1 at first
            foreach ($table_columns as $index => $table_column) {
                $column_value = (isset($table_row[$table_column['name']]) ? $table_row[$table_column['name']] : null);
                $columnValueDataAtts = '';
                $columnType = (isset($table_column['original']['data_type']) ? $table_column['original']['data_type'] : null);
                if (is_array($column_value)) {
                    if ($columnType == 'image') {
                        $columnValueDataAtts = json_encode($column_value);
                        if ($hasImageFunction) {
                            $column_value = nt_parse_image_column($column_value, $table_column);
                        } else {
                            $column_value = '';
                        }
                    } else {
                        $columnValueDataAtts = json_encode($column_value);
                        $column_value = implode(', ', $column_value);
                        $column_value = do_shortcode($column_value);
                    }
                } else if ($columnType == 'button') {
                    if ($hasImageFunction) {
                        $column_value = nt_parse_button_column($column_value, $table_column);
                    }
                } else if(is_string($column_value)) {
                    $column_value = do_shortcode($column_value);
                }
                $colspan = false;
                if ($index != $columnLength) {
                    if ($column_value && strip_tags($column_value) == '#colspan#') {
                        $row = '<td class="ninja_temp_cell" data-colspan="#colspan#"></td>' . $row;
                        $colSpanCounter = $colSpanCounter + 1;
                        // if we get #colspan# value then we are increasing colspan counter by 1 and adding a temp column
                        continue;
                    }
                }

                if ($colSpanCounter > 1) {
                    $colspan = ' colspan="' . $colSpanCounter . '"';
                    // if colspan counter is greater than 1 then we are adding the colspan into the dom
                }

                if ($columnValueDataAtts) {
                    $row = '<td' . $colspan . ' data-json_values=' . $columnValueDataAtts . '>' . $column_value . '</td>' . $row;
                } else {
                    $row = '<td' . $colspan . '>' . $column_value . '</td>' . $row;
                }

                $colSpanCounter = 1;
                // we are reseting the colspan counter value here because the colspan is done for this iteration
            }
            ninjaTablesPrintSafeVar($row); //the $row html attributes from admins are already escaped and sanitized
            ?>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
</tbody><!--ninja_tobody_rendering_done-->
