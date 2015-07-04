<?php namespace Academe\SerializeParser;

/**
 * Given a string, this class will the string to be scanned
 * through using a number of terminating rules:
 * - One character.
 * - A specified number of characters.
 * - Until a matching character is found.
 */

class StringReader
{
    protected $pos = 0;
    protected $max = 0;
    protected $string = [];

    public function __construct($string)
    {
        // Split the string up into an array of UTF-8 characters.
        // As an array we can read through it one character at a time.

        $this->string = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
        $this->max = count($this->string) - 1;
    }

    /**
     * Read the next character from the supplied string.
     * Return null when we have run out of characters.
     */
    public function readOne()
    {
        if ($this->pos <= $this->max) {
            $value = $this->string[$this->pos];
            $this->pos += 1;
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Read characters until we reach the given character $char.
     * By default, discard that final matching character and return
     * the rest.
     */
    public function readUntil($char, $discard_char = true)
    {
        $value = '';

        while(null != ($one = $this->readOne())) {
            if ($one != $char || !$discard_char) {
                $value .= $one;
            }

            if ($one == $char) {
                break;
            }
        }

        return $value;
    }

    /**
     * Read $count characters until or until we have reaced the end.
     * By default, remove enclosing double-quotes from the result.
     */
    public function read($count, $strip_quotes = true)
    {
        $value = '';

        while($count > 0 && null != ($one = $this->readOne())) {
            $value .= $one;
            $count -= 1;
        }

        return $strip_quotes ? $this->stripQuotes($value) : $value;
    }

    public function stripQuotes($string)
    {
        // FIXME: only remove exactly one quote from the start and the end.
        return trim($string, '"');
    }
}
