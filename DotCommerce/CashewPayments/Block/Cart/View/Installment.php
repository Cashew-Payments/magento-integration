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

namespace DotCommerce\CashewPayments\Block;

use DotCommerce\CashewPayments\Helper\Config;
use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class to support fetch data from product
 * 
 * PHP version 7
 * 
 * @category Checkout
 * @package  CashewPayments
 * @author   DotCommerce <mi@discretecommerce.com>
 * @license  https://www.cashewpayments.com/license.txt cashew License
 * @link     https://www.cashewpayments.com
 */
class CheckoutCashew extends Template
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Registry
     */
    protected $registry = null;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StoreManagerInterface
     */
    protected $localeResolver;

    /**
     * Get product price and system laguange and currency code
     * 
     * @param Context               $context        context for installment
     * @param Registry              $registry       registry for installment
     * @param StoreManagerInterface $storeManager   store manager reference
     * @param LocaleResolver        $localeResolver local resolver
     * @param Config                $config         config helper
     * @param array                 $data           data related to products
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        LocaleResolver $localeResolver,
        Config $config,
        array $data = []
    ) {
        $this->config = $config;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->localeResolver = $localeResolver;

        parent::__construct($context, $data);
    }

    /**
     * Get module status
     *
     * @return bool
     */
    public function isEnabled()
    {
        $isEnabled = $this->config->getIsEnabled();

        return $isEnabled;
    }

    /**
     * Get environment
     *
     * @return bool
     */
    public function getEnvironment()
    {
        return $this->config->getEnvironment() === 'sandbox' ? 
        'https://s3-eu-west-1.amazonaws.com/cdn-sandbox.cashewpayments.com/' : 
        'https://cdn.cashewpayments.com/';
    }

    /**
     * Get system currency
     * 
     * @return $currencyCode currency code of the system
     */
    public function getCurrency()
    {
        $currencyCode = $this->storeManager
            ->getStore()
            ->getCurrentCurrency()
            ->getCode();

        return $currencyCode;
    }

    /**
     * Get system language
     * 
     * @return $langCode language code of the system
     */
    public function getLang()
    {
        $localeCode = $this->localeResolver->getLocale();
        $langCode = strstr($localeCode, '_', true);

        return $langCode;
    }
}
