<?php

use Jstewmc\Url\Path;

/**
 * A class to test the Path class
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2014 Jack Clayton
 * @license    MIT
 * @since      0.1.0
 */

class PathTest extends PHPUnit_Framework_TestCase
{
	/* !Providers */
	
	public function notAnArrayProvider()
	{
		return [
			[null],
			[false],
			[1],
			[1.0],
			['foo'],
			// [[]],
			[new StdClass()]
		];
	}
	
	public function notAnIntegerProvider()
	{
		return [
			[null],
			[false],
			// [1],
			[1.0],
			['foo'],
			[[]],
			[new StdClass()]
		];
	}
	
	public function notAnIntegerOrNullProvider()
	{
		return [
			// [null],
			[false],
			// [1],
			[1.0],
			['foo'],
			[[]],
			[new StdClass()]
		];
	}
	
	public function notAnIntegerOrStringProvider()
	{
		return [
			[null],
			[false],
			// [1],
			[1.0],
			// ['foo'],
			[[]],
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
			// ['foo'],
			[[]],
			[new StdClass()]
		];
	}
	
	
	/* !Get/set tests */
	
	public function test_setAndGetSegments_setsAndGetsSegments()
	{
		$segments = ['foo', 'bar', 'baz'];
		
		$path = new Path();
		$path->setSegments($segments);
		
		$this->assertEquals($segments, $path->getSegments());
		
		return;
	}
	
	public function test_setAndGetSeparator_setsAndGetsSeparator()
	{
		$separator = '|';
		
		$path = new Path();
		$path->setSeparator($separator);
		
		$this->assertEquals($separator, $path->getSeparator());
		
		return;
	}
	
	
	/* !__construct() */
	
	/**
	 * construct() should construct an empty object if path does not exist
	 */
	public function test_construct_constructsObject_ifPathDoesNotExist()
	{
		$path = new Path();
		
		$this->assertTrue($path instanceof Path);
		$this->assertEmpty($path->getSegments());
		
		return;
	}
	
	/**
	 * construct() should construct a non-empty object if path does exist
	 */
	public function test_construct_constructObject_ifPathDoesExist()
	{
		$path = new Path('foo/bar/baz');
		
		$this->assertTrue($path instanceof Path);
		$this->assertEquals(['foo', 'bar', 'baz'], $path->getSegments());
		
		return;
	}
	
	
	/* !__toString() */
	
