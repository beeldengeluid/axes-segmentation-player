<?php

function toPrettyTime($ms) {
	$ms = intval($ms);
	$hours = 0;
	$minutes = 0;
	$seconds = 0;
	while($ms >= 360000){
		$hours++;
		$ms -= 360000;
	}
	while($ms >= 60000) {
		$minutes++;
		$ms -= 60000;
	}
	while($ms >= 1000) {
		$seconds++;
		$ms -= 1000;
	}
	if($hours < 10) {$hours = '0'.$hours;}
	if($minutes < 10) {$minutes = '0'.$minutes;}
	if($seconds < 10) {$seconds = '0'.$seconds;}
	return $hours.':'.$minutes.':'.$seconds;
}

?>

<!DOCTYPE html>
<!-- saved from url=(0045)http://deploy1.beeldengeluid.nl/frontend/vtt/ -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>Video Hyperlinking Annotator</title>
<meta name="author" content="Jaap Blom">
<meta name="description" content="">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<link href="./static/css/main.css" rel="stylesheet" type="text/css">

<style type="text/css">

	/*
	Color hierarchy
	---------------

	red: #e00034; (Logo)
	blue: #009fda; (Consument, Professionals, Instituut)
	yellow: #fce300; (Consument, Onderwijs)

	grey: # c7c2ba;
	orange: #ff5800;
	green-light: #92d400;
	blue-dark: # 0028be;
	purple: #56197b;
	green: #00b400;

	*/

	body {
		font-family: 'Roboto','Helvetica Neue',Helvetica,Arial,sans-serif;
		text-rendering: optimizelegibility;
		position: relative;
		padding-top: 70px;
		line-height: 1.6;
	}

	a {
		color: #009fda;
	}

	.navbar {
		text-transform: uppercase;
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.26);
	}

	.beng-header {
		font-family: 'Roboto Condensed','Helvetica Neue',Helvetica,Arial,sans-serif;
		text-transform: uppercase;
		font-weight: 700;
	}

	.beng-header-sub {
		font-family: 'Ubuntu','Helvetica Neue',Helvetica,Arial,sans-serif;
		font-weight: 300;
		font-style: italic;
	}

	.page-header .beng-header-sub {
		margin-top: 0;
		color: #009fda;
	}

	.beng-header-block-blue {
		background-color: #009fda;
		color: #ffffff;
		box-shadow: -10px 0 0 #009fda, 10px 0 0 #009fda;
		box-decoration-break: clone;
	}

	.beng-header-block-red {
		background-color: #e00034;
		color: #ffffff;
		box-shadow: -10px 0 0 #e00034, 10px 0 0 #e00034;
		box-decoration-break: clone;
	}

	.beng-text-blue {
		color: #009fda;
	}

	.beng-text-red {
		color: #e00034;
	}

	/* demo CSS */

	#demo-icons span {
		display: block;
		margin: 20px;
		float: left;
		font-size: 32px;
	}

	#home, #typography, #icons, #contact {
		padding-top: 70px;
		padding-bottom: 70px;
	}

	</style>

</head>

