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

class CompliesTest extends TestCase {

    /**
     * @dataProvider complyingProvider
     */
    public function testCompliesWhenExcepted(string $constraint, string $version): void {
        $this->assertTrue(
            (new VersionConstraintParser())->parse($constraint)->complies(new Version($version))
        );
    }

    /**
     * @dataProvider notComplyingProvider
     */
    public function testNotCompliesWhenExcepted(string $constraint, string $version): void {
        $this->assertFalse(
            (new VersionConstraintParser())->parse($constraint)->complies(new Version($version))
        );
    }

    public function complyingProvider(): array {
        return [
            '1.0.0'  => ['1.0.0', '1.0.0'],

            '~7.0.0' => ['~7.0.0', '7.0.1'],
            '~7.0' =>   ['~7.0', '7.0.1'],
            '~8.0' =>   ['~8.0', '8.2.3'],

            '^7.0.0' => ['^7.0.0', '7.0.1'],
            '^7.0' =>   ['^7.0', '7.0.1'],
            '^8.0' =>   ['^8.0', '8.2.3'],

            '^7.2 || ^8.0' => ['^7.2 || ^8.0', '7.4.12'],
            '^7.3 || ^8.0' => ['^7.3 || ^8.0', '8.0.3'],
            '^7.4 || ^8.0' => ['^7.3 || ^8.0', '8.1.3'],

            '5.1.*' => ['5.1.*', '5.1.3'],
            '^0.3' => ['^0.3', '0.3.1']
        ];
    }

    public function notComplyingProvider(): array {
        return [
            '1.0.0'  => ['1.0.0', '1.0.1'],
            '~4.6'   => ['~4.6', '4.5.3'],
            '~8.0.0' => ['~8.0.0', '8.1.0'],
            '5.1.*'  => ['5.1.*', '5.2.1'],
            '5.2.*'  => ['5.2.*', '5.1.9'],
            '^0.3' => ['^0.3', '0.4.1']
        ];
    }

}
