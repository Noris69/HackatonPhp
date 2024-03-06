<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

	<?php include 'includes/navbar.php'; ?>
	 
	  <div class="content-wrapper" style="background-color: #F1E9D2 ">
	    <div class="container" style="background-color: #F1E9D2 ">

	      <!-- Main content -->
	      <section class="content">
	      	<?php
	      		$parse = parse_ini_file('admin/config.ini', FALSE, INI_SCANNER_RAW);
    			$title = $parse['election_title'];
	      	?>
	      	<h1 class="page-header text-center title"><b><?php echo strtoupper($title); ?></b></h1>
	        <div class="row">
	        	<div class="col-sm-10 col-sm-offset-1">
	        		<?php
				        if(isset($_SESSION['error'])){
				        	?>
				        	<div class="alert alert-danger alert-dismissible">
				        		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					        	<ul>
					        		<?php
					        			foreach($_SESSION['error'] as $error){
					        				echo "
					        					<li>".$error."</li>
					        				";
					        			}
					        		?>
					        	</ul>
					        </div>
				        	<?php
				         	unset($_SESSION['error']);

				        }
				        if(isset($_SESSION['success'])){
				          	echo "
				            	<div class='alert alert-success alert-dismissible'>
				              		<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
				              		<h4><i class='icon fa fa-check'></i> Success!</h4>
				              	".$_SESSION['success']."
				            	</div>
				          	";
				          	unset($_SESSION['success']);
				        }

				    ?>
 
				    <div class="alert alert-danger alert-dismissible" id="alert" style="display:none;">
		        		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			        	<span class="message"></span>
			        </div>

				    <?php

	$sql = "SELECT * FROM votes WHERE voters_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $voter['id']);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
?>
		<div class="text-center" style="color:black ; font-size: 35px; font-family:Times">
			<h3>Vous avez déjà voté pour cette élection.</h3>
			<a href="#view" data-toggle="modal" class="btn btn-curve btn-primary btn-lg" style="background-color: #4682B4 ;color:black ; font-size: 22px; font-family:Times">Consulter votre bulletin de vote</a>
		</div>
<?php
	} else {
?>
		<form method="POST" id="ballotForm" action="submit_ballot.php">
<?php
			include 'includes/slugify.php';

			$candidate = '';
			$current_date = date('Y-m-d');

			$sql = "SELECT * FROM positions WHERE start_date <= ? AND end_date >= ? ORDER BY priority ASC";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ss", $current_date, $current_date);
			$stmt->execute();
			$position_result = $stmt->get_result();

			while($row = $position_result->fetch_assoc()) {
				$slug = slugify($row['description']);

				$sql = "SELECT * FROM candidates WHERE position_id = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("i", $row['id']);
				$stmt->execute();
				$candidate_result = $stmt->get_result();

				while($crow = $candidate_result->fetch_assoc()) {
					$image = (!empty($crow['photo'])) ? 'images/'.$crow['photo'] : 'images/profile.jpg';
					$input_type = ($row['max_vote'] > 1) ? 'checkbox' : 'radio';
					$input_name = ($row['max_vote'] > 1) ? $slug."[]" : slugify($row['description']);
					$checked = isset($_SESSION['post'][$slug]) && ($_SESSION['post'][$slug] == $crow['id']) ? 'checked' : '';

					$candidate .= '
						<li>
							<input type="'.$input_type.'" class="flat-red '.$slug.'" name="'.$input_name.'" value="'.$crow['id'].'" '.$checked.'>
							<button type="button" class="btn btn-primary btn-sm btn-curve clist platform" style="background-color: #4682B4 ;color:black ; font-size: 12px; font-family:Times" data-platform="'.$crow['platform'].'" data-fullname="'.$crow['firstname'].' '.$crow['lastname'].'"><i class="fa fa-search"></i> Platform</button>
							<img src="'.$image.'" height="100px" width="100px" class="clist">
							<span class="cname clist">'.$crow['firstname'].' '.$crow['lastname'].'</span>
						</li>
					';
				}

				$instruction = ($row['max_vote'] > 1) ? 'Vous pouvez sélectionner jusqu\'à '.$row['max_vote'].' candidats' : 'Sélectionnez un seul candidat';

				echo '
					<div class="row">
						<div class="col-xs-12">
							<div class="box box-solid" style="background-color: #d8d1bd" id="'.$row['id'].'">
								<div class="box-header with-border" style="background-color: #d8d1bd">
									<h3 class="box-title"><b>'.$row['description'].'</b></h3>
								</div>
								<div class="box-body">
									<p>'.$instruction.'
										<span class="pull-right">
											<button type="button" class="btn btn-success btn-sm btn-curve reset" style="background-color:#9CD095 ;color:black ; font-size: 12px; font-family:Times"  data-desc="'.slugify($row['description']).'"><i class="fa fa-refresh"></i> Reset</button>
										</span>
									</p>
									<div id="candidate_list">
										<ul>
											'.$candidate.'
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				';

				$candidate = '';
			}
?>
			<div class="text-center">
				<button type="button" class="btn btn-success btn-curve" style='background-color: #9CD095 ;color:black ; font-size: 12px; font-family:Times' id="preview"><i class="fa fa-file-text"></i> Preview</button> 
				<button type="submit" class="btn btn-primary btn-curve" style='background-color: #4682B4 ;color:black ; font-size: 12px; font-family:Times'name="vote"><i class="fa fa-check-square-o"></i> Submit</button>
			</div>
		</form>
<?php
	}
?>


	        	</div>
	        </div>
	      </section>
	     
	    </div>
	  </div>
  
  	<?php include 'includes/footer.php'; ?>
  	<?php include 'includes/ballot_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
	$('.content').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass: 'iradio_flat-green'
	});

	$(document).on('click', '.reset', function(e){
	    e.preventDefault();
	    var desc = $(this).data('desc');
	    $('.'+desc).iCheck('uncheck');
	});

	$(document).on('click', '.platform', function(e){
		e.preventDefault();
		$('#platform').modal('show');
		var platform = $(this).data('platform');
		var fullname = $(this).data('fullname');
		$('.candidate').html(fullname);
		$('#plat_view').html(platform);
	});

	$('#preview').click(function(e){
		e.preventDefault();
		var form = $('#ballotForm').serialize();
		if(form == ''){
			$('.message').html('You must vote atleast one candidate');
			$('#alert').show();
		}
		else{
			$.ajax({
				type: 'POST',
				url: 'preview.php',
				data: form,
				dataType: 'json',
				success: function(response){
					if(response.error){
						var errmsg = '';
						var messages = response.message;
						for (i in messages) {
							errmsg += messages[i]; 
						}
						$('.message').html(errmsg);
						$('#alert').show();
					}
					else{
						$('#preview_modal').modal('show');
						$('#preview_body').html(response.list);
					}
				}
			});
		}
		
	});

});
</script>
</body>
</html>