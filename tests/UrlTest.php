<?php

use Jstewmc\Url\Url;

/**
 * A class to test the URL class
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2014 Jack Clayton
 * @license    MIT
 * @since      0.1.0
 */

class UrlTest extends PHPUnit_Framework_TestCase
{
	/* !Providers */
	
	public function notAStringProvider()
	{
		return [
			[null],
			[false],
			[1],
			[1.0],
			// ['foo'],
			[[]],
			[new StdClass()]
		];
	}
	
	public function notAStringOrNullProvider()
	{
		return [
			// [null],
			[false],
			[1],
			[1.0],
			// ['foo'],
			[[]],
			[new StdClass()]
		];
	}
	
	
	/* !Get and set methods */
	
	public function test_setAndGetFragment_setsAndGetsFragment()
	{
		$url = new Url();
		$url->setFragment('foo');
		
		$this->assertEquals('foo', $url->getFragment());
		
		return;
	}
	
	public function test_setAndGetHost_setsAndGetSHost()
	{
		$url = new Url();
		$url->setHost('foo');
		
		$this->assertEquals('foo', $url->getHost());
		
		return;
	}
	
	public function test_setAndGetPassword_setsAndGetsPassword()
	{
		$url = new Url();
		$url->setPassword('foo');
		
		$this->assertEquals('foo', $url->getPassword());
		
		return;
	}
	
	public function test_setAndGetPath_setsAndGetsPath()
	{
		$url = new Url();
		$url->setPath('foo');
		
		$this->assertEquals('foo', $url->getPath());
		
		return;
	}
	
	public function test_setAndGetPort_setsAndGetsPort()
	{
		$url = new Url();
		$url->setPort(123);
		
		$this->assertEquals(123, $url->getPort());
		
		return;
	}
	
	public function test_setAndGetQuery_setsAndGetsQuery()
	{
		$url = new Url();
		$url->setQuery('foo=bar');
		
		$this->assertEquals('foo=bar', $url->getQuery());
		
		return;
	}
	
	public function test_setAndGetScheme_setsAndGetsScheme()
	{
		$url = new Url();
		$url->setScheme('foo');
		
		$this->assertEquals('foo', $url->getScheme());
		
		return;
	}
	
	public function test_setAndGetUsername_setsAndGetsUsername()
	{
		$url = new Url();
		$url->setUsername('foo');
		
		$this->assertEquals('foo', $url->getUsername());
		
		return;
	}
	
	
	/* !__construct() */
	
	/**
	 * construct() should instantiate a bare object if $url is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_construct_constructsObject_ifUrlIsNotAString($url)
	{
		$url = new Url($url);
		
		$this->assertTrue($url instanceof Url);
		$this->assertEmpty($url->getScheme());
		$this->assertEmpty($url->getUsername());
		$this->assertEmpty($url->getPassword());
		$this->assertEmpty($url->getHost());
		$this->assertEmpty($url->getPort());
		$this->assertTrue($url->getPath() instanceof Jstewmc\Url\Path);
		$this->assertTrue($url->getQuery() instanceof Jstewmc\Url\Query);
		$this->assertEmpty($url->getFragment());
		
		return;
	}
	
	/**
	 * construct() should instantiate an object and parse the url string
	 */
	public function test_construct_constructsObject_ifUrlIsAString()
	{
		$url = new Url('http://username:password@example.com:1234/foo/bar/baz?qux=quux&corge=grault#garply');
		
		$this->assertEquals('http', $url->getScheme());
		$this->assertEquals('username', $url->getUsername());
		$this->assertEquals('password', $url->getPassword());
		$this->assertEquals('example.com', $url->getHost());
		$this->assertEquals('1234', $url->getPort());
		$this->assertEquals('foo/bar/baz', $url->getPath());
		$this->assertEquals('qux=quux&corge=grault', $url->getQuery());
		$this->assertEquals('garply', $url->getFragment());
		
		return;
	}
	
	
	/* !__toString() */
	
