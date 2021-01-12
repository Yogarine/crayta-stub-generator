<?php

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

use JetBrains\PhpStorm\Pure;

class Module extends Variable
{
    public const EXTENDS = [
        'Camera'            => 'Entity',
        'Character'         => 'Entity',
        'Effect'            => 'Entity',
        'InnerHorizonAsset' => 'HorizonAsset',
        'Light'             => 'Entity',
        'Locator'           => 'Entity',
        'Mesh'              => 'Entity',
        'OuterHorizonAsset' => 'HorizonAsset',
        'Sound'             => 'Entity',
        'Trigger'           => 'Entity',
        'User'              => 'Entity',
        'VoxelMesh'         => 'Entity',
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

    public function getCode($lineLength = self::DEFAULT_LINE_LENGTH): string
    {
        $classTxt  = str_repeat("-", $lineLength) . "\n";
        $classTxt .= $this->getCommentBlock();
        $classTxt .= "--- @class {$this->identifier}" . (
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
