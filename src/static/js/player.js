
jwplayer.key = 'SWCiaYWnJ9Ri4wKfADRn3N40bPrMf2/GiO8iGQ==';
var videoUrl = 'http://axes.ch.bbc.co.uk/collections/cAXES/videos/cAXES/v20080516_100000_bbcone_to_buy_or_not_to_buy.webm';
var jw = null;
var _start = 0;
var _end = 0;
var _screenScale = 0;
var _videos = [];
var _fragmentMode = false;



var _curVideoIndex = 0;

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
    init();

});

/***********************************************************************************
 * player controllers
 **********************************************************************************/

function setStart(start) {
	if(start == undefined) {
		_start = jw.getPosition();
		console.debug('JW pos: ' + _start);
	} else {
		_start = start;
	}
	$('#start_time').val(formatTime(_start));
	updateBar();
}

function setEnd(end) {
	if(end == undefined) {
		_end = jw.getPosition();
	} else {
		_end = end;
	}
	$('#end_time').val(formatTime(_end));
	updateBar();
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
	var html = [];
	html.push('<ul class="list-group">');
	$.each(_videos[_curVideoIndex].anchors, function(index, value) {
		console.debug(value);
		html.push('<li class="list-group-item" onclick="selectVideo('+index+');">');
		html.push('<a href="#">' + value.label + '</a></li>');
	});
	html.push('</ul>');
	$('#anchors').html(html.join(''));
}

function saveAnchor(index) {
	var anchor = {
		start : _start * 1000,
		end : _end * 1000,
		label : 'test',
      	descriptionIdealLink : 'test2'
	}
	if(index) {
		//TODO update
	}
	if(_videos[_curVideoIndex].anchors) {
		_videos[_curVideoIndex].anchors.push(anchor);
	} else {
		_videos[_curVideoIndex].anchors = [anchor];
	}
	updateAnchors();
}

function switchMode() {
	var buttonText = $("#edit_mode").text();
	_fragmentMode = buttonText == 'Edit anchors';
	//if going into the mode where anchors can be edited, save the boundaries of the clip
	if(_fragmentMode) {
		_videos[_curVideoIndex].start = _start * 1000;//ms!
		_videos[_curVideoIndex].end = _end * 1000;//ms!
	} else {
		//TODO save the anchors to the current clip
	}
	selectVideo(_curVideoIndex, _fragmentMode);
	initTimebar(_fragmentMode);
	$("#edit_mode").text(_fragmentMode ? 'Edit clip boundaries' : 'Edit anchors');
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
		console.debug('DURATION= ' + dur + '('+(_end - _start)+') start=' + start + '('+_start+' )end=' + end + '('+_end+')');
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
	$('#video_title').text('Now playing: ' + videoObj.title);
}

function playClip(videoObj) {
	console.debug('playing clip');
	updateVideoMetadata(videoObj);
	jw = jwplayer("video_player").setup({
		file: videoObj.videoURL,
		width:'100%',
		controls : false,
		image: null
	}).onTime(onPlayerTime).onResize(onResizePlayer).onReady(onPlayerReady);
	setStart(videoObj.start / 1000);
	setEnd(videoObj.end / 1000);
}

function playFragment(videoObj) {
	console.debug('playing fragment only: ' + videoObj.start + ' ' + videoObj.end);
	updateVideoMetadata(videoObj);
	_start = -1;
	_end = -1
	jw = jwplayer("video_player").setup({
		file: videoObj.videoURL + '?t=' + (videoObj.start / 1000) + ',' + (videoObj.end / 1000),
		width:'100%',
		controls : false,
		image: null
	}).onTime(onPlayerTime).onResize(onResizePlayer);
}

/***********************************************************************************
 * form functions
 **********************************************************************************/

function setManualStart() {
	var s = $('#start_time').val();
	console.debug(s);
	_start = moment.duration(s).asSeconds();
	updateBar();
}

function setManualEnd() {
	var s = $('#end_time').val();
	console.debug(s);
	_end = moment.duration(s).asSeconds();
	updateBar();
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
	if(_fragmentMode) {
		playFragment(_currentClip);
	} else {
		playClip(_currentClip);
	}
}

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
		$('#description').text('Your interest: ' + _videoData['description']);
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

function initKeyBindings() {
	//arrow key shortcuts
	jwerty.key('left', function() {
		rw(60);
	});
	jwerty.key('right', function() {
		ff(60);
	});
	jwerty.key('q', function() {
		scaleScreen();
	});
	jwerty.key('w', function() {
		scaleScreen(false);
	});

	//pause & play shortcut
	jwerty.key('space', function() {
		if(jw.getState() == 'PLAYING') {
			jw.pause();
		} else {
			jw.play();
		}
	});

	//start & end shortcuts
	jwerty.key('shift+s', function() {
		setStart();
	});
	jwerty.key('shift+e', function() {
		setEnd();
	});
	jwerty.key('ctrl+s', function() {
		playStart();
	});
	jwerty.key('ctrl+e', function() {
		playEnd();
	});

	//fast forward shortcuts (somehow cannot create these in a loop...)
	jwerty.key('1', function() {
		ff(1);
	});
	jwerty.key('2', function() {
		ff(2);
	});
	jwerty.key('3', function() {
		ff(3);
	});
	jwerty.key('4', function() {
		ff(4);
	});
	jwerty.key('5', function() {
		ff(5);
	});
	jwerty.key('6', function() {
		ff(6);
	});
	jwerty.key('7', function() {
		ff(7);
	});
	jwerty.key('8', function() {
		ff(8);
	});
	jwerty.key('9', function() {
		ff(9);
	});

	//rewind shortcuts
	jwerty.key('shift+1', function() {
		rw(1);
	});
	jwerty.key('shift+2', function() {
		rw(2);
	});
	jwerty.key('shift+3', function() {
		rw(3);
	});
	jwerty.key('shift+4', function() {
		rw(4);
	});
	jwerty.key('shift+5', function() {
		rw(5);
	});
	jwerty.key('shift+6', function() {
		rw(6);
	});
	jwerty.key('shift+7', function() {
		rw(7);
	});
	jwerty.key('shift+8', function() {
		rw(8);
	});
	jwerty.key('shift+9', function() {
		rw(9);
	});
}