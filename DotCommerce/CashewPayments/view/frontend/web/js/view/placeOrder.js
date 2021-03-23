define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url',
        'Magento_Ui/js/modal/modal'
    ],
    function (
        $,
        Component,
        placeOrderAction,
        selectPaymentMethodAction,
        customer,
        checkoutData,
        additionalValidators,
        url,
        modal
    ) {
        'use strict';

        return Component.extend(
            {
                redirectAfterPlaceOrder: false,
                placeOrder: function (data, event) {
                    if (event) {
                        event.preventDefault();
                    }
                    var self = this;
                    var placeOrder;
                    var emailValidationResult = customer.isLoggedIn();
                    var loginFormSelector = 'form[data-role=email-with-possible-login]';

                    if (this.item.method == 'cashewpayment' && cashew.checkout.response.orderId) {
                        cashew.checkout.load();
                        return false;
                    }
                    
                    if (!customer.isLoggedIn()) {
                        $(loginFormSelector).validation();
                        emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                    }
                    if (emailValidationResult && this.validate() && additionalValidators.validate()) {
                        this.isPlaceOrderActionAllowed(false);
                        placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

                        $.when(placeOrder).fail(
                            function () {
                                self.isPlaceOrderActionAllowed(true);
                            }
                        ).done(this.afterPlaceOrder.bind(data));
                        return true;
                    }
                    return false;
                },
                selectPaymentMethod: function () {
                    selectPaymentMethodAction(this.getData());
                    checkoutData.setSelectedPaymentMethod(this.item.method);
                    return true;
                },
                afterPlaceOrder: function (data) {
                    if(this.item.method == 'cashewpayment') {
                        $.ajax(
                            {
                                type: "POST",
                                url: window.BASE_URL + 'cashewpayments/checkout',
                                data: { orderId: data },
                                success: function (data) {
                                    cashew.checkout.response = {
                                        token: data.data.token,
                                        orderId: data.data.orderId,
                                        storeToken: data.data.storeToken,
                                        successUrl: window.BASE_URL + 'checkout/onepage/success',
                                        failureUrl: window.BASE_URL + 'checkout/onepage/failure'
                                    };
                                    cashew.checkout.load();
                                },
                                failure: function (errMsg) {
                                    console.log(errMsg);
                                }
                            }
                        );

                        document.getElementById('cashewpayment')
                        .parentElement
                        .parentElement.querySelector('.disabled').classList.remove('disabled');
                    } else {
                        window.location = BASE_URL + 'checkout/onepage/success';
                    }
                }
            }
        );
    }
);
