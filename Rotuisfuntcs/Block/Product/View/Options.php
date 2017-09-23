<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Product options block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Reiz\Rotuisfuntcs\Block\Product\View;

use Magento\Catalog\Model\Product;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Options extends \Magento\Catalog\Block\Product\View\Options
{
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get option html block
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    public function getOptionHtml(\Reiz\Rotuisfuntcs\Model\Product\Option $option)
    {
        
        
        if(\Reiz\Rotuisfuntcs\Model\Product\Option::OPTION_TYPE_ROTIM == $option->getType()){
            $customoption = \Reiz\Rotuisfuntcs\Model\Product\Option;
            $type = $this->getGroupOfOption($customoption->getType());
        }else{
            $type = $this->getGroupOfOption($option->getType());
        }
        $renderer = $this->getChildBlock($type);

        $or = ($option->getType() == \Reiz\Rotuisfuntcs\Model\Product\Option::OPTION_TYPE_ROTIM) ? $customoption : $option;
        $renderer->setProduct($this->getProduct())->setOption($or);

        return $this->getChildHtml($type, false);
    }
}