<?php
namespace Opencart\Catalog\Model\Extension\Paymennt\Payment;

require_once __DIR__ . '/../../../includes/paymennt-php/vendor/autoload.php';
use Paymennt\PaymenntClient as PaymenntClient;

class Paymennt extends \Opencart\System\Engine\Model {
	
	public function getPaymenntClient() {
		$api_key = $this->config->get('payment_paymennt_apikey');
		$api_secret = $this->config->get('payment_paymennt_apisecret');
		$public_key = $this->config->get('payment_paymennt_publickey');
		$payment_type = $this->config->get('payment_paymennt_paymenttype');
		$test_env = $this->config->get('payment_paymennt_envrinoment');
		$test = true;
		if($test_env == 1) 
			$test = false;
		$client = new \Paymennt\PaymenntClient($api_key, $api_secret);
		$client->useTestEnvironment($test);
		return $client;
	}
	
	public function getMethods(array $address): array {
		$this->load->language('extension/paymennt/payment/paymennt');
		if (!$this->config->get('config_checkout_payment_address')) {
			$status = true;
		} elseif (!$this->config->get('payment_paymennt_geo_zone_id')) {
			$status = true;
		} else {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_paymennt_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

			if ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}
		}

		$method_data = [];

		if ($status) {
			$option_data = [];

			$option_data['credit_card'] = [
				'code' => 'paymennt.credit_card',
				'name' => $this->language->get('text_card_use'),
				//'title' => $this->language->get('heading_title')
			];

			// $results = $this->getCreditCards($this->customer->getId());

			// foreach ($results as $result) {
			// 	$option_data[$result['credit_card_id']] = [
			// 		'code' => 'credit_card.' . $result['credit_card_id'],
			// 		'name' => $this->language->get('text_card_use') . ' ' . $result['card_number']
			// 	];
			// }

			$method_data = [
				'code'       => 'paymennt',
				'name'       => $this->language->get('heading_title'),
				//'title' => $this->language->get('heading_title'),
				'option'     => $option_data,
				'sort_order' => $this->config->get('payment_paymennt_sort_order')
			];
		}

