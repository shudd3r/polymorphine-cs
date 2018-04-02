<?php

namespace Polymorphine\CodeStandards\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;


class AlignedMethodChainFixer extends AbstractFixer
{
    /**
     * @var Tokens
     */
    private $tokens;

    public function getName()
    {
        return 'Polymorphine/aligned_method_chain';
    }

    public function isCandidate(Tokens $tokens)
    {
        if (!$arrows = $tokens->findGivenKind(T_OBJECT_OPERATOR)) {
            return false;
        }

        foreach ($arrows as $idx => $token) {
            if ($tokens[$idx - 1]->isWhitespace() && $tokens[$idx - 2]->getContent() === ')') {
                return true;
            }
        }
        return false;
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
            'Multiline method chains must be alligned to object operator (arrow) of first method call.',
            [
                new CodeSample("<?php\n\$object->property->method()\n    ->anotherMethod();")
            ]
        );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->tokens = $tokens;

        $idx = 0;
        while ($next = $this->tokens->getNextTokenOfKind($idx, [[T_OBJECT_OPERATOR]])) {
            $idx = $this->fixNext($next);
        }
    }

    private function fixNext($idx)
    {
        if ($this->tokens[$idx - 1]->isWhitespace() || !$this->isStartOfMultilineChain($idx)) {
            return $idx;
        }

        $indent = $this->indentationToken($idx);
        $this->alignChain($idx, $indent);

        return $idx;
    }

    private function isStartOfMultilineChain($idx)
    {
        $type = $this->tokens[$idx + 2];

        if ($type->getContent() !== '(') {
            return false;
        }

        $next = $this->findClosing($idx + 2);

        if ($this->tokens[$next + 1]->isWhitespace() && $this->tokens[$next + 2]->isGivenKind(T_OBJECT_OPERATOR)) {
            return true;
        }

        if (!$this->tokens[$next + 1]->isGivenKind(T_OBJECT_OPERATOR)) {
            return false;
        }

        return $this->isStartOfMultilineChain($next + 1);
    }

    private function indentationToken($idx)
    {
        $lineBreakIndex = $this->findPrevLineBreak($idx);
        $code = $this->tokens->generatePartialCode($lineBreakIndex, $idx);

        return new Token([T_WHITESPACE, "\n" . str_repeat(' ', strlen(utf8_decode(ltrim($code, "\n"))) - 2)]);
    }

    private function alignChain($idx, Token $indent)
    {
        $type = $this->tokens[$idx + 2];

        if ($type->getContent() !== '(') {
            return $idx;
        }

        $next = $this->findClosing($idx + 2);

        $replace = $this->tokens[$next + 1]->isWhitespace() && $this->tokens[$next + 2]->isGivenKind(T_OBJECT_OPERATOR);
        $insert  = !$replace && $this->tokens[$next + 1]->isGivenKind(T_OBJECT_OPERATOR);

        if (!$replace && !$insert) {
            return $idx;
        }

        if ($replace) {
            $this->tokens[$next + 1] = $indent;
        } else {
            $this->tokens->insertAt($next + 1, $indent);
        }

        return $this->alignChain($this->tokens->getNextTokenOfKind($next + 1, [[T_OBJECT_OPERATOR]]), $indent);
    }

    private function findClosing($idx)
    {
        $parenthesis = $this->tokens->getNextTokenOfKind($idx, ['(', ')']);

        if ($this->tokens[$parenthesis]->getContent() === '(') {
            $nestedEnd = $this->findClosing($parenthesis);
            return $this->findClosing($nestedEnd);
        }

        return $parenthesis;
    }

    private function findPrevLineBreak($idx)
    {
        $lineBreak = $this->tokens->getPrevTokenOfKind($idx, [[T_WHITESPACE]]);
        if ($lineBreak && strpos($this->tokens[$lineBreak]->getContent(), "\n") === false) {
            return $this->findPrevLineBreak($lineBreak);
        }

        return $lineBreak;
    }
}
