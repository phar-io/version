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
     * @param string $metaData
     */
    public function testParsesVersionNumbers(
        $versionString,
        $expectedMajor,
        $expectedMinor,
        $expectedPatch,
        $expectedPreReleaseValue = '',
        $expectedReleaseCount = 0,
        $metaData = ''
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

        if ($metaData !== '') {
            $this->assertSame($metaData, $version->getBuildMetaData()->asString());
        }
    }

    public function versionProvider() {
        return [
            ['0.0.1', 0, 0, 1],
            ['0.1.2', 0, 1, 2],
            ['1.0.0-alpha', 1, 0, 0, 'alpha'],
            ['3.4.12-dev3', 3, 4, 12, 'dev', 3],
            ['1.2.3-beta.2', 1, 2, 3, 'beta', 2],
            ['v1.2.3-rc', 1, 2, 3, 'rc'],
            ['v1.2.3-rc1', 1, 2, 3, 'rc', 1],
            ['0.0.1-dev+ABC', 0, 0, 1, 'dev', 0, 'ABC'],
            ['0.0.1+git-15a90844ad40f127afd244c0cad228de2a80052a', 0, 0, 1, '', 0, 'git-15a90844ad40f127afd244c0cad228de2a80052a']
        ];
    }

    /**
     * @dataProvider versionStringProvider
     */
    public function testOrigionalStringReturnsExceptedVersionString(string $input): void {
        $this->assertEquals(
            (new Version($input))->getOriginalString(),
            $input
        );
    }

    /**
     * @dataProvider versionStringProvider
     */
    public function testAsStringReturnsExceptedVersionString(string $input, string $excepted): void {
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
            ['1.2.3.4'],
            ['1.0.0-x.7.z.92']
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

    public function testIdenticalVersionsAreConsideredEqual(): void {
        $a = new Version('1.0.0-rc1');
        $b = new Version('1.0.0-rc1');

        $this->assertTrue($a->equals($b));
    }

    public function testNonIdenticalVersionsAreNotConsideredEqual(): void {
        $a = new Version('1.0.0-rc1');
        $b = new Version('1.0.0-rc2');

        $this->assertFalse($a->equals($b));
    }

    public function testIdenticalVersionsWithBuildMetaDataAreConsideredEqual(): void {
        $a = new Version('1.0.0-rc1+abc');
        $b = new Version('1.0.0-rc1+abc');

        $this->assertTrue($a->equals($b));
    }

    public function testIdenticalVersionsWithOnlyOneHavingBuildMetaDataAreNotConsideredEqual(): void {
        $a = new Version('1.0.0-rc1+abc');
        $b = new Version('1.0.0-rc1');

        $this->assertFalse($a->equals($b));
    }

    public function testIdenticalVersionsWithDifferingBuildMetaDataAreNotConsideredEqual(): void {
        $a = new Version('1.0.0-rc1+abc');
        $b = new Version('1.0.0-rc1+def');

        $this->assertFalse($a->equals($b));
    }

    public function testGetPreReleaseSuffixThrowsExceptionWhenNoneIsSet(): void {
        $this->expectException(NoPreReleaseSuffixException::class);
        (new Version('1.2.3'))->getPreReleaseSuffix();
    }

    public function testGetBuildMetadataThrowsExceptionWhenNoneIsSet(): void {
        $this->expectException(NoBuildMetaDataException::class);
        (new Version('1.2.3'))->getBuildMetaData();
    }

    public function testHasBuildMetadataReturnsFalseWhenNoneIsSet(): void {
        $this->assertFalse((new Version('1.2.3'))->hasBuildMetaData());
    }

    public function testBuildMetadataCanBeRetreived(): void {
        $this->assertSame('test', (new Version('1.2.3+test'))->getBuildMetaData()->asString());
    }
}