	/**
	 * __toString() should return an empty string if segments do not exist
	 */
	public function test_toString_returnsString_ifSegmentsDoNotExist()
	{
		$path = new Path();
		
		$expected = '';
		$actual   = (string) $path;
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * __toString() should return a string if segments do exist
	 */
	public function test_toString_returnsString_ifSegmentsDoExist()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = 'foo/bar/baz';
		$actual   = (string) $path;
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !appendSegment() */
	
	/**
	 * appendSegment() should throw an InvalidArgumentException if $segment is not
	 *     a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_appendSegment_throwsInvalidArgumentException_ifSegmentIsNotAString($segment)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->appendSegment($segment);
		
		return;
	}
	
	/**
	 * appendSegment() should append the segment to end of path if segments do not exist
	 */
	public function test_appendSegment_appendsSegment_ifSegmentsDoNotExist()
	{		
		$path = new Path();
		$path->appendSegment('foo');
		
		$expected = ['foo'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * appendSegment() should append segment to end of the path if segments do exist
	 */
	public function test_appendSegment_appendsSegment_ifSegmentsDoExist()
	{
		$segments = ['foo', 'bar', 'baz'];
		
		$path = new Path();
		$path->appendSegment('qux');
		
		$expected = array_merge($segments, ['qux']);
		$actaul   = $path->getSegments();
		
		return;
	}
	
	
	/* !format() */
	
	/**
	 * format() should return an empty string if the path doesn't have segments
	 */
	public function test_format_returnsString_ifSegmentsDoNotExist()
	{
		$path = new Path();
		
		$expected = '';
		$actual   = $path->format();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * format() should return the path as a string with the default separator if the
	 *     path's separator is not set
	 */
	public function test_format_returnsString_ifSeparatorIsNotSet()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = 'foo/bar/baz';
		$actual   = $path->format();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * format() should return the path as a string with the set separator if the 
	 *     path's separator is  set
	 */
	public function test_format_returnsString_ifSeparatorIsSet()
	{
		$path = new Path();
		$path->setSeparator('|');
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = 'foo|bar|baz';
		$actual   = $path->format();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !getIndex() */
	
	/**
	 * getIndex() should throw an InvalidArgumentException if $segment is not a string
	 * 
	 * @dataProvider  notAStringProvider
	 */
	public function test_getIndex_throwsInvalidArgumentException_ifSegmentIsNotAString($segment)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->getIndex($segment);
		
		return;
	}
	
	/**
	 * getIndex() should return false if segment does not exist
	 */
	public function test_getIndex_returnsFalse_ifSegmentDoesNotExist()
	{
		$segments = ['foo', 'bar', 'baz'];
		
		$path = new Path();
		$path->setSegments($segments);
		
		$this->assertFalse($path->getIndex('qux'));
		
		return;
	}
	
	/**
	 * getIndex() should return integer if segment does exist
	 */
	public function test_getIndex_returnsInteger_ifSegmentDoesExist()
	{
	 	$segments = ['foo', 'bar', 'baz'];
		
		$path = new Path();
		$path->setSegments($segments);
		
		$expected = 0;
		$actual   = $path->getIndex('foo');
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !getSegment() */
	
	/**
	 * getSegment() should throw an InvalidArgumentException if $offset is not an integer
	 * 
	 * @dataProvider  notAnIntegerOrStringProvider
	 */
	public function test_getSegment_throwsInvalidArgumentException_ifOffsetIsNotAnIntegerOrString($offset)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->getSegment($offset);
		
		return;	
	}
	
	/**
	 * getSegment() should throw an OutOfBoundsException if $offset is an integer but
	 *     corresponds to an invalid index
	 */
	public function test_getSegment_throwsOutOfBoundsException_ifOffsetIntegerInvalid()
	{
		$this->setExpectedException('OutOfBoundsException');
		
		$path = new Path();
		$path->getSegment(-999);
		
		return;
	}
	 
	/**
	 * getSegment() should throw an InvalidArgumentException if $offset is an invalid
	 *     string
	 */
	public function test_getSegment_throwsInvalidArgumentException_ifOffsetStringInvalid()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->getSegment('foo');
		
		return;
	}
	
	/**
	 * getSegment() should return segment if positive offset
	 */
	public function test_getSegment_returnSegment_ifOffsetPositive()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = 'bar';
		$actual   = $path->getSegment(1);
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * getSegment() should return segment if negative offset
	 */
	public function test_getSegment_returnsSegment_ifOffsetNegative()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = 'bar';
		$actual   = $path->getSegment(-2);
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * getSegment() should return segment if string offset
	 */
	public function test_getSegment_returnsSegment_ifOffsetString()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = 'foo';
		$actual   = $path->getSegment('first');
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !getSlice() */
	
	/**
	 * getSlice() should throw in InvalidArgumentException if $offset is not an integer
	 * 
	 * @dataProvider  notAnIntegerProvider
	 */
	public function test_getSlice_throwsInvalidArgumentException_ifOffsetIsNotAnInteger($offset)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->getSlice($offset);
		
		return;
	}
	
	/**
	 * getSlice() should throw an InvalidArgumentException if $length is not null or an integer
	 *
	 * @dataProvider  notAnIntegerOrNullProvider
	 */
	public function test_getSlice_throwsInvalidArgumentException_ifLengthIsNotAnIntegerAndNotNull($length)
	{
		$this->setExpectedException('InvalidArgumentException');
	
		$path = new Path();
		$path->getSlice(0, $length);
		
		return;
	}
	
	/**
	 * getSlice() should slice from the offset to the end of the path
	 */
	public function test_getSlice_returnsSlice_ifOffsetPositiveAndLengthNull()
	{
		$old = new Path();
		$old->setSegments(['foo', 'bar', 'baz']);
		
		$new = $old->getSlice(1);
		
		$this->assertTrue($new instanceof Path);
		
		$expected = ['bar', 'baz'];
		$actual   = $new->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * getSlice() should slice from the offset for length (or until the end of the path)
	 */
	public function test_getSlice_returnsSlice_ifOffsetPositiveAndLengthPositive()
	{
		$old = new Path();
		$old->setSegments(['foo', 'bar', 'baz']);
		
		$new = $old->getSlice(1, 1);
		
		$this->assertTrue($new instanceof Path);
		
		$expected = ['bar'];
		$actual   = $new->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * getSlice() should slice from the offset to length characters from the end of the path
	 */
	public function test_getSlice_returnsSlice_ifOffsetPositiveAndLengthNegative()
	{
		$old = new Path();
		$old->setSegments(['foo', 'bar', 'baz']);
		
		$new = $old->getSlice(0, -1);
		
		$this->assertTrue($new instanceof Path);
		
		$expected = ['foo', 'bar'];
		$actual   = $new->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * getSlice() should return slice if offset is negative
	 */
	public function test_getSlice_returnsSlice_ifOffsetNegativeAndLengthNull()
	{
		$old = new Path();
		$old->setSegments(['foo', 'bar', 'baz']);
		
		$new = $old->getSlice(-1);
		
		$this->assertTrue($new instanceof Path);
		
		$expected = ['baz'];
		$actual   = $new->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * getSlice() should return slice $offset places from the end of the path for $length if
	 *     offset is negative and length is positive
	 */
	public function test_getSlice_returnsSlice_ifOffsetNegativeAndLengthPositive()
	{
		$old = new Path();
		$old->setSegments(['foo', 'bar', 'baz']);
		
		$new = $old->getSlice(-3, 2);
		
		$this->assertTrue($new instanceof Path);
		
		$expected = ['foo', 'bar'];
		$actual   = $new->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * getSlice() should return slice $offset places from the end of the path and stop $length
	 *     places from the end of the array if offset is negative and length is negative
	 */
	public function test_getSlice_returnsSlice_ifOffsetNegativeAndLengthNegative()
	{
		$old = new Path();
		$old->setSegments(['foo', 'bar', 'baz']);
		
		$new = $old->getSlice(-3, -1);
		
		$this->assertTrue($new instanceof Path);
		
		$expected = ['foo', 'bar'];
		$actual   = $new->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}

	
	/* !getReverse() */
	
	/**
	 * getReverse() should return a reversed path if segments do not exist
	 */
	public function test_getReverse_reversesPath_ifSegmentsDoNotExist()
	{	
		$old = new Path();
		$new = $old->getReverse();
		
		$this->assertTrue($new instanceof Path);
		
		$expected = [];
		$actual   = $new->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * getReverse() should return a reversed path if segments do exist
	 */
	public function test_getReverse_reversesPath_ifSegmentsDoExist()
	{
		$segments = ['foo', 'bar', 'baz']; 
		
		$old = new Path();
		$old->setSegments($segments);
		
		$new = $old->getReverse();
		
		$this->assertTrue($new instanceof Path);
		
		$expected = array_reverse($segments);
		$actual   = $new->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	} 
	 
	
	/* !hasIndex() */
	
	/**
	 * hasIndex() should throw an InvalidArgumentException if $offset is not an integer
	 *
	 * @dataProvider  notAnIntegerProvider
	 */
	public function test_hasIndex_throwsInvalidArgumentException_ifOffsetIsNotAnInteger($offset)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->hasIndex($offset);
		
		return;
	}
	
	/**
	 * hasIndex() should return false if the path does not have a value at the negative offset
	 */
	public function test_hasIndex_returnsFalse_ifValueDoesNotExistAtNegativeOffset()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$this->assertFalse($path->hasIndex(-4));
		
		return;
	}
	
	/**
	 * hasIndex() should return false if the path does have a value at the positive offset
	 */
	public function test_hasIndex_returnsFalse_ifValueDoesNotExistAtPositiveOffset()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$this->assertFalse($path->hasIndex(3));
		
		return;
	}
	
	/**
	 * hasIndex() should return true if the path has a value at the negative offset
	 */
	public function test_hasIndex_returnsTrue_ifValueDoesExistAtNegativeOffset()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$this->assertTrue($path->hasIndex(-1));
		
		return;
	}
	
	/**
	 * hasIndex() should return true if the path has a value at the positive offset
	 */
	public function test_hasIndex_returnsTrue_ifValueDoesExistAtPositiveOffset()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$this->assertTrue($path->hasIndex(0));
		
		return;
	}
	
	
	/* !hasSegment() */
	
	/**
	 * hasSegment() should throw an InvalidArgumentException if $segment is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_hasSegment_throwsInvalidArgumentException_ifSegmentIsNotAString($segment)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->hasSegment($segment);
		
		return;
	}
	
	/**
	 * hasSegment() should return false if the segment does not exist
	 */
	public function test_hasSegment_returnsFalse_ifSegmentDoesNotExist()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$this->assertFalse($path->hasSegment('qux'));
		
		return;
	}
	
	/**
	 * hasSegment() should return true if the segment does exist
	 */
	public function test_hasSegment_returnsTrue_ifSegmentDoesExist()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$this->assertTrue($path->hasSegment('foo'));
		
		return;
	}
	
	/**
	 * hasSegment() should return true if the segment does exist at positive offset
	 */
	public function test_hasSegment_returnsTrue_ifSegmentDoesExistAtPositiveOffset()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$this->assertTrue($path->hasSegment('bar', 1));
		
		return;
	}
	
