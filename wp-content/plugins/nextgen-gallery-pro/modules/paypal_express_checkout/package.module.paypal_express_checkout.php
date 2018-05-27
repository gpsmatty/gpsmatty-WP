<?php
class A_PayPal_Express_Checkout_Ajax extends Mixin
{
    function paypal_express_checkout_action()
    {
        $checkout = C_NextGen_Pro_Checkout::get_instance();
        $response = $checkout->set_express_checkout();
        unset($response['token']);
        // for security reasons
        return $response;
    }
}
class E_NggProPaymentExpressError extends RuntimeException
{
}
class A_PayPal_Express_Checkout_Button extends Mixin
{
    function get_checkout_buttons()
    {
        $buttons = parent::call_parent('get_checkout_buttons');
        if ($this->is_paypal_express_checkout_enabled()) {
            $buttons[] = 'paypal_express_checkout';
        }
        return $buttons;
    }
    function is_paypal_express_checkout_enabled()
    {
        return C_NextGen_Settings::get_instance()->ecommerce_paypal_enable;
    }
    function _render_paypal_express_checkout_button()
    {
        return $this->render_partial('photocrati-paypal_express_checkout#button', array('value' => __('Pay with PayPal', 'nextgen-gallery-pro'), 'processing_msg' => __('Processing...', 'nextgen-gallery-pro')), TRUE);
    }
    function _paypal_request($method, $data)
    {
        $retval = array();
        $settings = C_NextGen_Settings::get_instance();
        // Determine which url to send the requests to
        $url = "https://api-3t.paypal.com/nvp";
        if (defined('NGG_PRO_PAYPAL_LIVE_URL')) {
            $url = NGG_PRO_PAYPAL_LIVE_URL;
        }
        if ($settings->ecommerce_paypal_sandbox) {
            if (defined('NGG_PRO_PAYPAL_SANDBOX_URL')) {
                $url = NGG_PRO_PAYPAL_SANDBOX_URL;
            } else {
                $url = 'https://api-3t.sandbox.paypal.com/nvp';
            }
        }
        // Set standard parameters
        $data['METHOD'] = $method;
        $data['USER'] = $settings->ecommerce_paypal_username;
        $data['PWD'] = $settings->ecommerce_paypal_password;
        $data['SIGNATURE'] = $settings->ecommerce_paypal_signature;
        $data['VERSION'] = 109;
        // Encode the data for the request
        $request = array();
        foreach ($data as $key => $value) {
            $value = urlencode($value);
            $request[] = "{$key}={$value}";
        }
        $request = implode('&', $request);
        // Send the request
        $response = '';
        if (function_exists('curl_exec')) {
            $curl = curl_init($url);
            curl_setopt_array($curl, array(CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_POST => 1, CURLOPT_RETURNTRANSFER => 1, CURLOPT_POSTFIELDS => $request, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_SSL_VERIFYPEER => 1, CURLOPT_FORBID_REUSE => 1, CURLOPT_HTTPHEADER => array('Connection: Close')));
            $response = curl_exec($curl);
        } else {
            $url_info = parse_url($url);
            $header = "POST {$url_info['path']} HTTP/1.1\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "Host: {$url_info['host']}\r\n";
            $header .= "Connection: close\r\n";
            $header .= "Content-Length: " . strlen($request) . "\r\n\r\n";
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
        // Submit request
        $response = wp_remote_post($url, array('httpversion' => '1.1', 'body' => $request));
        // Check the response
        if ($response) {
            foreach (explode('&', urldecode($response['body'])) as $line) {
                $parts = explode('=', $line);
                $key = strtolower(array_shift($parts));
                $value = array_shift($parts);
                $retval[$key] = $value;
            }
        }
        return $retval;
    }
    function finish_paypal_express_order()
    {
        $order = NULL;
        if (isset($_REQUEST['token'])) {
            $response = $this->get_express_checkout_details($_REQUEST['token']);
            if (isset($response['email']) && isset($response['invnum'])) {
                // Fetch the order
                $order_mapper = C_Order_Mapper::get_instance();
                if ($order = $order_mapper->find_by_hash($response['invnum'], TRUE)) {
                    // Ensure that the amount is what it should have been
                    $cart = new C_NextGen_Pro_Cart($order->cart);
                    if ($cart->get_total($order->use_home_country) == $response['paymentrequest_0_amt']) {
                        $order->customer_name = $response['firstname'] . ' ' . $response['lastname'];
                        $order->email = $response['email'];
                        $order->shipping_street_address = $response['paymentrequest_0_shiptostreet'];
                        if (isset($response['paymentrequest_0_shiptostreet2'])) {
                            $order->shipping_address_line = $response['paymentrequest_0_shiptostreet2'];
                        }
                        $order->shipping_city = $response['paymentrequest_0_shiptocity'];
                        $order->shipping_state = $response['paymentrequest_0_shiptostate'];
                        $order->shipping_zip = $response['paymentrequest_0_shiptozip'];
                        $order->shipping_country = $response['paymentrequest_0_shiptocountryname'];
                        if (isset($response['paymentrequest_0_shiptophonenum'])) {
                            $order->shipping_phone = $response['paymentrequest_0_shiptophonenum'];
                        }
                        $order->status = 'verified';
                    } else {
                        $order->Status = 'fraud';
                    }
                    if ($order_mapper->save($order)) {
                        $payer_id = isset($_REQUEST['payerid']) ? $_REQUEST['payerid'] : $_REQUEST['PayerID'];
                        $request_data = $response;
                        unset($request_data['transactionid']);
                        unset($request_data['paymentrequest_0_transactionid']);
                        unset($request_data['paymentrequestinfo_0_transactionid']);
                        $response = $this->do_express_checkout_payment($order->hash, $_REQUEST['token'], $payer_id, $request_data);
                        if (is_array($response) && isset($response['ack']) && in_array($response['ack'], array('Success', 'SuccessWithWarning'))) {
                            if (!isset($order->paypal_data)) {
                                $order->paypal_data = array();
                            }
                            $order->paypal_data = array_merge($order->paypal_data, $response);
                            $order->gateway_admin_note = __('Payment was successfully made via PayPal Express Checkout, with no further payment action required.', 'nextgen-gallery-pro');
                        } else {
                            $order->status = "error";
                            $order->gateway_admin_note = __('Could not complete order at PayPal');
                        }
                        $order_mapper->save($order);
                    } else {
                        throw new E_NggProPaymentExpressError(sprintf(_("Order #%d could not be updated. Invalid order.", $order->ID)));
                    }
                } else {
                    throw new E_NggProPaymentExpressError(sprintf(__("Could not find order reference #%s"), $response['invnum']));
                }
            }
        }
        return $order;
    }
    function do_express_checkout_payment($order_id, $token, $payerid, $set_express_checkout_response)
    {
        $set_express_checkout_response['TOKEN'] = $token;
        $set_express_checkout_response['PAYERID'] = $payerid;
        $set_express_checkout_response['METHOD'] = 'DoExpressCheckoutPayment';
        return $this->_paypal_request('DoExpressCheckoutPayment', $set_express_checkout_response);
    }
    function get_express_checkout_details($token)
    {
        $response = $this->_paypal_request('GetExpressCheckoutDetails', array('TOKEN' => $token));
        return $response;
    }
    function _get_paypal_currency_code()
    {
        $settings = C_NextGen_Settings::get_instance();
        return C_NextGen_Pro_Currencies::$currencies[$settings['ecommerce_currency']]['code'];
    }
    function set_express_checkout()
    {
        $response = array();
        if ($items = $this->param('items')) {
            $checkout = new C_NextGen_Pro_Checkout();
            $cart = new C_NextGen_Pro_Cart();
            $cart->add_items($items);
            $cart->apply_coupon($this->param('coupon'));
            // Calculate the total
            $local = $this->param('ship_to') ? TRUE : FALSE;
            $order_total = $cart->get_total($local);
            if ($order_total <= 0) {
                return array('error' => __('Invalid request', 'nextgen-gallery-pro'));
            }
            // Create the order
            if ($cart->has_items()) {
                // Call to_array() now because the discount_amount field we inspect later
                // is not updated dynamically as items are added, it's only calculated at to_array()
                $cartarray = $cart->to_array($local);
                $order = $checkout->create_order($cartarray, __('PayPal Customer', 'nextgen-gallery-pro'), __('Unknown', 'nextgen-gallery-pro'), $order_total, 'paypal_express');
                $order->status = 'unverified';
                $order->use_home_country = $local;
                // Now that we have an order, we'll send things to PayPal to get a token
                if (C_Order_Mapper::get_instance()->save($order)) {
                    $settings = C_NextGen_Settings::get_instance();
                    $return_url = site_url('/?ngg_ppxc_rtn=1');
                    $cancel_url = site_url('/?ngg_ppxc_ccl=1');
                    $notify_url = site_url('/?ngg_ppxc_nfy=1');
                    $currency = C_NextGen_Pro_Currencies::$currencies[$settings->ecommerce_currency];
                    $item_number = 0;
                    $free_item_count = 0;
                    // Set up request data
                    $data = array(
                        'RETURNURL' => $return_url,
                        'CANCELURL' => $cancel_url,
                        'CALLBACKTIMEOUT' => 6,
                        'NOSHIPPING' => 0,
                        # use 1 if the cart only contains digital downloads
                        'CALLBACKVERSION' => 61.0,
                        'PAYMENTREQUEST_0_NOTIFYURL' => $notify_url,
                        'PAYMENTREQUEST_0_PAYMENTREASON' => 'None',
                        'PAYMENTREQUEST_0_CURRENCYCODE' => $this->_get_paypal_currency_code(),
                        'PAYMENTREQUEST_0_CUSTOM' => $this->object->param('ship_to'),
                        'PAYMENTREQUEST_0_INVNUM' => $order->hash,
                    );
                    if ($settings->paypal_page_style) {
                        $data['PAGESTYLE'] = $settings->paypal_page_style;
                    }
                    // Add items to PayPal cart
                    foreach ($cart->get_items() as $item) {
                        $item_subtotal = round(doubleval(bcmul(intval($item->quantity), $item->price, intval($currency['exponent']) * 2)), $currency['exponent'], PHP_ROUND_HALF_UP);
                        if ($item_subtotal > 0) {
                            $image = $item->image;
                            $image_id = $image->{$image->id_field};
                            $item_id = $item->{$item->id_field};
                            $data['L_PAYMENTREQUEST_0_NAME' . $item_number] = $item->title . ' / ' . $image->alttext;
                            $data['L_PAYMENTREQUEST_0_DESC' . $item_number] = $image->filename;
                            $data['L_PAYMENTREQUEST_0_AMT' . $item_number] = sprintf("%.{$currency['exponent']}f", $item->price);
                            $data['L_PAYMENTREQUEST_0_NUMBER' . $item_number] = "{$image_id}-{$item_id}";
                            $data['L_PAYMENTREQUEST_0_QTY' . $item_number] = intval($item->quantity);
                            $data['L_PAYMENTREQUEST_0_ITEMCATEGORY' . $item_number] = 'Physical';
                            $item_number += 1;
                        } else {
                            $free_item_count += 1;
                        }
                    }
                    if ($free_item_count == 1) {
                        $data['NOTETOBUYER'] = __("Your cart also contains 1 item at no cost", 'nextgen-gallery');
                    }
                    if ($free_item_count > 1) {
                        $data['NOTETOBUYER'] = sprintf(__("Your cart also contains %d items at no cost", 'nextgen-gallery'), $free_item_count);
                    }
                    // Totals, Shipping & Taxes
                    $data['PAYMENTREQUEST_0_SHIPPINGAMT'] = sprintf("%.{$currency['exponent']}f", $cart->get_shipping($local));
                    $data['PAYMENTREQUEST_0_ITEMAMT'] = sprintf("%.{$currency['exponent']}f", $cart->get_undiscounted_subtotal());
                    $data['PAYMENTREQUEST_0_TAXAMT'] = sprintf("%.{$currency['exponent']}f", $cart->get_tax($local));
                    $data['PAYMENTREQUEST_0_SHIPDISCAMT'] = sprintf("%.{$currency['exponent']}f", $cart->get_discount() * -1);
                    $data['PAYMENTREQUEST_0_AMT'] = sprintf("%.{$currency['exponent']}f", $cart->get_total($local));
                    // Submit the PayPal request
                    $response = $this->_paypal_request('SetExpressCheckout', $data);
                    if (isset($response['token'])) {
                        if ($settings->ecommerce_paypal_sandbox) {
                            $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
                        } else {
                            $url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
                        }
                        $response['redirect'] = $url . $response['token'];
                    }
                    if (isset($response['l_longmessage0'])) {
                        $response['error'] = $response['l_longmessage0'];
                    }
                    if (isset($response['ERROR'])) {
                        $response['error'] = $response['ERROR'];
                        unset($response['ERROR']);
                    }
                } else {
                    $response['error'] = __('Could not save order', 'nextgen-gallery-pro');
                }
            } else {
                $response['error'] = __('Your cart is empty', 'nextgen-gallery-pro');
            }
        }
        return $response;
    }
}