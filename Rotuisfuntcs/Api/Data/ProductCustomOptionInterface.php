<?php
/**
 * Copyright © 2017 elReiz, Inc. All rights reserved.
 * Visit my website: http://elreiz.com
 */

namespace Reiz\Rotuisfuntcs\Api\Data

interface ProductCustomOptionInterfaceExtend extends Magento\Catalog\Api\Data\ProductCustomOptionInterface
{
	public function getWeightHeight();

    /**
     * @param string $imageSizeX
     * @return $this
     */
    public function setWeightHeight($wheight);

    /**
     * @return string|null
     */
    public function getWeightWidth();

    /**
     * @param string $imageSizeY
     * @return $this
     */
    public function setWeightWidth($wwidth);
}