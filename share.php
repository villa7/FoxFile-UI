<?php
session_start();
require ('includes/config.php');
if(!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
if(!isset($_SESSION['access_level'])) $_SESSION['access_level'] = 0;
if(!isset($_SESSION['uhd'])) $_SESSION['uhd'] = 'USER_HOME_DIR';
if(!isset($_SESSION['username'])) $_SESSION['username'] = 'USERNAME';

if(!isset($_GET['id'])) $_GET['id'] = "root";

$uid = $_SESSION['uid'];
$uhd = $_SESSION['uhd'];
$alvl = $_SESSION['access_level'];
$uname = $_SESSION['username'];
if (isset($_GET['id'])) $id = $_GET['id'];
//error_reporting($show_errors);//remove for debug
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$starttime = $time;
$hasUser = true;

if ($alvl < 1) {
	$hasUser = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<!--
 * browse.php - FoxFile
 * (C) Theodore Kluge 2014-2015
 * http://kluge.ninja
 -->
<head>
    <title><?php echo $title ?></title>
    <?php require('includes/header.php'); ?>
    <style type="text/css">
    <?php if (!$allowsharing) { ?>
    	li[cmm='share'] {
    		display: none;
    	}
    <?php } ?>
    .clickmenu li[cmm="rename"],
	.clickmenu li[cmm="delete"],
	.clickmenu li[cmm="share"],
	.clickmenu li[cmm="upload"],
	.clickmenu li[cmm="move"],
	.clickmenu li[cmm="newfolder"] {
		display: none;
	}
	.bar .menubar-title .menubar-action-btn .btn-new-folder,
	.bar .menubar-title .menubar-action-btn .btn-upload,
	.bar .menubar-title .menubar-action-btn .btn-rename,
	.bar .menubar-title .menubar-action-btn .btn-delete,
	.bar .menubar-title .menubar-action-btn .btn-move {
		display: none;
	}
	.menubar-content-desc:after {
		content: none;
	}
	.btn-login {
		padding: 10px;
		width: 140px;
		background: rgba(255,255,255,.2);
		color: #aaa;
		border: none;
	}
	.btn-login:hover {
		background: rgba(255,255,255,.1);
	}
	.promo {
		text-align: center;
		padding: 50px;
		padding-top: 50%;
		padding-bottom: 0;
	}
	.promo div {
		font-family: 'quicksandlight';
	}
	.promo button {
		width: 100%;
		position: relative;
		margin-bottom: 20px;
	}
	.menubar-title-bar-headers {
    	display: none !important;
    }
    </style>

</head>
<body>
<!-- <div class="spinner" id="spinner-pre" style="display:block"></div> -->
<div class="spinner" id="spinner-pre" style="display:block">
  <div class="loading up"></div>
  <div class="loading down"></div>
</div>
<?php if ($show_debug && $alvl >= $alvl_admin) { ?>
	<div style="z-index:100;color:#fff;font-size:9pt; position:fixed; bottom: 10px; left: 10px; padding: 10px; border-radius: 5px; background:rgba(255,255,255,.1)">
		<span>DEBUG:</span><br><hr>
		UUID: <?php echo $_SESSION['uid']; ?> (<?php echo $uname ?>)<br>
		ACCESS_LEVEL: <?php echo $_SESSION['access_level']; ?><br>
		DIR: <span class="debugaf" id="dirasd"><?php echo $id; ?></span> (bar <span class="debug" id="barid"></span>)<br>
		ACTIVE DZs: <span class="debug" id="dropzones-count">%NUM%</span>
	</div>
<?php } ?>
	<div class="alertbox"></div>

	<div id="wrapper" style="visibility:hidden">

	<section class="bar bar-vertical bar-main tabs" id="bar-1">
	<div class="title menubar-title"><?php echo $name . ' ' . $ver ?></div>
	<div class="menubar menubar-left menubar-main tab-links">
	<?php if ($hasUser) { ?>
	<ul>
		<li class="menubar-content menubar-content-user menubar-content-user-name menubar-content-active" id="menubar-button-1"><span id="display_name"><?php echo $uname ?></span><a href="uauth.php?logout" class="btn btn-logout"><i class="fa fa-sign-out"></i></a></li>
		<a href="browse"><li class="menubar-content menubar-content-main menubar-content-active" container="1" id="menubar-button-files" type="folder">My Files</li></a>
	</ul>
	<?php } else { ?>
		<div class="promo">
			<div id="promo-message">
				<h4>Semi-reliable cloud storage.</h4>
				<h4>Somewhat fast server connection.</h4>
				<h4>Absolutely no backups.</h4>
			</div>
			<br><br><br>
			<button class="btn btn-submit" onclick="document.location = 'register.php'"><b>Sign Up for 5GB FREE</b></button>
			<button class="btn btn-login" onclick="document.location = 'browse.php'">Log In</button>
		</div>
	<?php } ?>
	<div class="spinner">
		<div class="loading up"></div>
  		<div class="loading down"></div>
	</div>
	</div>
	</section>
	</div>

	<div class="return-to-main" onclick="bar.move(1,1);bar.move(2,2);bar.size(2,3);files.refresh(2);var a=bar.active;for(i=3;i<=a;i++){bar.remove(bar.active);console.log(bar.active)}$('.return-to-main').fadeOut();">
	<i class="fa fa-angle-left"></i>
	</div>

	<?php
	if ($showfooter) include('includes/footer.php');
  	?>

	<script type="text/template" id="folder_template">
    <li class="menubar-content" container="<%= model.get('container') %>" type="<%= model.get('basicFileType') %>" filehash="<%= model.get('hash_self') %>" id="<%= model.get('fileID') %>" name="<%= model.get('fileName') %>" pos="" fileparent="<%= model.get('hash_parent') %>">
		<div class="dragdrop-border"></div>
		<span class="file-multiselect-checkbox-container">
			<input type="checkbox" id="cb-<%= model.get('fileID') %>" value="<%= model.get('hash_self') %>" <%= model.get('is_checked') %>/>
			<label class="file-multiselect-label" for="cb-<%= model.get('fileID') %>"><span class="file-multiselect-checkbox"></span></label>
		</span>
		<span class="folder file-name"><%= model.get('fileName') %></span>
		<div class="file-info">
			<span class="file-info-item" id="filemod"><span class="filemod"><%= model.get('last_modified_date') %></span><br><span class="filemod"><%= model.get('last_modified_time') %></span></span>
			<span class="file-info-item" id="filedet"><span class="filetype"><%= model.get('fileType') %></span><br><span id="filesize" unit="<%= model.get('units') %>"><%= model.get('fileSize') %></span></span>
		</div>
	</li>
	</script>
	<script type="text/template" id="file_template">
    <li class="menubar-content-view" container="<%= model.get('container') %>" type="<%= model.get('basicFileType') %>" filehash="<%= model.get('hash_self') %>" id="<%= model.get('fileID') %>" name="<%= model.get('fileName') %>" pos="">
		<img class="img-preview" />
		<textarea class="text-preview" id="editor">Loading...</textarea>
		<!-- <div class="text-preview" id="editor"><div class="spinner"><div class="loading up"></div><div class="loading down"></div></div></div> -->
		<audio controls class="audio-preview">Audio tags are not supported by your browser.</audio>
		<video controls class="video-preview">Video tags are not supported by your browser.</video>
		<%= model.get('script') %>
		<div class="file-view"></div>
		<div class="file-info">
			<span class="file-info-item" id="filesize"><span class="filetype"><%= model.get('fileType') %></span><br><%= model.get('fileSize') %></span>
		</div>
	</li>
	</script>
	<!--<script type="text/template" id="minibar_template">
	<li class="minibar-content" filehash="<%= model.get('hash_self') %>" id="<%= model.get('fileID') %>" type="<%= model.get('basicFileType') %>" filename="<%= model.get('fileName') %>">
    	<span class="minibar-file-name"><%= model.get('fileName') %></span>
    </li>
	</script>
	<div type="text/template" id="preview-template" style="display: none;">
		<li class="menubar-content">
		<div class="dz-uploadprogress" data-dz-uploadprogress></div>
		<span class="folder file-name"><span data-dz-name></span></span>
		<div class="file-info">
			<span class="file-info-item"><span data-dz-size></span><br></span>
		</div>
		<span data-dz-errormessage></span>
		</li>
	</div>
	-->
	<!--<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-48081162-1', 'villa7.github.io');
	  ga('send', 'pageview');

	</script> -->
	<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/underscore.min.js"></script>
    <script type="text/javascript" src="js/backbone.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
	<script type="text/javascript">
	var init = {
		resize: function() {
			var title = {
				fontSize: 20,
				fontSpacing: 5,
				fontTotalWidth: $('.title').width(),
				fontLetterWidth: $('.title').width() / $('.title').text().length
			}
			var width = {
				titleBox: $('.title').width(),
				titleText: title.fontTotalWidth,
				titleLetterSpacing: $('.title').width() / (($('.title').text().length - 1) * 4.2)
			}
			$('.title, .heightsettertext').css({
				'font-size': title.fontLetterWidth + 'pt',
				'letter-spacing': width.titleLetterSpacing + 'pt'
			});
			$('.menubar').css({
				'top': parseInt($('.title').height()) + parseInt($('.title').css('paddingTop')) + parseInt($('.title').css('paddingBottom')) + 'px'
			});
			//d.info($('.menubar').css('top'));
		},
		loadFiles: function() {
			//BCL = new BarContentLoader();
			//BCL.start(2, 'home_dir', 'folder');
		}
	}

	if ($('.footer').height() > 0) {
		$(".alertbox").css("bottom", 60);
	} else {
		$(".alertbox").css("bottom", 20);
	}
	var pageTitle = $('title').text();
	var userOpen = false;
	var settingsOpen = false;
	var colorsOpen = false;
	var title_separator = ' ◦ ';
	var userRoot = '<?php echo $uhd; ?>';
	var isSharedPage = true;
	String.prototype.capt = function() {
	    return this.charAt(0).toUpperCase() + this.slice(1);
	}
	$(document).ready(function() {
	   	/*$('.tabs .tab-links li:not(.menubar-content-user-name)').on('click', function(e)  {
	   		$('.sp-container').remove();
	        var currentAttrValue = $(this).attr('href').replace('#', '');
	        $('.bar-alt').remove();
	        if (currentAttrValue != 'files') {
	        	$('#bar-2, #bar-3').remove();
		        $('#wrapper').append('<section class="bar bar-vertical bar-full bar-alt tabs" id="bar-' + currentAttrValue + '"></section>');
		        $('#bar-' + currentAttrValue).append('<div class="menubar-title">'+
					'<span class="heightsettertext"></span>'+
					'<span class="menubar-title-link" onclick="">' + ((currentAttrValue == 'search') ? 'Search Files' : ((currentAttrValue != 'settings' && currentAttrValue != 'colors') ? 'My' : 'Foxfile') + ' ' + currentAttrValue + ((currentAttrValue == 'shared') ? ' Files' : '')) + '</span>'+
					'</div>'+
					'<div class="menubar menubar-left">'+
					'<div class="spinner" id="' + this.active + '"><div class="loading up"></div><div class="loading down"></div></div>'+
					'</div>');
	        	setContent(currentAttrValue);
	        	document.title = pageTitle + title_separator + currentAttrValue.capt();
	        }
	        $('.tabs ' + currentAttrValue).show().siblings().hide();
	        $(this).addClass('menubar-content-active').siblings().removeClass('menubar-content-active');
	        //e.preventDefault();
	        resizeAll();
	    });*/

	    init.resize();
		clickMenu.rebind();
		names.get(<?php echo $_SESSION['uid']; ?>);
		//files.open('<?php echo $_SESSION["uhd"] ?>', 'Shared File', 1, 'folder');
		var name = 'nametemp';
		var type = 'typetemp';

		function getInfo(id) {
			$.post('dbquery.php',
			{
				getfileinfo: id
			},
			function(result) {
				if (result.includes('success')) {
					//d.info(result);
					var res = result.split("|-=-|");
					name = res[1];
					type = res[2];
					files.open('<?php echo $_GET["id"] ?>', 'Shared Files', 1, 'folder');
				} else {
					d.error(result);
				}
			});
		}
		getInfo('<?php echo $_GET["id"] ?>');
	});
	</script>
	<link href="css/dropzone.css" rel="stylesheet" />
    <script type="text/javascript" src="js/dropzone.js"></script>
    <script type="text/javascript" src="js/showlog.js"></script>
    <script type="text/javascript" src="js/foxfile.js"></script>
    <link rel="stylesheet" href="css/codemirror.css">
    <link rel="stylesheet" href="css/cm-themes/twilight.css">
    <script type="text/javascript" src="js/codemirror.js"></script>
    <script src="js/cm-keymap/sublime.js"></script>
    <?php if ($load_codeMirror_addons) { ?>
    <script type="text/javascript" src="js/cm-addon/dialog/dialog.js"></script>
    <link href="js/cm-addon/dialog/dialog.css" rel="stylesheet" />
    <script type="text/javascript" src="js/cm-addon/search/searchcursor.js"></script>
    <script type="text/javascript" src="js/cm-addon/search/search.js"></script>
    <script type="text/javascript" src="js/cm-addon/edit/closebrackets.js"></script>
    <script type="text/javascript" src="js/cm-addon/comment/comment.js"></script>
    <script type="text/javascript" src="js/cm-addon/fold/foldcode.js"></script>
    <script type="text/javascript" src="js/cm-addon/fold/foldgutter.js"></script>
    <script src="js/cm-addon/fold/brace-fold.js"></script>
    <script src="js/cm-addon/fold/xml-fold.js"></script>
    <script src="js/cm-addon/fold/markdown-fold.js"></script>
    <script src="js/cm-addon/fold/comment-fold.js"></script>
    <link href="js/cm-addon/fold/foldgutter.css" rel="stylesheet" />
    <!-- Load the basic modes in because they are used a lot -->
    <script src="js/cm-mode/xml/xml.js"></script>
    <script src="js/cm-mode/javascript/javascript.js"></script>
    <script src="js/cm-mode/css/css.js"></script>
    <script src="js/cm-mode/htmlmixed/htmlmixed.js"></script>
    <script src="js/cm-mode/clike/clike.js"></script>
	<?php
	}

	if ($showpageloadtime && $showfooter) {
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finishtime = $time;
		$total_time = round(($finishtime - $starttime), 4);
		echo '<script type="text/javascript">$("#loadtime").html("page generated in ' . $total_time . ' seconds.");</script>';
	}
	?>
    <script type="text/javascript">
	    setTimeout(function() { //give time for the page to set itself up
	    	$('#wrapper').css('visibility','visible');
			$('#spinner-pre').remove();
	    }, 700);
    </script>

</body>
</html>