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
    /** @var Tokens */
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
            'Assignments of same type variables (variable, array key, property) in consecutive lines will be aligned.' .
            'Only single line assignments are aligned.',
            [
                new CodeSample(
                    "<?php\n\$object->property = 'value';\n\$object->anotherProperty = 1;\n" .
                    "\$arr['test'] = true;\n\$array['something'] = function () { return 'Hello'; };\n"
                )
            ]
        );
    }

    public function fix(SplFileInfo $file, Tokens $tokens)
    {
        $this->tokens = $tokens;

        $groups = [];
        $assign = 0;
        while ($assign = $this->tokens->getNextTokenOfKind($assign, ['='])) {
            $newLine = $this->nearestLineBreakIdx($assign, false);
            if (!$this->isPureAssignment($newLine, $assign)) { continue; }

            $siblings = $this->findSiblings($newLine, $assign);
            if (!$siblings) { continue; }

            $groups[] = $siblings;
            $assign = $this->lastSiblingIdx($siblings);
        }

        foreach ($groups as $siblings) {
            $this->fixGroupIndentation($siblings);
        }
    }

    private function findSiblings($newLine, $assign)
    {
        $siblings  = [];
        $signature = $this->getTokenSignature($newLine, $assign);

        $idx = $assign;
        while ($sibling = $this->findNextSibling($idx, $signature)) {
            $siblings[] = $sibling;
            $idx = $sibling[0];
        }

        if (!$siblings) { return null; }

        array_unshift($siblings, [$assign, $this->indentationPointLength($newLine, $assign)]);
        return $siblings;
    }

    private function findNextSibling($idx, $signature)
    {
        $newLine = $this->nearestLineBreakIdx($idx);
        if (!$newLine || !$this->isNextLine($newLine)) { return null; }

        $assign = $this->tokens->getNextTokenOfKind($newLine, ['=']);
        if (!$assign) { return null; }
        if (!$this->isPureAssignment($newLine, $assign)) { return null; }

        $candidateSignature = $this->getTokenSignature($newLine, $assign);
        if ($candidateSignature !== $signature) { return null; }

        return [$assign, $this->indentationPointLength($newLine, $assign)];
    }

    private function getTokenSignature($idx, $assign)
    {
        $signature = [];
        while (++$idx <= $assign) {
            $signature[] = $this->tokens[$idx]->getId();
        }
        return $signature;
    }

    private function indentationPointLength($newLine, $assign)
    {
        $code = $this->tokens->generatePartialCode($newLine, $assign - 1);
        return strlen(utf8_decode(ltrim($code, "\n")));
    }

    private function findMaxLength(array $siblings)
    {
        $maxLength = 0;
        foreach ($siblings as [$idx, $length]) {
            if ($length <= $maxLength) { continue; }
            $maxLength = $length;
        }
        return $maxLength;
    }

    private function fixGroupIndentation(array $group)
    {
        $maxLength = $this->findMaxLength($group);
        foreach ($group as [$idx, $length]) {
            $indent = new Token([T_WHITESPACE, str_repeat(' ', 1 + $maxLength - $length)]);
            $this->tokens[$idx - 1] = $indent;
        }
    }

    private function nearestLineBreakIdx(int $idx, bool $forwardSearch = true)
    {
        $direction = $forwardSearch ? 1 : -1;
        do {
            $idx = $this->tokens->getTokenOfKindSibling($idx, $direction, [[T_WHITESPACE]]);
        } while ($idx && strpos($this->tokens[$idx]->getContent(), "\n") === false);

        return $idx;
    }

    private function isPureAssignment($newLine, $assign)
    {
        $endLine = $this->nearestLineBreakIdx($assign);
        if ($this->tokens[$endLine - 1]->getContent() !== ';') {
            return false;
        }

        $types = [T_VARIABLE, T_CONST, T_PUBLIC, T_PROTECTED, T_PRIVATE];
        if (!$this->tokens[$newLine + 1]->isGivenKind($types)) {
            return false;
        }

        $idx = $newLine;
        while ($idx++ < $assign) {
            if ($this->tokens[$idx]->getContent() === '(') { return false; }
        }

        return $assign;
    }

    private function isNextLine($idx)
    {
        return substr_count($this->tokens[$idx]->getContent(), "\n") === 1;
    }

    private function lastSiblingIdx(array $siblings)
    {
        $last = array_pop($siblings);
        return $last[0];
    }
}
