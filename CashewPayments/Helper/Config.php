<?php

namespace CashewPayments\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const API_KEY         = 'payment/cashewpayment/api_key';
    const STORE_URL       = 'payment/cashewpayment/store_url';
    const MIN_ORDER_TOTAL = 'payment/cashewpayment/min_order_total';
    const MAX_ORDER_TOTAL = 'payment/cashewpayment/max_order_total';

    public function apiKey()
    {
        return $this->scopeConfig->getValue(
            self::API_KEY,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function storeUrl()
    {
        return $this->scopeConfig->getValue(
            self::STORE_URL,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getMinimumOrderTotal()
    {
        return $this->scopeConfig->getValue(
            self::MIN_ORDER_TOTAL,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getMaximumOrderTotal()
    {
        return $this->scopeConfig->getValue(
            self::MAX_ORDER_TOTAL,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
