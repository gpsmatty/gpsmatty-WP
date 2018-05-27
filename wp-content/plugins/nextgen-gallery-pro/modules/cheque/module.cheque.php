<?php
/**
{
    Module: photocrati-cheque
}
 **/
class M_Photocrati_cheque extends C_Base_Module
{
    function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
    {
        parent::define(
            'photocrati-cheque',
            'Pay by cheque',
            'Allows users to pay by mail with a cheque',
            '2.6.0',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-pro/',
            'Imagely',
            'https://www.imagely.com'
        );

        C_Photocrati_Installer::add_handler($this->module_id, 'C_Cheque_Installer');
    }

    function _register_adapters()
    {
        if (!is_admin())
        {
            $this->get_registry()->add_adapter('I_NextGen_Pro_Checkout', 'A_Cheque_Checkout_Button');
            $this->get_registry()->add_adapter('I_Ajax_Controller',      'A_Cheque_Checkout_Ajax');
        }

    }

    function _register_hooks()
    {
        add_filter('ngg_order_details', array($this, 'add_cheque_reminder_to_order_details'), 10, 2);
        add_filter('ngg_pro_settings_reset_installers', array($this, 'return_own_installer'));

        // enable "Verify cheque payment" bulk action
        if (strpos($_SERVER['SCRIPT_NAME'], '/wp-admin/edit.php') !== FALSE
        &&  isset($_REQUEST['post_type'])
        &&  $_REQUEST['post_type'] == 'ngg_order'
        &&  C_NextGen_Settings::get_instance()->ecommerce_cheque_enable)
        {
            add_action('admin_footer-edit.php', array($this, 'order_bulk_actions'));
            add_action('load-edit.php', array($this, 'process_cheque_bulk_actions'));
            add_action('admin_notices', array($this, 'cheque_bulk_action_notices'));
        }
    }

    /**
     * Adds additional bulk actions to the orders list. Despite how awful the code to this may appear
     * it is THE only way to add additional bulk actions to Wordpress.
     */
    function order_bulk_actions()
    {
        global $post_type;
        if ($post_type == 'ngg_order')
        { ?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('<option>').val('verify_cheques').text('<?php _e('Verify check payment', 'nextgen-gallery-pro')?>').appendTo("select[name='action']");
                    jQuery('<option>').val('verify_cheques').text('<?php _e('Verify check payment', 'nextgen-gallery-pro')?>').appendTo("select[name='action2']");
                });
            </script>
        <?php }
    }

    /**
     * Processes 'verify cheque payment' bulk action
     */
    function process_cheque_bulk_actions()
    {
        global $typenow;
        if ($typenow !== 'ngg_order')
            return;

        if (empty($_REQUEST['post']))
            return;

        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();

        $ids = array_map('intval', $_REQUEST['post']);
        if (empty($ids))
            return;

        $url = remove_query_arg(array('verify_cheques'), wp_get_referer());
        if (!$url)
            $url = admin_url('edit.php?post_type=ngg_order');
        $url = add_query_arg('paged', $wp_list_table->get_pagenum(), $url);

        switch ($action) {
            case 'verify_cheques':
                $checkout = new C_NextGen_Pro_Checkout();
                $verified = 0;
                foreach ($ids as $post_id) {
                    $order = C_Order_Mapper::get_instance()->find($post_id, TRUE);
                    if ($order->status !== 'unverified' || $order->payment_gateway !== 'cheque')
                        continue;
                    $order->status = 'verified';
                    if ($order->save())
                    {
                        $verified++;
                        $checkout->send_email_receipt($order->hash);
                    }
                }

                setcookie('ngg_pro_verified_cheques', (int)$verified, time() + 18000, ADMIN_COOKIE_PATH, COOKIE_DOMAIN);
                wp_redirect($url);
                throw new E_Clean_Exit;
            default:
                return;
        }
    }

    /**
     * Prints notification that orders have been verified
     */
    function cheque_bulk_action_notices()
    {
        global $post_type;

        if (empty($_COOKIE['ngg_pro_verified_cheques']))
            return;

        if ($post_type == 'ngg_order')
        {
            setcookie('ngg_pro_verified_cheques', 0, time() - 3600, ADMIN_COOKIE_PATH, COOKIE_DOMAIN);
            $message = sprintf(
                _n(
                    'Order payment verified',
                    '%s orders payment verified',
                    (int)$_COOKIE['ngg_pro_verified_cheques']
                ),
                number_format_i18n((int)$_COOKIE['ngg_pro_verified_cheques'])
            );
            echo "<div class='updated'><p>{$message}</p></div>";
        }
    }

    function add_cheque_reminder_to_order_details($text, $order)
    {
        if ($order->status == 'unverified'
        &&  $order->payment_gateway == 'cheque'
        &&  !empty(C_NextGen_Settings::get_instance()->ecommerce_cheque_instructions))
        {
            $text .= C_NextGen_Settings::get_instance()->ecommerce_cheque_instructions;
        }
        return $text;
    }

    public function return_own_installer($installers)
    {
        $installers[] = 'C_Cheque_Installer';
        return $installers;
    }

    function get_type_list()
    {
        return array(
            'A_Cheque_Checkout_Button' => 'adapter.cheque_checkout_button.php',
            'A_Cheque_Checkout_Ajax'   => 'adapter.cheque_checkout_ajax.php'
        );
    }
}

class C_Cheque_Installer extends AC_NextGen_Pro_Settings_Installer
{
    function __construct()
    {
        $this->set_defaults(array(
            'ecommerce_cheque_enable' => '0',
            'ecommerce_cheque_instructions' => __("<p>Thanks very much for your purchase! We'll be in touch shortly via email to confirm your order and to provide details on payment.</p>", 'nextgen-gallery-pro')
        ));

        $this->set_groups(array('ecommerce'));
    }
}

new M_Photocrati_cheque;
