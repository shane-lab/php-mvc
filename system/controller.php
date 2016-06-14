 <?php
/**
 * The MVC controller class, handles the view, the models and the page logic
 *
 * @author Shane van den Bogaard
 */
abstract class Controller
{
    
    /**
     * @var Request Contains a collect of the POST and GET parameters
     */
    public $request = null;

    /**
     * @var Mysqli_db The database helper
     */
    public $database = null;
    
    /**
     * @var array Array of public actions
     */
    protected $_public_functions = array();
    
    /**
     * @var Module The active module of this controller
     */
    private $_module = null;
    
    /**
     * Used as an index file of a module
     */
    abstract function index_action();
    
    const HTTP_REQUEST = 'HTTP_X_REQUESTED_WITH';
    
    /**
     * Gets executed before the page is loaded
     * If the action is not in the public actions array, it will force authentication
     */
    public function pre_dispatch()
    {
    }
    
    public function __construct($module, $controller, $action)
    {
        require_once('mysqli.php');
        $this->database = new Mysqli_db();
        $this->_module = $module;
        $this->request = new Request($controller, $action);
        $this->pre_dispatch();
    }
    
    public function load_model($name)
    {
        return $this->_module->get_model($name);
    }
    
    public function load_view()
    {
        return $this->_module->get_view(preg_replace('/_controller$/', '', $this->request->controller), preg_replace('/_action$/', '', $this->request->action));
    }
    
    /** 
     * Checks if the http request is in ajax
     *
     * @return bool Returns true if the call is a xmlhttp(CORS) request
     */
    public function is_cors()
    {
        $http_request = self::HTTP_REQUEST;
        return array_contains_key($_SERVER, $http_request) && $_SERVER[$http_request] == 'XMLHttpRequest';
    }
}

/**
 * Wrapper class used to contain the requests(POST and GET) made to this controller
 *
 * @author Shane van den Bogaard
 */
class Request
{
    /**
     * @var assoc array Assocative array with POST and GET parameters
     */
    private $_parameters = array();
    
    /**
     * @var string The current function name
     */
    public $action = null;
    
    /**
     * @var string The current controller class name
     */
    public $controller = null;
    
    public function __construct($controller, $action)
    {
        $this->controller  = strtolower($controller);
        $this->action      = strtolower($action);
        $this->_parameters = array_merge($_POST, $_GET);
    }
    
    /**
     * Get the value of the key-value pair in the array
     *
     * @param string The array key
     * @return object The array value
     */
    public function get($key)
    {
        return $this->contains($key) ? $this->_parameters[$key] : null;
    }
    
    /**
     * Checks if the array contains a key-value pair
     *
     * @param string The array key
     * @return bool Returns true if the array contains the key
     */
    public function contains($key)
    {
        return isset($this->_parameters[$key]);
    }
    
    /**
     * Checks if the required parameters are set
     *
     * @param array List of required parameter fields
     * @return bool Returns true if the required parameters are set
     */
    public function required($parameters = array())
    {
        $flag = true;
        foreach ($parameters as $parameter) {
            if ($flag)
                $flag = $this->contains($parameter);
        }
        
        return $flag;
    }
} 