 <?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require('model.php');
require('view.php');
require('controller.php');
require('module.php');
require('router.php');

$config['module_location'] = '/modules/'; // Default module locations
$config['models_location'] = 'undefined'; // Define models location
$config['product_name']    = 'undefined'; // Defines the product/company name
$config['base_url']        = 'undefined'; // Base URL including trailing slash (e.g. http://localhost/)
$config['mail_address']    = 'undefined'; // Sender mail address

$config['db'] = array(
	'enabled' => false,
	'host' => 'undefined',
	'name' => 'undefined',
	'user' => 'undefined',
	'pswd' => 'undefined'
);
?> 