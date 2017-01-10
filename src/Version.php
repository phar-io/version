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

class Version {
    /**
     * @var VersionNumber
     */
    private $major;

    /**
     * @var VersionNumber
     */
    private $minor;

    /**
     * @var VersionNumber
     */
    private $patch;

    /**
     * @var string
     */
    private $label = '';

    /**
     * @var string
     */
    private $versionString = '';

    /**
     * @param string $versionString
     */
    public function __construct($versionString) {
        $this->ensureVersionStringIsValid($versionString);
        $this->versionString = $versionString;
        $this->parseVersion($versionString);
    }

    /**
     * @param $versionString
     */
    private function parseVersion($versionString) {
        $this->extractLabel($versionString);
        $versionSegments = explode('.', $versionString);
        $this->major     = new VersionNumber($versionSegments[0]);

        $minorValue = isset($versionSegments[1]) ? $versionSegments[1] : null;
        $patchValue = isset($versionSegments[2]) ? $versionSegments[2] : null;

        $this->minor = new VersionNumber($minorValue);
        $this->patch = new VersionNumber($patchValue);
    }

    /**
     * @param string $versionString
     */
    private function extractLabel(&$versionString) {
        if (preg_match('/\-(.*)/', $versionString, $matches) == 1) {
            $this->label   = $matches[1];
            $versionString = str_replace($matches[0], '', $versionString);
        }
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getVersionString() {
        return $this->versionString;
    }

    /**
     * @param Version $version
     *
     * @return bool
     */
    public function isGreaterThan(Version $version) {
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
        if ($version->getPatch()->getValue() >= $this->getPatch()->getValue()) {
            return false;
        }
        if ($version->getPatch()->getValue() < $this->getPatch()->getValue()) {
            return true;
        }

        return false;
    }

    /**
     * @return VersionNumber
     */
    public function getMajor() {
        return $this->major;
    }

    /**
     * @return VersionNumber
     */
    public function getMinor() {
        return $this->minor;
    }

    /**
     * @return VersionNumber
     */
    public function getPatch() {
        return $this->patch;
    }

    /**
     * @param string $version
     *
     * @throws InvalidVersionException
     */
    private function ensureVersionStringIsValid($version) {
        $regex = '/^v?
            (?<Major>(0|(?:[1-9][0-9]*)))
            \\.
            (?<Minor>(0|(?:[1-9][0-9]*)))
            (\\.
                (?<Patch>(0|(?:[1-9][0-9]*)))
            )?
            (?:
                -
                (?<ReleaseType>(?:(dev|beta|b|RC|alpha|a|patch|p)))
                (?:
                    (?<ReleaseTypeCount>[0-9])
                )?
            )?       
        $/x';
        if (preg_match($regex, $version, $matches) !== 1) {
            throw new InvalidVersionException(
                sprintf("Version string '%s' does not follow SemVer semantics", $version)
            );
        }
    }
}
