<?php
/**
 * Magento 2 extension for Cashew Payments
 * 
 * @author DotCommerce <mi@discretecommerce.com>
 */

namespace DotCommerce\CashewPayments\Block\Product\View;

use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class to support fetch data from product
 * 
 * @author DotCommerce <mi@discretecommerce.com>
 */
class Installment extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $registry = null;
    protected $storeManager;
    protected $localeResolver;

    /**
     * Get product price and system laguange and currency code
     * 
     * @param Context               $context        context for installment
     * @param Registry              $registry       registry for installment
     * @param StoreManagerInterface $storeManager   store manager reference
     * @param LocaleResolver        $localeResolver local resolver
     * @param array                 $data           data related to products
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        LocaleResolver $localeResolver,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->localeResolver = $localeResolver;

        parent::__construct($context, $data);
    }

    /**
     * Get product price
     * 
     * @return array|float
     */
    public function getPrice()
    {
        $product = $this->registry->registry('product');

        return $product->getPrice();
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
