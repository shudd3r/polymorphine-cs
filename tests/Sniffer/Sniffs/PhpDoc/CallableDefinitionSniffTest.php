<?php

/*
 * This file is part of Polymorphine/CodeStandards package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\CodeStandards\Tests\Sniffer\Sniffs\PhpDoc;

use Polymorphine\CodeStandards\Tests\SnifferTest;
use Polymorphine\CodeStandards\Sniffer\Sniffs\PhpDoc\CallableDefinitionSniff;


class CallableDefinitionSniffTest extends SnifferTest
{
    /**
     * @dataProvider properties
     *
     * @param array $properties
     * @param int[] $expectedWarningLines
     */
    public function testCallableParamDocWithoutDefinitionGivesWarning(array $properties, array $expectedWarningLines)
    {
        $this->setProperties($properties);
        $this->assertWarningLines('./tests/Files/Sniffs/PhpDocCallableDefinitions.php', $expectedWarningLines);
    }

    public function properties()
    {
        return [
            [['syntax' => 'both', 'includeClosure' => false], range(15, 18)],
            [['syntax' => 'both', 'includeClosure' => true], range(15, 22)],
            [['syntax' => 'short', 'includeClosure' => false], [15, 16, 17, 18, 27, 28]],
            [['syntax' => 'long', 'includeClosure' => false], [15, 16, 17, 18, 23, 24]],
            [['syntax' => 'short', 'includeClosure' => true], [15, 16, 17, 18, 19, 20, 21, 22, 27, 28, 29, 30]],
            [['syntax' => 'long', 'includeClosure' => true], range(15, 26)]
        ];
    }

    protected function sniffer(): string
    {
        return CallableDefinitionSniff::class;
    }
}
