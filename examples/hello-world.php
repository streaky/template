<?php

require_once("../template.php");

// Not using __DIR__ for < PHP 5.3 in examples.
$path = realpath(dirname(__FILE__));

use github\streaky\template\template as tpl;

// Add the template directory to the search path
tpl::addPath("{$path}/templates/hello-world/");

// Assign some template vars
tpl::assign("page-title", "'hello-world' Example");
tpl::assign("whom", "World");

// Display the template
tpl::display("page-outer.php");
