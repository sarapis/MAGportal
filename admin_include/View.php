<?php
class View
{
	// ===== singleton =============================================
	
	protected static $instance;

	public static function engine()
	{
		if (!isset(self::$instance)) 
		{
			$c = get_called_class();
			self::$instance = new $c;
		}
		return self::$instance;
	}
		
	public function __clone()
	{
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}


	
// ===== html ================================================

	public function redirect($trg, $die=true)
	{
		if (isset($_GET['embedded']))
			$trg = $trg . '?embedded';
		header("Location: {$trg}");
		if ($die)
			die();
	}


	public function back()
	{
		
	?>	<script>
			function goBack() {
			window.history.back();
			}
			document.onload(goBack());
		</script>
	<?php
	die();
	}


	public function welcomeForm()
	{
	?><div class="<?php embContainer()?> my-4">
			<div class="row justify-content-center" id="jumboFormContainer">
			  <div class="col-6">
				<h2>Mutual Aid Group Portal</h2>
				  <p class="mb-2 mt-4"><a href="email.php<?php embUrl() ?>" type="button" class="btn btn-primary">Register</a></p>
				  <p><a href="email.php<?php embUrl() ?>" type="button" class="btn btn-primary">Login</a></p>
			  </div>
			</div>
		</div>
	<?php
	}


	public function emailForm()
	{
	?><div class="<?php embContainer()?> my-4">
			<div class="row justify-content-center" id="jumboFormContainer">
			  <div class="col-6">
				<h2>Mutual Aid Group Portal</h2>
				<form enctype="multipart/form-data" action="auth.php<?php embUrl() ?>" method="POST">
				  <div class="form-group">
					<label for="email">Email address</label>
					<input type="email" class="form-control" id="email" name="email">
					<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
				  </div>
				  <button type="submit" class="btn btn-primary">Submit</button>
				</form>
			  </div>
			</div>
		</div>
	<?php
	}


	public function emailUpdate($email)
	{
		?><div class="jumbo">You have requested email changing.<br/>Please verify new email address.</div>
		<form id="redirect" enctype="multipart/form-data" action="auth.php<?php embUrl() ?>" method="POST">
			<input type="hidden" name="email" value="<?php echo $email ?>">
		</form>
		<script>
			window.setTimeout(function (){	
				$('#redirect').submit();
			}, 1000);
		</script>
		<?php
	}


	public function emailUpdateFailed()
	{
		?><div class="jumbo">User with entered email exists in database.<br/>Please try again.</div>
		<form id="redirect" enctype="multipart/form-data" action="dashboard.php<?php embUrl() ?>" method="POST">
			<input type="hidden" name="dummy">
		</form>
		<script>
			window.setTimeout(function (){	
				$('#redirect').submit();
			}, 3000);
		</script>
		<?php
	}


	public function jumbo($html)
	{
		?><div class="jumbo"><?php echo $html; ?></div>
		<?php
	}


	function simplePage($callback, $html='')
	{
	?><!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8"/>
			<title></title>
			<!-- bootstrap 4.x is supported. You can also use the bootstrap css 3.3.x versions -->
			<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
			<link href="resources/styles.css" media="all" rel="stylesheet" type="text/css"/>
			
		</head>
		<body<?php embBgd() ?>>
		<?php $this->$callback($html); ?>
		</body>

		<script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

		</html><?php	
	}


	public function profileForm($dd)
	{
		$vv = new FieldsViewer($dd);
	?><div class="<?php embContainer()?> pb-5">
		<?php $this->drawNavbar(); ?>
		<div class="<?php embContainer()?>">
		  <div class="row my-4">
		    <div class="col-md-8 col">		  
				<h3 class="mt-3">My Profile</h3>
				
				<form enctype="multipart/form-data" action="dashboard.php<?php embUrl() ?>" method="POST">
				  <div class="row">
					  <div class="form-group col-6">
						<label for="first_name">First Name</label>
						<input type="text" class="form-control" id="first_name" name="first_name"<?php $vv->drawValue('first_name'); ?>>
					  </div>
					  <div class="form-group col-6">
						<label for="last_name">Last Name</label>
						<input type="text" class="form-control" id="last_name" name="last_name"<?php $vv->drawValue('last_name'); ?>>
					  </div>
				  </div>
				  <div class="row">
					  <div class="form-group col-6">
						<label for="email">Email</label>
						<input type="text" class="form-control" id="email" name="email"<?php $vv->drawValue('email'); ?>>
						<small id="emailHelp" class="form-text text-muted">We use 'magic email' method for authentication. We'll never share your email with anyone else.</small>
					  </div>
					  <div class="form-group col-6">
						<label for="phone">Phone</label>
						<input type="text" class="form-control" id="phone" name="phone"<?php $vv->drawValue('phone'); ?>>
					  </div>
				  </div>
				  <hr/>
				  <input type="hidden" name="src" value="profile">
				  <div class="row">
					<div class="col-2">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				  </div>			
				</form>
			</div>
		  </div>
		</div>
	</div>
	<?php
	}


