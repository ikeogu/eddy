class pm_mymodule {
  public $id = __CLASS__;
  public $name = 'My Module';
  public $description = 'Lorem ipsum dolor';
  public $author = 'ACME Corp.';
  public $version = '1.0';
  public $website = 'http://www.litecart.net';
  public $priority = 1;
    
  public function options($items, $subtotal, $tax, $currency_code, $customer) {
    return array(
      'title' => 'My Payment module',
      'options' => array(
        array(
          'id' => 'method1',
          'icon' => 'images/payment/mymodule-method1.png',
          'name' => 'Method 1',
          'description' => 'Select this option for method 1.',
          'fields' => '',
          'cost' => 0,
          'tax_class_id' => 0,
          'confirm' => 'Confirm Order',
        ),
      )
    );
  }
  
  public function pre_check($order) {
  }
  
  public function transfer($order) {
  }
  
  public function verify($order) {
  }
  
  public function after_process($order) {
  }
  
  public function receipt($order) {
  }
  
  function settings() {
    return array(
      array(
        'key' => 'status',
        'default_value' => '0',
        'title' => 'Status',
        'description' => 'Enables or disables the module.',
        'function' => 'toggle("e/d")',
      ),
      array(
        'key' => 'icon',
        'default_value' => 'images/payment/'.__CLASS__.'.png',
        'title' => 'Icon',
        'description' => 'Web path of the icon to be displayed.',
        'function' => 'input()',
      ),
      array(
        'key' => 'order_status_id',
        'default_value' => '0',
        'title' => 'Order Status:',
        'description' => 'Give successful orders made with this payment module the following order status.',
        'function' => 'order_status()',
      ),
      array(
        'key' => 'priority',
        'default_value' => '0',
        'title' => 'Priority',
        'description' => 'Process this module in the given priority order.',
        'function' => 'int()',
      ),
    );
  }
  
  public function install() {
  }
  
  public function uninstall() {
  }
}