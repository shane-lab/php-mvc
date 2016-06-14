 <?php
class Index_controller extends Controller
{
    
    function pre_dispatch()
    {
        array_push($this->_public_functions, 'index_action', 'sql_action');
        
        parent::pre_dispatch();
    }
    
    function index_action()
    {
        echo "base index";
    }
} 