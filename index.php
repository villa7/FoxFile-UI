<?php
//session_start();
include 'includes/cfgvars.php';
//if (isset ($_SESSION['foxfile_uid'])) header ("Location: browse");
/*if (!isset ($_SESSION['access_token'])) header ("Location: login");*/
?>
<!DOCTYPE html>
<html lang="en-US">
<!--
                                                              
   ad88                             ad88  88  88              
  d8"                              d8"    ""  88              
  88                               88         88              
MM88MMM  ,adPPYba,  8b,     ,d8  MM88MMM  88  88   ,adPPYba,  
  88    a8"     "8a  `Y8, ,8P'     88     88  88  a8P_____88  
  88    8b       d8    )888(       88     88  88  8PP"""""""  
  88    "8a,   ,a8"  ,d8" "8b,     88     88  88  "8b,   ,aa  
  88     `"YbbdP"'  8P'     `Y8    88     88  88   `"Ybbd8"'  
                                                              

	Foxfile : <?php echo basename(__FILE__); ?> 
	Version <?php echo $foxfile_version; ?> 
	Copyright (C) 2016 Theodore Kluge
	https://tkluge.net

-->
<head>
	<title>FoxFile</title>
	<meta charset="utf-8" />
	<meta author="tkluge" />
	<link rel="stylesheet" href="css/login.css" />
	<link href="css/materialdesignicons.min.css" media="all" rel="stylesheet" type="text/css" />
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'>
	<link rel="icon" type="image/ico" href="img/favicon.png">
	<meta name="viewport" content="initial-scale=1, width=device-width, maximum-scale=1, minimum-scale=1, user-scalable=no">
</head>
<body>
<main class="float-2">
	<span class="back" onclick="removeUser()"><i class="mdi mdi-arrow-left"></i></span>
	<header class="header float" id="header-main">
		<img class="float" src="img/default_avatar.png" alt="profile picture" />
		<span>Sign in to FoxFile</span>
	</header>
	<section class="content">
	    <form name="login" action="login.php" method="post" onsubmit="return false;">
	    	<section class="slider">
				<div class="inputbar nosel">
					<label class="userlabel">
						<input name="email" class="userinfo" id="email" type="text" required>
						<span class="placeholder-userinfo nosel">Enter your email</span>
						<hr class="input-underline" />
						<div class="error error-email"><div class="error-message">Invalid email address</div></div>
					</label>
				</div>
				<div class="inputbar nosel">
					<label class="userlabel">
						<input name="password" class="userinfo" id="userpass" type="password" required>
						<span class="placeholder-userinfo nosel">Password</span>
						<hr class="input-underline" />
						<div class="error error-pass"><div class="error-message">Incorrect password</div></div>
					</label>
				</div>
			</section>
			</div>
			<a href="register" class="new-account">Need an account?</a>
	        <button class="btn btn-submit" type="button" onclick="checkEmail()">Next<link class="rippleJS" /></button>
	        <a href="recover" class="forgot-password"><!-- Forgot password? --></a>
	        <button class="btn btn-submit" type="button" onclick="sub()">Sign In<link class="rippleJS" /></button>
	    </form>
	</section>
