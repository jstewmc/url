<?php

use Jstewmc\Url\Query;

/**
 * A class to test the Query class
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2014 Jack Clayton
 * @license    MIT
 * @since      0.1.0
 */
class QueryTest extends PHPUnit_Framework_TestCase
{
	/* !Provider */
	
	public function notAnArrayProvider()
	{
		return [
			[null],
			[false],
			[1],
			[1.0],
			['foo'],
			[new StdClass()]	
		];
	}
	
	public function notAStringProvider()
	{
		return [
			[null],
			[false],
			[1],
			[1.0],
			[[]],
			[new StdClass()]
		];
	}
	
	
	/* !Getter and setters */
	
	public function test_setGetParameters_setsGetsParameters()
	{
		$parameters = ['foo' => 'bar', 'baz' => 'qux'];
		
		$query = new Query();
		$query->setParameters($parameters);
		
		$this->assertEquals($parameters, $query->getParameters());
		
		return;
	}
	
	public function test_setGetSeparator_setsGetsSeparator()
	{
		$separator = ',';
		
		$query = new Query();
		$query->setSeparator($separator);
		
		$this->assertEquals($separator, $query->getSeparator());
		
		return;
	}
	
	
	/* !_construct() */
	
	/**
	 * __construct() should construct object if query does not exist
	 */
	public function test_construct_constructsObject_ifQueryDoesNotExist()
	{
		$query = new Query();
		
		$this->assertTrue($query instanceof Query);
		$this->assertEmpty($query->getParameters());
		
		return;
	}
	