	function dashboard($dd)
	{
		//echo '<pre>';
		//print_r($dd);
	?><div class="<?php embContainer()?> pb-5">
		<?php $this->drawNavbar('dashboard'); ?>
		<div class="<?php embContainer()?>">
		  <div class="row my-3">
			<div class="col-md-9 col">
			  <form id="orgs" enctype="multipart/form-data" action="group.php<?php embUrl() ?>" method="POST">
				<p class="my-3"><a type="button" value="new" class="btn btn-primary edit-link" href="#">Register a Group</a></p>
				<?php if ($dd['orgs']) : ?>
					<h3 class="mt-5 mb-3">My Groups</h3>
					<table class="table table-borderless">
					  <thead>
						<th scope="col">Name</th><th scope="col">Last Modified</th><th scope="col">Requests</th><th scope="col">Actions</th>
					  </thead>
					  <tbody>
						<?php foreach ((array)$dd['orgs'] as $i=>$org) : ?>
							<tr>
							  <td><?php echo $org['name']; ?></td>
							  <td><?php echo $org['last_modified']; ?></td>
							  <td>
								<a href="#" class="posts-link" value="<?php echo $org['id']; ?>"><?php echo $org['posts_num'] ? $org['posts_num'] : 'Add' ?></a>
							  </td>
							  <td>
								<a href="#" class="posts-link" value="<?php echo $org['id']; ?>">Make a Request</a>
								<a href="#" class="edit-link" value="<?php echo $org['id']; ?>">Edit</a>
								<a href="#" class="del-link" value="<?php echo $org['id']; ?>">Delete</a>
							  </td>
							</tr>
						<?php endforeach; ?>
					  </tbody>
					</table>
					<hr/>
				<?php endif; ?>
				<input id="group_id" name="group_id" type="hidden" />
			  </form>
			  <form id="delete_org" enctype="multipart/form-data" action="dashboard.php<?php embUrl() ?>" method="POST">
				 <input id="delete_group_id" name="delete_group_id" type="hidden" />
				 <input name="src" value="delete_org" type="hidden" />
			  </form>
			</div>
		  </div>
				
		  <div class="row">
			<div class="col">				
				<h3 class="mt-1">My Profile</h3>
			</div>
		  </div>

		  <div class="<?php embContainer()?>">
			  <div class="row my-1">
				<div class="col">
					<small>Email</small>
					<h6><?php echo $dd['email'] ?></h6>
				</div>
			  </div>
			  <div class="row my-1">
				<div class="col-3">
					<small>First Name</small>
					<h6><?php echo $dd['first_name'] ?></h6>
				</div>
				<div class="col">
					<small>Last Name</small>
					<h6><?php echo $dd['last_name'] ?></h6>
				</div>
			  </div>
			  <div class="row">
				<div class="col-3">
					<small>Phone</small>
					<h6><?php echo $dd['phone'] ?></h6>
				</div>
			  </div>
			  <div class="row mt-2">
				<div class="col">
					<a type="button" class="btn btn-sm btn-primary" href="profile.php<?php embUrl() ?>">Edit</a>
				</div>
			  </div>
		  </div>
			
	
		</div>
	  </div>
	  <script>
		window.onload = function () {		
			$('a.edit-link').click(function () {
				$('#group_id').val($(this).attr('value'));
				$('#orgs').submit();
			});
			
			$('a.posts-link').click(function () {
				$('#group_id').val($(this).attr('value'));
				$('#orgs').attr('action', (
					$(this).text() == 'Make a Request' ? 'volunteerrequest.php<?php embUrl() ?>' : 'posts.php<?php embUrl() ?>'
				));
				$('#orgs').submit();
			});
			
			$('a.del-link').click(function () {
			var result = confirm("Are you sure you want to delete this group?");
			if (result) {
				$('#delete_group_id').val($(this).attr('value'));
				$('#delete_org').submit();
			}
		});

		}
	  </script>

	<?php
	}


