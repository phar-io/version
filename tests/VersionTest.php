<?php
/*
 * This file is part of PharIo\Version.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PharIo\Version;

/**
 * @covers PharIo\Version\Version
 */
class VersionTest extends \PHPUnit_Framework_TestCase {
    /**
     * @dataProvider versionProvider
     *
     * @param string $versionString
     * @param string $expectedMajor
     * @param string $expectedMinor
     * @param string $expectedPatch
     * @param string $expectedLabel
     * @param string $expectedMetadata
     */
    public function testParsesVersionNumbers($versionString, $expectedMajor, $expectedMinor, $expectedPatch, $expectedLabel = '', $expectedMetadata = '') {
        $version = new Version($versionString);

        $this->assertSame($expectedMajor, $version->getMajor()->getValue());
        $this->assertSame($expectedMinor, $version->getMinor()->getValue());
        $this->assertSame($expectedPatch, $version->getPatch()->getValue());
        $this->assertSame($expectedLabel, $version->getLabel());
        $this->assertSame($expectedMetadata, $version->getBuildMetaData());
        $this->assertSame($versionString, $version->getVersionString());
    }

    public function versionProvider() {
        return [
            ['0.0.1', '0', '0', '1'],
            ['0.1.2', '0', '1', '2'],
            ['1.0.0-alpha', '1', '0', '0', 'alpha'],
            ['0.0.1-dev+ABC', '0', '0', '1', 'dev', 'ABC'],
            ['1.0.0-x.7.z.92', '1', '0', '0', 'x.7.z.92']
        ];
    }

    /**
     * @dataProvider versionGreaterThanProvider
     *
     * @param Version $versionA
     * @param Version $versionB
     * @param bool    $expectedResult
     */
    public function testIsGreaterThan(Version $versionA, Version $versionB, $expectedResult) {
        $this->assertSame($expectedResult, $versionA->isGreaterThan($versionB));
    }

    public function versionGreaterThanProvider() {
        return [
            [new Version('1.0.0'), new Version('1.0.1'), false],
            [new Version('1.0.1'), new Version('1.0.0'), true],
            [new Version('1.1.0'), new Version('1.0.1'), true],
            [new Version('1.1.0'), new Version('2.0.1'), false],
            [new Version('1.1.0'), new Version('1.1.0'), false],
            [new Version('2.5.8'), new Version('1.6.8'), true],
            [new Version('2.5.8'), new Version('2.6.8'), false],
            [new Version('2.5.8'), new Version('3.1.2'), false],
        ];
    }

}
