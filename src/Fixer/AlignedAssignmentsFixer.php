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

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;


class AlignedAssignmentsFixer implements DefinedFixerInterface
{
    /**
     * @var Tokens
     */
    private $tokens;

    public function getName()
    {
        return 'Polymorphine/aligned_assignments';
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('=');
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    public function getPriority(): int
    {
        return -40;
    }

    public function getDefinition()
    {
        return new FixerDefinition(
            'xxx',
            [
                new CodeSample('xxx')
            ]
        );
    }

    public function fix(SplFileInfo $file, Tokens $tokens)
    {
        $this->tokens = $tokens;
        $siblings = [];
        $idx      = 0;
        while ($idx = $this->tokens->getNextTokenOfKind($idx, ['='])) {
            if (!$this->isSingleLine($idx)) { continue; }
            $typeIdx = $this->tokens->getNextMeaningfulToken($this->findPrevLineBreak($idx));
            if (!$this->isPureAssignment($typeIdx)) { continue; }
            if (!$group = $this->findSiblings($typeIdx, $idx)) { continue; }
            $siblings[] = $group;
            $idx = $this->getLastItem($group);
        }

        foreach ($siblings as $group) {
            $this->fixGroup($group);
        }
    }

    private function fixGroup(array $tokenIds)
    {
        $maxDiff = 0;
        $diffs   = [];
        foreach ($tokenIds as $idx) {
            $lineStart = $this->findPrevLineBreak($idx);
            $code      = $this->tokens->generatePartialCode($lineStart, $idx - 1);

            $diff = strlen(utf8_decode(ltrim($code, "\n")));
            if ($diff > $maxDiff) {
                $maxDiff = $diff;
            }
            $diffs[] = [$idx, $diff];
        }

        foreach ($diffs as [$idx, $diff]) {
            $indent = new Token([T_WHITESPACE, ' ' . str_repeat(' ', $maxDiff - $diff)]);
            $this->tokens[$idx - 1] = $indent;
        }
    }

    private function findPrevLineBreak($idx)
    {
        $lineBreak = $this->tokens->getPrevTokenOfKind($idx, [[T_WHITESPACE]]);
        if ($lineBreak && strpos($this->tokens[$lineBreak]->getContent(), "\n") === false) {
            return $this->findPrevLineBreak($lineBreak);
        }

        return $lineBreak;
    }

    private function findNextLineBreak($idx)
    {
        $lineBreak = $this->tokens->getNextTokenOfKind($idx, [[T_WHITESPACE]]);
        if ($lineBreak && strpos($this->tokens[$lineBreak]->getContent(), "\n") === false) {
            return $this->findNextLineBreak($lineBreak);
        }

        return $lineBreak;
    }

    private function isSingleLine($idx)
    {
        $endStatement = $this->tokens->getNextTokenOfKind($idx, [';']);
        $endLine      = $this->findNextLineBreak($idx);

        return $endStatement < $endLine;
    }

    private function isPureAssignment($idx)
    {
        return $this->tokens[$idx]->isGivenKind([T_VARIABLE, T_CONST, T_PUBLIC, T_PROTECTED, T_PRIVATE]);
    }

    private function nextLine($idx)
    {
        return substr_count($this->tokens[$idx]->getContent(), "\n") === 1;
    }

    private function findSiblings($typeIdx, $idx)
    {
        $siblings  = [];
        $signature = $this->getTokenSignature($typeIdx, $idx);

        $newLine = $idx;
        while ($newLine = $this->findNextLineBreak($newLine)) {
            if (!$this->nextLine($newLine)) { break; }

            $nextType = $this->tokens->getNextMeaningfulToken($newLine);
            if (!$this->isSingleLine($nextType) || !$this->isPureAssignment($nextType)) {
                break;
            }
            if (!$assign = $this->tokens->getNextTokenOfKind($nextType, ['='])) {
                break;
            }
            if ($assign - $nextType !== $idx - $typeIdx) { break; }
            if ($this->getTokenSignature($nextType, $assign) !== $signature) {
                break;
            }

            $siblings[] = $assign;
        }

        if ($siblings) { array_unshift($siblings, $idx); }
        return $siblings;
    }

    private function getTokenSignature($typeIdx, $idx)
    {
        $signature = [];
        $tokenIdx  = $typeIdx - 1;
        while (($tokenIdx = $this->tokens->getNextMeaningfulToken($tokenIdx)) <= $idx) {
            $signature[] = $this->tokens[$tokenIdx]->isGivenKind([T_CONSTANT_ENCAPSED_STRING])
                ? T_LNUMBER
                : $this->tokens[$tokenIdx]->getId();
        }

        if (isset($check)) { var_dump($signature); }
        return $signature;
    }

    private function getLastItem(array $siblings)
    {
        return array_pop($siblings);
    }
}
