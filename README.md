# SerializeParser
A PHP parser for serialized data, to be able to "peek" into serialize strings.

## Purpose

I had a bunch of PHP serialized data in the datanase, and needed to peek into
that data for presenting information to the administrator. There appeared to be
no way to interpret this data in PHP without instantiating all the objects that
were in that data, and I did not want to do that.

So here we are, a simple parser that takes a serialized data string, and tries
to deserialize it, but replacing all objects with a `stdClass` so we are not
instantiating classes that have no business being instantiated, or classes that
may not even exist in the application.

## How to Use

Here is a simple example:

~~~php
// Create a complex array/object/string/number/boolean to serialize.
$obj = new  \Academe\SerializeParser\StringReader('xyz');
$obj->foo = true;
$data = ['a' => 1, ['foo' => 'bar', $obj, 'b' => false];
$serialized = serialize($data);

echo $serialized;
//a:3:{s:1:"a";i:1;i:0;a:2:{s:3:"foo";s:3:"bar";i:0;O:36:"Academe\SerializeParser\StringReader":4:{s:6:"*pos";i:0;s:6:"*max";i:2;s:9:"*string";a:3:{i:0;s:1:"x";i:1;s:1:"y";i:2;s:1:"z";}s:3:"foo";b:1;}}s:1:"b";b:0;}

// Now parse it to look what it inside, without instantiating the
// original objects in it.
$parser = new \Academe\SerializeParser\Parser;
$parsed = $parser->parse($serialized);

var_dump($parsed);

/*
array(3) {
  ["a"]=>
  int(1)
  [0]=>
  array(2) {
    ["foo"]=>
    string(3) "bar"
    [0]=>
    object(stdClass)#5 (5) {
      ["__class_name"]=>
      string(36) "Academe\SerializeParser\StringReader"
      ["pos"]=>
      int(0)
      ["max"]=>
      int(2)
      ["string"]=>
      array(3) {
        [0]=>
        string(1) "x"
        [1]=>
        string(1) "y"
        [2]=>
        string(1) "z"
      }
      ["foo"]=>
      bool(true)
    }
  }
  ["b"]=>
  bool(false)
}
*/
~~~

Note that the `StringReader` class has been unserialized as `stdClass` and the original
name moved to attribute `__class_name`. The protected and private attributes are all
also present and accessible.

Remember, the purpose of this is not to reconstruct the original data as an accurate
representation. It is to allow the data to be inspected and some key values pulled out
for logging, showing to the user etc.

## TODO

* Tests.
* In-code documentation.
* Maybe make the `Parser::parse()` method static.
* Make the StringReader a little more efficient. Efficiency was not key in getting this working.

## Want to Help?

If you fancy writing some tests, have found a bug, or can extend it to handle more
cases of serialized data, then please feel free to get involved. PRs, issues, or just
email me - whatever you like.

## Source Specification

The only source specification for how serialization works, is the PHP source code.
However, there are a number of serialized parsers written for langauegs other than
PHP that work, and have been derived from that code. I have ported this code from
some of those packages.

This is not complete, so will not handle references for example, but does enough of
the simple stuff for my needs.

This has a handy algorithm in Python:

https://github.com/jqr/php-serialize/blob/master/lib/php_serialize.rb#L195

There is a pretty good descriptino of some of the intricaces here:

http://stackoverflow.com/questions/14297926/structure-of-a-serialized-php-string

My origional SO question that led me to write my own solution:

http://stackoverflow.com/questions/31219158/analyse-parse-a-serialized-php-data-containining-objects/31223873

