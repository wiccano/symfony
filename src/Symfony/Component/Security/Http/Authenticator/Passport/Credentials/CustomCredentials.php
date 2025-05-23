<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Authenticator\Passport\Credentials;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Implements credentials checking using a custom checker function.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 *
 * @final
 */
class CustomCredentials implements CredentialsInterface
{
    private \Closure $customCredentialsChecker;
    private bool $resolved = false;

    /**
     * @param callable(mixed, UserInterface) $customCredentialsChecker If the callable does not return `true`, a
     *                                                                 BadCredentialsException is thrown. You may
     *                                                                 also throw a more specific exception.
     */
    public function __construct(
        callable $customCredentialsChecker,
        private mixed $credentials,
    ) {
        $this->customCredentialsChecker = $customCredentialsChecker(...);
    }

    public function executeCustomChecker(UserInterface $user): void
    {
        $checker = $this->customCredentialsChecker;

        if (true !== $checker($this->credentials, $user)) {
            throw new BadCredentialsException('Credentials check failed as the callable passed to CustomCredentials did not return "true".');
        }

        $this->resolved = true;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }
}
