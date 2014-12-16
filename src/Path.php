<?php

namespace Jstewmc\Url;

/**
 * A Uniform Resource Locator (URL) path
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2014 Jack Clayton
 * @license    MIT
 * @since      0.1.0
 */
 
class Path 
{
	/* !Public properties */
	
	/**
	 * @var  string[]  an array of path segments; defaults to empty array
	 * @since  0.1.0
	 */
	protected $segments = [];
	
	/**
	 * @var  string  the path's separator; defaults to forward-slash ("/")
	 * @since  0.1.0
	 */
	protected $separator = '/';
	
	
	/* !Get methods */
	
	/**
	 * @since  0.1.0
	 */
	public function getSegments()
	{
		return $this->segments;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function getSeparator()
	{
		return $this->separator;
	}
	
	
	/* !Set methods */
	
	/**
	 * @since  0.1.0
	 */
	public function setSegments($segments)
	{
		$this->segments = $segments;
		
		return $this;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function setSeparator($separator)
	{
		$this->separator = $separator;
		
		return $this;
	}
	
	
	/* !Magic methods */
	
	/**
	 * Constructs the object
	 *
	 * If a path is given, I'll attempt to parse it.
	 *
	 * Keep in mind, I can only parse paths that use the forward-slash ("/") separator. 
	 *
	 * If the path does not use the forward-slash as a separator, you should call the 
	 * setSeparator() and parse() manually. Otherwise, my results will be wonky.
	 *
	 * @param  string  $path  a path to parse (optional; if omitted, defaults to null
	 *     and returns a bare object)
	 * @return  self
	 * @since  0.1.0
	 */
	public function __construct($path = null)
	{
		if (is_string($path)) {
			$this->parse($path);
		}
		
		return;
	}
	
	/**
	 * Called automatically when the object is converted to a string
	 *
	 * @return  string
	 * @since   0.1.0 
	 */
	public function __toString()
	{
		return $this->format();
	}
	
	
	/* !Public methods */	

	/**
	 * Appends $segment to the end of the path
	 * 
	 * @param  string  $segment  the segment to append
	 * @return  self
	 * @throws  InvalidArgumentException  if $segment is not a string
	 * @since   0.1.0
	 */
	public function appendSegment($segment)
	{
		if (is_string($segment)) {
			$this->segments[] = $segment;
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, segment, to be a string"
			);
		}
		
		return $this;
	}
	
	/**
	 * Returns the path as a string
	 *
	 * @return  string
	 * @since   0.1.0
	 */
	public function format()
	{
		$string = '';
		
		if ( ! empty($this->segments)) {
			$string = implode($this->separator, $this->segments);
		}
		
		return $string;
	}
	
	/**
	 * Returns the 0-based index of $segment
	 *
	 * WARNING! This method may return false ($segment does not exist in the
	 * path) or 0 ($segment exists in the first position). Be sure to use the 
	 * strict comparison operator === when testing this method's return values.
	 *
	 * For example:
	 *
	 *     $path = new Path('foo/bar/baz');
	 *     $path->getIndex('foo');  // returns 0
	 *     $path->getIndex('bar');  // returns 1
	 *     $path->getIndex('qux');  // returns false
	 *
	 * @param  string  $segment  the segment to find (case-sensitive)
	 * @return  int|bool  the integer index or false if the segment does not exist
	 * @throws  InvalidArgumentException  if $segment is not a string
	 * @since   0.1.0
	 */
	public function getIndex($segment)
	{
		$index = false;
		
		if (is_string($segment)) {
			$index = array_search($segment, $this->segments);
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, segment, to be a string"
			);
		}
		
		return $index;
	}
	
