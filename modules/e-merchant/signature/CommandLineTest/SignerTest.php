<?php
error_reporting(0);

//merchant account parameters assigned by gateway
//change them according to your own account details
$md5_key = "XYZ12345";
$client_id = "517243";
$form_id = "1061";

$currency = 'USD';
$paymentformurl = '';
$amount = '5.00';
$reference = '125444';

//Server URL - <Payment Form URL> to be replaced with the associated one for the account
$server_url = $paymentformurl."/payment/form/post";

include_once('../ParamSigner.class.php');
$ps=new Paramsigner();

//prepare trans request string
//required fields
$ps->setSecret($md5_key);
$ps->setParam('client_id',$client_id);
$ps->setParam('form_id',$form_id);
$ps->setParam('order_currency',$currency);
$ps->setParam('order_reference',$reference);
$ps->setParam('test_transaction',"1"); //For the LIVE environment set to 0 or remove

//dynamic item
$ps->setParam('item_1_code',"EMPTEST01");
$ps->setParam('item_1_qty',"1");
$ps->setParam('item_1_predefined',"0");
$ps->setParam('item_1_name',"DEMO ITEM");
$ps->setParam('item_1_description',"DEMO ITEM DESCRIPTION");
$ps->setParam('item_1_digital',"1");
$ps->setParam('item_1_unit_price_'.$currency,$amount);

//customer details
$ps->setParam('customer_first_name',"Bob");
$ps->setParam('customer_last_name',"Jones");
$ps->setParam('customer_address',"123 Franklin Street");
$ps->setParam('customer_city',"Philadelphia");
$ps->setParam('customer_state',"PA");
$ps->setParam('customer_postcode',"91304");
$ps->setParam('customer_country',"US");
$ps->setParam('customer_phone',"8522478800");
$ps->setParam('customer_email',"bjones@test.com");

//generate Query String
$requestString=$ps->getQueryString();

// To test other parser result, modify here.
// $requestString = 'PS_SIGNATURE=a32f106934785151d0f5cbe74e3d330a1f4e9d6c&PS_EXPIRETIME=1371093728.78&PS_SIGTYPE=PSSHA1&client_id=517243&customer_address=123%20Franklin%20Street&customer_city=Philadelphia&customer_country=US&customer_email=bjones%40test.com&customer_first_name=Bob&customer_last_name=Jones&customer_phone=8522478800&customer_postcode=91304&customer_state=PA&form_id=1061&item_1_code=EMPTEST01&item_1_description=DEMO%20ITEM%20DESCRIPTION&item_1_digital=1&item_1_name=DEMO%20ITEM&item_1_predefined=0&item_1_qty=1&item_1_unit_price_USD=5.00&order_currency=USD&order_reference=125444&test_transaction=1';

parse_str($requestString,$resultArray);

$res = $ps->paramAuthenticate($resultArray,$md5_key);
if($res){
    echo "\nAuthenticate Success!\n";
}else{
    echo "\nAuthenticate failure!\n";
}

