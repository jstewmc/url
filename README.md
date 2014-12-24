Url
===

A class to create, parse, and manipulate Uniform Resource Locators (URL).

A Uniform Resource Locator (URL) is a string that identifies a resource and describes how to locate it. 

Most of the time, PHP (or your PHP framework) will handle creating, parsing, and manipulating URLs for you. However, every once in a while, you'll need to work with a URL manually. 

I've always found working with a URL as a string can be a little cumbersome. So, I created a URL class:

```php
// create a url
$url = new Url();

$url->setScheme('http');
$url->setHost('example.com');
$url->setPath('foo/bar');
$url->setQuery('baz=qux');

echo $url;  // prints "http://example.com/foo/bar?baz=qux"
```

```php
// parse a url
$url = new Url('http://example.com/foo/bar?baz=qux');

echo $url->getScheme();                      // prints 'https'
echo $url->getHost();                        // prints 'example.com'
echo $url->getPath();                        // prints 'foo/bar'
echo $url->getPath()->getSegment(1);         // prints 'bar'
echo $url->getQuery();                       // prints 'baz=qux'
echo $Url->getQuery()->getParameter('baz');  // prints 'qux'
```

```php
// manipulate a url
$url = new Url('http://example.com/foo/bar?baz=qux');

$url->getPath()->reverse()->prependSegment('qux')->insertSegment('quux', 2);

echo $url;  // prints "http://example.com/qux/bar/quux/foo?baz=qux

$url->getQuery()->unsetParameter('baz')->setParameter('quux', 'corge');

echo $url;  // prints "http://example.com/qux/bar/quux/foo?quux=corge
```

These examples are a little contrived (a string might be faster for some of these) and verbose (you can chain most of the methods). However, I think you get the point.

