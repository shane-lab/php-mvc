 <?php
/**
 * The MVC view class, loads the html and assigns additional page parameters
 *
 * @author Shane van den Bogaard
 */
class View
{
    
    /**
     * @var assoc array The page parameters
     */
    private $_parameters = array();
    
    /**
     * @var string The page to load
     */
    private $_view_file;
    
    /**
     * @var string The wrapper html file for the view, default layout
     */
    private $_template_file;
    
    public function __construct($path)
    {
        $this->_view_file = $path;
    }
    
    public function __destruct()
    {
    }
    
    /**
     * Set the page parameters
     *
     * @param string The array key
     * @param object The array value
     * @return this
     */
    public function set_template($template)
    {
        $this->_template_file = $template;
        
        return $this;
    }
    
    /**
     * Set the page parameters
     *
     * @param string The array key
     * @param object The array value
     * @return this
     */
    public function set($key, $value)
    {
        $this->_parameters[$key] = $value;
        
        return $this;
    }
    
    /**
     * Get the page parameter by key
     *
     * @param string The array key
     * @return object The array value
     */
    public function get($key)
    {
        return array_key_exists($key, $this->_parameters) ? $this->_parameters[$key] : null;
    }
    
    /**
     * Renders the template
     */
    public function save_render()
    {
        try {
            $this->render();
        }
        catch (Exception $e) {
            die('View file is missing.');
        }
    }
    
    public function render()
    {
        if ($this->_view_file == null || $this->_view_file != null && !is_readable($this->_view_file)) {
            // view not found
            throw new Exception('View not found');
        } else {
            extract($this->_parameters);
            
            ob_start(); // enable output buffer
            if ($this->_template_file != null && is_readable($this->_template_file)) {
            require($this->_template_file);
        } else {
            require($this->_view_file);
        }
            
            echo ob_get_clean(); // release template content
        }
    }
} 