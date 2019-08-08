<?php

namespace LinkORB\AppEventLogger\Processor;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Add the current security token to the log record.
 */
class TokenProcessor
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(array $record)
    {
        if (null === $this->tokenStorage->getToken()) {
            $record['extra']['token'] = null;

            return $record;
        }

        $token = $this->tokenStorage->getToken();
        $roles = [];
        foreach ($token->getRoles() as $role) {
            if (null === $role->getRole()) {
                continue;
            }
            $roles[] = $role->getRole();
        }

        $record['extra']['token'] = [
            'username' => $token->getUsername(),
            'authenticated' => $token->isAuthenticated(),
            'roles' => $roles,
        ];

        return $record;
    }
}
