<?php

/**
 * Magento 2 extension for Cashew Payments
 * Action to send details to cashew once a refund is issued
 * 
 * PHP version 7
 * 
 * @category Refund
 * @package  CashewPayments
 * @author   DotCommerce <mi@discretecommerce.com>
 * @license  https://www.cashewpayments.com/license.txt cashew License
 * @link     https://www.cashewpayments.com
 */

namespace DotCommerce\CashewPayments\Plugin\Creditmemo;

use DotCommerce\CashewPayments\Helper\Api as ApiHelper;

use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Psr\Log\LoggerInterface;


/**
 * Magento 2 extension for Cashew Payments
 * Class to support cashew integration
 * 
 * PHP version 7
 * 
 * @category Refund
 * @package  CashewPayments
 * @author   DotCommerce <mi@discretecommerce.com>
 * @license  https://www.cashewpayments.com/license.txt cashew License
 * @link     https://www.cashewpayments.com
 */
class Refund
{
    const API_POST = 'refunds/merchant';

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


    /**
     * Magento 2 extension for Cashew Payments
     * 
     * PHP version 7
     * 
     * @category Refund
     * @package  CashewPayments
     * @author   DotCommerce <mi@discretecommerce.com>
     * @license  https://www.cashewpayments.com/license.txt cashew License
     * @link     https://www.cashewpayments.com
     * 
     * @param $apiHelper api helper
     * @param $logger    logger
     */
    public function __construct(
        ApiHelper $apiHelper,
        LoggerInterface $logger
    ) {
        $this->apiHelper = $apiHelper;
        $this->logger = $logger;
    }

    /**
     * Magento 2 extension for Cashew Payments
     * Integrate refunds
     * 
     * @param $subject subject to save
     * @param $result  result
     * 
     * @return $result 
     */
    public function afterSave(
        CreditmemoRepositoryInterface $subject,
        $result
    ) {
        $orderId = $refundedOrder->getIncrementId();
        $refundedOrder  = $subject->get($orderId)->getOrder();
        $refundedAmount = $subject->get($orderId)->getGrandTotal();
        $cashewPayment  = $refundedOrder->getPayment()->getMethod();
        if ($cashewPayment == 'cashewpayment') {
            $data = [
                'orderReference' => $orderId,
                'refundAmount'   => $refundedAmount
            ];

            $token = $this->apiHelper->getToken();
            $response = $this->apiHelper
                ->postData($token, json_encode($data), self::API_POST);

            if ($response['status'] !== 'success') {
                $this->logger->debug('Refunded Errors:');
                $this->logger->debug(print_r(json_encode($response), true));
            }
        }

        return $result;
    }
}
