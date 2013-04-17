<?php

namespace core;

error_reporting(0);
require 'constants.php';
require 'session.php';
require 'load.php';
require 'controller.php';
require 'model.php';
require '/controllers/dispatcher.php';
require 'database.php';

date_default_timezone_set('America/New_York'); 

new Dispatcher(); 
