<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog product option factory
 */
namespace Reiz\Rotuisfuntcs\Model\Product\Option\Type;

class Factory extends \Magento\Catalog\Model\Product\Option\Type\Factory
{
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create product option
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Catalog\Model\Product\Option\Type\DefaultType
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($className, array $data = [])
    {
        $option = $this->_objectManager->create($className, $data);

        if (!$option instanceof \Magento\Catalog\Model\Product\Option\Type\DefaultType) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 doesn\'t extends \Magento\Catalog\Model\Product\Option\Type\DefaultType', $className)
            );
        }
        return $option;
    }
}
