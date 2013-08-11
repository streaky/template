<?php

namespace streaky\template;

class templateException extends \Exception {}

class template {

	protected static $assigned = array();

	protected static $paths = array();
	
	protected static $widgets = array(); 

	public static function addPath($path) {
		self::$paths[] = $path;
	}

	public static function assign($name, $data) {
		// set assigned[name] with passed data
		self::$assigned[$name] = $data;
	}

	public static function append($name, $data) {
		// check if name is assigned
		if(!isset(self::$assigned[$name])) {
			// it isn't so set name to null
			self::$assigned[$name] = "";
		}
		// append data to name
		self::$assigned[$name] .= $data;
	}

	public static function assigned($name) {
		// check if name is set
		if(isset(self::$assigned[$name])) {
			return true;
		}
		// it's not, return false
		return false;
	}

	public static function display($template, $abs_safe = false) {
		// loop through the set paths LIFO
		$template_file = self::find_template($template, $abs_safe);

		if($template_file != true) {
			// template couldn't be found in the paths list - return false
			throw new templateException("Template Not Found in search");
		}

		//debug toy.
		//$_name = htmlspecialchars($template_file, ENT_QUOTES);
		//echo "<div class='ele-info'>{$_name}</div>";

		include($template_file);
		return true;
	}

	/**
	 * @param string $template Template path
	 * @param bool $abs_safe If true relative and absolute filesystem path will be checked - potentially extremely dangerous, DO NOT allow near user input
	 * @return string|boolean String file path if found or false
	 */
	private static function find_template($template, $abs_safe = false) {
		// reverse the set paths list
		$paths = array_reverse(self::$paths);
		// Run through paths list, LIFO
		foreach ($paths as $path) {
			$test = "{$path}{$template}";

			// check if the template file exists for this path
			if(file_exists($test)) {
				// it exists in the current path so include it and return true
				return $test;
			}
		}
		
		if($abs_safe === true && file_exists($template)) {
			return $template;
		}
		
		return false;
	}

	public static function fetch($template, $abs_safe = false) {
		ob_start();
		self::display($template, $abs_safe);
		return ob_get_clean();
	}

	public static function registerWidget($root, $class) {
		
		if(class_exists($class) && isset($class::$widgets) && is_array($class::$widgets)) {
			foreach($class::$widgets as $id => $call) {
				self::$widgets["{$root}.{$id}"] = "{$class}::{$call}";
			}
			return true;
		}
		
		throw new templateException("Invalid widget handler");
	}
	
	private static function widget($name, $params = array()) {
		if(isset(self::$widgets[$name])) {
			return call_user_func_array(self::$widgets[$name], $params);
		}
		throw new templateException("Unregistered widget called");
	}
	
	public static function e($name) {
		// check if name is set
		if(isset(self::$assigned[$name])) {
			// it's set, so output the variable
			echo self::$assigned[$name];
			// return true
			return true;
		}
		// the name isn't set so return false
		return false;
	}

	public static function s($name) {
		// check if name is set
		if(isset(self::$assigned[$name])) {
			echo htmlspecialchars(self::$assigned[$name], ENT_QUOTES);
			return true;
		}
		// the name isn't set so return false
		return false;
	}

	public static function g($name) {
		// check if name is set
		if(isset(self::$assigned[$name])) {
			// it's set, so output the variable
			return self::$assigned[$name];
		}
		// the name isn't set so return false
		return false;
	}
}
