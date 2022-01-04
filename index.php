<?php

require 'vendor/autoload.php';

require 'release/Model_builder.php';
require 'release/Controller_builder.php';

Flight::route('/', function(){
    
});

Flight::start();