<?php
/**
 * Magento 2 extension for Cashew Payments
 */

namespace DotCommerce\CashewPayments\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Payment\Model\MethodInterface;

class PaymentAction implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [[
            'value' => MethodInterface::ACTION_AUTHORIZE,
            'label' => __('Authorize'),
        ]];
    }
}
