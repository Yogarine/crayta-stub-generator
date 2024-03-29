<?php

/**
 * @author    Alwin Garside <alwin@garsi.de>
 * @copyright 2021 Alwin Garside
 * @license   MIT
 */

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

class Field extends Variable
{
    const TYPE_MAPPING = [
        'bool' => 'boolean',
        'float' => 'number',
        'int' => 'number',
        'mesh' => 'Mesh',
        'object' => 'table',
        'unhandled/int64' => 'number',
        'unhandled/UPZPropertyBag' => 'Properties',
        'unknown' => 'void',
        'vector' => 'Vector',
        'voxelmesh' => 'VoxelMesh',
        'Script' => 'Script<Entity>',
    ];

    const CUSTOM_IDENTIFIERS = [
        'Camera' => [
            'camera.var' => '[string]',
        ],
        'Character' => [
            'character.var' => '[string]',
        ],
        'Effect' => [
            'effect.var' => '[string]',
        ],
        'Light' => [
            'light.var' => '[string]',
        ],
        'Entity' => [
            'entity.var' => '[string]',
        ],
        'Locator' => [
            'locator.var' => '[string]',
        ],
        'Mesh' => [
            'mesh.var' => '[string]',
        ],
        'Properties' => [
            'properties.var' => '[string]',
        ],
        'PropertyArray' => [
            'propertyArray.var' => '[number]',
        ],
        'Trigger' => [
            'trigger.var' => '[string]',
        ],
        'User' => [
            'user.var' => '[string]',
        ],
        'VoxelMesh' => [
            'voxelMesh.var' => '[string]',
        ],
        'Widget' => [
            'widget.var' => '[string]',
        ],
        'WidgetBindings' => [
            'widgetBindings.var' => '[string]',
        ],
    ];

    const CUSTOM_TYPES = [
        'Camera' => [
            '[string]' => 'Script<Camera>|Widget',
        ],
        'Character' => [
            '[string]' => 'Script<Character>|Widget',
        ],
        'Effect' => [
            '[string]' => 'Script<Effect>|Widget',
        ],
        'Entity' => [
            '[string]' => 'Script<Entity>|Widget|any',
        ],
        'Light' => [
            '[string]' => 'Script<Light>|Widget',
        ],
        'Locator' => [
            '[string]' => 'Script<Locator>|Widget',
        ],
        'Mesh' => [
            '[string]' => 'Script<Mesh>|Widget',
            'mesh' => 'MeshAsset',
        ],
        'Properties' => [
            '[string]' => 'PropertyValue|PropertyArray<PropertyValue>',
        ],
        'PropertyArray' => [
            '[number]' => 'T',
            'length' => 'number',
        ],
        'Trigger' => [
            '[string]' => 'Script<Trigger>|Widget',
        ],
        'User' => [
            '[string]' => 'Script<User>|Widget',
        ],
        'VoxelMesh' => [
            '[string]' => 'Script<VoxelMesh>|Widget',
            'mesh' => 'VoxelMeshAsset',
        ],
        'Widget' => [
            '[string]' => 'table',
        ],
        'WidgetBindings' => [
            '[string]' => 'table',
        ],
        'World' => [
            'innerHorizon' => 'InnerHorizonAsset',
            'outerHorizon' => 'OuterHorizonAsset',
        ],
    ];

    const SKIP_FIELDS = [
        'Sound' => [
            'sound.var' => true,
        ],
        'Properties' => [
            'properties.var =' => true,
        ],
        'PropertyArray' => [
            'propertyArray.var =' => true,
        ],
        'Rotation' => [
            'operator +' => true,
            'operator – void' => true,
            'operator *' => true,
            'tostring' => true,
        ],
        'Widget' => [
            'widget.var =' => true,
            '[string]' => true,
        ],
        'WidgetBindings' => [
            'widgetBindings.var =' => true,
        ],
    ];

    /**
     * @var \Yogarine\CraytaStubs\Lua\Module
     */
    private $module;

    /**
     * @param  \Yogarine\CraytaStubs\Lua\Module  $module
     * @param  string|null                       $type
     * @param  string                            $identifier
     * @param  string                            $comment
     *
     * @noinspection PhpOptionalBeforeRequiredParametersInspection
     */
    public function __construct(
        Module $module,
        string $type = null,
        string $identifier,
        string $comment
    ) {
        $this->module = $module;

        parent::__construct($type, $identifier, $comment);

        if (
            ! isset(
                self::SKIP_FIELDS[$module->getIdentifier()][$this->identifier]
            )
        ) {
            $module->addField($this);
        }
    }

    /**
     * @param  string|null  $type
     * @return string|null
     */
    public function parseType(string $type = null)
    {
        $module = $this->module->getIdentifier();

        return static::CUSTOM_TYPES[$module][$this->identifier]
            ?? parent::parseType($type);
    }

    /**
     * @param  string  $identifier
     * @return string
     */
    public function parseIdentifier(string $identifier): string
    {
        $module = $this->module->getIdentifier();

        return static::CUSTOM_IDENTIFIERS[$module][$identifier]
            ?? parent::parseIdentifier($identifier);
    }

    /**
     * @param  int  $lineLength
     * @return string
     */
    public function getCommentDocBlock(
        int $lineLength = self::DEFAULT_LINE_LENGTH
    ): string {
        $field = str_pad(
                $this->identifier,
                $this->module->getMaxCombinedFieldLength() - strlen($this->type)
            ) . $this->type;

        $doc          = " @field public {$field} ";
        $docComment   = "---{$doc}";
        $commentBreak = "\n---" . str_repeat(' ', strlen($doc));
        $commentWidth = self::DEFAULT_LINE_LENGTH - strlen($docComment);

        if ($this->comment) {
            $comment = "@{$this->comment}";
        } else {
            $comment = '';
        }

        $wrappedComment = wordwrap(
            $comment,
            $commentWidth,
            $commentBreak
        );

        return "{$docComment}{$wrappedComment}\n";
    }
}
