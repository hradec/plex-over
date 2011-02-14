<script>
 $(function(){
		$('a.tip').tipTip({maxWidth: 400, delay : 1000, fadeOut:0, defaultPosition: 'top'});
		var myPlayer = VideoJS.setup("show-player");
 });
</script>
<div id="content" class="fit">

	<?= $views->top_nav ?>
	
	<div id="details-main" class="details dark-gradient bb">
		<div id="details-cover" class="left">
			<?= transcode_img($episode, array('width' => 130, 'height' => 72, 'scale' => 'both', 'type' => 'src'))?>
		</div>
		
		<div class="left">
				<h1 class="txt-shadow">
					<?= $episode->title ?> 
					<small>- <?= lang('episode')." ".$episode->index ?></small></h1>
		    <h2><?= $item->title1." ".$item->title2 ?></h2>
		</div>
		<div class="clear"></div>
	</div>
	
	<div id="show-content">
		<div id="show-main">
			<div class="video-js-box">
				<video id="show-player" class="video-js" controls="controls" poster="<?= $this->plex_url.thumb($episode) ?>" >
					<source src="<?= $this->transcode->video($episode->media->part[0], array('ratingKey' => $item->key)) ?>"  type="video/mp4" />
					<track kind="subtitles" src="<?= $episode->media->part[0]->subtitles ?>" srclang="en-US" label="English"></track>
				</video>
			</div>
			
			<div id="show-details" class=" media-prod left">
				<ul>
					<li>
						<h4><?= lang('duration')?>:</h4>
						<?= duration($episode->duration, 'movie')?>
					</li>
					<?php foreach ($episode->details as $key => $details): ?>
					<li>
						<h4><?= pluralize(count($details), lang($key), false) ?>:</h4>
						<?= movie_details($details) ?>
					</li>
				<?php endforeach ?>
					<li>
						<p><?= anchor_download($episode->media->part[0]->file, $episode->media->part[0]->size) ?></p>
					</li>
				</ul>
			</div>
		</div>
		
		<div id="episodes">
			<?php foreach ($item->content as $friend): ?>
				<div class="jacket left">
					<a href="<?= link_media($link, $friend->ratingKey.$show_link, $this->uri->segment(9)) ?>"
						title="<h3><?= $friend->title ?></h3> <?= $friend->summary ?>" 
						class="tip" >
						<div class="img <?= active_item((string)$episode->index, (string)$friend->index, 'current') ?>">
						<?= transcode_img($friend, array('width' => 130, 'height' => 72, 'scale' => 'both'))?>
						<strong><?= ucwords(lang('episode'))." ".$friend->index ?></strong><br />
						<span><?= $friend->title ?></span>
						</div>
					</a>
				</div>
			<?php endforeach ?>
		</div>
	</div> <!-- show content -->
</div>