<?php

require 'vendor/autoload.php';

require 'release/Model_builder.php';
require 'release/Controller_builder.php';

Flight::route('/', function(){
    (new Controller_builder())->build();
    (new Model_builder())->build();
});

Flight::start();