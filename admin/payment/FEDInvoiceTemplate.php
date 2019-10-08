<?php
if ( ! defined('ABSPATH')) {
    exit;
}

if ( ! class_exists('FEDInvoiceTemplate')) {
    /**
     * Class FEDInvoiceTemplate
     */
    class FEDInvoiceTemplate
    {
        public function template()
        {

            $templates = apply_filters('fed_invoice_template', array());

            if ($templates && count($templates) > 0) {
                $settings = get_option('fed_payment_settings');
                ?>
                <form method="post" class="fed_ajax"
                      action="<?php echo fed_get_ajax_form_action('fed_ajax_request').'&fed_action_hook=FEDInvoiceTemplate@update'; ?>">
                    <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce'); ?>
                    <div class="row">
                        <?php
                        foreach ($templates as $index => $template) {
                            ?>
                            <div class="col-md-4">
                                <div class="panel panel-secondary-heading">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><?php echo $template['name'] ?></h3>
                                    </div>
                                    <div class="panel-body">
                                        <img alt="" class="img-responsive" src="<?php echo $template['image'] ?>">
                                        <div class="fed_flex_center">
                                            <label>
                                                <input class="radio" type="radio" name="template"
                                                       value="template-1" <?php echo isset($settings['invoice']['template']['default']) && $settings['invoice']['template']['default'] === 'template-1' ? 'checked' : '' ?>/>
                                            </label>
                                        </div>
                                        <div class="text-center">
                                            <strong><?php _e('Select to make default',
                                                    'frontend-dashboard') ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <button class="btn btn-primary" type="submit"><?php _e('Submit', 'frontend-dashboard') ?></button>
                </form>
                <?php
            } else {
                $template = new FEDMPPRO();
                $template->pro();
            }
        }

        /**
         * @param $request
         */
        public function update($request)
        {
            $validate = new FED_Validation();
            $validate->name('Template')->value($request['template'])->required();

            if ( ! $validate->isSuccess()) {
                $errors = implode('<br>', $validate->getErrors());
                wp_send_json_error(array('message' => $errors));
            }
            $settings                                   = get_option('fed_payment_settings');
            $settings['invoice']['template']['default'] = fed_sanitize_text_field($request['template']);
            update_option('fed_payment_settings', $settings);

            wp_send_json_success(array('message' => __('Invoice Template Successfully Updated')));

        }
    }

}