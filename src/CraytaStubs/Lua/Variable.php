<?php

/**
 * @author    Alwin Garside <alwin@garsi.de>
 * @copyright 2021 Alwin Garside
 * @license   MIT
 */

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
        'characterToAttachTo:' => 'character:',
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
        $search     = array_keys(static::IDENTIFIER_REPLACE);
        $replace    = array_values(static::IDENTIFIER_REPLACE);
        $identifier = str_replace($search, $replace, $identifier);
        $identifier = trim($identifier);

        $this->identifier = $this->parseIdentifier($identifier);
        $this->comment    = $comment;
        $this->type       = $this->parseType($type);
    }

    /**
     * @param  string|null  $type
     * @return string|null
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
            $parts = explode('. ', $this->comment, 2);
            if (count($parts) === 1) {
                $parts = explode("\n", $this->comment, 2);
            } else {
                $parts[0] .= '.';
            }

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
     */
    public function getLocalModuleIdentifier()
    {
        if (preg_match(self::REGEX, $this->identifier, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * @param  string  $type
     * @return bool
     */
    protected function isTableType(string $type): bool
    {
        return strrpos($type, ']') === (strlen($type) - 1);
    }
}
