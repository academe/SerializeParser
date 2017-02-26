<?php

require_once __DIR__ . '/../include.php';

$a = 'unicode: ✔';

$parser = new Academe\SerializeParser\Parser();

$data = $parser->parse( serialize( $GLOBALS ) );