	/**
	 * __construct() should construct object is query does exist
	 */
	public function test_construct_constructsObject_ifQueryDoesExist()
	{
		$query = new Query('foo=bar&baz=qux');
		
		$this->assertTrue($query instanceof Query);
		
		$expected = ['foo' => 'bar', 'baz' => 'qux'];
		$actual   = $query->getParameters();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !__toString() */
	
	/**
	 * __toString() should return an empty string if the query doesn't have parameters
	 */
	public function test_toString_returnsString_ifParametersDoNotExist()
	{
		$query = new Query();
		
		$expected = '';
		$actual   = (string) $query;
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * __toString() should return a string if the query has parameters
	 */
	public function test_toString_returnsString_ifParametersDoExist()
	{
		$query = new Query();
		$query->setParameters(['foo' => 'bar', 'baz' => 'qux']);
		
		$expected = 'foo=bar&baz=qux';
		$actual   = (string) $query;
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !format() */
	
	/**
	 * format() should return an empty string if the query is empty
	 */
	public function test_format_returnsString_ifZeroParameters()
	{
		$query = new Query();
		
		$expected = '';
		$actual   = $query->format();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * format() should return query as string if one parameter
	 */
	public function test_format_returnsString_ifOneParameter()
	{
		$query = new Query();
		$query->setParameter('foo', 'bar');
		
		$expected = 'foo=bar';
		$actual   = $query->format();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * format() should return query as a string if many parameters and no separator is
	 *     specified
	 */
	public function test_format_returnsString_ifManyParametersAndDefaultSeparator()
	{
		$query = new Query();
		$query->setParameter('foo', 'bar');
		$query->setParameter('baz', 'qux');
		$query->setParameter('quux', 'corge');
		
		$expected = 'foo=bar&baz=qux&quux=corge';
		$actual   = $query->format();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * format() should return query as a string if many parameters and separator is
	 *     specified
	 */
	public function test_format_returnsString_ifManyParametersAndNotDefaultSeparator()
	{
		$query = new Query();
		$query->setSeparator('|');
		$query->setParameter('foo', 'bar');
		$query->setParameter('baz', 'qux');
		$query->setParameter('quux', 'corge');
		
		$expected = 'foo=bar|baz=qux|quux=corge';
		$actual   = $query->format();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !getParameter() */
	
	/**
	 * getParameter() should throw an InvalidArgumentException if $key is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_getParameter_throwsInvalidArgumentException_ifKeyIsNotAString($key)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$query = new Query();
		$query->getParameter($key);
		
		return;
	}
	
	/**
	 * getParameter() should throw an OutOfBoundsException if key does not exist
	 */
	public function test_getParameter_throwsOutOfBoundsException_ifKeyDoesNotExist()
	{
		$this->setExpectedException('OutOfBoundsException');
		
		$query = new Query();
		$query->getParameter('foo');
		
		return;
	}
	
	/**
	 * getParameter() should return value if key exists
	 */
	public function test_getParameter_returnsValue_ifKeyDoesExist()
	{
		$query = new Query();
		$query->setParameter('foo', 'bar');
		
		$this->assertEquals('bar', $query->getParameter('foo'));
		
		return;
	}
	
	
	/* !hasParameter() */
	
	/**
	 * hasParameter() should throw an InvalidArgumentException if $key is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_hasParameter_throwsInvalidArgumentException_ifKeyIsNotAString($key)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$query = new Query();
		$query->hasParameter($key);
		
		return;
	}
	
	/**
	 * hasParameter() should return false if the parameter does not exist
	 */
	public function test_hasParameter_returnsFalse_ifParameterDoesNotExist()
	{
		$query = new Query();
		$query->setParameter('foo', 'bar');
		
		$this->assertFalse($query->hasParameter('baz'));
		
		return;
	}
	
	/**
	 * hasParameter() should return true if the parameter does exist
	 */
	public function test_hasParameter_returnsTrue_ifParameterDoesExist()
	{
		$query = new Query();
		$query->setParameter('foo', 'bar');
		
		$this->assertTrue($query->hasParameter('foo'));
		
		return;
	}
	
	/**
	 * hasParameter() should return true if the parameter does exist and evaluates to false
	 */
	public function test_hasParameter_returnsTrue_ifParameterDoesExistAndValueIsFalse()
	{
		$query = new Query();
		
		$query->setParameter('foo', null);
		
		$this->assertTrue($query->hasParameter('foo'));
		
		$query->setParameter('bar', false);
		
		$this->assertTrue($query->hasParameter('bar'));
		
		$query->setParameter('baz', 0);
		
		$this->assertTrue($query->hasParameter('baz'));
		
		return;
	}
	
	
	/* !parse() */
	
	/**
	 * parse() should throw an InvalidArgumentException if $query is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_parse_throwsInvalidArgumentException_ifQueryIsNotAString($query)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$query = new Query();
		$query->parse($query);
		
		return;
	}
	
	/**
	 * parse() should parse a query string with one parameter (separator doesn't matter)
	 */
	public function test_parse_parsesString_ifOneParameter()
	{
		$query = new Query();
		$query->parse('foo=bar');
		
		$expected = ['foo' => 'bar'];
		$actual   = $query->getParameters();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * parse() should parse a query string with many parameters and the default separator
	 */
	public function test_parse_parsesString_ifManyParametersAndDefaultSeparator()
	{
		$query = new Query();
		$query->parse('foo=bar&baz=qux');
		
		$expected = ['foo' => 'bar', 'baz' => 'qux'];
		$actual   = $query->getParameters();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * parse() should throw a BadMethodCallException if there are many parameters and
	 *     the separator is not set correctly
	 */
	public function test_parse_throwsBadMethodCallException_ifManyParametersAndBadSeparator()
	{
		$this->setExpectedException('BadMethodCallException');
		
		$query = new Query();
		$query->setSeparator('|');
		$query->parse('foo=bar&baz=qux&quux=corge');
		
		return;
	}
	
	/**
	 * parse() should parse a query string if there are many parameters and the separator
	 *     is set correctly
	 */
	public function test_parse_parsesString_ifManyParametersAndGoodSeparator()
	{
		$query = new Query();
		$query->setSeparator('|');
		$query->parse('foo=bar|baz=qux|quux=corge');
		
		$expected = ['foo' => 'bar', 'baz' => 'qux', 'quux' => 'corge'];
		$actual   = $query->getParameters();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}  


	/* !setParameter() */
	
	/**
	 * setParameter() should throw an InvalidArgumentException if $key is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_setParameter_throwsInvalidArgumentException_ifKeyIsNotAString($key)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$query = new Query();
		$query->setParameter($key, 'foo');
		
		return;
	}
	
	/**
	 * setParameter() should insert the param's value if key doesn't exist
	 */
	public function test_setParameter_insertsValue_ifKeyDoesNotExist()
	{
		$query = new Query();
		$query->setParameter('foo', 'bar');
		
		$parameters = $query->getParameters();
		$this->assertEquals('bar', $parameters['foo']);
		
		return;
	}
	
	/**
	 * setParameter() should update the param's value if key does exist
	 */
	public function test_setParameter_updatesValue_ifKeyDoesExist()
	{
		$query = new Query();
		$query->setParameter('foo', 'bar');
		
		$parameters = $query->getParameters();
		$this->assertEquals('bar', $parameters['foo']);
		
		$query->setParameter('foo', 'baz');
		
		$parameters = $query->getParameters();
		$this->assertEquals('baz', $parameters['foo']);
		
		return;
	}
	
	
	/* !unsetParameter() */
	
	/**
	 * unsetParameter() should throw an InvalidArgumentException if $key is not a string
	 * 
	 * @dataProvider  notAStringProvider
	 */
	public function test_unsetParameter_throwsInvalidArgumentException_ifKeyIsNotAString($key)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$query = new Query();
		$query->unsetParameter($key);
		
		return;
	}
	
	/**
	 * unsetParameter() should return self if key does not exist
	 */
	public function test_unsetParameter_returnsSelf_ifKeyDoesNotExist()
	{
		$query = new Query();
		
		$expected = $query;
		$actual   = $query->unsetParameter('foo');
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * unsetParameter() should return self if key does exist
	 */
	public function test_unsetParameter_returnsSelf_ifKeyDoesExist()
	{
		$query = new Query();
		$query->setParameter('foo', 'bar');
		
		$expected = $query;
		$actual   = $query->unsetParameter('foo');
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
}
