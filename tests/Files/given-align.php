<?php
$next = 'next';
$object = new stdClass();
$object->first = 1;
$object->second = 2;
$object->thirdValue = 3;

$var = 'string';
$more = 123;
$arr['long key value'] = 'value';
$array['third'] = 5;
$arr[22] = 1;
$val = true;
$x = $next
    ? 1
    : 0;
$another = 10;
return $next = 400;

class Some
{
    const VAR = '20';
    public $var = 10;
    const VARIABLE = 'foo bar baz';
    protected $some = 23;
    private $test = 22;
    protected $x = true;

    public function foo()
    {
        $var[] = function () { return 'Hello World!'; };
        $another[] = null;
        $here['leave'] = 'super';
        $here['something'] = function () {
            return 'Hello World!';
        };
    }
}
