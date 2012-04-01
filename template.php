<?php

/**
 * Copyright (c) Martin Nicholls, 2012
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1 - Redistributions of source code must retain the above copyright notice, this
 *     list of conditions and the following disclaimer.
 *
 * 2 - Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

class templateException extends Exception {}

class template {

	private static $assigned = array();

	private static $paths = array();

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

	public static function display($template) {
		// loop through the set paths LIFO
		$template_file = self::find_template($template);

		if($template_file != true) {
			// template couldn't be found in the paths list - return false
			$paths = implode(", ", array_reverse(self::$paths));
			throw new templateException("Template Not Found in search");
		}

		//debug toy.
		//$_name = htmlspecialchars($template_file, ENT_QUOTES);
		//echo "<div class='ele-info'>{$_name}</div>";

		include($template_file);
		return true;
	}

	private static function find_template($template) {
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
		return false;
	}

	public static function fetch($template) {
		ob_start();
		self::display($template);
		return ob_get_clean();
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
		if(self::$assigned($name)) {
			// it's set, so output the variable
			return self::$assigned[$name];
		}
		// the name isn't set so return false
		return false;
	}
}
