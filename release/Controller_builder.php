<?php
require_once 'BuilderInterface.php';

class Controller_builder implements BuilderInterface{

    private $config;
    private $content;

    public function __construct()
    {
        $this->config = json_decode(file_get_contents('configuration.json'),TRUE);
        $this->content = "";
    }

    public function build(){
        
        foreach($this->config['model'] as $model){
            $this->makeController($model);   
        }
        $this->makeViewFile();
    }

    protected function makeController($model){
         // capitalize first letter
         $model_name = ucfirst($model['name']);
         $lowercase_model_name = strtolower($model_name);
         $this->content .= "Flight::route('/api/$lowercase_model_name' ,function() {
        \${$lowercase_model_name} = R::dispense('{$lowercase_model_name}s');".PHP_EOL;
        foreach ($model['field'] as $field) {
            $name = $field[0];
            $this->content .= "     \${$lowercase_model_name}->{$name} = Flight::request()->data->{$name};".PHP_EOL  ;         
        }

         $this->content .= "\$id = R::store('$lowercase_model_name');
        });".PHP_EOL;

         // make Controller
         
    }

    protected function makeViewFile(){
        $root_path = $_SERVER['DOCUMENT_ROOT'];
         $file = "$root_path/release/controller.php";
         
         file_put_contents($file, $this->content);
    }
}