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

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;


final class ConstructorsFirstFixer implements FixerInterface
{
    private Tokens $tokens;

    public function getName()
    {
        return 'Polymorphine/constructors_first';
    }

    public function getPriority()
    {
        return -40;
    }

    public function isRisky()
    {
        return false;
    }

    public function supports(SplFileInfo $file)
    {
        return true;
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_FUNCTION]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens)
    {
        $this->tokens = $tokens;

        $classIdx    = $this->tokens->getNextTokenOfKind(0, [[T_CLASS]]) + 2;
        $isConstruct = fn ($idx) => $this->tokens[$idx + 2]->getContent() === '__construct';
        $insertIdx   = $this->getMethodIdx($classIdx, $isConstruct, false);
        if (!$insertIdx) { return; }

        $construct = $this->getMethodIdx($classIdx, $isConstruct);
        if ($insertIdx < $construct) {
            $insertIdx = $this->moveMethod($construct, $insertIdx);
        }

        $classTypes          = $this->getClassTypes($classIdx);
        $isStaticConstructor = fn ($idx) => $this->isStaticConstructor($idx, $classTypes);

        $insertIdx = $this->getMethodIdx($insertIdx, $isStaticConstructor, false);
        if (!$insertIdx) { return; }

        $idx = $insertIdx;
        while ($idx = $this->getMethodIdx($idx + 10, $isStaticConstructor)) {
            $insertIdx = $this->moveMethod($idx, $insertIdx);
        }
    }

    private function isStaticConstructor(int $idx, array $classTypes): bool
    {
        $static = $this->tokens[$idx - 2]->isGivenKind(T_STATIC) && $this->tokens[$idx - 4]->isGivenKind(T_PUBLIC);
        if (!$static) { return false; }

        $openBrace  = $this->tokens->getNextTokenOfKind($idx + 4, ['{']);
        $returnType = $this->tokens[$this->tokens->getPrevMeaningfulToken($openBrace)];

        return $returnType->isGivenKind(T_STRING) && isset($classTypes[$returnType->getContent()]);
    }

    private function getMethodIdx(int $start, callable $condition = null, bool $expected = true): int
    {
        $idx = $this->tokens->getNextTokenOfKind($start, [[T_FUNCTION]]);
        while ($idx && $condition && $condition($idx) !== $expected) {
            $idx = $this->tokens->getNextTokenOfKind($idx, [[T_FUNCTION]]);
        }

        if (!$idx) { return 0; }

        $definition = [T_PUBLIC, T_PRIVATE, T_PROTECTED, T_STATIC, T_FINAL, T_ABSTRACT, T_FUNCTION];
        while ($this->tokens[$idx]->isGivenKind($definition)) {
            $idx = $this->tokens->getPrevMeaningfulToken($idx);
        }
        return $this->tokens->getNonEmptySibling($idx, 1);
    }

    private function moveMethod(int $methodIdx, int $insertIdx): int
    {
        $methodTokens = $this->extractMethod($methodIdx);

        $topIndent = $this->tokens[$insertIdx];
        $this->tokens[$insertIdx] = $methodTokens[0];
        $methodTokens[0] = $topIndent;

        $this->tokens->insertAt($insertIdx, Tokens::fromArray($methodTokens));

        return $insertIdx + count($methodTokens);
    }

    private function extractMethod($idx): array
    {
        $beginBlock = $this->tokens->getNextTokenOfKind($idx, ['{']);
        $endBlock   = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $beginBlock);

        $methodTokens = [];
        while ($idx <= $endBlock) {
            $methodTokens[] = $this->tokens[$idx];
            $this->tokens->clearAt($idx);
            $idx++;
        }

        return $methodTokens;
    }

    private function getClassTypes(int $class): array
    {
        $classTypes = ['self', $this->tokens[$class]->getContent()];

        if ($this->tokens[$class + 2]->isGivenKind(T_EXTENDS)) {
            $class = $class + 4;
            $classTypes[] = $this->tokens[$class]->getContent();
        }

        if ($this->tokens[$class + 2]->isGivenKind(T_IMPLEMENTS)) {
            $classTypes[] = $this->tokens[$class + 4]->getContent();
        }

        return array_flip($classTypes);
    }
}
