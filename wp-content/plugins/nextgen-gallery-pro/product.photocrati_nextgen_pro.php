<?php

/***
	{
		Product: photocrati-nextgen-pro,
		Depends: { photocrati-nextgen }
	}
***/

class P_Photocrati_NextGen_Pro extends C_Base_Product
{
	static $modules = array();

    function define_modules()
    {
        self::$modules = array(
            'photocrati-nextgen_pro_i18n',
            'photocrati-nextgen_picturefill',
            'photocrati-galleria',
            'photocrati-comments',
            'photocrati-nextgen_pro_slideshow',
            'photocrati-nextgen_pro_horizontal_filmstrip',
            'photocrati-nextgen_pro_thumbnail_grid',
            'photocrati-nextgen_pro_blog_gallery',
            'photocrati-nextgen_pro_film',
            'photocrati-nextgen_pro_masonry',
            'photocrati-nextgen_pro_albums',
            'photocrati-nextgen_pro_mosaic',
            'photocrati-nextgen_pro_sidescroll',
            'photocrati-nextgen_pro_imagebrowser'
        );

        // Add auto-update modules if this is an admin request
        if (is_admin()) {
            self::$modules = array_merge(self::$modules, array(
                'photocrati-auto_update',
                'photocrati-auto_update-admin'
            ));
        }

        // Include modules which depend on Pro Lightbox
        self::$modules = array_merge(self::$modules, array(
			'photocrati-nextgen_pro_lightbox',
            'photocrati-nextgen_pro_ecommerce',
	        'photocrati-coupons',
            'photocrati-paypal_express_checkout',
            'photocrati-paypal_standard',
            'photocrati-stripe',
            'photocrati-test_gateway',
            'photocrati-cheque',
	        'photocrati-free_gateway',
            'photocrati-image_protection',
            'photocrati-nextgen_pro_proofing',
            'photocrati-nextgen_pro_captions',
            'photocrati-nextgen_pro_settings',
        ));
    }

	function define($id = 'pope-product',
                    $name = 'Pope Product',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
	{
		parent::define(
			'photocrati-nextgen-pro',
			'NextGEN Pro',
			'NextGEN Pro',
			NGG_PRO_PLUGIN_VERSION,
			'http://www.nextgen-gallery.com',
			'Imagely',
			'http://www.imagely.com'
		);

		$module_path = path_join(dirname(__FILE__), 'modules');
		$registry = $this->get_registry();
		$registry->set_product_module_path($this->module_id, $module_path);
        $this->define_modules();

		foreach (self::$modules as $module_name) {
		    $registry->load_module($module_name);
        }

        include_once('class.nextgen_pro_installer.php');
		C_Photocrati_Installer::add_handler($this->module_id, 'C_NextGen_Pro_Installer');
	}
}

new P_Photocrati_NextGen_Pro();
