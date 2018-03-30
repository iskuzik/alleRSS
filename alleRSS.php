<?php

define('ALL_KEY', 'tutaj wklej klucz webapi allegro'); //klucz webapi allegro 
define('RES_SIZE', 60); //domyślna ilość zwracanych wyników

$filterOptions = array();

if (isset($_GET['string']) && strlen($_GET['string']) > 1) {
    $searchString = str_replace("+", " ", $_GET['string']);
    $filterOptions[] = array('filterId' => 'search',
    						'filterValueId' => array($searchString));
}

if (isset($_GET['userId'])) {
    	$filterOptions[] = array('filterId' => 'userId',
                        		'filterValueId' => array($_GET['userId']));
}

if (isset($_GET['categoryId'])) {
    	$filterOptions[] = array('filterId' => 'category',
                        		'filterValueId' => array($_GET['categoryId']));
}

if (isset($_GET['resultSize']) && $_GET['resultSize'] > 1 && $_GET['resultSize'] < 1001) {
    $resultSize = $_GET['resultSize'];
} else {
    $resultSize = RES_SIZE;
}

if (isset($_GET['description']) && $_GET['description'] == 1) {
    $filterOptions[] = array('filterId' => 'description',
							'filterValueId' => array(true));
}

if ((isset($_GET['price_from']) && is_numeric($_GET['price_from'])) || (isset($_GET['price_to']) && is_numeric($_GET['price_to']))) {
	$priceArray = array();

	if (isset($_GET['price_from']) && is_numeric($_GET['price_from'])) {

		$priceArray['rangeValueMin'] = $_GET['price_from'];
	}

	if (isset($_GET['price_to']) && is_numeric($_GET['price_to'])) {

		$priceArray['rangeValueMax'] = $_GET['price_to'];
	}
	
	$filterOptions[] = array('filterId' => 'price',
							'filterValueRange' => $priceArray);
}


if (isset($_GET['offerType'])) {
	$offerType = $_GET['offerType'];
	
    if ($offerType == "buyNow") {
    
    	$filterOptions[] = array('filterId' => 'offerType',
                        		'filterValueId' => array('buyNow'));
    	
    } else if ($offerType == "auction") {
    
    	$filterOptions[] = array('filterId' => 'offerType',
                        		'filterValueId' => array('auction'));
    }
}


if (isset($_GET['condition'])) {
	$condition = $_GET['condition'];
	
    if ($condition == "new") {
    
    	$filterOptions[] = array('filterId' => 'condition',
                        		'filterValueId' => array('new'));
    	
    } else if ($condition == "used") {
    
    	$filterOptions[] = array('filterId' => 'condition',
                        		'filterValueId' => array('used'));
    }
}

/* Offer Options */

if (isset($_GET['freeShipping']) && $_GET['freeShipping'] == 1) {
    
    	$filterOptions[] = array('filterId' => 'offerOptions',
                        		'filterValueId' => array('freeShipping'));
}

if (isset($_GET['freeReturn']) && $_GET['freeReturn'] == 1) {
    
    	$filterOptions[] = array('filterId' => 'offerOptions',
                        		'filterValueId' => array('freeReturn'));
}

if (isset($_GET['generalDelivery']) && $_GET['generalDelivery'] == 1) {
    
    	$filterOptions[] = array('filterId' => 'offerOptions',
                        		'filterValueId' => array('generalDelivery'));
}

if (isset($_GET['installmentAvailable']) && $_GET['installmentAvailable'] == 1) {
    
    	$filterOptions[] = array('filterId' => 'offerOptions',
                        		'filterValueId' => array('installmentAvailable'));
}

if (isset($_GET['vatInvoice']) && $_GET['vatInvoice'] == 1) {
    
    	$filterOptions[] = array('filterId' => 'offerOptions',
                        		'filterValueId' => array('vatInvoice'));
}

if (isset($_GET['personalReceipt']) && $_GET['personalReceipt'] == 1) {
    
    	$filterOptions[] = array('filterId' => 'offerOptions',
                        		'filterValueId' => array('personalReceipt'));
}

/* Location Options */

if (isset($_GET['city'])) {
	$city = str_replace("+", " ", $_GET['city']);
    $filterOptions[] = array('filterId' => 'city',
    						'filterValueId' => array($city));
    						
} else if (isset($_GET['distance']) && isset($_GET['postCode'])) {
    
    $filterOptions[] = array('filterId' => 'distance',
    						'filterValueId' => array($_GET['distance']));
	$filterOptions[] = array('filterId' => 'postCode',
							'filterValueId' => array($_GET['postCode']));
    
} else if (isset($_GET['state'])) {
    $filterOptions[] = array('filterId' => 'state',
    						'filterValueId' => array($_GET['state']));
}


