<?php
/**
 * Magento 2 extension for Cashew Payments
 */

namespace CashewPayments\Controller\Checkout;

use CashewPayments\Helper\Api as ApiHelper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory as ResultRawFactory;

class Index extends Action
{
    const API_POST = 'checkouts';

    protected $resultRawFactory;
    protected $apiHelper;

    /**
     * @param Context $context
     * @param ResultRawFactory $resultRawFactory
     * @param ApiHelper $apiHelper
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
                $data = $this->apiHelper->postData($token, $jsonData, self::API_POST);
                $data['data']['storeToken'] = $token;

                $response = $this->resultRawFactory->create()
                    ->setHeader('Content-type', 'application/json')
                    ->setContents(json_encode($data));

                return $response;
            }
        }
    }
}
