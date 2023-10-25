<?php

function load_config($name, $schema){
	$config = new DOMDocument();
	$config->load($name);
	$res = $config->schemaValidate($schema);
	if ($res===FALSE){ 
	   throw new InvalidArgumentException("Check configuration file");
	} 		
	$data = simplexml_load_file($name);	
	$ip = $data->xpath("//ip");
	$name = $data->xpath("//name");
	$user = $data->xpath("//user");
	$password = $data->xpath("//password");	
	$conn_string = sprintf("mysql:dbname=%s;host=%s", $name[0], $ip[0]);
	$result = [];
	$result[] = $conn_string;
	$result[] = $user[0];
	$result[] = $password[0];
	return $result;
}

function check_user($name, $password){
	$res = load_config(dirname(__FILE__)."/configuration.xml", dirname(__FILE__)."/configuration.xsd");
	$db = new PDO($res[0], $res[1], $res[2]);
	$ins = "select codRes, mail from restaurants where mail = '$name' 
			and password = '$password'";
	$resul = $db->query($ins);	
	if($resul->rowCount() === 1){		
		return $resul->fetch();		
	}else{
		return FALSE;
	}
}
function load_categories(){
	$res = load_config(dirname(__FILE__)."/configuration.xml", dirname(__FILE__)."/configuration.xsd");
	$db = new PDO($res[0], $res[1], $res[2]);
	$ins = "select codCat, name from categories";
	$resul = $db->query($ins);	
	if (!$resul) {
		return FALSE;
	}
	if ($resul->rowCount() === 0) {    
		return FALSE;
    }
	//if there is one or more
	return $resul;	
}

function load_category($codCat){
	$res = load_config(dirname(__FILE__)."/configuration.xml", dirname(__FILE__)."/configuration.xsd");
	$db = new PDO($res[0], $res[1], $res[2]);
	$ins = "select name, description from categories where codcat = $codCat";
	$resul = $db->query($ins);	
	if (!$resul) {
		return FALSE;
	}
	if ($resul->rowCount() === 0) {    
		return FALSE;
    }	
	//if there is one or more
	return $resul->fetch();	
}
function load_products_category($codCat){
	$res = load_config(dirname(__FILE__)."/configuration.xml", dirname(__FILE__)."/configuration.xsd");
	$db = new PDO($res[0], $res[1], $res[2]);	
	$sql = "select * from products where category  = $codCat";	
	$resul = $db->query($sql);	
	if (!$resul) {
		return FALSE;
	}
	if ($resul->rowCount() === 0) {    
		return FALSE;
    }	
	//if there is one or more
	return $resul;			
}
function load_products($codes){
	$res = load_config(dirname(__FILE__)."/configuration.xml", dirname(__FILE__)."/configuration.xsd");
	$db = new PDO($res[0], $res[1], $res[2]);
	if(count($codes) == 0){
		return FALSE;
	}
	$texto_in = implode(",", $codes);
	$ins = "select * from products where codProd in($texto_in)";
	$resul = $db->query($ins);	
	if (!$resul) {
		return FALSE;
	}
	if ($resul->rowCount() === 0) {    
		return FALSE;
    }	
	return $resul;	
}


function insert_order($cart, $codRes){
	$res = load_config(dirname(__FILE__)."/configuration.xml", dirname(__FILE__)."/configuration.xsd");
	$db = new PDO($res[0], $res[1], $res[2]);
	$db->beginTransaction();	
	$hour = date("Y-m-d H:i:s", time());
	// insert order
	$sql = "insert into orders(Date, Sent, Restaurant) 
			values('$hour',0, $codRes);";
	$resul = $db->query($sql);	
	if (!$resul) {
		return FALSE;
	}
	// take id of new order for the detail rows
	$order = $db->lastInsertId();
	// inser rows in orderproducts
	foreach($cart as $codProd=>$units){
		$sql = "insert into orderproducts(`Order`, Product, Units) 
		             values($order, $codProd, $units)";			
		$resul = $db->query($sql);			
		if (!$resul) {
			echo $sql . "<br>";
			print_r($db->errorInfo());
			$db->rollback();
			return FALSE;
		}		
		$sql = "update products set stock = stock - $units
		             where codProd = $codProd";			
		$resul = $db->query($sql);			
		if (!$resul) {
			$db->rollback();
			return FALSE;
		}
	}
	$db->commit();
	return $order;
}
