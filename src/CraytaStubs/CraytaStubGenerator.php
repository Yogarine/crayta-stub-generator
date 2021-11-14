<?php

/**
 * @author    Alwin Garside <alwin@garsi.de>
 * @copyright 2021 Alwin Garside
 * @license   MIT
 */

declare(strict_types=1);

namespace Yogarine\CraytaStubs;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use RuntimeException;
use Yogarine\CraytaStubs\Lua\Constant;
use Yogarine\CraytaStubs\Lua\Field;
use Yogarine\CraytaStubs\Lua\LuaFunction;
use Yogarine\CraytaStubs\Lua\Module;

class CraytaStubGenerator
{
    /**
     * @var \DOMXPath
     */
    private $xPath;

    public function __construct()
    {
        $filename = dirname(__DIR__, 2) . '/LuaDocs/LuaApi.xml';

        $document          = new DOMDocument();
        $document->recover = true;
        $document->load($filename, LIBXML_NOWARNING | LIBXML_NOERROR);

        $this->xPath = new DOMXPath($document);
    }

    /**
     * Returns the path to directory containing the static Crayta stubs.
     *
     * @return string  Absolute path to static Crayta stubs.
     */
    public static function getStaticStubsDir(): string
    {
        return realpath(dirname(__DIR__) . "/Crayta");
    }

