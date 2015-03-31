<div class="row">
	<div class="col-md-8">
		<h4>Files for this project <small class="pull-right">
			<?php if ($_GET['view'] == 'grid') : ?>
				<a href="<?php echo $this->request->here; ?>?view=list">List view</a>
				Grid view
			<?php else : ?>
				List view
				<a href="<?php echo $this->request->here; ?>?view=grid">Grid view</a></small>
			<?php endif; ?>
		</small></h4>
		
		
		<?php if ($_GET['view'] == 'grid') : ?>
			<div class="row">
			<?php for ($i=0; $i < count($attachments); $i++) : ?>
				
				<div class="col-xs-4 col-sm-4 col-md-4 text-center">
					<div class="col-md-12" style="border: 1px solid #eee; border-radius: 3px; margin-top: 10px; height: 100%; padding-top: 10px; overflow: hidden;">
						<?php if ($attachments[$i]['file_attachments']['thumbnailable'] == true) : ?>
						<a target="_blank" href="/projects/pivotal_tracker/download/<?php echo $attachments[$i]['file_attachments']['id']; ?>">
							<img src="<?php echo $attachments[$i]['file_attachments']['thumbnail_url']; ?>">
						</a>
						<?php else : ?>
						<a target="_blank" href="/tracker/projects/download/<?php echo $attachments[$i]['file_attachments']['id']; ?>">
							<img src="/img/lgnoimage.gif"><br>
						</a>
						<?php endif; ?>
						<p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><small><?php echo $attachments[$i]['file_attachments']['filename']; ?></small></p>
					</div>
				</div>
				<?php if (($i+1) % 3 == 0) : ?>
				</div>
				<div class="row">
				<?php endif; ?>
			<?php endfor; ?>
			</div>
		<?php else : ?>
			<?php for ($i=0; $i < count($attachments); $i++) : ?>
				<div class="row">
					<div class="col-md-12">
					<?php if ($attachments[$i]['file_attachments']['thumbnailable'] == true) : ?>
						<a class="pull-left" target="_blank" href="/tracker/projects/download/<?php echo $attachments[$i]['file_attachments']['id']; ?>">
							<img class="pull-left thumbnail" style="width:90px; margin-right: 10px;" src="<?php echo $attachments[$i]['file_attachments']['thumbnail_url']; ?>">
						</a>
					<?php else : ?>
						<a class="pull-left" target="_blank" href="/tracker/projects/download/<?php echo $attachments[$i]['file_attachments']['id']; ?>">
							<img class="pull-left thumbnail" style="width:90px; margin-right: 10px;" src="/img/lgnoimage.gif">
						</a>
					<?php endif; ?>
						<h5><strong><?php echo $attachments[$i]['file_attachments']['filename']; ?></strong></h5>
					</div>
				</div>
				<hr />
			<?php endfor; ?>
		<?php endif; ?>
		<?php echo empty($attachments) ? '<div class="text-center well"> -- No files found yet, add one via comment on a <a href="/tracker/projects/messages/' . $project['id'] . '">task</a> -- </div>' : null; ?>
	</div>
	
	
	<div class="col-md-4" style="overflow:hidden;text-overflow:ellipsis;">
		<?php echo $this->element('asset-space-01'); ?>
		<?php echo $this->element('asset-space-02'); ?>
	</div>
</div>

<?php
// set the contextual menu items
$this->set('menus', array(
	'<li><a href="/tracker/projects/view/' . $project['id'] . '">Dashboard</a></li>',
	'<li><a href="/tracker/projects/messages/' . $project['id'] . '">Scope</a></li>',
	'<li class="active"><a href="/tracker/projects/files/' . $project['id'] . '">Files</a></li>'
	));
	
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Go To',
		'items' => array(
			$this->Html->link(__('Pivotal Tracker'), 'https://www.pivotaltracker.com/projects/' . $project['id']),
			)
		)
	)));