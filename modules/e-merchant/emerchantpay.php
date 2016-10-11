<?php
/**
 * WHMCS Sample Payment Gateway Module
 *
 * Payment Gateway modules allow you to integrate payment solutions with the
 * WHMCS platform.
 *
 * This sample file demonstrates how a payment gateway module for WHMCS should
 * be structured and all supported functionality it can contain.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "gatewaymodule" and therefore all functions
 * begin "emerchantpay_".
 *
 * If your module or third party API does not support a given function, you
 * should not define that function within your module. Only the _config
 * function is required.
 *
 * For more information, please refer to the online documentation.
 *
 * @see http://docs.whmcs.com/Gateway_Module_Developer_Docs
 *
 * @copyright Copyright (c) WHMCS Limited 2015
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see http://docs.whmcs.com/Gateway_Module_Meta_Data_Parameters
 *
 * @return array
 */
function emerchantpay_MetaData()
{
    return array(
        'DisplayName' => 'eMPPay Limited.',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function emerchantpay_config()
{
    return array(
        //The friendly display name for a payment gateway should be defined here for backwards compatibility
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'eMPPay Limited.',
        ),
        'md5Key' => array(
            'FriendlyName' => 'Secret Key',
            'Type' => 'text',
            'Default' => '',
            'Description' => 'Get your secret key from your EMPPay account: Account -> My account -> Payment Forms: Secret Key',
        ),
        'formID' => array(
            'FriendlyName' => 'Form ID',
            'Type' => 'text',
            'Default' => '',
            'Description' => 'Get your form ID from: Account -> My account -> Payment Forms: Defined Payment Forms section',
        ),
        'clientID' => array(
            'FriendlyName' => 'Client ID',
            'Type' => 'text',
            'Default' => '',
            'Description' => 'Get your client ID from: Account -> My account -> Details: Account ID',
        ),
        'serverUrl' => array(
            'FriendlyName' => 'Server URL',
            'Type' => 'text',
            'Default' => '',
            'Description' => 'Get your assigned payment form url: Account -> My account: Payment Forms -> Assigned From URL)',
        ),
        'paymentFee' => array(
            'FriendlyName' => 'Payment fees',
            'Type' => 'text',
            'Default' => '',
            'Description' => 'The commission you are charged for each transaction via EMP',
        ),
        'whiteListMode' => array(
            'FriendlyName' => 'White List Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to enable white list mode',
        ),
        'whiteListUsers' => array(
            'FriendlyName' => 'Comma separated user id list',
            'Type' => 'text',
            'Default' => '',
            'Description' => 'List of users permitted to use this method',
        ),
        'whiteListGroups' => array(
            'FriendlyName' => 'Comma separated group id list',
            'Type' => 'text',
            'Default' => '',
            'Description' => 'List of groups permitted to use this method',
        ),
        'testMode' => array(
            'FriendlyName' => 'Test Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to enable test mode',
        ),
    );
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see http://docs.whmcs.com/Payment_Gateway_Module_Parameters
 *
 * @return string
 */
function emerchantpay_link($params)
{
    // Gateway Configuration Parameters
    $md5_key = $params['md5Key'];
    $client_id = $params['clientID'];
    $form_id = $params['formID'];
    $testMode  = $params['testMode'];
    $whiteListMode = $params['whiteListMode'];
    $whiteListUserArray = explode(',',$params['whiteListUsers']);
    $whiteListGroupArray = explode(',',$params['whiteListGroups']);
    $paymentformurl = $params['serverUrl'];
    $server_url = $paymentformurl."/payment/form/post";
    $currency = $params['currency'];
    $amount = $params['amount'];
    $reference = $params['invoiceid'];

    include_once('emerchantpay/ParamSigner.class.php');
    $ps = new Paramsigner();

    //Required fields
    $ps->setSecret($md5_key);
    $ps->setParam('client_id',$client_id);
    $ps->setParam('form_id',$form_id);
    $ps->setParam('order_currency',$currency);
    $ps->setParam('order_reference',$reference);
    $ps->setParam('test_transaction', ($testMode) ? 1 : 0 ); //For the LIVE environment set to 0 or remove

    //Dynamic item
    $ps->setParam('item_1_code',$reference);
    $ps->setParam('item_1_qty',"1");
    $ps->setParam('item_1_predefined',"0");
    $ps->setParam('item_1_name',"WEBHOSTING SERVICES");
    $ps->setParam('item_1_description',$params["description"]);
    $ps->setParam('item_1_digital',"1");
    $ps->setParam('item_1_unit_price_'.$currency,$amount);

    //Customer details
    $ps->setParam('customer_first_name',$params['clientdetails']['firstname']);
    $ps->setParam('customer_last_name',$params['clientdetails']['lastname']);
    $ps->setParam('customer_address',$params['clientdetails']['address1']." ".$params['clientdetails']['address2']);
    $ps->setParam('customer_city',$params['clientdetails']['city']);
    $ps->setParam('customer_state',$params['clientdetails']['state']);
    $ps->setParam('customer_postcode',$params['clientdetails']['postcode']);
    $ps->setParam('customer_country',$params['clientdetails']['country']);
    $ps->setParam('customer_phone',$params['clientdetails']['phonenumber']);
    $ps->setParam('customer_email',$params['clientdetails']['email']);

    //generate Query String
    $requestString=$ps->getQueryString();

    $group_id = $params['clientdetails']['groupid'];
    $user_id = $params['clientdetails']['id'];

    //Get the users group
    if (!$whiteListMode or (in_array($group_id, $whiteListGroupArray) or in_array($user_id, $whiteListUserArray))) {
        $htmlOutput = '<form method="post" action="' . $server_url."?".$requestString . '">';
        $htmlOutput .= '<input type="submit" value="' . $params['langpaynow'] . '" />';
        $htmlOutput .= '</form>';
    } else {
        $htmlOutput = '<form method="post" action="' . $server_url."?".$requestString . '">';
        $htmlOutput .= '<input type="submit" value="You must be white listed to use this method." disabled/>';
        $htmlOutput .= '</form>';
    }

    return $htmlOutput;

}
