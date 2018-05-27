<?php
class A_PayPal_Standard_Ajax extends Mixin
{
    function paypal_standard_order_action()
    {
        $retval = array();
        if ($items = $this->param('items')) {
            $checkout = new C_NextGen_Pro_Checkout();
            $cart = new C_NextGen_Pro_Cart();
            $cart->add_items($items);
            $cart->apply_coupon($this->param('coupon'));
            // Calculate the total
            $use_home_country = intval($this->param('use_home_country'));
            $order_total = $cart->get_total($use_home_country);
            if ($order_total <= 0) {
                return array('error' => __('Invalid request', 'nextgen-gallery-pro'));
            }
            // Create the order
            if ($cart->has_items()) {
                // Call to_array() now because the discount_amount field we inspect later
                // is not updated dynamically as items are added, it's only calculated at to_array()
                $cartarray = $cart->to_array($use_home_country);
                $order = $checkout->create_order($cartarray, __('PayPal Customer', 'nextgen-gallery-pro'), __('Unknown', 'nextgen-gallery-pro'), $order_total, 'paypal_standard');
                $order->status = 'unverified';
                $order->use_home_country = $use_home_country;
                $order->gateway_admin_note = __('Payment was successfully made via PayPal Standard, with no further payment action required.', 'nextgen-gallery-pro');
                C_Order_Mapper::get_instance()->save($order);
                $retval['order'] = $order->hash;
                $retval['total'] = $order_total;
                if (!empty($cartarray['coupon'])) {
                    $coupon = $cartarray['coupon'];
                    if ($coupon['discount_type'] == 'flat') {
                        $retval['discount_amount_cart'] = $coupon['discount_amount'];
                    } else {
                        if ($coupon['discount_type'] == 'percent') {
                            $retval['discount_rate_cart'] = $coupon['discount_amount'];
                        }
                    }
                }
            } else {
                $retval['error'] = __('Your cart is empty', 'nextgen-gallery-pro');
            }
        }
        return $retval;
    }
}
class A_PayPal_Standard_Button extends Mixin
{
    function get_checkout_buttons()
    {
        $buttons = parent::call_parent('get_checkout_buttons');
        if ($this->is_paypal_standard_enabled()) {
            $buttons[] = 'paypal_standard';
        }
        return $buttons;
    }
    function is_sandbox_mode()
    {
        return C_NextGen_Settings::get_instance()->get('ecommerce_paypal_std_sandbox', TRUE);
    }
    function get_paypal_url()
    {
        return $this->is_sandbox_mode() ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
    }
    function is_paypal_standard_enabled()
    {
        return C_NextGen_Settings::get_instance()->get('ecommerce_paypal_std_enable', FALSE) ? TRUE : FALSE;
    }
    function _get_paypal_currency_code()
    {
        $settings = C_NextGen_Settings::get_instance();
        return C_NextGen_Pro_Currencies::$currencies[$settings['ecommerce_currency']]['code'];
    }
    function _render_paypal_standard_button()
    {
        return $this->render_partial('photocrati-paypal_standard#button', array('value' => __('Pay with PayPal', 'nextgen-gallery-pro'), 'currency' => $this->_get_paypal_currency_code(), 'email' => C_NextGen_Settings::get_instance()->ecommerce_paypal_std_email, 'continue_shopping_url' => $this->get_continue_shopping_url(), 'return_url' => site_url('/?ngg_pstd_rtn=1'), 'notify_url' => site_url('/?ngg_pstd_nfy=1'), 'cancel_url' => site_url('/?ngg_pstd_cnl=1'), 'paypal_url' => $this->get_paypal_url(), 'processing_msg' => __('Processing...', 'nextgen-gallery-pro')), TRUE);
    }
    function is_pdt_enabled()
    {
        return strlen(trim(C_NextGen_Settings::get_instance()->get('ecommerce_paypal_std_pdt_token', ''))) > 1;
    }
    function create_paypal_standard_order()
    {
        $order_mapper = C_Order_Mapper::get_instance();
        if ($order = $order_mapper->find_by_hash($this->param('order'))) {
            $order->paypal_data = $_REQUEST;
            // If PDT is available, use it to verify the order
            if ($this->is_pdt_enabled()) {
                // TODO: Use PDT to verify order
                $order->status = 'verified';
            }
            // Save the order
            $order_mapper->save($order);
            // Redirect the user
            if ($order->status == 'verified') {
                $this->redirect_to_thank_you_page($order);
            } else {
                $this->redirect_to_order_verification_page($order->hash);
            }
        }
    }
    function update_order_status($order, $total, $customer_name, $email, $shipping_street_address, $shipping_city, $shipping_state, $shipping_zip, $shipping_country, $phone)
    {
        $retval = $order;
        // Has fraud been detected?
        $cart = new C_NextGen_Pro_Cart($order->cart);
        if ($cart->get_total($order->use_home_country) == $total) {
            $order->customer_name = $customer_name;
            $order->email = $email;
            $order->shipping_street_address = $shipping_street_address;
            $order->shipping_city = $shipping_city;
            $order->shipping_state = $shipping_state;
            $order->shipping_zip = $shipping_zip;
            $order->shipping_country = $shipping_country;
            $retval = $order;
            $order->status = 'verified';
        } else {
            $order->status = "fraud";
        }
        return $retval;
    }
    function paypal_ipn_listener()
    {
        if (!headers_sent()) {
            header('HTTP/1.1 200 Ok');
        }
        // STEP 1: read POST data
        // Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
        // Instead, read raw POST data from the input stream.
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }
        // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&{$key}={$value}";
        }
        // STEP 2: Validate the IPN
        if (isset($_REQUEST['custom'])) {
            $response = '';
            if (function_exists('curl_exec')) {
                $curl = curl_init($this->get_paypal_url());
                curl_setopt_array($curl, array(CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_POST => 1, CURLOPT_RETURNTRANSFER => 1, CURLOPT_POSTFIELDS => $req, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_SSL_VERIFYPEER => 1, CURLOPT_FORBID_REUSE => 1, CURLOPT_HTTPHEADER => array('Connection: Close')));
                $response = curl_exec($curl);
            } else {
                $url_info = parse_url($this->get_paypal_url());
                $header = "POST {$url_info['path']} HTTP/1.1\r\n";
                $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $header .= "Host: {$url_info['host']}\r\n";
                $header .= "Connection: close\r\n";
                $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
                $fp = fsockopen('ssl://' . $url_info['host'], 443, $errno, $errstr, 30);
                if ($fp) {
                    fputs($fp, $header . $req);
                    while (!feof($fp)) {
                        $res = fgets($fp, 1024);
                        $res = trim($res);
                        //NEW & IMPORTANT
                        $response .= $res;
                    }
                    fclose($fp);
                }
            }
            if ($response) {
                $order_mapper = C_Order_Mapper::get_instance();
                if ($order = $order_mapper->find_by_hash($_REQUEST['custom'], TRUE)) {
                    $order->status = 'unverified';
                    if (stripos($response, 'VERIFIED') === FALSE) {
                        $order->status = "fraud";
                    } else {
                        $order = $this->update_order_status($order, isset($_REQUEST['mc_gross']) ? $_REQUEST['mc_gross'] : 0.0, isset($_REQUEST['first_name']) && isset($_REQUEST['last_name']) ? $_REQUEST['first_name'] . ' ' . $_REQUEST['last_name'] : '', isset($_REQUEST['payer_email']) ? $_REQUEST['payer_email'] : '', isset($_REQUEST['address_street']) ? $_REQUEST['address_street'] : '', isset($_REQUEST['address_city']) ? $_REQUEST['address_city'] : '', isset($_REQUEST['address_state']) ? $_REQUEST['address_state'] : '', isset($_REQUEST['address_zip']) ? $_REQUEST['address_zip'] : '', isset($_REQUEST['address_country']) ? $_REQUEST['address_country'] : '', isset($_REQUEST['contact_phone']) ? $_REQUEST['contact_phone'] : '');
                    }
                    // Save the order
                    $order->save();
                    // Send the e-mail notifications
                    if ($order->status == 'verified') {
                        $this->send_email_notification($order);
                        $this->send_email_receipt($order);
                        $order->sent_emails = TRUE;
                        $order->save();
                    }
                }
            }
        }
        throw new E_Clean_Exit();
    }
}
class A_PayPal_Standard_Form extends Mixin
{
    function _get_field_names()
    {
        $fields = $this->call_parent('_get_field_names');
        $fields[] = 'paypal_std_enable';
        // $fields[] = 'paypal_std_currencies_supported';
        $fields[] = 'paypal_std_sandbox';
        $fields[] = 'paypal_std_email';
        return $fields;
    }
    function enqueue_static_resources()
    {
        $this->call_parent('enqueue_static_resources');
        wp_enqueue_script('ngg_pro_paypal_std_form', $this->get_static_url('photocrati-paypal_standard#form.js'));
    }
    function _render_paypal_std_enable_field()
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->_render_radio_field($model, 'paypal_std_enable', __('Enable PayPal Standard', 'nextgen-gallery-pro'), C_NextGen_Settings::get_instance()->ecommerce_paypal_std_enable, __('Not all currencies are supported by all payment gateways. Please be sure to confirm your desired currency is supported by PayPal', 'nextgen-gallery-pro'));
    }
    function _render_paypal_std_sandbox_field()
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->_render_radio_field($model, 'paypal_std_sandbox', __('Use Sandbox?', 'nextgen-gallery-pro'), C_NextGen_Settings::get_instance()->ecommerce_paypal_std_sandbox, '', !C_NextGen_Settings::get_instance()->ecommerce_paypal_std_enable ? TRUE : FALSE);
    }
    function _render_paypal_std_email_field()
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->_render_text_field($model, 'paypal_std_email', __('Email', 'nextgen-gallery-pro'), C_NextGen_Settings::get_instance()->ecommerce_paypal_std_email, '', !C_NextGen_Settings::get_instance()->ecommerce_paypal_std_enable ? TRUE : FALSE);
    }
    function _render_paypal_std_currencies_supported_field()
    {
        $settings = C_NextGen_Settings::get_instance();
        $currency = C_NextGen_Pro_Currencies::$currencies[$settings->ecommerce_currency];
        $supported = array('CAD', 'EUR', 'GBP', 'USD', 'JPY', 'AUD', 'NZD', 'CHF', 'HKD', 'SGD', 'SEK', 'DKK', 'PLN', 'NOK', 'HUF', 'CZK', 'ILS', 'MXN', 'BRL', 'MYR', 'PHP', 'TWD', 'THB', 'TRY', 'RUB');
        if (!in_array($currency['code'], $supported)) {
            $message = __('PayPal does not support your currently chosen currency', 'nextgen-gallery-pro');
            return "<tr id='tr_ecommerce_paypal_std_currencies_supported'><td colspan='2'>{$message}</td></tr>";
        }
    }
}