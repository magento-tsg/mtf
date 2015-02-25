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
namespace Magento\Mtf\Util;

/**
 * Sorts array elements depends on next, prev keys
 *
 * Class SequencesSorter
 */
class SequencesSorter
{
    /**
     * Name of key identifying element that should be later in sequence
     *
     * @var string
     */
    protected $nextKeyName;

    /**
     * Name of key identifying element that should be earlier in sequence
     *
     * @var string
     */
    protected $prevKeyName;

    /**
     * @param string $nextKeyName
     * @param string $prevKeyName
     */
    public function __construct($nextKeyName = 'next', $prevKeyName = 'prev')
    {
        $this->nextKeyName = $nextKeyName;
        $this->prevKeyName = $prevKeyName;
    }

    /**
     * Sort array according to next and prev keys of it's elements
     *
     * @param array $array
     * @param string $firstKey
     * @return array
     * @throws \Exception
     */
    public function sort(array $array, $firstKey = null)
    {
        if (!count($array)) {
            throw new \Exception('Empty array is passed to SequenceSorter!');
        }
        if (null === $firstKey) {
            $firstKey = key($array);
        } elseif (!isset($array[$firstKey])) {
            throw new \Exception("First key $firstKey is absent in sequence!");
        }

        $linksMap = $this->createLinksMap($array);

        $sortedSequence = [];

        if (!isset($linksMap[$firstKey][$this->prevKeyName])) {
            $sortedSequence = [$firstKey => $array[$firstKey]];
            unset($array[$firstKey]);
        }

        foreach ($array as $key => $value) {
            if (!isset($linksMap[$key][$this->prevKeyName])) {
                $sortedSequence[$key] = $value;
                unset($array[$key]);
            }
        }

        while (count($array)) {
            $progressFlag = false;
            foreach ($array as $key => $value) {
                $elementCanBeInserted = true;
                foreach ($linksMap[$key][$this->prevKeyName] as $prevKey) {
                    if (!isset($sortedSequence[$prevKey])) {
                        $elementCanBeInserted = false;
                    }
                }
                if ($elementCanBeInserted) {
                    $sortedSequence[$key] = $value;
                    unset($array[$key]);
                    $progressFlag = true;
                }
            }
            if (!$progressFlag) {
                throw new \Exception('Cannot sort sequence! Exiting to prevent infinite loop!');
            }
        }
        return $sortedSequence;
    }

    /**
     * Create map of links for each element of array
     *
     * @param array $array
     * @return array
     * @throws \Exception
     */
    protected function createLinksMap(array $array)
    {
        $linksMap = [];
        foreach ($array as $key => $value) {
            if (isset($value[$this->nextKeyName])) {
                $this->assertKeyInArray($value[$this->nextKeyName], $array);
                $this->addLinkToMap($key, $value[$this->nextKeyName], $linksMap);
            }
            if (isset($value[$this->prevKeyName])) {
                $this->assertKeyInArray($value[$this->prevKeyName], $array);
                $this->addLinkToMap($value[$this->prevKeyName], $key, $linksMap);
            }
        }
        return $linksMap;
    }

    /**
     * Add record for earlier and later keys to links map
     *
     * @param string $earlierKey
     * @param string $laterKey
     * @param array $linksMap
     * @throws \Exception
     */
    protected function addLinkToMap($earlierKey, $laterKey, array &$linksMap)
    {
        if (isset($linksMap[$earlierKey][$this->prevKeyName][$laterKey])
            || isset($linksMap[$laterKey][$this->nextKeyName][$earlierKey])
        ) {
            throw new \Exception(sprintf('Circular dependency between keys "%s" and "%s"!', $earlierKey, $laterKey));
        }
        $linksMap[$earlierKey][$this->nextKeyName][$laterKey] = $laterKey;
        $linksMap[$laterKey][$this->prevKeyName][$earlierKey] = $earlierKey;
    }

    /**
     * Throw exception if passed key is not present in passed array
     *
     * @param string $key
     * @param array $array
     * @throws \Exception
     */
    protected function assertKeyInArray($key, array $array)
    {
        if (!isset($array[$key])) {
            throw new \Exception(sprintf('Referenced key "%s" does not exist in sequence!', $key));
        }
    }
}
