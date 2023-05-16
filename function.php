<?php
include 'design.php';
include 'connect.php';
include "simple_html_dom.php";
if(isset($_POST['barcode'])){
	$barcode = $_POST['barcode'];

	scrapWebsite($barcode);
}

function scrapWebsite($barcode) {	


	
	$response = ['is_success'=>false,'message'=>''];

	
	 $sites = ['https://www.buycott.com/','https://go-upc.com/search?q=','https://www.upcitemdb.com/upc/','https://www.barcodelookup.com/','https://upcdatabase.org/search'];
	
		
	foreach($sites as $site){

		$ch = curl_init();
		$url = $site.''.$barcode;
	

		curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$html = curl_exec($ch);
		curl_close($ch);

		if(!empty($html)){
			$response["is_success"] = false;
			$response["message"] = 'No data found';
			//$result = json_decode($response);

			
			if($site == 'https://www.buycott.com/'){
				$product_information = forBuycott($html);
			}else if($site == 'https://go-upc.com/search?q='){
				$product_information = forgoUPC($html);
			}else if($site == 'https://www.upcitemdb.com/upc/'){
				$product_information = forupcItemDb($html);
			}else if($site == 'https://www.barcodelookup.com/'){
				$product_information = forbarcodelookup($html);
			 }else if($site == 'https://upcdatabase.org/search'){
				$product_information = forupcdatabase($html);
			 }
		 }
		
		
		if(!empty($product_information['product_name'])){
			// save to database
			echo $url;
			echo'<pre>';
			print_r($product_information);
			echo'</pre>';
			$response['is_success'] = true;
			$response['message'] = 'Product found';
			return;
			//return json_decode($response);

			$con=new mysqli('localhost','root','','scrap');

			if(!$con){
       	 die(mysqli_error($con));
						}
				$insert="INSERT INTO product_details (product_name, brand, manufacturer, EAN, country, image, description) VALUES(?, ?, ?, ?, ?, ?, ?)";
				$result=mysqli_prepare($con,$insert);
				if($result){
				 echo "<p>Entered</p>";
				// Bind parameters to placeholders
				mysqli_stmt_bind_param($result, "sssssss", $product_information['product_name'], $product_information['brand'], $product_information['manufacturer'], 
				$product_information['EAN'], $product_information['country'],$product_information['image'], $product_information['description']);
				echo "<p>Executing</p>";
				// Execute statement
				mysqli_stmt_execute($result);
				 echo "Data Inserted Successfully";
				// Close statement and connection
					mysqli_stmt_close($result);
					mysqli_close($con);
			
					
				
				}
				
				
		
		
			

		}
	

	 	}

	}

	function forBuycott($html){
		$doc = new DOMDocument();
		$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);
		$productName = $xpath->query('//div[@id="container_header"]/h2')->item(0)->textContent;
		// Extract brand name
		$brandNode = $xpath->query('//td[text()="Brand"]/following-sibling::td/a')->item(0);
		$brand = $brandNode ? $brandNode->textContent : 'None';

		// Extract manufacturer name
		$manufacturerNode = $xpath->query('//td[text()="Manufacturer"]/following-sibling::td/a')->item(0);
		$manufacturer = $manufacturerNode ? $manufacturerNode->textContent : 'None';

		// Extract EAN code
		$eanNode = $xpath->query('//td[text()="EAN"]/following-sibling::td')->item(0);
		$ean = $eanNode ? $eanNode->textContent : 'None';
		
		// Extract country name
		$countryNode = $xpath->query('//td[text()="Country"]/following-sibling::td')->item(0);
		$country = $countryNode ? $countryNode->textContent : 'None';

		// Extract category name
		$categoryNode = $xpath->query('N/a')->item(0);
		$category = $categoryNode ? $categoryNode->textContent : 'None';

		// Extract header image
		$headerImageNode = $xpath->query('//div[@class="centered_image header_image"]/img')->item(0);
		$headerImage = $headerImageNode ? $headerImageNode->getAttribute("src")  : 'None';

		// Extract description
		$descriptionNode = $xpath->query('//td[text()="Description"]/following-sibling::td/div[@id="read_desc"]')->item(0);
		$description = $descriptionNode ? $descriptionNode->textContent : 'None';

		$product_information = [
			'product_name' => $productName,
			'brand'=>$brand,
			'manufacturer'=>$manufacturer,
			'EAN'=>$ean,
			'country'=>$country,
			'category'=>$category,
			'image'=>$headerImage,
			'description'=> trim($description)
		];
		return $product_information;
		
	}
	function forgoUPC($html){
		$doc = new DOMDocument();
		$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		//upc site
        // Extract manufacturer name from the other web page
        $productName = $xpath->query('//h1[@class="product-name"]')->item(0)->textContent;
		//new site
		if (empty($productName)) {

		}

		// Extract brand name
		$brandNode = $xpath->query('//td[text()="Brand"]/following-sibling::td')->item(0);
		$brand = $brandNode ? $brandNode->textContent : 'None';

		// Extract manufacturer name
		$manufacturerNode = $xpath->query('//td[text()="Manufacturer"]/following-sibling::td/a')->item(0);
		$manufacturer = $manufacturerNode ? $manufacturerNode->textContent : 'None';

		// Extract EAN code
		$eanNode = $xpath->query('//td[text()="EAN"]/following-sibling::td')->item(0);
		$ean = $eanNode ? $eanNode->textContent : 'None';
		
		// Extract country name
		$countryNode = $xpath->query('//td[text()="Country"]/following-sibling::td')->item(0);
		$country = $countryNode ? $countryNode->textContent : 'None';

		// Extract Category name
		$categoryNode = $xpath->query('//td[text()="Category"]/following-sibling::td')->item(0);
		$category = $categoryNode ? $categoryNode->textContent : 'None';

		// Extract header image
		$headerImageNode = $xpath->query('//figure[@class="product-image non-mobile"]/img')->item(0);
		$headerImage = $headerImageNode ? $headerImageNode->getAttribute("src") : 'None';

		// Extract description
		$descriptionNode = $xpath->query('//h2[1]/following-sibling::span')->item(0);
		$description = $descriptionNode ? $descriptionNode->textContent : 'None';
		$product_information = [
			'product_name' => $productName,
			'brand'=>$brand,
			'manufacturer'=>$manufacturer,
			'EAN'=>$ean,
			'country'=>$country,
			'category'=>$category,
			'image'=>$headerImage,
			'description'=> trim($description)
		];
		return $product_information;
	}
	function forupcItemDb($html){

		$doc = new DOMDocument();
		$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);
		$productName = $xpath->query('//p[@class="detailtitle"]')->item(0)->textContent;
		//new site
		if (empty($productName)) {

		}

		// Extract brand name
		$brandNode = $xpath->query('//td[text()="Brand:"]/following-sibling::td')->item(0);
		$brand = $brandNode ? $brandNode->textContent : 'None';

		// Extract manufacturer name
		$manufacturerNode = $xpath->query('//td[text()="Manufacturer"]/following-sibling::td/a')->item(0);
		$manufacturer = $manufacturerNode ? $manufacturerNode->textContent : 'None';

		// Extract EAN code
		$eanNode = $xpath->query('//td[text()="EAN-13:"]/following-sibling::td')->item(0);
		$ean = $eanNode ? $eanNode->textContent : 'None';
		
		// Extract country name
		$countryNode = $xpath->query('//td[text()="Country of Registration:"]/following-sibling::td')->item(0);
		$country = $countryNode ? $countryNode->textContent : 'None';

		// Extract category name
		$categoryNode = $xpath->query('N/a')->item(0);
		$category = $categoryNode ? $categoryNode->textContent : 'None';

		// Extract header image
		$headerImage = $xpath->query('//img[class="product amzn"]/src')->item(0);
		$image = $headerImage ? $headerImage->textContent : 'None';


		// Extract description
		$descriptionNode = $xpath->query('N/a')->item(0);
		$description = $descriptionNode ? $descriptionNode->textContent : 'None';
		$product_information = [
			'product_name' => $productName,
			'brand'=>$brand,
			'manufacturer'=>$manufacturer,
			'EAN'=>$ean,
			'country'=>$country,
			'category'=>$category,
			'image'=>$image,
			'description'=> trim($description)
		];
		return $product_information;
	}
	function forbarcodelookup($html){
		$doc = new DOMDocument();
		$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		//upc site
        // Extract manufacturer name from the other web page
        $productName = $xpath->query('//div[@class="col-50 product-details"]/h4')->item(0)->textContent;
		//new site
		if (empty($productName)) {

		}

		// Extract brand name
		$brandNode = $xpath->query('N/a')->item(0);
		$brand = $brandNode ? $brandNode->textContent : 'None';

		// Extract manufacturer name
		$manufacturerNode = $xpath->query('//div[tclass="Manufacturer: "]/span')->item(0);
		$manufacturer = $manufacturerNode ? $manufacturerNode->textContent : 'None';

		// Extract EAN code
		$eanNode = $xpath->query('//h1[text()="EAN"]')->item(0);
		$ean = $eanNode ? $eanNode->textContent : 'None';
		
		// Extract country name
		$countryNode = $xpath->query('n/a')->item(0);
		$country = $countryNode ? $countryNode->textContent : 'None';

		// Extract Category name
		$categoryNode = $xpath->query('//div[@class="Category: "]/span')->item(0);
		$category = $categoryNode ? $categoryNode->textContent : 'None';

		// Extract header image
		$headerImageNode = $xpath->query('//div[@class="largeProductImage"]/img')->item(0);
		$headerImage = $headerImageNode ? $headerImageNode->getAttribute("src") : 'None';

		// Extract description
		$descriptionNode = $xpath->query('//div[@class="Descripton: &nbsp; "]/span')->item(0);
		$description = $descriptionNode ? $descriptionNode->textContent : 'None';
	
		$product_information = [
			'product_name' => $productName,
			'brand'=>$brand,
			'manufacturer'=>$manufacturer,
			'EAN'=>$ean,
			'country'=>$country,
			'category'=>$category,
			'image'=>$headerImage,
			'description'=> trim($description)
		];
		return $product_information;
	}
	function forupcdatabase($html){
		$doc = new DOMDocument();
		$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);

		//upc site
        // Extract manufacturer name from the other web page
        $productName = $xpath->query('//h3[@class="lead"]/h3')->item(0)->textContent;
		//new site
		if (empty($productName)) {

		}

		// Extract brand name
		$brandNode = $xpath->query('//td[text()="Brand"]/following-sibling::td')->item(0);
		$brand = $brandNode ? $brandNode->textContent : 'None';

		// Extract manufacturer name
		$manufacturerNode = $xpath->query('//td[text()="Manufacturer"]/following-sibling::td')->item(0);
		$manufacturer = $manufacturerNode ? $manufacturerNode->textContent : 'None';

		// Extract EAN code
		$eanNode = $xpath->query('n/a')->item(0);
		$ean = $eanNode ? $eanNode->textContent : 'None';
		
		// Extract country name
		$countryNode = $xpath->query('//td[text()="Countries"]/following-sibling::td')->item(0);
		$country = $countryNode ? $countryNode->textContent : 'None';

		// Extract Category name
		$categoryNode = $xpath->query('//td[text()="Category"]/following-sibling::td')->item(0);
		$category = $categoryNode ? $categoryNode->textContent : 'None';

		// Extract header image
		$headerImageNode = $xpath->query('//div[@id="upc_img"]/img')->item(0);
		$headerImage = $headerImageNode ? $headerImageNode: 'None';

		// Extract description
		$descriptionNode = $xpath->query('//td[text()="Description"]/following-sibling::td"]/span')->item(0);
		$description = $descriptionNode ? $descriptionNode->textContent : 'None';
	
		$product_information = [
			'product_name' => $productName,
			'brand'=>$brand,
			'manufacturer'=>$manufacturer,
			'EAN'=>$ean,
			'country'=>$country,
			'category'=>$category,
			'image'=>$headerImage,
			'description'=> trim($description)
		];
		return $product_information;
	}
// function insertToDatabase(){

// }
// function getPostDetails_backup ($html) {
// 	$titles = array();
// 	$i = 0 ;
// 	foreach($html->find('h2') as $post) {		
// 		$titles[$i]['Porduct_Name'] = $post->plaintext;  			
// 		$i++;
// 	}
// 	$i = 0 ;
// 	foreach($html->find('div[class=centered_image header_image] img') as $img) {		
// 		$titles[$i]['Image'] = $img->src;  			
// 		$i++;
// 	}
//     $i = 0 ;
//     foreach($html->find('table[class=table product_info_table]td a') as $post){
//         $titles[$i]['Category'] = $post->plaintext; 
//         $i++;
//     }
//     $i = 0 ;
//     foreach($html->find('table[class=table product_info_table]td a') as $post){
//         $titles[$i]['Manufacturer'] = $post->plaintext; 
//         $i++;
//     }
// 	return $titles;	
// }