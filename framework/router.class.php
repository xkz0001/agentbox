<?php 

/**
 * The router class.
 * 
 * @package framework
 */
class router
{
	/**
     * The directory seperator.
     * 
     * @var string
     * @access private
     */
    private $pathFix;

    /**
     * The base path of the ZenTaoPMS framework.
     *
     * @var string
     * @access private
     */
    private $basePath;

    /**
     * The root directory of the framwork($this->basePath/framework)
     * 
     * @var string
     * @access private
     */
    private $frameRoot;

    /**
     * The root directory of the core library($this->basePath/lib)
     * 
     * @var string
     * @access private
     */
    private $coreLibRoot;

    /**
     * The root directory of the app.
     * 
     * @var string
     * @access private
     */
    private $appRoot;

    /**
     * The root directory of the app library($this->appRoot/lib).
     * 
     * @var string
     * @access private
     */
    private $appLibRoot;

    /**
     * The root directory of temp.
     * 
     * @var string
     * @access private
     */
    private $tmpRoot;

    /**
     * The root directory of cache.
     * 
     * @var string
     * @access private
     */
    private $cacheRoot;

    /**
     * The root directory of log.
     * 
     * @var string
     * @access private
     */
    private $logRoot;

    /**
     * The root directory of config.
     * 
     * @var string
     * @access private
     */
    private $configRoot;

    /**
     * The root directory of module.
     * 
     * @var string
     * @access private
     */
    private $moduleRoot;

    /**
     * The root directory of them.
     * 
     * @var string
     * @access private
     */
    private $themeRoot;

    /**
     * The lang of the client user.
     * 
     * @var string
     * @access private
     */
    private $clientLang;

    /**
     * The theme of the client user.
     * 
     * @var string
     * @access private
     */
    private $clientTheme;

    /**
     * The control object of current module.
     * 
     * @var object
     * @access public
     */
    public $control;

    /**
     * The module name
     * 
     * @var string
     * @access private
     */
    private $moduleName;

    /**
     * The control file of the module current visiting.
     * 
     * @var string
     * @access private
     */
    private $controlFile;

    /**
     * The name of the method current visiting.
     * 
     * @var string
     * @access private
     */
    private $methodName;

    /**
     * The action extension file of current method.
     * 
     * @var string
     * @access private
     */
    private $extActionFile;

    /**
     * The URI.
     * 
     * @var string
     * @access private
     */
    private $URI;

    /**
     * The params passed in through url.
     * 
     * @var array
     * @access private
     */
    private $params;

    /**
     * The view type.
     * 
     * @var string
     * @access private
     */
    private $viewType;

    /**
     * The global $config object.
     * 
     * @var object
     * @access public
     */
    public $config;

    /**
     * The global $lang object.
     * 
     * @var object
     * @access public
     */
    public $lang;

    /**
     * The global $dbh object, the database connection handler.
     * 
     * @var object
     * @access private
     */
    public $dbh;

    /**
     * The slave database handler.
     * 
     * @var object
     * @access private
     */
    public $slaveDBH;

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
     * Create an application.
     * 
     * <code>
     * <?php
     * $demo = router::createApp('demo');
     * ?>
     * or specify the root path of the app. Thus the app and framework can be seperated.
     * <?php
     * $demo = router::createApp('demo', '/home/app/demo');
     * ?>
     * </code>
     * @param string $appName   the name of the app 
     * @param string $appRoot   the root path of the app
     * @param string $className the name of the router class. When extends a child, you should pass in the child router class name.
     * @static
     * @access public
     * @return object   the app object
     */
    public static function createApp($appName = 'demo', $appRoot = '', $className = 'router')
    {
        if(empty($className)) $className = __CLASS__;
        return new $className($appName, $appRoot);
    }
    
    /**
     * The construct function.
     * 
     * Prepare all the paths, classes, super objects and so on.
     * Notice: 
     * 1. You should use the createApp() method to get an instance of the router.
     * 2. If the $appRoot is empty, the framework will comput the appRoot according the $appName
     *
     * @param string $appName   the name of the app 
     * @param string $appRoot   the root path of the app
     * @access protected
     * @return void
     */
    protected function __construct($appName = 'demo', $appRoot = '')
    {
    	$this->connectDB();
		session_start();
    }
    
