<?php
/**
 * Magento 2 extension for Cashew Payments
 * Integrate refunds
 */

namespace DotCommerce\CashewPayments\Plugin\Creditmemo;

use DotCommerce\CashewPayments\Helper\Api as ApiHelper;

use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Psr\Log\LoggerInterface;

class Refund
{
    const API_POST = 'refunds/merchant';

    /**
     * @var ApiHelper
     */
    protected $apiHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ApiHelper $apiHelper,
        LoggerInterface $logger
    ) {
        $this->apiHelper = $apiHelper;
        $this->logger = $logger;
    }

    public function afterSave(
        CreditmemoRepositoryInterface $subject,
        $result
    ) {
        $orderId = $result->getIncrementId();
        $refundedOrder  = $subject->get($orderId)->getOrder();
        $refundedAmount = $subject->get($orderId)->getGrandTotal();
        $cashewPayment  = $refundedOrder->getPayment()->getMethod();
        /*$this->logger->debug('Method: ' . $cashewPayment);*/

        if ($cashewPayment == 'cashewpayment') {
            $data = [
                'orderReference' => $orderId,
                'refundAmount'   => $refundedAmount
            ];

            $token = $this->apiHelper->getToken();
            $response = $this->apiHelper->postData($token, json_encode($data), self::API_POST);

            if ($response['status'] !== 'success') {
                $this->logger->debug('Refunded Errors:');
                $this->logger->debug(print_r(json_encode($response), true));
            }
        }

        return $result;
    }
}
