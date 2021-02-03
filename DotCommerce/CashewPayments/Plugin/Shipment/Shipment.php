<?php
namespace DotCommerce\CashewPayments\Plugin\Shipment;

use Magento\Framework\Event\ObserverInterface;

use DotCommerce\CashewPayments\Helper\Api as ApiHelper;

use Psr\Log\LoggerInterface;

class ProcessShipment implements ObserverInterface
{
    const API_POST = 'refunds/merchant';

    /**
     * Param to save api helper
     * 
     * @var ApiHelper
     */
    protected $apiHelper;
    protected $logger;

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer, 
    ApiHelper $apiHelper,
    LoggerInterface $logger)
    {
        $this->apiHelper = $apiHelper;
        $this->logger = $logger;
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