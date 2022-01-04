<?php
// make an interface for the Model builder
class Model_builder{

    private $config;

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
        $content = "";
        foreach ($model['field'] as $field) {
            $name = $field[0];
            $type = $field[1];
            $label = $field[2];
            $rule = $field[3];
            $content .= $this->getLabel($label,$name);
            $content .= $this->getInput($name,$this->getInputType($type),$this->getInputValidationRule($rule));

        }
        // make view
        $root_path = $_SERVER['DOCUMENT_ROOT'];
        $file = "$root_path/release/views/{$model['name']}.php";
        if (!is_dir("$root_path/release/views")) {
            // dir doesn't exist, make it
            mkdir("$root_path/release/views");
          }
        file_put_contents($file, $content);
    }
 
    protected function getLabel(string $label, string $for) :string{
        $label = "<label for=\"$for\"> $label </label>".PHP_EOL;
        return $label;
    }
   
    protected function getInput(string $name, string $type, bool $required = TRUE) :string{
        $required = $required? 'required' : '';

        $input = "<input name=\"{$name}\" id=\"{$name}\" type=\"{$type}\" {$required} />".PHP_EOL;

        return $input;
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

    protected function makeModel($model){
        foreach ($model['field'] as $field) {
            $name = $field[0];
            $type = $field[1];
            $label = $field[2];
            $rule = $field[3];
        }
        // capitalize first letter
        $model_name = ucfirst($model['name']);
        $content = "
        <?php
        class Model_{$model_name} extends RedBean_SimpleModel {
            
            public function update() {
                ";
        foreach ($model['field'] as $field) {
            $name = $field[0];
            $type = $field[1];
            $label = $field[2];
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
}