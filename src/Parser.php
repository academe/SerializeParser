<?php namespace Academe\SerializeParser;

/**
 * The main parser.
 */

class Parser
{
    // Protected and private opbject attributes will have prefixes
    // to their names. We will strip those prefixes off by default.

    const PROTECTED_PREFIX = "\0*\0";
    const PRIVATE_PREFIX = "\0<s>\0";

    public function parse($string)
    {
        return $this->doParse(new StringReader($string));
    }

    protected function doParse($string)
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
                // Object: O:length:"class":length:{[attribute][value]...}
                $len = (int)$string->readUntil(':');

                // +2 for quotes
                $class = $string->read(2 + $len);

                // Eat the separator
                $string->read(1);

                // Do the attributes.
                // Initialise with the original name of the class.
                $attributes = ['__class_name' => $class];

                // Read the number of attribuites.
                $len = (int)$string->readUntil(':');

                // Eat "{" holding the attributes.
                $string->read(1);

                for($i=0; $i < $len; $i++) {
                    $attr_key = $this->doParse($string);
                    $attr_value = $this->doParse($string);

                    // Strip the protected and private prefixes from the names.
                    // Maybe replace them with something more usable, such as "protected:" and "private:"?

                    if (substr($attr_key, 0, strlen(self::PROTECTED_PREFIX)) == self::PROTECTED_PREFIX) {
                        $attr_key = substr($attr_key, strlen(self::PROTECTED_PREFIX));
                    }

                    if (substr($attr_key, 0, strlen(self::PRIVATE_PREFIX)) == self::PRIVATE_PREFIX) {
                        $attr_key = substr($attr_key, strlen(self::PRIVATE_PREFIX));
                    }

                    $attributes[$attr_key] = $attr_value;
                }

                // Eat "}" terminating attributes.
                $string->read(1);

                $val = (object)$attributes;

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
