<?php

/**
 * @author    Alwin Garside <alwin@garsi.de>
 * @copyright 2021 Alwin Garside
 * @license   MIT
 */

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

class LuaFunction extends Variable
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
        'function' => 'void',
    ];

    const CUSTOM_RETURN_TYPES = [
        'scriptComponent:GetProperties' => 'Properties',
        'scriptComponent:GetEntity' => 'T',
        'entity:FindScriptProperty' => 'PropertyValue',
        'entity:FindAllScripts' => 'Script<Entity>[]',
        'world:FindAllScripts' => 'Script<Entity>[]',
    ];

    const OVERLOADS = [
        'character:PlayAction(actionName, properties)' => [
            'fun('
            . 'actionName: "Melee",  '
            . 'properties: AnimationProperties<MeleeAnimationEvents>' .
            ')',
            'fun('
            . 'actionName: "Reload",  '
            . 'properties: AnimationProperties<ReloadAnimationEvents>' .
            ')',
        ],
    ];

    /**
     * @var \Yogarine\CraytaStubs\Lua\Argument[]
     */
    protected $arguments = [];

    /**
     * LuaFunction constructor.
     *
     * @param  string  $type
     * @param  string  $identifier
     * @param  string  $comment
     * @param  array   $arguments
     */
    public function __construct(
        string $type,
        string $identifier,
        string $comment,
        array $arguments
    ) {
        parent::__construct($type, $identifier, $comment);

        foreach ($arguments as $argumentIdentifier => $argumentType) {
            $this->addArgument(
                new Argument($this, $argumentType, $argumentIdentifier)
            );
        }
    }

    /**
     * @param  string|null  $type
     * @return string|null
     */
    public function parseType(string $type = null)
    {
        $type = parent::parseType($type);
        $type = self::CUSTOM_RETURN_TYPES[$this->identifier] ?? $type;

        return $type;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param  \Yogarine\CraytaStubs\Lua\Argument  $argument
     * @return void
     */
    public function addArgument(Argument $argument)
    {
        $this->arguments[$argument->getIdentifier()] = $argument;
    }

    /**
     * @return int
     */
    public function getMaxArgumentIdentifierLength(): int
    {
        $maxLength = 0;
        foreach ($this->arguments as $argument) {
            $argumentLength = strlen($argument->getIdentifier());

            if ($argumentLength > $maxLength) {
                $maxLength = $argumentLength;
            }
        }

        return $maxLength;
    }

    /**
     * @return string
     */
    public function getSignature(): string
    {
        $argTxt = implode(', ', $this->getArgumentIdentifiers());

        return "{$this->getIdentifier()}({$argTxt})";
    }

    /**
     * @return string[]
     */
    public function getArgumentIdentifiers(): array
    {
        $arg = [];

        foreach ($this->arguments as $argument) {
            $arg[] = $argument->getIdentifier();
        }

        return $arg;
    }

    /**
     * @return string
     */
    public function getArgumentDocBlock(): string
    {
        $doc = [];

        $overloads = self::OVERLOADS[$this->getSignature()]
            ?? self::OVERLOADS[$this->getIdentifier()]
            ?? [];

        foreach ($overloads as $overload) {
            $doc[] = " @overload {$overload}";
        }

        $maxIdentifierLength = $this->getMaxArgumentIdentifierLength();

        foreach ($this->arguments as $argument) {
            if ('...' === $argument->getIdentifier()) {
                $doc[] = " @vararg any";
            } else {
                $identifier = str_pad(
                    $argument->getIdentifier(),
                    $maxIdentifierLength
                );
                $doc[]      = " @param  {$identifier}  {$argument->getType()}";
            }
        }
        $doc[] = " @return {$this->getType()}";

        return '---' . implode("\n---", $doc) . "\n";
    }

    /**
     * @return string
     */
    public function getFunctionCode(): string
    {
        $functionsTxt = "----\n";
        $functionsTxt .= $this->getCommentDocBlock();
        $functionsTxt .= $this->getArgumentDocBlock();
        $functionsTxt .= "----\n";
        $functionsTxt .= "function {$this->getSignature()}";

        if ('void' !== $this->type) {
            $types = explode(',', $this->type);
            // Ugly way to get an array with the appropriate amount of returns.
            foreach ($types as $key => $type) {
                $types[$key] = $this->getTypeReturnValue($type);
            }
            $functionsTxt .= "\n\treturn " . implode(', ', $types);
        }

        $functionsTxt .= "\nend\n\n";

        return $functionsTxt;
    }

    /**
     * @param  string  $type
     * @return string
     */
    private function getTypeReturnValue(string $type): string
    {
        if ($this->isTableType($type)) {
            return '{}';
        }

        return 'nil';
    }
}
