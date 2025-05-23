<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Tests\Fixtures;

class DependencyContainer implements DependencyContainerInterface
{
    public function __construct(
        public mixed $dependency,
    ) {
    }

    public function getDependency(): mixed
    {
        return $this->dependency;
    }
}
