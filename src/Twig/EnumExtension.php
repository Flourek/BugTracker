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
     * @return object|Proxy idk
     *                      creats proxy
     */
    public function createProxy(string $enumFQN): object
    {
        return new Proxy($enumFQN);
    }
}
