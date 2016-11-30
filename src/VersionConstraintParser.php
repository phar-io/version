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

class VersionConstraintParser {
    /**
     * @param string $value
     *
     * @return VersionConstraint
     *
     * @throws UnsupportedVersionConstraintException
     */
    public function parse($value) {

        if (!preg_match('/^[\^~\*]?[\d.\*]+$/', $value)) {
            throw new UnsupportedVersionConstraintException(
                sprintf('Version constraint %s is not supported.', $value)
            );
        }

        switch ($value[0]) {
            case '~':
                return $this->handleTildeOperator($value);
            case '^':
                return $this->handleCaretOperator($value);
        }

        $version = new Version($value);

        if ($version->getMajor()->isAny()) {
            return new AnyVersionConstraint();
        }
        if ($version->getMinor()->isAny()) {
            return new SpecificMajorVersionConstraint(
                $value,
                $version->getMajor()->getValue()
            );
        }
        if ($version->getPatch()->isAny()) {
            return new SpecificMajorAndMinorVersionConstraint(
                $value,
                $version->getMajor()->getValue(),
                $version->getMinor()->getValue()
            );
        }

        return new ExactVersionConstraint($value);
    }

    /**
     * @param string $value
     *
     * @return VersionConstraintGroup
     */
    private function handleTildeOperator($value) {
        $version     = new Version(substr($value, 1));
        $constraints = [
            new GreaterThanOrEqualToVersionConstraint($value, $version)
        ];
        if ($version->getPatch()->isAny()) {
            $constraints[] = new SpecificMajorVersionConstraint(
                $value,
                $version->getMajor()->getValue()
            );
        } else {
            $constraints[] = new SpecificMajorAndMinorVersionConstraint(
                $value,
                $version->getMajor()->getValue(),
                $version->getMinor()->getValue()
            );
        }

        return new VersionConstraintGroup($value, $constraints);
    }

    /**
     * @param string $value
     *
     * @return VersionConstraintGroup
     */
    private function handleCaretOperator($value) {
        $version = new Version(substr($value, 1));

        return new VersionConstraintGroup(
            $value,
            [
                new GreaterThanOrEqualToVersionConstraint($value, $version),
                new SpecificMajorVersionConstraint($value, $version->getMajor()->getValue())
            ]
        );
    }

}
