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
namespace Mtf\System\Event;

/**
 * Class for keeping State of the system
 */
class State
{
    /**
     * Name for current Test suite
     *
     * @var string
     */
    private static $testSuiteName;

    /**
     * Name for current Test class
     *
     * @var string
     */
    private static $testClassName;

    /**
     * Name for current Test method
     *
     * @var string
     */
    private static $testMethodName;

    /**
     * Url of current page
     *
     * @var string
     */
    private $pageUrl = 'about:blank';

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var string
     */
    private $appStateName = 'No AppState Applied';

    /**
     * @var string
     */
    private $stageName = 'Main Test Flow';

    /**
     * @param EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * Setter for testSuiteName
     *
     * @param string $testSuiteName
     */
    public static function setTestSuiteName($testSuiteName)
    {
        self::$testSuiteName = $testSuiteName;
    }

    /**
     * Setter for testClassName
     *
     * @param string $testClassName
     */
    public static function setTestClassName($testClassName)
    {
        self::$testClassName = $testClassName;
    }

    /**
     * Setter for testMethodName
     *
     * @param string $testMethodName
     */
    public static function setTestMethodName($testMethodName)
    {
        self::$testMethodName = $testMethodName;
    }

    /**
     * Setter for pageUrl
     *
     * @param string $pageUrl
     */
    public function setPageUrl($pageUrl)
    {
        if ($pageUrl && $this->pageUrl != $pageUrl) {
            $this->eventManager->dispatchEvent(
                ['page_changed'],
                [sprintf('Page changed from url %s to url %s', $this->pageUrl, $pageUrl)]
            );
            $this->pageUrl = $pageUrl;
        }
    }

    /**
     * Getter for $testSuiteName
     *
     * @return string
     */
    public static function getTestSuiteName()
    {
        return self::$testSuiteName ?: 'default';
    }

    /**
     * Getter for $testClassName
     *
     * @return string
     */
    public static function getTestClassName()
    {
        return self::$testClassName ?: 'default';
    }

    /**
     * Getter for $testMethodName
     *
     * @return string
     */
    public static function getTestMethodName()
    {
        return self::$testMethodName ?: 'default';
    }

    /**
     * Getter for current pageUrl
     *
     * @return string
     */
    public function getPageUrl()
    {
        return $this->pageUrl ?: 'default';
    }

    /**
     * Set application state name
     *
     * @param string $appStateName
     */
    public function setAppStateName($appStateName)
    {
        $this->appStateName = $appStateName;
    }

    /**
     * Get application state name
     *
     * @return string
     */
    public function getAppStateName()
    {
        return $this->appStateName;
    }

    /**
     * Set stage name (Currently persisting fixture class name or 'Main Test Flow')
     *
     * @param string $stageName
     */
    public function setStageName($stageName = null)
    {
        $this->stageName = $stageName ?: 'Main Test Flow';
    }

    /**
     * Get current stage name
     *
     * @return string
     */
    public function getStageName()
    {
        return $this->stageName;
    }
}
