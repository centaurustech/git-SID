<?php
	/* initial preps and includes */
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	if(function_exists('set_magic_quotes_runtime')) @set_magic_quotes_runtime(0);
	$curr_dir = dirname(__FILE__);
	include("$curr_dir/settings-manager.php");
	include("$curr_dir/defaultLang.php");
	include("$curr_dir/language.php");
	if(!extension_loaded('mysql')){
		?><div class="alert alert-danger">ERROR: PHP is not configured to connect to MySQL on this machine. Please see <a href=http://www.php.net/manual/en/ref.mysql.php>this page</a> for help on how to configure MySQL.</div><?php
		exit;
	}



	/*
		Determine execution scenario ...
		this script is called in 1 of 5 scenarios:
			1. to display the setup instructions no $_GET['show-form']
			2. to display the setup form $_GET['show-form'], no $_POST['test'], no $_POST['submit']
			3. to test the db info, $_POST['test'] no $_POST['submit']
			4. to save setup data, $_POST['submit']
			5. to show final success message, $_GET['finish']
		below here, we determine which scenario is being called
	*/
	$submit = $test = $form = $finish = false; 
	(isset($_POST['submit'])   ? $submit = true :
	(isset($_POST['test'])     ?   $test = true :
	(isset($_GET['show-form']) ?   $form = true :
	(isset($_GET['finish'])    ? $finish = true :
		false))));


	/* some function definitions */
	function undo_magic_quotes($str){
		return (get_magic_quotes_gpc() ? stripslashes($str) : $str);
	}

	function isEmail($email){
		if(preg_match('/^([*+!.&#$�\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', $email)){
			return $email;
		}else{
			return FALSE;
		}
	}


	/* if config file already exists, no need to continue */
	if(!$finish && detect_config(false)){
		@header('Location: index.php');
		exit;
	}



	/* include page header, unless we're testing db connection (ajax) */
	if(session_id()){ @session_write_close(); }
	@session_name('SID');
	@session_start();
	$_REQUEST['Embedded'] = 1; /* to prevent displaying the navigation bar */
	$x = new StdClass;
	$x->TableTitle = $Translation['Setup Data']; /* page title */
	if(!$test) include_once("$curr_dir/header.php");

	if($submit || $test){

		/* receive posted data */
		if($submit){
			$username = strtolower($_POST['username']);
			$email = isEmail($_POST['email']);
			$password = $_POST['password'];
			$confirmPassword = $_POST['confirmPassword'];
		}
		$db_name = str_replace('`', '', $_POST['db_name']);
		$db_password = $_POST['db_password'];
		$db_server = $_POST['db_server'];
		$db_username = $_POST['db_username'];

		/* validate data */
		$errors = array();
		if($submit){
			if(strlen($username) < 4){
				$errors[] = $Translation['username empty'];
			}
			if(strlen($password) < 4 || trim($password) != $password){
				$errors[] = $Translation['password invalid'];
			}
			if($password != $confirmPassword){
				$errors[] = $Translation['password no match'];
			}
			if(!$email){
				$errors[] = $Translation['email invalid'];
			}
		}

		/* test database connection */
		if(!($connection = @mysql_connect($db_server, $db_username, $db_password))){
			$errors[] = $Translation['Database connection error'];
		}
		if($connection !== false && !@mysql_select_db($db_name)){
			// attempt to create the database
			if(!@mysql_query("CREATE DATABASE IF NOT EXISTS `$db_name`")){
				$errors[] = @mysql_error();
			}elseif(!@mysql_select_db($db_name)){
				$errors[] = @mysql_error();
			}
		}

		/* in case of validation errors, output them and exit */
		if(count($errors)){
			if($test){
				echo 'ERROR!';
				exit;
			}

			?>
				<div class="row">
					<div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
						<h2 class="text-danger"><?php echo $Translation['The following errors occured']; ?></h2>
						<div class="alert alert-danger"><ul><li><?php echo implode('</li><li>', $errors); ?></li></ul></div>
						<a class="btn btn-default btn-lg vspacer-lg" href="#" onclick="history.go(-1); return false;"><i class="glyphicon glyphicon-chevron-left"></i> <?php echo $Translation['< back']; ?></a>
					</div>
				</div>
			<?php
			include_once("$curr_dir/footer.php");
			exit;
		}

		/* if db test is successful, output success message and exit */
		if($test){
			echo 'SUCCESS!';
			exit;
		}

		/* create database tables */
		$silent = false;
		include("$curr_dir/updateDB.php");


		/* attempt to save db config file */
		$new_config = array(
			'dbServer' => undo_magic_quotes($db_server),
			'dbUsername' => undo_magic_quotes($db_username),
			'dbPassword' => undo_magic_quotes($db_password),
			'dbDatabase' => undo_magic_quotes($db_name),
			'adminConfig' => array(
				'adminUsername' => undo_magic_quotes($username),
				'adminPassword' => md5($password),
				'notifyAdminNewMembers' => 0,
				'defaultSignUp' => 1,
				'anonymousGroup' => 'anonymous',
				'anonymousMember' => 'guest',
				'groupsPerPage' => 10,
				'membersPerPage' => 10,
				'recordsPerPage' => 10,
				'custom1' => 'Full Name',
				'custom2' => 'Address',
				'custom3' => 'City',
				'custom4' => 'State',
				'MySQLDateFormat' => '%d/%m/%Y',
				'PHPDateFormat' => 'j/n/Y',
				'PHPDateTimeFormat' => 'd/m/Y, h:i a',
				'senderName' => 'Membership management',
				'senderEmail' => $email,
				'approvalSubject' => 'Your membership is now approved',
				'approvalMessage' => "Dear member,\n\nYour membership is now approved by the admin. You can log in to your account here:\nhttp://{$_SERVER['HTTP_HOST']}" . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "\n\nRegards,\nAdmin" 
			)
		);

		$save_result = save_config($new_config);
		if($save_result !== true){
			// display instructions for manually creating them if saving not successful
			$folder_path_formatted = '<strong>' . dirname(__FILE__) . '</strong>';
			?>
				<div class="row">
					<div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
						<img src="logo.png" style="margin-bottom: 15px;" />
						<div class="alert alert-danger"><?php echo $Translation['error:'] . ' ' . $save_result['error']; ?></div>
						<?php printf($Translation['failed to create config instructions'], $folder_path_formatted); ?>
						<pre style="border: solid 2px darkgreen; padding: 10px; color: lightgreen; background-color: black; overflow: scroll; font-size: large;"><?php echo htmlspecialchars($save_result['config']); ?></pre>
					</div>
				</div>
			<?php
			exit;
		}


		/* sign in as admin if everything went ok */
		$_SESSION['adminUsername'] = $username;
		$_SESSION['memberID'] = $username;
		$_SESSION['memberGroupID'] = 2; // this should work fine in most cases



		/* redirect to finish page using javascript */
		?>
		<script>
			jQuery(function(){
				var a = window.location.href + '?finish=1';

				if(jQuery('div[class="text-danger"]').length){
					jQuery('body').append('<p class="text-center"><a href="' + a + '" class="btn btn-default vspacer-lg"><?php echo addslashes($Translation['Continue']); ?> <i class="glyphicon glyphicon-chevron-right"></i></a></p>');
				}else{
					jQuery('body').append('<div id="manual-redir">If not redirected automatically, <a href="<?php echo basename(__FILE__); ?>?finish=1">click here</a>!</div>');
					window.location = a;
				}
			});
		</script>
		<?php

		// exit
		include_once("$curr_dir/footer.php");
		exit;
	}elseif($finish){
		detect_config();
		@include("$curr_dir/config.php");
	}
