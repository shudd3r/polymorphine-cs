<?php

/*
 * This file is part of Polymorphine/CodeStandards package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\CodeStandards\Tests\Fixer;

use PHPUnit\Framework\TestCase;
use Polymorphine\CodeStandards\Fixer\DoubleLineBeforeClassDefinitionFixer;


class DoubleLineBeforeClassDefinitionFixerTest extends TestCase
{
    use FixerTestMethods;

    protected function setUp(): void
    {
        $this->setRunner(new DoubleLineBeforeClassDefinitionFixer());
    }

    public function testTwoEmptyLinesAreInsertedBeforeClassDefinition()
    {
        $code = $this->code(
            <<<'PHP'
            
            namespace Some\Package;
            class ExampleClass
            {
                private $self;
            
                public function __construct(ExampleClass $self)
                {
                    $this->self = $self;
                }
            }

            PHP
        );

        $expected = $this->code(
            <<<'PHP'
            
            namespace Some\Package;
            
            
            class ExampleClass
            {
                private $self;
            
                public function __construct(ExampleClass $self)
                {
                    $this->self = $self;
                }
            }

            PHP
        );

        $this->assertSame($expected, $this->runner->fix($code));
    }
}