</main>
<?php include './includes/footer.html'; ?>
<script type="text/javascript" src="//code.jquery.com/jquery-2.1.4.min.js"></script>
<!-- <script type="text/javascript" src="js/md5.js"></script> -->
<script type="text/javascript" src="js/ripple.js"></script>
<script type="text/javascript" src="js/forge.min.js"></script>
<?php if (isset($_GET['demo']) && false) { ?>
<script type="text/javascript">
$('#email').val('test@test.test');
$('#userpass').val('test');
</script>
<?php } ?>
<script type="text/javascript">
    $('input.userinfo').change(function() {
        $(this).attr('empty', ($(this).val() != '') ? 'false' : 'true');
    });
    var stage = 0;
    var pressed = false;
    $(document).keydown(function(e) {
		if (!pressed && e.keyCode == 13) { //enter
			pressed = true;
            if (stage == 0) {
            	checkEmail();
            } else {
            	sub();
            }
		}
		if (!pressed && e.keyCode == 9) { //tab
			pressed = true;
            if (stage == 0) {
            	e.preventDefault();
            	checkEmail();
            }
		}
	});
	$(document).keyup(function(e) {
		if (e.keyCode == 13) { //enter
			pressed = false;
		}
		if (e.keyCode == 9) {
			pressed = false;
		}
	});
	function md5(str) {
		var md = forge.md.md5.create();
		md.update(str);
		return md.digest().toHex();
	}
	function sha512(str) {
		var md = forge.md.sha512.create();
		md.update(str);
		return md.digest().toHex();
	}
    function checkEmail() {
    	if (/[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/g.test($('#email').val())) {
    		$('main').addClass('active');
    		$('.error-email').removeClass('active');
    		$('#header-main img').attr('src', '//www.gravatar.com/avatar/'+md5($('#email').val())+'?d=retro&r=r');
    		$('#header-main span').text($('#email').val());
    		setTimeout(function() {
    			$('#userpass').focus();
    		}, 450);
    		//setCookie('useremail', $('#email').val(), 7);
    		localStorage.setItem('email', $('#email').val());
    		stage = 1;
    	} else {
    		if ($('#email').val() != '') {
                $('.error-email').addClass('active');
            }
    	}
    }
    function restart() {
    	$('main').removeClass('active');
    	$('.error-email').removeClass('active');
    	$('#header-main span').text('Sign in to FoxFile');
    	$('.email').focus();
    	stage = 0;
    }
    function sub() {
		$.ajax({
            type: "POST",
            url: "./api/auth/login",
            data: {
				useremail: $('#email').val(),
				userpass: sha512($('#userpass').val())
			},
            success: function(result, s, x) {
            	console.log(result);
                var json = JSON.parse(result);

                var token = json['key'];
                //setCookie('api_key', token, 7);
                localStorage.setItem('api_key', token);
                localStorage.setItem('basekey', sha512(sha512($('#userpass').val())));

                window.location.href = "./browse";                
            },
            error: function(request, error) {
                if (request.status == 401 || request.status == 404) {
                	$('.error-pass').addClass('active').text('Invalid email/pass');
                } else if (request.status == 500) {
                	$('.error-pass').addClass('active').text('Database error');
                }
            }
        });
    }
	function removeUser() {
		//setCookie('useremail', '', 7);
		localStorage.removeItem('email');
		var user = $('#email').val();
        $('#email').attr('empty', (user != '') ? 'false' : 'true');
        var pass = $('#userpass').val();
        $('#userpass').attr('empty', (pass != '') ? 'false' : 'true');
		restart();
	}
	$(document).ready(function() {

		if (localStorage.getItem('api_key')) {
			$.ajax({
	            type: "POST",
	            url: "./api/auth/login",
	            data: {
					api_key: localStorage.getItem('api_key')
				},
	            success: function(result, s, x) {
	            	//console.log(result);
	                window.location.href = "./browse";                
	            },
	            error: function(request, error) {
	            	start();
	                if (request.status == 401 || request.status == 404) {
	                	$('.error-pass').addClass('active').text('Invalid auth key - log in again.');
	                	localStorage.removeItem('api_key');
	                } else if (request.status == 500) {
	                	$('.error-pass').addClass('active').text('Database error');
	                }
	            }
	        });
		} else {
			start();			
	    }
	});
	function start() {
		//var u = getCookie("useremail");
		var u = localStorage.getItem('email');
		if (u != null && u != "") {
		    using_cookie = true;
		    $('#email').val(u);
		    checkEmail();
		}
		 user = $('#email').val();
	    $('#email').attr('empty', (user != '') ? 'false' : 'true');
	    var pass = $('#userpass').val();
	    $('#userpass').attr('empty', (pass != '') ? 'false' : 'true');
	}
    </script>
</body>
</html>