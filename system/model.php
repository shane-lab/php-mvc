 <?php
/**
 * The MVC model class, handles raw and dynamic data with automatic getter/(un)setter
 * 
 * @author Shane van den Bogaard
 */
class Model
{
    
    /**
     * @var assoc array All properties of this model
     */
    protected $_properties = array();
    
    /**
     * @var string Database table for this model;
     */
    protected $_model = null;
    
    /**
     * Initializes automatic getter/setter/unsetter/contains properties on this Model
     */
    public function __construct()
    {
        $class_vars = get_object_vars($this);
        foreach ($class_vars as $key => $value) {
            if ($key != '_properties') {
                $key                     = preg_replace('/_/', '', $key, 1); // $key = preg_replace('/^_/', '', $key);
                $this->_properties[$key] = $value;
            }
        }
    }
    
    public function __destruct()
    {
    }
    
    /**
     * Automatic Getter/Setter/Unsetter/Contains functionality on this Model object
     *
     * @param string The method
     * @param assoc array Setter parameters
     */
    public function __call($method, $params = null)
    {
        if (strlen($method) < 5)
            throw new Exception('Method does not exist');
        
        $prefix = substr($method, 0, 4);
        $suffix = strtolower(substr($method, strlen($prefix)));
        
        switch ($prefix) {
            case 'get_':
                if ($this->_contains_property($suffix))
                    return $this->_properties[$suffix];
            case 'set_':
                if ($this->_contains_property($suffix) && count($params) == 1)
                    $this->_put($suffix, $params[0]);
                return $this;
            case 'uns_':
                if ($this->_contains_property($suffix))
                    $this->_properties[$suffix] = null;
                return $this;
            case 'has_':
                return $this->_contains_property($suffix);
        }
    }
    
    /** 
     * Checks whether or not they passed in key exists in the properties array
     *
     * @param string Key in properties array
     * @return bool Returns true if key exists in the properties array
     */
    private function _contains_property($property)
    {
        return array_key_exists($property, $this->_properties);
    }
    
    /**
     * Set the properties' key-value pair
     *
     * @param string The property name
     * @param object The property value
     */
    private function _put($key, $value)
    {
        $_key    = "_$key";
        $new_key = property_exists($this, $key) ? $key : property_exists($this, $_key) ? $_key : null;
        
        if ($new_key != null) {
            $this->$new_key          = $value;
            $this->_properties[$key] = $value;
        }
    }
    
    /**
     * Loads a model from an associative array
     *
     * @param assoc array The fetched data
     * @param string Removes db prefixes, default null
     * @return this
     */
    public function load($data, $prefix = null)
    {
        foreach ($data as $key => $value) {
            if ($prefix != null)
                $key = str_replace($prefix, '', $key);
            
            $method = "set_$key";
            $this->$method($value);
        }
        
        return $this;
    }
    
    /**
     * @todo Save the model to the database
     * 
     * @param string The corresponding table name to save the model to
     */
    public function save($table = null)
    {
    }
    
    /**
     * @todo Execute a query on this model
     *
     * @param string The query to execute
     */
    public function query($sql)
    {
    }
    
    /** 
     * Convert this object as array, used for fixing the nested json object
     *
     * @return assoc array Returns this object as array
     */
    public function as_array()
    {
        $stringified = (string) $this;
        return string_to_array($stringified);
    }
    
    public function escape_string($string)
    {
    }
    
    public function escape_array($array)
    {
    }
    
    /**
     * Converts the passed in object to boolean
     *
     * @return bool Returns the passed in object as a boolean
     */
    public function to_bool($val)
    {
        return !!$val;
    }
    
    /**
     * Converts any object to string
     *
     * @param object Any object
     * @return string Returns the __toString of the object
     */
    public function to_string($val = null)
    {
        return (string) $val;
    }
    
    /**
     * Creates a string of the properties
     *
     * @example key=>value,key=>value,key=>valu...
     * @return string Returns the model's properties array as string
     */
    public function __toString()
    {
        return array_to_string($this->_properties);
    }
    
    /**
     * Converts the passed in object to date
     *
     * @param object Any object
     * @return date Date format Years Months Days
     */
    public function to_date($val)
    {
        return date('Y-m-d', $val);
    }
    
    /**
     * Converts the passed in object to timestamp
     *
     * @param object Any object
     * @return date Date format Hours Minutes Seconds
     */
    public function to_time($val)
    {
        return date('H:i:s', $val);
    }
    
    /**
     * Converts the passed in object to date with timestamp
     *
     * @param object Any object
     * @return bool Date format Years Months Days Hours Minutes Seconds
     */
    public function to_datetime($val)
    {
        return date('Y-m-d H:i:s', $val);
    }
} 