	/**
	 * hasSegment() should return true if the segment does exist at positive offset
	 */
	public function test_hasSegment_returnsTrue_ifSegmentDoesExistAtNegativeOffset()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$this->assertTrue($path->hasSegment('bar', -2));
		
		return;
	}
	
	/**
	 * hasSegment() should return true if the segment does exist
	 */
	public function test_hasSegment_returnsFalse_ifSegmentDoesNotExistAtNegativeOffset()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$this->assertFalse($path->hasSegment('foo', -1));
		
		return;
	}
	
	/**
	 * hasSegment() should return true if the segment does exist
	 */
	public function test_hasSegment_returnsFalse_ifSegmentDoesNotExistAtPositiveOffset()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$this->assertFalse($path->hasSegment('foo', 2));
		
		return;
	}
	
	
	/* !insertSegment() */
	
	/**
	 * insertSegment() should throw an InvalidArgumentException if $offset is not an integer
	 *
	 * @dataProvider  notAnIntegerProvider
	 */
	public function test_insertSegment_throwsInvalidArgumentException_ifOffsetIsNotInteger($offset)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->insertSegment($offset, 'foo');
		
		return;
	}
	
	/**
	 * insertSegment() should throw an InvalidArgumentException if $segment is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_insertSegment_throwsInvalidArgumentException_ifSegmentIsNotAString($segment)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->insertSegment(0, $segment);	
		
		return;
	}
	
	/**
	 * insertSegment() should throw an OutOfBoundsException if $offset is an integer and
	 *     corresponds to an invalid index
	 */
	public function test_insertSegment_throwsOutOfBoundsException_ifOffsetInvalid()
	{
		$this->setExpectedException('OutOfBoundsException');
		
		$path = new Path();
		$path->insertSegment(999, 'foo');
		
		return;
	}
	
	/**
	 * insertSegment() should insert segment at offset if positive offset
	 */
	public function test_insertSegment_insertsSegment_ifOffsetPositive()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = $path;
		$actual   = $path->insertSegment(1, 'qux');
		
		$this->assertEquals($expected, $actual);
		
		$expected = ['foo', 'qux', 'bar', 'baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * insertSegment() should insert segment at offset is negative offset
	 */
	public function test_insertSegment_insertsSegment_ifOffsetNegative()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = $path;
		$actual   = $path->insertSegment(-1, 'qux');
		
		$this->assertEquals($expected, $actual);
		
		$expected = ['foo', 'bar', 'qux', 'baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !parse() */
	
	/**
	 * parse() should throw an InvalidArgumentException if $path is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_parse_throwsInvalidArgumentException_ifPathIsNotAString($path)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->parse($path);
		
		return;
	}
	
	/**
	 * parse() should parse path if the separator is the default separator ("/")
	 */
	public function test_parse_parsesPath_ifSeparatorIsDefault()
	{
		$string = 'foo/bar/baz';
		
		$path = new Path();
		$path->parse($string);
		
		$expected = ['foo', 'bar', 'baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * parse() should parse path if the separator is not the default separator but it's
	 *     defined before-hand
	 */
	public function test_parse_parsesPath_ifSeparatorIsNotDefaultAndSet()
	{
		$string = 'foo|bar|baz';
		
		$path = new Path();
		$path->setSeparator('|');
		$path->parse($string);
		
		$expected = ['foo', 'bar', 'baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * parse() should "parse" path even if the separator is not default and not defined
	 */
	public function test_parse_parsesPath_ifSeparatorIsNotDefaultAndNotSet()
	{
		$string = 'foo|bar|baz';
		
		$path = new Path();
		$path->parse($string);
		
		$expected = ['foo|bar|baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	 
	
	/* !prependSegment() */
	
	/**
	 * prependSegment() should throw an InvalidArgumentException if $segment is not
	 *     a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_prependSegment_throwsInvalidArgumentException_ifSegmentIsNotAString($segment)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->prependSegment($segment);
		
		return;
	}
	
	/**
	 * prependSegment() should append the segment to end of path if segments do not exist
	 */
	public function test_prependSegment_appendsSegment_ifSegmentsDoNotExist()
	{		
		$path = new Path();
		$path->prependSegment('foo');
		
		$expected = ['foo'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * appendSegment() should append segment to end of the path if segments do exist
	 */
	public function test_prependSegment_appendsSegment_ifSegmentsDoExist()
	{
		$segments = ['foo', 'bar', 'baz'];
		
		$path = new Path();
		$path->prependSegment('qux');
		
		$expected = array_merge(['qux'], $segments);
		$actaul   = $path->getSegments();
		
		return;
	}
	
	
	/* !reverse() */
	
	/**
	 * reverse() should reverse path, even if segments do not exist
	 */
	public function test_reverse_reversesPath_ifSegmentsDoNotExist()
	{
		$path = new Path();
		
		$expected = $path;
		$actual   = $path->reverse();
		
		$this->assertEquals($expected, $actual);
		
		$expected = [];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * reverse() should return self if segments do  exist
	 */
	public function test_reverse_reversesPath_ifSegmentsDoExist()
	{
		$segments = ['foo', 'bar', 'baz'];
		
		$path = new Path();
		$path->setSegments($segments);
		
		$expected = $path;
		$actual   = $path->reverse();
		
		$this->assertEquals($expected, $actual);
		
		$expected = array_reverse($segments);;
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !setSegment() */
	
	/**
	 * setSegment() should throw an InvalidArgumentException if $value is not a string
	 *
	 * @dataProvider  notAStringProvider
	 */
	public function test_setSegment_throwsInvalidArgumentException_ifValueIsNotAString($value)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->setSegment(0, $value);
		
		return;
	}
	
	/**
	 * setSegment() should throw an InvalidArgumentException if $offset is not an integer or string
	 * 
	 * @dataProvider  notAnIntegerOrStringProvider
	 */
	public function test_setSegment_throwsInvalidArgumentException_ifOffsetIsNotAnIntegerOrString($offset)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->setSegment($offset, 'foo');
		
		return;	
	}
	
	/**
	 * setSegment() should throw an OutOfBoundsException if $offset is an integer
	 *     and corresponds to an invalid index
	 */
	public function test_setSegment_throwsOutOfBoundsException_ifOffsetIntegerInvalid()
	{
		$this->setExpectedException('OutOfBoundsException');
		
		$path = new Path();
		$path->setSegment(-999, 'foo');
		
		return;
	}
	 
	/**
	 * setSegment() should throw an InvalidArgumentException if $offset is an invalid
	 *     string
	 */
	public function test_setSegment_throwsInvalidArgumentException_ifOffsetStringInvalid()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->setSegment('foo', 'foo');
		
		return;
	}
	
	/**
	 * setSegment() should set segment if positive offset
	 */
	public function test_setSegment_setsSegment_ifOffsetPositive()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = $path;
		$actual   = $path->setSegment(1, 'qux');
		
		$this->assertEquals($expected, $actual);
		
		$expected = ['foo', 'qux', 'baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * setSegment() should set segment if negative offset
	 */
	public function test_setSegment_setsSegment_ifOffsetNegative()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = $path;
		$actual   = $path->setSegment(-1, 'qux');
		
		$this->assertEquals($expected, $actual);
		
		$expected = ['foo', 'bar', 'qux'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * setSegment() should set segment if string offset
	 */
	public function test_setSegment_setsSegment_ifOffsetString()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		
		$expected = $path;
		$actual   = $path->setSegment('last', 'qux');
		
		$this->assertEquals($expected, $actual);
		
		$expected = ['foo', 'bar', 'qux'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !slice() */
	
	/**
	 * slice() should throw in InvalidArgumentException if $offset is not an integer
	 * 
	 * @dataProvider  notAnIntegerProvider
	 */
	public function test_slice_throwsInvalidArgumentException_ifOffsetIsNotAnInteger($offset)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->slice($offset);
		
		return;
	}
	
	/**
	 * slice() should throw an InvalidArgumentException if $length is not null or an integer
	 *
	 * @dataProvider  notAnIntegerOrNullProvider
	 */
	public function test_slice_throwsInvalidArgumentException_ifLengthIsNotAnIntegerAndNotNull($length)
	{
		$this->setExpectedException('InvalidArgumentException');
	
		$path = new Path();
		$path->slice(0, $length);
		
		return;
	}
	
	/**
	 * slice() should slice from $offset to the end of the path if offset is positive and 
	 *     $length is null
	 */
	public function test_slice_slicesPath_ifOffsetPositiveAndLengthNull()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		$path->slice(1);
		
		$expected = ['bar', 'baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * slice() should slice from $offset for $length if offset is positive and length is
	 *     positive
	 */
	public function test_slice_slicesPath_ifOffsetPositiveAndLengthPositive()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		$path->slice(1, 1);
		
		$expected = ['bar'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * slice() should slice from $offset to $length characters from the end of 
	 *     the path if offset is positive and length is negative
	 */
	public function test_slice_slicesPath_ifOffsetPositiveAndLengthNegative()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		$path->slice(0, -1);
		
		$expected = ['foo', 'bar'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * slice() should slice $offset places to end of path if offset is negative and
	 *     length is null
	 */
	public function test_slice_slicesPath_ifOffsetNegativeAndLengthNull()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		$path->slice(-1);
		
		$expected = ['baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * slice() should return slice $offset places from the end of the path for $length if
	 *     offset is negative and length is positive
	 */
	public function test_slice_slicesPath_ifOffsetNegativeAndLengthPositive()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		$path->slice(-3, 2);
		
		$expected = ['foo', 'bar'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * slice() should return slice $offset places from the end of the path and stop $length
	 *     places from the end of the array if offset is negative and length is negative
	 */
	public function test_slice_slicesPath_ifOffsetNegativeAndLengthNegative()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		$path->slice(-3, -1);
		
		$expected = ['foo', 'bar'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/* !unsetSegment() */
	
	/**
	 * unsetSegment() should throw an InvalidArgumentException if $offset is not an integer
	 * 
	 * @dataProvider  notAnIntegerOrStringProvider
	 */
	public function test_unsetSegment_throwsInvalidArgumentException_ifOffsetIsNotAnIntegerOrString($offset)
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->unsetSegment($offset);
		
		return;	
	}
	
	/**
	 * unsetSegment() should throw an OutOfBoundsException if $offset is an integer
	 *     and corresponds to an invalid index
	 */
	public function test_unsetSegment_throwsOutOfBoundsException_ifOffsetIntegerInvalid()
	{
		$this->setExpectedException('OutOfBoundsException');
		
		$path = new Path();
		$path->unsetSegment(-999);
		
		return;
	}
	 
	/**
	 * unsetSegment() should throw an InvalidArgumentException if $offset is an 
	 *     invalid string
	 */
	public function test_unsetSegment_throwsInvalidArgumentException_ifOffsetStringInvalid()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$path = new Path();
		$path->unsetSegment('foo');
		
		return;
	}
	
	/**
	 * unsetSegment() should remove segment if positive offset
	 */
	public function test_unsetSegment_removesSegment_ifOffsetPositive()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		$path->unsetSegment(1);
		
		$expected = ['foo', 'baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * unsetSegment() should remove segment if negative offset
	 */
	public function test_unsetSegment_removesSegment_ifOffsetNegative()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		$path->unsetSegment(-2);
		
		$expected = ['foo', 'baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * unsetSegment() should remove segment if string offset
	 */
	public function test_unsetSegment_removesSegment_ifOffsetString()
	{
		$path = new Path();
		$path->setSegments(['foo', 'bar', 'baz']);
		$path->unsetSegment('first');
		
		$expected = ['bar', 'baz'];
		$actual   = $path->getSegments();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
}
