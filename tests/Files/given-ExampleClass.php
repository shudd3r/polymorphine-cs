<?php
namespace Vendor\Package\Name;
use Some\Library;

use Another\Unused\Lib;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;


/**
 * Class MyClass summary
 *
 * Description hrere....
 *
 * @package Vendor\Package\Name
 */
abstract class MyClass
 implements SomeInterface {

    const CONSTANT = 'string';
    private $variable;

    public $field = array(
        'key' => 1,
        'other' => 'value',
    );
    private $bool = TRUE;

    private function getVar() {
        if (empty($this->variable)) { $this->variable = 'empty!'; }
        return $this->variable;
    }
    public abstract function somethingAbstract();
    public static function withHelloString() {
        return new self('Hello World!');
    }


    protected function getVar2() {
        empty($this->variable) or $this->variable = 'empty!';
        return $this->variable;
    }public function getVariable() {
        return empty($this->variable)
            ? (string)$this->variable = 'empty!'.'string'
            : $this->variable;
    }


    /**
     * Creates from array.
     * @param array $arr
     * @return MyClass
     */
    public static function fromArray(array $arr): self {
        return new self(implode('.', $arr));
    }

    /**
     * MyClass constructor.
     *
     * @param string $variable
     */
    public function __construct(string $variable = '')
    {
        $this->variable = $variable;
    }

    public function fixer(
        ArraySyntaxFixer $fixer, Library $library) {
        $this->field = function () use ($fixer) { return $this->getVar(); };
        $this->variable = $library;
        return;
    }
}
