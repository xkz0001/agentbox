<?php
/**
 * The control class file.
 *
 */

/**
 * The base class of control.
 * 
 * @package framework
 */
class control
{
    /**
     * The global $app object.
     * 
     * @var object
     * @access protected
     */
    protected $app;

    /**
     * The global $config object.
     * 
     * @var object
     * @access protected
     */
    protected $config;
    
    /**
     * The global $dbh object, the database connection handler.
     * 
     * @var object
     * @access protected
     */
    protected $db;

    /**
     * The $post object, used to access the $_POST var.
     * 
     * @var ojbect
     * @access public
     */
    public $post;

    /**
     * The $get object, used to access the $_GET var.
     * 
     * @var ojbect
     * @access public
     */
    public $get;

    /**
     * The $session object, used to access the $_SESSION var.
     * 
     * @var ojbect
     * @access public
     */
    public $session;

    /**
     * The $server object, used to access the $_SERVER var.
     * 
     * @var ojbect
     * @access public
     */
    public $server;

    /**
     * The $cookie object, used to access the $_COOKIE var.
     * 
     * @var ojbect
     * @access public
     */
    public $cookie;

    /**
     * The $global object, used to access the $_GLOBAL var.
     * 
     * @var ojbect
     * @access public
     */
    public $global;

    /**
     * The name of current module.
     * 
     * @var string
     * @access protected
     */
    protected $moduleName;

    /**
     * The vars assigned to the view page.
     * 
     * @var object
     * @access public
     */
    public $smarty; 

    /**
     * The construct function.
     *
     * 1. global the global vars, refer them by the class member such as $this->app.
     * 2. set the pathes of current module, and load it's mode class.
     * 3. auto assign the $lang and $config to the view.
     * 
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        /* Global the globals, and refer them to the class member. */
        global $app, $config, $smarty, $db;
        $this->app        = $app;
        $this->config     = $config;
        $this->smarty	  = $smarty;
        $this->db		  = $db;

//        $this->setModuleName($moduleName);
//        $this->setMethodName($methodName);

//        $this->setSuperVars();
    }

    //-------------------- Model related methods --------------------//

    /* Set the module name. 
     * 
     * @param   string  $moduleName     The module name, if empty, get it from $app.
     * @access  private
     * @return  void
     */
    private function setModuleName($moduleName = '')
    {
        $this->moduleName = $moduleName ? strtolower($moduleName) : $this->app->getModuleName();
    }

    /* Set the method name. 
     * 
     * @param   string  $methodName    The method name, if empty, get it from $app.
     * @access  private
     * @return  void
     */
    private function setMethodName($methodName = '')
    {
        $this->methodName = $methodName ? strtolower($methodName) : $this->app->getMethodName();
    }

    /**
     * Set the super vars.
     * 
     * @access protected
     * @return void
     */
    protected function setSuperVars()
    {
        $this->post    = $this->app->post;
        $this->get     = $this->app->get;
        $this->server  = $this->app->server;
        $this->session = $this->app->session;
        $this->cookie  = $this->app->cookie;
        $this->global  = $this->app->global;
    }

   
}
