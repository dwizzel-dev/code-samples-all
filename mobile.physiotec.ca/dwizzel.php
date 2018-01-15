<!DOCTYPE html>
<html lang="en">
<!--
only for testing pupose
http://sproutvideo-examples.s3.amazonaws.com/custom_player/final/index.html#
-->
<head>
<title>Test Video Page</title>

<meta charset="utf-8">

<!-- 
1) si pas de meta X-UA-Compatible
	a) alors les boutons ne s'affiche pas  
2) si pas de meta X-UA-Compatible a IE="edge"
	a) les boutons s'affiche et la cache fonctionne meme sur le refresh
		quand pas de user agent defini
	b) 


-->

<!--<meta http-equiv="X-UA-Compatible" content="IE=edge">-->
<meta http-equiv="X-UA-Compatible" content="IE=7,8,9,10">
<meta name="viewport" content="width=device-width, initial-scale=1"> 


<script type="text/javascript" src="https://c.sproutvideo.com/player_api.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/3.0.2/css/font-awesome.min.css">
</head>
<body>
<div class="player">
	<div class="video">
		<iframe class='sproutvideo-player' type='text/html' src='//videos.sproutvideo.com/embed/e89bdcb91118e2cb60/9cf0c3870ea2797e?type=sd&noBigPlay=true&showcontrols=false' width='100%' height='100%' frameborder='0'></iframe>
	</div>
	<div class="toolbar">
		<div class="control play-pause"><a href="#"><i class="icon-play"></i></a></div>
		<div class="progress-container">
			<div class="progress"></div>
		</div>
		<div class="control fullscreen"><a href="#"><i class="icon-fullscreen"></i></a></div>
		<div class="control volume"><a href="#"><i class="icon-volume-up"></i></a></div>
	</div>
</div>
</body>
<style>
html {
background: #fff;
-webkit-background-size: cover;
-moz-background-size: cover;
-o-background-size: cover;
background-size: cover;
}

.player {
width: 480px;
height: 360px;
position: relative;
margin: 10px auto;
border: 1px solid #CCC;
box-shadow: 1px 1px 15px #CCC;
}

.video {
position: absolute;
left:0;
right:0;
top: 0;
bottom:43px;
}

.control {
float: left;
padding: 0 10px;
}
.toolbar {
position: absolute;
left: 0;
right: 0;
bottom: 0;
background-color: rgba(255,255,255,0.8);
padding: 13px 0 13px;

}

.control a {
text-decoration: none;
color: #000000;
}

.progress-container {
position: absolute;
left: 43px;
right: 79px;
width: auto;
}
.volume, .fullscreen {
float: right;
}

.player:-webkit-full-screen {
width: 100%;
height: 100%;
}
</style>
<script type="text/javascript">
var videoPlayer;
$(document).ready(function(){
$('.progress').slider({
	value: 0,
	orientation: "horizontal",
	range: "min",
	animate: true,
	slide: function(event, ui) {
	videoPlayer.seek(ui.value);
	}
	});

var _duration = 0, _playing = false, _volume = 1, _fullscreen = false;
var videoPlayer = new SV.Player({videoId: 'e89bdcb91118e2cb60'});
videoPlayer.bind('ready', function(event){
	_duration = event.data.duration;
	$(".progress").slider("option", "max", _duration);
	});
videoPlayer.bind('progress', function(event){
	$('.progress').slider("option", "value", (event.data.time));
	});
videoPlayer.bind('pause', function(event){
	_playing = false;
	$('.play-pause i').removeClass('icon-pause').addClass('icon-play');
	});
videoPlayer.bind('play', function(event){
	_playing = true;
	$('.play-pause i').removeClass('icon-play').addClass('icon-pause');
	});
videoPlayer.bind('volume', function(event){
	_volume = event.data;
	if (_volume == 1) {
		$('.volume i').removeClass('icon-volume-off').addClass('icon-volume-up');
	} else if (_volume == 0) {
		$('.volume i').removeClass('icon-volume-up').addClass('icon-volume-off');
		}
	});
$('.play-pause a').click(function(){
	if (!_playing) {
		videoPlayer.play();
	} else {
		videoPlayer.pause();
		}
	});
$('.volume a').click(function(){
	if (_volume == 0) {
		_volume = 1;
	} else {
		_volume = 0;
	}
	videoPlayer.setVolume(_volume);
	});

$('.fullscreen a').click(function(){
	var elem = $('.player')[0];
	if (!_fullscreen) {
		if (elem.requestFullscreen) {
			elem.requestFullscreen();
		} else if (elem.mozRequestFullScreen) {
			elem.mozRequestFullScreen();
		} else if (elem.webkitRequestFullscreen) {
			elem.webkitRequestFullscreen();
			}
		_fullscreen = true;
	} else {
		if (document.exitFullscreen) {
			document.exitFullscreen();
		} else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else if (document.webkitCancelFullScreen) {
			document.webkitCancelFullScreen();
			}
		_fullscreen = false;
		}
	});
});
</script>
</html>