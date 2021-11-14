<?php

/**
 * @author    Alwin Garside <alwin@garsi.de>
 * @copyright 2021 Alwin Garside
 * @license   MIT
 */

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

/**
 * Represents a Lua function argument.
 */
class Argument extends Variable
{
    const CUSTOM_ARGUMENT_IDENTIFIERS = [
        'world:Raycast' => [
            'start' => 'startPosition',
            'end' => 'endPosition',
        ],
    ];

    const CUSTOM_ARGUMENT_TYPES = [
        'Analytics.PlayerHealthCritical' => [
            'playerOrUser' => 'Character|User',
        ],
        'camera:RevertClientProperty' => [
            'propertyName' => 'string',
        ],
        'character:Attach' => [
            'socketName' => 'SocketName',
        ],
        'character:PlayAction(actionName)' => [
            'actionName' => 'string',
        ],
        'character:PlayAction(actionName, properties)' => [
            'actionName' => '"Fire"',
            'animationProperties' => 'AnimationProperties<nil>',
        ],
        'character:RevertClientProperty' => [
            'propertyName' => 'string',
        ],
        'character:SetGrip' => [
            'gripPresetAsset' => 'GripAsset|nil',
        ],
        'effect:RevertClientProperty' => [
            'propertyName' => 'string',
        ],
        'entity:AttachTo' => [
            'socketName' => 'SocketName',
        ],
        'light:RevertClientProperty' => [
            'propertyName' => 'string',
        ],
        'locator:RevertClientProperty' => [
            'propertyName' => 'string',
        ],
        'mesh:RevertClientProperty' => [
            'propertyName' => 'string',
        ],
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
        'Script:OnActivityTriggered' => [
            'display' => 'string',
        ],
        'Script:OnButtonPressed' => [
            'buttonName' => 'ButtonName',
        ],
        'Script:OnButtonReleased' => [
            'buttonName' => 'ButtonName',
        ],
        'Script:LocalOnButtonPressed' => [
            'buttonName' => 'ButtonName',
        ],
        'Script:LocalOnButtonReleased' => [
            'buttonIndex' => 'ButtonName',
        ],
        'sound:RevertClientProperty' => [
            'propertyName' => 'string',
        ],
        'triggerComponent:RevertClientProperty' => [
            'propertyName' => 'string',
        ],
        'user:AddToLeaderboardValue' => [
            'callback' => 'fun(score: number): void',
        ],
        'user:GetLeaderboardValue' => [
            'callback' => 'fun(score: number, rank: string): void',
        ],
        'user:DespawnPlayerWithEffect' => [
            'onEffectEnded' => 'fun(): void',
        ],
        'user:GoToGame' => [
            'travelFailedCallback' => 'fun(): void',
        ],
        'user:GoToWorld' => [
            'travelFailedCallback' => 'fun(): void',
        ],
        'user:LeaveGame' => [
            'travelFailedCallback' => 'fun(): void',
        ],
        'user:RevertClientProperty' => [
            'propertyName' => 'string',
        ],
        'user:SetLeaderboardValue' => [
            'callback' => 'fun(): void',
        ],
        'user:SpawnPlayerWithEffect' => [
            'onEffectEnded' => 'fun(): void',
        ],
        'voxelMesh:RevertClientProperty' => [
            'propertyName' => 'string',
        ],
        'world:Raycast' => [
            'collisionCallback' =>
                'fun(entity: Entity, hitResult: HitResult): void',
            'entitiesToIgnoreTable' => 'Entity[]',
        ],
        'world:ForEachUser' => [
            'callback' => 'fun(user: User, ...: any|nil): void',
        ],
        'world:GetGames' => [
            'callback' => 'fun(games: string[]): void',
        ],
    ];

    /**
     * @var \Yogarine\CraytaStubs\Lua\LuaFunction
     */
    private $function;

    /**
     * Argument constructor.
     *
     * @param  \Yogarine\CraytaStubs\Lua\LuaFunction  $function
     * @param  string|null                            $type
     * @param  string                                 $identifier
     *
     * @noinspection PhpOptionalBeforeRequiredParametersInspection
     */
    public function __construct(
        LuaFunction $function,
        string $type = null,
        string $identifier
    ) {
        $this->function = $function;

        $identifier = [
                'function' => 'callback',
                'varArgs' => '...',
            ][$identifier] ?? $identifier;

        if ('…' === $type || '...' === $type) {
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
        $function  = $this->function->getIdentifier();
        $signature = $this->function->getSignature();

        $identifier = parent::parseIdentifier($identifier);
        $identifier = self::CUSTOM_ARGUMENT_IDENTIFIERS[$signature][$identifier]
            ?? self::CUSTOM_ARGUMENT_IDENTIFIERS[$function][$identifier]
            ?? $identifier;

        return $identifier;
    }

    /**
     * @param  string|null  $type
     * @return string|null
     */
    public function parseType(string $type = null)
    {
        $function  = $this->function->getIdentifier();
        $signature = $this->function->getSignature();
        $argument  = $this->identifier;

        $type = parent::parseType($type);
        $type = self::CUSTOM_ARGUMENT_TYPES[$signature][$argument]
            ?? self::CUSTOM_ARGUMENT_TYPES[$function][$argument]
            ?? $type;

        return $type;
    }
}
