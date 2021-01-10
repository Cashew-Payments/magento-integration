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
        ApiHelper $apiHelper
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->apiHelper = $apiHelper;

        parent::__construct($context);
    }

    public function execute()
    {
        $orderId = $this->_request->getParam('orderId');

        if ($orderId) {
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
        }
    }
}
