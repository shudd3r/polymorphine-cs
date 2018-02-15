<?php

/**
 * This file is part of Polymorphine/CodeStandards package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\CodeStandards;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;


final class BraceAfterFunctionFixer implements DefinedFixerInterface
{
    public function getName() {
        return 'Polymorphine/brace_after_method';
    }

    public function isCandidate(Tokens $tokens) {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_FUNCTION]);
    }

    public function getDefinition() {
        return new FixerDefinition(
            'Method definition opening brace MUST go on same line.',
            [
                new CodeSample(
                    '<?php

final class Example
{
    public function example() 
    {
    }
}
'
                )
            ]
        );
    }

    public function isRisky(): bool {
        return false;
    }

    public function supports(SplFileInfo $file): bool {
        return true;
    }

    public function getPriority(): int {
        return -40;
    }

    public function fix(SplFileInfo $file, Tokens $tokens) {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $newlineIndex = $tokens->getNextTokenOfKind($index, ['{']) - 1;

            $tokens[$newlineIndex] = new Token([T_WHITESPACE, ' ']);
        }
    }
}
