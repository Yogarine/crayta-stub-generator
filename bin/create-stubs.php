#!/bin/env php
<?php

declare(strict_types=1);

use Yogarine\CraytaStubs\Lua\Constant;
use Yogarine\CraytaStubs\Lua\Field;
use Yogarine\CraytaStubs\Lua\LuaFunction;
use Yogarine\CraytaStubs\Lua\Module;

require_once __DIR__ . '/../vendor/autoload.php';

libxml_use_internal_errors(true);

$document = new \DOMDocument();
$document->loadHTMLFile("https://developer.crayta.com/api-docs/");

$xPath = new \DOMXPath($document);
$classNodes = $xPath->query('//*[@id="api-doc"]/*[@class="api"]');

function parse_usage_node(\DOMElement $node): array
{
    $type = 'void';

    $sibling = $node->firstChild;
    if ($sibling instanceof DOMElement && $sibling->getAttribute('class') === 'type') {
        $type    = $sibling->textContent;
        $sibling = $sibling->nextSibling;
    }

    [$identifier] = explode('(', $sibling->textContent, 2);

    $arguments = [];
    while ($sibling = $sibling->nextSibling) {
        $argumentType = 'void';

        if ($sibling->getAttribute('class') === 'type') {
            $argumentType = $sibling->textContent;
            $sibling      = $sibling->nextSibling;
        }

        [$argumentName] = explode(')', $sibling->textContent, 2);
        $argumentName = trim($argumentName, ", \t\n\r\0\x0B");

        $arguments[$argumentName] = $argumentType;
    }

    return [$type, $identifier, $arguments];
}

if ($classNodes) {
    foreach ($classNodes as $classNode) {
        $moduleName     = $xPath->evaluate('string(div[@class="api-name"])', $classNode);
        $moduleComment  = $xPath->evaluate('string(div[@class="api-comment"])', $classNode);
        $localClassName = $moduleName;

        $module = new Module($moduleName, $extends[$moduleName] ?? null, $moduleComment);

        /*
         * CONSTANTS
         */
        foreach ($xPath->query('div[@class="api-constants"]/div[@class="api-constant"]', $classNode) as $node) {
            $name    = trim($xPath->evaluate('string(span[@class="api-constant-name"])', $node));
            // TODO: get all child nodes to get elements with classes like `comment-bold`.
            $comment = trim($xPath->evaluate('string(span[@class="api-constant-comment"])', $node));

            /** @var \DOMElement $usageNode */
            $usageNode = $xPath->query('span[@class="api-constant-usage"]', $node)->item(0);
            [$type, $identifier, $arguments] = parse_usage_node($usageNode);

            $module->addConstant(new Constant($type, $identifier, $comment));
        }

        /*
         * FIELDS
         */
        foreach ($xPath->query('div[@class="api-parameters"]/div[@class="api-parameter"]', $classNode) as $node) {
            $name = trim($xPath->evaluate('string(span[@class="api-parameter-name"])', $node));
            // TODO: get all child nodes to get elements with classes like `comment-bold`.
            $comment = trim($xPath->evaluate('string(span[@class="api-parameter-comment"])', $node));

            /** @var \DOMElement $usageNode */
            $usageNode = $xPath->query('span[@class="api-parameter-usage"]', $node)->item(0);
            [$type, $identifier, $arguments] = parse_usage_node($usageNode);

            $field = new Field($type, $identifier, $comment);
            $module->addField($field);
        }

        /*
         * FUNCTIONS
         */
        $functionNodes = $xPath->query('div[@class="api-functions" or @class="api-entrypoints"]/div[@class="api-function" or @class="api-entrypoint"]', $classNode);
        foreach ($functionNodes as $node) {
            $name = trim($xPath->evaluate('string(span[@class="api-function-name" or @class="api-entrypoint-name"])', $node));
            // TODO: get all child nodes to get elements with classes like `comment-bold`.
            $comment = trim($xPath->evaluate('string(span[@class="api-function-comment" or @class="api-entrypoint-comment"])', $node));

            /** @var \DOMElement $usageNode */
            $usageNode = $xPath->query('span[@class="api-function-usage" or @class="api-entrypoint-usage"]', $node)->item(0);
            [$type, $identifier, $arguments] = parse_usage_node($usageNode);

            $function = new LuaFunction($type, $identifier, $comment, $arguments);

            $module->addFunction($function);
        }

        if (null === $localClassName) {
            $localClassName = $moduleName;
        }

        file_put_contents(__DIR__ . "/../stubs/{$moduleName}.lua", $module->getCode());
    }
}
