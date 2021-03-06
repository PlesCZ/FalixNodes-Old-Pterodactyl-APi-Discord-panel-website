<?php
session_start();
include("global.php");
include("config.php");
if( checklogin() == true ) {
	$user = $_SESSION['discord_user'];
	$pterodactyl_panelinfo = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->fetch_assoc();
	$pterodactyl_username = $pterodactyl_panelinfo['pterodactyl_username'];
	$pterodactyl_password = $pterodactyl_panelinfo['pterodactyl_password'];
} else {
	header("Location: /");
	die();
}
include("plans.php");
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  
	  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	  
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <title>LimitedNodes | Free Minecraft Server Hosting</title>
	  
	  <script>
		  $(window).on('load', function(){
			$("#createServerBox").load("create");
		  });
	  </script>
	  
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<script>
	  (adsbygoogle = window.adsbygoogle || []).push({
		google_ad_client: "ca-pub-9842180416703608",
		enable_page_level_ads: true
	  });
	</script>
  </head>
  <body>
	  
	  <center>
	
			<div class="jumbotron">
			  <h1 class="display-4">LimitedNodes</h1>
				<hr class="my-4">
			  <p class="lead">Free Minecraft Server Hosting</p>
				<?php
				echo '<hr class="my-4">Welcome ' . $user->username . '#' . $user->discriminator . '!<br /><a class="btn btn-primary btn-lg" href="logout" role="button"><i class="fas fa-sign-out-alt"></i> Logout</a><br /><br />';
				?>
			</div>
		  
	  <div class="container">
		  <?php
		  if( checklogin() == true ) {
			  echo '
			  <a href="/" class="btn btn-primary" role="button">Home</a>&nbsp;
			  <a href="#" class="btn btn-primary" role="button" data-toggle="modal" data-target="#logintopanel"><i class="fas fa-sign-in-alt"></i> Login to Panel</a>&nbsp;
			  <a href="#" class="btn btn-primary" role="button" data-toggle="modal" data-target="#plan_info"><i class="fas fa-info"></i> My Plan</a>&nbsp;
			  <a href="pricing" class="btn btn-primary" role="button"><i class="fas fa-dollar-sign"></i> Pricing</a>
			  ';
		  }
		  ?>
		  <br /><br />
		  <?php
		  if( checklogin() == true ) {
			  echo '<form method="POST" action="c_ram" id="c_ram"></form>';
				$results = mysqli_query($conn, "SELECT * FROM servers WHERE owner_id='" . mysqli_real_escape_string($conn, $user->id) . "'");
				echo "<table class=\"table table-striped\">";
			  echo "
				  <thead>
				  <tr>
					<th>Name</th>
					<th>Current RAM</th>
					<th>New RAM</th>
				  </tr>
				</thead>
			  ";
			  if( $results->num_rows !== 0 ) {
				 while($rowitem = mysqli_fetch_array($results)) {
					 $ch = curl_init("https://" . $ptero_domain . "/api/application/servers/" . $rowitem['pterodactyl_serverid']);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						"Authorization: Bearer " . $ptero_key,
						"Content-Type: application/json",
						"Accept: Application/vnd.pterodactyl.v1+json"
					));
					$result = curl_exec($ch);
					curl_close($ch);
					 $result = json_decode($result, true);
					 $serverRAM = $result['attributes']['limits']['memory'] . " MB";
					 $serverRAM_nombtext = $result['attributes']['limits']['memory'];
					 $serverDisk = $result['attributes']['limits']['disk'] . " MB";
					 $serverCores = $result['attributes']['limits']['cpu'] / 100;
					 $serverName = $result['attributes']['name'];
					echo "<tr>";
					echo "<td>" . $serverName . "</td>";
					echo "<td>" . $serverRAM . "</td>";
					echo "<td>" . '<input type="number" class="form-control" name="' . $rowitem['pterodactyl_serverid'] . '" value="' . $serverRAM_nombtext . '" min="128" form="c_ram"></input>' . "</td>";
					echo "</tr>";
				}
			  } else {
				  echo "You don't have any servers.";
			  }
				echo "</table>"; //end table tag
		  }
		  ?>
		  <button type="submit" form="c_ram" class="btn btn-success">Change RAM</button>
		<?php include("templates/footer.php"); ?>
	  </div>
		  
	  </center>
	  
	  <?php
	  if( checklogin() == true ) {
		  $PlanExpiry = $conn->query("SELECT * FROM users WHERE discord_id='" . mysqli_real_escape_string($conn, $user->id) . "'")->fetch_assoc()['plan_expiry'];
		  if( $PlanExpiry == 0 ) {
			  $PlanExpiry = "Never";
		  } else {
			  $PlanExpiry = date('m/d/Y', $PlanExpiry);
		  }
		  echo '
		  	<!-- modal:logintopanel -->
			<div id="logintopanel" class="modal fade" role="dialog">
			  <div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
				  <div class="modal-header">
					<h4 class="modal-title">Login to Panel</h4>
				  </div>
				  <div class="modal-body">
				  	<strong>Your Panel Username:</strong> ' . $pterodactyl_username . '<br />
					<strong>Your Panel Password:</strong> ' . $pterodactyl_password . '
					<hr>
					<a target="_blank" href="https://' . $ptero_domain . '/auth/login" class="btn btn-primary" role="button">Panel Login</a>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  </div>
				</div>

			  </div>
			</div>
			
			<!-- modal:plan_info -->
			<div id="plan_info" class="modal fade" role="dialog">
			  <div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
				  <div class="modal-header">
					<h4 class="modal-title">Plan Info</h4>
				  </div>
				  <div class="modal-body">
				  	<strong>You are currently on:</strong> ' . $level_data['title'] . '<br />
					<strong>Your plan gives you:</strong> ' . $level_data['ram_balance'] . ' MB RAM balance, ' . $level_data['max_servers'] . ' max servers, and ' . $level_data['max_cores'] . ' max CPU cores per server.<br />
					<strong>Your plan will expire on:</strong> ' . $PlanExpiry . '
					<hr>
					You have additional <strong>' . $user_extra_ram . '</strong> MB RAM balance.<br />
					You also have additional <strong>' . $user_extra_servers . '</strong> servers.<br />
					<em>additional RAM balanace/servers</em> means that you can use those if you finished your plan\'s max resources.
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  </div>
				</div>

			  </div>
			</div>
		  ';
	  }
	  ?>

    <!-- Optional JavaScript -->
    <!-- first Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>