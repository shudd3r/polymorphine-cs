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

use Some\Library;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;


/**
 * Class MyClass summary.
 *
 * Description hrere....
 */
abstract class ExampleClass implements SomeInterface
{
    const CONSTANT = 'string';

    public $field = [
        'key'   => 1,
        'other' => 'value'
    ];
    private $variable;
    private $bool = true;

    /**
     * MyClass constructor.
     *
     * @param string $variable
     */
    public function __construct(string $variable = '')
    {
        $this->variable = $variable;
    }

    public static function withHelloString()
    {
        return new self('Hello World!');
    }

    /**
     * Creates from array.
     *
     * @param array $arr
     *
     * @return MyClass
     */
    public static function fromArray(array $arr): self
    {
        return new self(implode('.', $arr));
    }

    abstract public function somethingAbstract();

    public function getVariable()
    {
        return empty($this->variable)
            ? (string) $this->variable = 'empty!' . 'string'
            : $this->variable;
    }

    public function fixer(
        ArraySyntaxFixer $fixer,
        Library $library
    ) {
        $this->field    = function () use ($fixer) { return $this->getVar(); };
        $this->variable = $library;
    }

    protected function getVar2()
    {
        empty($this->variable) or $this->variable = 'empty!';
        return $this->variable;
    }

    private function getVar()
    {
        if (empty($this->variable)) {
            $this->variable = 'empty!';
        }
        if ($maxCommands < 0 || $maxSpaces < 0 && $more80charsLinex) {
            return;
        }
        if ($maxCommands < 0 || $maxSpaces < 0 && $less81charsLine) { return; }
        if ($moreLines) {
            unset($x);
            return;
        }
        //4 whitespaces in body
        if ($notShortStatement) {
            return $this->call($arg, $arg2);
        }
        //3 whitespaces in body
        if ($oneArgumentMethod) { return $this->callLongerMethodName($arg); }
        if ($twoArgumentMethod) { $this->commandMethodName($arg, $arg2); }
    }
}
