<?php

/*
 * This file is part of Polymorphine/CodeStandards package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\CodeStandards\Tests;

use PHPUnit\Framework\TestCase;


class FileFixerTest extends TestCase
{
    /**
     * @dataProvider fileList
     *
     * @param $expected
     * @param $given
     */
    public function testFixedFiles_MatchExpectations($expected, $given)
    {
        $result = $this->fixFile($given);
        $this->assertSame(file_get_contents($expected), $result);
    }

    public function fileList()
    {
        $files = [];
        foreach (array_diff(scandir(__DIR__ . '/Files'), ['..', '.']) as $file) {
            [$type, $index]                         = explode('-', $file, 2) + [false, false];
            $id                                     = ($type === 'expected') ? 0 : 1;
            isset($files[$index]) or $files[$index] = [];
            $files[$index][$id]                     = __DIR__ . '/Files/' . $file;
        }

        return $files;
    }

    private function fixFile($file)
    {
        $tmpFilename = substr(basename($file), strpos(basename($file), '-') + 1);
        $tmpFile     = dirname(__DIR__) . '/temp/' . $tmpFilename;
        copy($file, $tmpFile);
        $executable = dirname(__DIR__) . '/vendor/friendsofphp/php-cs-fixer/php-cs-fixer';
        $config     = dirname(__DIR__) . '/cs-fixer.php.dist';
        $command    = 'fix -v --config=' . $config . ' --using-cache=no --path-mode=intersection "' . $tmpFile . '"';
        echo shell_exec('php ' . $executable . ' ' . $command);
        $fixed = file_get_contents($tmpFile);
        unlink($tmpFile);

        return $fixed;
    }
}
