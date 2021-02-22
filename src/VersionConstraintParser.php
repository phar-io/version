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

class VersionConstraintParser {
    /**
     * @throws UnsupportedVersionConstraintException
     */
    public function parse(string $value): VersionConstraint {
        if (\strpos($value, '||') !== false) {
            return $this->handleOrGroup($value);
        }

        if (!\preg_match('/^[\^~*]?v?[\d.*]+(?:-.*)?$/i', $value)) {
            throw new UnsupportedVersionConstraintException(
                \sprintf('Version constraint %s is not supported.', $value)
            );
        }

        switch ($value[0]) {
            case '~':
                return $this->handleTildeOperator($value);
            case '^':
                return $this->handleCaretOperator($value);
        }

        $constraint = new VersionConstraintValue($value);

        if ($constraint->getMajor()->isAny()) {
            return new AnyVersionConstraint();
        }

        if ($constraint->getMinor()->isAny()) {
            return new SpecificMajorVersionConstraint(
                $constraint->getVersionString(),
                $constraint->getMajor()->getValue()
            );
        }

        if ($constraint->getPatch()->isAny()) {
            return new SpecificMajorAndMinorVersionConstraint(
                $constraint->getVersionString(),
                $constraint->getMajor()->getValue(),
                $constraint->getMinor()->getValue()
            );
        }

        return new ExactVersionConstraint($constraint->getVersionString());
    }

    private function handleOrGroup(string $value): OrVersionConstraintGroup {
        $constraints = [];

        foreach (\explode('||', $value) as $groupSegment) {
            $constraints[] = $this->parse(\trim($groupSegment));
        }

        return new OrVersionConstraintGroup($value, $constraints);
    }

    private function handleTildeOperator(string $value): AndVersionConstraintGroup {
        $version     = new Version(\substr($value, 1));
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

        return new AndVersionConstraintGroup($value, $constraints);
    }

    private function handleCaretOperator(string $value): AndVersionConstraintGroup {
        $version = new Version(\substr($value, 1));

        return new AndVersionConstraintGroup(
            $value,
            [
                new GreaterThanOrEqualToVersionConstraint($value, $version),
                new SpecificMajorVersionConstraint($value, $version->getMajor()->getValue())
            ]
        );
    }
}
