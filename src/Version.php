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

class Version {
    /** @var VersionNumber */
    private $major;

    /** @var VersionNumber */
    private $minor;

    /** @var VersionNumber */
    private $patch;

    /** @var PreReleaseSuffix */
    private $preReleaseSuffix;

    /** @var string */
    private $versionString = '';

    /**
     * @param string $versionString
     */
    public function __construct($versionString) {
        $this->ensureVersionStringIsValid($versionString);

        $this->versionString = $versionString;
    }

    public function getPreReleaseSuffix(): PreReleaseSuffix {
        return $this->preReleaseSuffix;
    }

    public function getVersionString(): string {
        return $this->versionString;
    }

    public function hasPreReleaseSuffix(): bool {
        return $this->preReleaseSuffix !== null;
    }

    public function isGreaterThan(Version $version): bool {
        if ($version->getMajor()->getValue() > $this->getMajor()->getValue()) {
            return false;
        }

        if ($version->getMajor()->getValue() < $this->getMajor()->getValue()) {
            return true;
        }

        if ($version->getMinor()->getValue() > $this->getMinor()->getValue()) {
            return false;
        }

        if ($version->getMinor()->getValue() < $this->getMinor()->getValue()) {
            return true;
        }

        if ($version->getPatch()->getValue() > $this->getPatch()->getValue()) {
            return false;
        }

        if ($version->getPatch()->getValue() < $this->getPatch()->getValue()) {
            return true;
        }

        if (!$version->hasPreReleaseSuffix() && !$this->hasPreReleaseSuffix()) {
            return false;
        }

        if ($version->hasPreReleaseSuffix() && !$this->hasPreReleaseSuffix()) {
            return true;
        }

        if (!$version->hasPreReleaseSuffix() && $this->hasPreReleaseSuffix()) {
            return false;
        }

        return $this->getPreReleaseSuffix()->isGreaterThan($version->getPreReleaseSuffix());
    }

    public function getMajor(): VersionNumber {
        return $this->major;
    }

    public function getMinor(): VersionNumber {
        return $this->minor;
    }

    public function getPatch(): VersionNumber {
        return $this->patch;
    }

    private function parseVersion(array $matches): void {
        $this->major = new VersionNumber($matches['Major']);
        $this->minor = new VersionNumber($matches['Minor']);
        $this->patch = isset($matches['Patch']) ? new VersionNumber($matches['Patch']) : new VersionNumber(null);

        if (isset($matches['PreReleaseSuffix'])) {
            $this->preReleaseSuffix = new PreReleaseSuffix($matches['PreReleaseSuffix']);
        }
    }

    /**
     * @param string $version
     *
     * @throws InvalidVersionException
     */
    private function ensureVersionStringIsValid($version): void {
        $regex = '/^v?
            (?<Major>(0|(?:[1-9][0-9]*)))
            \\.
            (?<Minor>(0|(?:[1-9][0-9]*)))
            (\\.
                (?<Patch>(0|(?:[1-9][0-9]*)))
            )?
            (?:
                -
                (?<PreReleaseSuffix>(?:(dev|beta|b|RC|alpha|a|patch|p)\.?\d*))
            )?       
        $/x';

        if (\preg_match($regex, $version, $matches) !== 1) {
            throw new InvalidVersionException(
                \sprintf("Version string '%s' does not follow SemVer semantics", $version)
            );
        }

        $this->parseVersion($matches);
    }
}
