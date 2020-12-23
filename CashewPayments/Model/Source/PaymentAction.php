<?php
/**
 * Magento 2 extension for Cashew Payments
 */

namespace CashewPayments\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Payment\Model\MethodInterface;

class PaymentAction implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [[
            'value' => MethodInterface::ACTION_AUTHORIZE_CAPTURE,
            'label' => __('Authorize and Capture'),
        ]];
    }
}