		return $method_data;
	}
	public function getMethod(array $address): array {
		$this->load->language('extension/paymennt/payment/paymennt');
		if (!$this->config->get('config_checkout_payment_address')) {
			$status = true;
		} elseif (!$this->config->get('payment_paymennt_geo_zone_id')) {
			$status = true;
		} else {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_paymennt_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

			if ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}
		}

		$method_data = [];

		if ($status) {
			$option_data = [];

			$option_data['credit_card'] = [
				'code' => 'paymennt.credit_card',
				'title' => $this->language->get('text_card_use')
			];

			// $results = $this->getCreditCards($this->customer->getId());

			// foreach ($results as $result) {
			// 	$option_data[$result['credit_card_id']] = [
			// 		'code' => 'credit_card.' . $result['credit_card_id'],
			// 		'name' => $this->language->get('text_card_use') . ' ' . $result['card_number']
			// 	];
			// }

			$method_data = [
				'code'       => 'paymennt',
				'title' => $this->language->get('text_card_use'),
				'option'     => $option_data,
				'sort_order' => $this->config->get('payment_paymennt_sort_order')
			];
		}

		return $method_data;
	}
	public function getPaymentInfo($checkout_id) {
		$client = $this->getPaymenntClient();
		return $client->getCheckout($checkout_id);


	}
	
	public function createWebCheckout($customer,$order_info) {
		$client = $this->getPaymenntClient();
		$request = $this->createPaymentRequest($customer, $order_info);
		$checkout = $client->createWebCheckout($request);
		return $checkout;

	}
	public function createTokenizedCheckout($customer,$order_info,$token) {

		$client = $this->getPaymenntClient();
		$checkout_details = $this->createPaymentRequest($customer, $order_info);
		$request = new  \Paymennt\payment\CreatePaymentRequest("TOKEN", $token,
                                                       "", $checkout_details);
		$checkout = $client->createPayment($request);
		return $checkout;
	} 

	public function createPaymentRequest($customer, $order_info) {
		//print_r($order_info);
		$this->load->model('localisation/country');
		//echo $country_name = $this->model_localisation_country->getCountryCode($order_info['payment_country_id']);
		//print_r($this->session->data['currency']);
		$this->load->model('checkout/order');
		$request = new \Paymennt\checkout\WebCheckoutRequest();
		$request->requestId = $order_info['order_id'].'-'.time();
		$request->orderId = $order_info['order_id']; 
		
		//print_r($order_info);
		//if(!isset($order_info['currency_code']))
		//	$order_info['currency_code'] = strtolower($order_info['currency']);
		
		$request->currency = $order_info['currency_code']; 
		$request->amount = number_format($order_info['total'],2, '.', ''); 
		//$request->totals = $order_info['total']; 
		$request->returnUrl = $this->url->link("extension/paymennt/payment/paymennt&rte=confirm");
		
		
		$request->customer = new \Paymennt\model\Customer(); // required
		
		if ($order_info['customer_id'] !== '' && $order_info['customer_id'] != 0) {
            $request->customer->reference = $order_info['customer_id'] ; 
        }
		
		$request->customer->firstName = $order_info['firstname']; 
		$request->customer->lastName = $order_info['lastname']; 
		$request->customer->email = $order_info['email']; 
		$request->customer->phone = $order_info['telephone']; 
		
		//print_r($order_info);

		if(!empty($order_info['shipping_address_id']) ) {
			$delivery_name = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
			if(empty(trim($delivery_name)))
				$delivery_name =  $order_info['firstname'] . '' . $order_info['lastname'];

			$request->deliveryAddress = new \Paymennt\model\Address(); // required if shipping is required
			$request->deliveryAddress->name = $delivery_name;
			$request->deliveryAddress->address1 = $order_info['shipping_address_1'];
			$request->deliveryAddress->address2 = $order_info['shipping_address_2'];
			$request->deliveryAddress->city = $order_info['shipping_city'];
			$request->deliveryAddress->state = $order_info['shipping_zone'];
			$request->deliveryAddress->zip = $order_info['shipping_postcode'];
			$request->deliveryAddress->country = $order_info['shipping_iso_code_3'];
		}
		
		$request->billingAddress = new \Paymennt\model\Address(); // required
		$billing_name = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		
		if(empty(trim($billing_name))) {
			$billing_name =  $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];;
			$request->billingAddress->name = $billing_name; 
			$request->billingAddress->address1 = $order_info['shipping_address_1'];
			$request->billingAddress->address2 = $order_info['shipping_address_2'];
			$request->billingAddress->city = $order_info['shipping_city']; 
			$request->billingAddress->state = $order_info['shipping_zone']; 
			$request->billingAddress->zip = $order_info['shipping_postcode']; 
			$request->billingAddress->country = $order_info['shipping_iso_code_3'];
		} else {
			$billing_name = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
			$request->billingAddress->name = $billing_name; 
			$request->billingAddress->address1 = $order_info['payment_address_1'];
			$request->billingAddress->address2 = $order_info['payment_address_2'];
			$request->billingAddress->city = $order_info['payment_city']; 
			$request->billingAddress->state = $order_info['payment_zone']; 
			$request->billingAddress->zip = $order_info['payment_postcode']; 
			$request->billingAddress->country = $order_info['payment_iso_code_3'];
		}
		
		
		



		$request->items = []; // required	
        
        $items = array();
        $i = 0;

        $data = array_change_key_case($this->session->data, CASE_LOWER);
        //echo $data['order_id']; die();
       	//$products = $this->model_checkout_order->getOrderProducts($data['order_id']);
        $products = $this->cart->getProducts();
        foreach ($products as $product) {
        	$request->items[$i] = new \Paymennt\model\Item();
            $request->items[$i]->name = $product['name']; 
			$request->items[$i]->unitprice = $product['price']; 
			$request->items[$i]->sku = $product['model']; 
			$request->items[$i]->quantity = $product['quantity'];
			$request->items[$i]->linetotal = $product['price'] * $product['quantity']; 
            $i++;
        }
        //print_r($request);		
		return $request;
	}

	// public function getCreditCard(int $customer_id, int $credit_card_id) {
	// 	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . 
	// 		"credit_card` WHERE `customer_id` = '" . (int)$customer_id . "' AND `credit_card_id` = '" . (int)$credit_card_id . "'");

	// 	return $query->row;
	// }

	// public function getCreditCards(int $customer_id): array {
	// 	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "credit_card` WHERE `customer_id` = '" . (int)$customer_id . "'");

	// 	return $query->rows;
	// }

	// public function addCreditCard(int $customer_id, array $data): void {
	// 	$this->db->query("INSERT INTO `" . DB_PREFIX . "credit_card` SET `customer_id` = '" . (int)$customer_id . "', `card_name` = '" . $this->db->escape($data['card_name']) . "', `card_number` = '" . $this->db->escape($data['card_number']) . "', `card_expire_month` = '" . $this->db->escape($data['card_expire_month']) . "', `card_expire_year` = '" . $this->db->escape($data['card_expire_year']) . "', `card_cvv` = '" . $this->db->escape($data['card_cvv']) . "', `date_added` = NOW()");
	// }

	// public function deleteCreditCard(int $customer_id, int $credit_card_id): void {
	// 	$this->db->query("DELETE FROM `" . DB_PREFIX . "credit_card` WHERE `customer_id` = '" . (int)$customer_id . "' AND `credit_card_id` = '" . (int)$credit_card_id . "'");
	// }

	public function charge(int $customer_id, int $order_id, float $amount, int $credit_card_id = 0): string {
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "credit_card` SET `customer_id` = '" . (int)$customer_id . "', `card_name` = '" . $this->db->escape($data['card_name']) . "', `card_number` = '" . $this->db->escape($data['card_number']) . "', `card_expire_month` = '" . $this->db->escape($data['card_expire_month']) . "', `card_expire_year` = '" . $this->db->escape($data['card_expire_year']) . "', `card_cvv` = '" . $this->db->escape($data['card_cvv']) . "', `date_added` = NOW()");

		return $this->config->get('payment_paymennt_response');
	}
}
