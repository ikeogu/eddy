<?php


class pm_voguepay{

	public $id = __CLASS__;
    public $name = 'Voguepay';
    public $description = '<img src="https://voguepay.com/images/buttons/make_payment_grey.png" height="90" />';
    public $author = 'Clement Omiwale';
    public $version = '2.1';
    public $website = 'http://www.occonsultng.com';
    public $priority = 0;
    
    public function __construct() {
    }

	 public function after_process() {
    }

    public function options($items, $subtotal, $tax, $currency_code, $customer) {
    
      if (empty($this->settings['status'])) return;
      
      if (!empty($this->settings['geo_zone_id'])) {
        if (functions::reference_in_geo_zone($this->settings['geo_zone_id'], $customer['country_code'], $customer['zone_code']) != true) return;
      }
     
	  if($this->settings['billingmode'] == 1){  
	  
		$voguepayName = "<span style=\"color:red;font-weight:bold\">Please check your billing address details match the details on your card that you are going to use.<span><style>.icon-wrapper{display:none;}#payment-options .option.selected .description{margin-bottom:10px;}#payment-options .option.selected{min-width: 22%;}</style>";
		
	  }else{
	  
	  $voguepayName = "<style>.icon-wrapper{display:none;}#payment-options .option.selected .title{margin-bottom:15px;}#payment-options .option.selected .description{display:none;}#payment-options .option.selected{min-width: 22%;}</style>";
	  }
	  
	 
	 
      $method = array(
        'title' => '<img src="https://voguepay.com/images/buttons/make_payment_grey.png" height="70px" />',
        'description' => '',
        'options' => array(
          array(
            'id' => 'Voguepay',
			'name' => 'Pay Securely with Voguepay',
			'description' => $voguepayName,
            'fields' => '',
			'cost' => '',
			'icon' => '',
			'tax_class_id' => '',
            'confirm' => language::translate(__CLASS__.':title_confirm_order', 'Confirm Order'),
          ),
        )
      );
      return $method;
    }
	
    public function transfer($order) {
      
	  if ($order->data['customer']['different_shipping_address'] == "0"){
	  
	  $shippingFullname = $order->data['customer']['firstname'] . ', '. $order->data['customer']['lastname'];
	  $shippingAddress = $order->data['customer']['address1'];
	  $shippingCity = $order->data['customer']['city'];
	  $shippingPostcode = $order->data['customer']['postcode'];
	  
	  }else{
	  
	  $shippingFullname = $order->data['customer']['shipping_address']['firstname'] . ', ' . $order->data['customer']['shipping_address']['lastname'];
	  $shippingAddress = $order->data['customer']['shipping_address']['address1'];
	  $shippingCity = $order->data['customer']['shipping_address']['city'];
	  $shippingPostcode = $order->data['customer']['shipping_address']['postcode'];
	  }
	  
	  if($this->settings['xmlCollection'] == 0){
	  $description = "";
	  
		foreach ($order->data['items'] as $item) {
		  
		    $description = "Product: " .  $item['name'] . ", Quantity: " . $item['quantity'] . ", Price: " .  $item['price'];
        }
	  
	  $description .= "";
	  $xmlCollection = "";
	  }else{
		$description= "Order created for: " . $order->data['id'];
	   $xmlCollection = "<items>";
	  
		foreach ($order->data['items'] as $item) {
		  
		    $xmlCollection .= "<item><id></id><name>" .  $item['name'] . "</name><description>" .  $item['name'] . "</description><quantity>" . $item['quantity'] . "</quantity><price>" .  $item['price'] . "</price></item>";
        }
	  
	  $xmlCollection .= "</items>";
	   }
	   
	   
	 if($this->settings['billingmode'] == 1){  
	   $hideBilling = true;
	   }else{
	   
	    $hideBilling = "";
	   }
	   
	   	$previous_order_item_query = database::query(
            "select id from ". DB_TABLE_ORDERS ." order by `id` DESC;"
          );
       $previous_order_item = database::fetch($previous_order_item_query);
         
		 
		$orderID = $previous_order_item["id"]+1;
		$developer_code = '5afba8ef4f333';
		$currency = 'NGN';
		
	  $fields = array(
        'v_merchant_id'   => $this->settings['merchantid'],
        'merchant_ref'   => $orderID,
		'total'        => $order->data['payment_due'],
		'memo'   => $description,
		'developer_code' => $developer_code,
		'total'        => number_format($order->data['order_total']['new_1']['value'], 2, '.', ''),
		'cancel_url'   => document::ilink('checkout'),
        'success_url'   => document::ilink('order_process'),
		'hide_billing_details'   => $hideBilling,
		'cur' => $currency,	
		'xml_item_collection'   => $xmlCollection,
      );
      
	$gateway_url = 'https://voguepay.com/pay'; 
       
      return array(
        'action' => $gateway_url,
        'method' => 'post',
        'fields' => $fields,
      );

			
    }
	
	public function verify($order) {
		
	    return array(
        'order_status_id' => $this->settings['order_status_id_complete'],
      );

}
		
    public function receipt($order) {
	
	echo "<h1> Order created: ".$order->data['id']."</h1>";
	
	}
	
	function settings() {
		return array(
		
		 array(
          'key' => 'status',
          'default_value' => '1',
          'title' => language::translate(__CLASS__.':title_status', 'Status'),
          'description' => language::translate(__CLASS__.':description_status', 'Enables or disables the module.'),
          'function' => 'toggle("e/d")',
        ),
        array(
          'key' => 'merchantid',
          'default_value' => '',
          'title' => language::translate(__CLASS__.':title_merchantid', 'Merchant ID'),
          'description' => language::translate(__CLASS__.':description_merchantid', 'Your Voguepay Merchant ID or Enter demo for testing.'),
          'function' => 'input()',
        ),
        array(
          'key' => 'billingmode',
          'default_value' => '0',
          'title' => language::translate('title_billingmode', 'Hide Billing Details'),
          'description' => language::translate('modules:description_billingmode', 'Enable this option to hide the billing details.'),
          'function' => 'toggle("y/n")',
        ),
        array(
          'key' => 'xmlCollection',
          'default_value' => '',
          'title' => language::translate('title_xmlCollection', 'xmlCollection'),
          'description' => language::translate('modules:description_xmlCollection', 'Enable this option to view products in a structured format.'),
          'function' => 'toggle("y/n")',
        ),
		 array(
          'key' => 'order_status_id_complete',
          'default_value' => '3',
          'title' => language::translate(__CLASS__.':title_order_status', 'Order Status') .': '. language::translate(__CLASS__.':title_complete', 'Complete'),
          'description' => language::translate(__CLASS__.':description_order_status_success', 'Give successful orders made with this payment module the following order status.'),
          'function' => 'order_status()',
        ),
        array(
          'key' => 'order_status_id_error',
          'default_value' => '2',
          'title' => language::translate(__CLASS__.':title_order_status', 'Order Status') .': '. language::translate(__CLASS__.':title_error', 'Error'),
          'description' => language::translate(__CLASS__.':description_order_status_error', 'Give failed orders made with this payment module the following order status.'),
          'function' => 'order_status()',
        ),
		array(
          'key' => 'priority',
          'default_value' => '0',
          'title' => language::translate(__CLASS__.':title_priority', 'Priority'),
          'description' => language::translate(__CLASS__.':description_priority', 'Process this module in the given priority order.'),
          'function' => 'int()',
        ),
		);
	}

	public function install() {}
    
    public function uninstall() {}
}



?>