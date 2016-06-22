<?php

include_once('../ParamSigner.class.php');
//print_r($_GET);

$authenticatedParam= ParamSigner::paramAuthenticate('XYZ12345',$_GET);
if(!$authenticatedParam)
{
  die("Data tampering detected or offer expired.");
}
else
{      
  echo '<br><font color="red"><b>Your transaction has been approved.</b></font>';

  echo '<br><b>Order ID: </b>'.$_REQUEST['order_id'];
  echo '<br><b>Transaction ID: </b>'.$_REQUEST['trans_id'];
  echo '<br><b>Transaction Type: </b>'.$_REQUEST['trans_type'];
  echo '<br><b>Item ID: </b>'.$_REQUEST['item_id'];
  echo '<br><b>Amount: </b>'.$_REQUEST['amount'];
}

?>