<?php

  class pm_bank_transfer {
    public $id = __CLASS__;
    public $name = 'Bank Transfer';
    public $description = '';
    public $author = 'LiteCart Dev Team';
    public $version = '1.1';
    public $website = 'http://www.litecart.net';
    public $priority = 0;
    
    public function options($items, $subtotal, $tax, $currency_code, $customer) {
    
      if (empty($this->settings['status'])) return;
      
      if (!empty($this->settings['geo_zone_id'])) {
        if (functions::reference_in_geo_zone($this->settings['geo_zone_id'], $customer['country_code'], $customer['zone_code']) != true) return;
      }
      
      $method = array(
        'title' => language::translate(__CLASS__.':title_module', 'Bank Transfer'),
        'options' => array(
          array(
            'id' => 'bank_transfer',
            'icon' => $this->settings['icon'],
            'name' => language::translate(__CLASS__.':title_option_bank_account', 'Bank Account'),
            'description' => strtr(language::translate(__CLASS__.':description_option_bank_account', 'Transfer to bank account IBAN: %iban, BIC/SWIFT: %bic, %bank_name.'), $this->_get_bank_account($currency_code)),
            'fields' => '',
            'cost' => $this->settings['fee'],
            'tax_class_id' => $this->settings['tax_class_id'],
            'confirm' => language::translate(__CLASS__.':title_confirm_order', 'Confirm Order'),
          ),
        )
      );
      
      return $method;
    }
    
    public function pre_check() {
    }
    
    public function transfer($order) {
      return array(
        'action' => '',
        'method' => '',
        'fields' => '',
      );
    }
    
    public function verify($order) {
      
      $order->data['comments'][] = array(
        'text' => strtr(language::translate(__CLASS__.':text_instructions', 'Transfer to bank account IBAN: %iban, BIC/SWIFT: %bic, %bank_name.'), $this->_get_bank_account($order->data['currency_code'])),
        'notify' => true,
      );
      
      return array(
        'order_status_id' => $this->settings['order_status_id'],
        'payment_transaction_id' => '',
        'errors' => '',
      );
    }

    private function _get_bank_account($currency_code) {
      
      $rows = functions::csv_decode($this->settings['bank_accounts']);
      
      foreach ($rows as $row) {
        if ($currency_code == $row['currency_code']) return array(
          '%currency_code' => $row['currency_code'],
          '%iban' => $row['iban'],
          '%bic' => $row['bic'],
          '%bank_name' => $row['bank_name'],
        );
      }
      
      if ($currency_code != 'XXX') {
        return $this->_get_bank_account('XXX');
      }
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
          'key' => 'icon',
          'default_value' => '',
          'title' => language::translate(__CLASS__.':title_icon', 'Icon'),
          'description' => language::translate(__CLASS__.':description_icon', 'Web path of the icon to be displayed.'),
          'function' => 'input()',
        ),
        array(
          'key' => 'fee',
          'default_value' => '0',
          'title' => language::translate(__CLASS__.':title_payment_fee', 'Payment Fee'),
          'description' => language::translate(__CLASS__.':description_payment_fee', 'Adds a payment fee to the order.'),
          'function' => 'int()',
        ),
        array(
          'key' => 'bank_accounts',
          'default_value' => 'currency_code,iban,bic,bank_name' . PHP_EOL
                           . 'XXX,XX11 2222 3333 4444 5555 66,XXXXXXX,ACME',
          'title' => language::translate(__CLASS__.':title_bank_accounts', 'Bank Accounts'),
          'description' => language::translate(__CLASS__.':description_bank_accounts', 'A coma separated list of bank accounts to where the customer should transfer the payment.'),
          'function' => 'mediumtext()',
        ),
        array(
          'key' => 'tax_class_id',
          'default_value' => '',
          'title' => language::translate(__CLASS__.':title_tax_class', 'Tax Class'),
          'description' => language::translate(__CLASS__.':description_tax_class', 'The tax class for the shipping cost.'),
          'function' => 'tax_classes()',
        ),
        array(
          'key' => 'order_status_id',
          'default_value' => '0',
          'title' => language::translate('title_order_status', 'Order Status'),
          'description' => language::translate('modules:description_order_status', 'Give orders made with this payment method the following order status.'),
          'function' => 'order_status()',
        ),
        array(
          'key' => 'geo_zone_id',
          'default_value' => '',
          'title' => language::translate(__CLASS__.':title_geo_zone', 'Geo Zone'),
          'description' => language::translate(__CLASS__.':description_geo_zone', 'Limit this module to the selected geo zone. Otherwise leave blank.'),
          'function' => 'geo_zones()',
        ),
        array(
          'key' => 'priority',
          'default_value' => '0',
          'title' => language::translate('title_priority', 'Priority'),
          'description' => language::translate(__CLASS__.':description_priority', 'Displays this module by the given priority order value.'),
          'function' => 'int()',
        ),
      );
    }
    
    public function install() {}
    
    public function uninstall() {}
  }
    
?>