<?php
class A_Free_Checkout_Ajax extends Mixin
{
    function free_checkout_action()
    {
        $retval = array();
        $items = $this->param('items');
        if (!is_array($items)) {
            return array('error' => __('Your cart is empty', 'nextgen-gallery-pro'));
        }
        $checkout = new C_NextGen_Pro_Checkout();
        $cart = new C_NextGen_Pro_Cart();
        $cart->add_items($items);
        $cart->apply_coupon($this->param('coupon'));
        // Calculate the total
        $use_home_country = intval($this->param('ship_to'));
        $order_total = $cart->get_total($use_home_country);
        if ((double) $cart->get_total($use_home_country) > 0) {
            return array('error' => __('Invalid request', 'nextgen-gallery-pro'));
        }
        // Create the order
        if (!$cart->has_items()) {
            return array('error' => __('Your cart is empty', 'nextgen-gallery-pro'));
        }
        $customer = array('name' => $this->param('customer_name'), 'email' => $this->param('customer_email'));
        if ($cart->has_shippable_items()) {
            $customer['address'] = $this->param('customer_address');
            $customer['city'] = $this->param('customer_city');
            $customer['state'] = $this->param('customer_state');
            $customer['postal'] = $this->param('customer_postal');
            $customer['country'] = $this->param('customer_country');
            if (empty(C_NextGen_Pro_Currencies::$countries[$customer['country']])) {
                return array('error' => __('Invalid country selected, please try again.', 'nextgen-gallery-pro'));
            } else {
                $customer['country'] = C_NextGen_Pro_Currencies::$countries[$customer['country']]['name'];
            }
        }
        // Presently we only do basic field validation: ensure that each field is filled and that
        // the country selected exists in C_NextGen_Pro_Currencies::$countries
        foreach ($customer as $key => $val) {
            if (empty($val)) {
                return array('error' => __('Please fill all fields and try again', 'nextgen-gallery-pro'));
            }
        }
        // Prevent access warnings later
        if (!$cart->has_shippable_items()) {
            $customer['address'] = FALSE;
            $customer['city'] = FALSE;
            $customer['state'] = FALSE;
            $customer['postal'] = FALSE;
            $customer['country'] = FALSE;
        }
        $retval['customer'] = $customer;
        $order = $checkout->create_order($cart->to_array($use_home_country), $customer['name'], $customer['email'], $order_total, 'free', $customer['address'], $customer['city'], $customer['state'], $customer['postal'], $customer['country'], $use_home_country, 'verified');
        $order->gateway_admin_note = __('Order was free; no payment was charged', 'nextgen-gallery-pro');
        C_Order_Mapper::get_instance()->save($order);
        $checkout->send_email_notification($order->hash);
        $checkout->send_email_receipt($order->hash);
        $retval['order'] = $order->hash;
        $retval['redirect'] = $checkout->get_thank_you_page_url($order->hash, TRUE);
        return $retval;
    }
}
class A_Free_Checkout_Button extends Mixin
{
    function get_checkout_buttons()
    {
        $buttons = parent::call_parent('get_checkout_buttons');
        $buttons[] = 'free_checkout';
        return $buttons;
    }
    function enqueue_free_checkout_resources()
    {
        wp_enqueue_script('jquery-placeholder', $this->object->get_static_url('photocrati-nextgen_admin#jquery.placeholder.min.js'), 'jquery', FALSE, FALSE);
        wp_enqueue_script('ngg-free-checkout', $this->object->get_static_url('photocrati-free_gateway#button.js'), array('jquery-placeholder'));
        wp_enqueue_style('ngg-free-checkout', $this->object->get_static_url('photocrati-free_gateway#button.css'));
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->headline = __('Shipping information', 'nextgen-gallery-pro');
        $i18n->button_text = __('Free checkout', 'nextgen-gallery-pro');
        $i18n->button_text_submit = __('Place order', 'nextgen-gallery-pro');
        $i18n->button_text_cancel = __('Cancel', 'nextgen-gallery-pro');
        $i18n->processing_msg = __('Processing...', 'nextgen-gallery-pro');
        $i18n->field_name = __('Name', 'nextgen-gallery-pro');
        $i18n->field_email = __('Email', 'nextgen-gallery-pro');
        $i18n->field_address = __('Address', 'nextgen-gallery-pro');
        $i18n->field_city = __('City', 'nextgen-gallery-pro');
        $i18n->field_state = __('State', 'nextgen-gallery-pro');
        $i18n->field_postal = __('Zip', 'nextgen-gallery-pro');
        $i18n->field_country = __('Country', 'nextgen-gallery-pro');
        return $i18n;
    }
    function _render_free_checkout_button()
    {
        return $this->render_partial('photocrati-free_gateway#button', array('countries' => C_NextGen_Pro_Currencies::$countries, 'i18n' => $this->get_i18n_strings()), TRUE);
    }
}