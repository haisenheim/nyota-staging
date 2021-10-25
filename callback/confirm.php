<?PHP
	
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	
	$amount  = '';
	$status = '';
	$transaction_id = '';
	$currency = '';
	$refresh=0;
	$transaction_date = '';
	$msisdn = '';
	$paymentID = '';

		if(isset($_GET['status'])){
			$status = $_GET['status'];
			$transaction_id = $_GET['transaction_id'];
			$currency = $_GET['currency'];
			$transaction_date = $_GET['transaction_date'];
			$msisdn = $_GET['Payment'];
			$paymentID = $_GET['paymentID'];
			$num=
			$amount = $_GET['amount'];
$msg= 'Page de Callback' ;


 echo '<META HTTP-EQUIV="refresh" CONTENT="30">';

			if($status == "200"){
				
	
	
	
	echo"<br>";
	
			echo"<span style='color:blue;font-size:20px'> Tableau de bord des retour des transactions clients </span>.<br>";
			
				echo"<table border=1>" ;

				echo"<tr><th>Client</th><th>Montant</th><th>Devise</th><th>Trans_ID</th><th>Trans_STATUT</th><th>PaiementAM_ID</th><th>TransDATE</th></tr>";
				echo"<tr><td>$msisdn</td><td>$amount</td><td>$Curency</td><td>$transaction_id</td><td>$status</td><td>$paymentID</td><td>$transaction_date</td></tr>".'<br>';
			
				echo"</table>";
	
			                    }
			
		else{
				
				echo'Votre Transaction n\'a pas pu aboutir';
			}	
		}

	?>

