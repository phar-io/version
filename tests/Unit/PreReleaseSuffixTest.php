<?php declare(strict_types = 1);
namespace PharIo\Version;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Version\PreReleaseSuffix
 */
class PreReleaseSuffixTest extends TestCase {
    /**
     * @dataProvider greaterThanProvider
     */
    public function testGreaterThanReturnsExpectedResult(
        string $leftSuffixValue,
        string $rightSuffixValue,
        bool $expectedResult
    ): void {
        $leftSuffix  = new PreReleaseSuffix($leftSuffixValue);
        $rightSuffix = new PreReleaseSuffix($rightSuffixValue);

        $this->assertSame($expectedResult, $leftSuffix->isGreaterThan($rightSuffix));
    }

    public function greaterThanProvider() {
        return [
            ['alpha1', 'alpha2', false],
            ['alpha2', 'alpha1', true],
            ['beta1', 'alpha3', true],
            ['b1', 'alpha3', true],
            ['b1', 'a3', true],
            ['dev1', 'alpha2', false],
            ['dev1', 'alpha2', false],
            ['alpha2', 'dev5', true],
            ['rc1', 'beta2', true],
            ['patch5', 'rc7', true],
            ['alpha1', 'alpha.2', false],
            ['alpha.3', 'alpha2', true],
            ['alpha.3', 'alpha.2', true],
        ];
    }

    /**
     * @dataProvider suffixProvider
     */
    public function testParsedValue(string $suffix): void {
        $prs = new PreReleaseSuffix($suffix);
        $this->assertEquals($suffix, $prs->asString());
    }

    public function suffixProvider() {
        return [
            ['alpha1'],
            ['beta1'],
            ['b1'],
            ['dev1'],
            ['rc1'],
            ['patch5'],

            ['alpha.1'],
            ['beta.1'],
            ['b.1'],
            ['dev.1'],
            ['rc.1'],
            ['patch.5']
        ];
    }
}
