<?php
class A_Test_Gateway_Checkout_Ajax extends Mixin
{
    function test_gateway_checkout_action()
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
        $cart_array = $cart->to_array($use_home_country);
        // Create the order
        if ($cart->has_items()) {
            // the below address is the USPS headquarters; it's only an example.
            $order = $checkout->create_order($cart_array, __('Test Customer', 'nextgen-gallery-pro'), __('No email', 'nextgen-gallery-pro'), $order_total, 'test_gateway', "475 L'Enfant Plaza SW", 'Washington D.C.', 'MD', '20260-0004');
            $order->status = 'verified';
            $order->use_home_country = $use_home_country;
            $order->gateway_admin_note = __('Payment was successfully made via the Test Gateway, with no further payment action required.');
            C_Order_Mapper::get_instance()->save($order);
            $checkout->send_email_notification($order->hash);
            $retval['order'] = $order->hash;
            $retval['redirect'] = $checkout->get_thank_you_page_url($order->hash, TRUE);
        } else {
            $retval['error'] = __('Your cart is empty', 'nextgen-gallery-pro');
        }
        return $retval;
    }
}
class A_Test_Gateway_Checkout_Button extends Mixin
{
    function get_checkout_buttons()
    {
        $buttons = parent::call_parent('get_checkout_buttons');
        if (C_NextGen_Settings::get_instance()->ecommerce_test_gateway_enable) {
            $buttons[] = 'test_gateway_checkout';
        }
        return $buttons;
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->button_text = __('Place order', 'nextgen-gallery-pro');
        $i18n->processing_msg = __('Processing...', 'nextgen-gallery-pro');
        return $i18n;
    }
    function _render_test_gateway_checkout_button()
    {
        return $this->render_partial('photocrati-test_gateway#button', array('i18n' => $this->get_i18n_strings()), TRUE);
    }
}