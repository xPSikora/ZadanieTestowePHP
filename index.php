<!DOCTYPE html> 
<head>	
    <meta http-equiv="Content-Language" content="pl">
    <meta charset="UTF-8">
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="style.css">

	<title>Zadanie testowe</title>
</head>
<?php
	include_once 'include/NBP_Class.php';
	$NBP_Data = new NBP_Data();
	$NBP_Data->Set_REQUEST_Params();
	$NBP_Data->Currency_Date_Form();
	if(isset($_REQUEST['Get_Data'])){
		if($NBP_Data->Check_Date()){
			$NBP_Data->Get_Data();
			$NBP_Data->Create_Currency_Object();
			$NBP_Data->Table();
		}
	}
?>
