<?php

require_once('../files/functions.php');

if(isset($_REQUEST['key']) && ctype_alnum($_REQUEST['key']) && is_string($_REQUEST['key'])) {
	$stmt = $pdo->prepare('SELECT UserID, UserAPI, UserName FROM users WHERE UserAPI = :UserAPI');
	$stmt->execute(array(':UserAPI' => $_REQUEST['key']));
	
	if($stmt->rowCount() == 1) {
		$query = $stmt->fetch();
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'create') {
			if($query['UserName'] != 'demo') {
				if(isset($_REQUEST['service']) && ctype_digit($_REQUEST['service'])) {
					if(isset($_REQUEST['link']) && ctype_alnum($_REQUEST['link']) && is_string($_REQUEST['link'])) {
						if(isset($_REQUEST['quantity']) && ctype_digit($_REQUEST['quantity'])) {
							$quantity = $_REQUEST['quantity'];
							
							if(!isset($_REQUEST['comments'])) {
								$quantity = $_REQUEST['quantity'];
							} else {
								$quantity = preg_replace("/\n/m", '\n', $_REQUEST['comments']);
								$quantity = substr_count( $quantity, "\n" );
							}
							
							$link = $_REQUEST['link'];
							$service_id = $_REQUEST['service'];
							$charge = $orders->GetPrice($service_id, $quantity);
							$max_quantity = $layer->GetData('services', 'ServiceMaxQuantity', 'ServiceID', $service_id);
							
							$stmt = $pdo->prepare('SELECT * FROM services WHERE ServiceID = :ServiceID');
							$stmt->execute(array(':ServiceID' => $service_id));

							if($stmt->rowCount() == 1) {
								$row = $stmt->fetch();
								if($row['ServiceType'] == 'comments') {
									if(isset($_REQUEST['comments']) && !empty($_REQUEST['comments'])) {
										$additional = $_REQUEST['comments'];
										$additional = str_replace("\n", ",", $additional);
									} else {
										echo '{"Error":"Comments value is required."}';
										exit();
									}
								}
								
								if($row['ServiceType'] == 'hashtag') {
									if(isset($_REQUEST['hashtag']) && !empty($_REQUEST['hashtag'])) {
										$additional = $_REQUEST['hashtag'];
									} else {
										echo '{"Error":"Hashtag value is required."}';
										exit();
									}
								}
								
								if($row['ServiceType'] == 'mentions') {
									if(isset($_REQUEST['username']) && !empty($_REQUEST['username'])) {
										$additional = $_REQUEST['username'];
									} else {
										echo '{"Error":"IG mentions username value is required."}';
										exit();
									}
								}
								
								if($UserBalance >= $charge) {
									if($quantity >= $row['ServiceMinQuantity'] && $quantity <= $row['ServiceMaxQuantity']) {
										$stmt = $pdo->prepare('SELECT * FROM orders WHERE OrderLink = :OrderLink AND OrderServiceID = :OrderServiceID');
										$stmt->execute(array(':OrderLink' => $link, ':OrderServiceID' => $service_id));
										
										if($stmt->rowCount() > 0) {
											if($stmt->rowCount() == 1) {
												$query_row = $stmt->fetch();
												$qu_am = $query_row['OrderQuantity'];
											} else {
												$qu_am = 0;
												
												foreach($stmt->fetchAll() as $qu_row) {
													$qu_am += $qu_row['OrderQuantity'];
												}
											}
											$total = $qu_am + $quantity;
											$total_more = $max_quantity - $qu_am;
											if($total_more < 0) {
												$total_more = 0;
											}
											
											if($total > $max_quantity) {
												echo '{"Error":"'.$total_more.' quantity is left for this link &amp service."}';
												exit();
											}
										}
										$order_id = 0;
										$start_count = 0;
										
										if(!empty($row['ServiceAPI'])) {
											$URL = str_replace('[QUANTITY]', $quantity, $row['ServiceAPI']);
											$URL = str_replace('[LINK]', $link, $URL);
											if(isset($additional) && !empty($additional))
												$URL = str_replace('[ADDON]', $additional, $URL);
											$return = $layer->SendCurl($URL);
											$resp = json_decode($return);
											
											if(isset($resp) && property_exists($resp, 'order'))
												$order_id = $resp->order;
										}

										$NewBalance = $UserBalance - $charge;

										if($row['ServiceType'] != 'default') {
											 $stmt = $pdo->prepare('INSERT INTO orders (OrderServiceID, OrderUserID, OrderQuantity, OrderLink, OrderCharge, OrderAPIID, OrderAdditional, OrderDate, OrderType)
										  VALUES (:OrderServiceID, :OrderUserID, :OrderQuantity, :OrderLink, :OrderCharge, :OrderAPIID, :OrderAdditional, :OrderDate, :OrderType)');

											$stmt->execute(array(':OrderServiceID' => $service_id, ':OrderUserID' => $query['UserID'], ':OrderQuantity' => $quantity, ':OrderLink' => $link,
												':OrderCharge' => $charge, ':OrderAPIID' => $order_id, ':OrderAdditional' => $additional, ':OrderDate' => time(), ':OrderType' => 'API'));
										} else {
											$stmt = $pdo->prepare('INSERT INTO orders (OrderServiceID, OrderUserID, OrderQuantity, OrderLink, OrderCharge, OrderAPIID, OrderDate, OrderType)
										  VALUES (:OrderServiceID, :OrderUserID, :OrderQuantity, :OrderLink, :OrderCharge, :OrderAPIID, :OrderDate, :OrderType)');

											$stmt->execute(array(':OrderServiceID' => $service_id, ':OrderUserID' => $query['UserID'], ':OrderQuantity' => $quantity, ':OrderLink' => $link,
												':OrderCharge' => $charge, ':OrderAPIID' => $order_id, ':OrderDate' => time(), ':OrderType' => 'API'));
										}
										
										$c_order_id = $pdo->lastInsertId();
										
										$stmt = $pdo->prepare('UPDATE users SET UserBalance = :UserBalance WHERE UserID = :UserID');
										$stmt->execute(array(':UserBalance' => $NewBalance, ':UserID' => $query['UserID']));
										
										echo '{"order":"'.$c_order_id.'"}';
									} else {
										echo '{"Error":"Quantity is lower or bigger than the default."}';
										exit();
									}
								} else {
									echo '{"Error":"Not enough balance."}';
									exit();
								}
							} else {
								echo '{"Error":"Service does not exists."}';
								exit();
							}
						} else {
							echo '{"Error":"Invalid quantity."}';
							exit();
						}
					} else {
						echo '{"Error":"Invalid link."}';
						exit();
					}
				} else {
					echo '{"Error":"Invalid service ID."}';
					exit();
				}
			} else {
				echo '{"Error":"Demo account is not allowed to place orders by API."}';
				exit();
			}
		} else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'status') {
			if(isset($_REQUEST['order']) && ctype_digit($_REQUEST['order'])) {
				$stmt = $pdo->prepare('SELECT * FROM orders WHERE OrderID = :OrderID');
				$stmt->execute(array(':OrderID' => $_REQUEST['order']));
				
				if($stmt->rowCount() == 1) {
					$order_row = $stmt->fetch();
					
					$OrderRemains = $orders->CheckOrderRemains($order_row['OrderID']);
					$OrderStartCount = $orders->CheckOrderStartCount($order_row['OrderID']);
					$OrderStatus = $orders->CheckOrderStatus($order_row['OrderID']);
			
					$html = '{';
					$html .= '"quantity":"'.$order_row['OrderQuantity'].'",';
					$html .= '"link":"'.$order_row['OrderLink'].'",';
					$html .= '"charge":"'.$order_row['OrderCharge'].'",';
					$html .= '"service":"'.$order_row['OrderServiceID'].'",';
					$html .= '"remains":"'.$OrderRemains.'",';
					$html .= '"status":"'.$OrderStatus.'",';
					$html .= '"start_count":"'.$OrderStartCount.'"';
					$html .= '}';
					
					echo $html;
				} else {
					echo '{"Error":"Invalid order ID."}';
					exit();
				}
			}
		} else {
			echo '{"Error":"Invalid action."}';
			exit();
		}
	} else {
		echo '{"Error":"Invalid API key."}';
		exit();
	}
} else {
	echo '{"Error":"Invalid API usage."}';
	exit();
}
