<?php
namespace Opencart\Admin\Controller\Extension\Paymennt\Payment;
class Paymennt extends \Opencart\System\Engine\Controller {
	public function index(): void {
		
		if(count($this->request->post) > 0 ) {
			$this->save();
			die();
		}

		$this->load->language('extension/paymennt/payment/paymennt');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/paymennt/payment/paymennt', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/paymennt/payment/paymennt.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

		$data['payment_paymennt_response'] = $this->config->get('payment_paymennt_response');
		$data['payment_paymennt_envrinoment'] = $this->config->get('payment_paymennt_envrinoment');
		
		$approved_status = 2;
		if($this->config->get('payment_paymennt_approved_status_id') !=  '')
			$approved_status = $this->config->get('payment_paymennt_approved_status_id');

		$failed_status = 10;
		if($this->config->get('payment_paymennt_failed_status_id') !=  '')
			$failed_status = $this->config->get('payment_paymennt_failed_status_id');
		
		$order_status = 1;
		if($this->config->get('payment_paymennt_order_status_id') !=  '')
			$order_status = $this->config->get('payment_paymennt_order_status_id');
		
		$data['payment_paymennt_approved_status_id'] = $approved_status;
		$data['payment_paymennt_failed_status_id'] = $failed_status;

		$data['payment_paymennt_order_status_id'] = $order_status;
		

		$data['payment_paymennt_apikey'] = $this->config->get('payment_paymennt_apikey');
		$data['payment_paymennt_apisecret'] = $this->config->get('payment_paymennt_apisecret');
		$data['payment_paymennt_publickey'] = $this->config->get('payment_paymennt_publickey');
		$data['payment_paymennt_paymenttype'] = $this->config->get('payment_paymennt_paymenttype');
		

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['payment_paymennt_geo_zone_id'] = $this->config->get('payment_paymennt_geo_zone_id');

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['payment_paymennt_status'] = $this->config->get('payment_paymennt_status');
		$data['payment_paymennt_sort_order'] = $this->config->get('payment_paymennt_sort_order');

		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/paymennt/payment/paymennt', $data));
	}

	public function save(): void {
		
		$this->load->language('extension/paymennt/payment/paymennt');
		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/paymennt/payment/paymennt')) {
			$json['error'] = $this->language->get('error_permission');
		}
		
		if (empty($json['error'])) {

			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('payment_paymennt', $this->request->post);
			//echo "I am here";
			$json['success'] = $this->language->get('text_success');
			
		}

		$this->response->addHeader('Content-Type: application/json');
		echo json_encode($json);
		// $this->response->setOutput(json_encode($json));
	}

	public function install(): void {
		//if ($this->user->hasPermission('modify', 'extension/paymennt/payment/paymennt')) {
			$this->load->model('extension/paymennt/payment/paymennt');

			$this->model_extension_paymennt_payment_paymennt->install();
		//}
	}

	public function uninstall(): void {
		//if ($this->user->hasPermission('modify', 'extension/paymennt/payment/paymennt')) {
		 	$this->load->model('extension/paymennt/payment/paymennt');

		 	$this->model_extension_paymennt_payment_paymennt->uninstall();
		 //}
	}

}
