
jwplayer.key = 'SWCiaYWnJ9Ri4wKfADRn3N40bPrMf2/GiO8iGQ==';
var videoUrl = 'http://axes.ch.bbc.co.uk/collections/cAXES/videos/cAXES/v20080516_100000_bbcone_to_buy_or_not_to_buy.webm';
var jw = null;

var _id = null;
var _userId = null;

var _start = -1;
var _end = -1;
var _screenScale = 0;
var _videos = [];
var _fragmentMode = false;

var _curVideoIndex = 0;
var _currentAnchorIndex = -1;

var _currentClip = null;

$(document).ready( function(){
    //Get the canvas &
    var c = $('#timebar_canvas');
    var ct = c.get(0).getContext('2d');
    var container = $(c).parent();

    //Run function when browser resizes
    $(window).resize( respondCanvas );

    function respondCanvas(){
        c.attr('width', $(container).width() ); //max width
        c.attr('height', $(container).height() ); //max height
    }
    respondCanvas();

    //make sure to disable the standard button keyevents, so the space bar won't accidentally trigger a button press
    $('button').keydown(function(event){
		event.preventDefault();
    });
    $('button').keyup(function(event){
		event.preventDefault();
    });
    $('button').keypress(function(event){
		event.preventDefault();
    });
    init();

});

/***********************************************************************************
 * helper functions
 **********************************************************************************/

function formatTime(t) {
	var pt = moment.duration(t * 1000);
	var h = pt.hours() < 10 ? '0' + pt.hours() : pt.hours();
	var m = pt.minutes() < 10 ? '0' + pt.minutes() : pt.minutes();
	var s = pt.seconds() < 10 ? '0' + pt.seconds() : pt.seconds();
	return h + ':' + m + ':' + s;
}

function validateDuration(start, end) {
	if(end == -1 || start == -1) {
		return true;
	}
	if(end - start < 15 && !_fragmentMode) {
		alert('A clip must be at least 15 seconds long');
		return false;
	} else if(end - start < 1 && _fragmentMode) {
		alert('An anchor must be at least 1 seconds long');
		return false;
	}
	return true;
}

/***********************************************************************************
 * player controllers
 **********************************************************************************/

function setStart(start) {
	var temp = -1;
	if(start == undefined) {
		temp = jw.getPosition();
	} else {
		temp = start;
	}
	if((_end != -1 && temp < _end) || _end == -1) {
		if(validateDuration(temp, _end)) {
			_start = temp;
			if(!_fragmentMode) {
				$('#video_start').text(formatTime(_start));
			}
			$('#start_time').val(formatTime(_start));
			updateBar();
		}
	} else {
		alert('The start must be smaller than the end time');
	}
}

function setEnd(end) {
	var temp = -1;
	if(end == undefined) {
		temp = jw.getPosition();
	} else {
		temp = end;
	}
	if((_start != -1 && temp > _start) || _start == -1) {
		if(validateDuration(_start, temp)) {
			_end = temp;
			if(!_fragmentMode) {
				$('#video_end').text(formatTime(_end));
			}
			$('#end_time').val(formatTime(_end));
			updateBar();
		}
	} else {
		alert('The end time must be bigger than the start time');
	}
}

function playStart() {
	jw.seek(_start);
}

function playEnd() {
	jw.seek(_end);
}

function rw(t) {
	jw.seek(jw.getPosition() -t);
}

function ff(t) {
	jw.seek(jw.getPosition() +t);
}

function scaleScreen(enlarge) {
	if(enlarge) {
		jw.resize(50, 50);
	} else {
		jw.resize(-50, -50);
	}
}

/***********************************************************************************
 * player event handlers
 **********************************************************************************/

function onPlayerTime(e) {
	updateBar();
}

function onResizePlayer(e) {

}

function onPlayerReady(e) {
	jw.play();
}

/***********************************************************************************
 * anchor functions
 **********************************************************************************/

