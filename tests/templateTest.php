<?php

class template_test_rig extends \github\streaky\template\template {
	public static function getPaths() {
		return self::$paths;
	}
	
	public static function clearPaths() {
		self::$paths = array();
	}
}

class templateTest extends \PHPUnit_Framework_TestCase {
	
	public function testAddSearchPath() {
		template_test_rig::clearPaths();
		$this->assertEquals(count(template_test_rig::getPaths()), 0);
		template_test_rig::addPath(__DIR__."/resources/template1/");
		$set = template_test_rig::getPaths();
		$this->assertEquals(1, count($set));
		$this->assertStringEndsWith("/resources/template1/", $set[0]);
	}
	
	public function testAssigned() {
		$this->assertFalse(template_test_rig::assigned("foo"));
		template_test_rig::assign("foo", "bar");
		$this->assertTrue(template_test_rig::assigned("foo"));
	}
	
	public function testGet() {
		$this->assertFalse(template_test_rig::g("doesntexist"));
		$this->assertEquals("bar", template_test_rig::g("foo"));
	}
	
	public function testAppend() {
		template_test_rig::append("foo", "bar");
		$this->assertEquals("barbar", template_test_rig::g("foo"));
	}
	
	public function testSpecialChars() {
		template_test_rig::assign("special", "&\"'<>");
		ob_start();
		$this->assertTrue(template_test_rig::s("special"));
		$res = ob_get_clean();
		$this->assertEquals("&amp;&quot;&#039;&lt;&gt;", $res);
	}
	
	public function testFetch() {
		template_test_rig::addPath(__DIR__."/resources/template2/");
		$set = template_test_rig::getPaths();
		$this->assertEquals(2, count($set));
		$this->assertEquals("template-1:test1", template_test_rig::fetch("test1.php")); 
	}
	
	/**
	 * Test that templates in the search path override each other correctly 
	 */
	public function testSearchOverride() {
		$this->assertEquals("template-2:test2", template_test_rig::fetch("test2.php"));
	}
}