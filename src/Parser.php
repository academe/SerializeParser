<?php namespace Academe\SerializeParser;

/**
 * The main parser.
 */

class Parser
{
    // Protected and private opbject properties will have prefixes
    // to their names. We will strip those prefixes off by default.

    const PROTECTED_PREFIX = "\0*\0";

    /**
     * Parse a string containing a serialized data structure.
     * This is the initial entry point into the recursive parser.
     * FIXME: work out some way the string reader can be mocked.
     */
    public function parse($string)
    {
        return $this->doParse(new StringReader($string));
    }

    /**
     * This is the recursive parser.
     */
    protected function doParse(StringReader $string)
    {
        $val = null;

        // May be : or ; as a terminator, depending on what the data
        // type is.

        $type = substr($string->read(2), 0, 1);

        switch ($type) {
            case 'a':
                // Associative array: a:length:{[index][value]...}
                $count = (int)$string->readUntil(':');

                // Eat the opening "{" of the array.
                $string->read(1);

                $val= [];
                for($i=0; $i < $count; $i++) {
                    $array_key = $this->doParse($string);
                    $array_value = $this->doParse($string);

                    $val[$array_key] = $array_value;
                }

                // Eat "}" terminating the array.
                $string->read(1);

                break;

            case 'O':
                // Object: O:length:"class":length:{[property][value]...}
                $len = (int)$string->readUntil(':');

                // +2 for quotes
                $class = $string->read(2 + $len);

                // Eat the separator
                $string->read(1);

                // Do the properties.
                // Initialise with the original name of the class.
                $properties = ['__class_name' => $class];

                // Read the number of properties.
                $len = (int)$string->readUntil(':');

                // Eat "{" holding the properties.
                $string->read(1);

                for($i=0; $i < $len; $i++) {
                    $prop_key = $this->doParse($string);
                    $prop_value = $this->doParse($string);

                    // Strip the protected and private prefixes from the names.
                    // Maybe replace them with something more informative, such as "protected:" and "private:"?

                    if (substr($prop_key, 0, strlen(self::PROTECTED_PREFIX)) == self::PROTECTED_PREFIX) {
                        $prop_key = substr($prop_key, strlen(self::PROTECTED_PREFIX));
                    }

                    if (substr($prop_key, 0, 1) == "\0") {
                        list(, $private_class, $private_property_name) = explode("\0", $prop_key);

                        $prop_key = $private_property_name;
                    }

                    $properties[$prop_key] = $prop_value;
                }

                // Eat "}" terminating properties.
                $string->read(1);

                $val = (object)$properties;

                break;

            case 's':
                $len = (int)$string->readUntil(':');
                $val = $string->read($len + 2);

                // Eat the separator
                $string->read(1);
                break;

            case 'i':
                $val = (int)$string->readUntil(';');
                break;

            case 'd':
                $val = (float)$string->readUntil(';');
                break;

            case 'b':
                // Boolean is 0 or 1
                $bool = $string->read(2);
                $val = substr($bool, 0, 1) == '1';
                break;

            case 'N':
                $val = null;
                break;

            default:
                throw new \Exception(sprintf('Unable to unserialize type "%s"', $type));
        }

        return $val;
    }
}