function updateAnchors() {
	if(_videos[_curVideoIndex].anchors) {
		var html = [];
		html.push('<ul class="list-group">');
		$.each(_videos[_curVideoIndex].anchors, function(index, value) {
			html.push('<li class="list-group-item">');
			html.push('<a onclick="loadAnchor('+index+');"><abbr>' + value.title + '</abbr>&nbsp;');
			html.push(formatTime(value.start / 1000) + ' - ');
			html.push(formatTime(value.end / 1000));
			html.push('</a>');
			html.push('&nbsp;<a onclick="deleteAnchor('+index+');">');
			html.push('<i class="glyphicon glyphicon-remove"></i>');
			html.push('</li>');
		});
		html.push('</ul>');
		$('#anchors').html(html.join(''));
	} else {
		$('#anchors').html('');
	}
}

function loadAnchor(index) {
	var anchor = _videos[_curVideoIndex].anchors[index];
	_currentAnchorIndex = index;
	_start = anchor.start / 1000;
	_end = anchor.end / 1000;
	$('#anchor_title').val(anchor.title);
	$('#anchor_desc').val(anchor.description);
}

function deleteAnchor(index) {
	_videos[_curVideoIndex].anchors.splice(index, 1);
	updateAnchors();
}

function validateAnchor() {
	if($('#anchor_title').val().trim().length < 5 || $('#anchor_desc').val().trim().length <5) {
		alert('Please enter a title and a description of at least 5 characters each');
		return false;
	}
	return true;
}

function saveAnchor() {
	if(validateAnchor()) {
		var anchor = {
			start : _start * 1000,
			end : _end * 1000,
			title : $('#anchor_title').val(),
	      	description : $('#anchor_desc').val()
		}
		if(_currentAnchorIndex != -1) {
			_videos[_curVideoIndex].anchors[_currentAnchorIndex] = anchor;
		} else {
			if(_videos[_curVideoIndex].anchors) {
				_videos[_curVideoIndex].anchors.push(anchor);
			} else {
				_videos[_curVideoIndex].anchors = [anchor];
			}
		}
		_start = -1;
		_end = -1;
		$('#anchor_title').val('');
		$('#anchor_desc').val('');
		_currentAnchorIndex = -1;
		updateAnchors();
	}
}

/***********************************************************************************
 * switch mode
 **********************************************************************************/

function switchMode() {
	var buttonText = $("#edit_mode").text();
	_fragmentMode = buttonText.indexOf('anchors') != -1;
	//if going into the mode where anchors can be edited, save the boundaries of the clip
	if(_fragmentMode) {
		$('#anchor_save').css('visibility', 'visible');
		_videos[_curVideoIndex].start = _start * 1000;//ms!
		_videos[_curVideoIndex].end = _end * 1000;//ms!
	} else {
		$('#anchor_save').css('visibility', 'hidden');
		//TODO save the anchors to the current clip
	}
	selectVideo(_curVideoIndex);
	initTimebar(_fragmentMode);
	$("#edit_mode").text(_fragmentMode ? 'Edit clip boundaries' : 'Save refinement & Edit anchors');
	updateBar();
}

/***********************************************************************************
 * timebar functions
 **********************************************************************************/