	function drawNavbar($st=null)
	{
	?><nav class="navbar navbar-expand-lg navbar-light bg-light">
	  <a class="navbar-brand" href="#">Mutual Aid Group Portal</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	  </button>

	  <div class="collapse navbar-collapse" id="navbar">
		<ul class="navbar-nav mr-auto">
		  <li class="nav-item<?php echo ($st == 'dashboard') ? ' active' : ''; ?>">
			<a class="nav-link dashboard" href="<?php echo ($st == 'dashboard') ? '#' : 'dashboard.php'; ?><?php embUrl() ?>">Dashboard</a>
		  </li>
		</ul>
		<a class="nav-item nav-link logout" href="logout.php<?php embUrl() ?>">Log out</a>
	  </div>
	</nav>		
	<?php
	}


	function groupPage($dd)
	{
		$vv = new FieldsViewer($dd);
		//print_r($dd);
	?><div class="<?php embContainer()?> mb-2">
		<?php $this->drawNavbar(); ?>
    <form enctype="multipart/form-data" action="dashboard.php<?php embUrl() ?>" method="POST">
	  <div class="<?php embContainer()?> px-4 py-4">
	  
		  <div class="row">
			  <div class="form-group col">
				<h1>Mutual Aid Group</h1>
			  </div>
		  </div>
		  
		  <div class="row">
			  <div class="form-group col">
				<label for="name">Group Name</label>
				<input type="text" class="form-control" id="name" name="name"<?php $vv->drawValue('name'); ?>>
			  </div>
		  </div>

		  <div class="row">
			  <div class="form-group col">
				<label for="description">Description</label>
				<!-- <input type="text" class="form-control" id="description" name="description"<?php $vv->drawValue('description'); ?>> -->
				<textarea rows=2 type="text" class="form-control" id="description" name="description"><?php echo $vv->dd['description']; ?></textarea>
			  </div>
		  </div>

		  <div class="row">
			  <div class="form-group col">
				<label for="email">Email</label>
				<input type="text" class="form-control" id="email" name="email"<?php $vv->drawValue('email'); ?>>
			  </div>
			  
			  <div class="form-group col">
				<label for="website">Website</label>
				<input type="text" class="form-control" id="website" name="website"<?php $vv->drawValue('website'); ?>>
			  </div>
			  
			  <div class="form-group col">
				<label for="phone">Phone Number</label>
				<input type="text" class="form-control" id="phone" name="phone"<?php $vv->drawValue('phone'); ?>>
			  </div>
		  </div>

		  <div class="row">
			  <div class="form-group col">
				<label for="facebook">Facebook</label>
				<input type="text" class="form-control" id="facebook" name="facebook"<?php $vv->drawValue('facebook'); ?>>
			  </div>
			  
			  <div class="form-group col">
				<label for="instagram">Instagram</label>
				<input type="text" class="form-control" id="instagram" name="instagram"<?php $vv->drawValue('instagram'); ?>>
			  </div>
			  
			  <div class="form-group col">
				<label for="twitter">Twitter</label>
				<input type="text" class="form-control" id="twitter" name="twitter"<?php $vv->drawValue('twitter'); ?>>
			  </div>
		  </div>

		  <div class="row">
			  <div class="form-group col-6">
				<label for="neighborhoods">Neighborhoods</label>
				<div class="dd-outer form-control">
					<select class="selectpicker" id="neighborhoods" name="neighborhoods[]" multiple>
						<?php $vv->drawOptions('neighborhoods'); ?>
					</select>
				</div>
			  </div>
			  <div class="form-group col-6">
				<label for="geoscope">Geographical Scope</label>
				<div class="dd-outer form-control">
					<select class="selectpicker" id="geoscope" name="geoscope[]" multiple>
						<?php $vv->drawOptions('geoscope'); ?>
					</select>
				</div>
			  </div>
		  </div>
		  
		  <div class="row my-4">
			  <div class="form-group col-12">
				<label for="coverimg">Cover Image</label>
				<div class="file-loading">
					<input id="coverimg" type="file" name="coverimg[]" />
				</div>
			  </div>
		  </div>

		  
		  <hr>
		  <input type="hidden" name="user_id[]"<?php $vv->drawValue('user_id'); ?>>
		  <input type="hidden" name="src" value="organization">
		  <div class="row">
			<div class="col-3 text-center">
				<a type="button" class="btn btn-light" href="dashboard.php<?php embUrl() ?>">&lt; Back to Dashboard</a>
			</div>
			<div class="col-7"></div>
			<div class="col-2 text-center">
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>
		  </div>
	  </div>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {

        $("#coverimg").fileinput(
			Object.assign(fileinputMultiDefSettings, {
				allowedFileExtensions: ['jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff', 'doc', 'docx', 'pdf']
				<?php $vv->drawMultiInput('coverimg'); ?>
			})
		);
		
		//$('#date_founded').datepicker({});
    });
	

</script>	
	<?php
	}


	function groupPosts($pp)
	{
		$ss = ['Requested' => 'primary', 'Denied' => 'dark', 'Approved' => 'success', 'Under Review' => 'info', 'In Process' => 'info', 'Team Review' => 'info', 'Group Review' => 'info', 'Scheduled' => 'warning', 'Posted' => 'warning', ];
	?><div class="<?php embContainer()?> mb-5">
		<?php $this->drawNavbar(); ?>
		<div class="<?php embContainer()?> px-4 py-4">
		    <div class="row">
			  <div class="form-group col-2">
			  </div>
			  <div class="form-group col">
				<h1>Volunteers Requests</h1>
			  </div>
		    </div>
			  
			<div class="row">
				<?php if (!$pp) : ?>
					<div class="col-2">
					</div>
				<?php endif; ?>
				
				<div class="col-2">
					<form enctype="multipart/form-data" action="volunteerrequest.php<?php embUrl() ?>" method="POST">
						<input name="group_id" type="hidden" value="<?php echo $_POST['group_id'] ?>">
						<button type="submit" class="btn btn-primary">Request Volunteers</button>
					</form>
				</div>
				<div class="col">
					<?php foreach ($pp as $p) : ?>
						<div class="card mb-3">
						  <div class="card-body">
							  <div class="row my-1">
								<div class="col">
									<h3 class="mb-0"><?php echo $p['label'] ?></h3>
									<small><a title="Date of creation"><?php echo $p['created_at'] ?></a></small>
								</div>
								<div class="col-3 text-right">
								  <?php if ($p['status']) : ?>
									<h6 class="mb-0"><span class="badge badge-<?php echo $ss[$p['status']]; ?>"><?php echo $p['status'] ?></span></h6>
									<small><a title="Date of last review"><?php echo $p['modified_at'] ?></a></small>
								  <?php endif; ?>
								</div>
							  </div>
							  <div class="row mt-2">
								<div class="col">
									<p><?php echo $p['text'] ?></p>
								</div>
							  </div>
							  <div class="row mb-2">
							    <div class="col">
								<?php foreach ((array)$p['types'] as $t) : ?>
									<span class="badge badge-pill badge-light" style="background: #e5e5e5;"><?php echo $t ?></span>
								<?php endforeach; ?>
								</div>
							  </div>
							  
							  <div class="row mb-2 mt-3">
								<?php if ($p['start']) : ?>
									<div class="col-3">
										<small>Opportunity Start Date</small> 
										<h6><?php echo $p['start'] ?></h6>
									</div>
								<?php endif; ?>
								<?php if ($p['end']) : ?>
									<div class="col-3">
										<small>Opportunity End Date</small> 
										<h6><?php echo $p['end'] ?></h6>
									</div>
								<?php endif; ?>
								<?php if ($p['link']) : ?>
								  <div class="col-6">
									<small>Register Link</small>
									<h6><a href="<?php echo $p['link'] ?>" target="_blank"><?php echo $p['link'] ?></a></h6>
								  </div>
								<?php endif; ?>
							  </div>
							  <div class="row my-2">
								<?php if ($p['address']) : ?>
								  <div class="col-4">
									<small>Opportunity Address</small>
									<h6><?php echo $p['address'] ?></h6>
								  </div>
								<?php endif; ?>
								<?php if ($p['location']) : ?>
								  <div class="col-8">
									<small>Other Location Information</small>
									<h6><?php echo $p['location'] ?></h6>
								  </div>
								<?php endif; ?>
							  </div>


						  </div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<?php

	}


	function volunteerRequestPage($dd)
	{
		global $PostFormStaticSelects;
		//print_r($dd);
	?><div class="<?php embContainer()?> mb-2">
		<?php $this->drawNavbar(); ?>
    <form enctype="multipart/form-data" action="posts.php<?php embUrl() ?>" method="POST">
	  <div class="<?php embContainer()?> px-4 py-4">
	  
		  <div class="row">
			  <div class="form-group col">
				<h1>Volunteer Request</h1>
			  </div>
		  </div>
		  
		  <div class="row">
			  <div class="form-group col-8">
				<label for="label">Request Label</label>
				<input type="text" class="form-control" id="label" name="label">
				<small>This is the internal name for this request. This label will be used to help you identify this request. It will not be made public.</small>
			  </div>
			  
			  <div class="form-group col-4">
				<label for="group_name">Group</label>
				<input type="text" class="form-control" id="group_name" value="<?php echo $dd['name'] ?>" disabled>
			  </div>
		  </div>
		  
		  <div class="row">
			  <div class="form-group col">
				<label for="text">Text for Volunteer Blast</label>
				<textarea rows=2 type="text" class="form-control" id="text" name="text"></textarea>
				<small>Please send us the exact text you'd like us to send to our volunteers.</small>
			  </div>
		  </div>

		  <div class="row">
			  <div class="form-group col">
				<label for="link">Register Link</label>
				<input type="text" class="form-control" id="link" name="link">
				<small>How can people register to participate in your volunteer opportunity? Please give us a link to a form.</small>
			  </div>

			  <div class="form-group col">
				<label for="instagram">Task Types</label>
				<div class="dd-outer form-control">
					<select class="selectpicker" id="types" name="types[]" multiple>
						<?php foreach ($PostFormStaticSelects['types'] as $o) : ?>
							<option value="<?php echo $o ?>"><?php echo $o ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			  </div>
		  </div>
			  
		  <div class="row">
			  <div class="form-group col">
				<label for="address">Opportunity Address</label>
				<input type="text" class="form-control" id="address" name="address">
				<small>If this volunteer opportunity requires people show up at a specific address, please include it. This address will be shared publicly.</small>
			  </div>
			  
			  <div class="form-group col">
				<label for="location">Other Location Information</label>
				<input type="text" class="form-control" id="location" name="location">
				<small>If there are other location details, please let us know.</small>
			  </div>
		  </div>

		  <div class="row">
			  <div class="form-group col">
				<label for="start">Opportunity Start Date/Time</label>
				<input type="datetime-local" class="form-control" id="start" name="start">
				<small>When is this opportunity active? If it is currently active select today's date.</small>
			  </div>
			  
			  <div class="form-group col">
				<label for="end">Opportunity End Date/Time</label>
				<input type="datetime-local" class="form-control" id="end" name="end">
				<small>When is this opportunity no longer active? If you don't know, leave it blank.</small>
			  </div>
			  
		  </div>
		  
		  <hr>
		  <input type="hidden" name="group[]" value="<?php echo $dd['id']; ?>">
		  <input type="hidden" name="status" value="Requested">
		  <input type="hidden" name="group_id" value="<?php echo $dd['id']; ?>">
		  <input type="hidden" name="src" value="vrequest">
		  <div class="row">
			<div class="col-3 text-center">
				<a type="button" class="btn btn-light" href="dashboard.php<?php embUrl() ?>">&lt; Back to Dashboard</a>
			</div>
			<div class="col-7"></div>
			<div class="col-2 text-center">
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>
		  </div>
	  </div>
    </form>
</div>
	<?php
	}


	function drawThankYou()
	{
	?><div class="jumbo">
		Thank you for submitting the form!
		<br/>
		<small><a href="vendor.php<?php embUrl() ?>">Sign in again</a></small>
	</div><?php
	}

	function drawWriteError()
	{
	?><div class="jumbo">Something got wrong...</div><?php
	}

	public function session()
	{
		if (ADMIN_SESSION_VERBOSE)
		{	
		?>            
				<pre style="color: #ddd;"><?php print_r($this->formatSession($_SESSION)); ?></pre>
		<?php
		}
	}


	public function intercom($dd)
	{
	?>            
		<script>
		  window.intercomSettings = {
			app_id: "<?php echo INTERCOM_KEY ?>",
			name: "<?php echo $dd['name'] ?>", // Full name
			email: "<?php echo $dd['email'] ?>", // Email address
			created_at: "<?php echo strtotime($dd['reg_date']) ?>" // Signup date as a Unix timestamp
		  };
		</script>

		<script>
		// We pre-filled your app ID in the widget URL: 'https://widget.intercom.io/widget/wib83ryk'
		(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/<?php echo INTERCOM_KEY ?>';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
		</script>
	<?php
	}


	public function formatSession($s)
	{
		$out = [];
		foreach ($s as $k=>$v)
		{
			if (is_array($v))
				if (count($v) > 200)
					$out[$k] = "[{$v[0]},{$v[1]},{$v[2]} - " . count($v) . ' values...]';
				else
					$out[$k] = $this->formatSession($v);
			else
				$out[$k] = $v;
		}
		return $out;
	}


	// --------- srv -------------------------------

	public function headers($modal=false)
	{
	?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title></title>
    
	<!-- bootstrap 4.x is supported. You can also use the bootstrap css 3.3.x versions -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
	<!-- if using RTL (Right-To-Left) orientation, load the RTL CSS file after fileinput.css by uncommenting below -->
	<!-- link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/css/fileinput-rtl.min.css" media="all" rel="stylesheet" type="text/css" /-->
	<!-- the font awesome icon library if using with `fas` theme (or Bootstrap 4.x). Note that default icons used in the plugin are glyphicons that are bundled only with Bootstrap 3.x. -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" crossorigin="anonymous">
	<link href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" rel="stylesheet">
	<link href="krajee/themes/explorer-fas/theme.css" media="all" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
	<link href="resources/styles.css" media="all" rel="stylesheet" type="text/css"/>
</head>
<body<?php embBgd() ?>>

	<?php
	}



	public function footers()
	{
	?></body>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<!-- piexif.min.js is needed for auto orienting image files OR when restoring exif data in resized images and when you
	wish to resize images before upload. This must be loaded before fileinput.min.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/plugins/piexif.min.js" type="text/javascript"></script>
<!-- sortable.min.js is only needed if you wish to sort / rearrange files in initial preview. 
	This must be loaded before fileinput.min.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/plugins/sortable.min.js" type="text/javascript"></script>
<!-- purify.min.js is only needed if you wish to purify HTML content in your preview for 
	HTML files. This must be loaded before fileinput.min.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/plugins/purify.min.js" type="text/javascript"></script>
<!-- popper.min.js below is needed if you use bootstrap 4.x (for popover and tooltips). You can also use the bootstrap js 
   3.3.x versions without popper.min.js. -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<!-- the main fileinput plugin file -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/fileinput.min.js"></script>
<script src="krajee/themes/explorer-fas/theme.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
<script src="resources/script.js" type="text/javascript"></script>


</html><?php
	}
}


