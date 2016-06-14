 <?php
class Index_controller extends Controller
{
    
    function pre_dispatch()
    {
        array_push($this->_public_functions, 'index_action');
        
        parent::pre_dispatch();
    }
    
    function index_action()
    {
        $this->load_view()->set_template(APP_DIR . 'templates/main.php')->save_render();
    }
} 