Feel free to check out the [API documentation](https://jstewmc.github.io/url/api/0.1.1), [report an issue](https://github.com/jstewmc/url/issues), [contribute](https://github.com/jstewmc/url/blob/master/CONTRIBUTING.md), or [ask a question](mailto:clayjs0@gmail.com). 

Url
---

This class is based on (and makes use of) PHP's [parse_url()](http://php.net/manual/en/function.parse-url.php) function.

As far as this class (and that function) are concerned, a URL is composed of the following parts:

- `scheme` - the protocol (e.g., `http` or `https`)
- `username` - a username for authentication
- `password` - a password for authentication
- `host` - the domain or IP address (e.g., `example.com` or `123.123.123.123`)
- `port` - the server's port (e.g., `80` for `http` or `443` for `https`)
- `path` - the file's path on the server (e.g., `path/to/file`)
- `query` - a string of key-value pairs (aka, `?foo=bar`) 
- `fragment` - an anchor within the page (e.g., `#example`)

Putting it all together:

```
https://username:password@example.com:8080/path/to/file?key1=value1#fragment
-------|--------|--------|-----------|----|------------|-----------|--------
scheme |username|password|host       |port|path        |query      |fragment
```

Format
------

As far as this class is concerned, URLs come in two formats: *relative* and *absolute*. An *absolute* URL includes all its non-empty parts. On the other hand, a *relative* URL includes the URL's path, query, and fragment.

When used as a string, this class will return its *absolute* URL. However, you can use the format() method to return its *relative* URL:

```php
$url = new Url();
$url
	->setScheme('http')
	->setHost('example.com')
	->setPort('1234')
	->setPath('foo/bar/baz')
	->setQuery('qux=quux')
	->setFragment('corge');

echo $url;                      // prints "http://example.com:1234/foo/bar/baz?qux=quux#corge
echo $url->format('absolute');  // prints "http://example.com:1234/foo/bar/baz?qux=quux#corge
echo $url->format('relative');  // prints "/foo/bar/baz?qux=quux#corge"
```

Case 
----

In line with [W3 guidelines](http://www.w3.org/TR/WD-html40-970708/htmlweb.html), the Url class is case-sensitive. According to the W3:

> URLs in general are case-sensitive (with the exception of machine names). There may be URLs, or parts of URLs, where case doesn't matter, but identifying these may not be easy. Users should always consider that URLs are case-sensitive.

Scheme, Host, Port, and Fragment
--------------------------------

The Url's `scheme`, `host`, `port`, and `fragment` are simple strings: 

```php
// create a url
$url = new Url();
$url
	->setScheme('http')
	->setHost('example.com')
	->setPort('1234')
	->setFragment('foo');

echo $url;  // prints "http://example.com:1234#foo"

// parse a url
$url = new Url('http://example.com:1234#foo');

echo $url->getScheme();    // prints 'http'
echo $url->getHost();      // prints 'example.com' 
echo $url->getPort();      // prints '1234'
echo $url->getFragment();  // prints 'foo'
```

Path
----

The Url's `path` can be treated as a string or Path object:

```php
// set the path as a string
$url = new Url();
$url->setScheme('http')
	->setHost('example.com')
	->setPath('foo/bar');

echo $url;  // prints "http://example.com/foo/bar"

// set the path as an array of segments
$url = new Url();
$url->setScheme('http')
	->setHost('example.com')
	->getPath()
		->setSegments(['foo', 'bar']);

echo $url;  // prints "http://example.com/foo/bar"

// set the path's segments one-by-one
$url = new Url();
$url->setScheme('http')
	->setHost('example.com')
	->getPath()
		->appendSegment('foo')
		->appendSegment('bar');

echo $url;  // prints "http://example.com/foo/bar"
```

A path is composed of segments. For example, the path `foo/bar/baz` has three segments: `foo`, `bar`, and `baz`. 

Segments are indexed started with 0. So, in the path `foo/bar/baz`, the index of `foo` is 0. The index of `bar` is 1, and the index of `baz` is 2. 

Most methods that use a segment's index as an argument will accept an offset. An offset can be positive (that many places from the beginning of the path) or negative (that many places from the end of the path). In addition, most methods accept the special strings `first` and `last`.

You can append, prepend, insert, set, and unset a path's segments:

```php
$url = new Url();
$url->setScheme('http')
	->setHost('example')
	->getPath()
		->appendSegment('foo')     // path is "foo"
		->prependSegment('bar')    // path is "bar/foo"
		->insertSegment('baz', 1)  // path is "bar/baz/foo"
		->setSegment(-1, 'qux')    // path is "bar/baz/qux"
		->unsetSegment('last');    // path is "bar/baz"
```

You can also get, find, and verify a segment by value or offset:

```php
$url  = new Url("http://example.com/foo/bar/baz");
$path = $url->getPath();

echo $path;  // prints "foo/bar/baz"

// get the index of the 'foo' segment
$path->getIndex('foo');  // returns 0
$path->getIndex('qux');  // returns false

// get the value of the 0-th (aka, 'first') segment
$path->getSegment(0);        // returns 'foo'
$path->getSegment('first');  // returns 'foo'

// does the path have a segment at the 1-st index?
$path->hasIndex(1);   // returns true
$path->hasIndex(10);  // returns false

// does the path have the given segments at any index?
$path->hasSegment('bar');  // returns true
$path->hassegment('qux');  // returns false

// does the path have the given segments at the given indices?
$path->hasSegment('foo', 0);        // returns true
$path->hasSegment('foo', 'first');  // returns true
$path->hasSegment('qux', 'last');   // returns false
```

Finally, you can slice and reverse a path:

```php
$url  = new Url("http://example.com/foo/bar/baz");
$path = $url->getPath();

echo $path;  // prints "foo/bar/baz"

// get a slice (as a new Path) from the 1-st index to the end
$path->getSlice(1);  // returns ['bar', 'baz']

// get a slice (as a new Path) from the 1-st index for one segment
$path->getSlice(1, 1);  // returns ['bar']

// slice the path itself
$path->slice(1, 1);
echo $path;  // prints "bar"

// get a new, reversed Path
$reverse = $path->getReverse();
echo $reverse;  // prints "baz/bar/foo"

// reverse the path itself
$path->reverse();
echo $path;  // prints "baz/bar/foo"
```

The Url class depends on my [Path class](https://github.com/jstewmc/path). See that README.md for details.

Query
-----

A query can be treated as a string or Query object:

```php
// set the query as a string
$url = new Url();
$url->setScheme('http')
	->setHost('example.com')
	->setQuery('foo=bar&baz=qux');

echo $url;  // prints "http://example.com?foo=bar&baz=qux"

// set the query's parameters as an array
$url = new Url();
$url->setScheme('http')
	->setHost('example.com')
	->getQuery()
		->setParameters(['foo' => 'bar', 'baz' => 'qux']);

echo $url;  // prints "http://example.com?foo=bar&baz=qux"

// finally, you can set the query's parameters one-by-one
$url = new Url();
$url->setScheme('http')
	->setHost('example.com')
	->getQuery()
		->setParameter('foo', 'bar')
		->setParameter('baz', 'qux');

echo $url;  // prints "http://example.com?foo=bar&baz=qux"
```

A query is composed of parameters. For example, in the query `foo=bar&baz=qux`, there are two parameters, `foo` and `baz`. The value of `foo` is `bar`, and the value of `baz` is `qux`. A query is commonly thought of as a list of key-value pairs.

Unlike a path, where order matters, order does not matter in a query string. A parameter is there, or it is not.

You can set or unset a query's parameters:

```php
$url   = new Url();
$url
	->setScheme('http')
	->setHost('example.com')
	->getQuery()
		->setParameter('foo', 'bar')
		->setParameter('baz', 'qux');

echo $url;  // prints "http://example.com?foo=bar&baz=qux"

$url->getQuery()->unsetParameter('foo');

echo $url;  // prints "http://example.com?baz=qux"
```

You can also get or verify a parameter:

```php
$url   = new Url('http://example.com?foo=bar&baz=qux');
$query = $url->getQuery();

// does the query have the given parameters?
$query->hasParameter('foo');    // returns true
$query->hasParameter('corge');  // returns false

// what is the value of the given parameters?
$query->getParameter('foo');  // returns 'foo'
$query->getParameter('baz');  // returns 'qux' 
$query->getParameter('qux');  // throws OutOfBoundsException
```

Separators
----------

The Url class parses paths and queries under the assumption that you're using the default separators: the forward-slash character ("/") for paths and the ampersand character ("&") for query parameters. 

If you are using a different parameter, you should avoid parsing the url on instantiation. Instead, you should set the separators manually and then parse the url:

```php
$string = 'http://example.com|foo|bar|baz?foo=bar;baz=qux';

// notice the pipe character ("|") as the path separator (for some reason), and
//     notice the semi-colon character (";") as the argument separator
//

$url = new Url($string);  // this will not work!

// instead, set your separators and call the parse() method manually

$url = new Url();
$url->getPath()->setSeparator('|');
$url->getQuery()->setSeparator(';');
$url->parse($string);  
``` 

Customizing
-------------

In most of the examples above, I have to set the `scheme` and `host` explicitly every time. That can get annoying.

One solution is to extend the `Url` class to create a `MyUrl` class with your default settings:

```php
class MyUrl extends Url
{
	/* !Protected properties */
	
	/**
     * @var  string  my default host name
     */
	protected $host = 'mydomain.com';
	
	/**
     * @var  string  my default scheme
     */
	protected $scheme = 'https';
	
	/**
     * @var  string  my non-standard default https port
     */
    protected $port = '12345';    
}

$url = new MyUrl();
$url->setPath('foo/bar');

echo $url;  // prints "https://mydomain.com:12345/foo/bar"
```

One-liner
---------

Of course, we wouldn't be cool if we couldn't whip out a cryptic one-liner (using PHP 5.4+ method chaining):

```php
// print "http://example.com" in one line
echo (new Url())->setScheme('http')->setHost('example.com');
```

Tests
-----

I've written unit tests with an average of 93% code coverage. I'm still learning how to write great tests. So, feel free to check them out and tell me what you think.

Contributing
------------

Feel free to contribute your own improvements:

1. Fork
2. Clone
3. PHPUnit
4. Branch
5. PHPUnit
6. Code
7. PHPUnit
8. Commit
9. Push
10. Pull request
11. Relax and eat a Paleo muffin

See [contributing.md](https://github.com/jstewmc/url/blob/master/CONTRIBUTING.md) for details.

## Author

Jack Clayton - [clayjs0@gmail.com](mailto:clayjs0@gmail.com).

## License

Url is released under the MIT License. See the [LICENSE](https://github.com/jstewmc/url/blob/master/LICENSE) file for details.

## History

You can view the (short) history of the Url project in the [changelog.md](https://github.com/jstewmc/url/blob/master/CHANGELOG.md) file.