function updateBar() {
	var c = document.getElementById("timebar_canvas");
	var dur = -1;

	if(_fragmentMode) {
		var start = _videos[_curVideoIndex].start / 1000;
		var end = _videos[_curVideoIndex].end / 1000;
		var dur = end - start;
		var t = jw.getPosition();
		var dt = t - start;
		var formattedTime = formatTime(t);
		var elapsed = c.width / 100 * (dt / (dur / 100));
		var startPoint = c.width / 100 * ((_start - start) / (dur / 100));
		var endPoint = c.width / 100 * ((_end - start) / (dur / 100));
		var ctx = c.getContext("2d");
		ctx.clearRect (0, 0, c.width, c.height);
		ctx.fillStyle = "#FF0000";
		ctx.fillRect(0,0, elapsed, c.height / 3);//time progressing
		ctx.fillStyle = "#00FF00";
		ctx.fillRect(startPoint, 0, 2, c.height);//time progressing
		ctx.fillStyle = "#FFFF00";
		ctx.fillRect(endPoint, 0, 2, c.height);//time progressing
		ctx.font = "20px Verdana";
		ctx.fillStyle = "#FFFF00";
		ctx.fillText(formattedTime, 10, c.height - 5);


	} else {
		var dur = jw.getDuration();
		var t = jw.getPosition();
		var formattedTime = formatTime(t);
		var elapsed = c.width / 100 * (t / (dur / 100));
		var startPoint = c.width / 100 * (_start / (dur / 100));
		var endPoint = c.width / 100 * (_end / (dur / 100));
		var ctx = c.getContext("2d");
		ctx.clearRect (0, 0, c.width, c.height);
		ctx.fillStyle = "#FF0000";
		ctx.fillRect(0,0, elapsed, c.height / 3);//time progressing
		ctx.fillStyle = "#00FF00";
		ctx.fillRect(startPoint, 0, 2, c.height);//time progressing
		ctx.fillStyle = "#FFFF00";
		ctx.fillRect(endPoint, 0, 2, c.height);//time progressing
		ctx.font = "20px Verdana";
		ctx.fillStyle = "#FFFFFF";
		ctx.fillText(formattedTime, 10, c.height - 5);
	}
}

function getMousePos(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    return {
      x: evt.clientX - rect.left,
      y: evt.clientY - rect.top
    };
}

/***********************************************************************************
 * playout functions
 **********************************************************************************/

function updateVideoMetadata(videoObj) {
	$('#video_start').text(formatTime(videoObj.start / 1000));
	$('#video_end').text(formatTime(videoObj.end / 1000));
}

function playClip(videoObj) {
	updateVideoMetadata(videoObj);
	jw = jwplayer("video_player").setup({
		file: videoObj.videoURL + '#t=' + (videoObj.start / 1000) + ',' + (videoObj.end / 1000),
		width:'100%',
		controls : false,
		image: null
	}).onTime(onPlayerTime).onResize(onResizePlayer).onReady(onPlayerReady);
	setStart(videoObj.start / 1000);
	setEnd(videoObj.end / 1000);
}

/***********************************************************************************
 * form functions
 **********************************************************************************/

function setManualStart() {
	var s = $('#start_time').val();
	console.debug(s);
	_start = moment.duration(s).asSeconds();
	updateBar();
	jw.seek(_start);
}

function setManualEnd() {
	var s = $('#end_time').val();
	console.debug(s);
	_end = moment.duration(s).asSeconds();
	updateBar();
	jw.seek(_end);
}

function finish() {
	$( "#dialog-confirm" ).dialog({
		resizable: false,
		height:240,
		modal: true,
		buttons: {
			"Save & finish": function() {
				$(this).dialog("close");
				document.location.href = 'http://axes.ch.bbc.co.uk/axes/me2014/axes/#/';
			},
			"Cancel": function() {
		  		$(this).dialog("close");
			}
		}
    });
}

/***********************************************************************************
 * video select functions
 **********************************************************************************/

function toPrettyVideoName(videoUrl) {
	if (videoUrl.indexOf('/') != -1) {
		var u_arr = videoUrl.split('/');
		return u_arr[u_arr.length -1];
	}
	return videoUrl;
}

function changeVideo(elm) {
	selectVideo($('#video_select option:selected').val());
}

function selectVideo(index) {
	_curVideoIndex = index;
	_currentClip = _videos[_curVideoIndex];
	playClip(_currentClip);
	updateAnchors();
}

/***********************************************************************************
 * init functions
 **********************************************************************************/

function init() {
	console.debug(_videoData);
	initVideoData();
	selectVideo(_curVideoIndex);
	initTabs();
	initTimebar();
	initKeyBindings();
}

