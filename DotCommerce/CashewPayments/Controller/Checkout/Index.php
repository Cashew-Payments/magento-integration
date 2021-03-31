<?php
/**
 * Magento 2 extension for Cashew Payments
 * 
 * PHP version 7
 * 
 * @category Checkout
 * @package  CashewPayments
 * @author   DotCommerce <mi@discretecommerce.com>
 * @license  https://www.cashewpayments.com/license.txt cashew License
 * @link     https://www.cashewpayments.com
 */

namespace DotCommerce\CashewPayments\Controller\Checkout;

use DotCommerce\CashewPayments\Helper\Api as ApiHelper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory as ResultRawFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface as Logger;

/**
 * Magento 2 extension for Cashew Payments
 * 
 * PHP version 7
 * 
 * @category Checkout
 * @package  CashewPayments
 * @author   DotCommerce <mi@discretecommerce.com>
 * @license  https://www.cashewpayments.com/license.txt cashew License
 * @link     https://www.cashewpayments.com
 */
class Index extends Action
{
    const API_POST = 'checkouts';

    protected $resultRawFactory;
    protected $apiHelper;
    protected $orderFactory;

    protected $logger;

    
    /**
     * Magento 2 extension for Cashew Payments
     * 
     * PHP version 7
     * 
     * @category Checkout
     * @package  CashewPayments
     * @author   DotCommerce <mi@discretecommerce.com>
     * @license  https://www.cashewpayments.com/license.txt cashew License
     * @link     https://www.cashewpayments.com
     * 
     * @param Context          $context          Context
     * @param ResultRawFactory $resultRawFactory Result raw
     * @param ApiHelper        $apiHelper        Api helper
     */
    public function __construct(
        Context $context,
        ResultRawFactory $resultRawFactory,
        ApiHelper $apiHelper,
        Logger $logger,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->apiHelper = $apiHelper;
        $this->orderFactory = $orderFactory;
        $this->logger = $logger;

        parent::__construct($context);
    }

    public function execute()
    {
        $orderId = $this->_request->getParam('orderId');
        $this->logger->debug('PARAMS :: ' . print_r($this->_request->getProperties(), true));
        if ($orderId) {
            $order = $this->orderFactory->create();
            $order->load($orderId);
            $order->setStatus(Order::STATE_PENDING_PAYMENT)->save();
            $token = $this->apiHelper->getToken();
            if ($token) {
                $jsonData = $this->apiHelper->checkout($orderId);
                $data = $this->apiHelper
                    ->postData($token, $jsonData, self::API_POST);
                $data['data']['storeToken'] = $token;

                $response = $this->resultRawFactory->create()
                    ->setHeader('Content-type', 'application/json')
                    ->setContents(json_encode($data));

                return $response;
            }
        } else {
            $response = $this->resultRawFactory->create()
                ->setHeader('Content-type', 'application/json')
                ->setContents(json_encode([]));
            return $response;
        }
    }
}