	/**
     * Load a module.
     *
     * 1. include the control file or the extension action file.
     * 2. create the control object.
     * 3. set the params passed in through url.
     * 4. call the method by call_user_function_array
     * 
     * @access public
     * @return bool|object  if the module object of die.
     */
    public function loadModule($moduleName, $methodName)
    {
	    require_once BASE_PATH . '/modules/user.class.php';
    	if(user::getPriv() < 1){
			$instance = new user();
			$instance -> $methodName();
			return;
		}
		
		$classFile = BASE_PATH . '/modules/' . $moduleName . '.class.php';
		if (file_exists ( $classFile )) {
			require_once ($classFile);
			if (class_exists ($moduleName)) {
				try {
					$instance = new $moduleName();
					
					try {
						$result = $instance->$methodName();
					} catch ( Exception $error ) {
						die ( $error->getMessage () );
					}
				} catch ( Exception $error ) {
					die ( $error->getMessage () );
				}
			} else {
				die ( "Anvalid module for your request was not found" );
			}
		} else {
			die ( "Could not find:$classFile" );
		}

        return $moduleName;
    }
    
    
	/**
     * Connect to database.
     * 
     * @access public
     * @return void
     */
    public function connectDB()
    {
        global $config, $db;
		
        $this->dbh = $db = mysql_connect($config['dbServer'], $config['username'], $config['password']);
        mysql_select_db("agentbox", $db);
        
        
        if (!$db)
		{
			die('Could not connect: ' . mysql_error());
		}

        
    }
}

/**
 * The super object class.
 * 
 * @package framework
 */
class super
{
    /**
     * Construct, set the var scope.
     * 
     * @param   string $scope  the score, can be server, post, get, cookie, session, global
     * @access  public
     * @return  void
     */
    public function __construct($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Set one member value. 
     * 
     * @param   string    the key
     * @param   mixed $value  the value
     * @access  public
     * @return  void
     */
    public function set($key, $value)
    {
        if($this->scope == 'post')
        {
            $_POST[$key] = $value;
        }
        elseif($this->scope == 'get')
        {
            $_GET[$key] = $value;
        }
        elseif($this->scope == 'server')
        {
            $_SERVER[$key] = $value;
        }
        elseif($this->scope == 'cookie')
        {
            $_COOKIE[$key] = $value;
        }
        elseif($this->scope == 'session')
        {
            $_SESSION[$key] = $value;
        }
        elseif($this->scope == 'env')
        {
            $_ENV[$key] = $value;
        }
        elseif($this->scope == 'global')
        {
            $GLOBAL[$key] = $value;
        }
    }

    /**
     * The magic get method.
     * 
     * @param  string $key    the key
     * @access public
     * @return mixed|bool return the value of the key or false.
     */
    public function __get($key)
    {
        if($this->scope == 'post')
        {
            if(isset($_POST[$key])) return $_POST[$key];
            return false;
        }
        elseif($this->scope == 'get')
        {
            if(isset($_GET[$key])) return $_GET[$key];
            return false;
        }
        elseif($this->scope == 'server')
        {
            if(isset($_SERVER[$key])) return $_SERVER[$key];
            $key = strtoupper($key);
            if(isset($_SERVER[$key])) return $_SERVER[$key];
            return false;
        }
        elseif($this->scope == 'cookie')
        {
            if(isset($_COOKIE[$key])) return $_COOKIE[$key];
            return false;
        }
        elseif($this->scope == 'session')
        {
            if(isset($_SESSION[$key])) return $_SESSION[$key];
            return false;
        }
        elseif($this->scope == 'env')
        {
            if(isset($_ENV[$key])) return $_ENV[$key];
            return false;
        }
        elseif($this->scope == 'global')
        {
            if(isset($GLOBALS[$key])) return $GLOBALS[$key];
            return false;
        }
        else
        {
            return false;
        }
    }
}
?>