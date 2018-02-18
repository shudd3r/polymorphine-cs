<?php

namespace Polymorphine\CodeStandards\Tests;

use PHPUnit\Framework\TestCase;


class FileFixerTest extends TestCase
{
    private function fixFile($file) {
        $tmpFilename = substr(basename($file), strpos(basename($file), '-') + 1);
        $tmpFile = dirname(__DIR__) . '/temp/' . $tmpFilename;
        copy($file, $tmpFile);
        $executable = dirname(__DIR__) . '/vendor/friendsofphp/php-cs-fixer/php-cs-fixer';
        $config = dirname(__DIR__) . '/php_cs.dist';
        $command = 'fix -v --config=' . $config .' --using-cache=no --path-mode=intersection "' . $tmpFile . '"';
        echo shell_exec('php ' . $executable . ' ' . $command);
        $fixed = file_get_contents($tmpFile);
        unlink($tmpFile);
        return $fixed;
    }

    /**
     * @dataProvider fileList
     * @param $expected
     * @param $given
     */
    public function testFixedFiles_MatchExpectations($expected, $given) {
        $result = $this->fixFile($given);
        $this->assertSame(file_get_contents($expected), $result);
    }

    public function fileList() {
        $files = [];
        foreach (array_diff(scandir(__DIR__ . '/Files'), array('..', '.')) as $file) {
            list($type, $index) = explode('-', $file, 2) + [false, false];
            $id = ($type === 'expected') ? 0 : 1;
            isset($files[$index]) or $files[$index] = [];
            $files[$index][$id] = __DIR__ . '/Files/' . $file;
        }

        return $files;
    }
}
