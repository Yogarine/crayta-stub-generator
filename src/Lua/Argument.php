<?php

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

class Argument extends Variable
{
    public const CUSTOM_ARGUMENT_TYPES = [
        'GameStorage.GetCounter' => [
            'callback' => 'fun(count: number)',
        ],
        'GameStorage.UpdateCounter' => [
            'callback' => 'fun(count: number)',
        ],
    ];

    /**
     * Argument constructor.
     *
     * @param  string|null  $type
     * @param  string       $identifier
     */
    public function __construct(?string $type, string $identifier)
    {
        $identifier = match ($identifier) {
            'function' => 'callback',
            'varArgs'  => '...',
            default    => $identifier,
        };

        if ('…' === $type) {
            $identifier = '...';
        }

        parent::__construct($type, $identifier, '');
    }

    /**
     * @param  string|null  $type
     * @return string|null
     */
    public function parseType(?string $type): ?string
    {
        $type = parent::parseType($type);
        $type = self::CUSTOM_ARGUMENT_TYPES[$this->identifier][$this->identifier] ?? $type;

        return $type;
    }
}