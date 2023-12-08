<?php
namespace Opencart\Catalog\Controller\Extension\Paymennt\Payment;



class Paymennt extends \Opencart\System\Engine\Controller {
	public function index(): string {
		
		//routing compatibility for backward
		if(isset( $this->request->get['rte'])) {
			if($this->request->get['rte'] == 'hosted') {
				$this->hosted(); 
			}
			if($this->request->get['rte'] == 'confirm') {
				$this->confirm(); 
			}
			if($this->request->get['rte'] == 'charge') {
				$this->charge(); 
			
			}
		}
		$this->load->language('extension/paymennt/payment/paymennt');

		if (isset($this->session->data['payment_method'])) {
			$data['logged'] = $this->customer->isLogged();
			$data['subscription'] = $this->cart->hasSubscription();

			$data['months'] = [];

			foreach (range(1, 12) as $month) {
				$data['months'][] = date('m', mktime(0, 0, 0, $month, 1));
			}

			$data['years'] = [];

			foreach (range(date('Y'), date('Y', strtotime('+10 year'))) as $year) {
				$data['years'][] = $year;
			}

			$data['language'] = $this->config->get('config_language');
			$data['public_key'] = $this->config->get('payment_paymennt_publickey');
			$data['mode'] = $this->config->get('payment_paymennt_envrinoment');

			// Frames
			
			if ($this->config->get('payment_paymennt_paymenttype') == 'frames') {
				
				$this->document->addScript('https://pay.paymennt.com/static/js/paymennt-frames.js?t='.time());
				$this->document->addScript('extension/paymennt/catalog/view/scripts/paymennt_checkout.js?t='.time());
				return $this->load->view('extension/paymennt/payment/frames', $data);
			} else {

				return $this->load->view('extension/paymennt/payment/hosted', $data);
			}
		}

		return '';
	}
	
	public function checkPaymentStatus() {
		$this->load->model('extension/paymennt/payment/paymennt');
		$checkout_id = '';
		if(isset( $this->request->get['checkout'])) {
			$checkout_id = $this->request->get['checkout'];
			return $this->model_extension_paymennt_payment_paymennt->getPaymentInfo($checkout_id);
	

		} else 
		{
			return '';
		}

	}

	public function confirm(): void {
		$this->load->language('extension/paymennt/payment/paymennt');
		$json = [];
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} else {
			$reference = $this->request->get['reference'];
			$reference = explode('-',$reference);
			if(isset($reference[0]))
				$order_id = $reference[0];
			else
				$order_id = 0;
		}
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
		if (!$order_info) {
			$json['error']['warning'] = $this->language->get('error_order');
		}

		if (!$json) {

			//$response = $this->config->get('payment_paymennt_response');

			$response = $this->checkPaymentStatus();
			
			// Set Payment response
			if (!empty($response)) {
				if($response->status == 'PAID') {
					$this->model_checkout_order->addHistory($order_id, $this->config->get('payment_paymennt_approved_status_id'), '', true);
					$redirect_url = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'), true);
				} else {
					$this->model_checkout_order->addHistory($order_id, $this->config->get('payment_paymennt_failed_status_id'), '', true);
					$redirect_url = $this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true);
				}
			} else {
				$this->model_checkout_order->addHistory($order_id, $this->config->get('payment_paymennt_failed_status_id'), '', true);
				$redirect_url = $this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true);
			}
		}
		$this->response->redirect($redirect_url);
	}

	public function hosted(): void {
		$this->load->language('extension/paymennt/payment/paymennt');

		$json = [];

		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} else {
			$order_id = 0;
		}

		if (isset($this->session->data['payment_method']['code'])) {
			$payment = explode('.', $this->session->data['payment_method']['code']);
		} else {
			$payment[] = $this->session->data['payment_method'];
		}
		

		if (isset($payment[0])) {
			$payment_method = $payment[0];
		} else {
			$payment_method = '';
		}



		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if (!$order_info) {
			$json['error']['warning'] = $this->language->get('error_order');
		}

		$this->load->model('extension/paymennt/payment/paymennt');

		$checkout = $this->model_extension_paymennt_payment_paymennt->createWebCheckout($this->customer,$order_info);
		$web_checkout_url = $checkout->redirectUrl;
		if(!empty($web_checkout_url )) {
			$json['redirect'] = $web_checkout_url;
		} else {
			$this->model_checkout_order->addHistory($this->session->data['order_id'], $this->config->get('payment_paymennt_failed_status_id'), '', true);
			$json['redirect'] = $this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function charge() {
		error_reporting(E_ALL ^ E_WARNING); 
		//charge logic will go here
		$this->load->model('checkout/order');
		
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} else {
			$order_id = 0;
		}

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if (!$order_info) {
			$json['error']['warning'] = $this->language->get('error_order');
		}
		$this->load->model('extension/paymennt/payment/paymennt');
		$token = $this->request->post['pptoken'];
		$checkout = $this->model_extension_paymennt_payment_paymennt->createTokenizedCheckout($this->customer,$order_info,$token);
	
		
		 $checkout_url = $checkout->redirectUrl;
		if(!empty($checkout_url )) {
			 $json['redirect'] = $checkout_url;
		} else {
			$this->model_checkout_order->addHistory($this->session->data['order_id'], $this->config->get('payment_paymennt_failed_status_id'), '', true);
			$json['redirect'] = $this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
