<?php

require_once("../template.php");

// Not using __DIR__ for < PHP 5.3 in examples.
$path = realpath(dirname(__FILE__));

// Add the template directory to the search path
template::addPath("{$path}/templates/hello-world/");

// Assign some template vars
template::assign("page-title", "'hello-world' Example");
template::assign("whom", "World");

// Display the template
template::display("page-outer.php");
