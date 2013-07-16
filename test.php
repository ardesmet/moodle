<?php
$link = sqlsrv_connect('explorer', array('UID' => 'ardesmet', 'PWD' => 'Oussama123'));
if($link === FALSE) {
    echo 'Could not connect';
    //print_r(sqlsrv_errors(SQLSRV_ERR_ALL));
	die('Could not connect: ' . sqlsrv_errors(SQLSRV_ERR_ALL));

}
echo 'Successful connection';
sqlsrv_close($link);
?>