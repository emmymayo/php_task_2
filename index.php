<?php

require 'vendor/autoload.php';

require 'release/Model_builder.php';

Flight::route('/', function(){
    (new Model_builder())->build();
});

Flight::start();