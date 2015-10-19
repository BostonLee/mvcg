<?php

DEFINED('SL')           OR DEFINE('SL', '/');
DEFINED('PATH')         OR DEFINE('PATH', realpath(dirname(__FILE__)));
DEFINED('APP_PATH')     OR DEFINE('APP_PATH', PATH . SL . 'app');
DEFINED('CONTROLLER_PATH')  OR DEFINE('CONTROLLER_PATH', APP_PATH . SL . 'controllers');
DEFINED('VIEW_PATH')        OR DEFINE('VIEW_PATH', APP_PATH . SL . 'views');
DEFINED('MODEL_PATH')       OR DEFINE('MODEL_PATH', APP_PATH . SL . 'models');

/** @noinspection PhpUndefinedClassInspection */
class CVM
{
    protected $Name;
    function __construct($Name)
    {
        $Name = strtolower($Name);
        $this->generate_controller(ucfirst($Name));
        $this->generate_view($Name);
        $this->generate_model(ucfirst($Name));
    }
    function generate_controller($Name)
    {
        $add = "Controller";
        $path = CONTROLLER_PATH. SL. $Name . $add .".php";
        if(!file_exists($path)) {
            $Controller = fopen($path, "w") or die("Unable to open file!");
            $content = "<?php \n class " . $Name . "_" . $add . " {\n \t function indexAction(){\n\n \t } \n }";
            fwrite($Controller, $content);
            fclose($Controller);
            echo "Created controller: " . CONTROLLER_PATH . SL . $Name . $add . ".php<br/>";
        }
        else
        {
            echo "Controller Exist!<br/>";
        }
    }
    function generate_view($Name)
    {
        $add = "View";
        mkdir(VIEW_PATH . SL . $Name, 0700);
        $path = VIEW_PATH . SL . $Name . SL . 'index' . $add . ".php";
        if(!file_exists($path)) {
            $View = fopen($path, "w") or die("Unable to open file!");
            $content = "";
            fwrite($View, $content);
            fclose($View);
            echo "Created view: " . VIEW_PATH . SL . $Name . SL . 'index' . $add . ".php<br/>";
        }
        else {
            echo "View Exist!<br/>";
        }

    }
    function generate_model($Name)
    {
        $add = "Model";
        $path = MODEL_PATH. SL. $Name . $add .".php";
        if(!file_exists($path)) {
            $Model = fopen($path, "w") or die("Unable to open file!");
            $content = "<?php \n class " . $Name . "_" . $add . " {\n \t function get_data(){\n\n \t } \n }";
            fwrite($Model, $content);
            fclose($Model);
            echo "Created model: " . MODEL_PATH . SL . $Name . $add . ".php<br/>";
        }
        else {
            echo "Model Exist!<br/>";
        }
    }
}

if ($_SERVER['REQUEST_METHOD']== "POST") {
    if($_POST['password'] == 12345) {
        $call = new CVM($_POST['controller']);
        echo "Done!";
    }
    else
    {
        echo "Password incorrect!";
    }
}
else
{
    echo '<form method="post">
        <input type="text" name="password" placeholder="password for CVM add">
        <input type="text" name="controller" placeholder="controller name">
        <input type="submit" value="add">
</form>';
}