<?php

/*
 * This file is part of Polymorphine/CodeStandards package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$next   = 'next';
$object = new stdClass();
$object->first      = 1;
$object->second     = 2;
$object->thirdValue = 3;

$test = [];

name($x = 'test');
name($test[] = null);
name($foo[] = 'foo bar baz');

$var  = 'string';
$more = 123;
$arr['long key value'] = 'value';
$array['third']        = 5;
$arr[22] = 1;
$val = true;
$x = $next
    ? 1
    : 0;
$another = 10;
return $next = 400;


class Some
{
    const VAR      = '20';
    const VARIABLE = 'foo bar baz';
    public $var = 10;
    protected $some = 23;
    protected $x    = true;
    private $test = 22;

    public function foo($var)
    {
        $var[]     = function () { return 'Hello World!'; };
        $another[] = null;
        $here['leave'] = 'super';
        $here['something'] = function () {
            return 'Hello World!';
        };
    }

    public function func()
    {
        $this->foo($default = 'test');
        $this->foo($var = 1);
        $this->foo($longNameVariable = 2);
    }
}

function name($var)
{
}
