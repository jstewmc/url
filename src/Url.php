<?php

namespace Jstewmc\Url;

use Jstewmc\Path\Path;

/**
 * A Uniform Resource Locator (URL)
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2014 Jack Clayton 
 * @license    MIT
 * @since      0.1.0
 */

class Url
{
	/* !Protected properties */
	
	/**
	 * @var  string  the url's fragment
	 * @since  0.1.0
	 */
	protected $fragment; 
	
	/**
	 * @var  string  the url's host
	 * @since  0.1.0
	 */
	protected $host;
	
	/**
	 * @var  string  the url's password
	 * @since  0.1.0
	 */
	protected $password;
	
	/**
	 * @var  Jstewmc\Path\Path  the url's Path
	 * @since  0.1.0
	 */
	protected $path;
	
	/**
	 * @var  string  the url's port
	 * @since  0.1.0
	 */
	protected $port;
	
	/**
	 * @var  Query  the url's Query string
	 * @since  0.1.0
	 */
	protected $query;
	
	/**
	 * @var  string  the url's scheme
	 * @since  0.1.0
	 */
	protected $scheme; 
	
	/**
	 * @var  string  the url's username
	 * @since  0.1.0
	 */
	protected $username;
	
	
	/* !Get methods */
	
	/**
	 * @since  0.1.0
	 */
	public function getFragment()
	{
		return $this->fragment;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function getHost()
	{
		return $this->host;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function getPassword()
	{
		return $this->password;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function getPath()
	{
		return $this->path;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function getPort()
	{
		return $this->port;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function getQuery()
	{
		return $this->query;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function getScheme()
	{
		return $this->scheme;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function getUsername()
	{
		return $this->username;
	}
	
	
	/* !Set methods */
	
	/**
	 * @since  0.1.0
	 */
	public function setFragment($fragment)
	{
		$this->fragment = $fragment;
		
		return $this;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function setHost($host)
	{
		$this->host = $host;
		
		return $this;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function setPassword($password)
	{
		$this->password = $password;
		
		return $this;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function setPath($path)
	{
		if (is_string($path)) {
			$path = new Path($path);	
		}
		
		$this->path = $path;
		
		return $this;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function setPort($port)
	{
		$this->port = $port;
		
		return $this;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function setQuery($query)
	{
		if (is_string($query)) {
			$query = new Query($query);
		}
		
		$this->query = $query;
		
		return $this;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function setScheme($scheme)
	{
		$this->scheme = $scheme;
		
		return $this;
	}
	
	/**
	 * @since  0.1.0
	 */
	public function setUsername($username)
	{
		$this->username = $username;
		
		return $this;
	}
	
	
	/* !Magic methods */
	
	/**
	 * Constructs the object 
	 *
	 * If a string url is given, I'll parse it. Keep in mind, I can only auto-magically
	 * parse well-formed URL's that follow convention (i.e., forward-slash ("/") path 
	 * separator, ampersand ("&") argument separator, etc.).
	 *
	 * @param  string  $url  a string url to parse (optional; if omitted, defaults 
	 *     to null, and this object is empty)
	 * @return  self
	 * @since  0.1.0
	 */
	public function __construct($url = null)
	{
		if (is_string($url)) {
			$this->parse($url);
		}
		
		// if the path and query haven't already been instantiated in the parse() method, 
		//     instantiate them now as empty objects; otherwise, they will be null, and
		//     if the user attempts to set query params or path segments PHP will raise
		//     an non-object exception...
		//
		if (empty($this->path)) {
			$this->path = new Path();
		}

		if (empty($this->query)) {
			$this->query = new Query();
		}
			
		return;
	}
	
	/**
	 * Called when this url is treated like a string
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
	 * Formats the URL as a string
	 *
	 * For example:
	 *
	 *     $url = new Url();
	 *     $url->setScheme('http')->setHost('example.com')->setPath('foo/bar');
	 *     $url->format('absolute');  // prints "http://example.com/foo/bar" 
	 *     $url->format('relative');  // prints "/foo/bar"
	 * 
	 * @param  string  $format  the format to output (possible values: 'absolute', 
	 *      include all non-empty-parts; 'relative', includes path, query, and 
	 *      fragment) (optional; if omitted, defaults to 'absolute') (case-insensitive)
	 * @return  string
	 * @throws  InvalidArgumentException  if $format is not a string
	 * @throws  InvalidArgumentException  if $format is not 'absolute' or 'relative'
	 * @since   0.1.0
	 */	
	public function format($format = 'absolute')
	{	
		if (is_string($format)) {
			switch (strtolower($format)) {
				
				case 'absolute':
					$string = '';
					$string = $this->appendScheme($string);
					$string = $this->appendCredentials($string);
					$string = $this->appendHost($string);
					$string = $this->appendPort($string);
					$string = $this->appendPath($string);
					$string = $this->appendQuery($string);
					$string = $this->appendFragment($string);
					break;
				
				case 'relative':
					$string = '/';
					$string = $this->appendPath($string);
					$string = $this->appendQuery($string);
					$string = $this->appendFragment($string);
					break;
				
				default:
					throw new \InvalidArgumentException(
						__METHOD__."() expects parameter one, format, to be the string 'absolute' "
							. "or 'relative'; '$format' given"
					);
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, format, to be a string"
			);
		}
		
		return $string;
	}
	
	/**
	 * Parses a string url
	 *
	 * I break the parsing of a url into smaller functions which are easier to extend
	 * in child-classes.
	 *
	 * Keep in mind, I'll only function correctly if the URL is well-formed and follows 
	 * convention (i.e., forward-slash ("/") path separator, ampersand ("&") argument 
	 * separator, etc). If not, results will be wonky and you should parse the URL by 
	 * hand instead.
	 * 
	 * @param  string  $url  the url to parse
	 * @return  self
	 * @throws  InvalidArgumentException  if $url is not a string
	 * @throws  InvalidArgumentException  if $url is not a well-formed url
	 * @since   0.1.0
	 */
	public function parse($url)
	{
		// if $url is a string
		if (is_string($url)) {
			// if PHP can parse the url into its constituent parts...
			// parse_url() will return false if the url is mal-formed, and if a part is missing,
			//     its key-value will be omitted from the result array
			//
			$parts = parse_url($url);
			if ($parts !== false) {
				if (isset($parts['scheme'])) {
					$this->parseScheme($parts['scheme']);
				}
				
				if (isset($parts['user'])) {
					$this->parseUsername($parts['user']);
				}
				
				if (isset($parts['pass'])) {
					$this->parsePassword($parts['pass']);
				}
				
				if (isset($parts['host'])) {
					$this->parseHost($parts['host']);
				}
				
				if (isset($parts['port'])) {
					$this->parsePort($parts['port']);
				}
				
				if (isset($parts['path'])) {
					$this->parsePath($parts['path']);
				}
				
				if (isset($parts['query'])) {
					$this->parseQuery($parts['query']);
				}
				
				if (isset($parts['fragment'])) {
					$this->parseFragment($parts['fragment']);
				}
			} else {
				throw new \InvalidArgumentException(
					__METHOD__."() expects parameter one, url, to be a well-formed url"
				);
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, url, to be a string url"
			);
		}
		
		return $this;
	}

	
	/* !Protected methods */
	
	/**
	 * Appends the user's credentials to the url
	 *
	 * @param  string  $url  the url to append the credentials to
	 * @return  string
	 * @throws  InvalidArgumentException  if $url is not a string
	 * @since   0.1.0
	 */
	protected function appendCredentials($url)
	{
		if (is_string($url)) {
			if ( ! empty($this->username)) {
				$url .= $this->username;
				if ( ! empty($this->password)) {
					$url .= ':'.$this->password;
				}
				$url .= '@';
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, url, to be a string"
			);
		}
		
		return $url;
	}
	
	/**
	 * Appends the fragment to the url
	 *
	 * @param  string  $url  the url to append the fragment to
	 * @return  string
	 * @throws  InvalidArgumentException  if $url is not a string
	 * @since   0.1.0
	 */
	protected function appendFragment($url)
	{
		if (is_string($url)) {
			if ( ! empty($this->fragment)) {
				$url .= '#'.$this->fragment;
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, url, to be a string"
			);
		}
		
		return $url;
	}
	
	/**
	 * Appends the host to the url
	 *
	 * @param  string  $url  the url to append the host to
	 * @return  string
	 * @throws  InvalidArgumentException  if $url is not a string
	 * @since   0.1.0
	 */
	protected function appendHost($url)
	{
		if (is_string($url)) {
			if ( ! empty($this->host)) {
				$url .= $this->host;
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, url, to be a string"
			);
		}
		
		return $url;
	}
	
	/**
	 * Appends the path to the url
	 *
	 * @param  string  $url  the url to append
	 * @return  string
	 * @throws  InvalidArgumentException  if $url is not a string
	 * @since   0.1.0
	 */
	protected function appendPath($url)
	{
		if (is_string($url)) {
			// don't forget that path is a Path object
			// type-cast it to string to check if it's empty
			//
			if ( ! empty((string) $this->path)) {
				// if the url doesn't have a separator, add it
				if (substr($url, -1, 1) !== '/') {
					$url .= '/';
				}
				$url .= $this->path;
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, url, to be a string"
			);
		}
		
		return $url;
	}
	
	/**
	 * Appends the port to the url
	 *
	 * @param  string  $url  the url to append the port to
	 * @return  string
	 * @throws  InvalidArgumentException  if $url is not a string
	 * @since   0.1.0
	 */
	protected function appendPort($url)
	{
		if (is_string($url)) {
			if ( ! empty($this->port)) {
				$url .= ':'.$this->port;
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, url, to be a string"
			);
		}
		
		return $url;
	}
	
	/**
	 * Appends the query to $url
	 *
	 * @param  string  $url  the url to append the query to
	 * @return  string
	 * @throws  InvalidArgumentException  if $url is not a string
	 * @since   0.1.0
	 */
	protected function appendQuery($url)
	{
		if (is_string($url)) {
			// don't forget that query is a Query object
			// type-cast it to string to check if it's empty
			//
			if ( ! empty((string) $this->query)) {
				$url .= '?'.$this->query;
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, url, to be a string"
			);
		}
		
		return $url;
	}
	
	/**
	 * Appends the scheme to the url
	 *
	 * @param  string  $url  the url to append the scheme to
	 * @return  string
	 * @throws  InvalidArgumentException  if $url is not a string
	 * @since   0.1.0
	 */
	protected function appendScheme($url)
	{
		if (is_string($url)) {
			if ( ! empty($this->scheme)) {
				$url .= $this->scheme.'://';	
			}
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, url, to be a string"
			);
		}
		
		return $url;
	}
	
	/**
	 * Parses the url's fragment
	 *
	 * @param  string  $fragment  the url fragment to parse
	 * @return  self
	 * @throws  InvalidArgumentException  if $fragment is not a string
	 * @since   0.1.0
	 */
	protected function parseFragment($fragment)
	{
		if (is_string($fragment)) {
			if (substr($fragment, 0, 1) === '#') {
				$fragment = substr($fragment, 1);
			}
			$this->fragment = $fragment;
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, fragment, to be a string"
			);
		}
		
		return $this;
	}
	
	/**
	 * Parses the url's host
	 *
	 * @param  string  $host  the url's host
	 * @return  self
	 * @throws  InvalidArgumentException  if $host is not a string
	 * @since   0.1.0
	 */
	protected function parseHost($host)
	{
		if (is_string($host)) {
			$this->host = $host;	
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, host, to be a string"
			);
		}
		
		
		return $this;
	}
	
	/**
	 * Parses the url's password
	 *
	 * @param  string  $password  the url's password
	 * @return  self
	 * @throws  InvalidArgumentException  if $password is not a string
	 * @since   0.1.0
	 */
	protected function parsePassword($password)
	{
		if (is_string($password)) {
			$this->password = $password;	
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, password, to be a string url password"
			);
		}
		
		return $this;
	}
	
	/**
	 * Parses the url's path
	 *
	 * @param  string  $path  the url's path
	 * @return  self
	 * @throws  InvalidArgumentException  if $path is not a string
	 * @since   0.1.0
	 */
	protected function parsePath($path)
	{
		if (is_string($path)) {
			$this->path = new Path($path);
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, path, to be a string url path"
			);
		}
		
		return $this;
	}
	
	/**
	 * Parses the url's port
	 *
	 * @return  string|int  $port  the url's port
	 * @return  self
	 * @throws  InvalidArgumentException  if $port is not a string or int
	 * @since   0.1.0
	 */
	protected function parsePort($port)
	{
		if (is_string($port) || (is_numeric($port) && is_int(+$port) && $port > 0)) {
			$this->port = (int) $port;
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, port, to be a string or integer"
			);
		}
		
		return $this;
	}
	
	/**
	 * Parses the url's query string
	 *
	 * @param  string  $query  the url's query string
	 * @return  self
	 * @throws  InvalidArgumentException  if $query is not a string
	 * @throws  InvalidArgumentException  if $query is a string, but its key-value
	 *     pairs contain multiple assignment operators ("=") (i.e., the query's 
	 *     separator is not the default ampersand "&")
	 * @since   0.1.0
	 */
	protected function parseQuery($query)
	{
		if (is_string($query)) {
			$this->query = new Query($query);
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, query, to be a string"
			);
		}
		
		return $this;
	}
	
	/**
	 * Parses the url's scheme
	 *
	 * @param  string  $scheme  the url's scheme
	 * @return  self
	 * @throws  InvalidArgumentException  if $scheme is not a string
	 * @since   0.1.0
	 */
	protected function parseScheme($scheme) 
	{
		if (is_string($scheme)) {
			$this->scheme = $scheme;	
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, scheme, to be a string"
			);
		}
		
		return $this;
	}
	
	/**
	 * Parses the url's username
	 *
	 * @param  string  $username  the url's username
	 * @return  self
	 * @throws  InvalidArgumentException  if $username is not a string
	 * @since   0.1.0
	 */
	protected function parseUsername($username)
	{
		if (is_string($username)) {
			$this->username = $username;	
		} else {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, username, to be a string"
			);
		}
		
		return $this;
	}
}
