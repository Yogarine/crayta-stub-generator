<?php

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

use JetBrains\PhpStorm\Pure;

class Module extends Variable
{
    public const EXTENDS = [
        'Camera'            => 'Entity',
        'Character'         => 'Entity',
        'ColorGradingAsset' => 'Asset',
        'Effect'            => 'Entity',
        'EffectAsset'       => 'Asset',
        'InnerHorizonAsset' => 'HorizonAsset',
        'Light'             => 'Entity',
        'Locator'           => 'Entity',
        'Mesh'              => 'Entity',
        'MeshAsset'         => 'Asset',
        'OuterHorizonAsset' => 'HorizonAsset',
        'PostProcessAsset ' => 'Asset',
        'ScriptAsset'       => 'Asset',
        'SkydomeAsset'      => 'Asset',
        'SkyMeshAsset'      => 'Asset',
        'Sound'             => 'Entity',
        'SoundAsset'        => 'Asset',
        'Template'          => 'Asset',
        'Trigger'           => 'Entity',
        'User'              => 'Entity',
        'VoxelMesh'         => 'Entity',
        'VoxelMeshAsset'    => 'Asset',
        'VoxelAsset'        => 'Asset',
        'WidgetAsset'       => 'Asset',
        'WorldAsset'        => 'Asset',
    ];

    public const EXTRA_FIELDS = [
        'Script' => [
            'properties' => 'Properties',
        ],
    ];

    public const GENERICS = [
        'PropertyArray' => '<T>',
    ];

    /**
     * Identifier used only for the local declaration of the module.
     *
     * @var string
     */
    private string $localIdentifier;

    /**
     * @var \Yogarine\CraytaStubs\Lua\Constant[]
     */
    private array $constants;

    /**
     * @var \Yogarine\CraytaStubs\Lua\Field[]
     */
    private array $fields;

    /**
     * @var \Yogarine\CraytaStubs\Lua\LuaFunction[]
     */
    private array $functions;

    /**
     * @param  string                                   $identifier
     * @param  string|null                              $type
     * @param  string                                   $comment
     * @param  \Yogarine\CraytaStubs\Lua\Constant[]     $constants
     * @param  \Yogarine\CraytaStubs\Lua\Field[]        $fields
     * @param  \Yogarine\CraytaStubs\Lua\LuaFunction[]  $functions
     */
    public function __construct(
        string $identifier,
        string $type = null,
        string $comment = '',
        array $constants = [],
        array $fields = [],
        array $functions = []
    ) {
        $type = $type ?? self::EXTENDS[$identifier] ?? null;

        parent::__construct($type, $identifier, $comment);

        $this->localIdentifier = $identifier;
        $this->constants       = $constants;
        $this->fields          = $fields;
        $this->functions       = $functions;

        foreach (
            self::EXTRA_FIELDS[$identifier] ?? [] as $fieldName => $fieldType
        ) {
            new Field($this, $fieldType, $fieldName, '');
        }
    }

    /**
     * @return string
     */
    public function getGenerics(): string
    {
        return static::GENERICS[$this->identifier] ?? '';
    }

    /**
     * @return string
     */
    public function getLocalIdentifier(): string
    {
        return $this->localIdentifier;
    }

    /**
     * @return string|null
     */
    #[Pure] public function getLocalModuleIdentifier(): ?string
    {
        return $this->getLocalIdentifier();
    }

    /**
     * @param  \Yogarine\CraytaStubs\Lua\Constant  $constant
     * @return void
     */
    public function addConstant(Constant $constant): void
    {
        $this->constants[$constant->getIdentifier()] = $constant;
        $localModuleIdentifier = $constant->getLocalModuleIdentifier();

        if (
            null !== $localModuleIdentifier &&
            $localModuleIdentifier !== $this->identifier
        ) {
            $this->localIdentifier = $localModuleIdentifier;
        }
    }

    /**
     * @return string
     */
    public function getConstantsCode(): string
    {
        $result = '';

        foreach ($this->constants as $constant) {
            $result .= $constant->getCode();
        }

        return $result;
    }

    /**
     * @param  \Yogarine\CraytaStubs\Lua\Field  $field
     * @return void
     */
    public function addField(Field $field): void
    {
        $this->fields[$field->getIdentifier()] = $field;
        $localModuleIdentifier = $field->getLocalModuleIdentifier();

        if (
            null !== $localModuleIdentifier &&
            $localModuleIdentifier !== $this->identifier
        ) {
            $this->localIdentifier = $localModuleIdentifier;
        }
    }

    /**
     * @return string
     */
    #[Pure] public function getFieldCommentBlocks(): string
    {
        $parameterDocTxt = '';

        foreach ($this->fields as $field) {
            $parameterDocTxt .= $field->getCommentBlock();
        }

        return $parameterDocTxt;
    }

    /**
     * @param  \Yogarine\CraytaStubs\Lua\LuaFunction  $function
     * @return void
     */
    public function addFunction(LuaFunction $function): void
    {
        $this->functions[] = $function;

        $localModuleIdentifier = $function->getLocalModuleIdentifier();

        if (
            null !== $localModuleIdentifier &&
            $localModuleIdentifier !== $this->identifier
        ) {
            $this->localIdentifier = $localModuleIdentifier;
        }
    }

    /**
     * @return string
     */
    public function getFunctionsCode(): string
    {
        $functionsTxt = '';

        foreach ($this->functions as $function) {
            $functionsTxt .= $function->getFunctionCode();
        }

        return $functionsTxt;
    }

    /**
     * @param  int  $lineLength
     * @return string
     */
    public function getCode(int $lineLength = self::DEFAULT_LINE_LENGTH): string
    {
        $classTxt  = str_repeat("-", $lineLength) . "\n";
        $classTxt .= $this->getCommentBlock();
        $classTxt .= "--- @generated GENERATED CODE! DO NOT EDIT!\n";
        $classTxt .= "---\n";
        $classTxt .= "--- @class {$this->identifier}{$this->getGenerics()}" . (
                isset($this->type) ? " : {$this->type}" : ''
            ) . "\n";
        $classTxt .= $this->getFieldCommentBlocks();
        $classTxt .= str_repeat("-", $lineLength) . "\n";

        if ('math' !== $this->localIdentifier) {
            $classTxt .= "local {$this->localIdentifier} = {}\n";

            if ($this->identifier !== $this->localIdentifier) {
                $classTxt .= "{$this->identifier} = {$this->localIdentifier}\n";
            }
        }
        $classTxt .= "\n";

        $classTxt .= $this->getConstantsCode();
        $classTxt .= $this->getFunctionsCode();
        $classTxt .= "return {$this->localIdentifier}\n";

        return $classTxt;
    }

    /**
     * @return int
     */
    #[Pure] public function getMaxFieldIdentifierLength(): int
    {
        $maxLength = 0;

        foreach ($this->fields as $field) {
            $length = strlen($field->getIdentifier());
            if ($length > $maxLength) {
                $maxLength = $length;
            }
        }

        return $maxLength;
    }

    /**
     * @return int
     */
    #[Pure] public function getMaxFieldTypeLength(): int
    {
        $maxLength = 0;

        foreach ($this->fields as $field) {
            $length = strlen($field->getType());
            if ($length > $maxLength) {
                $maxLength = $length;
            }
        }

        return $maxLength;
    }
}