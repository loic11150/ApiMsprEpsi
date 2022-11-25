<?php
//database acces 
	try {
      $db = new PDO('mysql:host=localhost;dbname=dolibarr','dolibarrmysql','changeme');
    } 
    catch (PDOException $e) {
        echo "Erreur : ".$e->getMessage();
        die();
    }
   // get http request method  
   $request_method = $_SERVER["REQUEST_METHOD"];
   switch($request_method)
  {
  //-- Delete
  	case 'DELETE':
    	if (isset($_DELETE['id'])){
    		$req = $db->prepare('DELETE FROM llx_societe where code_client =":id"');
    		$req = $req->execute(array(':id'=> $_GET('id')));
    		if ($req){
    			$response = array('status'=>201,'status_message'=>'Utilisateur '.$_GET['id'].' supprimé avec succès !');
	    		http_response_code(201);
	    		echo json_encode($response);
	    	}else{
	    			$response = array('status'=>400,'status_message'=>"Il n'y a pas ce code client dans la BDD.");
    				http_response_code(400);
    				echo json_encode($response);
    				// echo($request_method);
	    	}
    	}else{
    		$response = array('status'=>400,'status_message'=>"Il n'y a pas d'ID renseigné / code client.");
    		http_response_code(400);
    		echo json_encode($response);
    	}
    	break;
   //--
   //-- GET
  	case 'GET':
  		if(isset($_GET['id'])){
  			$exec = $db->query('SELECT nom,email,code_client from llx_societe where client = "2" and code_client = "'.$_GET['id'].'";');
  			// $res = $exec->execute(array(':id'=>);
  			$res = $exec->fetchAll(PDO::FETCH_ASSOC);
  			if ($res == ""){

  			}else{
  				echo json_encode($res);	
  			}
  		}else{
  			$exec = $db->query('SELECT nom,email,code_client from llx_societe where client = "2";');
  			$exec = $exec->fetchAll(PDO::FETCH_ASSOC);
  			echo json_encode($exec);	
  		}
  		break;
  	//--
  	//--POST
    case 'POST':
    	if(isset($_POST['firstName']) and isset($_POST['mail'])){
    		$max_req = 'SELECT max(code_client) as max from llx_societe;';
	  		$res = $db->query($max_req);
	  		$res = $res->fetchAll(PDO::FETCH_ASSOC);
	  		$max = $res[0]['max'];
	  		$max_num = explode('-',$max);
	  		$num = intval($max_num[1])+1;
	  		$code_client = $max_num[0].'-0000'.$num;
	    	$exec = $db->prepare('INSERT INTO llx_societe (nom,email,client,code_client) values (:firstName,:mail,:client,:code_client)');
	    	$res = $exec->execute(array(':firstName' => $_POST['firstName'], ':mail'=>$_POST['mail'],':client'=>2,':code_client'=>$code_client));	
	    	if($res){
	    		$response = array('status'=>201,'status_message'=>'Utilisateur '.$code_client.' ajouté avec succès !');
	    		http_response_code(201);
	    		echo json_encode($response);
	    		
	    	}else{
	    		$response = array('status'=>500,'status_message'=>'Erreur SQL');
	    		http_response_code(500);
	    		echo json_encode($response);
	    	}
    	}else{
	    	$response = array('status'=>400,'status_message'=>'Il manque le mail ou le prenom.');
    		http_response_code(400);
    		echo json_encode($response);
    	}
    	break;
    //--
    default:
      //invalid request
      header("HTTP/1.0 405 Method Not Allowed");
      break;
  }
?>