<?php
/**
 * EPI License.
 */
declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Quality of life Twig extension for working with php enums.
 */
class EnumExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('enum', [$this, 'createProxy']),
        ];
    }

    /**
     * @param string $enumFQN fqn
     *
     * @return object proxy
     *                creates proxy
     */
    public function createProxy(string $enumFQN): object
    {
        return new class ($enumFQN) {
            /**
             * @param string $enum
             *                     constructor  class
             */
            public function __construct(private readonly string $enum)
            {
                if (!enum_exists($this->enum)) {
                    throw new \InvalidArgumentException("$this->enum is not an Enum type and cannot be used in this function");
                }
            }

            /**
             * @param string $name      name
             * @param array  $arguments args
             *
             * @return mixed
             *               profixy
             */
            public function __call(string $name, array $arguments)
            {
                $enumFQN = sprintf('%s::%s', $this->enum, $name);

                if (defined($enumFQN)) {
                    return constant($enumFQN);
                }

                if (method_exists($this->enum, $name)) {
                    return $this->enum::$name(...$arguments);
                }

                throw new \BadMethodCallException("Neither \"{$enumFQN}\" nor \"{$enumFQN}::{$name}()\" exist in this runtime.");
            }
        };
    }
}
