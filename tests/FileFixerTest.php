<?php

namespace Polymorphine\CodeStandards\Tests;

use PHPUnit\Framework\TestCase;


class FileFixerTest extends TestCase
{
    protected $tmpFile;

    public function tearDown() {
        if (file_exists($this->tmpFile)) { unlink($this->tmpFile); }
    }

    private function fixFile($file) {
        $this->tmpFile = dirname(__DIR__) . '/temp/fixed.php';
        copy($file, $this->tmpFile);
        $executable = dirname(__DIR__) . '/vendor/friendsofphp/php-cs-fixer/php-cs-fixer';
        $config = dirname(__DIR__) . '/php_cs.dist';
        $command = 'fix -v --config=' . $config .' --using-cache=no --path-mode=intersection "' . $this->tmpFile . '"';
        echo shell_exec('php ' . $executable . ' ' . $command);
        return $this->tmpFile;
    }

    /**
     * @dataProvider fileList
     * @param $expected
     * @param $given
     */
    public function testFixedFiles_MatchExpectations($expected, $given) {
        $temp = $this->fixFile($given);
        $result = file_get_contents($temp);
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
