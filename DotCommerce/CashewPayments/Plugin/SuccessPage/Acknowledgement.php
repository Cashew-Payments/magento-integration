<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace DotCommerce\CashewPayments\Controller\Onepage;

use DotCommerce\CashewPayments\Helper\Api as ApiHelper;

class Success extends \Magento\Checkout\Controller\Onepage
{
    const API_POST = 'refunds/merchant';
    protected $apiHelper;
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Checkout\Model\Session $checkoutSession,
        ApiHelper $apiHelper
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_checkoutSession = $checkoutSession;
        $this->apiHelper = $apiHelper;
    }

    public function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }
    
    public function execute()
    {
        $session = $this->getOnepage()->getCheckout();
        if (!$this->_objectManager->get(\Magento\Checkout\Model\Session\SuccessValidator::class)->isValid()) {
            $data = [
                'orderReference' => $session->getLastOrderId(),
                'refundAmount'   => 0
            ];
            $this->apiHelper
                ->postData($token, json_encode($data), self::API_POST);

            if ($response['status'] !== 'success') {
                $this->logger->debug('Refunded Errors:');
                $this->logger->debug(print_r(json_encode($response), true));
            }
        }
        return $resultPage;
    }

    public function beforeExecute(\Magento\Checkout\Controller\Onepage\Success $subject)
    {
        $currentOrder = $this->_checkoutSession->getLastRealOrder();
        $this->_coreRegistry->register('current_order', $currentOrder);
    }
}