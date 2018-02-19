<?php

/*
 * This file is part of Polymorphine/CodeStandards package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\CodeStandards;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;


final class StaticNamedConstructorsBeforeConstructFixer extends AbstractFixer
{
    public function getName() {
        return 'Polymorphine/static_named_constructors_before_construct';
    }

    public function getDefinition() {
        return new FixerDefinition(
            'Static named constructors should be placed before construct method.',
            [new CodeSample("<?php...")]
        );
    }

    public function isCandidate(Tokens $tokens) {
        return $tokens->isAnyTokenKindsFound([T_STATIC]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens) {
        $idx = 0;

        while ($indexStatic = $tokens->getNextTokenOfKind($idx, [[T_STATIC]])) {
            $method = $this->extractMethod($indexStatic - 3, $tokens);
            $insert = $tokens->getNextTokenOfKind(0, [[T_FUNCTION]]) - 3;

            $tokens->insertAt($insert, $method);
            $idx = $indexStatic + 1;
        }
    }

    private function extractMethod($idx, Tokens $tokens) {
        $beginBlock = $tokens->getNextTokenOfKind($idx, ['{']);
        $endBlock = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $beginBlock);
        $method = [];

        while ($idx <= $endBlock) {
            $method[] = $tokens[$idx];
            $tokens->clearAt($idx);
            $idx++;
        }

        return Tokens::fromArray($method);
    }
}
