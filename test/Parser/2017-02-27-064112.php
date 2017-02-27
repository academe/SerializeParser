<?php

require_once __DIR__ . '/../include.php';

$input = 'unicode: ✔';

$parser = new Academe\SerializeParser\Parser();

$output = $parser->parse( serialize( $input ) );

assert( '$input === $output', "'$input' === '$output'" );


