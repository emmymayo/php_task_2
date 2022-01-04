<?php
require_once 'BuilderInterface.php';

class Controller_builder implements BuilderInterface{

    private $config;

    public function __construct()
    {
        $this->config = json_decode(file_get_contents('configuration.json'),TRUE);
    }

    public function build(){
        
        foreach($this->config['model'] as $model){
            $this->makeController($model);   
        }

    }

    protected function makeController($model){
         // capitalize first letter
         $model_name = ucfirst($model['name']);
         $lowercase_model_name = strtolower($model_name);
         $content = "
         <?php
         class {$model_name}Controller{
             
             public function store() {
                \${$lowercase_model_name} = R::dispense('{$lowercase_model_name}s');
                 ".PHP_EOL;
        foreach ($model['field'] as $field) {
            $name = $field[0];
            $content .= "     \${$lowercase_model_name}->{$name} = Flight::request()->data->{$name};".PHP_EOL  ;         
        }

         $content .= "
                  \$id = R::store('$lowercase_model_name');
                }
            }
            ";

         // make Controller
         $root_path = $_SERVER['DOCUMENT_ROOT'];
         $file = "$root_path/release/controllers/{$model_name}Controller.php";
         if (!is_dir("$root_path/release/controllers")) {
             // dir doesn't exist, make it
             mkdir("$root_path/release/controllers");
           }
         file_put_contents($file, $content);
    }
}