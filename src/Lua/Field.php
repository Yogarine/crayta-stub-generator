<?php

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

use JetBrains\PhpStorm\Pure;

class Field extends Variable
{
    public const CUSTOM_TYPES = [
        'World' => [
            'innerHorizon' => 'InnerHorizonAsset',
            'outerHorizon' => 'OuterHorizonAsset',
        ],
    ];

    /**
     * @var \Yogarine\CraytaStubs\Lua\Module
     */
    private Module $module;

    /**
     * @param  \Yogarine\CraytaStubs\Lua\Module  $module
     * @param  string|null                       $type
     * @param  string                            $identifier
     * @param  string                            $comment
     */
    public function __construct(
        Module $module,
        ?string $type,
        string $identifier,
        string $comment
    ) {
        $this->module = $module;

        parent::__construct($type, $identifier, $comment);

        $module->addField($this);
    }

    /**
     * @param  string|null  $type
     * @return string|null
     */
    public function parseType(?string $type): ?string
    {
        $module = $this->module->getIdentifier();

        return static::CUSTOM_TYPES[$module][$this->identifier]
            ?? parent::parseType($type);
    }

    /**
     * @param  int  $lineLength
     * @return string
     */
    #[Pure] public function getCommentBlock(
        $lineLength = self::DEFAULT_LINE_LENGTH
    ): string {
        $identifier = str_pad(
            $this->identifier,
            $this->module->getMaxFieldIdentifierLength()
        );
        $type = str_pad(
            $this->type,
            $this->module->getMaxFieldTypeLength()
        );

        $doc          = " @field public {$identifier} {$type} ";
        $docComment   = "---{$doc}";
        $commentBreak = "\n---" . str_repeat(' ', strlen($doc));
        $commentWidth = self::DEFAULT_LINE_LENGTH - strlen($docComment);

        $wrappedComment = wordwrap(
            $this->comment,
            $commentWidth,
            $commentBreak
        );

        return "{$docComment}{$wrappedComment}\n";
    }
}
