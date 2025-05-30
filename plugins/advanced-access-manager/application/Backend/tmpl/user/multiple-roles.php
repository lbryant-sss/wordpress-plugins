<?php /** @version 7.0.0 **/ ?>

<?php if (defined('AAM_KEY')) { ?>
    <?php if ((!defined('IS_PROFILE_PAGE') || !IS_PROFILE_PAGE) && !is_network_admin() && (empty($user) || current_user_can('promote_user', $user->ID))) { ?>
        <table class="form-table">
            <tr>
                <th><?php echo __('User Roles', 'advanced-access-manager'); ?></th>
                <td>
                    <div class="wp-tab-panel">
                        <ul>
                            <?php $roles = (!empty($user) ? $user->roles : array(get_option('default_role'))); ?>
                            <?php foreach (get_editable_roles() as $id => $role) { ?>
                                <li>
                                    <label for="aam_user_role_<?php echo esc_attr($id); ?>">
                                        <input
                                            type="checkbox"
                                            name="aam_user_roles[]"
                                            value="<?php echo esc_attr($id); ?>"
                                            <?php checked(in_array($id, $roles)); ?>
                                            id="aam_user_role_<?php echo esc_attr($id); ?>"
                                        />
                                        <?php echo esc_html(translate_user_role($role['name'])); ?>
                                    </label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Remove standard WordPress roles selector-->
        <script>
            (function($) {
                $(document).ready(function() {
                    // Remove default role drop-down from User Edit page
                    if ($('.user-role-wrap').length) {
                        $('.user-role-wrap').remove();
                    }

                    // Remove default role drop-down from Add New User page
                    if ($('#role').length) {
                        $('#role').parent().parent().remove();
                    }

                    // Remove default role drop-down from Add Existing User page
                    if ($('#adduser-role').length) {
                        $('#adduser-role').parent().parent().remove();
                    }
                });
            })(jQuery);
        </script>
    <?php } ?>
<?php }