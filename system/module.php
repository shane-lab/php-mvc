 <?php
/**
 * 
 *
 * @author Shane van den Bogaard
 */
class Module
{
    
    private $_module_path;
    
    private $_models_path;
    
    private $_controller;
    
    public function __construct($options)
    {
        $this->_module_path = $options->module_path;
        $this->_models_path = $options->models_path;
    }
    
    public function load_controller_path($controller)
    {
        $clazz = null;
        
        $path = $this->_controller_path($controller);
        if ($path == null || $path != null && !is_readable($path)) {
            // controller not found
            throw new NotFoundException('Controller not found');
        }
        
        $this->_controller = strtolower($controller);
        
        return $path;
    }
    
    public function invoke($path, $action, $parameters)
    {
        $clazz      = null;
        $controller = $this->_controller;
        
        if ($controller == null) {
            // invalid invoke request
            throw new Exception('Invalid invoke request');
        } else {
            require_once($path);
            
            if (isset($action) && !method_exists($controller, $action)) {
                // method not found
                throw new NotFoundException('Method not found');
            }
            
            $clazz = new $controller($this, $controller, $action);
        }
        
        return die(call_user_func_array(array(
            $clazz,
            $action
        ), array_slice($parameters, 3))); // excludes [0]{module}[1]{controller}[2]{action}
    }
    
    private function _controller_path($controller)
    {
        return $this->_module_path . '/controllers/' . strtolower($controller) . '.php';
    }

    private function _view_path($controller, $action)
    {
        return $this->_module_path . '/views/' . strtolower($controller) . '/' . $action . '_view.php';
    }
    
    /** 
     * Loads a new Model based on the given arguments
     *
     * @param string Name of the Model in application/models folder
     */
    public function get_model($name)
    {
        require_once($this->_models_path . strtolower($name) . '.php');
        
        $model = new $name;
        $model->set_model($name);
        return $model;
    }
    
    /**
     * Loads a new view based on the given arguments
     *
     * @param string Action of the controller corresponding to the view file
     * @return View Returns a new View
     */
    public function get_view($controller, $action)
    {
        $view = new View($this->_view_path($controller, $action));

        return $view;
    }
}

/**
 * Used to catch method and/or controller not found in this module
 *
 * @author Shane van den Bogaard
 */
class NotFoundException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
} 