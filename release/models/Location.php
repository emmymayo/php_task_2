
        <?php
        class Model_Location extends RedBean_SimpleModel {
            public function update() {
                
                   if ( $this->bean->id == ''  )
                   throw new Exception( 'id field is required' );
                
                   if ( !is_integer($this->bean->id)  )
                   throw new Exception( 'id field must be an integer' );
                
                   if ( $this->bean->name == ''  )
                   throw new Exception( 'name field is required' );
                
                   if ( !is_string($this->bean->name)  )
                   throw new Exception( 'name field must be a string' );
                
                   if ( $this->bean->status == ''  )
                   throw new Exception( 'status field is required' );
                
                   if ( !is_integer($this->bean->status)  )
                   throw new Exception( 'status field must be an integer' );
                
            }
        }
        