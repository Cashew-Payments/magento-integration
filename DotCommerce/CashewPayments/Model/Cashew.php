<?php
/**
 * Magento 2 extension for Cashew Payments
 */

namespace DotCommerce\CashewPayments\Model;

use DotCommerce\CashewPayments\Helper\Config as ConfigHelper;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote as MagentoQuote;

class Cashew extends AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'cashewpayment';

    /**
     * @var ConfigHelper
     */
    protected $config;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        ConfigHelper $config,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );

        $this->config = $config;
    }

    /**
     * @param  CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(CartInterface $quote = null): bool
    {
        if ($quote && ($quote instanceof MagentoQuote)) {
            $orderSubtotal = $quote->getSubtotal();
            $currency = $quote->getCurrencyCode();
            $minOrderTotal = !empty($this->config->getMinimumOrderTotal()) ? $this->config->getMinimumOrderTotal() : 0;
            $maxOrderTotal = !empty($this->config->getMaximumOrderTotal()) ? $this->config->getMaximumOrderTotal() : PHP_INT_MAX;
            print_r($currency);
            if ($currency === 'AED' || $currency === 'SAR') {
                if ($orderSubtotal >= $minOrderTotal && $orderSubtotal <= $maxOrderTotal) {
                    return true;
                }
            }
        }

        return false;
    }
}
