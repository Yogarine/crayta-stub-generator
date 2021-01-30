<?php

declare(strict_types=1);

namespace Yogarine\CraytaStubs\Lua;

class Constant extends Variable
{
    /**
     * @return string
     */
    public function getCode(): string
    {
        $constantTxt = "----\n";
        $constantTxt .= $this->getCommentDocBlock();
        $constantTxt .= "--- @type {$this->type}\n";
        $constantTxt .= "----\n";
        $constantTxt .= "{$this->identifier} = nil\n\n";

        return $constantTxt;
    }
}