?>

	<div class="row"><div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
	<?php if(!$form && !$finish){ /* show instructions */ ?>

		<div id="intro1" class="instructions">
			<p><img src="logo.png"></p>
			<p><?php echo $Translation['setup intro 1']; ?></p>
			<br><br>
			<p class="text-center"><button class="btn btn-default" id="show-intro2" type="button"><?php echo $Translation['Continue']; ?> <i class="glyphicon glyphicon-chevron-right"></i></button></p>
		</div>

		<div id="intro2" class="instructions" style="display: none;">
			<p><img src="logo.png"></p>
			<p><?php echo $Translation['setup intro 2']; ?></p>
			<br><br>
			<p class="text-center"><button class="btn btn-success btn-lg" id="show-login-form" type="button"><i class="glyphicon glyphicon-ok"></i> <?php echo $Translation['Lets go']; ?></button></p>
		</div>

	<?php }elseif($form){ /* show setup form */ ?>

		<div class="page-header"><h1><?php echo $Translation['Setup Data']; ?></h1></div>

		<form method="post" action="<?php echo basename(__FILE__); ?>" onSubmit="return jsValidateSetup();" id="login-form" style="display: none;">
			<fieldset id="database" class="form-horizontal">
				<legend><?php echo $Translation['Database Information']; ?></legend>

				<div class="form-group">
					<label for="db_server" class="control-label col-sm-4"><?php echo $Translation['mysql server']; ?></label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="text" class="form-control" id="db_server" name="db_server" placeholder="<?php echo htmlspecialchars($Translation['mysql server']); ?>" value="localhost">
							<span class="input-group-btn">
								<button data-toggle="collapse" tabindex="-1" data-target="#db_server-help" class="btn btn-info" type="button"><i class="glyphicon glyphicon-info-sign"></i></button>
							</span>
						</div>
						<span class="help-block collapse" id="db_server-help"><?php echo $Translation['db_server help']; ?></span>
					</div>
				</div>

				<div class="form-group">
					<label for="db_name" class="control-label col-sm-4"><?php echo $Translation['mysql db']; ?></label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="text" class="form-control" id="db_name" name="db_name" placeholder="<?php echo htmlspecialchars($Translation['mysql db']); ?>">
							<span class="input-group-btn">
								<button data-toggle="collapse" tabindex="-1" data-target="#db_name-help" class="btn btn-info" type="button"><i class="glyphicon glyphicon-info-sign"></i></button>
							</span>
						</div>
						<span class="help-block collapse" id="db_name-help"><?php echo $Translation['db_name help']; ?></span>
					</div>
				</div>

				<div class="form-group">
					<label for="db_username" class="control-label col-sm-4"><?php echo $Translation['mysql username']; ?></label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="text" class="form-control" id="db_username" name="db_username" placeholder="<?php echo htmlspecialchars($Translation['mysql username']); ?>">
							<span class="input-group-btn">
								<button data-toggle="collapse" tabindex="-1" data-target="#db_username-help" class="btn btn-info" type="button"><i class="glyphicon glyphicon-info-sign"></i></button>
							</span>
						</div>
						<span class="help-block collapse" id="db_username-help"><?php echo $Translation['db_username help']; ?></span>
					</div>
				</div>

				<div class="form-group">
					<label for="db_password" class="control-label col-sm-4"><?php echo $Translation['mysql password']; ?></label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="password" class="form-control" id="db_password" name="db_password" placeholder="<?php echo htmlspecialchars($Translation['mysql password']); ?>">
							<span class="input-group-btn">
								<button data-toggle="collapse" tabindex="-1" data-target="#db_password-help" class="btn btn-info" type="button"><i class="glyphicon glyphicon-info-sign"></i></button>
							</span>
						</div>
						<span class="help-block collapse" id="db_password-help"><?php echo $Translation['db_password help']; ?></span>
					</div>
				</div>

				<div id="db_test" class="alert" style="display: none;"></div>
			</fieldset>

			<fieldset class="form-horizontal" id="inputs">
				<legend><?php echo $Translation['Admin Information']; ?></legend>

				<div class="row form-group">
					<label for="username" class="control-label col-sm-4"><?php echo $Translation['username']; ?></label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="text" required class="form-control" id="username" name="username" placeholder="<?php echo htmlspecialchars($Translation['username']); ?>">
							<span class="input-group-btn">
								<button data-toggle="collapse" tabindex="-1" data-target="#username-help" class="btn btn-info" type="button"><i class="glyphicon glyphicon-info-sign"></i></button>
							</span>
						</div>
						<span class="help-block collapse" id="username-help"><?php echo $Translation['username help']; ?></span>
					</div>
				</div>

				<div class="form-group">
					<label for="email" class="control-label col-sm-4"><?php echo $Translation['email']; ?></label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="text" required class="form-control" id="email" name="email" placeholder="<?php echo htmlspecialchars($Translation['email']); ?>">
							<span class="input-group-btn">
								<button data-toggle="collapse" tabindex="-1" data-target="#email-help" class="btn btn-info" type="button"><i class="glyphicon glyphicon-info-sign"></i></button>
							</span>
						</div>
						<span class="help-block collapse" id="email-help"><?php echo $Translation['email help']; ?></span>
					</div>
				</div>
			</fieldset>

			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label for="password" class="control-label"><?php echo $Translation['password']; ?></label>
						<div class="input-group">
							<input type="password" required class="form-control" id="password" name="password" placeholder="<?php echo htmlspecialchars($Translation['password']); ?>">
							<span class="input-group-btn">
								<button data-toggle="collapse" tabindex="-1" data-target="#password-help" class="btn btn-info" type="button"><i class="glyphicon glyphicon-info-sign"></i></button>
							</span>
						</div>
						<span class="help-block collapse" id="password-help"><?php echo $Translation['password help']; ?></span>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="confirmPassword" class="control-label"><?php echo $Translation['confirm password']; ?></label>
						<input type="password" required class="form-control" id="confirmPassword" name="confirmPassword" placeholder="<?php echo htmlspecialchars($Translation['confirm password']); ?>">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-offset-3 col-sm-6">
					<button class="btn btn-primary btn-lg btn-block" value="submit" id="submit" type="submit" name="submit"><?php echo $Translation['Submit']; ?></button>
				</div>
			</div>
		</form>

	<?php }elseif($finish){ ?>

		<?php
			// make sure this is an admin
			if(!$_SESSION['adminUsername']){
				?>
				<div id="manual-redir">If not redirected automatically, <a href="index.php">click here</a>!</div>
				<script>
					window.location = 'index.php';
				</script>
				<?php
				exit;
			}
		?>

		<div class="instructions">
			<img src="logo.png" />
			<?php echo $Translation['setup finished']; ?>
			<ul id="next-actions">
				<li><a href="index.php"><b><?php echo $Translation['setup next 1']; ?></b></a></li>
				<li><a href="admin/pageUploadCSV.php"><?php echo $Translation['setup next 2']; ?></a></li>
				<li><a href="admin/pageHome.php"><?php echo $Translation['setup next 3']; ?></a></li>
			</ul>
		</div>

	<?php } ?>
	</div></div>

	<script>
	<?php if(!$form && !$finish){ ?>
		document.observe("dom:loaded", function() {
			$('show-intro2').observe('click', function(){
				$('intro1').hide();
				$('intro2').appear({ duration: 2 });
			});
			$('show-login-form').observe('click', function(){
				var a = window.location.href;
				window.location = a + '?show-form=1';
			});
		});
	<?php }elseif($form){ ?>
		document.observe("dom:loaded", function() {
			/* password strength feedback */
			$('password').observe('keyup', function(){
				ps = passwordStrength($F('password'), $F('username'));

				if(ps == 'strong'){
					$('password').removeClassName('redBG').removeClassName('yellowBG').addClassName('greenBG');
					$('password').title = '<?php echo htmlspecialchars($Translation['Password strength: strong']); ?>';
				}else if(ps == 'good'){
					$('password').removeClassName('redBG').removeClassName('greenBG').addClassName('yellowBG');
					$('password').title = '<?php echo htmlspecialchars($Translation['Password strength: good']); ?>';
				}else{
					$('password').removeClassName('greenBG').removeClassName('yellowBG').addClassName('redBG');
					$('password').title = '<?php echo htmlspecialchars($Translation['Password strength: weak']); ?>';
				}
			});

			/* inline feedback of confirm password */
			$('confirmPassword').observe('keyup', function(){
				if($F('confirmPassword') != $F('password') || !$F('confirmPassword').length){
					$('confirmPassword').removeClassName('greenBG').addClassName('redBG');
				}else{
					$('confirmPassword').removeClassName('redBG').addClassName('greenBG');
				}
			});

			/* inline feedback of email */
			$('email').observe('change', function(){
				if(validateEmail($F('email'))){
					$('email').removeClassName('redBG').addClassName('greenBG');
				}else{
					$('email').removeClassName('greenBG').addClassName('redBG');
				}
			});

			$('login-form').appear({ duration: 2 });
			setTimeout("$('db_name').focus();", 2006);

			$('db_name').observe('change', function(){ db_test(); });
			$('db_password').observe('change', function(){ db_test(); });
			$('db_server').observe('change', function(){ db_test(); });
			$('db_username').observe('change', function(){ db_test(); });
		});

		/* validate data before submitting */
		function jsValidateSetup(){
			var p1 = $F('password');
			var p2 = $F('confirmPassword');
			var user = $F('username');
			var email = $F('email');

			/* passwords not matching? */
			if(p1 != p2){
				modal_window({ message: '<div class="alert alert-danger"><?php echo addslashes($Translation['password no match']); ?></div>', title: "<?php echo addslashes($Translation['error:']); ?>", close: function(){ jQuery('#confirmPassword').focus(); } });
				return false;
			}

			/* user exists? */
			if($('usernameNotAvailable').visible()){
				modal_window({ message: '<div class="alert alert-danger"><?php echo addslashes($Translation['username exists']); ?></div>', title: "<?php echo addslashes($Translation['error:']); ?>", close: function(){ jQuery('#username').focus(); } });
				return false;
			}

			return true;
		}

		/* test db info */
		var db_test_in_progress = false;
		function db_test(){
			if(db_test_in_progress) return;

			if($F('db_name').length && $F('db_username').length && $F('db_server').length && $$('#db_password:focus') == ''){
				setTimeout(function(){
					if(db_test_in_progress) return;

					new Ajax.Request(
						'<?php echo basename(__FILE__); ?>', {
							method: 'post',
							parameters: {
								db_name: $F('db_name'),
								db_server: $F('db_server'),
								db_password: $F('db_password'),
								db_username: $F('db_username'),
								test: 1
							},
							onCreate: function() {
								db_test_in_progress = true;
							},
							onSuccess: function(resp) {
								if(resp.responseText == 'SUCCESS!'){
									$('db_test').removeClassName('alert-danger').addClassName('alert-success').update('<?php echo addslashes($Translation['Database info is correct']); ?>').appear();
								}else if(resp.responseText.match(/^ERROR!/)){
									$('db_test').removeClassName('alert-success').addClassName('alert-danger').update('<?php echo addslashes($Translation['Database connection error']); ?>').show();
									Effect.Shake('db_test');
								}
							},
							onComplete: function() {
								db_test_in_progress = false;
							}
						}
					);
				}, 1000);
			}
		}
	<?php } ?>
	</script>

	<style>
		legend{ font-weight: bold; font-size: large; background-color: White; border-radius: 4px; border:2px solid #316B40; padding: 3px 20px; }
		#usernameAvailable,#usernameNotAvailable{ cursor: pointer; }
		.greenBG{ border-color: Green !important; background-color: LightGreen !important; }
		.yellowBG{ border-color: Gold !important; background-color: LightYellow !important; }
		.redBG{ border-color: Red !important; background-color: LighRed !important; }

		.instructions{
			padding: 30px;
			margin: 40px auto;
			border: solid 1px silver;
			border-radius: 4px;
			background-color: White;
		}
		.instructions img{ display: block; margin: 0 auto 40px; }
		.instructions .buttons{
			display: block;
			height: 1px;
			margin: 30px auto;
		}
		body{ background-color: #f8f8f8; }
		ul#next-actions li a{ text-decoration: none; color: navy; }
		ul#next-actions li a:hover{ text-decoration: underline; }
		ul#next-actions li { margin: 20px 0; }
	</style>

<?php include_once("$curr_dir/footer.php"); ?>

