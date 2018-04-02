<?php

/*
 * This file is part of Polymorphine/CodeStandards package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vendor\Package\Name;

use Some\Name;
use ArrayAccess;
use Exception;


class ValidClass implements ArrayAccess
{
    const TEST = 1;

    protected $inheritedValues = [
        'key'       => 'value',
        'longerKey' => 'another value'
    ];

    protected $something;

    private $variable;
    private $anotherVariable = [];

    public function __construct(string $something, Name $variable)
    {
        $this->something = $something;
        $this->variable  = $variable;
    }

    public static function withSomething(Name $variable)
    {
        return new self('string', $variable);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->anotherVariable);
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new Exception('No such key');
        }
        return $this->anotherVariable[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->anotherVariable[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->anotherVariable[$offset]);
    }

    private function someMethod($var)
    {
        if (!$var) {
            return null;
        }

        $var .= ' extended';
    }
}
