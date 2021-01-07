<?php

namespace DotCommerce\CashewPayments\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\HTTP\Client\Curl;

use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface as Logger;

class Api extends AbstractHelper
{
    protected $curl;

    protected $logger;

    protected $orderFactory;

    protected $productRepository;

    protected $storeManager;

    protected $countryFactory;

    protected $config;

    const API_TOKEN = "identity/store/authorize";

    public function __construct(
        Curl $curl,
        Logger $logger,
        UrlInterface $url,
        ResponseHttp $response,
        \Magento\Framework\Message\ManagerInterface $managerInterface,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \DotCommerce\CashewPayments\Helper\Config $config
    ) {
        $this->logger = $logger;
        $this->url = $url;
        $this->curl = $curl;
        $this->response = $response;
        $this->manager = $managerInterface;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->orderFactory = $orderFactory;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->countryFactory = $countryFactory;
        $this->config = $config;
    }

    public function getToken()
    {
        $headers = [
            'cashewSecretKey' => $this->config->apiKey(),
            'storeUrl' => $this->config->storeUrl()
        ];

        $this->curl->setHeaders($headers);
        $this->curl->post($this->config->apiDomain() . '' . self::API_TOKEN, []);

        $response = json_decode($this->curl->getBody(), true);

        /*$this->logger->debug(print_r($response,true));*/
        /*$this->logger->debug('STATUS :: ' . $response['status']);*/

        if ($response['status'] === 'success') {
            $token = $response['data']['token'];
            /*$this->logger->debug('TOKEN :: ' . $token);*/

            return $token;
        }

        return null;
    }

    public function checkout($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);

        $shippingCountryName = $this->countryFactory->create()->loadByCode($order->getShippingAddress()->getCountryId())->getName();
        $billingCountryName  = $this->countryFactory->create()->loadByCode($order->getBillingAddress()->getCountryId())->getName();

