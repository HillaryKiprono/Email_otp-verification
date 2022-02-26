<?php
session_start();
function signup($data)
{
  $errors=array();
  

  //validate
  if(!preg_match('/^[a-zA-Z]+$/', $data['username'])){
    $errors[] = "Please enter a valid username";
 }

if(!filter_var($data['email'],FILTER_VALIDATE_EMAIL)){
    $errors[] = "Please enter a valid email";
}

if(strlen(trim($data['password'])) < 4){
    $errors[] = "Password must be atleast 4 chars long";
}

if($data['password'] != $data['password2']){
    $errors[] = "Passwords must match";
}

//save
if(count($errors) == 0){

  $arr['username'] = $data['username'];
  $arr['email'] = $data['email'];
  $arr['password'] = hash('sha256',$data['password']);
  $arr['date'] = date("Y-m-d H:i:s");

  $query = "insert into users (username,email,password,date) values 
  (:username,:email,:password,:date)";

  database_run($query,$arr);
}
  return $errors;
 
}


function login($data)
{
  $errors=array();
  

  //validate
  

if(!filter_var($data['email'],FILTER_VALIDATE_EMAIL)){
    $errors[] = "Please enter a valid email";
}

if(strlen(trim($data['password'])) < 4){
    $errors[] = "Password must be atleast 4 chars long";
}


//check
if(count($errors) == 0){

  $arr['email'] = $data['email'];
  $arr['password'] = hash('sha256',$data['password']);

  $query = "select * from users where email = :email $$ password=:password limit 1";

  $row=database_run($query,$arr);
  if(is_array($row))
  {
    $_SESSION["USER"]=$row;
    $_SESSION["LOGGED_IN"]=true;


  }
  else{
    $errors[] = " wrong email or Password ";

  }
}
  return $errors;
 
}



function database_run($query,$vars = array())
{
	$string = "mysql:host=localhost;dbname=verify_email";
	$con = new PDO($string,'root','');

	if(!$con){
		return false;
	}

	$stm = $con->prepare($query);
	$check = $stm->execute($vars);
 
	if($check){
		
		$data = $stm->fetchAll(PDO::FETCH_OBJ);
		
		if(count($data) > 0){
			return $data;
		}
	}

	return false;
}

?>