function initVideoData() {
	if(_videoData) {
		$('#description').text('You were looking for: "' + _videoData.description + '"');
		//fill the list of videos
		$.each(_videoData['relevant'], function(index, value) {
			_videos.push(value);
		});
	} else {
		alert('You have to post some video data in order for this page to load');
		//document.location.href = '/axes-segmentation-player';
	}
}

function initTabs() {
	$('#tabs a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
}

function initTimebar(fragmentMode) {
	//first unbind any existing events from the canvas
	$('#timebar_canvas').unbind( "click" );

	//then bind a new one depending on the edit mode (fragmentMode)
	if(_fragmentMode) {
		$('#timebar_canvas').click(function(e) {
			var c = document.getElementById("timebar_canvas");
			var mousePos = getMousePos(c, e);
			var start = _videos[_curVideoIndex].start / 1000;
			var end = _videos[_curVideoIndex].end / 1000;
			var dur = end - start;
			var pos = dur / 100 * (mousePos.x / (c.width / 100));
			jw.seek(start + pos);
		});
	} else {
		$('#timebar_canvas').click(function(e) {
			var c = document.getElementById("timebar_canvas");
			var mousePos = getMousePos(c, e);
			var dur = jw.getDuration();
			var pos = dur / 100 * (mousePos.x / (c.width / 100));
			jw.seek(pos);
		});
	}
}

//this function checks if the user is using any of the input fields. If so the keyboard shortcuts must be disabled
function checkFocus(f, args) {
	if($('input').is(':focus')) {
		return true;
	}
	if(f) {
		f(args);
	}
}

function initKeyBindings() {
	//arrow key shortcuts
	jwerty.key('left', function() {
		checkFocus(rw, 60);
	});
	jwerty.key('right', function() {
		checkFocus(ff, 60);
	});

	//switch mode
	jwerty.key('m', function() {
		checkFocus(switchMode);
	});

	//pause & play shortcut
	jwerty.key('space', function() {
		if(!checkFocus()) {
			if(jw.getState() == 'PLAYING') {
				jw.pause();
			} else {
				jw.play();
			}
		}
	});

	//start & end shortcuts
	jwerty.key('shift+s', function() {
		checkFocus(setStart);
	});
	jwerty.key('shift+e', function() {
		checkFocus(setEnd);
	});
	jwerty.key('ctrl+s', function() {
		checkFocus(playStart);
	});
	jwerty.key('ctrl+e', function() {
		checkFocus(playEnd);
	});

	//fast forward shortcuts (somehow cannot create these in a loop...)
	jwerty.key('1', function() {
		checkFocus(ff, 1);
	});
	jwerty.key('2', function() {
		checkFocus(ff, 2);
	});
	jwerty.key('3', function() {
		checkFocus(ff, 3);
	});
	jwerty.key('4', function() {
		checkFocus(ff, 4);
	});
	jwerty.key('5', function() {
		checkFocus(ff, 5);
	});
	jwerty.key('6', function() {
		checkFocus(ff, 6);
	});
	jwerty.key('7', function() {
		checkFocus(ff, 7);
	});
	jwerty.key('8', function() {
		checkFocus(ff, 8);
	});
	jwerty.key('9', function() {
		checkFocus(ff, 9);
	});

	//rewind shortcuts
	jwerty.key('shift+1', function() {
		checkFocus(rw, 1);
	});
	jwerty.key('shift+2', function() {
		checkFocus(rw, 2);
	});
	jwerty.key('shift+3', function() {
		checkFocus(rw, 3);
	});
	jwerty.key('shift+4', function() {
		checkFocus(rw, 4);
	});
	jwerty.key('shift+5', function() {
		checkFocus(rw, 5);
	});
	jwerty.key('shift+6', function() {
		checkFocus(rw, 6);
	});
	jwerty.key('shift+7', function() {
		checkFocus(rw, 7);
	});
	jwerty.key('shift+8', function() {
		checkFocus(rw, 8);
	});
	jwerty.key('shift+9', function() {
		checkFocus(rw, 9);
	});
}