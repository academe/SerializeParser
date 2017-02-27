<?php

namespace Academe\SerializeParser;

use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    protected $parser;
    protected $parsed;

    public function setUp()
    {
        // Set up a new parser for each test method.
        $this->parser = new Parser();
    }

    /**
     * Parse a data structre as a serialized string and return a JSON encoded version
     * of the resulting structure.
     * The JSON encoding makes it easier to apply assertions.
     *
     * @param $data mixed Any data structure.
     * @returns string JSON encoded parsed data
     */
    protected function parseData($data)
    {
        $this->parsed = json_encode($this->parser->parse(serialize($data)));

        return $this->parsed;
    }

    /**
     * Parse a data structure from a serialized string and return the raw result.
     */
    protected function parseRaw( $data )
    {
      return $this->parsed = $this->parser->parse( serialize( $data ) );
    }

    /**
     * Parse a simple string.
     */
    public function testParseSimpleString()
    {
        $this->parseData('123');
        $this->assertSame($this->parsed, '"123"');
    }

    /**
     * Parse simple array.
     */
    public function testParseSimpleArray()
    {
        $this->parseData(['123']);
        $this->assertSame($this->parsed, '["123"]');
    }

    /**
     * Parse simple tuple array.
     */
    public function testParseSimpleTupleArray()
    {
        $this->parseData(['abc' => '123']);
        $this->assertSame($this->parsed, '{"abc":"123"}');
    }

    /**
     * Parse a UTF-8 encoded Unicode string.
     */
    public function testUtf8UnicodeStringTick()
    {
      $this->parseRaw('unicode: ✔');
      $this->assertSame($this->parsed, 'unicode: ✔');
    }

    /**
     * Parse a UTF-8 encoded Unicode string.
     */
    public function testUtf8UnicodeStringTickQuoted()
    {
      $this->parseRaw('"unicode: ✔"');
      $this->assertSame($this->parsed, '"unicode: ✔"');
    }

    /**
     * An empty string with just "nothing" quoted.
     */
    public function testEmptyString()
    {
      $this->parseRaw('');
      $this->assertSame($this->parsed, '');
    }

    /**
     * An empty string with just "nothing" quoted.
     */
    public function testEmptyStringQuoted()
    {
      $this->parseRaw('""');
      $this->assertSame($this->parsed, '""');
    }

    /**
     * An string with multiple quotes.
     */
    public function testSingleQuoteString()
    {
      $this->parseRaw('"');
      $this->assertSame($this->parsed, '"');
    }
}
