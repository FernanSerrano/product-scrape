<?php
	include 'function.php';
	//include 'connect.php';
	echo '
		<br>	
		<form method="post" name="scrap_form" id="scrap_form" action="function.php">   
		<label>Enter the Barcode Number!</label>
		<br> 
		<input autofocus type="input" name="barcode" id="numcode"> 
		<button type="submit" class="btn btn-primary">Submit
				</button>		
		</form>';

	/*function scrap() {
	if(isset($_POST['website_url'])) 
	{
	echo $_POST['numcode']; 
	 	}
	}

	if(isset($_POST['submit']) && $_POST['numcode']){
	$html = scrapWebsite($_POST['numcode']);
	//$postDetail = getPostDetails($html);
	 echo '<pre>';
	print_r($postDetail);
	echo '</pre>';
	$insert="INSERT INTO product_details (product_name, brand, manufacturer, EAN, country, description) VALUES(?, ?, ?, ?, ?, ?)";
	$result=mysqli_prepare($con,$insert);
	if($result){
 	echo "<p>Entered</p>";
	// Bind parameters to placeholders
	mysqli_stmt_bind_param($result, "ssssss", $postDetail['product_name'], $postDetail['brand'], $postDetail['manufacturer'], 
 	$postDetail['EAN'], $postDetail['country'], $postDetail['description']);
	echo "<p>Executing</p>";
	// Execute statement
	mysqli_stmt_execute($result);
 	echo "Data Inserted Successfully";
	// Close statement and connection
		mysqli_stmt_close($result);
		mysqli_close($con);

		
	header('location:view.php');
	}else{
	 	die(mysqli_error($con));
	}
	}*/
?>