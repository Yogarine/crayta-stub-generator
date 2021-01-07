<?php

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

class Field extends Variable
{
    public function getCommentBlock(): string
    {
        $doc = " @field public {$this->identifier} {$this->type} ";
        $parameterDocTxt = "---{$doc}" . wordwrap(
                $this->comment,
                101 - strlen($doc),
                "\n---" . str_repeat(' ', strlen($doc))
            ) . "\n";

        return $parameterDocTxt;
    }
}
