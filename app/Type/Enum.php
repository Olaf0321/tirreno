<?php

/**
 * Tirreno ~ Open source user analytics
 * Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.tirreno.com Tirreno(tm)
 */

declare(strict_types=1);

namespace Type;

/**
 * Enum to polyfill PHP 8.1 enum feature.
 */
abstract class Enum {
    protected $value;

    public function __construct(int|string $value) {
        if (!static::isValid($value)) {
            throw new \InvalidArgumentException('Invalid value for enum ' . static::class);
        }
        $this->value = $value;
    }

    public function __get($name): int|string {
        if ($name === 'value') {
            return $this->value;
        }
        throw new \InvalidArgumentException('Invalid property ' . $name);
    }

    public static function getValues(): array {
        $reflection = new \ReflectionClass(static::class);

        return array_values($reflection->getConstants());
    }

    public static function from(int|string $value): self {
        if (!static::isValid($value)) {
            throw new \ValueError(\sprintf('Invalid value for %s: %s', static::class, $value));
        }

        return new static($value);
    }

    public static function tryFrom(int|string $value): ?self {
        if (!static::isValid($value)) {
            return null;
        }

        return new static($value);
    }

    private static function isValid(int|string $value): bool {
        return in_array($value, static::getValues(), true);
    }
}
