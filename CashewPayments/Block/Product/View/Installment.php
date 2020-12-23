<?php
/**
 * Magento 2 extension for Cashew Payments
 */

namespace CashewPayments\Block\Product\View;

use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;

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
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param LocaleResolver $localeResolver
     * @param array $data
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
     * @return  array|float
     */
    public function getPrice()
    {
        /** @var Product $product */
        $product = $this->registry->registry('product');

        return $product->getPrice();
    }

    public function getCurrency()
    {
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrency()->getCode();

        return $currencyCode;
    }

    public function getLang()
    {
        $localeCode = $this->localeResolver->getLocale();
        $langCode = strstr($localeCode, '_', true);

        return $langCode;
    }
}
