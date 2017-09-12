<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Customers defined options
 */
namespace Reiz\Rotuisfuntcs\Block\Adminhtml\Product\Edit\Tab\Options;

//suse Magento\Backend\Block\Widget;
//use Magento\Catalog\Model\Product;

//namespace A\Cust\Block\Adminhtml\Product\Edit\Tab\Options;

class Option extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option
    {
        /**
         * Class constructor
         */
        public function _construct()
        {
            parent::_construct();
            $this->setTemplate('Magento_Catalog::product/edit/options/option.phtml');
        }

    /**
     * Retrieve html templates for different types of product custom options
     *
     * @return string
     */
    public function getTemplatesHtml()
    {
        $canEditPrice = $this->getCanEditPrice();
        $canReadPrice = $this->getCanReadPrice();
 
        $this->getChildBlock('rotimpresions_option_type')
            ->setCanReadPrice($canReadPrice)
            ->setCanEditPrice($canEditPrice);

        $this->getChildBlock('rotimpresionselect_option_type')
            ->setCanReadPrice($canReadPrice)
            ->setCanEditPrice($canEditPrice);

        $templates = parent::getTemplatesHtml() . "\n" .
            $this->getChildHtml('rotimpresions_option_type'). "\n" .
            $this->getChildHtml('rotimpresionselect_option_type');
            
        return $templates;    
    }     
    public function getOptionValues()
    {
        $optionsArr = $this->getProduct()->getOptions();
        if ($optionsArr == null) {
            $optionsArr = [];
        }

        if (!$this->_values || $this->getIgnoreCaching()) {
            $showPrice = $this->getCanReadPrice();
            $values = [];
            $scope = (int)$this->_scopeConfig->getValue(
                \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            foreach ($optionsArr as $option) {
                /* @var $option \Magento\Catalog\Model\Product\Option */
                print_r($option);
                
                $this->setItemCount($option->getOptionId());

                $value = [];

                $value['id'] = $option->getOptionId();
                $value['item_count'] = $this->getItemCount();
                $value['option_id'] = $option->getOptionId();
                $value['title'] = $option->getTitle();
                $value['type'] = $option->getType();
                $value['is_require'] = $option->getIsRequire();
                $value['sort_order'] = $option->getSortOrder();
                $value['can_edit_price'] = $this->getCanEditPrice();

                if ($this->getProduct()->getStoreId() != '0') {
                    $value['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                        $option->getOptionId(),
                        'title',
                        is_null($option->getStoreTitle())
                    );
                    $value['scopeTitleDisabled'] = is_null($option->getStoreTitle()) ? 'disabled' : null;
                }

                if ($option->getGroupByType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT) {
                    $i = 0;
                    $itemCount = 0;
                    foreach ($option->getValues() as $_value) {
                        /* @var $_value \Magento\Catalog\Model\Product\Option\Value */
                        $value['optionValues'][$i] = [
                            'item_count' => max($itemCount, $_value->getOptionTypeId()),
                            'option_id' => $_value->getOptionId(),
                            'option_type_id' => $_value->getOptionTypeId(),
                            'title' => $_value->getTitle(),
                            'price' => $showPrice ? $this->getPriceValue(
                                $_value->getPrice(),
                                $_value->getPriceType()
                            ) : '',
                            'price_type' => $showPrice ? $_value->getPriceType() : 0,
                            'sku' => $_value->getSku(),
                            'sort_order' => $_value->getSortOrder(),
                        ];

                        if ($this->getProduct()->getStoreId() != '0') {
                            $value['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                                $_value->getOptionId(),
                                'title',
                                is_null($_value->getStoreTitle()),
                                $_value->getOptionTypeId()
                            );
                            $value['optionValues'][$i]['scopeTitleDisabled'] = is_null(
                                $_value->getStoreTitle()
                            ) ? 'disabled' : null;
                            if ($scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE) {
                                $value['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                                    $_value->getOptionId(),
                                    'price',
                                    is_null($_value->getstorePrice()),
                                    $_value->getOptionTypeId(),
                                    ['$(this).up(1).previous()']
                                );
                                $value['optionValues'][$i]['scopePriceDisabled'] = is_null(
                                    $_value->getStorePrice()
                                ) ? 'disabled' : null;
                            }
                        }
                        $i++;
                    }
                } else {
                    $value['price'] = $showPrice ? $this->getPriceValue(
                        $option->getPrice(),
                        $option->getPriceType()
                    ) : '';
                    $value['price_type'] = $option->getPriceType();
                    $value['sku'] = $option->getSku();
                    $value['max_characters'] = $option->getMaxCharacters();
                    $value['file_extension'] = $option->getFileExtension();
                    $value['image_size_x'] = $option->getImageSizeX();
                    $value['image_size_y'] = $option->getImageSizeY();
                    if ($this->getProduct()->getStoreId() != '0'
                        && $scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE
                    ) {
                        $value['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                            $option->getOptionId(),
                            'price',
                            is_null($option->getStorePrice())
                        );
                        $value['scopePriceDisabled'] = is_null($option->getStorePrice()) ? 'disabled' : null;
                    }
                }
                $values[] = new \Magento\Framework\DataObject($value);
            }
            $this->_values = $values;
        }

        return $this->_values;
    }  
}