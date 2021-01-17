<?php

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

/**
 * Represents a Lua function argument.
 */
class Argument extends Variable
{
    public const CUSTOM_ARGUMENT_IDENTIFIERS = [
        'world:Raycast' => [
            'start' => 'startPosition',
            'end'   => 'endPosition',
        ],
    ];

    public const CUSTOM_ARGUMENT_TYPES = [
        'GameStorage.GetCounter' => [
            'callback' => 'fun(count: number): void',
        ],
        'GameStorage.UpdateCounter' => [
            'callback' => 'fun(count: number): void',
        ],
        'Leaderboards.GetNearbyValues' => [
            'callback' => 'fun(values: LeaderboardValue[]): void',
        ],
        'Leaderboards.GetTopValues' => [
            'callback' => 'fun(values: LeaderboardValue[]): void',
        ],
        'Leaderboards.GetMetadata' => [
            'callback' => 'fun(metadata: LeaderboardMetadata): void',
        ],
        'Leaderboards.GetAllMetadata' => [
            'callback' => 'fun(metadatas: LeaderboardMetadata[]): void',
        ],
        'Leaderboards.GetNearbyValuesForGame' => [
            'callback' => 'fun(values: LeaderboardValue[]): void',
        ],
        'Leaderboards.GetTopValuesForGame' => [
            'callback' => 'fun(values: LeaderboardValue[]): void',
        ],
        'Leaderboards.GetMetadataForGame' => [
            'callback' => 'fun(metadata: LeaderboardMetadata): void',
        ],
        'Leaderboards.GetAllMetadataForGame' => [
            'callback' => 'fun(metadatas: LeaderboardMetadata[]): void',
        ],
        'scriptComponent:Schedule' => [
            'callback' => 'fun(): void',
        ],
        'scriptComponent:GetSaveData' => [
            'callback' => 'fun(saveData: table): void',
        ],
        'user:GetLeaderboardValue' => [
            'callback' => 'fun(score: number, rank: string): void',
        ],
        'user:AddToLeaderboardValue' => [
            'callback' => 'fun(score: number): void',
        ],
        'world:Raycast' => [
            'collisionCallback'
                => 'fun(entity: Entity, hitResult: HitResult): void'
            ,
        ],
        'world:ForEachUser' => [
            'callback' => 'fun(user: User, ...): void',
        ],
        'world:GetGames' => [
            'callback' => 'fun(games: string[]): void',
        ],
    ];

    /**
     * @var \Yogarine\CraytaStubs\Lua\LuaFunction
     */
    private LuaFunction $function;

    /**
     * Argument constructor.
     *
     * @param  \Yogarine\CraytaStubs\Lua\LuaFunction  $function
     * @param  string|null                            $type
     * @param  string                                 $identifier
     */
    public function __construct(
        LuaFunction $function,
        ?string $type,
        string $identifier
    ) {
        $this->function = $function;

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
     * @param  string  $identifier
     * @return string
     */
    public function parseIdentifier(string $identifier): string
    {
        $function = $this->function->getIdentifier();

        $identifier = parent::parseIdentifier($identifier);
        $identifier = self::CUSTOM_ARGUMENT_IDENTIFIERS[$function][$identifier]
            ?? $identifier;

        return $identifier;
    }

    /**
     * @param  string|null  $type
     * @return string|null
     */
    public function parseType(?string $type): ?string
    {
        $function = $this->function->getIdentifier();
        $argument = $this->identifier;

        $type = parent::parseType($type);
        $type = self::CUSTOM_ARGUMENT_TYPES[$function][$argument] ?? $type;

        return $type;
    }
}
