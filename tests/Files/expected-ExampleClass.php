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


final class ExampleClass implements SomeInterface
{
    const CONSTANT = 'string';
    public $field = [
        'key' => 1,
        'other' => 'value'
    ];
    private $variable;
    private $bool = true;

    public static function fromArray(array $arr): self {
        return new self(implode('.', $arr));
    }

    public function __construct(string $variable = '') {
        $this->variable = $variable;
    }

    public function GetVariable() {
        return empty($this->variable)
            ? (string) $this->variable = 'empty!' . 'string'
            : $this->variable;
    }

    public function Fixer(
        ArraySyntaxFixer $fixer,
        Library $library
    ) {
        $this->field = function () use ($fixer) { return $this->getVar(); };
        $this->variable = $library;
    }

    private function getVar() {
        if (empty($this->variable)) {
            $this->variable = 'empty!';
        }

        return $this->variable;
    }

    private function getVar2() {
        empty($this->variable) or $this->variable = 'empty!';

        return $this->variable;
    }
}
