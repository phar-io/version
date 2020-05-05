<?php declare(strict_types = 1);
namespace PharIo\Version;

class VersionConstraintValue {
    /** @var VersionNumber */
    private $major;

    /** @var VersionNumber */
    private $minor;

    /** @var VersionNumber */
    private $patch;

    /** @var string */
    private $label = '';

    /** @var string */
    private $buildMetaData = '';

    /** @var string */
    private $versionString = '';

    /**
     * @param string $versionString
     */
    public function __construct($versionString) {
        $this->versionString = $versionString;

        $this->parseVersion($versionString);
    }

    public function getLabel(): string {
        return $this->label;
    }

    public function getBuildMetaData(): string {
        return $this->buildMetaData;
    }

    public function getVersionString(): string {
        return $this->versionString;
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

    /**
     * @param $versionString
     */
    private function parseVersion($versionString): void {
        $this->extractBuildMetaData($versionString);
        $this->extractLabel($versionString);
        $this->stripPotentialVPrefix($versionString);

        $versionSegments = \explode('.', $versionString);
        $this->major     = new VersionNumber(\is_numeric($versionSegments[0]) ? (int)$versionSegments[0] : null);

        $minorValue = isset($versionSegments[1]) && \is_numeric($versionSegments[1]) ? (int)$versionSegments[1] : null;
        $patchValue = isset($versionSegments[2]) && \is_numeric($versionSegments[2]) ? (int)$versionSegments[2] : null;

        $this->minor = new VersionNumber($minorValue);
        $this->patch = new VersionNumber($patchValue);
    }

    /**
     * @param string $versionString
     */
    private function extractBuildMetaData(&$versionString): void {
        if (\preg_match('/\+(.*)/', $versionString, $matches) === 1) {
            $this->buildMetaData = $matches[1];
            $versionString       = \str_replace($matches[0], '', $versionString);
        }
    }

    /**
     * @param string $versionString
     */
    private function extractLabel(&$versionString): void {
        if (\preg_match('/-(.*)/', $versionString, $matches) === 1) {
            $this->label   = $matches[1];
            $versionString = \str_replace($matches[0], '', $versionString);
        }
    }

    private function stripPotentialVPrefix(&$versionString): void {
        if ($versionString[0] !== 'v') {
            return;
        }
        $versionString = \substr($versionString, 1);
    }
}
