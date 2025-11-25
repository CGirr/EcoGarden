<?php

namespace App\Factory;

use App\Entity\Advice;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Advice>
 */
final class AdviceFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Advice::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'description' => self::faker()->text(255),
            'months' => self::faker()->randomElements(range(1,12), rand(1,3)),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Advice $advice): void {})
        ;
    }
}
