<?php
 
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
 
 require_once('../functions.php');
 
// DB table to use
$table = 'orders';
 
// Table's primary key
$primaryKey = 'OrderID';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes


$columns = array(
    array( 'db' => 'OrderID', 'dt' => 0 ),
	array(
        'db'        => 'OrderServiceID',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {
			global $layer;
			
			$ServiceName = $layer->GetData('services', 'ServiceName', 'ServiceID', $d);
			return $ServiceName;
        }
    ),
	array(
        'db'        => 'OrderCharge',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {
			global $currency;
			
			return $d.$currency;
        }
    ),
	array( 'db' => 'OrderQuantity', 'dt' => 3 ),
	array( 'db' => 'OrderLink', 'dt' => 4 ),
	array(
        'db'        => 'OrderDate',
        'dt'        => 5,
        'formatter' => function( $d, $row ) {
			return date('d.m.Y', $d);
        }
    ),
	array(
        'db'        => 'OrderID',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {
			global $orders;
			
			$OrderRemains = $orders->CheckOrderRemains($d);
			return $OrderRemains;
        }
    ),
	array(
        'db'        => 'OrderID',
        'dt'        => 7,
        'formatter' => function( $d, $row ) {
			global $orders;
			
			$OrderStartCount = $orders->CheckOrderStartCount($d);
			return $OrderStartCount;
        }
    ),
	array(
        'db'        => 'OrderID',
        'dt'        => 8,
        'formatter' => function( $d, $row ) {
			global $orders;
			
			$OrderStatus = $orders->CheckOrderStatus($d);
			return $OrderStatus;
        }
    )
   
);
// SQL server connection information
$sql_details = array(
    'user' => username,
    'pass' => password,
    'db'   => database,
    'host' => hostname
);
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( 'ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, "OrderUserID = '$UserID' AND OrderType = 'API'" )
);