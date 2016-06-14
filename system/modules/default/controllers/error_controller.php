 <?php
/**
 * Wrapper class for the page not found(code 404)
 * 
 * @author Shane van den Bogaard
 */
class Error_controller extends Controller
{
    
    function pre_dispatch()
    {
        array_push($this->_public_functions, 'index_action');
        
        parent::pre_dispatch();
    }
    
    function index_action()
    {
        $this->load_view()->save_render();
    }
} 