	/**
	 * Returns the segment at $offset
	 *
	 * For example:
	 *
	 *     $path = new Path('foo/bar/baz');
	 *     $path->getSegment(0);        // returns 'foo' (zero segments from beginning)
	 *     $path->getSegment(-3);       // returns 'foo' (third segment from end)
	 *     $path->getSegment('first');  // returns 'foo'
	 * 
	 * @param  int|string  $offset  if offset is non-negative, the segment that many
	 *     places from the beginning of the path will be returned; if offset is negative, 
	 *     the segment that many places from the end of the path will be returned; if 
	 *     offset is a non-integer string, it must be one of the following strings:
	 *     'first', for the first segment; 'last', for the last segment; or, 'rand[om]',
	 *     for a random segment (case-insensitive)
	 * @return  string
	 * @throws  InvalidArgumentException  if $offset is not a string or integer
	 * @throws  InvalidArgumentException  if $offset is an unsupported string
	 * @throws  OutOfBoundsException      if $offset results in an invalid index
	 * @since   0.1.0
	 */
	public function getSegment($offset)
	{
		$segment = false;
		
		if (is_numeric($offset) && is_int(+$offset)) {
			$index = $this->getIndexFromOffset($offset);
			if ($index >= 0 && array_key_exists($index, $this->segments)) {
				$segment = $this->segments[$index];
			} else {
				throw new \OutOfBoundsException(
					__METHOD__."() expects parameter one, offset, when an integer, to result in "
						. " a valid index; $index is not a valid index"
				);
			}
		} elseif (is_string($offset)) {
			switch (strtolower($offset)) {
				
				case 'first':
					$segment = reset($this->segments);
					break;
				
				case 'last':
					$segment = end($this->segments);
					break;
				
				case 'rand':
				case 'random':
					$segment = $this->segments[array_rand($this->segments)];
					break;
				
				default:
					throw new \InvalidArgumentException(
						__METHOD__."() expects parameter one, offset, when a string, to be one "
							. "of the following: 'first', 'last', or 'rand[om]'; '$offset' is not "
							. "supported"
					);
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, index, to be a string or integer"
			);
		}
		
		return $segment;
	}
	
	/**
	 * Returns a slice of the path starting at $offset for $length segments
	 *
	 * I'll return a new Path object (leaving this Path unchanged). To slice this 
	 * path, use the slice() method instead.
	 *
	 * For example:
	 *
	 *     $a = new Path('foo/bar/baz');
	 *     $b = $a->getSlice(1);
	 *
	 *     echo $a;  // prints 'foo/bar/baz'
	 *     echo $b;  // prints 'bar/baz'
	 * 
	 * @param  int  $offset  if offset is non-negative, the slice will start at that 
	 *     offset from the beginning of the path; if offset is negative, the slice 
	 *     will start that far from the end of the path
	 * @param  int  $length  if length is given and positive, the slice will have up 
	 *     to that many segments in it; if the path is shorter than length, then only
	 *     the available segments will be present; if length is given and negative,
	 *     the slice will stop that many segments from the end of the path; finally, 
	 *     if length is omitted, the slice will have everything from the offset to the
	 *     end of the path
	 * @return  Jstewmc\Url\Path  the slice
	 * @throws  InvalidArgumentException  if $offset is not an integer
	 * @throws  InvalidArgumentException  if $length is not an integer or null
	 * @since   0.1.0
	 */
	public function getSlice($offset, $length = null)
	{
		$slice = false;
		
		if (is_numeric($offset) && is_int(+$offset)) {
			if ($length === null || (is_numeric($length) && is_int(+$length))) {
				$slice = (new Path())
					->setSegments(array_slice($this->segments, $offset, $length))
					->setSeparator($this->separator);
			} else {
				throw new \InvalidArgumentException(
					__METHOD__."() expects parameter two, length, to be null or an integer"
				);
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, offset, to be an integer"
			);
		}
		
		return $slice;
	}
	
	/**
	 * Returns the reverse of the path
	 *
	 * I'll return a new Path object, and I'll leave this path unchanged. To reverse 
	 * this path, use the reverse() method instead.
	 *
	 * For example:
	 *
	 *     $a = new Path('foo/bar/baz');
	 *     $b = $a->getReverse();
	 *
	 *     echo $a;  // prints "foo/bar/baz"
	 *     echo $b;  // prints "baz/bar/foo"
	 *
	 * @return  Jstewmc\Url\Path
	 * @since   0.1.0
	 */
	public function getReverse()
	{
		return (new Path())
			->setSegments(array_reverse($this->segments))
			->setSeparator($this->separator);
	}
	
