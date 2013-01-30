<?php

	error_reporting(E_ALL); //report all errors
    
    //**************** PLEASE FILL IN ***************//
    //PayChoice account credentials
    $useTestSite = true; //true for sandbox test site; otherwise false for production
	$apiUserName = "{insert api username}"; //api username
	$apiPassword = "{insert api password}"; //api password
    //**************** PLEASE FILL IN ***************//   

	include "../lib/paychoice.php";
?>
<html>
<body>
<form action="paychoice-demo.php" method="post">
    <h1>Charge Card</h1>
    <table>
	    <tr>
		    <td>Card Name: *</td>
		    <td>
			    <input type="text" value="" name="cardName" />
		    </td>
	    </tr>
	    <tr>
		    <td>Card Number: *</td>
		    <td>
			    <input type="text" value="" name="cardNumber" />
		    </td>
	    </tr>
	    <tr>
		    <td>Expiry: *</td>
		    <td>
			    <select name="cardExpiryMonth">
				    <option value="01">01</option>
				    <option value="02">02</option>
				    <option value="03">03</option>
				    <option value="04">04</option>
				    <option value="05">05</option>						
				    <option value="06">06</option>						
				    <option value="07">07</option>					
				    <option value="08">08</option>						
				    <option value="09">09</option>						
				    <option value="10">10</option>						
				    <option value="11">11</option>																			
				    <option value="12">12</option>																			
			    </select> 
			    /
			    <select name="cardExpiryYear">
				    <?php 
					    for ($year = date('y'); $year< date('y')+10; $year++)
					    {
						    echo "<option value=\"{$year}\">20{$year}</option>";
					    }
				    ?>
			    </select>
		    </td>
	    </tr>
	    <tr>
		    <td>CVV: *</td>
		    <td>
			    <input type="text" size="4" value="" maxlength="4" name="cardCSC" />
		    </td>
	    </tr>
	    <tr>
		    <td>Reference: *</td>
		    <td>
			    <input type="text" name="referenceNumber" />
		    </td>	
	    </tr>
	    <tr>
		    <td>Amount: *</td>
		    <td>
			    <input type="text" name="chargeAmount" />
			    <input type="submit" name="chargeCard" value="Charge" />
		    </td>
	    </tr>
    </table>
</form>
<?php
    
try
{

	/** CHARGE CARD **/
	if (isset($_POST["chargeCard"]))
	{	
		$gateway = new Paychoice($apiUserName, $apiPassword, $useTestSite);
		$result = $gateway->chargeCard(
			$_POST["referenceNumber"],
			$_POST["cardName"],
			$_POST["cardNumber"],
			$_POST["cardExpiryMonth"],
			$_POST["cardExpiryYear"],
			$_POST["cardCSC"],
            "AUD",
			$_POST["chargeAmount"]);			
			
		?>
		<h2>Charge Results</h2>
		<?php
        displayCharge($result->charge);
	}

}
catch (PaychoiceException $ex)
{
	echo $ex;
}

?>
<hr />
<form action="paychoice-demo.php" method="post">
    <h1>Query Charge</h1>
    <table>
    	<tr>
		    <td>Transaction Id: *</td>
		    <td>
			    <input type="text" value="" name="transactionId" /> <input type="submit" name="queryCharge" value="Query" />
		    </td>
	    </tr>
    </table>
</form>
 <?php
        
    try
	{

        /** QUERY CHARGE **/
		if (isset($_POST["queryCharge"]))
		{	
			$gateway = new Paychoice($apiUserName, $apiPassword, $useTestSite);	
			$result = $gateway->getCharge($_POST["transactionId"]);
            
			
			?>
			<h2>Charge</h2>
			<?php
            displayCharge($result->charge);
		}

    }
	catch (PaychoiceException $ex)
	{
		echo $ex;
	}

?>
<hr />
<form action="paychoice-demo.php" method="post">
    <h1>Store Card</h1>
    <table>
    	<tr>
		    <td>Card Name: *</td>
		    <td>
			    <input type="text" value="" name="cardName" />
		    </td>
	    </tr>
	    <tr>
		    <td>Card Number: *</td>
		    <td>
			    <input type="text" value="" name="cardNumber" />
		    </td>
	    </tr>
	    <tr>
		    <td>Expiry: *</td>
		    <td>
			    <select name="cardExpiryMonth">
				    <option value="01">01</option>
				    <option value="02">02</option>
				    <option value="03">03</option>
				    <option value="04">04</option>
				    <option value="05">05</option>						
				    <option value="06">06</option>						
				    <option value="07">07</option>					
				    <option value="08">08</option>						
				    <option value="09">09</option>						
				    <option value="10">10</option>						
				    <option value="11">11</option>																			
				    <option value="12">12</option>																			
			    </select> 
			    /
			    <select name="cardExpiryYear">
				    <?php 
					    for ($year = date('y'); $year< date('y')+10; $year++)
					    {
						    echo "<option value=\"{$year}\">20{$year}</option>";
					    }
				    ?>
			    </select>
		    </td>
	    </tr>
	    <tr>
		    <td>CVV: *</td>
		    <td>
			    <input type="text" size="4" value="" maxlength="4" name="cardCSC" />
			    <input type="submit" name="storeCard" value="Store"  />
		    </td>
	    </tr>
        </table>
