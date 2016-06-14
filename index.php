 <?php
// The server should keep it's session data for at least 1 hour
ini_set('session.gc_maxlifetime', 3600);

// Start the Session
session_start();

// Defines
define('ROOT_DIR', realpath(dirname(__FILE__)) . '/');
define('APP_DIR', ROOT_DIR . '/example/application/');

// External includes
require(ROOT_DIR . 'system/config.php');

global $config;

$config['module_location'] = APP_DIR . 'modules/';
$config['models_location'] = APP_DIR . 'models/';
$config['product_name'] = 'Shane Lab';
$config['base_url'] = 'https://github.com/shane-lab/'; // Base URL including trailing slash (e.g. http://localhost/)
$config['mail_address'] = 'git@shanelab.nl'; // Sender mail address

define('PRODUCT_NAME', $config['product_name']);
define('BASE_URL', $config['base_url']);

// Start mvc
Router::route($config);
?> 