	/**
	 * Returns true if the path has any segment at $index
	 *
	 * For example:
	 *
	 *     $path = new Path('foo/bar/baz');
	 *     $path->hasIndex(0);  // returns true 
	 *     $path->hasIndex(3);  // returns false
	 *
	 * @param  int  $offset   if offset is non-negative, the search will occur that  
	 *     many places from the beginning of the path; if offset is negative, the search 
	 *     will occur that many places from the end of the path
	 * @return  bool
	 * @throws  InvalidArgumentException  if $offset is not an integer
	 * @since   0.1.0
	 */
	public function hasIndex($offset) 
	{
		if (is_numeric($offset) && is_int(+$offset)) {
			$index = $this->getIndexFromOffset($offset);	
			if ($index >= 0 && array_key_exists($index, $this->segments)) {
				return true;
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, offset, to be an integer"
			);
		}
		
		return false;
	}

	/**
	 * Returns true if the path has $segment (optionally, at $offset) 
	 *
	 * For example:
	 *
	 *     $path = new Path('foo/bar/baz');
	 *     $path->hasSegment('foo');           // returns true
	 *     $path->hasSegment('foo', 0);        // returns true
	 *     $path->hasSegment('foo', 'first');  // returns true
	 *     $path->hasSegment('qux');           // returns false
	 *     $path->hasSegment('foo', 1);        // returns false
	 * 
	 * @param  string           $segment  the segment to search for 
	 * @param  null|int|string  $offset   if offset is non-negative, the search will  
	 *     occur that many places from the beginning of the path; if offset is negative, 
	 *     the search will occur that far from the end of the path; if string, must be
	 *     'first' or 'last' (optional; if omitted, defaults to null and the segment's 
	 *     position doesn't matter)
	 * @return  bool
	 * @throws  InvalidArgumentException  if $segment is not a string
	 * @throws  InvalidArgumentException  if $offset is not an integer, not the strings
	 *     'first' or 'last', and not null
	 * @since   0.1.0
	 */
	public function hasSegment($segment, $offset = null)
	{
		if (is_string($segment)) {
			if ($offset === null ) {
				foreach ($this->segments as $key => $value) {
					if ($value == $segment) {
						return true;
					}
				}
			} elseif (is_numeric($offset) && is_int(+$offset)) {
				$index = $this->getIndexFromOffset($offset);
				if ($index >= 0 && array_key_exists($index, $this->segments)) {
					if ($this->segments[$index] == $segment) {
						return true;
					}
				}
			} elseif ($offset === 'first') {
				if (reset($this->segments) === $segment) {
					return true;
				}
			} elseif ($offset === 'last') {
				if (end($this->segments) === $segment) {
					return true;
				}
			} else {
				throw new \InvalidArgumentException(
					__METHOD__."() expects parameter two, offset, to be an integer, the string "
						. "'first' or 'last', or null"
				);
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, segment, to be a string"
			);
		}
		
		return false;
	}
	
	/**
	 * Inserts $segment at $offset
	 *
	 * For example:
	 *
	 *     $path = new Path('foo/bar/baz');
	 *     $path->insertSegment('qux', 1);
	 *
	 *     echo $path;  // prints "foo/qux/bar/baz"
	 *
	 * @param  int  $offset  if offset is non-negative, the insert starts at that
	 *     position from the beginning of the path; if offset is negative, the insert 
	 *     starts that far from the end of the array
	 * @param  string  $segment  the segment to insert
	 * @return  self
	 * @throws  InvalidArgumentException  if $offset is not an integer
	 * @throws  InvalidArgumentException  if $segment is not a string
	 * @throws  OutOfBoundsException      if $offset corresponds to invalid index
	 * @since   0.1.0
	 */
	public function insertSegment($offset, $segment)
	{
		if (is_numeric($offset) && is_int(+$offset)) {
			if (is_string($segment)) {
				$index = $this->getIndexFromOffset($offset);
				if ($index >= 0 && array_key_exists($index, $this->segments)) {
					array_splice($this->segments, $offset, 0, $segment);	
				} else {
					throw new \OutOfBoundsException(
						__METHOD__."() expects parameter one, offset, to correspond to a valid "
							. "index; $index is not a valid index"
					);	
				}
			} else {
				throw new \InvalidArgumentException(
					__METHOD__."() expects parameter two, segment, to be a string"
				);	
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, offset, to be an integer"
			);
		}
		
		return $this;
	}
	
