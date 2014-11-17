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

namespace Mtf\Page;

use Mtf\Fixture\FixtureInterface;
use Mtf\Block\BlockInterface;

/**
 * Interface for Pages
 *
 * @api
 */
interface PageInterface
{
    /**
     * Prepare page according to fixture data
     *
     * @param FixtureInterface $fixture
     * @return void
     */
    public function init(FixtureInterface $fixture);

    /**
     * Open the page URL in browser
     *
     * @param array $params [optional]
     * @return $this
     */
    public function open(array $params = []);

    /**
     * Retrieve an instance of block
     *
     * @param string $blockName
     * @return BlockInterface
     */
    public function getBlockInstance($blockName);
}
