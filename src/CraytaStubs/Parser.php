<?php

namespace Yogarine\CraytaStubs;

use DOMElement;
use DOMNodeList;

interface Parser
{
    /**
     * @return \DOMNodeList
     */
    public function getApiDocNodeList(): DOMNodeList;

    /**
     * @param  \DOMElement  $apiNode
     * @return string
     */
    public function getApiName(DOMElement $apiNode): string;

    /**
     * @param  \DOMElement  $apiNode
     * @return string
     */
    public function getApiComment(DOMElement $apiNode): string;

    /**
     * @param  \DOMElement  $apiNode
     * @return \DOMNodeList
     */
    public function getApiConstantNodeList(DOMElement $apiNode): DOMNodeList;

    /**
     * @param  \DOMElement  $apiConstantNode
     * @return string
     */
    public function getApiConstantName(DOMElement $apiConstantNode): string;

    /**
     * @param  \DOMElement  $apiConstantNode
     * @return string
     */
    public function getApiConstantUsage(DOMElement $apiConstantNode): string;

    /**
     * @param  \DOMElement  $apiConstantNode
     * @return string
     */
    public function getApiConstantComment(DOMElement $apiConstantNode): string;
    

}