try {
	$client = new SoapClient('https://webapi.allegro.pl/service.php?wsdl',array('trace' => 1, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS));
     
	$doGetItemsList_request = array(
									'webapiKey' => ALL_KEY,
									'countryId' => 1,
									'filterOptions' => $filterOptions,
									'sortOptions' => array(
															'sortType' => 'startingTime',
															'sortOrder' => 'desc'),
									'resultSize' => $resultSize,
									'resultOffset' => 0,
									'resultScope' => 1);
 
	try {
		$response = $client->doGetItemsList($doGetItemsList_request);
		
		if (isset($_GET['string'])) {
				$infoString = "Wyszukiwanie: " . $searchString;
		}
		
		if (isset($_GET['userId'])) {
			if (isset($_GET['string'])) {
         			$infoString .= ", użytkownik: ";
         		} else {
         			$infoString = "Użytkownik: ";
         		}
         	
         		if ($response->itemsCount > 1) {
         			$infoString .= $response->itemsList->item[0]->sellerInfo->userLogin;
         		} else {
         			$infoString .= $_GET['userId'];
         		}
         	}
         
         	if (isset($_GET['categoryId'])) {
         		if (isset($_GET['string']) || isset($_GET['userId'])) {
         			$infoString .= ", kategoria: ";
         		} else {
         			$infoString = "Kategoria: ";
         		}
         	
         		if ($response->itemsCount > 1) {
         	
         			foreach ($response->categoriesList->categoriesTree->item as $key => $cat) {
         				if($cat->categoryId == $_GET['categoryId']) {
         					$infoString .= $cat->categoryName;
         				}
         			}
         		} else {
         			$infoString .= $_GET['categoryId'];
         		}
         	}
        
        	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
        	echo "<rss version=\"2.0\">\n";
        	echo "<channel>\n";
        	echo "<title>Allegro.pl - $infoString</title>\n";
        	echo "<link>https://allegro.pl</link>\n";
        	echo "<description>$infoString - najnowsze oferty. Promowane: ". $response->itemsFeaturedCount . "/" . ($response->itemsCount > $resultSize ? $resultSize : $response->itemsCount) . "</description>\n";
        
		foreach ($response->itemsList->item as $key => $object) {
			echo "<item>\n";
			$title = str_replace("&", "&amp;", $object->itemTitle);
			$title = str_replace("<", "&lt;", $title);
			
			echo "<title>" . $title . "</title>\n";
			echo "<link>https://allegro.pl/i" . $object->itemId . ".html</link>\n";
			echo "<description>\n";
			echo "<![CDATA[";
			if (isset($object->sellerInfo->userId)) {
			echo "Sprzedający: <a href=\"https://allegro.pl/show_user.php?uid=" . $object->sellerInfo->userId . "\">" . $object->sellerInfo->userLogin . "</a><br />\n";
			}
			echo "Do końca: " . $object->timeToEnd;
			
			if (isSet($object->endingTime)) {
				echo " (";
				echo str_replace("T", ", ", $object->endingTime);
				echo ")";
			}
			echo "<br />\n";
			
			foreach ($object->priceInfo->item as $key => $price) {
			
				if ($price->priceType == "bidding") {
					echo "Aktualna cena: " . $price->priceValue . "zł<br />\n";
				}
				
				if ($price->priceType == "buyNow") {
					echo "Cena Kup Teraz: " . $price->priceValue . "zł<br />\n";
				}
				
				if ($price->priceType == "advert") {
					echo "Cena w ogłoszeniu: " . $price->priceValue . "zł<br />\n";
				}
			}
			
			echo "<p><a href=\"https://allegro.pl/i" . $object->itemId . ".html\">Przejdź do oferty</a></p>\n";
			
			if(isSet($object->photosInfo->item)) {
				foreach ($object->photosInfo->item as $key => $photo) {
			
					if ($photo->photoSize == "medium" && $photo->photoIsMain == 1) {
 		   				echo "<a href=\"https://allegro.pl/i" . $object->itemId . ".html\"><img src=\"" . $photo->photoUrl . "\"></a>]]>\n";
 		   			}
				}
			}
			echo "</description>\n";
			echo "<guid isPermaLink=\"false\">" . $object->itemId . "</guid>\n";
			echo "</item>\n";

		}
    		echo "</channel>\n";
    		echo "</rss>";
		
	} catch (SoapFault $error) {
        	echo $error->faultstring;
    }
     
} catch (SoapFault $error) {
	echo 'Błąd ', $error->faultcode, ': ', $error->faultstring;
}

?>