	/**
	 * toString() should return an empty string if parts do not exist
	 */
	public function test_toString_returnsString_ifPartsDoNotExist()
	{
		$url = new Url();
		
		$expected = '';
		$actual   = (string) $url;
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * toString() should return a string if the parts do exist
	 */
	public function test_toString_returnsString_ifPartsDoExist()
	{
		$url = new Url('http://example.com');
		
		$expected = 'http://example.com';
		$actual   = (string) $url;
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !format() */
	
	/**
	 * format() should throw an InvalidArgumentException if $format is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_format_throwsInvalidArgumentException_ifFormatIsNotAString($format)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$url = new Url();
		$url->format($format);
		
		return;
	}
	
	/**
	 * format() should return a string if $format is 'relative' and zero parts exist
	 */
	public function test_format_returnsString_ifFormatRelativeAndZeroPartsExist()
	{
		$url = new Url();
		
		$expected = '/';
		$actual   = $url->format('relative');
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * format() should return a string if $format is 'relative' and few parts exist
	 */
	public function test_format_returnsString_ifFormatRelativeAndFewPartsExist()
	{
		$url = new Url();
		
		$url
			->setScheme('http')
			->setPath('foo/bar/baz')
			->setFragment('qux');
			
		$expected = '/foo/bar/baz#qux';
		$actual   = $url->format('relative');
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * format() should return a string if $format is 'relative' and many parts exist
	 */
	public function test_format_returnsString_ifFormatRelativeAndManyPartsExist()
	{
		$url = new Url();
		
		$url
			->setScheme('http')
			->setUsername('username')
			->setPassword('password')
			->setHost('example.com')
			->setPort('1234')
			->setPath('foo/bar/baz')
			->setQuery('qux=quux')
			->setFragment('corge');
		
		$expected = '/foo/bar/baz?qux=quux#corge';
		$actual   = $url->format('relative');
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * format() should return string if $format is 'absolute' and zero parts exist
	 */
	public function test_format_returnsString_ifFormatAbsoluteAndZeroPartsExist()
	{
		$url = new Url();
		
		$expected = '';
		$actual   = $url->format('absolute');
		
		return;
	}
	
	/**
	 * format() should return a string if $format is 'absolute' and few parts exist
	 */
	public function test_format_returnsString_ifFormatAbsoluteAndFewPartsExist()
	{
		$url = new Url();
		
		$url
			->setScheme('http')
			->setHost('example.com')
			->setPort('1234')
			->setFragment('corge');
		
		$expected = 'http://example.com:1234#corge';
		$actual   = $url->format();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * format() should return a string if $format is 'absolute' and many parts exist
	 */
	public function test_format_returnsString_ifFormatAbsoluteAndManyPartsExist()
	{
		$url = new Url();
		
		$url
			->setScheme('http')
			->setUsername('username')
			->setPassword('password')
			->setHost('example.com')
			->setPort('1234')
			->setPath('foo/bar/baz')
			->setQuery('qux=quux')
			->setFragment('corge');
		
		$expected = 'http://username:password@example.com:1234/foo/bar/baz?qux=quux#corge';
		$actual   = $url->format();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !parse() */	
	
	/**
	 * parse() should throw an InvalidArgumentException if $url is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_parse_throwsInvalidArgumentException_ifUrlIsNotAString($rurl)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$url = new Url();
		$url->parse($url);
		
		return;
	}
	
	/**
	 * parse() should parse an absolute url
	 */
	public function test_parse_parsesUrl_ifUrlIsAbsolute()
	{
		$url = new Url();
		$url->parse('http://username:password@example.com:1234/foo/bar/baz?qux=quux&corge=grault#garply');
		
		$this->assertEquals('http', $url->getScheme());
		$this->assertEquals('username', $url->getUsername());
		$this->assertEquals('password', $url->getPassword());
		$this->assertEquals('example.com', $url->getHost());
		$this->assertEquals('1234', $url->getPort());
		$this->assertEquals('foo/bar/baz', $url->getPath());
		$this->assertEquals('qux=quux&corge=grault', $url->getQuery());
		$this->assertEquals('garply', $url->getFragment());
		
		return;
	}
	
	/**
	 * parse() should parse an relative url
	 */
	public function test_parse_parsesUrl_ifUrlIsRelative()
	{
		$url = new Url();
		$url->parse('/foo/bar/baz?qux=quux&corge=grault#garply');
		
		$this->assertEquals('foo/bar/baz', $url->getPath());
		$this->assertEquals('qux=quux&corge=grault', $url->getQuery());
		$this->assertEquals('garply', $url->getFragment());
		
		return;
	}
}