<body data-spy="scroll" data-target="#navbar" data-offset="100">

	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Menu</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">Labs · Beeld en Geluid</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li class="active"><a href="/">Video</a></li>
					<li class=""><a href="/">Contact</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div id="video">

		<div class="container">

			<div class="row">
				<div class="col-md-12">
					<h3 id="description"></h3>
				</div>


				<div id="selection_panel" class="col-md-12">
					<table id="select_table" class="table">
						<!-- will be filled from javascript -->
					</table>

					<div class="text-center">
						<button class="btn btn-danger" onclick="finish()">
							Save task
						</button>
					</div>

					<div>
						<h3>Keyboard shortcuts</h3>
						<div class="well" style="color:crimson;">
							<strong>Warning: keyboard shortcuts ONLY work when your cursor is outside of any input field</strong>
						</div>
						<div class="well">
							<strong>Standard player controls</strong><br>
							Press <kbd>space</kbd> to play/pause the video.
						</div>
						<div class="well">
							<strong>Fast forward &amp; rewind</strong><br>
							Use the numbers to fast forward a certain amaount of seconds: e.g. press <kbd>3</kbd> to fast forward 3 seconds. <br><br>
							To rewind, hold <kbd>SHIFT</kbd> plus a number. <kbd>SHIFT+4</kbd> rewinds the video by 4 seconds.

							Use the left and right arrow to fast forward or rewind by 1 minute:<br><br>
							Pressing <kbd>left</kbd> on your keypad rewinds the video for 1 minute<br><br>
							Pressing <kbd>right</kbd> on your keypad fast forwards the video for 1 minute
						</div>

						<div class="well">
							<strong>Start &amp; end of clip</strong><br>
							To mark the start of the clip, press: <kbd>i</kbd><br>
							To mark the end of the clip, press: <kbd>o</kbd>
							To go to the start of your clip selection, press: <kbd>SHIFT+i</kbd><br>
							To go to the end of your clip selection, press: <kbd>SHIFT+o</kbd>
						</div>
						<div class="well">
							<strong>Anchors</strong><br>
							To save an anchor press: <kbd>CTRL+s</kbd><br>
							To create a new anchor press: <kbd>CTRL+n</kbd><br>
							To edit the next anchor in the list press: <kbd>]</kbd><br>
							To edit the previous anchor in the list press: <kbd>[</kbd><br>
						</div>
					</div>
				</div>

			</div>

			<br>

			<div id="refinement_panel" class="row">

				<div class="col-md-12">
					<span id="video_label"></span>
				</div>

				<div class="col-md-8">

					<!-- THE VIDEO PLAYER AND VIDEO CONTROLS ARE SHOWN IN BOTH ANCHOR & REFINEMENT MODE-->

					<div id="video_container">
						<div id="video_player">Loading the player...</div>
						<div id="timebar">
							<canvas id="timebar_canvas" width="300" height="50">

							</canvas>
						</div>
					</div>

					<br>

					<div class="row">
						<div class="col-sm-6">
							<div class="input-group">
								<span class="input-group-addon start-group">
									Start
								</span>
								<input id="start_time" type="text" class="form-control" placeholder="00:00:00">
								<span class="input-group-btn">
									<button class="btn btn-default" type="button" onclick="setManualStart()"
										title="When you press this the start time will be set to the time you entered in the input field">
										Save
									</button>
									<button class="btn btn-default" type="button" onclick="setStart()"
										title="When you press this the start time will be same as the current player time">
										Copy
									</button>
									<button class="btn btn-default" type="button" onclick="playStart()"
										title="When you press this, the player will skip to the defined starting point">
										Go!
									</button>
								</span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="input-group">
								<span class="input-group-addon end-group">&nbsp;End&nbsp;</span>
								<input id="end_time" type="text" class="form-control" placeholder="00:00:00">
								<span class="input-group-btn">
									<button class="btn btn-default" type="button" onclick="setManualEnd()"
										title="When you press this the end time will be set to the time you entered in the input field">
										Save
									</button>
									<button class="btn btn-default" type="button" onclick="setEnd()"
										title="When you press this the end time will be same as the current player time">
										Copy
									</button>
									<button class="btn btn-default" type="button" onclick="playEnd()"
										title="When you press this, the player will skip to the defined end point">
										Go!
									</button>
								</span>
							</div>
						</div>
					</div><!-- END OF PLAYER & CONTROLS-->

					<br>

					<div class="row">
						<div class="col-sm-12">

							<!-- ONLY SHOWN IN ANCHOR MODE (using ugly jquery :s) -->
							<form id="anchor_save">
								<div class="form-group">
									<label for="anchor_title">Title <span id="anchor_edit">&nbsp;(new)</span></label>
									<input id="anchor_title" type="text" class="form-control" placeholder="Enter title">
								</div>
								<div class="form-group">
									<label for="anchor_desc">Description of ideal linked video</label>
									<input id="anchor_desc" type="text" class="form-control" placeholder="Description">
								</div>
								<button class="btn btn-primary" type="button" onclick="newAnchor()">
									New anchor
								</button>
								<button class="btn btn-primary" type="button" onclick="saveAnchor()">
									Save
								</button>
								<button class="btn btn-danger" type="button" onclick="backToSelection()">
									End task / select clip
								</button>
							</form>

							<!-- ONLY SHOWN IN REFINEMENT MODE -->
							<div id="refine_button_panel" class="text-center">
								<button class="btn btn-primary" onclick="addAnchors();">Add anchors</button>
							</div>

						</div>
					</div>

					<br>

				</div>

				<div class="col-md-4">
					<div class="row">
						<div class="col-md-12">

							<!-- ONLY DISPLAYED WHEN EDITING ANCHORS -->
							<div id="anchor_tabs" role="tabpanel">
								<ul class="nav nav-tabs" role="tablist">
									<li role="presentation" class="active">
										<a href="#anchors" aria-controls="anchors" role="tab" data-toggle="tab">Anchors</a>
									</li>
									<li role="presentation">
										<a href="#guidelines" aria-controls="guidelines" role="tab" data-toggle="tab">Guidelines</a>
									</li>
								</ul>

								<div class="tab-content">

									<!-- LIST OF ANCHORS TAB-->
									<div role="tabpanel" class="tab-pane active" id="anchors">
										<h3>Saved anchors</h3>
										<div id="saved_anchors">
											<!-- filled on the client-->
										</div>
									</div>

									<!-- GUIDLINES TAB -->
									<div role="tabpanel" class="tab-pane" id="guidelines">
										<h3>Guidelines</h3>
										<div class="well">
											Anchors should be created for one of the following reasons:<br>
											<ul>
												<li>Linked clips may help users to understand the anchor better.</li>
												<li>Linked clips may contain relevant information about the anchor, given the current information need.</li>
												<li>Linked clips may contain information about occurring objects, places, people, and events that appear in this clip.</li>
											</ul>
										</div>
									</div>

								</div>

							</div>
						</div>
					</div>

				</div>

			</div>

		</div>

	</div>

	<div id="dialog-confirm" title="Finish" style="display:none;">
		Proceed to next search?
	</div>

	<script>
	var _videoData = <?php echo $this->data; ?>
	</script>

	<script src="./static/js/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="./static/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="./static/js/jwerty/jwerty.js"></script>
	<script type="text/javascript" src="./static/jwplayer/jwplayer.js"></script>
	<script type="text/javascript" src="./static/js/moment.min.js"></script>
	<script type="text/javascript" src="./static/js/player.js"></script>


	</body></html>