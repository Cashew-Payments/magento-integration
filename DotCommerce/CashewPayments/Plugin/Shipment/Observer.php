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

    public function __construct(
        ApiHelper $apiHelper,
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
        $this->logger->debug('Increment ID: ' . $order->getIncrementId());
        $this->logger->debug('Entity ID: ' . $order->getEntityId());
        $this->logger->debug('Payment Method: ' . $cashewPayment);
        $this->logger->debug('Shippments count: ' . $order->getShipmentsCollection()->count());
        $this->logger->debug('Object: ' . print_r(json_encode($shipment->getAllItems()), true));

        $shipmentObj = $observer->getEvent()->getShipment();

        foreach ($shipmentObj->getItemsCollection() as $orderItem) {
            if (!$orderItem->getParentItem()) {
                $qty = $orderItem->getQtyOrdered();
                $this->logger->debug('Max: ' . $orderItem->getTotalQty());
                if (!$order->getReordered()) {
                    $qty -= max($orderItem->getQtyShipped(), $orderItem->getQtyInvoiced());
                }
                $this->logger->debug('Item shipped: ' . $qty . ' itemId: ' . $orderItem->getId());
            }
        }



        if ($cashewPayment == 'cashewpayment') {
            $token = $this->apiHelper->getToken();
            $data = [
                'orderReference' => $order->getIncrementId(),
                'entityId' => $order->getEntityId(),
                'numShippedItems' => []
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
