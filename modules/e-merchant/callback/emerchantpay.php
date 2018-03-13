<?php
/**
 * WHMCS Sample Payment Callback File
 *
 * This sample file demonstrates how a payment gateway callback should be
 * handled within WHMCS.
 *
 * It demonstrates verifying that the payment gateway module is active,
 * validating an Invoice ID, checking for the existence of a Transaction ID,
 * Logging the Transaction for debugging and Adding Payment to an Invoice.
 *
 * For more information, please refer to the online documentation.
 *
 * @see http://docs.whmcs.com/Gateway_Module_Developer_Docs
 *
 * @copyright Copyright (c) WHMCS Limited 2015
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';
include_once('../emerchantpay/ParamSigner.class.php');

// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');
$transactionStatus = [];

/**
 * Set variables
 */
$success = true;

/**
 * Get the WHMCS gateway parameters
 */
$gatewayParams = getGatewayVariables($gatewayModuleName);

/**
 * Check that we have a EMP secret
 */
if (!isset($gatewayParams['md5Key']))
{
    $transactionStatus[] = 'Missing secret.';
    $success = false;
}

$secretKey = $gatewayParams['md5Key'];

/**
 * Authenticate the signature
 */

$authenticatedParam = ParamSigner::paramAuthenticate($_POST,$secretKey);
if(!$authenticatedParam)
{
    $transactionStatus[] = 'Signature failed or promise expired.';
    $success = false;
}

/**
 * Check we have fees charged by EMP
 */
$payment_fee_flag = true;
if ( !isset($gatewayParams['paymentFee']) OR !is_numeric($gatewayParams['paymentFee']) )
{
    $payment_fee_flag = false;
    $transactionStatus[] = 'Missing gateway fee.';
}
$paymentFee = $gatewayParams['paymentFee'];

/**
 * Make sure the module is activated
 */
if (!$gatewayParams['type']) {
    $transactionStatus[] = 'Inactive module.';
    $success = false;
}

/**
 * Check we have a notification type
 */
if (!isset($authenticatedParam['notification_type']))
{
    $transactionStatus[] = 'Missing notification variable';
    $success = false;
}

/**
 * Check the order status
 * At this moment, anything other than ORDER is not handled
 */

if ($authenticatedParam['notification_type'] != "order")
{
    $transactionStatus[] = 'Notification type returned as: '.$authenticatedParam['notification_type'];
    $success = false;
}

/**
 * Check order reference
 */
if (!isset($_POST['order_reference']))
{
    $transactionStatus[] = 'Missing order reference';
    $success = false;
}

/**
 * Set the variables
 */
$invoiceId = $_POST['order_reference'];
$transactionId = $_POST['trans_id'];
$paymentAmount = $_POST['amount'];

if ($payment_fee_flag)
{
    $paymentFee = $paymentAmount*$paymentFee/100;
} else {
    $paymentFee = $paymentAmount*6.5/100;
}

$status = $success ? 'Success' : 'Failure';

/**
 * Validate Callback Invoice ID.
 *
 * Checks invoice ID is a valid invoice number. Note it will count an
 * invoice in any status as valid.
 *
 * Performs a die upon encountering an invalid Invoice ID.
 *
 * Returns a normalised invoice ID.
 */
$invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['name']);

/**
 * Check Callback Transaction ID.
 *
 * Performs a check for any existing transactions with the same given
 * transaction number.
 *
 * Performs a die upon encountering a duplicate.
 */
checkCbTransID($transactionId);

/**
 * Log Transaction.
 *
 * Add an entry to the Gateway Log for debugging purposes.
 *
 * The debug data can be a string or an array. In the case of an
 * array it will be
 *
 * @param string $gatewayName        Display label
 * @param string|array $debugData    Data to log
 * @param string $transactionStatus  Status
 */

logTransaction($gatewayParams['name'], $_POST, $status);
logTransaction($gatewayParams['name'], "InvoiceID: ".$invoiceId. " TransactionID: ".$transactionId." Payment Amount: ".$paymentAmount." Success variable: ".$success." Imploded transaction data: ".implode(", ", $transactionStatus), $status);

if ($success) {



    /**
     * Add Invoice Payment.
     *
     * Applies a payment transaction entry to the given invoice ID.
     *
     * @param int $invoiceId         Invoice ID
     * @param string $transactionId  Transaction ID
     * @param float $paymentAmount   Amount paid (defaults to full balance)
     * @param float $paymentFee      Payment fee (optional)
     * @param string $gatewayModule  Gateway module name
     */
    addInvoicePayment(
        $invoiceId,
        $transactionId,
        $paymentAmount,
        $paymentFee,
        $gatewayModuleName
    );
}

echo "OK";