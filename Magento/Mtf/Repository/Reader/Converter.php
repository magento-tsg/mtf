<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\Repository\Reader;

/**
 * Converter for repository xml files.
 */
class Converter implements \Magento\Mtf\Config\ConverterInterface
{
    /**
     * Interpreter that aggregates named interpreters and delegates every evaluation to one of them.
     *
     * @var \Magento\Mtf\Data\Argument\Interpreter\Composite
     */
    protected $argumentInterpreter;

    /**
     * @constructor
     * @param \Magento\Mtf\ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(\Magento\Mtf\ObjectManagerFactory $objectManagerFactory)
    {
        $objectManager = $objectManagerFactory->getObjectManager();
        $this->argumentInterpreter = $objectManager->get('Magento\Mtf\Data\Argument\InterpreterInterface');
    }

    /**
     * Convert repository xml to array.
     *
     * @param \DOMDocument $config
     * @return array
     * @throws \Exception
     */
    public function convert($config)
    {
        $output = [];
        $repositories = $config->getElementsByTagName('repository');
        foreach ($repositories as $repository) {
            /** @var \DOMElement $repository */
            if ($repository->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $classNamespace = $repository->getAttribute('class');
            $output[$classNamespace] = $this->convertNode($repository);
        }

        return $output;
    }

    /**
     * Convert xml node to array or string recursively.
     *
     * @param \DOMNode $node
     * @return array
     * @throws \Exception
     */
    public function convertNode(\DOMNode $node)
    {
        $data = [];
        switch ($node->nodeName) {
            case 'repository':
            case 'dataset':
                foreach ($node->childNodes as $dataSet) {
                    /** @var \DOMElement $dataSet */
                    if ($dataSet->nodeType != XML_ELEMENT_NODE) {
                        continue;
                    }
                    $key = $dataSet->getAttribute('name');
                    $childNodeData = [];
                    foreach ($dataSet->childNodes as $childNode) {
                        /** @var \DOMElement $childNode */
                        if ($childNode->nodeType != XML_ELEMENT_NODE) {
                            continue;
                        }
                        $nodeName = $this->getKey($childNode);
                        $nodeData = $this->convertNode($childNode);
                        if (isset($nodeData['path'])) {
                            $nodeName = str_replace(["\\", "/"], "_", $nodeName);
                            $childNodeData['section'][$nodeName] = $this->evaluateConfig($nodeData);
                        } else {
                            $childNodeData[$nodeName] = $this->argumentInterpreter->evaluate($nodeData);
                        }
                    }
                    $data[$key] = $dataSet->childNodes->length === 1 && empty($childNodeData)
                        ? $dataSet->nodeValue
                        : $childNodeData;
                }
                break;
            case 'default_value':
            case 'data_config':
            case 'field':
            case 'item':
                $fieldAttributes = $node->attributes;
                foreach ($fieldAttributes as $fieldAttribute) {
                    $data[$fieldAttribute->nodeName] = $fieldAttribute->nodeValue;
                }
                if ($node->childNodes->length > 1) {
                    $childNodeData = [];
                    foreach ($node->childNodes as $childNode) {
                        /** @var \DOMElement $childNode */
                        if ($childNode->nodeType != XML_ELEMENT_NODE) {
                            continue;
                        }
                        $nodeName = $this->getKey($childNode);
                        $childNodeData[$nodeName] = $this->convertNode($childNode);
                    }
                    $data['item'] = $childNodeData;
                } else {
                    $data['value'] = $node->nodeValue;
                }
                break;
            default:
                throw new \Exception("Invalid repository data. Unknown node: {$node->nodeName}.");
        }

        return $data;
    }

    /**
     * Get unique identifier of element.
     *
     * @param \DOMElement $node
     * @return string|null
     */
    protected function getKey(\DOMElement $node)
    {
        $attributes = ['name', 'path', 'label'];
        foreach ($attributes as $attribute) {
            if ($node->hasAttribute($attribute)) {
                return $node->getAttribute($attribute);
            }
        }
        return null;
    }

    /**
     * Compute and return effective value of a config argument.
     *
     * @param array $data
     * @return array
     */
    protected function evaluateConfig(array $data)
    {
        $preparedData = $this->argumentInterpreter->evaluate($data);
        $data['value'] = $preparedData;
        unset($data['xsi:type'], $data['item']);
        return $data;
    }
}
