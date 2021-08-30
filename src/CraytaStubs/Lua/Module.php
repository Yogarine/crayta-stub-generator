<?php

/**
 * @author    Alwin Garside <alwin@garsi.de>
 * @copyright 2021 Alwin Garside
 * @license   MIT
 */

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

class Module extends Variable
{
    const EXTENDS = [
        'Camera' => 'Entity',
        'Character' => 'Entity',
        'ColorGradingAsset' => 'Asset',
        'Effect' => 'Entity',
        'EffectAsset' => 'Asset',
        'InnerHorizonAsset' => 'HorizonAsset',
        'Light' => 'Entity',
        'Locator' => 'Entity',
        'Mesh' => 'Entity',
        'MeshAsset' => 'Asset',
        'OuterHorizonAsset' => 'HorizonAsset',
        'PostProcessAsset ' => 'Asset',
        'ScriptAsset' => 'Asset',
        'SkydomeAsset' => 'Asset',
        'SkyMeshAsset' => 'Asset',
        'Sound' => 'Entity',
        'SoundAsset' => 'Asset',
        'Template' => 'Asset',
        'Trigger' => 'Entity',
        'User' => 'Entity',
        'VibrationEffectAsset' => 'Asset',
        'VoxelMesh' => 'Entity',
        'VoxelMeshAsset' => 'Asset',
        'VoxelAsset' => 'Asset',
        'WidgetAsset' => 'Asset',
        'WorldAsset' => 'Asset',
    ];

    const EXTRA_FIELDS = [
        'Script' => [
            'properties' => 'Properties',
            'Properties' => 'PropertyBag',
        ],
    ];

    const GENERICS = [
        'PropertyArray' => '<T>',
        'Script' => '<T : Entity>',
    ];

    const ANNOTATIONS = [
        'PropertyArray' => self::ANNOTATION_SHAPE,
    ];

    const ANNOTATION_CLASS = 'class';
    const ANNOTATION_SHAPE = 'shape';

    /**
     * Identifier used only for the local declaration of the module.
     *
     * @var string
     */
    private $localIdentifier;

    /**
     * Crayta API Version that this Module applies to.
     *
     * @var string
     */
    private $apiVersion;

    /**
     * @var \Yogarine\CraytaStubs\Lua\Constant[]
     */
    private $constants;

    /**
     * @var \Yogarine\CraytaStubs\Lua\Field[]
     */
    private $fields;

    /**
     * @var \Yogarine\CraytaStubs\Lua\LuaFunction[]
     */
    private $functions;

    /**
     * @param  string                                   $identifier
     * @param  string|null                              $type
     * @param  string                                   $comment
     * @param  string                                   $apiVersion
     * @param  \Yogarine\CraytaStubs\Lua\Constant[]     $constants
     * @param  \Yogarine\CraytaStubs\Lua\Field[]        $fields
     * @param  \Yogarine\CraytaStubs\Lua\LuaFunction[]  $functions
     *
     * @noinspection PhpOptionalBeforeRequiredParametersInspection
     */
    public function __construct(
        string $identifier,
        string $type = null,
        string $comment,
        string $apiVersion,
        array $constants = [],
        array $fields = [],
        array $functions = []
    ) {
        $type = $type ?? self::EXTENDS[$identifier] ?? null;

        parent::__construct($type, $identifier, $comment);

        $this->localIdentifier = $identifier;
        $this->apiVersion      = $apiVersion;
        $this->constants       = $constants;
        $this->fields          = $fields;
        $this->functions       = [];

        foreach ($functions as $function) {
            $functionIdentifier = $function->getIdentifier();

            if (isset($this->functions[$functionIdentifier])) {
                $this->functions[$functionIdentifier]->addOverload($function);
            } else {
                $this->functions[$functionIdentifier] = $function;
            }
        }

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
    public function getLocalModuleIdentifier()
    {
        return $this->getLocalIdentifier();
    }

    /**
     * @param  \Yogarine\CraytaStubs\Lua\Constant  $constant
     * @return void
     */
    public function addConstant(Constant $constant)
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
    public function addField(Field $field)
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
    public function getFieldCommentBlocks(): string
    {
        $parameterDocTxt = '';

        foreach ($this->fields as $field) {
            $parameterDocTxt .= $field->getCommentDocBlock();
        }

        return $parameterDocTxt;
    }

    /**
     * @param  \Yogarine\CraytaStubs\Lua\LuaFunction  $function
     * @return void
     */
    public function addFunction(LuaFunction $function)
    {
        $identifier = $function->getIdentifier();

        if (isset($this->functions[$identifier])) {
            $this->functions[$identifier]->addOverload($function);
        } else {
            $this->functions[$identifier] = $function;
        }

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
        $identifier = $this->identifier;
        $annotation = self::ANNOTATIONS[$identifier] ?? self::ANNOTATION_CLASS;

        $classTxt = str_repeat("-", $lineLength) . "\n";
        $classTxt .= $this->getCommentDocBlock();
        $classTxt .= "--- @generated GENERATED CODE! DO NOT EDIT!\n";
        $classTxt .= "--- @version {$this->apiVersion}\n";
        $classTxt .= "---\n";
        $classTxt .= "--- @{$annotation} {$identifier}{$this->getGenerics()}"
            . (isset($this->type) ? " : {$this->type}" : '') . "\n";
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
    public function getMaxFieldIdentifierLength(): int
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
    public function getMaxFieldTypeLength(): int
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


    /**
     * @return int
     */
    public function getMaxCombinedFieldLength(): int
    {
        $maxLength = 0;

        foreach ($this->fields as $field) {
            $length = strlen("{$field->getIdentifier()} {$field->getType()}");
            if ($length > $maxLength) {
                $maxLength = $length;
            }
        }

        return $maxLength;
    }
}