</form>
<?php
	try
	{

		/** STORE CARD **/
		if (isset($_POST["storeCard"]))
		{	
			$gateway = new Paychoice($apiUserName, $apiPassword, $useTestSite);	
			$result = $gateway->storeCard(
				$_POST["cardName"],
				$_POST["cardNumber"],
				$_POST["cardExpiryMonth"],
				$_POST["cardExpiryYear"],
                $_POST["cardCSC"]);				
                
			?>
			<h2>Storage Results</h2>
			<?php
            displayCard($result->card);
		}

    }
	catch (PaychoiceException $ex)
	{
		echo $ex;
	}
?>
<hr />
<h1>Charge Stored Card</h1>
<form action="paychoice-demo.php" method="post">		
	<table>
		<tr>
			<td>Credit Card Token: *</td>
			<td>
				<input type="text" name="storedToken" />
			</td>	
		</tr>				
		<tr>
		    <td>Reference: *</td>
		    <td>
			    <input type="text" name="referenceNumber" />
		    </td>	
		</tr>				
		<tr>
			<td>Amount: *</td>
			<td>
				<input type="text" name="chargeAmount" />
				<input type="submit" name="chargeStoredCard" value="Charge" />
			</td>
		</tr>				
	</table>
</form>
<?php
    try
	{
	
		/** CHARGE STORE **/
		if (isset($_POST["chargeStoredCard"]))
		{	
			$gateway = new Paychoice($apiUserName, $apiPassword, $useTestSite);
			$result = $gateway->chargeToken(
				$_POST["referenceNumber"],
				$_POST["storedToken"],
                "AUD",
				$_POST["chargeAmount"]);
				
			?>
			<h2>Charge Results</h2>
			<?php	
            displayCharge($result->charge);				
		}

    }
	catch (PaychoiceException $ex)
	{
		echo $ex;
	}
?>
<hr />
<h1>Stored Card</h1>
<form action="paychoice-demo.php" method="post">		
	<table>
		<tr>
			<td>Credit Card Token: *</td>
			<td>
				<input type="text"  name="storedToken" />
			</td>	
		</tr>				
		<tr>
			<td></td>
			<td><input type="submit" name="retrieveStoredCard" value="Retreive" /></td>
		</tr>				
	</table>
</form>
<?php
    
    try
	{

        /** RETRIEVE CARD **/
		if (isset($_POST["retrieveStoredCard"]))
		{	
			$gateway = new Paychoice($apiUserName, $apiPassword, $useTestSite);
			$result = $gateway->getToken($_POST["storedToken"]);
				
			?>
			<h2>Retrieve Results</h2>
			<?php					
            displayCard($result->card);
		}

    }
	catch (PaychoiceException $ex)
	{
		echo $ex;
	}    

?>
<hr />
<h1>List Charges</h1>
<form action="paychoice-demo.php" method="post">		
	<input type="submit" name="getCharges" value="List charges" />
</form>
<?php
    try
	{
		
		if (isset($_POST["getCharges"]))
		{	
			$gateway = new Paychoice($apiUserName, $apiPassword, $useTestSite);	
			$results = $gateway->getCharges(0,10);
				
			?>
			<h2>Charges</h2>
            <?php
            foreach($results->charge_list as $result)    
            {
                displayCharge($result);
                echo "<div style=\"margin: 10px 0;\">...</div>";
            }
		}

    }
	catch (PaychoiceException $ex)
	{
		echo $ex;
	}
?>
<hr />
<h1>Public Key</h1>
<form action="paychoice-demo.php" method="post">		
	<input type="submit" name="queryPublicKey" value="Get public key" />
</form>
<?php

    try
	{

        if (isset($_POST["queryPublicKey"]))
		{	
			$gateway = new Paychoice($apiUserName, $apiPassword, $useTestSite);	
			$result = $gateway->getPublicKey();
            
            ?>
			<h2>Public Key</h2>
			<?php
            echo "<code>".$result->public_key."</code>";
        }
	
	}
	catch (PaychoiceException $ex)
	{
		echo $ex;
	}

?>

</body>
</html>


<?php
    

    //*********** HELPER FUNCTIONS *************//

    function displayCard($card)
    {
        ?>
        <table>
            <tr><td>Token:</td><td><?php echo $card->token ?></td></tr>
			<tr><td>Card Name:</td><td><?php echo $card->card_name ?></td></tr>
			<tr><td>Card Number:</td><td><?php echo $card->masked_number ?></td></tr>
            <tr><td>Card Type:</td><td><?php echo $card->card_type ?></td></tr>
            <tr><td>Expiry Month:</td><td><?php echo $card->expiry_month ?></td></tr>
            <tr><td>Expiry Year:</td><td><?php echo $card->expiry_year ?></td></tr>
		</table>
        <?php
    }

    function displayCharge($charge)
    {
        ?>
    	<table>
			<tr><td>Transaction Id:</td><td><?php echo $charge->id ?></td></tr>
            <tr><td>Reference:</td><td><?php echo $charge->reference ?></td></tr>
            <tr><td>Date:</td><td><?php echo $charge->created ?></td></tr>
			<tr><td>Status:</td><td><?php echo $charge->status ?></td></tr>
            <tr><td>Status Code:</td><td><?php echo $charge->status_code ?></td></tr>
			<tr><td>Error Description:</td><td><?php echo $charge->error ?></td></tr>
			<tr><td>Error Code:</td><td><?php echo $charge->error_code ?></td></tr>
            <tr><td>Amount:</td><td><?php echo $charge->amount ?></td></tr>
		</table>
        <?php
    }

?>