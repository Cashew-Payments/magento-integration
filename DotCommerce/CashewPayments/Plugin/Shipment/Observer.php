<?php

namespace DotCommerce\CashewPayments\Plugin\Shipment;

use Magento\Framework\Event\ObserverInterface;

use DotCommerce\CashewPayments\Helper\Api as ApiHelper;

use Psr\Log\LoggerInterface;

class Observer implements ObserverInterface
{
    const API_POST = 'stores/magento/dispatch';

    protected $apiHelper;
    protected $logger;

    public function __construct(ApiHelper $apiHelper,
        LoggerInterface $logger
    ) {
        $this->apiHelper = $apiHelper;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $cashewPayment  = $order->getPayment()->getMethod();
        $this->logger->debug('Shippment: '.$cashewPayment);
        $this->logger->debug($order->getIncrementId());
        $this->logger->debug($order->getEntityId());
        $this->logger->debug($cashewPayment);
        $this->logger->debug($order->getShipmentsCollection()->count());
        if ($cashewPayment == 'cashewpayment') {
            $token = $this->apiHelper->getToken();
            $data = [
            'orderReference' => $order->getIncrementId(),
            'entityId' => $order->getEntityId()
            ];
            $response = $this->apiHelper
                ->postData($token, json_encode($data), self::API_POST);

            if ($response['status'] !== 'success') {
                $this->logger->debug('Dispatch Errors:');
                $this->logger->debug(print_r(json_encode($response), true));
            } else {
                $this->logger->debug('Dispatch Success:');
                $this->logger->debug(print_r(json_encode($response), true));
            }
        }
        return $response;
    }
}
