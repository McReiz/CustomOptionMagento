<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * customers defined options
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Reiz\Rotuisfuntcs\Block\Adminhtml\Product\Edit\Tab\Options\Type;

class Rotimpresionselect extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\AbstractType
{
    /**
     * @var string
     */
    //protected $_template = 'catalog/product/edit/options/type/select.phtml'
    protected $_template = 'Reiz_Rotuisfuntcs::catalog/product/edit/options/type/rotimpresionselect.phtml';

	protected function _construct()
    {
        parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getPriceTypeSelectHtml($extraParams = '')
    {

        return parent::getPriceTypeSelectHtml($extraParams);
    }
 
}
