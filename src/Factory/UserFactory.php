<?php

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'city' => self::faker()->postcode(),
            'password' => '1234',
            'roles' => ['ROLE_USER'],
            'username' => self::faker()->userName(),
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function(User $user): void {
                    $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
        });
    }
}
