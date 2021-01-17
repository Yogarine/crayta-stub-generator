<?php

declare(strict_types=1);

namespace Yogarine\CraytaStubs;

use DOMDocument;
use DOMElement;
use DOMXPath;
use JetBrains\PhpStorm\Pure;
use RuntimeException;
use Yogarine\CraytaStubs\Lua\Constant;
use Yogarine\CraytaStubs\Lua\Field;
use Yogarine\CraytaStubs\Lua\LuaFunction;
use Yogarine\CraytaStubs\Lua\Module;

class CraytaStubGenerator
{
    /**
     * Contains the Crayta API docs HTML DOM.
     *
     * @var \DOMDocument
     */
    private DOMDocument $document;

    public function __construct()
    {
        libxml_use_internal_errors(true);
        $this->document = new DOMDocument();
        $this->document->loadHTMLFile("https://developer.crayta.com/api-docs/");
    }

    /**
     * @param  string  $targetDir
     * @return void
     */
    public static function copyStaticStubs(string $targetDir): void
    {
        exec('cp -af "' . __DIR__ . "/../Crayta/\"*.lua \"{$targetDir}\"/");
    }

    /**
     * @param  string  $targetDir
     * @return void
     */
    public function generateStubs(string $targetDir): void
    {
        $xPath = new DOMXPath($this->document);
        $classNodes = $xPath->query('//*[@id="api-doc"]/*[@class="api"]');

        self::ensureTargetDir($targetDir);

        if ($classNodes) {
            foreach ($classNodes as $classNode) {
                $moduleName     = $xPath->evaluate('string(div[@class="api-name"])', $classNode);
                $moduleComment  = $xPath->evaluate('string(div[@class="api-comment"])', $classNode);

                $module = new Module($moduleName, $extends[$moduleName] ?? null, $moduleComment);

                /*
                 * CONSTANTS
                 */
                foreach ($xPath->query('div[@class="api-constants"]/div[@class="api-constant"]', $classNode) as $node) {
                    //$name    = trim($xPath->evaluate('string(span[@class="api-constant-name"])', $node));
                    // TODO: get all child nodes to get elements with classes like `comment-bold`.
                    $comment = trim($xPath->evaluate('string(span[@class="api-constant-comment"])', $node));

                    /** @var \DOMElement $usageNode */
                    $usageNode = $xPath->query('span[@class="api-constant-usage"]', $node)->item(0);

                    /** @noinspection PhpUnusedLocalVariableInspection */
                    [$type, $identifier, $arguments] = self::parseUsageNode($usageNode);

                    $module->addConstant(new Constant($type, $identifier, $comment));
                }

                /*
                 * FIELDS
                 */
                foreach ($xPath->query('div[@class="api-parameters"]/div[@class="api-parameter"]', $classNode) as $node) {
                    //$name = trim($xPath->evaluate('string(span[@class="api-parameter-name"])', $node));
                    // TODO: get all child nodes to get elements with classes like `comment-bold`.
                    $comment = trim($xPath->evaluate('string(span[@class="api-parameter-comment"])', $node));

                    /** @var \DOMElement $usageNode */
                    $usageNode = $xPath->query('span[@class="api-parameter-usage"]', $node)->item(0);

                    /** @noinspection PhpUnusedLocalVariableInspection */
                    [$type, $identifier, $arguments] = self::parseUsageNode($usageNode);

                    new Field($module, $type, $identifier, $comment);
                }

                /*
                 * OVERRIDES
                 */
                foreach ($xPath->query('div[@class="api-overrides"]/div[@class="api-override"]', $classNode) as $node) {
                    //$name = trim($xPath->evaluate('string(span[@class="api-override-name"])', $node));
                    // TODO: get all child nodes to get elements with classes like `comment-bold`.
                    $comment = trim($xPath->evaluate('string(span[@class="api-override-comment"])', $node));

                    /** @var \DOMElement $usageNode */
                    $usageNode = $xPath->query('span[@class="api-override-usage"]', $node)->item(0);

                    /** @noinspection PhpUnusedLocalVariableInspection */
                    [$type, $identifier, $arguments] = self::parseUsageNode($usageNode);

                    new Field($module, $type, $identifier, $comment);
                }

                /*
                 * FUNCTIONS
                 */
                $functionNodes = $xPath->query('div[@class="api-functions" or @class="api-entrypoints"]/div[@class="api-function" or @class="api-entrypoint"]', $classNode);
                foreach ($functionNodes as $node) {
                    //$name = trim($xPath->evaluate('string(span[@class="api-function-name" or @class="api-entrypoint-name"])', $node));
                    // TODO: get all child nodes to get elements with classes like `comment-bold`.
                    $comment = trim($xPath->evaluate('string(span[@class="api-function-comment" or @class="api-entrypoint-comment"])', $node));

                    /** @var \DOMElement $usageNode */
                    $usageNode = $xPath->query('span[@class="api-function-usage" or @class="api-entrypoint-usage"]', $node)->item(0);
                    [$type, $identifier, $arguments] = self::parseUsageNode($usageNode);

                    $function = new LuaFunction($type, $identifier, $comment, $arguments);

                    $module->addFunction($function);
                }

                file_put_contents("{$targetDir}/{$moduleName}.lua", $module->getCode());
            }
        }

    }

    /**
     * @param  string  $targetDir
     * @return void
     *
     * @throws \RuntimeException
     */
    private static function ensureTargetDir(string $targetDir): void
    {
        // Ensure stubs target is created.
        if (! file_exists($targetDir) && ! mkdir($targetDir) && ! is_dir($targetDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $targetDir));
        }
    }

    /**
     * @param  \DOMElement  $node
     * @return array
     *
     * @noinspection PhpPureFunctionMayProduceSideEffectsInspection
     */
    #[Pure] private static function parseUsageNode(\DOMElement $node): array
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
}
