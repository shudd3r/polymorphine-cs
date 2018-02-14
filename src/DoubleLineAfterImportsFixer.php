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
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use SplFileInfo;


final class DoubleLineAfterImportsFixer implements DefinedFixerInterface
{
    public function getName() {
        return 'PolymorphineCS/double_line_after_imports';
    }

    public function isCandidate(Tokens $tokens) {
        return $tokens->isAnyTokenKindsFound([T_USE, T_CLASS]);
    }

    public function getDefinition() {
        return new FixerDefinition(
            'Each namespace use MUST go on its own line and there MUST be exactly two blank lines after the use statements block.',
            [
                new CodeSample(
                    '<?php
namespace Foo;

use Bar;
use Baz;
final class Example
{
}
'
                ),
                new CodeSample(
                    '<?php
namespace Foo;

use Bar;
use Baz;

final class Example
{
}
'
                ),
                new CodeSample(
                    '<?php
namespace Foo;

use Bar;
use Baz;



final class Example
{
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
        return 0;
    }

    public function fix(SplFileInfo $file, Tokens $tokens) {
        $ending = "\n";
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        $added = 0;
        foreach ($tokensAnalyzer->getImportUseIndexes() as $index) {
            $index += $added;
            $indent = '';

            $semicolonIndex = $tokens->getNextTokenOfKind($index, [';']);
            $insertIndex = $semicolonIndex;

            $newline = $ending;
            ++$insertIndex;

            if ($tokens[$insertIndex]->isWhitespace(" \t") && $tokens[$insertIndex + 1]->isComment()) {
                ++$insertIndex;
            }

            if ($tokens[$insertIndex]->isComment()) {
                ++$insertIndex;
            }

            $afterSemicolon = $tokens->getNextMeaningfulToken($semicolonIndex);
            if (null === $afterSemicolon || !$tokens[$afterSemicolon]->isGivenKind(T_USE)) {
                $newline .= $ending.$ending;
            }

            if ($tokens[$insertIndex]->isWhitespace()) {
                $nextToken = $tokens[$insertIndex];
                $nextMeaningfulAfterUseIndex = $tokens->getNextMeaningfulToken($insertIndex);
                if (null !== $nextMeaningfulAfterUseIndex && $tokens[$nextMeaningfulAfterUseIndex]->isGivenKind(T_USE)) {
                    if (substr_count($nextToken->getContent(), "\n") < 2) {
                        $tokens[$insertIndex] = new Token([T_WHITESPACE, $newline.$indent.ltrim($nextToken->getContent())]);
                    }
                } else {
                    $tokens[$insertIndex] = new Token([T_WHITESPACE, $newline.$indent.ltrim($nextToken->getContent())]);
                }
            } else {
                $tokens->insertAt($insertIndex, new Token([T_WHITESPACE, $newline.$indent]));
                ++$added;
            }
        }
    }
}
