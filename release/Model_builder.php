<?php
require_once 'BuilderInterface.php';
class Model_builder implements BuilderInterface{

    private $config;
    private $content;
    public function __construct()
    {
        $this->config = json_decode(file_get_contents('configuration.json'),TRUE);
    }

    public function build(){
        
        foreach($this->config['model'] as $model){
            
            $this->makeView($model);
            $this->makeModel($model);
            

            
        }

    }

    protected function makeView($model){
        $this->addFormOpeningTag($model);
        foreach ($model['field'] as $field) {
            $name = $field[0];
            $type = $field[1];
            $label = $field[2];
            $rule = $field[3];
            $this->content .= $this->addLabel($label,$name);
            $this->content .= $this->addInput($name,$this->getInputType($type),$this->getInputValidationRule($rule));
        }
        $this->content .=$this->addInput('submit', 'submit',false,true);
        $this->addFormClosingTag();
        $this->saveModel($model);
    }

    protected function addFormOpeningTag($model){
        $this->content = "<form method=\"POST\" action=\"/api/{$model['name']}\" >".PHP_EOL;
    }
    protected function addFormClosingTag(){
        $this->content .= "</form>".PHP_EOL;
    }
 
    protected function addLabel(string $label, string $for) :string{
        $label = "  <label for=\"$for\"> $label </label>".PHP_EOL;
        return $label;
    }
   
    protected function addInput(string $name, string $type, bool $required = true, $submit = false) :string{
        $required = $required? 'required' : '';
        $submit = $submit? 'Submit' : '';

        $input = "  <input name=\"{$name}\" id=\"{$name}\" type=\"{$type}\" value=\"{$submit}\" {$required} />".PHP_EOL;

        return $input;
    }

    protected function saveModel($model){
        $root_path = $_SERVER['DOCUMENT_ROOT'];
        $file = "$root_path/release/views/{$model['name']}.php";
        if (!is_dir("$root_path/release/views")) {
            // dir doesn't exist, make it
            mkdir("$root_path/release/views");
          }
        file_put_contents($file, $this->content);
    }

    protected function makeModel($model){
        $model_name = ucfirst($model['name']);
        $content = "
        <?php
        class Model_{$model_name} extends RedBean_SimpleModel {
            
            public function update() {
                ";
        foreach ($model['field'] as $field) {
            $name = $field[0];
            $type = $field[1];
            $rule = $field[3];
            if($rule == 'required'){
                $content .= "
                   if ( \$this->bean->{$name} == ''  )
                   throw new Exception( '{$name} field is required' );
                ";
            }
            if($type == 'string'){
                $content .= "
                   if ( !is_string(\$this->bean->{$name})  )
                   throw new Exception( '{$name} field must be a string' );
                ";
            } 
            else if($type == 'integer'){
                $content .= "
                   if ( !is_integer(\$this->bean->{$name})  )
                   throw new Exception( '{$name} field must be an integer' );
                ";
            }
        }
                

        $content .= "
            }
        }
        ";
        // make Model
        $root_path = $_SERVER['DOCUMENT_ROOT'];
        $file = "$root_path/release/models/$model_name.php";
        if (!is_dir("$root_path/release/models")) {
            // dir doesn't exist, make it
            mkdir("$root_path/release/models");
          }
        file_put_contents($file, $content);
    }

    private function getInputType(string $value) :string{
        $type = '';
        switch ($value) {
            case 'integer':
                $type = 'number';
                break;
            case 'string':
                $type = 'text';
                break;
            
            default:
                break;
        }

        return $type;
    }
    private function getInputValidationRule($value) :bool{
        return $value == 'required'? true : false;
    }
}