<?php

namespace DotCommerce\CashewPayments\Plugin\Shipment;

use Magento\Framework\Event\ObserverInterface;

use DotCommerce\CashewPayments\Helper\Api as ApiHelper;

use Psr\Log\LoggerInterface;
class Observer {
    const API_POST = 'refunds/merchant';

    /**
     * Param to save api helper
     * 
     * @var ApiHelper
     */
    protected $apiHelper;
    protected $logger;
    
    public function __construct( 
        ApiHelper $apiHelper,
        LoggerInterface $logger
    ) {
        $this->apiHelper = $apiHelper;
        $this->logger = $logger;
    }
    public function eventLogging(\Magento\Framework\Object $observer) {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $cashewPayment  = $order->getPayment()->getMethod();
        $this->logger->debug('Shippment');
        if ($cashewPayment == 'cashewpayment') {
            $token = $this->apiHelper->getToken();
            $data = [
                'orderStatus' => 'DISPATCHED'
            ];
            $response = $this->apiHelper
                ->postData($token, json_encode($data), self::API_POST);

            if ($response['status'] !== 'success') {
                $this->logger->debug('Dispatch Errors:');
                $this->logger->debug(print_r(json_encode($response), true));
            }
        }
    }
}
