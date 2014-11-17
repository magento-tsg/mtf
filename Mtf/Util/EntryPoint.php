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

namespace Mtf\Util;

use Mtf\Util\Generate\BlockFactory;
use Mtf\Util\Generate\PageFactory;
use Mtf\Util\Generate\FixtureFactory;
use Mtf\Util\Generate\RepositoryFactory;
use Mtf\Util\Generate\HandlerFactory;

/**
 * Class EntryPoint
 */
class EntryPoint
{
    /**
     * Configuration parameters
     *
     * @var array
     */
    protected $_params;

    /**
     * Initialize configuration parameters
     *
     * @param array $params
     */
    public function __construct($params)
    {
        $this->_params = $params;
    }

    /**
     * Start to generate all classes
     * @return void
     */
    public function processRequest()
    {
        try {
            $blocks = new BlockFactory($this->_params);
            $blocks->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $pages = new PageFactory($this->_params);
            $pages->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $fixtures = new FixtureFactory($this->_params);
            $fixtures->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $blocks = new RepositoryFactory($this->_params);
            $blocks->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $handlers = new HandlerFactory($this->_params);
            $handlers->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }
    }
}
