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
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;


class AlignedArrayValuesFixer implements DefinedFixerInterface
{
    use FixerMethods;

    private $groups = [];
    private $group  = [];

    public function getName()
    {
        return 'Polymorphine/aligned_array_values';
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DOUBLE_ARROW);
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
        return new FixerDefinition('', [new CodeSample('')]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens)
    {
        $this->tokens = $tokens;

        $array = new ArrayContext(0, $tokens);
        if (!$groups = $array->operatorGroups()) { return; }
        foreach ($groups as $group) {
            $this->fixGroupIndentation($group);
        }
    }
}
