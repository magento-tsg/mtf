<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Data\Argument\Interpreter;

use Magento\Mtf\Data\Argument\InterpreterInterface;
use Magento\Mtf\Stdlib\BooleanUtils;

/**
 * Interpreter of boolean data type, such as boolean itself or boolean string
 */
class Boolean implements InterpreterInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils)
    {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * {@inheritdoc}
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (!isset($data['value'])) {
            throw new \InvalidArgumentException('Boolean value is missing.');
        }
        $value = $data['value'];
        return $this->booleanUtils->toBoolean($value);
    }
}
