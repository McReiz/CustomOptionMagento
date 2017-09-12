<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Reiz\Rotuisfuntcs\Model\Product;

use Reiz\Rotuisfuntcs\Api\Data\ProductCustomOptionInterface;

/**
 * Catalog product option model
 *
 * @method \Magento\Catalog\Model\ResourceModel\Product\Option getResource()
 * @method int getProductId()
 * @method \Magento\Catalog\Model\Product\Option setProductId(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Option extends \Magento\Catalog\Model\Product\Option implements ProductCustomOptionInterface
{   
    const OPTION_GROUP_ROTIM = 'rotimpresion';
    const OPTION_TYPE_ROTIM = 'medidas_options';

    const KEY_WEIGHT_ONE = 'fieldone';
    const KEY_WEIGHT_TWO = 'fieldtwo';
    
    protected function _construct()
  {
    parent::_construct();
  }

    /**
     * Get group name of option by given option type
     *
     * @param string $type
     * @return string
     */
    public function getGroupByType($type = null)
    {
        if ($type === null) {
            $type = $this->getType();
        }
        $optionGroupsToTypes = [
            parent::OPTION_TYPE_FIELD => parent::OPTION_GROUP_TEXT,
            parent::OPTION_TYPE_AREA => parent::OPTION_GROUP_TEXT,
            parent::OPTION_TYPE_FILE => parent::OPTION_GROUP_FILE,
            parent::OPTION_TYPE_DROP_DOWN => parent::OPTION_GROUP_SELECT,
            parent::OPTION_TYPE_RADIO => parent::OPTION_GROUP_SELECT,
            parent::OPTION_TYPE_CHECKBOX => parent::OPTION_GROUP_SELECT,
            parent::OPTION_TYPE_MULTIPLE => parent::OPTION_GROUP_SELECT,
            parent::OPTION_TYPE_DATE => parent::OPTION_GROUP_DATE,
            parent::OPTION_TYPE_DATE_TIME => parent::OPTION_GROUP_DATE,
            parent::OPTION_TYPE_TIME => parent::OPTION_GROUP_DATE,
            self::OPTION_TYPE_ROTIM => self::OPTION_GROUP_ROTIM,
        ];

        return isset($optionGroupsToTypes[$type]) ? $optionGroupsToTypes[$type] : '';
    }

    /**
     * Group model factory
     *
     * @param string $type Option type
     * @return \Magento\Catalog\Model\Product\Option\Type\DefaultType
     * @throws LocalizedException
     */
    public function groupFactory($type)
    {
        $group = $this->getGroupByType($type);
        if (!empty($group)) {
            return $this->optionTypeFactory->create(
                'Magento\Catalog\Model\Product\Option\Type\\' . $this->string->upperCaseWords($group)
            );
        }
        throw new LocalizedException(__('The option type to get group instance is incorrect.'));
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function beforeSave()
    {
        parent::beforeSave();
        if ($this->getData('previous_type') != '') {
            $previousType = $this->getData('previous_type');

            /**
             * if previous option has different group from one is came now
             * need to remove all data of previous group
             */
            if ($this->getGroupByType($previousType) != $this->getGroupByType($this->getData('type'))) {
                switch ($this->getGroupByType($previousType)) {
                    case parent::OPTION_GROUP_SELECT:
                        $this->unsetData('values');
                        if ($this->getId()) {
                            $this->getValueInstance()->deleteValue($this->getId());
                        }
                        break;
                    case parent::OPTION_GROUP_FILE:
                        $this->setData('file_extension', '');
                        $this->setData('image_size_x', '0');
                        $this->setData('image_size_y', '0');
                        break;
                    case parent::OPTION_GROUP_TEXT:
                        $this->setData('max_characters', '0');
                        break;
                    case self::OPTION_GROUP_ROTIM:
                        $this->setData('fieldone', '');
                        $this->setData('fieldtwo', '');
                    case parent::OPTION_GROUP_DATE:
                        break;
                }
                if ($this->getGroupByType($this->getData('type')) == parent::OPTION_GROUP_SELECT) {
                    $this->setData('sku', '');
                    $this->unsetData('price');
                    $this->unsetData('price_type');
                    if ($this->getId()) {
                        $this->deletePrices($this->getId());
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
   /* public function afterSave()
    {
        $this->getValueInstance()->unsetValues();
        $values = $this->getValues() ?: $this->getData('values');
        if (is_array($values)) {
            foreach ($values as $value) {
                if ($value instanceof \Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface) {
                    $data = $value->getData();
                } else {
                    $data = $value;
                }
                $this->getValueInstance()->addValue($data);
            }

            $this->getValueInstance()->setOption($this)->saveValues();
        } elseif ($this->getGroupByType($this->getType()) == self::OPTION_GROUP_SELECT) {
            throw new LocalizedException(__('Select type options required values rows.'));
        }

        return parent::afterSave();
    }*/

    public function getWeightHeight()
    {
        return $this->getData(self::KEY_WEIGHT_ONE);
    }

    /**
     * @return int|null
     */
    public function getWeightWidth()
    {
        return $this->getData(self::KEY_WEIGHT_TWO);
    }

    
    public function setWeightHeight($wheight)
    {
        return $this->setData(self::KEY_WEIGHT_ONE, $wheight);
    }

 
    public function setWeightWidth($wwidth)
    {
        return $this->setData(self::KEY_WEIGHT_TWO, $wwidth);
    }
}