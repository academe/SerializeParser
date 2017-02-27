<?php

namespace Academe\SerializeParser;

use PHPUnit\Framework\TestCase;

class StringReaderTest extends TestCase
{
    /**
     * Read a three character simple string, one character at a time.
     */
    public function testReadOneTwoThree()
    {
        $reader = new StringReader('123');

        $one = $reader->readOne();
        $two = $reader->readOne();
        $three = $reader->readOne();
        $four = $reader->readOne();

        $this->assertSame($one, '1');
        $this->assertSame($two, '2');
        $this->assertSame($three, '3');
        $this->assertSame($four, null);
    }
}
