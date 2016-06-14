 <?php
/**
 * 
 *
 * @author Shane van den Bogaard
 */
class Router
{
    
    /**
     * Loads the controller based on the entered url
     *
     * @param assoc array The global configuration
     * @return Controller Returns the requested Controller
     */
    public static function route($config)
    {
        $request_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $script_url  = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
        $module      = '';
        if ($request_url != $script_url)
            $url = trim(preg_replace('/' . str_replace('/', '\/', str_replace('index.php', '', $script_url)) . '/', '', $request_url, 1), '/');
        
        return self::_load_module($config, self::_parameterize($url));
    }
    
    private static function _load_module($config, $options)
    {
        $models_path = $config['models_location'];
        $module_path = self::_get_module_location($config['module_location'], $options->module);
        if ($module_path == null && $options->module == 'default') {
            $module_path = self::_get_base_module_location($option->module);
            $default = true;
        }
        
        $module = null;
        if ($module_path != null) {
            $module = new Module((object) array(
                'module_path' => $module_path,
                'models_path' => $models_path
            ));
        }
        
        $error   = false;
        $default = false;
        if ($module == null) {
            $error = true;
            
            $module_path = self::_get_module_location($config['module_location'], 'default');
            if ($module_path == null) {
                $module_path = self::_get_base_module_location();
                $default     = true;
            }
            
            $options->controller = 'error_controller';
            $options->action = 'index_action';
            $module = new Module((object) array(
                'module_path' => $module_path,
                'models_path' => $models_path
            ));
        }
        
        try {
            if ($module_path = $module->load_controller_path($options->controller)) {
                return $module->invoke($module_path, $options->action, $options->parameters);
            }
        }
        catch (Exception $e) {
            try {
                if ($options != 'default') {
                    $error = true;
                }

                if ($error) {
                    if ($default) {
                        die('error in default');
                    } else {
                        $module_path = $module_path = self::_get_base_module_location();
                    }
                } else {
                    $module_path = self::_get_module_location($config['module_location'], 'default');
                    if ($module_path == null) {
                        $module_path = self::_get_base_module_location();
                    }
                }
                
                $options->controller = 'error_controller';
                $options->action = 'index_action';
                $module = new Module((object) array(
                    'module_path' => $module_path,
                    'models_path' => $models_path
                ));
                
                if ($module_path = $module->load_controller_path($options->controller)) {
                    return $module->invoke($module_path, $options->action, $options->parameters);
                }
            }
            catch (Exception $e) {
                die('An unhandled error occured on the server.' . $e->getMessage());
            }
        }
        
        return false;
    }
    
    private static function _get_module_location($location, $module)
    {
        $module_path = $location . $module;
        
        return !is_readable($module_path) ? null : $module_path;
    }
    
    private static function _get_base_module_location()
    {
        return self::_get_module_location(realpath(dirname(__FILE__)) . '/modules/', 'default');
    }
    
    private static function _parameterize($url = null)
    {
        $module = 'default';
        $controller = 'index';
        $action = 'index';
        $parameters = explode('/', $url == null ? '' : $url);
        
        if (isset($parameters[0]) && $parameters[0] != '')
            $module = strtolower($parameters[0]);
        if (isset($parameters[1]) && $parameters[1] != '')
            $controller = strtolower($parameters[1]);
        if (isset($parameters[2]) && $parameters[2] != '')
            $action = strtolower($parameters[2]);
        
        return (object) array(
            'module' => $module,
            'controller' => $controller . '_controller',
            'action' => $action . '_action',
            'parameters' => $parameters
        );
    }
} 