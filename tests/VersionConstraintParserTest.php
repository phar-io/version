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
 * @covers PharIo\Version\VersionConstraintParser
 */
class VersionConstraintParserTest extends \PHPUnit_Framework_TestCase {
    /**
     * @dataProvider versionStringProvider
     *
     * @param string            $versionString
     * @param VersionConstraint $expectedConstraint
     */
    public function testReturnsExpectedConstraint($versionString, VersionConstraint $expectedConstraint) {
        $parser = new VersionConstraintParser;

        $this->assertEquals($expectedConstraint, $parser->parse($versionString));
    }

    /**
     * @dataProvider unsupportedVersionStringProvider
     *
     * @param string $versionString
     */
    public function testThrowsExceptionIfVersionStringIsNotSupported($versionString) {
        $parser = new VersionConstraintParser;

        $this->expectException(UnsupportedVersionConstraintException::class);

        $parser->parse($versionString);
    }

    public function versionStringProvider() {
        return [
            ['1.0.2', new ExactVersionConstraint('1.0.2')],
            [
                '~4.6',
                new VersionConstraintGroup(
                    '~4.6',
                    [
                        new GreaterThanOrEqualToVersionConstraint('~4.6', new Version('4.6')),
                        new SpecificMajorVersionConstraint('~4.6', 4)
                    ]
                )
            ],
            [
                '~4.6.2',
                new VersionConstraintGroup(
                    '~4.6.2',
                    [
                        new GreaterThanOrEqualToVersionConstraint('~4.6.2', new Version('4.6.2')),
                        new SpecificMajorAndMinorVersionConstraint('~4.6.2', 4, 6)
                    ]
                )
            ],
            [
                '^2.6.1',
                new VersionConstraintGroup(
                    '^2.6.1',
                    [
                        new GreaterThanOrEqualToVersionConstraint('^2.6.1', new Version('2.6.1')),
                        new SpecificMajorVersionConstraint('^2.6.1', 2)
                    ]
                )
            ],
            ['5.1.*', new SpecificMajorAndMinorVersionConstraint('5.1.*', 5, 1)],
            ['5.*', new SpecificMajorVersionConstraint('5.*', 5)],
            ['*', new AnyVersionConstraint()]
        ];
    }

    public function unsupportedVersionStringProvider() {
        return [
            ['foo'],
            ['+1.0.2'],
            ['1.0.2 || 1.0.5'],
            ['>=2.0'],
        ];
    }
}
