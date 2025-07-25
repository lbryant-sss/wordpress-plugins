<?php

// NOTE: 13.9.0 is the requirement for CusomerSession support. 12.2.0 is the requirement for PaymentMethodDomain support.
// We don't enforce this because a lot of plugins are using older versions of Stripe and we give them priority.
// This simply does not allow the feature to re-use payment methods for logged in users.

if( class_exists( 'Stripe\Stripe' ) && !empty( Stripe\Stripe::VERSION ) &&
    version_compare( Stripe\Stripe::VERSION, '7.33.0' ) >= 0 
  ){
    // using already existing class
} else {

  require __DIR__ . '/lib/Util/ApiVersion.php';

  // Stripe singleton
  require __DIR__ . '/lib/Stripe.php';

  // Utilities
  require __DIR__ . '/lib/Util/CaseInsensitiveArray.php';
  require __DIR__ . '/lib/Util/LoggerInterface.php';
  require __DIR__ . '/lib/Util/DefaultLogger.php';
  require __DIR__ . '/lib/Util/RandomGenerator.php';
  require __DIR__ . '/lib/Util/RequestOptions.php';
  require __DIR__ . '/lib/Util/Set.php';
  require __DIR__ . '/lib/Util/Util.php';
  require __DIR__ . '/lib/Util/ObjectTypes.php';

  // HttpClient
  require __DIR__ . '/lib/HttpClient/ClientInterface.php';
  require __DIR__ . '/lib/HttpClient/StreamingClientInterface.php';
  require __DIR__ . '/lib/HttpClient/CurlClient.php';

  // Exceptions
  require __DIR__ . '/lib/Exception/ExceptionInterface.php';
  require __DIR__ . '/lib/Exception/ApiErrorException.php';
  require __DIR__ . '/lib/Exception/ApiConnectionException.php';
  require __DIR__ . '/lib/Exception/AuthenticationException.php';
  require __DIR__ . '/lib/Exception/BadMethodCallException.php';
  require __DIR__ . '/lib/Exception/CardException.php';
  require __DIR__ . '/lib/Exception/IdempotencyException.php';
  require __DIR__ . '/lib/Exception/InvalidArgumentException.php';
  require __DIR__ . '/lib/Exception/InvalidRequestException.php';
  require __DIR__ . '/lib/Exception/PermissionException.php';
  require __DIR__ . '/lib/Exception/RateLimitException.php';
  require __DIR__ . '/lib/Exception/SignatureVerificationException.php';
  require __DIR__ . '/lib/Exception/UnexpectedValueException.php';
  require __DIR__ . '/lib/Exception/UnknownApiErrorException.php';

  // OAuth exceptions
  require __DIR__ . '/lib/Exception/OAuth/ExceptionInterface.php';
  require __DIR__ . '/lib/Exception/OAuth/OAuthErrorException.php';
  require __DIR__ . '/lib/Exception/OAuth/InvalidClientException.php';
  require __DIR__ . '/lib/Exception/OAuth/InvalidGrantException.php';
  require __DIR__ . '/lib/Exception/OAuth/InvalidRequestException.php';
  require __DIR__ . '/lib/Exception/OAuth/InvalidScopeException.php';
  require __DIR__ . '/lib/Exception/OAuth/UnknownOAuthErrorException.php';
  require __DIR__ . '/lib/Exception/OAuth/UnsupportedGrantTypeException.php';
  require __DIR__ . '/lib/Exception/OAuth/UnsupportedResponseTypeException.php';

  // API operations
  require __DIR__ . '/lib/ApiOperations/All.php';
  require __DIR__ . '/lib/ApiOperations/Create.php';
  require __DIR__ . '/lib/ApiOperations/Delete.php';
  require __DIR__ . '/lib/ApiOperations/NestedResource.php';
  require __DIR__ . '/lib/ApiOperations/Request.php';
  require __DIR__ . '/lib/ApiOperations/Retrieve.php';
  require __DIR__ . '/lib/ApiOperations/Search.php';
  require __DIR__ . '/lib/ApiOperations/SingletonRetrieve.php';
  require __DIR__ . '/lib/ApiOperations/Update.php';

  // Plumbing
  require __DIR__ . '/lib/ApiResponse.php';
  require __DIR__ . '/lib/RequestTelemetry.php';
  require __DIR__ . '/lib/StripeObject.php';
  require __DIR__ . '/lib/ApiRequestor.php';
  require __DIR__ . '/lib/ApiResource.php';
  require __DIR__ . '/lib/SingletonApiResource.php';
  require __DIR__ . '/lib/Service/AbstractService.php';
  require __DIR__ . '/lib/Service/AbstractServiceFactory.php';

  require __DIR__ . '/lib/Collection.php';
  require __DIR__ . '/lib/SearchResult.php';
  require __DIR__ . '/lib/ErrorObject.php';
  require __DIR__ . '/lib/Issuing/CardDetails.php';

  // StripeClient
  require __DIR__ . '/lib/BaseStripeClientInterface.php';
  require __DIR__ . '/lib/StripeClientInterface.php';
  require __DIR__ . '/lib/StripeStreamingClientInterface.php';
  require __DIR__ . '/lib/BaseStripeClient.php';
  require __DIR__ . '/lib/StripeClient.php';

  // The beginning of the section generated from our OpenAPI spec
  require __DIR__ . '/lib/Account.php';
  require __DIR__ . '/lib/AccountLink.php';
  require __DIR__ . '/lib/AccountSession.php';
  require __DIR__ . '/lib/ApplePayDomain.php';
  require __DIR__ . '/lib/Application.php';
  require __DIR__ . '/lib/ApplicationFee.php';
  require __DIR__ . '/lib/ApplicationFeeRefund.php';
  require __DIR__ . '/lib/Apps/Secret.php';
  require __DIR__ . '/lib/Balance.php';
  require __DIR__ . '/lib/BalanceTransaction.php';
  require __DIR__ . '/lib/BankAccount.php';
  require __DIR__ . '/lib/Billing/Meter.php';
  require __DIR__ . '/lib/Billing/MeterEvent.php';
  require __DIR__ . '/lib/Billing/MeterEventAdjustment.php';
  require __DIR__ . '/lib/Billing/MeterEventSummary.php';
  require __DIR__ . '/lib/BillingPortal/Configuration.php';
  require __DIR__ . '/lib/BillingPortal/Session.php';
  require __DIR__ . '/lib/Capability.php';
  require __DIR__ . '/lib/Card.php';
  require __DIR__ . '/lib/CashBalance.php';
  require __DIR__ . '/lib/Charge.php';
  require __DIR__ . '/lib/Checkout/Session.php';
  require __DIR__ . '/lib/Climate/Order.php';
  require __DIR__ . '/lib/Climate/Product.php';
  require __DIR__ . '/lib/Climate/Supplier.php';
  require __DIR__ . '/lib/ConfirmationToken.php';
  require __DIR__ . '/lib/ConnectCollectionTransfer.php';
  require __DIR__ . '/lib/CountrySpec.php';
  require __DIR__ . '/lib/Coupon.php';
  require __DIR__ . '/lib/CreditNote.php';
  require __DIR__ . '/lib/CreditNoteLineItem.php';
  require __DIR__ . '/lib/Customer.php';
  require __DIR__ . '/lib/CustomerBalanceTransaction.php';
  require __DIR__ . '/lib/CustomerCashBalanceTransaction.php';
  require __DIR__ . '/lib/CustomerSession.php';
  require __DIR__ . '/lib/Discount.php';
  require __DIR__ . '/lib/Dispute.php';
  require __DIR__ . '/lib/EphemeralKey.php';
  require __DIR__ . '/lib/Event.php';
  require __DIR__ . '/lib/ExchangeRate.php';
  require __DIR__ . '/lib/File.php';
  require __DIR__ . '/lib/FileLink.php';
  require __DIR__ . '/lib/FinancialConnections/Account.php';
  require __DIR__ . '/lib/FinancialConnections/AccountOwner.php';
  require __DIR__ . '/lib/FinancialConnections/AccountOwnership.php';
  require __DIR__ . '/lib/FinancialConnections/Session.php';
  require __DIR__ . '/lib/FinancialConnections/Transaction.php';
  require __DIR__ . '/lib/Forwarding/Request.php';
  require __DIR__ . '/lib/FundingInstructions.php';
  require __DIR__ . '/lib/Identity/VerificationReport.php';
  require __DIR__ . '/lib/Identity/VerificationSession.php';
  require __DIR__ . '/lib/Invoice.php';
  require __DIR__ . '/lib/InvoiceItem.php';
  require __DIR__ . '/lib/InvoiceLineItem.php';
  require __DIR__ . '/lib/Issuing/Authorization.php';
  require __DIR__ . '/lib/Issuing/Card.php';
  require __DIR__ . '/lib/Issuing/Cardholder.php';
  require __DIR__ . '/lib/Issuing/Dispute.php';
  require __DIR__ . '/lib/Issuing/PersonalizationDesign.php';
  require __DIR__ . '/lib/Issuing/PhysicalBundle.php';
  require __DIR__ . '/lib/Issuing/Token.php';
  require __DIR__ . '/lib/Issuing/Transaction.php';
  require __DIR__ . '/lib/LineItem.php';
  require __DIR__ . '/lib/LoginLink.php';
  require __DIR__ . '/lib/Mandate.php';
  require __DIR__ . '/lib/PaymentIntent.php';
  require __DIR__ . '/lib/PaymentLink.php';
  require __DIR__ . '/lib/PaymentMethod.php';
  require __DIR__ . '/lib/PaymentMethodConfiguration.php';
  require __DIR__ . '/lib/PaymentMethodDomain.php';
  require __DIR__ . '/lib/Payout.php';
  require __DIR__ . '/lib/Person.php';
  require __DIR__ . '/lib/Plan.php';
  require __DIR__ . '/lib/PlatformTaxFee.php';
  require __DIR__ . '/lib/Price.php';
  require __DIR__ . '/lib/Product.php';
  require __DIR__ . '/lib/PromotionCode.php';
  require __DIR__ . '/lib/Quote.php';
  require __DIR__ . '/lib/Radar/EarlyFraudWarning.php';
  require __DIR__ . '/lib/Radar/ValueList.php';
  require __DIR__ . '/lib/Radar/ValueListItem.php';
  require __DIR__ . '/lib/Refund.php';
  require __DIR__ . '/lib/Reporting/ReportRun.php';
  require __DIR__ . '/lib/Reporting/ReportType.php';
  require __DIR__ . '/lib/ReserveTransaction.php';
  require __DIR__ . '/lib/Review.php';
  require __DIR__ . '/lib/Service/AccountLinkService.php';
  require __DIR__ . '/lib/Service/AccountService.php';
  require __DIR__ . '/lib/Service/AccountSessionService.php';
  require __DIR__ . '/lib/Service/ApplePayDomainService.php';
  require __DIR__ . '/lib/Service/ApplicationFeeService.php';
  require __DIR__ . '/lib/Service/Apps/AppsServiceFactory.php';
  require __DIR__ . '/lib/Service/Apps/SecretService.php';
  require __DIR__ . '/lib/Service/BalanceService.php';
  require __DIR__ . '/lib/Service/BalanceTransactionService.php';
  require __DIR__ . '/lib/Service/Billing/BillingServiceFactory.php';
  require __DIR__ . '/lib/Service/Billing/MeterEventAdjustmentService.php';
  require __DIR__ . '/lib/Service/Billing/MeterEventService.php';
  require __DIR__ . '/lib/Service/Billing/MeterService.php';
  require __DIR__ . '/lib/Service/BillingPortal/BillingPortalServiceFactory.php';
  require __DIR__ . '/lib/Service/BillingPortal/ConfigurationService.php';
  require __DIR__ . '/lib/Service/BillingPortal/SessionService.php';
  require __DIR__ . '/lib/Service/ChargeService.php';
  require __DIR__ . '/lib/Service/Checkout/CheckoutServiceFactory.php';
  require __DIR__ . '/lib/Service/Checkout/SessionService.php';
  require __DIR__ . '/lib/Service/Climate/ClimateServiceFactory.php';
  require __DIR__ . '/lib/Service/Climate/OrderService.php';
  require __DIR__ . '/lib/Service/Climate/ProductService.php';
  require __DIR__ . '/lib/Service/Climate/SupplierService.php';
  require __DIR__ . '/lib/Service/ConfirmationTokenService.php';
  require __DIR__ . '/lib/Service/CoreServiceFactory.php';
  require __DIR__ . '/lib/Service/CountrySpecService.php';
  require __DIR__ . '/lib/Service/CouponService.php';
  require __DIR__ . '/lib/Service/CreditNoteService.php';
  require __DIR__ . '/lib/Service/CustomerService.php';
  require __DIR__ . '/lib/Service/CustomerSessionService.php';
  require __DIR__ . '/lib/Service/DisputeService.php';
  require __DIR__ . '/lib/Service/EphemeralKeyService.php';
  require __DIR__ . '/lib/Service/EventService.php';
  require __DIR__ . '/lib/Service/ExchangeRateService.php';
  require __DIR__ . '/lib/Service/FileLinkService.php';
  require __DIR__ . '/lib/Service/FileService.php';
  require __DIR__ . '/lib/Service/FinancialConnections/AccountService.php';
  require __DIR__ . '/lib/Service/FinancialConnections/FinancialConnectionsServiceFactory.php';
  require __DIR__ . '/lib/Service/FinancialConnections/SessionService.php';
  require __DIR__ . '/lib/Service/FinancialConnections/TransactionService.php';
  require __DIR__ . '/lib/Service/Forwarding/ForwardingServiceFactory.php';
  require __DIR__ . '/lib/Service/Forwarding/RequestService.php';
  require __DIR__ . '/lib/Service/Identity/IdentityServiceFactory.php';
  require __DIR__ . '/lib/Service/Identity/VerificationReportService.php';
  require __DIR__ . '/lib/Service/Identity/VerificationSessionService.php';
  require __DIR__ . '/lib/Service/InvoiceItemService.php';
  require __DIR__ . '/lib/Service/InvoiceService.php';
  require __DIR__ . '/lib/Service/Issuing/AuthorizationService.php';
  require __DIR__ . '/lib/Service/Issuing/CardService.php';
  require __DIR__ . '/lib/Service/Issuing/CardholderService.php';
  require __DIR__ . '/lib/Service/Issuing/DisputeService.php';
  require __DIR__ . '/lib/Service/Issuing/IssuingServiceFactory.php';
  require __DIR__ . '/lib/Service/Issuing/PersonalizationDesignService.php';
  require __DIR__ . '/lib/Service/Issuing/PhysicalBundleService.php';
  require __DIR__ . '/lib/Service/Issuing/TokenService.php';
  require __DIR__ . '/lib/Service/Issuing/TransactionService.php';
  require __DIR__ . '/lib/Service/MandateService.php';
  require __DIR__ . '/lib/Service/PaymentIntentService.php';
  require __DIR__ . '/lib/Service/PaymentLinkService.php';
  require __DIR__ . '/lib/Service/PaymentMethodConfigurationService.php';
  require __DIR__ . '/lib/Service/PaymentMethodDomainService.php';
  require __DIR__ . '/lib/Service/PaymentMethodService.php';
  require __DIR__ . '/lib/Service/PayoutService.php';
  require __DIR__ . '/lib/Service/PlanService.php';
  require __DIR__ . '/lib/Service/PriceService.php';
  require __DIR__ . '/lib/Service/ProductService.php';
  require __DIR__ . '/lib/Service/PromotionCodeService.php';
  require __DIR__ . '/lib/Service/QuoteService.php';
  require __DIR__ . '/lib/Service/Radar/EarlyFraudWarningService.php';
  require __DIR__ . '/lib/Service/Radar/RadarServiceFactory.php';
  require __DIR__ . '/lib/Service/Radar/ValueListItemService.php';
  require __DIR__ . '/lib/Service/Radar/ValueListService.php';
  require __DIR__ . '/lib/Service/RefundService.php';
  require __DIR__ . '/lib/Service/Reporting/ReportRunService.php';
  require __DIR__ . '/lib/Service/Reporting/ReportTypeService.php';
  require __DIR__ . '/lib/Service/Reporting/ReportingServiceFactory.php';
  require __DIR__ . '/lib/Service/ReviewService.php';
  require __DIR__ . '/lib/Service/SetupAttemptService.php';
  require __DIR__ . '/lib/Service/SetupIntentService.php';
  require __DIR__ . '/lib/Service/ShippingRateService.php';
  require __DIR__ . '/lib/Service/Sigma/ScheduledQueryRunService.php';
  require __DIR__ . '/lib/Service/Sigma/SigmaServiceFactory.php';
  require __DIR__ . '/lib/Service/SourceService.php';
  require __DIR__ . '/lib/Service/SubscriptionItemService.php';
  require __DIR__ . '/lib/Service/SubscriptionScheduleService.php';
  require __DIR__ . '/lib/Service/SubscriptionService.php';
  require __DIR__ . '/lib/Service/Tax/CalculationService.php';
  require __DIR__ . '/lib/Service/Tax/RegistrationService.php';
  require __DIR__ . '/lib/Service/Tax/SettingsService.php';
  require __DIR__ . '/lib/Service/Tax/TaxServiceFactory.php';
  require __DIR__ . '/lib/Service/Tax/TransactionService.php';
  require __DIR__ . '/lib/Service/TaxCodeService.php';
  require __DIR__ . '/lib/Service/TaxIdService.php';
  require __DIR__ . '/lib/Service/TaxRateService.php';
  require __DIR__ . '/lib/Service/Terminal/ConfigurationService.php';
  require __DIR__ . '/lib/Service/Terminal/ConnectionTokenService.php';
  require __DIR__ . '/lib/Service/Terminal/LocationService.php';
  require __DIR__ . '/lib/Service/Terminal/ReaderService.php';
  require __DIR__ . '/lib/Service/Terminal/TerminalServiceFactory.php';
  require __DIR__ . '/lib/Service/TestHelpers/ConfirmationTokenService.php';
  require __DIR__ . '/lib/Service/TestHelpers/CustomerService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Issuing/AuthorizationService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Issuing/CardService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Issuing/IssuingServiceFactory.php';
  require __DIR__ . '/lib/Service/TestHelpers/Issuing/PersonalizationDesignService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Issuing/TransactionService.php';
  require __DIR__ . '/lib/Service/TestHelpers/RefundService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Terminal/ReaderService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Terminal/TerminalServiceFactory.php';
  require __DIR__ . '/lib/Service/TestHelpers/TestClockService.php';
  require __DIR__ . '/lib/Service/TestHelpers/TestHelpersServiceFactory.php';
  require __DIR__ . '/lib/Service/TestHelpers/Treasury/InboundTransferService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Treasury/OutboundPaymentService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Treasury/OutboundTransferService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Treasury/ReceivedCreditService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Treasury/ReceivedDebitService.php';
  require __DIR__ . '/lib/Service/TestHelpers/Treasury/TreasuryServiceFactory.php';
  require __DIR__ . '/lib/Service/TokenService.php';
  require __DIR__ . '/lib/Service/TopupService.php';
  require __DIR__ . '/lib/Service/TransferService.php';
  require __DIR__ . '/lib/Service/Treasury/CreditReversalService.php';
  require __DIR__ . '/lib/Service/Treasury/DebitReversalService.php';
  require __DIR__ . '/lib/Service/Treasury/FinancialAccountService.php';
  require __DIR__ . '/lib/Service/Treasury/InboundTransferService.php';
  require __DIR__ . '/lib/Service/Treasury/OutboundPaymentService.php';
  require __DIR__ . '/lib/Service/Treasury/OutboundTransferService.php';
  require __DIR__ . '/lib/Service/Treasury/ReceivedCreditService.php';
  require __DIR__ . '/lib/Service/Treasury/ReceivedDebitService.php';
  require __DIR__ . '/lib/Service/Treasury/TransactionEntryService.php';
  require __DIR__ . '/lib/Service/Treasury/TransactionService.php';
  require __DIR__ . '/lib/Service/Treasury/TreasuryServiceFactory.php';
  require __DIR__ . '/lib/Service/WebhookEndpointService.php';
  require __DIR__ . '/lib/SetupAttempt.php';
  require __DIR__ . '/lib/SetupIntent.php';
  require __DIR__ . '/lib/ShippingRate.php';
  require __DIR__ . '/lib/Sigma/ScheduledQueryRun.php';
  require __DIR__ . '/lib/Source.php';
  require __DIR__ . '/lib/SourceMandateNotification.php';
  require __DIR__ . '/lib/SourceTransaction.php';
  require __DIR__ . '/lib/Subscription.php';
  require __DIR__ . '/lib/SubscriptionItem.php';
  require __DIR__ . '/lib/SubscriptionSchedule.php';
  require __DIR__ . '/lib/Tax/Calculation.php';
  require __DIR__ . '/lib/Tax/CalculationLineItem.php';
  require __DIR__ . '/lib/Tax/Registration.php';
  require __DIR__ . '/lib/Tax/Settings.php';
  require __DIR__ . '/lib/Tax/Transaction.php';
  require __DIR__ . '/lib/Tax/TransactionLineItem.php';
  require __DIR__ . '/lib/TaxCode.php';
  require __DIR__ . '/lib/TaxDeductedAtSource.php';
  require __DIR__ . '/lib/TaxId.php';
  require __DIR__ . '/lib/TaxRate.php';
  require __DIR__ . '/lib/Terminal/Configuration.php';
  require __DIR__ . '/lib/Terminal/ConnectionToken.php';
  require __DIR__ . '/lib/Terminal/Location.php';
  require __DIR__ . '/lib/Terminal/Reader.php';
  require __DIR__ . '/lib/TestHelpers/TestClock.php';
  require __DIR__ . '/lib/Token.php';
  require __DIR__ . '/lib/Topup.php';
  require __DIR__ . '/lib/Transfer.php';
  require __DIR__ . '/lib/TransferReversal.php';
  require __DIR__ . '/lib/Treasury/CreditReversal.php';
  require __DIR__ . '/lib/Treasury/DebitReversal.php';
  require __DIR__ . '/lib/Treasury/FinancialAccount.php';
  require __DIR__ . '/lib/Treasury/FinancialAccountFeatures.php';
  require __DIR__ . '/lib/Treasury/InboundTransfer.php';
  require __DIR__ . '/lib/Treasury/OutboundPayment.php';
  require __DIR__ . '/lib/Treasury/OutboundTransfer.php';
  require __DIR__ . '/lib/Treasury/ReceivedCredit.php';
  require __DIR__ . '/lib/Treasury/ReceivedDebit.php';
  require __DIR__ . '/lib/Treasury/Transaction.php';
  require __DIR__ . '/lib/Treasury/TransactionEntry.php';
  require __DIR__ . '/lib/UsageRecord.php';
  require __DIR__ . '/lib/UsageRecordSummary.php';
  require __DIR__ . '/lib/WebhookEndpoint.php';

  // The end of the section generated from our OpenAPI spec

  // OAuth
  require __DIR__ . '/lib/OAuth.php';
  require __DIR__ . '/lib/OAuthErrorObject.php';
  require __DIR__ . '/lib/Service/OAuthService.php';

  // Webhooks
  require __DIR__ . '/lib/Webhook.php';
  require __DIR__ . '/lib/WebhookSignature.php';

}
