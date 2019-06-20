<?php

/*
 * LOL surprise comment!
 */
$x = $argv[0] ?? 'command';

if ($x === 'command')
{
    echo 'Allman style?';
}
else
{
    //Try something else (indentation unfixed)
}

function doSomething($x=3,$a=10)
{
    return $x+$a;
}

