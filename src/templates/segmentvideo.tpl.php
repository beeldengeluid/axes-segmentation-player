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

</head>

<body data-spy="scroll" data-target="#navbar" data-offset="100">

	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<a id="navbar-info" class="navbar-brand" href="#">Select clip</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#" id="session_id"></a></li>
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
						<label for="perspective">Perspective</label>
						<div id="perspective" class="btn-group" data-toggle="buttons">
						    <label class="btn btn-default active">
						        <input type="radio" name="need_perspective" id="default_perspective"
						         value="My preference" checked>
						    My preference </label>
						    <label class="btn btn-default">
						        <input type="radio" name="need_perspective" value="Others">
						    Others </label>
						    <label class="btn btn-default">
						        <input type="radio" name="need_perspective" value="Both">
						    Both </label>
						</div>
						&nbsp;&nbsp;
						<button class="btn btn-danger" onclick="finish()">
							End task
						</button>
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
										Set
									</button>
									<button class="btn btn-default" type="button" onclick="setStart()"
										title="When you press this the start time will be same as the current player time (press i)">
										Copy
									</button>
									<button class="btn btn-default" type="button" onclick="playStart()"
										title="When you press this, the player will skip to the defined starting point (SHIFT+i)">
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
										Set
									</button>
									<button class="btn btn-default" type="button" onclick="setEnd()"
										title="When you press this the end time will be same as the current player time (press o)">
										Copy
									</button>
									<button class="btn btn-default" type="button" onclick="playEnd()"
										title="When you press this, the player will skip to the defined end point (SHIFT+o)">
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
									<label for="anchor_desc">
										Description of ideal linked clips (Start with: <em>&quot;Relevant links have ...&quot;)</em>
									</label>
									<input id="anchor_desc" type="text" class="form-control" placeholder="Description">
								</div>
								<div class="form-group">
									<label for="anchor_options">Characteristic</label>
									<div id="anchor_options" class="btn-group" data-toggle="buttons">
									    <label class="btn btn-default active">
									        <input type="radio" name="anchor_characteristic" id="default_characteristic"
									         value="Visual" checked>
									    Visual </label>
									    <label class="btn btn-default">
									        <input type="radio" name="anchor_characteristic" value="Speech">
									    Speech </label>
									    <label class="btn btn-default">
									        <input type="radio" name="anchor_characteristic" value="Both">
									    Both </label>
									</div>
								</div>
								<button class="btn btn-primary" type="button" onclick="newAnchor()" title="Create new anchor (CTRL+n)">
									New anchor
								</button>
								<button class="btn btn-primary" type="button" onclick="saveAnchor()" title="Save current anchor (CTRL+s)">
									Save anchor
								</button>
								<button class="btn btn-danger" type="button" onclick="backToSelection(true)">
									End task / select clip
								</button>
							</form>

							<!-- ONLY SHOWN IN REFINEMENT MODE -->
							<div id="refine_button_panel" class="text-center">
								<button class="btn btn-primary" onclick="addAnchors();">Edit anchors</button>
								<button class="btn btn-danger" onclick="backToSelection(false);">Back</button>
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
										<a href="#guidelines" aria-controls="guidelines" role="tab" data-toggle="tab">Guidelines</a>
									</li>
									<li role="presentation">
										<a href="#anchors" aria-controls="anchors" role="tab" data-toggle="tab">Anchors</a>
									</li>
									<li role="presentation">
										<a href="#shortkeys" aria-controls="shortkeys" role="tab" data-toggle="tab">Shortcuts</a>
									</li>
								</ul>

								<div class="tab-content">

									<!-- GUIDLINES TAB -->
									<div role="tabpanel" class="tab-pane active" id="guidelines">
										<h3>Guidelines</h3>
										<div class="well">
											Anchors should be created for one of the following reasons:<br>
											<ul>
												<li>Links may help users to understand the anchor better.</li>
												<li>Links may contain relevant information about the anchor, given what you are currently looking for.</li>
												<li>Links may contain information about occurring objects, places, people, and events that appear in this anchor.</li>
											</ul>
										</div>
									</div>

									<!-- LIST OF ANCHORS TAB-->
									<div role="tabpanel" class="tab-pane" id="anchors">
										<h3>Saved anchors</h3>
										<div id="saved_anchors">
											<!-- filled on the client-->
										</div>
									</div>

									<div role="tabpanel" class="tab-pane" id="shortkeys">
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
											Use the numbers to fast forward a certain amaount of seconds: e.g. press <kbd>3</kbd> to fast forward 3 seconds. <br>
											To rewind, hold <kbd>SHIFT</kbd> plus a number, e.g. <kbd>SHIFT+4</kbd> rewinds the video by 4 seconds.<br><br>

											Use the left and right arrow to fast forward or rewind by 1 minute:<br>
											Pressing <kbd>left</kbd> on your keypad rewinds the video for 1 minute<br>
											Pressing <kbd>right</kbd> on your keypad fast forwards the video for 1 minute
										</div>

										<div class="well">
											<strong>Start &amp; end of clip</strong><br>
											To mark the start of the clip, press: <kbd>i</kbd><br>
											To mark the end of the clip, press: <kbd>o</kbd><br>
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


							<!-- ONLY DISPLAYED WHEN REFINING CLIPS -->
							<div id="refine_tabs">
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
									Use the numbers to fast forward a certain amaount of seconds: e.g. press <kbd>3</kbd> to fast forward 3 seconds. <br>
									To rewind, hold <kbd>SHIFT</kbd> plus a number, e.g. <kbd>SHIFT+4</kbd> rewinds the video by 4 seconds.<br><br>

									Use the left and right arrow to fast forward or rewind by 1 minute:<br>
									Pressing <kbd>left</kbd> on your keypad rewinds the video for 1 minute<br>
									Pressing <kbd>right</kbd> on your keypad fast forwards the video for 1 minute
								</div>

								<div class="well">
									<strong>Start &amp; end of clip</strong><br>
									To mark the start of the clip, press: <kbd>i</kbd><br>
									To mark the end of the clip, press: <kbd>o</kbd><br>
									To go to the start of your clip selection, press: <kbd>SHIFT+i</kbd><br>
									To go to the end of your clip selection, press: <kbd>SHIFT+o</kbd>
								</div>
							</div><!-- END OF REFINE CLIP TABS-->



						</div>
					</div>

				</div>

			</div>

		</div>

	</div>

	<div id="dialog-confirm" title="Finish" style="display:none;">
		Proceed to next search?
	</div>

	<div id="dialog-confirm-anchors" title="Select Clip, security question (yes/no)" style="display:none;">
		Are you confident that you created all anchors according to the guidelines?
	</div>

	<script>
	var _videoData = <?php echo $this->data; ?>
	</script>

	<script src="./static/js/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script type="text/javascript" src="./static/js/jwerty/jwerty.js"></script>
	<script src="./static/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="./static/jwplayer/jwplayer.js"></script>
	<script type="text/javascript" src="./static/js/moment.min.js"></script>

	<script type="text/javascript" src="./static/js/player.js"></script>


	</body></html>