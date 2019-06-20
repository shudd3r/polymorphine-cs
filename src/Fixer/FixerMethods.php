<?php

/*
 * This file is part of Polymorphine/CodeStandards package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\CodeStandards\Fixer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;


trait FixerMethods
{
    /** @var Tokens */
    private $tokens;

    private function isNextLine($idx)
    {
        return substr_count($this->tokens[$idx]->getContent(), "\n") === 1;
    }

    private function nextLineBreak(int $idx): ?int
    {
        return $this->nearestLineBreakIdx($idx);
    }

    private function prevLineBreak(int $idx): ?int
    {
        return $this->nearestLineBreakIdx($idx, false);
    }

    private function nearestLineBreakIdx(int $idx, bool $forwardSearch = true)
    {
        $direction = $forwardSearch ? 1 : -1;
        do {
            $idx = $this->tokens->getTokenOfKindSibling($idx, $direction, [[T_WHITESPACE]]);
        } while ($idx && strpos($this->tokens[$idx]->getContent(), "\n") === false);

        return $idx;
    }

    private function indentationPointLength($newLine, $assign)
    {
        $code = $this->tokens->generatePartialCode($newLine, $assign - 1);
        return $this->codeLength($code);
    }

    private function codeLength(string $code): int
    {
        return strlen(utf8_decode(ltrim($code, "\n")));
    }

    private function indentationToken(int $length, int $lineBreaks = 0)
    {
        return new Token([T_WHITESPACE, str_repeat("\n", $lineBreaks) . str_repeat(' ', $length)]);
    }

    private function fixGroupIndentation(array $group)
    {
        $maxLength = $this->findMaxLength($group);
        foreach ($group as [$idx, $length]) {
            $this->tokens[$idx - 1] = $this->indentationToken($maxLength - $length + 1);
        }
    }

    private function findMaxLength(array $group)
    {
        $maxLength = 0;
        foreach ($group as [$idx, $length]) {
            if ($length <= $maxLength) { continue; }
            $maxLength = $length;
        }
        return $maxLength;
    }

    private function lastSiblingIdx(array $group)
    {
        $last = array_pop($group);
        return $last[0];
    }
}
