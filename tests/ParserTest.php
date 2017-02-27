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
     */
    protected function parseData($date)
    {
        $this->parsed = json_encode($this->parser->parse(serialize($date)));

        return $this->parsed;
    }

    /**
     * Parse a data structure from a serialized string and return the raw result.
     */
    protected function parseRow( $data )
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
      $this->parseRaw( 'unicode: ✔' );
      $this->assertSame( $this->parsed, 'unicode: ✔' );
    }
}
