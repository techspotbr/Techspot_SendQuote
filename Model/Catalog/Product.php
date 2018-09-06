<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Model\Catalog;

class Product extends \Magento\Catalog\Model\Product
{
    const ONLY_QUOTATION_ATTRIBUTE = 'only_for_quotation';

    /*
    * Return true if product avaiable only for quotation
    *
    * @return bool
    */
    public function isOnlyForQuotation()
    {
        if($this->_getData(self::ONLY_QUOTATION_ATTRIBUTE)){
            return true;
        }
        return false;
    }
}
