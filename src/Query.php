<?php
	
namespace Jstewmc\Url;

/**
 * A Uniform Resource Locator (URL) query string
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2014 Jack Clayton
 * @license    MIT
 * @since      0.1.0
 */

class Query
{
	/* !Protected properties */
	
	/**
	 * @var  string[]  the query string's parameters
	 * @since  0.1.0
	 */
	protected $parameters = array();
	
	/**
	 * @var  string  the argument separator; defaults to ampersand ("&")
	 * @since  0.1.0
	 */
	protected $separator = '&';
	
	
	/* !Get methods */
	
	/**
	 * @since  0.1.0
	 */
	public function getParameters()
	{
		return $this->parameters;
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
	public function setParameters($parameters)
	{
		$this->parameters = $parameters;
		
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
	 * If a query string is given, I'll attempt to parse it. 
	 *
	 * Keep in mind, I can only parse query strings that use the ampersand ("&") as 
	 * argument separator and the equal sign ("=") as assignment operator. 
	 *
	 * If the query string does not those conventions, you should use the setSeparator() 
	 * method to set the separator and call parse() manually. Otherwise, my results will 
	 * be wonky.
	 *
	 * @param  string  $query  a query string to parse (optional; if omitted, defaults
	 *     to null and returns a bare object)
	 * @return  self
	 * @since   0.1.0
	 */
	public function __construct($query = null)
	{
		if (is_string($query)) {
			$this->parse($query);
		}
		
		return;
	}
	
	/**
	 * Called automatically when the object is used as a string
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
	 * Sets $parameter in the query string
	 *
	 * If $key exists, it's value will be updated to $value.
	 *
	 * @param  string  $key    the param's key
	 * @param  string  $value  the param's value
	 * @return  self
	 * @throws  InvalidArgumentException  if $key is not a string
	 * @since   0.1.0
	 */
	public function setParameter($key, $value)
	{
		if (is_string($key)) {
			$this->parameters[$key] = $value;	
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, key, to be a string"
			);
		}
		
		return $this;
	}
	
	/**
	 * Returns the query string as a string
	 *
	 * @return  string
	 * @since   0.1.0
	 */
	public function format()
	{
		$string = '';
		
		if ( ! empty($this->parameters)) {
			$string = http_build_query($this->parameters, null, $this->separator);
		}
		
		return $string;
	}
	
	/**
	 * Returns a query parameter's value
	 *
	 * @param  string  $key  the parameter's key name
	 * @return  mixed
	 * @throws  InvalidArgumentException  if $key is not a string
	 * @throws  OutOfBoundsException  if $key does not exist
	 * @since   0.1.0
	 */
	public function getParameter($key)
	{
		$value = null;
		
		if (is_string($key)) {
			if (array_key_exists($key, $this->parameters)) {
				$value = $this->parameters[$key];
			} else {
				throw new \OutOfBoundsException(
					__METHOD__."() expects key, $key, to be a valid index"
				);
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, key, to be a string"
			);
		}
		
		return $value;
	}
	
	/**
	 * Returns true if the parameter exists in the query string
	 *
	 * @param  string  $key  the parameter's key name
	 * @return  bool
	 * @since   0.1.0
	 * @throws  InvalidArgumentException  if $key is not a string
	 */
	public function hasParameter($key)
	{
		$hasParam = false;
		
		if (is_string($key)) {
			$hasParam = array_key_exists($key, $this->parameters);
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, key, to be a string"
			);
		}
		
		return $hasParam;
	}
	
	/**
	 * Parses a query string into this object's properties
	 *
	 * @param  string  $query  the query string to parse
	 * @return  self
	 * @throws  InvalidArgumentException  if $query is not a string
	 * @throws  BadMethodCallException    if a key-value pair contains multiple
	 *     assignment operators ('=') (i.e., the argument separator is probably
	 *     not set correctly)
	 * @since   0.1.0
	 */
	public function parse($query)
	{ 
		if (is_string($query)) {
			$pairs = explode($this->separator, $query);
			foreach ($pairs as $pair) {
				if (substr_count($pair, '=') == 1) {
					$key   = substr($pair, 0, strpos($pair, '='));
					$value = substr($pair, strpos($pair, '=') + 1);
					$this->parameters[$key] = $value;
				} else {
					throw new \BadMethodCallException(
						__METHOD__."() expects a single assignment operator ('=') per key-value "
							. "pair; is the separator correct?"
					);
				}
			}	
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, query, to be a string"
			);
		}
		
		return $this;
	}
	
	/**
	 * Unsets a parameter from the query string
	 *
	 * @param  string  $key  the param's key
	 * @return  self
	 * @throws  InvalidArgumentException  if $key is not a string
	 * @since   0.1.0
	 */
	public function unsetParameter($key)
	{
		if (is_string($key)) {
			if (array_key_exists($key, $this->parameters)) {
				unset($this->parameters[$key]);
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, key, to be a string"
			);
		}
		
		return $this;
	}
}
