<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DotCommerce\CashewPayments\Observer; 
use DotCommerce\CashewPayments\Helper\Api as ApiHelper;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\ObserverInterface;
class Acknowledgement implements ObserverInterface { 
    const API_POST = 'stores/magento/order/status';

    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */
    protected $order;
    /**
     * Param to save api helper
     * 
     * @var ApiHelper
     */
    protected $apiHelper;

    /**
     * Param to save logger
     * 
     * @var LoggerInterface
     */
    protected $logger;

     public function __construct(
        \Magento\Sales\Model\Order $order,
        ApiHelper $apiHelper,
        LoggerInterface $logger
    )
    {
        $this->order = $order;
        $this->apiHelper = $apiHelper;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrderIds();
        $order = $this->order->load($orderId);
        if ($order->getPayment()->getMethod() == 'cashewpayment') {
            $data = [
                'orderReference' => $orderId,
                'orderStatus' => 'CONFIRMED'
            ];
            $this->logger->debug(json_encode($data));
                // $token = $this->apiHelper->getToken();
                // $response = $this->apiHelper
                //     ->postData($token, json_encode($data), self::API_POST);
        }
    }
}