<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Version.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PharIo\Version;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Version\Version
 */
class VersionTest extends TestCase {
    /**
     * @dataProvider versionProvider
     *
     * @param string $versionString
     * @param string $expectedMajor
     * @param string $expectedMinor
     * @param string $expectedPatch
     * @param string $expectedPreReleaseValue
     * @param int    $expectedReleaseCount
     */
    public function testParsesVersionNumbers(
        $versionString,
        $expectedMajor,
        $expectedMinor,
        $expectedPatch,
        $expectedPreReleaseValue = '',
        $expectedReleaseCount = 0
    ): void {
        $version = new Version($versionString);

        $this->assertSame($expectedMajor, $version->getMajor()->getValue());
        $this->assertSame($expectedMinor, $version->getMinor()->getValue());
        $this->assertSame($expectedPatch, $version->getPatch()->getValue());

        if ($expectedPreReleaseValue !== '') {
            $this->assertSame($expectedPreReleaseValue, $version->getPreReleaseSuffix()->getValue());
        }

        if ($expectedReleaseCount !== 0) {
            $this->assertSame($expectedReleaseCount, $version->getPreReleaseSuffix()->getNumber());
        }

    }

    public function versionProvider() {
        return [
            ['0.0.1', 0, 0, 1],
            ['0.1.2', 0, 1, 2],
            ['1.0.0-alpha', 1, 0, 0, 'alpha'],
            ['3.4.12-dev3', 3, 4, 12, 'dev', 3],
            ['1.2.3-beta.2', 1, 2, 3, 'beta', 2],
            ['v1.2.3-rc', 1, 2, 3, 'rc']
        ];
    }

    /**
     * @dataProvider versionStringProvider
     */
    public function testOrigionalStringReturnsExceptedVersionString(string $input) {
        $this->assertEquals(
            (new Version($input))->getOriginalString(),
            $input
        );
    }

    /**
     * @dataProvider versionStringProvider
     */
    public function testAsStringReturnsExceptedVersionString(string $input, string $excepted) {
        $this->assertEquals(
            (new Version($input))->getVersionString(),
            $excepted
        );
    }

    /**
     * @dataProvider versionGreaterThanProvider
     *
     * @param bool $expectedResult
     */
    public function testIsGreaterThan(Version $versionA, Version $versionB, $expectedResult): void {
        $this->assertSame($expectedResult, $versionA->isGreaterThan($versionB));
    }

    public function versionGreaterThanProvider(): array {
        return [
            [new Version('1.0.0'), new Version('1.0.1'), false],
            [new Version('1.0.1'), new Version('1.0.0'), true],
            [new Version('1.1.0'), new Version('1.0.1'), true],
            [new Version('1.1.0'), new Version('2.0.1'), false],
            [new Version('1.1.0'), new Version('1.1.0'), false],
            [new Version('2.5.8'), new Version('1.6.8'), true],
            [new Version('2.5.8'), new Version('2.6.8'), false],
            [new Version('2.5.8'), new Version('3.1.2'), false],
            [new Version('3.0.0-alpha1'), new Version('3.0.0-alpha2'), false],
            [new Version('3.0.0-alpha2'), new Version('3.0.0-alpha1'), true],
            [new Version('3.0.0-alpha.1'), new Version('3.0.0'), false],
            [new Version('3.0.0'), new Version('3.0.0-alpha.1'), true],
        ];
    }

    /**
     * @dataProvider invalidVersionStringProvider
     *
     * @param string $versionString
     */
    public function testThrowsExceptionIfVersionStringDoesNotFollowSemVer($versionString): void {
        $this->expectException(InvalidVersionException::class);
        new Version($versionString);
    }

    public function invalidVersionStringProvider(): array {
        return [
            ['foo'],
            ['0.0.1-dev+ABC', '0', '0', '1', 'dev', 'ABC'],
            ['1.0.0-x.7.z.92', '1', '0', '0', 'x.7.z.92']
        ];
    }

    public function versionStringProvider() {
        return [
            ['0.0.1', '0.0.1'],
            ['0.1.0', '0.1.0'],
            ['1.0.0-alpha', '1.0.0-alpha'],
            ['3.4.12-dev3', '3.4.12-dev3'],
            ['1.2.3-beta.2', '1.2.3-beta.2'],

            ['v0.0.1', '0.0.1'],
            ['v0.1.0', '0.1.0'],
            ['v1.0.0-alpha', '1.0.0-alpha'],
            ['v3.4.12-dev3', '3.4.12-dev3'],
            ['v1.2.3-beta.2', '1.2.3-beta.2'],

            ['0.1', '0.1.0'],
            ['v0.1', '0.1.0']
        ];
    }

    public function testIdenticalVersionsAreConsideredEqual() {
        $a = new Version('1.0.0-rc1');
        $b = new Version('1.0.0-rc1');

        $this->assertTrue($a->equals($b));
    }

    public function testNonIdenticalVersionsAreNotConsideredEqual() {
        $a = new Version('1.0.0-rc1');
        $b = new Version('1.0.0-rc2');

        $this->assertFalse($a->equals($b));
    }

}
