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
$table = 'referrs';
 
// Table's primary key
$primaryKey = 'ReferrID';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes


$columns = array(
    array( 'db' => 'ReferrID', 'dt' => 0 ),
	array(
        'db'        => 'ReferrUserID',
        'dt'        => 1,
        'formatter' => function( $d, $row ) {
			global $layer;
			
			$UserName = $layer->GetData('users', 'UserName', 'UserID', $d);
			return $UserName;
        }
    ),
	array(
        'db'        => 'ReferrReferralUserID',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {
			global $layer;
			
			$UserName = $layer->GetData('users', 'UserName', 'UserID', $d);
			return $UserName;
        }
    ),
	array(
	    'db' => 'ReferrPaid',
        'dt' => 3,
        'formatter' => function ($d, $row) {
	        global $layer;

	        return $layer->ReferEarned($row['ReferrID']);
            #return serialize($row);
            #return $row['ReferrID'];
        }
    ),
	array(
        'db'        => 'ReferrDate',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {
			return date('d.m.Y', $d);
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
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, "ReferrUserID = '".$UserID."'")
);