<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\JsonStreamer\CacheWarmer;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Symfony\Component\JsonStreamer\Exception\RuntimeException;
use Symfony\Component\VarExporter\ProxyHelper;

/**
 * Generates lazy ghost {@see \Symfony\Component\VarExporter\LazyGhostTrait}
 * PHP files for $streamable types.
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 *
 * @deprecated since Symfony 7.3, native lazy objects will be used instead
 *
 * @internal
 */
final class LazyGhostCacheWarmer extends CacheWarmer
{
    private Filesystem $fs;

    /**
     * @param iterable<class-string> $streamableClassNames
     */
    public function __construct(
        private iterable $streamableClassNames,
        private string $lazyGhostsDir,
    ) {
        $this->fs = new Filesystem();
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        if (!$this->fs->exists($this->lazyGhostsDir)) {
            $this->fs->mkdir($this->lazyGhostsDir);
        }

        foreach ($this->streamableClassNames as $className) {
            $this->warmClassLazyGhost($className);
        }

        return [];
    }

    public function isOptional(): bool
    {
        return true;
    }

    /**
     * @param class-string $className
     */
    private function warmClassLazyGhost(string $className): void
    {
        $path = \sprintf('%s%s%s.php', $this->lazyGhostsDir, \DIRECTORY_SEPARATOR, hash('xxh128', $className));

        try {
            $classReflection = new \ReflectionClass($className);
        } catch (\ReflectionException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $this->writeCacheFile($path, \sprintf(
            'class %s%s',
            \sprintf('%sGhost', preg_replace('/\\\\/', '', $className)),
            ProxyHelper::generateLazyGhost($classReflection),
        ));
    }
}
