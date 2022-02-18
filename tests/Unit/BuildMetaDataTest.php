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
 * @covers \PharIo\Version\BuildMetaData
 */
class BuildMetaDataTest extends TestCase {

    public function testCanBeCreated(): void {
        $this->assertInstanceOf(BuildMetaData::class, new BuildMetaData('some'));
    }

    public function testSetValueCanBeRetrieved(): void {
        $this->assertSame('some', (new BuildMetaData('some'))->asString());
    }
}