class FieldsViewer
{
	public $dd;
	
	function __construct($dd)
	{
		$this->dd = $dd;
	}
	
	function drawValue($f)
	{
		$v = $this->dd[$f];
		if ($v !== null)
			echo " value=\"{$v}\"";
	}

	function drawOptions($f)
	{
		$vv = $this->dd[$f];
		foreach ((array)$vv as $v)
			echo sprintf('<option value="%s"%s>%s</option>', $v['v'], $v['selected'] ? ' selected' : '', $v['text']);
	}

	function drawMultiInput($f)
	{
		$vv = $this->dd[$f];
		if ($vv['initialPreview'])
			echo sprintf(',initialPreview: %s, initialPreviewConfig: %s', json_encode($vv['initialPreview']), json_encode($vv['initialPreviewConfig']));
	}

	function drawDisabledIfNew()
	{
		if ($this->dd['isNew'])
			echo ' disabled';
	}
	
	function hideIfNew($do=false)
	{
		echo ($this->dd['isNew'] xor !$do) ? ' hidden' : '';
	}
}

function embContainer()
{
	echo isset($_GET['embedded']) ? 'container-fluid' : 'container';
}

function embUrl()
{
	echo isset($_GET['embedded']) ? '?embedded' : '';
}

function embBgd()
{
	echo isset($_GET['embedded']) ? ' class="embedded"' : '';
}