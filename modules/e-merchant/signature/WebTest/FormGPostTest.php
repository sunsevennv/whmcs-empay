<?php
error_reporting(0);
//merchant account parameters assigned by gateway
//change them according to your own account details
$md5_key = "DsqiGuuQDM";
$client_id = "815103";
$form_id = "2743";

//Posted parameters from the index.php
$currency = $_POST["currency"];
$paymentformurl = $_POST["paymentformurl"];
$amount = $_POST["amount"];
$reference = $_POST["reference"];

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
?>

<html>
<head>
<title>eCommerce Secure Payment Form - Example</title>
<meta http-equiv="Content-Type" content="text/html;">
<style type="text/css">
body {
	background-color:#e9e9ce;
}
a{ color:#009ec3; text-decoration:none; outline:none; font-size:28px; font-weight:bold; }
a:hover{ color:#2c539e; text-decoration:underline; }
</style>
</head>

<body>
<table align="center" width="860" heigh="670">
  <tr>
    <td><strong>Generated payment form URL is:</strong><br /><?php echo $server_url; ?>?<?php echo $requestString; ?></td>
  </tr>
  <tr>
    <td align="center" valign="middle" width="100%">
      <br />
      <a href="<?php echo $server_url; ?>?<?php echo $requestString; ?>" >Please click here to load the above generated form!</a>
    </td>
  </tr>
</table>
</body>
</html>
