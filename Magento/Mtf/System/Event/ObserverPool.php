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
namespace Magento\Mtf\System\Event;

use Magento\Mtf\ObjectManager;

/**
 * Class ObserverPool
 */
class ObserverPool
{
    /**
     * @var \Magento\Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Mtf\System\Event\ObserverInterface[]
     */
    protected $observerPool;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Returns instance of observer
     *
     * @param string $class
     * @return ObserverInterface
     * @throws \InvalidArgumentException
     */
    public function getObserver($class)
    {
        if (empty($this->observerPool[$class])) {
            $instance = $this->objectManager->create($class);
            if (!$instance instanceof ObserverInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Observer class %s should implement ObserverInterface.', $class)
                );
            }
            $this->observerPool[$class] = $instance;
        }
        return $this->observerPool[$class];
    }
}