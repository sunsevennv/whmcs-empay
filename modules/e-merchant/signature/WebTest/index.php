<html>
<head>
<meta http-equiv="Content-Type" content="text/html"; charset="iso-8859-1">
<STYLE type=text/css>
body{ background-color:#e9e9ce; }
input[type=text]{ width:280px; height:26px; -webkit-border-radius: 4px; border-radius: 4px; border:1px solid #c1c1c1; padding:0 0 0 5px;}
input[type=submit]{
	width:120px;
	height:28px;
	font-weight:bold;
	cursor:pointer;
	background: #00b7ea;
	background: -moz-linear-gradient(top,  #00b7ea 0%, #009ec3 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#00b7ea), color-stop(100%,#009ec3));
	background: -webkit-linear-gradient(top,  #00b7ea 0%,#009ec3 100%);
	background: -o-linear-gradient(top,  #00b7ea 0%,#009ec3 100%);
	background: -ms-linear-gradient(top,  #00b7ea 0%,#009ec3 100%);
	background: linear-gradient(to bottom,  #00b7ea 0%,#009ec3 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00b7ea', endColorstr='#009ec3',GradientType=0 );
	border:none;
	-webkit-border-radius: 4px;
	border-radius: 4px; 
}
input[type=submit]:hover{ background:#009ec3; color:#fff; }
.style1 {color: #FF0000}
</STYLE>

<title>eCommerce Secure Payment Form - Example</title>
</head>

<body>
<h2 align="center">Purchase Order</h2>
<TABLE height="100%" cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TBODY>
  <TR>
    <TD vAlign=top align=middle colSpan=2>
      <!-- Main Cell Start -->
	        <TABLE cellSpacing=0 cellPadding=0 width=797 border=0>
        <TBODY>
        <TR>
          <TD width="797" vAlign=top>
            <h3>              <BR>
              </FONT>
              <span class="style1">
              
                        </span> </h3>
            <form method="post" action="FormGPostTest.php">
            <CENTER>
            <TABLE width="100%" border=0>
              <TBODY>
              <TR>
              <TR>
                <TD align="right"><strong>Payment Form URL <span class="style1">*</span></strong></TD>
                <TD>
                  <input type="text" name="paymentformurl" value="https://payment-F8C406A3.emppay.com" >
				      </TD>
              </TR>
              <TR>
                <TD align="right"><strong>Currency <span class="style1">*</span></strong></TD>
                <TD>
                  <input type="text" name="currency" value="EUR"  >
				      </TD>
              </TR>
              <TR>
                <TD align="right"><strong>Price <span class="style1">*</span></strong></TD>
                <TD>
                  <input name="amount" id="amount" value="20.00" type="text" >
                </TD>
              </TR>
              <TR>
                <TD align="right"><strong>Reference <span class="style1">*</span></strong></TD>
                <TD>
                  <input name="reference" type="text" id="reference" value="9820084" >
                </TD>
              </TR>

              <TR height=50>
                <TD>&nbsp;</TD>
                <TD>
                  <input type="submit" value="Submit Order" >
                </TD>
              </TR>

            </TABLE>
            </CENTER>
            </form>
          </TD>
        </TR>
        
      </TABLE>
	        <!-- Main Cell End -->
    </TD>
  </TR>
  </TBODY>
</TABLE>

</body>
</html>
