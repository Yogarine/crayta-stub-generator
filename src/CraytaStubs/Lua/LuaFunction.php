<?php

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

use JetBrains\PhpStorm\Pure;

class LuaFunction extends Variable
{
    public const CUSTOM_RETURN_TYPES = [
        'scriptComponent:GetProperties' => 'Properties',
        'scriptComponent:GetEntity' => 'T',
    ];

    /**
     * @var \Yogarine\CraytaStubs\Lua\Argument[]
     */
    protected array $arguments = [];

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
    public function parseType(?string $type): ?string
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
     */
    public function addArgument(Argument $argument): void
    {
        $this->arguments[$argument->getIdentifier()] = $argument;
    }

    /**
     * @return int
     */
    #[Pure] public function getMaxArgumentIdentifierLength(): int
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
    public function getFunctionCode(): string
    {
        $doc = [];
        $arg = [];

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

            $arg[] = $argument->getIdentifier();
        }
        $doc[] = " @return {$this->getType()}";

        $argTxt = implode(', ', $arg);

        $functionsTxt = "----\n";
        $functionsTxt .= $this->getCommentBlock();
        $functionsTxt .= '---' . implode("\n---", $doc) . "\n";
        $functionsTxt .= "----\n";
        $functionsTxt .= "function {$this->getIdentifier()}({$argTxt})";

        if ('void' !== $this->type) {
            $types = explode(',', $this->type);
            // Ugly way to get an array with the appropriate amount of returns.
            foreach ($types as $key => $type) {
                $types[$key] = 'nil';
            }
            $functionsTxt .= ' return ' . implode(', ', $types);
        }

        $functionsTxt .= " end\n\n";

        return $functionsTxt;
    }
}
