<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DotCommerce\CashewPayments\Checkout\Observer; 
use DotCommerce\CashewPayments\Helper\Api as ApiHelper;
use Magento\Framework\Event\ObserverInterface;
class Acknowledgement implements ObserverInterface { 


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

        //get Order All Item
        $itemCollection = $order->getItemsCollection();
        $customer = $order->getCustomerId(); // using this id you can get customer name
        $this->logger->debug('success:');
        $this->logger->debug($customer);
    }
}