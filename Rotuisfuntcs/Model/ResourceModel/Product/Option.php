<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Reiz\Rotuisfuntcs\Model\ResourceModel\Product;

use Reiz\Rotuisfuntcs\Api\Data\ProductInterface;

/**
 * Catalog product custom option resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Option extends \Magento\Catalog\Model\ResourceModel\Product\Option
{
    
    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param string $connectionName
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Define main table and initialize connection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_option', 'option_id');
    }


    /**
     * Save value prices
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _saveValuePrices(\Magento\Framework\Model\AbstractModel $object)
    {
        $priceTable = $this->getTable('catalog_product_option_price');
        $connection = $this->getConnection();

        /*
         * Better to check param 'price' and 'price_type' for saving.
         * If there is not price skip saving price
         */

        if ($object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FIELD ||
            $object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_AREA ||
            $object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FILE ||
            $object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DATE ||
            $object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DATE_TIME ||
            $object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_TIME ||
            $object->getType() == \Reiz\Rotuisfuntcs\Model\Product\Option::OPTION_TYPE_ROTIM
        ) {
            //save for store_id = 0
            if (!$object->getData('scope', 'price')) {
                $statement = $connection->select()->from(
                    $priceTable,
                    'option_id'
                )->where(
                    'option_id = ?',
                    $object->getId()
                )->where(
                    'store_id = ?',
                    \Magento\Store\Model\Store::DEFAULT_STORE_ID
                );
                $optionId = $connection->fetchOne($statement);

                if ($optionId) {
                    $data = $this->_prepareDataForTable(
                        new \Magento\Framework\DataObject(
                            ['price' => $object->getPrice(), 'price_type' => $object->getPriceType()]
                        ),
                        $priceTable
                    );

                    $connection->update(
                        $priceTable,
                        $data,
                        [
                            'option_id = ?' => $object->getId(),
                            'store_id  = ?' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                        ]
                    );
                } else {
                    $data = $this->_prepareDataForTable(
                        new \Magento\Framework\DataObject(
                            [
                                'option_id' => $object->getId(),
                                'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                                'price' => $object->getPrice(),
                                'price_type' => $object->getPriceType(),
                            ]
                        ),
                        $priceTable
                    );
                    $connection->insert($priceTable, $data);
                }
            }

            $scope = (int)$this->_config->getValue(
                \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if ($object->getStoreId() != '0' && $scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE) {
                $baseCurrency = $this->_config->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    'default'
                );

                $storeIds = $this->_storeManager->getStore($object->getStoreId())->getWebsite()->getStoreIds();
                if (is_array($storeIds)) {
                    foreach ($storeIds as $storeId) {
                        if ($object->getPriceType() == 'fixed') {
                            $storeCurrency = $this->_storeManager->getStore($storeId)->getBaseCurrencyCode();
                            $rate = $this->_currencyFactory->create()->load($baseCurrency)->getRate($storeCurrency);
                            if (!$rate) {
                                $rate = 1;
                            }
                            $newPrice = $object->getPrice() * $rate;
                        } else {
                            $newPrice = $object->getPrice();
                        }

                        $statement = $connection->select()->from(
                            $priceTable
                        )->where(
                            'option_id = ?',
                            $object->getId()
                        )->where(
                            'store_id  = ?',
                            $storeId
                        );

                        if ($connection->fetchOne($statement)) {
                            $data = $this->_prepareDataForTable(
                                new \Magento\Framework\DataObject(
                                    ['price' => $newPrice, 'price_type' => $object->getPriceType()]
                                ),
                                $priceTable
                            );

                            $connection->update(
                                $priceTable,
                                $data,
                                ['option_id = ?' => $object->getId(), 'store_id  = ?' => $storeId]
                            );
                        } else {
                            $data = $this->_prepareDataForTable(
                                new \Magento\Framework\DataObject(
                                    [
                                        'option_id' => $object->getId(),
                                        'store_id' => $storeId,
                                        'price' => $newPrice,
                                        'price_type' => $object->getPriceType(),
                                    ]
                                ),
                                $priceTable
                            );
                            $connection->insert($priceTable, $data);
                        }
                    }
                }
            } elseif ($scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE && $object->getData('scope', 'price')
            ) {
                $connection->delete(
                    $priceTable,
                    ['option_id = ?' => $object->getId(), 'store_id  = ?' => $object->getStoreId()]
                );
            }
        }

        return $this;
    }

    /**
     * Duplicate custom options for product
     *
     * @param \Magento\Catalog\Model\Product\Option $object
     * @param int $oldProductId
     * @param int $newProductId
     * @return \Magento\Catalog\Model\Product\Option
     */
    public function duplicate(\Reiz\Rotuisfuntcs\Model\Product\Option $object, $oldProductId, $newProductId)
    {
        $connection = $this->getConnection();

        $optionsCond = [];
        $optionsData = [];

        // read and prepare original product options
        $select = $connection->select()->from(
            $this->getTable('catalog_product_option')
        )->where(
            'product_id = ?',
            $oldProductId
        );

        $query = $connection->query($select);

        while ($row = $query->fetch()) {
            $optionsData[$row['option_id']] = $row;
            $optionsData[$row['option_id']]['product_id'] = $newProductId;
            unset($optionsData[$row['option_id']]['option_id']);
        }

        // insert options to duplicated product
        foreach ($optionsData as $oId => $data) {
            $connection->insert($this->getMainTable(), $data);
            $optionsCond[$oId] = $connection->lastInsertId($this->getMainTable());
        }

        // copy options prefs
        foreach ($optionsCond as $oldOptionId => $newOptionId) {
            // title
            $table = $this->getTable('catalog_product_option_title');

            $select = $this->getConnection()->select()->from(
                $table,
                [new \Zend_Db_Expr($newOptionId), 'store_id', 'title']
            )->where(
                'option_id = ?',
                $oldOptionId
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $table,
                ['option_id', 'store_id', 'title'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            );
            $connection->query($insertSelect);

            // price
            $table = $this->getTable('catalog_product_option_price');

            $select = $connection->select()->from(
                $table,
                [new \Zend_Db_Expr($newOptionId), 'store_id', 'price', 'price_type']
            )->where(
                'option_id = ?',
                $oldOptionId
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $table,
                ['option_id', 'store_id', 'price', 'price_type'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            );
            $connection->query($insertSelect);

            $object->getValueInstance()->duplicate($oldOptionId, $newOptionId);
        }

        return $object;
    }
}
