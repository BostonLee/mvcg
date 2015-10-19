<?php
DEFINED('SL')           OR DEFINE('SL', '/');
DEFINED('PATH')         OR DEFINE('PATH', realpath(dirname(__FILE__)));
class MVC_GENERATOR
{
    function __construct()
    {
        $this->generate_folder();
        $this->generate_htacess();
        $this->generate_index_main();
        $this->generate_bootstrap();
        $this->generate_config();
        $this->generate_core();
        $this->generate_layout();
        $this->generate_main_mvc();
        $this->generate_controller_helper();
    }
    function wrap($path,$content)
    {
        $file = fopen($path, "w") or die("Unable to open file!");
        fwrite($file, $content);
        fclose($file);
    }
    function generate_folder()
    {
        mkdir(PATH . SL . "app", 0700);
        mkdir(PATH . SL . "app/controllers", 0700);
        mkdir(PATH . SL . "app/models", 0700);
        mkdir(PATH . SL . "app/views", 0700);
        mkdir(PATH . SL . "app/views/layouts", 0700);
        mkdir(PATH . SL . "app/core", 0700);
        mkdir(PATH . SL . "app/actionHelpers", 0700);
        mkdir(PATH . SL . "app/viewHelpers", 0700);
        mkdir(PATH . SL . "app/modules", 0700);
        mkdir(PATH . SL . "docs", 0700);
        mkdir(PATH . SL . "public", 0777);
        mkdir(PATH . SL . "public/js", 0777);
        mkdir(PATH . SL . "public/css", 0777);
        mkdir(PATH . SL . "public/images", 0777);
        mkdir(PATH . SL . "public/fonts", 0777);
        mkdir(PATH . SL . "public/upload", 0777);
    }
    function generate_htacess()
    {
        $content = "AddDefaultCharset utf-8\n
                    RewriteEngine on\n
                    RewriteCond %{REQUEST_FILENAME} !-f\n
                    RewriteCond %{REQUEST_FILENAME} !-d\n
                    RewriteRule ^([^?]*)$ /index.php?path$1 [NC,L,QSA]";
        $this->wrap(".htaccess",$content);


        $content = "Deny All";
        $this->wrap("app/.htaccess",$content);
    }
    function generate_index_main()
    {
        $content = "<?php
class init
{
    function __construct()
    {
        DEFINED('DEBUG')        OR DEFINE('DEBUG', true);

        DEFINED('SL')           OR DEFINE('SL', '/');

        DEFINED('PATH')         OR DEFINE('PATH', realpath(dirname(__FILE__)));
        DEFINED('APP_PATH')     OR DEFINE('APP_PATH', PATH . SL . 'app');
        DEFINED('CORE_PATH')    OR DEFINE('CORE_PATH', APP_PATH . SL . 'core');
        DEFINED('PUBLIC_PATH')  OR DEFINE('PUBLIC_PATH', PATH . SL . 'public');

        DEFINED('PUBLIC_PATH_HTML')  OR DEFINE('PUBLIC_PATH_HTML', SL . 'public');

        DEFINED('CSS_PATH')  OR DEFINE('CSS_PATH', PUBLIC_PATH_HTML . SL . 'css');
        DEFINED('JS_PATH')  OR DEFINE('JS_PATH', PUBLIC_PATH_HTML . SL . 'js');
        DEFINED('IMG_PATH')  OR DEFINE('IMG_PATH', PUBLIC_PATH_HTML . SL . 'images');

        DEFINED('UPLOAD_PATH')  OR DEFINE('UPLOAD_PATH', \$_SERVER['DOCUMENT_ROOT'] . SL . 'public/upload/');

        DEFINED('CONTROLLER_PATH')  OR DEFINE('CONTROLLER_PATH', APP_PATH . SL . 'controllers');
        DEFINED('VIEW_PATH')        OR DEFINE('VIEW_PATH', APP_PATH . SL . 'views');
        DEFINED('MODEL_PATH')       OR DEFINE('MODEL_PATH', APP_PATH . SL . 'models');

        DEFINED('MODULES_PATH')       OR DEFINE('MODULES_PATH', APP_PATH . SL . 'modules');


        DEFINED('DEF_CONTROLLER')  OR DEFINE('DEF_CONTROLLER', CONTROLLER_PATH . SL . 'IndexController.php');
        DEFINED('DEF_VIEW')        OR DEFINE('DEF_VIEW', 'indexView.php');
        DEFINED('DEF_VIEW_PATH')   OR DEFINE('DEF_VIEW_PATH', VIEW_PATH . SL . 'index' . SL . DEF_VIEW);

        DEFINED('LAYOUT')          OR DEFINE('LAYOUT', 'default.php');

        DEFINED('LAYOUT_PATH')     OR DEFINE('LAYOUT_PATH', VIEW_PATH . SL . 'layouts');
        DEFINED('DEF_LAYOUT')      OR DEFINE('DEF_LAYOUT', LAYOUT_PATH . SL . LAYOUT);

        DEFINED('LOGIN_PAGE')      OR DEFINE('LOGIN_PAGE', '/index/login');


        DEFINED('SITE_URL')      OR DEFINE('SITE_URL', 'http://' . \$_SERVER['SERVER_NAME']);

        DEFINED('SHOP_SESSION_INTERVAL_DELETE')      OR DEFINE('SHOP_SESSION_INTERVAL_DELETE', '3');

        global \$config;
        global \$isAdmin;
        \$isAdmin = false;
        global \$titlePage;



        \$config = parse_ini_file(APP_PATH . SL . 'config.ini', 1);
        /** @noinspection PhpIncludeInspection */
        include_once(APP_PATH . SL . 'bootstrap.php');

        Bootstrap::init();
    }
}
\$Init = new init();";
        $this->wrap("index.php",$content);
    }
    function generate_bootstrap()
    {
        $content = "<?php

foreach(glob(APP_PATH. SL .'interfaces/*.php') as \$file)
{
    /** @noinspection PhpIncludeInspection */
    include_once \$file;
}

/** @noinspection PhpIncludeInspection */
include_once(CORE_PATH . SL . 'router.php');
/** @noinspection PhpIncludeInspection */
include_once(CORE_PATH . SL . 'controller.php');
/** @noinspection PhpIncludeInspection */
include_once(CORE_PATH . SL . 'model.php');
/** @noinspection PhpIncludeInspection */
include_once(CORE_PATH . SL . 'view.php');


class Bootstrap
{
    static function init()
    {
        Model::db();
        Model::check_login();
        foreach(glob(APP_PATH. SL .'actionHelpers/*.php') as \$file)
        {
            /** @noinspection PhpIncludeInspection */
            include_once \$file;
        }
        global \$titlePage;
        global \$config;
        \$titlePage = \$config['settings']['title'];
        Route::launch();
    }
}";
        $this->wrap("app/bootstrap.php",$content);
    }
    function generate_config()
    {
        $content =";
;   Connection to dataBase
;
[database]
db_username = name
db_password = password
db_name = db_name
db_host = localhost
db_charset = utf8
[modules]
active_modules =
admin_login_required =
[admin]
login = admin
password = 12345
[settings]
title = my new web site";
        $this->wrap("app/config.ini",$content);
    }
    function generate_core()
    {
        $content = "<?php
class Route
{
    static function launch()
    {

        global \$config;
        global \$isAdmin;
        \$actual_link = \$_SERVER[HTTP_HOST].\$_SERVER[REQUEST_URI];
        \$route = explode('/',\$actual_link);

        \$modules = explode(',',\$config['modules']['active_modules']);
        \$is_module = false;

        foreach(\$modules as \$modul) { if(array_key_exists(1,\$route) && \$route[1] == \$modul && \$route[1] != '') { \$is_module = true; } }

        \$arr_length = count(\$route);
        \$add_value = 0;
        if(\$is_module){ \$add_value = 1; }
        for(\$i=1 - \$add_value;\$i<\$arr_length;\$i++)
        {
            switch(\$i)
            {

                case 0:
                    \$number_value = \$i + 1;
                    DEFINED('MODULES_APP') or DEFINE('MODULES_APP',\$route[\$number_value]);
                    foreach(explode(',',\$config['modules']['admin_login_required']) as \$check)
                    {

                        if(\$check == MODULES_APP && !\$isAdmin && MODULES_APP != '')
                        {
                            header('Location: '. LOGIN_PAGE);
                            die();
                        }
                    }
                    if(\$route[\$number_value + \$add_value] != ''){ DEFINED('CONTROLLER_APP') or DEFINE('CONTROLLER_APP',\$route[\$number_value + \$add_value]); }
                    else {
                        DEFINED('CONTROLLER_APP') or DEFINE('CONTROLLER_APP','index');
                        DEFINED('ACTION_APP') or DEFINE('ACTION_APP','index');
                    }
                    break;

                case 1:
                    \$number_value = \$i;
                    if(\$route[\$number_value + \$add_value + 1] == '') { DEFINED('ACTION_APP') or DEFINE('ACTION_APP','index'); }
                    if(\$route[\$number_value + \$add_value] != ''){ DEFINED('CONTROLLER_APP') or DEFINE('CONTROLLER_APP',\$route[\$number_value + \$add_value]); }
                    else {
                        DEFINED('CONTROLLER_APP') or DEFINE('CONTROLLER_APP','index');
                        DEFINED('ACTION_APP') or DEFINE('ACTION_APP','index');
                    }
                    break;
                case 2:
                    \$number_value = \$i;
                    if(\$route[\$number_value + \$add_value] != ''){ DEFINED('ACTION_APP') or DEFINE('ACTION_APP',\$route[\$number_value + \$add_value]); }
                    else {  DEFINED('ACTION_APP') or DEFINE('ACTION_APP','index'); }
                    break;
                case 3:
                    \$number_value = \$i;
                    if(\$route[\$number_value + \$add_value] != ''){ DEFINED('KEY_APP') or DEFINE('KEY_APP',\$route[\$number_value + \$add_value]); }
                    break;
                case 4:
                    \$number_value = \$i;
                    if(\$route[\$number_value + \$add_value] != ''){ DEFINED('VALUE_APP') or DEFINE('VALUE_APP',\$route[\$number_value + \$add_value]); }
                    break;
            }
        }

        switch(\$add_value)
        {
            case 0:
                if(defined('CONTROLLER_APP')) { self::check_include(CONTROLLER_PATH. SL .ucfirst(CONTROLLER_APP).'Controller.php'); }
                else { self::check_include(DEF_CONTROLLER.''); }
                break;
            case 1:
                if(defined('CONTROLLER_APP')) { self::check_include(MODULES_PATH. SL . MODULES_APP . SL . 'controllers' . SL .ucfirst(CONTROLLER_APP).'Controller.php'); }
                else { self::check_include(MODULES_PATH. SL . MODULES_APP . SL . 'controllers' . SL . 'IndexController.php'); }
                break;
        }

        Model::launch_model();
        Controller::launch_controller();

    }
    static private function check_include(\$inc)
    {
        if( file_exists(\$inc) && is_readable(\$inc)) {
            /** @noinspection PhpIncludeInspection */
            include(\$inc); }
        else { self::not_found(CONTROLLER_APP); }
    }
    static function not_found(\$error)
    {
        if(DEBUG) { echo(\"Page: \$error not found!\"); die(); }
else { self::error404('page:'.\$error); }
}

static function error404(\$error)
{
    header(\"Location: /error#\".\$error);
    die();
}
}";
        $this->wrap("app/core/router.php",$content);

        $content = "<?php
class Controller extends Route
{
    static function launch_controller()
    {
        if(defined('ACTION_APP') && !defined('MODULES_APP'))
        {
            self::check_include(VIEW_PATH . SL . CONTROLLER_APP . SL . ACTION_APP . 'View.php');
        }
        else if(defined('MODULES_APP'))
        {
            self::check_include(MODULES_PATH. SL . MODULES_APP  . SL . 'views' . SL . CONTROLLER_APP . SL . ACTION_APP . 'View.php');
        }
        else
        {
            if(CONTROLLER_APP != 'error')
            {
                self::check_include(DEF_VIEW_PATH.'');
            }
            else
            {
                self::check_include(VIEW_PATH. SL . 'error' . SL . DEF_VIEW);
            }
        }
    }
    static private function check_include(\$inc)
    {
        if (\$_SERVER['REQUEST_METHOD'] === 'GET' && strpos(KEY_APP,'ajax?get') !== false || \$_SERVER['REQUEST_METHOD'] === 'POST' && KEY_APP == 'ajax?post') {

            \$ACTION_APP = '';
            \$classMain = CONTROLLER_APP . '_Controller';
            foreach(explode(\"-\", ACTION_APP) as \$index=>\$spl)
            {
                if(\$index != 0){\$ACTION_APP .= ucfirst(\$spl);}
                else { \$ACTION_APP .= \$spl;}
            }
            \$classMain = CONTROLLER_APP . '_Controller';
            \$functionAction = \$ACTION_APP . 'Action';
            \$classMain = new \$classMain;
            \$classMain->\$functionAction();
        }
        else {
            if (file_exists(\$inc) && is_readable(\$inc)) {
                \$classMain = CONTROLLER_APP . '_Controller';
                \$functionAction = ACTION_APP . 'Action';
                \$classMain = new \$classMain;
                \$classMain->\$functionAction();

                \$view = new View();
                \$view->launch_view(\$inc);
            } else {
                self::not_found(ACTION_APP);
            }
        }
    }
    static function not_found(\$error)
    {
        if(DEBUG) { echo('Action: \"\$error\" not found!');  die(); }
else { Route::error404('action:' . ACTION_APP); }
}
}";
        $this->wrap("app/core/controller.php",$content);

        $content ="<?php
class View extends Route
{
    static function launch_view(\$layout = null)
    {
        global \$customLayout;
        if(DEFINED('MODULES_APP'))
        {
            foreach(glob(MODULES_PATH . SL . MODULES_APP . SL . 'viewHelpers' . SL . '*.php') as \$file)
            {
                /** @noinspection PhpIncludeInspection */
                include_once \$file;
            }

            if(isset(\$customLayout)) {
                \$path = MODULES_PATH . SL . MODULES_APP . SL . 'view/layouts' . SL . \$customLayout . '.php';

                if (file_exists(\$path) && is_readable(\$path)) {
                    /** @noinspection PhpIncludeInspection */
                    include_once(\$path);
                } else {
                    echo 'No Such Layout: ' . \$customLayout . '!';
                }
            }
            else
            {
                if (file_exists(MODULES_PATH . SL . MODULES_APP . SL . 'views/layouts/' . LAYOUT ) && is_readable(MODULES_PATH . SL . MODULES_APP . SL . 'views/layouts/' . LAYOUT )) {
                    /** @noinspection PhpIncludeInspection */
                    include_once(MODULES_PATH . SL . MODULES_APP . SL . 'views/layouts/' . LAYOUT);
                } else {
                    echo 'No Such Layout: ' . MODULES_PATH . SL . MODULES_APP . SL . 'view/layouts/' . LAYOUT . '!';
                }
            }
        }
        else
        {
            foreach(glob(APP_PATH . SL . 'viewHelpers' . SL . '*.php') as \$file)
            {
                /** @noinspection PhpIncludeInspection */
                include_once \$file;
            }

            if(isset(\$customLayout)) {
                \$path = LAYOUT_PATH . SL . \$customLayout . '.php';

                if (file_exists(\$path) && is_readable(\$path)) {
                    /** @noinspection PhpIncludeInspection */
                    include_once(\$path);
                } else {
                    echo 'No Such Layout: ' . \$customLayout . '!';
                }
            }
            else
            {
                if (file_exists(DEF_LAYOUT) && is_readable(DEF_LAYOUT)) {
                    /** @noinspection PhpIncludeInspection */
                    include_once(DEF_LAYOUT);
                } else {
                    echo 'No Such Layout: ' . DEF_LAYOUT . '!';
                }
            }
        }
    }

    static function get_content(\$file)
    {
        global \$Data;
        /** @noinspection PhpIncludeInspection */
        include_once(\$file);
    }

}";
        $this->wrap("app/core/view.php",$content);

        $content = "<?php

class Model extends Route
{
    static function launch_model()
    {
        if (DEFINED(MODULES_APP)) {
            \$inc = MODULES_PATH . SL . MODULES_APP . SL . 'models' . SL . ucfirst(CONTROLLER_APP) . 'Model.php';
            if (file_exists(\$inc) && is_readable(\$inc)) {
                /** @noinspection PhpIncludeInspection */
                include(\$inc);
            }
        } else {
            \$inc = MODEL_PATH . SL . ucfirst(CONTROLLER_APP) . 'Model.php';
            if (file_exists(\$inc) && is_readable(\$inc)) {
                /** @noinspection PhpIncludeInspection */
                include(\$inc);
            }
        }
    }

    static function db()
    {
        global \$config;
        \$data = \$config['database'];
        global \$pdo;
        try {
            \$dsn = \"mysql:host=\" . \$data['db_host'] . \";dbname=\" . \$data['db_name'] . \";charset=\" . \$data['db_charset'];
            \$opt = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            );
            \$pdo = new PDO(\$dsn, \$data['db_username'], \$data['db_password'], \$opt);
        } catch (PDOException \$e) {
            if (!DEBUG) {
                die('Connection fail.');
            } else {
                die('Connection fail: ' . \$e->getMessage());
            }
        }
    }

    static function check_login()
    {
        global \$config;
        global \$isAdmin;

        \$login = \$_POST['Login'];
        \$password = \$_POST['Password'];
        session_start();
        \$data_login = \$config['admin'];


        if (\$login == \$data_login['login'] && \$password == \$data_login['password']) {
            \$_SESSION['admin_login'] = \$login;
            \$_SESSION['admin_password'] = \$password;
        }


        if (isset(\$_SESSION['admin_login']) && isset(\$_SESSION['admin_password'])) {
            if (\$_SESSION['admin_login'] == \$data_login['login'] && \$_SESSION['admin_password'] == \$data_login['password']) {
                \$isAdmin = true;
            } else {
                \$isAdmin = false;
                session_unset();
                session_destroy();
            }
        }
    }



    static function sql_wrap(\$sql, \$array = null)
    {
        global \$pdo;
        \$statement = \$pdo->prepare(\$sql);
        \$statement->execute(\$array);
        if(strpos(\$sql,'SELECT') !== false) {
            \$data = \$statement->fetchAll();

            return \$data;
        }
    }
}";
        $this->wrap("app/core/model.php",$content);
    }
    function generate_layout()
    {
        $content = "<?php global \$isAdmin; global \$titlePage;?>
<?php header('Content-Type:text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html lang=\"en\">

<head>

<title><?php echo \$titlePage ; ?></title>

</head>
<body>
    <?php
    View::get_content(\$layout);
    ?>
</body>

</html>
";
        $this->wrap("app/views/layouts/default.php",$content);
    }
    function generate_main_mvc()
    {
        $content = "<?php
class Index_Controller extends Controller
{
    private static \$Model = 'Index_Model';
    function indexAction()
    {

    }
}";
        $this->wrap("app/controllers/IndexController.php",$content);
        $content = "<?php
class Error_Controller extends Controller
{
    private static \$Model = 'Error_Model';
    function indexAction()
    {

    }
}";
        $this->wrap("app/controllers/ErrorController.php",$content);




        $content = "<?php
class Index_Model extends Model
{
    function get()
    {
    }
}";
        $this->wrap("app/models/IndexModel.php",$content);
        $content = "<?php
class Error_Model extends Model
{
    function get()
    {
    }
}";
        $this->wrap("app/models/ErrorModel.php",$content);

        mkdir(PATH . SL . "app/views/index/", 0700);
        $content = "";
        $this->wrap("app/views/index/indexView.php",$content);

        mkdir(PATH . SL . "app/views/error/", 0700);
        $content = "";
        $this->wrap("app/views/error/indexView.php",$content);
    }

    function generate_controller_helper()
    {
        $content = "<?php

/**
 * ActionHelper : Selection_Db
 *
 * @author   Boston Lee <alexey.sysoev.dev@gmail.com>
 * @version  1.0
 * @access   public
 * (c) 2015 Boston Lee Creative Solutions
 */
class Action_Helper_Selection_Db extends Bootstrap
{

    /**
     * Call db function and return array data
     * action : call
     *
     * @param string \$className class name of model
     * @param string \$funcName function name of model class
     * @param mixed \$value sending data dataBase
     * @return mixed \$Data sample of data from dataBase
     * @access public
     **/
    static function call(\$className, \$funcName, \$value = null)
    {
        global \$Data;
        \$className = new \$className;
        \$Data = \$className->\$funcName(\$value);
        return \$Data;
    }
}
        ";
        $this->wrap("app/actionHelpers/selection_db.php",$content);


        $content = "<?php

class Action_Helper_Special extends Bootstrap
{
    static function checkAjax()
    {
        if(!empty(\$_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(\$_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') { return true;}
        else { Route::error404(\"Page not found\");}
    }
    static function errorDebug(\$result)
    {
        if(\$result != \"\")
        {
            echo json_encode(array('error'=>\$result));
        }
    }
    static function redirect(\$href)
    {
        header(\"Location: \".\$href);
        die();
    }
}";
        $this->wrap("app/actionHelpers/special.php",$content);
    }
}
$go = new MVC_GENERATOR();