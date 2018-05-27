<?php
/**
{
	Module: photocrati-stripe
}
**/
class M_Photocrati_Stripe extends C_Base_Module
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
			'photocrati-stripe',
			'Stripe',
			'Provides integration with Stripe payment gateway',
			'2.6.0',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-pro/',
            'Imagely',
            'https://www.imagely.com'
		);

        C_Photocrati_Installer::add_handler($this->module_id, 'C_Stripe_Installer');
	}

	function _register_adapters()
	{
        if (!is_admin())
        {
            $this->get_registry()->add_adapter('I_NextGen_Pro_Checkout', 'A_Stripe_Checkout_Button');
            $this->get_registry()->add_adapter('I_Ajax_Controller',      'A_Stripe_Checkout_Ajax');
        }
	}

    function _register_hooks()
    {
        add_action('init', array(&$this, 'route'));
        add_filter('ngg_pro_settings_reset_installers', array($this, 'return_own_installer'));
    }

    function route()
    {
        if (isset($_REQUEST['ngg_stripe_rtn']) && isset($_REQUEST['order']))
        {
            $checkout = C_NextGen_Pro_Checkout::get_instance();
            $checkout->redirect_to_thank_you_page($_REQUEST['order']);
        }
    }

    public function return_own_installer($installers)
    {
        $installers[] = 'C_Stripe_Installer';
        return $installers;
    }

	function get_type_list()
	{
        return array(
            'A_Stripe_Checkout_Button' => 'adapter.stripe_checkout_button.php',
            'A_Stripe_Checkout_Ajax'   => 'adapter.stripe_checkout_ajax.php'
        );
	}
}

class C_Stripe_Installer extends AC_NextGen_Pro_Settings_Installer
{
    function __construct()
    {
        $this->set_defaults(array(
            'ecommerce_stripe_enable'      => '0',
            'ecommerce_stripe_key_public'  => '',
            'ecommerce_stripe_key_private' => ''
        ));

        $this->set_groups(array('ecommerce'));
    }
}

new M_Photocrati_Stripe;
