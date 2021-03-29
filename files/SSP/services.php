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
$table = 'services';

// Table's primary key
$primaryKey = 'ServiceID';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes


$columns = array(
    array( 'db' => 'ServiceID', 'dt' => 0 ),
    array(
        'db'        => 'ServiceCategoryID',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {
            global $layer;
            
            $CategoryName = $layer->GetData('categories', 'CategoryName', 'CategoryID', $d);
            return $CategoryName;
        }
    ),
    array( 'db' => 'ServiceName', 'dt' => 2 ),
    array( 'db' => 'ServiceDescription', 'dt' => 3 ),
    array( 'db' => 'ServiceMinQuantity', 'dt' => 4 ),
    array( 'db' => 'ServiceMaxQuantity', 'dt' => 5 ),
	array(
        'db'        => 'ServiceID',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {
			global $currency;
			global $layer;

			$ServiceMinQuantity = $layer->GetData('services', 'ServiceMinQuantity', 'ServiceID', $d);
			$ServicePrice = $layer->GetData('services', 'ServicePrice', 'ServiceID', $d);
			$ServicePrice = $ServicePrice / $ServiceMinQuantity * 1000;
			$ServicePrice = round($ServicePrice, 2);

			return $ServicePrice.$currency.' / 1000';
        }
    ),
	 array(
        'db'        => 'ServiceID',
        'dt'        => 7,
        'formatter' => function( $d, $row ) {
			global $currency;
			global $layer;

			$ServiceMinQuantity = $layer->GetData('services', 'ServiceMinQuantity', 'ServiceID', $d);
			$ServiceResellerPrice = $layer->GetData('services', 'ServiceResellerPrice', 'ServiceID', $d);
			$ServiceResellerPrice = $ServiceResellerPrice / $ServiceMinQuantity * 1000;
      $ServiceResellerPrice = round($ServiceResellerPrice, 2);

			return $ServiceResellerPrice.$currency.' / 1000';
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
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, "ServiceActive = 'Yes'" )
);
