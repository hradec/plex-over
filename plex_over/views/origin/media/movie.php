<script type="text/javascript">
var uiEffect	= 'slide';
var uiSpeed		= 200;
var convertVideo = '<?= $convert ?>';
var player = null;
var paused = 'paused';
var playing = 'watching';

$(function(){
	
	var playlist	= $('#playlist');
	var downloads	= $('#download');
	var toMove		= $('#movie-prod');
	var playerDiv = $('#player');
	
	$('#movie-actions .button').eq(0).stop().toggle(
	function(){
		toMove.fadeTo(uiSpeed, 0, function(){
			$(this).slideUp(0);
			playerDiv.fadeTo(uiSpeed, 1);
		});
		playlist.show();
	},function(){
		playerDiv.fadeTo(uiSpeed, 0, function(){
			$(this).slideUp(0);
			toMove.fadeTo(uiSpeed, 1);
		});
		playlist.hide();
	});
	
	$('#movie-actions .button').eq(1).stop().toggle(
	function(){
		if (player) {
			$('a.'+playing).switchClass(playing, paused);
			player.video.pause();
		}
		downloads.show();
	},function(){
		downloads.hide();
	});
	
	
	$('#movie-actions .button').toggle(
		function(){
		$('.current').click();
			$(this).addClass('current');
		},function() {
			$(this).removeClass('current');
	});
		
	// PLaylist video player controls
	$('.playlist-section a').click(function(){
	
		if ($(this).is('.'+playing)) {
			$(this).switchClass(playing, paused);
			player.video.pause();
		}
		else if ($(this).is('.'+paused)) {
			$(this).switchClass(paused, playing);
			player.video.play();
		}
		else {
			var video = $('video');
			var curWidth = $('#show-player').width();
			
			//myPlayer.video.src = $(this).attr('href');
			$('video').attr('src', $(this).attr('href'));
			$('video').find('track').attr('src', $(this).attr('data-sub'));
			
			player = VideoJS.setup("show-player");
			player.subtitlesSource = $(this).attr('data-sub');
			player.video.load();
			$('.playlist-section a').attr('class', '');
			$(this).addClass(playing);
			
			var ratio = $(this).attr('data-ratio');
			$('#show-player').animate({'height': Math.round(curWidth / ratio)+'px'}, 'slow', function(){
					player.video.play();
			});
		}
		return false;
	});
	
	// Player playlist interactions
	$('.vjs-play-control').live('click', function(){
		if(player.video.paused) {
			$('a.'+playing).switchClass(playing, paused);
		}
		else {
			$('a.'+paused).switchClass(paused, playing);
		}
	});
	
});
</script>
<div id="content" class="fit">
	<?= $views->top_nav ?>

		<div id="movie-details" class="gradient">
			<div id="movie-cover" class="top left">
				<?= transcode_img($item, array('height' => 220, 'width' => 150, 'scale' => 'both', 'class' => 'rounded shadow'))?>
			</div>
			<div id="movie-tech" class="clear">
				<ul>
				<?php GLOBAL $duration; $duration='-1';foreach ($item->attributes as $name => $value): 
                        $duration = ($name == 'duration') ? duration($value, 'flv') : $duration ;?>
					<li>
						<span><?= $name ?></span>:
						<span><?= ($name == 'duration') ? $duration : $value ?></span>
					</li>
				<?php endforeach ?>
                <?php 
                    if($duration=='-1'){
                        $sizes=0;
                        foreach ($item->media->part as $part):
                            $file = "$part->file";
                            $cmd = "ffmpeg -i '$file' 2>&1  | grep Duration | cut -d' ' -f4 | tr -d ','";
                            $ret=0;
                            $duration = exec($cmd, $ret);
                            printf("\nDuration: "+$duration);
                        endforeach;
                    }
                ?>
					<li>
						<span>Duration</span>:
						<span><?= $duration ?></span>
					</li>
                
				</ul>
				<div id="movie-actions">
                    <!--
					<span class="button gradient"><?= lang('watch') ?></span>
                    !-->
					<span class="button gradient"><?= lang('download') ?></span>
				</div>
				
				<div id="playlist" class="button-rel" style="display:none">
				<?php $i = 1; foreach ($item->media->part as $part): ?>
	  			<div class="playlist-section">
	  				<?=anchor($this->transcode->video($part, array('ratingKey' => $item->ratingKey)), 
	  					lang('playlist.part_'.$i), 
	  					'data-file="'.$part->file.'"
	  					data-ratio="'.$item->attributes->aspectRatio.'"
						 data-sub="'.$part->subtitles.'"'
	  				)?>
	  			</div>
	  		<?php $i++; endforeach ?>
	  		</div>
	  		
				<div id="download" class="button-rel" style="display:none">
					<?php $i = 1; foreach ($item->media->part as $part): ?>
						<div><?=anchor('download'.$part->file, lang('playlist.part_'.$i).' ('.byte_format($part->size).')')?></div>
					<?php $i++; endforeach ?>
				</div>

			</div>
		</div>
		
		<div id="movie-content" class="dark-gradient">
			<div id="details-text" style="background-image: url(<?= transcode_img($item, array('height' => 500, 'width' => 500, 'force' => 'art'), true)?>)">
			<div class="opacity bb">
				<h1 id="movie-title" class="txt-shadow"><?= $item->title ?> <small>(<?= @$item->year ?>)</small></h1>
				<h2><?= @$item->tagline ?></h2>
				<p id="summary"><?= split_summary(@$item->summary) ?></p>
				</div>
			</div>
			
			<div id="movie-prod" class="media-prod">
				<?php foreach ($item->details as $key => $details): ?>
					<div class="prod movie_<?= count($item->details) ?>">
						<h4><?= pluralize(count($details), lang($key), false) ?></h4>
						<?= movie_details($details, $links->section.'/'.$prod_links.'/'.link_prod($key)) ?>
					</div>
				<?php endforeach ?>
			</div>
			
            <!--
			<div id="player" class="shadow dark-gradient" style="display:none">
				<div class="video-js-box">
					<video id="show-player" class="video-js" x-webkit-airplay="allow">
						<source type="video/mp4">
						<track kind="subtitles" src="" srclang="en-US" label="English"></track>
					</video>
				</div>
			</div>
            !-->