        $orderData = [
            'orderReference'    => !empty($order->getIncrementId()) ? $order->getIncrementId() : null,
            'totalAmount'       => !empty($order->getGrandTotal()) ? (int)$order->getGrandTotal() : null,
            'taxAmount'         => !empty($order->getTaxAmount()) ? (int)$order->getTaxAmount() : null,
            'currencyCode'      => !empty($order->getBaseCurrencyCode()) ? $order->getBaseCurrencyCode() : null,
            'shipping'          => [
                'reference'         => null,
                'name'              => null,
                'address'           => [
                    'firstName'         => !empty($order->getShippingAddress()->getFirstname()) ? $order->getShippingAddress()->getFirstname() : null,
                    'lastName'          => !empty($order->getShippingAddress()->getLastname()) ? $order->getShippingAddress()->getLastname() : null,
                    'phone'             => !empty($order->getShippingAddress()->getTelephone()) ? $order->getShippingAddress()->getTelephone() : 0,
                    'alternatePhone'    => 0,
                    'line1'             => !empty($order->getShippingAddress()->getStreet()[0]) ? $order->getShippingAddress()->getStreet()[0] : null,
                    'line2'             => !empty($order->getShippingAddress()->getStreet()[1]) ? $order->getShippingAddress()->getStreet()[1] : null,
                    'city'              => !empty($order->getShippingAddress()->getCity()) ? $order->getShippingAddress()->getCity() : null,
                    'state'             => !empty($order->getShippingAddress()->getRegion()) ? $order->getShippingAddress()->getRegion() : null,
                    'country'           => !empty($shippingCountryName) ? $shippingCountryName : null,
                    'postalCode'        => !empty($order->getShippingAddress()->getPostcode()) ? $order->getShippingAddress()->getPostcode() : null
                ],
            ],
            'billingAddress'    => [
                'firstName'         => !empty($order->getBillingAddress()->getFirstname()) ? $order->getBillingAddress()->getFirstname() : null,
                'lastName'          => !empty($order->getBillingAddress()->getLastname()) ? $order->getBillingAddress()->getLastname() : null,
                'phone'             => !empty($order->getBillingAddress()->getTelephone()) ? $order->getBillingAddress()->getTelephone() : null,
                'alternatePhone'    => 0,
                'line1'             => !empty($order->getBillingAddress()->getStreet()[0]) ? $order->getBillingAddress()->getStreet()[0] : null,
                'line2'             => !empty($order->getBillingAddress()->getStreet()[1]) ? $order->getBillingAddress()->getStreet()[1] : null,
                'city'              => !empty($order->getBillingAddress()->getCity()) ? $order->getBillingAddress()->getCity() : null,
                'state'             => !empty($order->getBillingAddress()->getRegion()) ? $order->getBillingAddress()->getRegion() : null,
                'country'           => !empty($billingCountryName) ? $billingCountryName : null,
                'postalCode'        => !empty($order->getBillingAddress()->getPostcode()) ? $order->getBillingAddress()->getPostcode() : null
            ],
            'customer'          => [
                'id'                => !empty($order->getCustomerId()) ? $order->getCustomerId() : null,
                'mobileNumber'      => !empty($order->getShippingAddress()->getTelephone()) ? $order->getShippingAddress()->getTelephone() : null,
                'email'             => !empty($order->getCustomerEmail()) ? $order->getCustomerEmail() : null,
                'firstName'         => !empty($order->getCustomerFirstname()) ? $order->getCustomerFirstname() : null,
                'lastName'          => !empty($order->getCustomerLastname()) ? $order->getCustomerLastname() : null,
                'gender'            => !empty($order->getCustomerGender()) ? $order->getCustomerGender() : null,
                'account'           => !empty($order->getCustomerId()) ? $order->getCustomerId() : null,
                'dateOfBirth'       => null,
                'dateJoined'        => null,
                'defaultAddress'        => [
                    'firstName'         => !empty($order->getShippingAddress()->getFirstname()) ? $order->getShippingAddress()->getFirstname() : null,
                    'lastName'          => !empty($order->getShippingAddress()->getLastname()) ? $order->getShippingAddress()->getLastname() : null,
                    'phone'             => !empty($order->getShippingAddress()->getTelephone()) ? $order->getShippingAddress()->getTelephone() : null,
                    'alternatePhone'    => '',
                    'line1'             => !empty($order->getShippingAddress()->getStreet()[0]) ? $order->getShippingAddress()->getStreet()[0] : null,
                    'line2'             => !empty($order->getShippingAddress()->getStreet()[1]) ? $order->getShippingAddress()->getStreet()[1] : null,
                    'city'              => !empty($order->getShippingAddress()->getCity()) ? $order->getShippingAddress()->getCity() : null,
                    'state'             => !empty($order->getShippingAddress()->getRegion()) ? $order->getShippingAddress()->getRegion() : null,
                    'country'           => !empty($shippingCountryName) ? $shippingCountryName : null,
                    'postalCode'        => !empty($order->getShippingAddress()->getPostcode()) ? $order->getShippingAddress()->getPostcode() : null
                ],
            ],
            'items'             => $this->getItems($orderId),
            'discounts'         => [],
            'merchant'          => [
                'confirmationUrl'   => $this->url->getUrl('checkout/onepage/success'),
                'cancelUrl'         => $this->url->getUrl('checkout/onepage/failure')
            ],
            'metaData'          => []
        ];

        /*$this->logger->debug(print_r($orderData,true));*/
        $orderData = json_encode($orderData, true);

        return $orderData;
    }

    public function postData($auth, $data, $endpoint)
    {
        $headers = ['Authorization' => $auth, 'Content-Type' => 'application/json'];

        $this->curl->setHeaders($headers);
        $this->curl->post($this->config->apiDomain() . $endpoint, $data);

        $response = json_decode($this->curl->getBody(), true);

        /*$this->logger->debug(print_r($response, true));*/

        return $response;
    }

    private function getItems($orderId)
    {
        $orderDetails = $this->orderFactory->create()->load($orderId);
        $store = $this->storeManager->getStore();

        $dataItems = [];

        foreach ($orderDetails->getItemsCollection() as $item) {
            $product = $this->productRepository->getById($item->getProductId());
            $dataItem = [
                'reference' => $item->getSku(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'url' => $product->getProductUrl(),
                'image' => $store->getBaseUrl($this->url::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage(),
                'unitPrice' => $item->getPrice(),
                'quantity' => (int)$item->getqty_ordered()
            ];

            $dataItems[] = $dataItem;
        }

        return $dataItems;
    }
}