    /**
     * Returns the path to directory containing the static Crayta stubs.
     *
     * @param  string|null  $subPath
     * @return string  Absolute path to static Crayta stubs.
     */
    public static function getDevStubsDir(string $subPath = null): string
    {
        $parts = [
            realpath(dirname(__DIR__, 2)),
            'stubs',
        ];

        if ($subPath) {
            $parts[] = $subPath;
        }

        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    /**
     * Copy static Crayta stubs over to the provided target directory.
     *
     * @param  string  $targetDir
     * @return void
     */
    public function copyStaticStubs(string $targetDir)
    {
        self::ensureTargetDir($targetDir);

        $staticStubsDir = escapeshellarg(self::getStaticStubsDir());
        $targetDir      = escapeshellarg($targetDir);
        exec("cp -af {$staticStubsDir}/*.lua {$targetDir}/");
    }

    /**
     * Generates Crayta stubs in the provided target directory.
     *
     * @param  string  $targetDir
     * @return void
     *
     * @throws \RuntimeException
     */
    public function generateStubs(string $targetDir)
    {
        $xPath       = $this->xPath;
        $apiVersion  = $xPath->evaluate('string(/apidocs/@version)');
        $apiNodeList = $xPath->query('/apidocs/apidoc');

        if (! $apiNodeList || 0 === $apiNodeList->length) {
            throw new \RuntimeException("Unable to find class nodes in HTML DOM.");
        }

        self::ensureTargetDir($targetDir);

        foreach ($apiNodeList as $apiNode) {
            $moduleName    = $xPath->evaluate('string(@name)', $apiNode);
            $moduleComment = $xPath->evaluate('string(comment)', $apiNode);

            $module = new Module($moduleName, $extends[$moduleName] ?? null, $moduleComment, $apiVersion);

            /*
             * CONSTANTS
             */
            foreach ($xPath->query('constants/constant', $apiNode) as $node) {
                list($type, $identifier, $arguments, $comment) = $this->parseNode($node);

                $module->addConstant(new Constant($type, $identifier, $comment));
            }

            /*
             * FIELDS
             */
            foreach ($xPath->query('parameters/parameter', $apiNode) as $node) {
                list($type, $identifier, $arguments, $comment) = $this->parseNode($node);

                new Field($module, $type, $identifier, $comment);
            }

            /*
             * OVERRIDES
             */
            foreach ($xPath->query('overrides/override', $apiNode) as $node) {
                list($type, $identifier, $arguments, $comment) = $this->parseNode($node);

                new Field($module, $type, $identifier, $comment);
            }

            /*
             * FUNCTIONS
             */
            $functionNodes = $xPath->query(
                '*[self::functions or self::entrypoints]/*[self::function or self::entrypoint]',
                $apiNode
            );
            foreach ($functionNodes as $node) {
                list($type, $identifier, $arguments, $comment) = $this->parseNode($node);

                $function = new LuaFunction($type, $identifier, $comment, $arguments);
                $module->addFunction($function);
            }

            file_put_contents("{$targetDir}/{$moduleName}.lua", $module->getCode());
        }
    }

    /**
     * @param  string  $targetDir
     * @return void
     *
     * @throws \RuntimeException
     */
    private static function ensureTargetDir(string $targetDir)
    {
        /**
         * Ensure stubs target is created.
         *
         * @noinspection MkdirRaceConditionInspection
         *               NotOptimalIfConditionsInspection
         */
        if (
            (file_exists($targetDir) || ! mkdir($targetDir, 0777, true))
            && ! is_dir($targetDir)
        ) {
            throw new RuntimeException(
                sprintf('Directory "%s" was not created', $targetDir)
            );
        }
    }

    /**
     * @param  \DOMNode  $node
     * @return array
     */
    private function parseNode(DOMNode $node): array
    {
        $xPath = $this->xPath;

//        $name       = trim($xPath->evaluate('string(@name)', $node));
        $serverOnly = trim($xPath->evaluate('string(@serveronly)', $node)) === 'true';
        $localOnly  = trim($xPath->evaluate('string(@localonly)', $node)) === 'true';
        $usage      = $xPath->evaluate('string(usage)', $node);
        $comment    = trim($this->xPath->evaluate('string(comment)', $node));

        if ($serverOnly || $localOnly) {
            $comment .= "\n\n";
            $notes   = [];

            if ($serverOnly) {
                $notes[] = "Server Only";
            }

            if ($localOnly) {
                $notes[] = "Local Only";
            }

            $comment .= implode(', ', $notes);
            $comment = trim($comment);
        }

        list($type, $identifier, $arguments) = self::parseUsage($usage);

        foreach ($xPath->query('info', $node) as $infoNode) {
            $returnType = $xPath->evaluate('string(@returntype)', $infoNode);

            if ($returnType) {
                $type = $returnType;
            }

            foreach ($xPath->query('args/arg', $infoNode) as $argNode) {
                $argumentType = $xPath->evaluate('string(@type)', $argNode);
                $argumentName = $xPath->evaluate('string(@name)', $argNode);

                if ($argumentType && $argumentName) {
                    $arguments[$argumentName] = $argumentType;
                }
            }
        }

        return [
            $type,
            $identifier,
            $arguments,
            $comment,
        ];
    }

    /**
     * @param  string  $usage
     * @return array
     */
    private static function parseUsage(string $usage): array
    {
        $matched = preg_match(
            '/^'
            . '(?:(?<assignment>[a-z_][a-z0-9_.]*\s*=)\s*)?'
            . '(?:(?<type>[a-z_][a-z0-9_,\/]*)\s+)?'
            . '(?<identifier>[+\-*\/]|[a-z_][a-z0-9_:.]*)'
            . '(?:\('
            . '(?<arguments>[^)]+)?'
            . '\).*)?'
            . '$/i',
            $usage,
            $matches
        );

        if (! $matched) {
            throw new RuntimeException("Unable to parse usage '{$usage}'");
        }

        $type       = $matches['type'] ?: 'void';
        $identifier = $matches['assignment'] ?: $matches['identifier'];

        $arguments = [];
        if (isset($matches['arguments'])) {
            $argumentsMatched = preg_match_all(
                '/(?:(?<argumentType>\.{3}|[a-z_][a-z0-9\/_]*)\s+)?'
                . '(?<argumentName>[a-z_][a-z0-9_.]*)(?:,\s*)?/i',
                $matches['arguments'],
                $argumentsMatches,
                PREG_SET_ORDER
            );

            if (! $argumentsMatched) {
                throw new RuntimeException("Unable to parse arguments '{$matches['arguments']}'");
            }

            foreach ($argumentsMatches as $argumentSet) {
                $argumentType = $argumentSet['argumentType'] ?: 'any';
                $argumentName = $argumentSet['argumentName'];
                $argumentName = trim($argumentName, ", \t\n\r\0\x0B");

                $arguments[$argumentName] = $argumentType;
            }
        }

        return [$type, $identifier, $arguments];
    }

    /**
     * @param  \DOMElement  $node
     * @return array
     */
    private static function parseUsageNode(\DOMElement $node): array
    {
        $type = 'void';

        $sibling = $node->firstChild;
        if (
            $sibling instanceof DOMElement &&
            $sibling->getAttribute('class') === 'type'
        ) {
            $type    = $sibling->textContent;
            $sibling = $sibling->nextSibling;
        }

        list($identifier) = explode('(', $sibling->textContent, 2);

        $arguments = [];
        while ($sibling = $sibling->nextSibling) {
            $argumentType = 'void';

            if ($sibling->getAttribute('class') === 'type') {
                $argumentType = $sibling->textContent;
                $sibling      = $sibling->nextSibling;
            }

            list($argumentName) = explode(')', $sibling->textContent, 2);
            $argumentName = trim($argumentName, ", \t\n\r\0\x0B");

            $arguments[$argumentName] = $argumentType;
        }

        return [$type, $identifier, $arguments];
    }
}
