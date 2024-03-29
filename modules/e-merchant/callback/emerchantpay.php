<?php
// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';
include_once('../emerchantpay/ParamSigner.class.php');

// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');

// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);


/**
 * INACTIVE MODULE
 */
if (!$gatewayParams['type']) {
    logTransaction("Module is not activated. POST DATA: ".$_POST);
    die("Module Not Activated");
}

/**
 * GET PAYMENT DATA CALLBACK
 */
$success = $_POST["response"]; //A = Authenticated, D = Declined
$invoiceId = $_POST["order_reference"];
$transactionId = $_POST["trans_id"];
$paymentAmount = $_POST["amount"];
$hash = $_POST["PS_SIGNATURE"];

/**
 * GET SUCCESS MESSAGE
 */
$transactionStatus = ($success == 'A') ? 'Success' : 'Failure';
$status = ($success == 'A') ? true : false;

/**
 * WORK OUT THE PAYMENT FEES
 */
if (!isset($gatewayParams['paymentFee']))
{
    logTransaction("Module payment fee in gateway params: ".$gatewayParams);
    $paymentFee = $paymentAmount*7/100;
} else {
    $paymentFee = $paymentAmount*$gatewayParams['paymentFee']/100;
}

/**
 * CHECK SIGNATURE
 */
if (!isset($gatewayParams['md5Key']))
{
    logTransaction("Module is missing MD5 Key: ".$_POST);
    die("Missing md5Key");
}

$secretKey = $gatewayParams['md5Key'];

/**
 * AUTHENTICATE KEYS
 */
$response = ParamSigner::paramAuthenticate($_POST,$secretKey);

if(!$response['response'])
{
    logTransaction("ERROR: ".$gatewayParams['name'], $response['message']);
    logTransaction("ERROR DATA: ".$gatewayParams['name'], $_POST);

    die("Error authenticating the key");
}

/**
 * CHECK INVOICE ID (This will perform a die if the invoice id is invalid)
 */
$invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['name']);

/**
 * Checks for existing transactions with the same given transaction number
 */
checkCbTransID($transactionId);

/**
 * Log the transaction
 */
logTransaction($gatewayParams['name'], $_POST, $transactionStatus);
logTransaction($gatewayParams['name'], "InvoiceID: ".$invoiceId. " TransactionID: ".$transactionId." Payment Amount: ".$paymentAmount." Success variable: ".$success);

if ($status) {
    addInvoicePayment(
        $invoiceId,
        $transactionId,
        $paymentAmount,
        $paymentFee,
        $gatewayModuleName
    );
}

echo "OK";
