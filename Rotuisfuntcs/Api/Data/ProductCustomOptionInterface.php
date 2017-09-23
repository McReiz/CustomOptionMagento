<?php
/**
 * Copyright © 2017 elReiz, Inc. All rights reserved.
 * Visit my website: http://elreiz.com
 */

namespace Reiz\Rotuisfuntcs\Api\Data;

interface ProductCustomOptionInterface extends \Magento\Catalog\Api\Data\ProductCustomOptionInterface
{
     /**
     * @return string|null
     */
	public function getWeightHeight();

    /**
     * @return string|null
     */
    
    public function getWeightWidth();

    /**
     * @param string $fieldone
     * @return $this
     */
    public function setWeightHeight($fieldone);


    /**
     * @param string $wwidth
     * @return $this
     */
    public function setWeightWidth($wwidth);
}