	/**
	 * Parses the string path into this Path object
	 *
	 * Keep in mind, if $path does not use the forward-slash as its separator, you
	 * should set the path's separator first, before calling parse().
	 *
	 * @param  string  $path  the path to parse (with or without leading separator)
	 * @return  self
	 * @throws  InvalidArgumentException  if $path is not a string
	 * @since   0.1.0
	 */
	public function parse($path)
	{
		if (is_string($path)) {
			if (substr($path, 0, 1) === $this->separator) {
				$path = substr($path, 1);
			}
			$this->segments = explode($this->separator, $path);	
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, path, to be a string"
			);
		}
		
		return;
	}
	
	/**
	 * Prepends $segment to the path
	 *
	 * For example:
	 *
	 *     $path = new Path('foo/bar/baz');
	 *     $path->prependSegment('qux');
	 *     
	 *     echo $path;  // prints "qux/foo/bar/baz"
	 *
	 * @param  string  $segment  the segment to prepend to the beginning of the path
	 * @return  self
	 * @throws  InvalidArgumentException  if $segment is not a string
	 * @since   0.1.0
	 */
	public function prependSegment($segment)
	{
		if (is_string($segment)) {
			array_unshift($this->segments, $segment);
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, segment, to be a string"
			);
		}
		
		return $this;
	}
	
	/**
	 * Reverses this path
	 *
	 * I'll change this path's segments. To leave this path unchanged and get a new
	 * reversed Path, use the getReverse() method instead.
	 *
	 *     $path = new Path('foo/bar/baz');
	 *     $path->reverse();
	 *
	 *     echo $path;  // prints "baz/bar/foo"
	 *
	 * @return  self
	 * @since   0.1.0
	 */
	public function reverse()
	{
		$this->segments = array_reverse($this->segments);
		
		return $this;
	}
	
	/**
	 * Sets segment at $offset to $value
	 *
	 * For example:
	 * 
	 *     $path = new Path('foo/bar/baz');
	 *     $path->setSegment(0, 'qux');        // path is "qux/bar/baz"
	 *     $path->setSegment('last', 'quux');  // path is "qux/bar/quux"
	 *     $path->setSegment(-2, 'corge');     // path is "qux/corge/quux"
	 *
	 *     echo $path;  // prints "qux/corge/quux"
	 *
	 * @param  int|string  $offset  if offset is non-negative, the segment that many
	 *     places from the beginning of the path will be updated; if offset is negative,
	 *     the segment that many places from the end of the path will be updated; if
	 *     offset is a string, it must be "first", the first segment, or "last", the
	 *     last segment (case-insensitive)
	 * @param  string  $value   the segment's new value
	 * @return  self
	 * @throws  InvalidArgumentException  if $value is not a string
	 * @throws  InvalidArgumentException  if $offset is not an integer or string
	 * @throws  InvalidArgumentException  if $offset is an unsupported string
	 * @throws  OutOfBoundsException      if $offset results in an invalid index
	 * @since   0.1.0
	 */
	public function setSegment($offset, $value)
	{
		if (is_string($value)) {
			if (is_numeric($offset) && is_int(+$offset)) {
				$index = $this->getIndexFromOffset($offset);
				if ($index >= 0 && array_key_exists($index, $this->segments)) {
					$this->segments[$index] = $value;
				} else {
					throw new \OutOfBoundsException(
						__METHOD__."() expects parameter one, offset, when an integer, to correspond "
							. "to a valid index; $index is not a valid index"
					);
				}
			} elseif (is_string($offset)) {
				switch (strtolower($offset)) {
					
					case 'first':
						$this->segments[0] = $value;
						break;
					
					case 'last':
						$this->segments[count($this->segments) - 1] = $value;
						break;
					
					default:
						throw new \InvalidArgumentException(
							__METHOD__."() expects parameter one, offset, when a string, to be "
								. "'first' or 'last'; '$offset' is not supported"
						);
				}
			} else {
				throw new \InvalidArgumentException(
					__METHOD__."() expects parameter one, offset, to be a string or integer"
				);
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter two, value, to be a string"
			);
		}
		
		return $this;
	}
	
	/**
	 * Slices the path
	 *
	 * Keep in mind, I will change this path. To keep this path the same and return a 
	 * new, sliced Path, use the getSlice() method instead.
	 *
	 * For example:
	 *
	 *     $path = new Path('foo/bar/baz');
	 *     $path->slice(1);
	 *     
	 *     echo $path;  // prints "bar/baz"
	 *
	 * @param  int  $offset  if offset is non-negative, the slice will start at that 
	 *     offset from the beginning of the path; if offset is negative, the slice 
	 *     will start that far from the end of the path
	 * @param  int  $length  if length is given and positive, the slice will have up 
	 *     to that many segments in it; if the path is shorter than length, then only
	 *     the available segments will be present; if length is given and negative,
	 *     the slice will stop that many segments from the end of the path; finally, 
	 *     if length is omitted, the slice will have everything from the offset to the
	 *     end of the path (optional; if omitted, defaults to null)
	 * @return  self
	 * @throws  InvalidArgumentException  if $offset is not an integer
	 * @throws  InvalidArgumentException  if $length is not null or an integer
	 * @since   0.1.0
	 */
	public function slice($offset, $length = null)
	{
		if (is_numeric($offset) && is_int(+$offset)) {
			if ($length === null || (is_numeric($length) && is_int(+$length))) {
				$this->segments = array_slice($this->segments, $offset, $length);
			} else {
				throw new \InvalidArgumentException(
					__METHOD__."() expects parameter two, length, to be null or an integer"
				);
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, offset, to be an integer"
			);
		}
		
		return $this;
	}
	
	/**
	 * Unsets the segment at $offset
	 *
	 * Keep in mind, keys will be reset after this method call.
	 *
	 * For example:
	 *
	 *     $path = new Path('foo/bar/baz');
	 *     $path->unsetSegment(0);        // path is "bar/baz"
	 *     $path->unsetSegment('first');  // path is "baz"
	 *     $path->unsetSegment('last');   // path is ""
	 *
	 * @param  int|string  $offset  if offset is non-negative, the segment that many
	 *     place from the beginning of the path will be removed; if offset is negative,
	 *     the segment that many places from the end of the path will be removed; if
	 *     the offset is a non-integer string, it must be 'first', the first segment or 
	 *     'last', the last segment (case-insensitive)
	 * @return  self
	 * @throws  InvalidArgumentException  if $offset is not an integer or string
	 * @throws  InvalidArgumentException  if $offset is an unsupported string
	 * @throws  OutOfBoundsException      if $offset results in an invalid index
	 * @since   0.1.0
	 */
	public function unsetSegment($offset)
	{
		if (is_numeric($offset) && is_int(+$offset)) {	
			$index = $this->getIndexFromOffset($offset);
			if ($index >= 0 && array_key_exists($index, $this->segments)) {
				unset($this->segments[$index]);
				$this->segments = array_values($this->segments);
			} else {
				throw new \OutOfBoundsException(
					__METHOD__."() expects parameter one, offset, when an integer, to correspond"
						. " with a valid index; $index is not valid"
				);
			}
		} elseif (is_string($offset)) {
			switch (strtolower($offset)) {
				
				case 'first':
					array_shift($this->segments);
					break;
				
				case 'last':
					array_pop($this->segments);
					break;
				
				default:
					throw new \InvalidArgumentException(
						__METHOD__."() expects parameter one, offset, when a string, to be 'first' "
							. " or 'last'; '$offset' is not supported"
					);
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, offset, to be a string or integer"
			);
		}
		
		return $this;
	}
	
	
	/* !Protected methods */
	
	/**
	 * Returns an index in $segments based on $offset
	 *
	 * @param  int  $offset  the array's offset
	 * @return  int  the array's index
	 * @throws  InvalidArgumentException  if $offset is not a string
	 * @since   0.1.0
	 */
	protected function getIndexFromOffset($offset)
	{
		if ( ! is_numeric($offset) && is_int(+$offset)) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, offset, to be an integer"
			);	
		}
		
		return $offset < 0 ? count($this->segments) + $offset : $offset;
	}
}