<?php $i = 1; foreach ($item->media->part as $part): ?>
<script type='text/javascript' src='http://hradec.no-ip.org:40001/plex/js/jwplayer.js'></script>
<div id='mediaspace'>This text will be replaced</div>
<script type='text/javascript'>
// TODO : implement stream using a stream script: 
// http://www.longtailvideo.com/support/forums/jw-player/setup-issues-and-embedding/359/streamscript

    var flashvars = {
    file:'<?=$this->transcode->video($part, array('ratingKey' => $item->ratingKey))?>',
    provider:'http',
    'http.startparam':'starttime'
  };

  jwplayer('mediaspace').setup({
    flashplayer: 'http://hradec.no-ip.org:40001/plex/js/player.swf',
    file: '<?=$this->transcode->video($part, array('ratingKey' => $item->ratingKey))?>',
    backcolor: '333333',
    frontcolor: '999999',
    controlbar: 'bottom',
    duration: '<?= $duration ?>',
    autostart: 'true',
    width: '640',
    height: '360',
//    skin: 'http://hradec.no-ip.org:40001/plex/js/modieus/modieus.xml',
    provider: 'http',
    provider:'http',
    'http.startparam':'offset',
//    modes: [
//            { type: 'html5' },
//            { type: 'flash', src: 'http://hradec.no-ip.org:40001/plex/js/player.swf' }
//        ],
    events: {onSeek: function(event) {
                this.load({file: '<?=$this->transcode->video($part, array('ratingKey' => $item->ratingKey))?>'.replace('offset=0','offset='+event.offset), 
                duration: '<?= $duration ?>',
                start: event.offset});
                this.play(true);
            }}
  });
</script>
<?php $i++; endforeach ?>

<a href="http://google.com" 
onclick="var win=window.open('','mywindow','height=360, width=640');
win.document.write('\x3Chtml\x3E\x3Chead\x3E\x3Ctitle\x3EPlex\x3C/title\x3E\x3Cstyle\x3Ehtml,body {margin:0;padding:0;}\x3C/style\x3E\x3C/head\x3E\x3Cbody\x3E\x3Cembed src=\'http://hradec.no-ip.org:40001/plex/js/player.swf?file=<?="\\\\\\\\\\'".$this->transcode->video($part, array('ratingKey' => $item->ratingKey))."\\\\\\\\\\'"?>&autoStart=true\' width=\'100%\' height=\'100%\' quality=\'high\' type=\'application/x-shockwave-flash\' pluginspage=\'http://www.macromedia.com/go/getflashplayer\'\x3E\x3C/embed\x3E\x3C/body\x3E\x3C/html\x3E');return false;">
POP PLAYER OUT!</a></div>


  <div class="fw-paragraphbottom"></div>



				
		</div> <!-- movie-content -->
	
</div>
