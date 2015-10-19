<?php


DEFINED('SL') OR DEFINE('SL', '/');
DEFINED('PATH') OR DEFINE('PATH', realpath(dirname(__FILE__)));
DEFINED('APP_PATH') OR DEFINE('APP_PATH', PATH);
DEFINED('CONTROLLER_PATH') OR DEFINE('CONTROLLER_PATH', APP_PATH . SL . 'controllers');
DEFINED('VIEW_PATH') OR DEFINE('VIEW_PATH', APP_PATH . SL . 'views');
DEFINED('MODEL_PATH') OR DEFINE('MODEL_PATH', APP_PATH . SL . 'models');

class Action_Generator
{
    function __construct($action, $controller)
    {
        $controller = strtolower($controller);
        $controller = ucfirst($controller);
        if ($this->remove_last($controller, $action)) {
            if ($this->add_to_controller($controller, strtolower($action))) {
                if ($this->add_model($controller, strtolower($action . '_data'))) {
                    $this->add_view(strtolower($controller), strtolower($action));
                }
            }
        }
    }

    function remove_last($controller, $action)
    {
        $lines = file(CONTROLLER_PATH . SL . $controller . 'Controller.php');
        $last = sizeof($lines) - 1;
        if ($lines[$last] == "}") {
            unset($lines[$last]);

            $fp = fopen(CONTROLLER_PATH . SL . $controller . 'Controller.php', 'w');
            fwrite($fp, implode('', $lines));
            fclose($fp);
            return true;
        } else {
            echo "LAST STING IN " . CONTROLLER_PATH . SL . $controller . 'Controller.php' . " IS NOT '}'";
        }
    }

    function add_to_controller($controller, $action)
    {
        $View = fopen(CONTROLLER_PATH . SL . $controller . 'Controller.php', "a") or die("Unable to open file!");
        $content = "
    function " . $action . "Action()
    {
        Action_Helper_Selection_Db::call(self::\$Model,'" . $action . "_data');
    }
}";
        fwrite($View, $content);
        fclose($View);
        return true;
    }

    function add_model($controller, $action)
    {
        $lines = file(MODEL_PATH . SL . $controller . 'Model.php');
        $last = sizeof($lines) - 1;
        if ($lines[$last] == "}") {
            unset($lines[$last]);
            $fp = fopen(MODEL_PATH . SL . $controller . 'Model.php', 'w');
            fwrite($fp, implode('', $lines));
            fclose($fp);


            $View = fopen(MODEL_PATH . SL . $controller . 'Model.php', "a") or die("Unable to open file!");
            $content = "
    function " . $action . "()
    {
       //return Model::sql_wrap(null,null);
    }
}";
            fwrite($View, $content);
            fclose($View);
            return true;

        }
    }

    function add_view($controller, $action)
    {
        $View = fopen(VIEW_PATH . SL . $controller . SL . $action .'View.php', "w") or die("Unable to open file!");
        $content = "";
        fwrite($View, $content);
        fclose($View);
    }
    function backup($file1,$file2,$file3,$step)
    {

    }
}
if ($_SERVER['REQUEST_METHOD']== "POST") {
    if ($_POST['password'] == 12345) {
        $create = new Action_Generator($_POST['action'], $_POST['controller']);
    }
    else
    {
        echo "Password incorrect!";
    }
}
else
{
    echo '<form method="post">
        <input type="text" name="password" placeholder="password for AG add">
        <input type="text" name="controller" placeholder="controller name exist">
         <input type="text" name="action" placeholder="action name new">
        <input type="submit" value="add">
</form>';
}

