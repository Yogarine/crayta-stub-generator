<?php

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

abstract class Variable
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

    const IDENTIFIER_REPLACE = [
        'entityOrNill' => 'entity',
        'function ' => '',
        'voxelComponent:' => 'voxelMesh:',
        'voxels:' => 'voxelMesh:',
        'trigger:' => 'triggerComponent:',
    ];

    const DEFAULT_LINE_LENGTH = 104;

    const REGEX = '/^(?:function )?([^.:]+):/';

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $comment;

    /**
     * Variable constructor.
     *
     * @param  string|null  $type
     * @param  string       $identifier
     * @param  string       $comment
     *
     * @noinspection PhpOptionalBeforeRequiredParametersInspection
     */
    public function __construct(
        string $type = null,
        string $identifier,
        string $comment
    ) {
        $identifier = str_replace(
            array_keys(static::IDENTIFIER_REPLACE),
            array_values(static::IDENTIFIER_REPLACE),
            $identifier
        );
        $identifier = trim($identifier);

        $this->identifier = $this->parseIdentifier($identifier);
        $this->comment    = $comment;
        $this->type       = $this->parseType($type);
    }

    /**
     * @param  string|null  $type
     * @return string|null
     *
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function parseType(string $type = null)
    {
        return static::TYPE_MAPPING[$type] ?? $type;
    }

    /**
     * @param  string  $identifier
     * @return string
     */
    public function parseIdentifier(string $identifier): string
    {
        return $identifier;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getCommentDocBlock(): string
    {
        $result = '';

        if ($this->comment) {
            $parts    = explode('.', $this->comment, 2);
            $parts[0] .= '.';

            foreach ($parts as $part) {
                if ($part) {
                    $result .= "--- " . implode(
                            "\n--- ",
                            explode("\n", wordwrap(trim($part), 100))
                        ) . "\n---\n";
                }
            }
        }

        return $result;
    }

    /**
     * @return string|null
     *
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getLocalModuleIdentifier()
    {
        if (preg_match(self::REGEX, $this->identifier, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}
