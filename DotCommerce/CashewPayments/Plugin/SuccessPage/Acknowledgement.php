<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace DotCommerce\CashewPayments\Controller\Onepage;

use DotCommerce\CashewPayments\Helper\Config;
use DotCommerce\CashewPayments\Helper\Api as ApiHelper;
use Psr\Log\LoggerInterface;

class Success extends \Magento\Checkout\Controller\Onepage
{
    const API_POST = 'stores/magento/order/status';
    protected $apiHelper;
    /**
     * @var Config
     */
    protected $config;
    protected $logger;
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Checkout\Model\Session $checkoutSession,
        ApiHelper $apiHelper,
        Config $config,
        LoggerInterface $logger

    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_checkoutSession = $checkoutSession;
        $this->apiHelper = $apiHelper;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }
    
    public function execute()
    {

        $this->logger->debug('Executing');
        $session = $this->getOnepage()->getCheckout();
        if (!$this->_objectManager->get(\Magento\Checkout\Model\Session\SuccessValidator::class)->isValid()) {
            $data = [
                'orderReference' => $session->getLastOrderId(),
                'orderStatus'   => 'CONFIRMED'
            ];
            $token = $this->apiHelper->getToken();
            $this->apiHelper
                ->postData($token, json_encode($data), self::API_POST);
        }
        return $resultPage;
    }

    public function beforeExecute(\Magento\Checkout\Controller\Onepage\Success $subject)
    {
        $this->logger->debug('before Executing');
        $currentOrder = $this->_checkoutSession->getLastRealOrder();
        $this->_coreRegistry->register('current_order', $currentOrder);
    }
}