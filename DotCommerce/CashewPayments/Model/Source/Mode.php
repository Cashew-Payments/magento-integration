<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace DotCommerce\CashewPayments\Model\Source;

/**
 * Class Mode
 *
 * @package DotCommerce\CashewPayments\Model\Source
 */
class Mode implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'https://api.cashewpayments.com/v1/',
                'label' => 'Production',
            ],
            [
                'value' => 'https://api-sandbox.cashewpayments.com/v1/',
                'label' => 'Sandbox',
            ]
        ];
    }
}
