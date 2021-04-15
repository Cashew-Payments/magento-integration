# cashew extension for Magento
Version 1.0.14

# Magento
This extension allows you to use cashew as a payment gateway in your Magento store.

# 1. Installation steps
Place the file under 'app/code' folder
unzip the file
You will find a folder called 'DotCommerce'
go to the root of your magento folder
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
# 2a. Admin Configuration
Login to your Magento Admin portal
Navigate to Stores > Configuration > Sales > Payment Methods
Look for “Cashew Payments” in the list
# 2b. Payment Setup
Enable extension
Place API Key shared with you
Save changes
# 2c. Create integration point
Navigate to System > Integrations
Add new integration
Give a name
Callback URL with value https://api-sandbox.cashewpayments.com/v1/stores/magento
Identity link URL with value https://api-sandbox.cashewpayments.com/v1/stores/magento
Go to API and enable the following resources:
"View" and "Credit Memos" under Sales > Operations > Orders > Actions
"Credit Memos" under Sales > Operations
Save changes
Proceed with the activation of the saved integration