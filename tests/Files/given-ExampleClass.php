<?php
namespace Vendor\Package\Name;
use Some\Library;

use Another\Unused\Lib;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;



final class MyClass
 implements SomeInterface {

    const CONSTANT = 'string';
    private $variable;
    public $field = array(
        'key' => 1,
        'other' => 'value',
    );
    private $bool = TRUE;

    public function __construct(string $variable = '')
    {
        $this->variable = $variable;
    }

    private function getVar() {
        if (empty($this->variable)) { $this->variable = 'empty!'; }
        return $this->variable;
    }
    private function getVar2() {
        empty($this->variable) or $this->variable = 'empty!';
        return $this->variable;
    }

    public function GetVariable() {
        return empty($this->variable)
            ? (string)$this->variable = 'empty!'.'string'
            : $this->variable;
    }

    public static function fromArray(array $arr): self {
        return new self(implode('.', $arr));
    }

    public function Fixer(
        ArraySyntaxFixer $fixer, Library $library) {
        $this->field = function () use ($fixer) { return $this->getVar(); };
        $this->variable = $library;
        return;
    }
}
