<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'Composer\\InstalledVersions' => $vendorDir . '/composer/InstalledVersions.php',
    'Paymennt\\Exception' => $baseDir . '/src/Exception.php',
    'Paymennt\\PaymenntClient' => $baseDir . '/src/PaymenntClient.php',
    'Paymennt\\Validatable' => $baseDir . '/src/Validatable.php',
    'Paymennt\\branches\\AbstractBranchRequest' => $baseDir . '/src/branches/AbstractBranchRequest.php',
    'Paymennt\\branches\\BranchLookupRequest' => $baseDir . '/src/branches/BranchLookupRequest.php',
    'Paymennt\\branches\\CreateBranchRequest' => $baseDir . '/src/branches/CreateBranchRequest.php',
    'Paymennt\\branches\\DisableBranchRequest' => $baseDir . '/src/branches/DisableBranchRequest.php',
    'Paymennt\\branches\\EnableBranchRequest' => $baseDir . '/src/branches/EnableBranchRequest.php',
    'Paymennt\\branches\\GetBranchRequest' => $baseDir . '/src/branches/GetBranchRequest.php',
    'Paymennt\\checkout\\AbstractCheckoutRequest' => $baseDir . '/src/checkout/AbstractCheckoutRequest.php',
    'Paymennt\\checkout\\CancelCheckoutRequest' => $baseDir . '/src/checkout/CancelCheckoutRequest.php',
    'Paymennt\\checkout\\CheckoutLookupRequest' => $baseDir . '/src/checkout/CheckoutLookupRequest.php',
    'Paymennt\\checkout\\GetCheckoutRequest' => $baseDir . '/src/checkout/GetCheckoutRequest.php',
    'Paymennt\\checkout\\LinkCheckoutRequest' => $baseDir . '/src/checkout/LinkCheckoutRequest.php',
    'Paymennt\\checkout\\MobileCheckoutRequest' => $baseDir . '/src/checkout/MobileCheckoutRequest.php',
    'Paymennt\\checkout\\QRCheckoutRequest' => $baseDir . '/src/checkout/QRCheckoutRequest.php',
    'Paymennt\\checkout\\RefundCheckoutRequest' => $baseDir . '/src/checkout/RefundCheckoutRequest.php',
    'Paymennt\\checkout\\WebCheckoutRequest' => $baseDir . '/src/checkout/WebCheckoutRequest.php',
    'Paymennt\\model\\Address' => $baseDir . '/src/model/Address.php',
    'Paymennt\\model\\Branch' => $baseDir . '/src/model/Branch.php',
    'Paymennt\\model\\BranchPage' => $baseDir . '/src/model/BranchPage.php',
    'Paymennt\\model\\Checkout' => $baseDir . '/src/model/Checkout.php',
    'Paymennt\\model\\CheckoutPage' => $baseDir . '/src/model/CheckoutPage.php',
    'Paymennt\\model\\Customer' => $baseDir . '/src/model/Customer.php',
    'Paymennt\\model\\Item' => $baseDir . '/src/model/Item.php',
    'Paymennt\\model\\Payment' => $baseDir . '/src/model/Payment.php',
    'Paymennt\\model\\Subscription' => $baseDir . '/src/model/Subscription.php',
    'Paymennt\\model\\SubscriptionPage' => $baseDir . '/src/model/SubscriptionPage.php',
    'Paymennt\\model\\SubscriptionPayments' => $baseDir . '/src/model/SubscriptionPayments.php',
    'Paymennt\\model\\TokenPaymentSource' => $baseDir . '/src/model/TokenPaymentSource.php',
    'Paymennt\\model\\Totals' => $baseDir . '/src/model/Totals.php',
    'Paymennt\\model\\Webhook' => $baseDir . '/src/model/Webhook.php',
    'Paymennt\\model\\WebhookPage' => $baseDir . '/src/model/WebhookPage.php',
    'Paymennt\\payment\\CaptureAuthPaymentRequest' => $baseDir . '/src/payment/CaptureAuthPaymentRequest.php',
    'Paymennt\\payment\\CreatePaymentRequest' => $baseDir . '/src/payment/CreatePaymentRequest.php',
    'Paymennt\\payment\\GetPaymentRequest' => $baseDir . '/src/payment/GetPaymentRequest.php',
    'Paymennt\\subscription\\AbstractSubscriptionRequest' => $baseDir . '/src/subscription/AbstractSubscriptionRequest.php',
    'Paymennt\\subscription\\CreateSubscriptionRequest' => $baseDir . '/src/subscription/CreateSubscriptionRequest.php',
    'Paymennt\\subscription\\GetSubscriptionPaymentsRequest' => $baseDir . '/src/subscription/GetSubscriptionPaymentsRequest.php',
    'Paymennt\\subscription\\GetSubscriptionRequest' => $baseDir . '/src/subscription/GetSubscriptionRequest.php',
    'Paymennt\\subscription\\PauseSubscriptionRequest' => $baseDir . '/src/subscription/PauseSubscriptionRequest.php',
    'Paymennt\\subscription\\ResumeSubscriptionRequest' => $baseDir . '/src/subscription/ResumeSubscriptionRequest.php',
    'Paymennt\\subscription\\SubscriptionLookupRequest' => $baseDir . '/src/subscription/SubscriptionLookupRequest.php',
    'Paymennt\\webhooks\\AbstractWebhookRequest' => $baseDir . '/src/webhooks/AbstractWebhookRequest.php',
    'Paymennt\\webhooks\\CreateWebhookRequest' => $baseDir . '/src/webhooks/CreateWebhookRequest.php',
    'Paymennt\\webhooks\\DeleteWebhookRequest' => $baseDir . '/src/webhooks/DeleteWebhookRequest.php',
    'Paymennt\\webhooks\\DisableWebhookRequest' => $baseDir . '/src/webhooks/DisableWebhookRequest.php',
    'Paymennt\\webhooks\\EnableWebhookRequest' => $baseDir . '/src/webhooks/EnableWebhookRequest.php',
    'Paymennt\\webhooks\\GetWebhookRequest' => $baseDir . '/src/webhooks/GetWebhookRequest.php',
    'Paymennt\\webhooks\\TestWebhookRequest' => $baseDir . '/src/webhooks/TestWebhookRequest.php',
    'Paymennt\\webhooks\\WebhookLookupRequest' => $baseDir . '/src/webhooks/WebhookLookupRequest.php',
);
