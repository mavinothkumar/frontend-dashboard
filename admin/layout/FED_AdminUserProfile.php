<?php
if ( ! class_exists('FED_AdminUserProfile')) {
    class FED_AdminUserProfile
    {
        public function __construct()
        {
            add_action('show_user_profile', array($this, 'fed_show_user_profile'));
            add_action('edit_user_profile', array($this, 'fed_show_user_profile'));
            /**
             * Save extra user fields on User Profile Page
             */
            add_action('personal_options_update', array($this, 'fed_personal_options_update'));
            add_action('edit_user_profile_update', array($this, 'fed_personal_options_update'));
        }

        /**
         * Show extra fields on User Profile Page
         *
         * @param WP_User $user User Profile Fields.
         */
        public function fed_show_user_profile($user)
        { ?>
			<h3><?php _e('Frontend Dashboard', 'frontend-dashboard') ?></h3>
			<table class="form-table bc_fed fed_profile_table">
                <?php
                $fields = fed_fetch_user_profile_extra_fields();
                if (null === $fields || false === $fields) {
                    echo __('Sorry no extra fields added - Please add here ') . make_clickable(menu_page_url('fed_user_profile',
                            false));
                } else {
                    foreach ($fields as $field) {
                        $extended        = isset($field['extended']) ? is_string($field['extended']) ? unserialize($field['extended']) : $field['extended'] : '';
                        $extended_fields = fed_default_extended_fields();
                        $default_value   = array(
                            'label_name'  => isset($field['label_name']) ? esc_attr($field['label_name']) : '',
                            'is_required' => isset($field['is_required']) ? esc_attr($field['is_required']) : 'false',
                            'input_type'  => isset($field['input_type']) ? esc_attr($field['input_type']) : '',
                            'input_meta'  => isset($field['input_meta']) ? esc_attr($field['input_meta']) : '',
                            'placeholder' => isset($field['placeholder']) ? esc_attr($field['placeholder']) : '',
                            'class_name'  => isset($field['class_name']) ? esc_attr($field['class_name']) . ' regular-text' : 'regular-text',
                            'id_name'     => isset($field['id_name']) ? esc_attr($field['id_name']) : '',
                            'user_value'  => get_user_meta($user->ID, $field['input_meta'], true),
                            'input_min'   => isset($field['input_min']) ? esc_attr($field['input_min']) : '',
                            'input_max'   => isset($field['input_max']) ? esc_attr($field['input_max']) : '',
                            'input_step'  => isset($field['input_step']) ? esc_attr($field['input_step']) : '',
                            'input_row'   => isset($field['input_row']) ? esc_attr($field['input_row']) : '',
                            'input_value' => isset($field['input_value']) ? esc_attr($field['input_value']) : '',
                        );

                        foreach ($extended_fields as $index => $extended_field) {
                            $default_value['extended'][$index] = isset($extended[$index]) ? esc_attr($extended[$index]) : '';
                        }
                        ?>
						<tr>
							<th>
								<label for="<?php echo $default_value['input_meta'] ?>"><?php echo $default_value['label_name'] ?></label>
							</th>

							<td>
                                <?php
                                echo fed_get_input_details($default_value);
                                ?>
							</td>
						</tr>
                    <?php }
                }
                ?>
			</table>
        <?php }

        /**
         * Personal Options to Update on Backend.
         *
         * @param string $user_id User ID
         *
         * @return bool
         */
        public function fed_personal_options_update($user_id)
        {

            if ( ! current_user_can('edit_user', $user_id)) {
                return false;
            }
            $fields = fed_fetch_user_profile_extra_fields();
            if ($fields && null !== $fields) {
                foreach ($fields as $field) {
                    $default_value = array(
                        'label_name'  => isset($field['label_name']) ? esc_attr($field['label_name']) : '',
                        'is_required' => isset($field['is_required']) ? esc_attr($field['is_required']) : 'false',
                        'input_type'  => isset($field['input_type']) ? esc_attr($field['input_type']) : '',
                        'input_meta'  => isset($field['input_meta']) ? esc_attr($field['input_meta']) : '',
                        'placeholder' => isset($field['placeholder']) ? esc_attr($field['placeholder']) : '',
                        'class_name'  => isset($field['class_name']) ? esc_attr($field['class_name']) : '',
                        'id_name'     => isset($field['id_name']) ? esc_attr($field['id_name']) : '',
                        'input_min'   => isset($field['input_min']) ? esc_attr($field['input_min']) : '',
                        'input_max'   => isset($field['input_max']) ? esc_attr($field['input_max']) : '',
                        'input_step'  => isset($field['input_step']) ? esc_attr($field['input_step']) : '',
                        'input_row'   => isset($field['input_row']) ? esc_attr($field['input_row']) : '',
                        'input_value' => isset($field['input_value']) ? esc_attr($field['input_value']) : '',
                    );

                    update_user_meta($user_id, $default_value['input_meta'], $_POST[$default_value['input_meta']]);
                }
            }

            return true;
        }
    }

    new  FED_AdminUserProfile();
}