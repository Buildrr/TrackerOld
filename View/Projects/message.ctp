
<div class="row clearfix">
	<div class="col-md-5">
		<h4><?php echo $story['name']; ?></h4>
		<?php echo !empty($story['description']) ? $this->Html->markdown($story['description']) : '-- no task description provided --'; ?>
	</div>
	<div class="col-md-7">
		<h4>Discussion - <small><a href="#" class="toggleClick" data-target="#comment-form">add comment</a></small></h4>
		
		<div id="comment-form">
			<?php echo $this->Form->create('Comment', array('type' => 'file', 'url' => array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'comment'))); ?>
			<?php echo $this->Form->input('Comment.project_id', array('value' => $story['project_id'], 'type' => 'hidden')); ?>
			<?php echo $this->Form->input('Comment.story_id', array('value' => $story['id'], 'type' => 'hidden')); ?>
			<?php echo $this->Form->input('Comment.text', array('type' => 'textarea', 'label' => 'Leave a Comment - <small><a href="http://markdown-here.com/livedemo.html" target="_blank">Supports Markdown Syntax <span class="glyphicon glyphicon-new-window"></span></a></small>')); ?>
			<?php echo $this->Form->input('Comment.file', array('label' => 'Attach a File <strong>Max Size 50mb</strong>', 'type' => 'file')); ?>
			<?php echo $this->Form->end('Submit'); ?>
		</div>
		
		<?php if (!empty($story['comments'])) : ?>
			<?php for ($i=0; $i < count($story['comments']); $i++) : ?>
				<div class="row" style="border-bottom: 1px solid #d5d5d5;margin-bottom: 10px;">
					<div class="col-xs-2 col-sm-1">
						<?php if ($story['comments'][$i]['User']) : ?>
							<?php echo $this->element('Galleries.thumb', array('thumbClass' => 'img-responsive', 'defaultImage' => '/img/icon.client.png', 'thumbClass' => false, 'model' => 'User', 'foreignKey' => $story['comments'][$i]['User']['id'], 'thumbLink' => '/tracker/members/view/' . $story['comments'][$i]['User']['id'])); ?>
						<?php elseif ($clientId == $story['comments'][$i]['person_id']) : ?>
							<img src="/img/icon.client.png" alt="client comment">
						<?php else : ?>
							<?php echo $this->element('Galleries.thumb', array('thumbClass' => 'img-responsive', 'thumbSize' => 'large', 'thumbLink' => '', 'thumbLinkOptions' => array('style' => 'color: #333;font-size: 10px;'), 'defaultImage' => '/img/icon.buildrr.png', 'model' => 'Tracker', 'foreignKey' => 1)); ?>
						<?php endif; ?>
					</div>
					<div class="col-xs-10 col-sm-11">
						<?php $editLink = strtotime($story['comments'][$i]['updated_at']) > strtotime('-30 minutes') && $clientId == $story['comments'][$i]['person_id'] ? '<br><a href="#" data-toggle="collapse" data-target="#comment-update-form-' . $story['comments'][$i]['id'] . '">Edit Comment</a>' : null; ?>
						<?php echo $this->Html->markdown('<small class="text-muted pull-right text-right">' . $this->Time->timeagoinwords($story['comments'][$i]['updated_at']) . $editLink . '</small> &nbsp;' . $story['comments'][$i]['text']); ?>
						
						<?php foreach ($story['comments'][$i]['file_attachments'] as $attachment) : ?>
							<div class="col-xs-3 col-sm-2 text-center">
							<?php if ($attachment['thumbnailable'] == true) : ?>
								<a href="/tracker/projects/download/<?php echo $attachment['id']; ?>" target="_blank">
									<img src="<?php echo $attachment['thumbnail_url']; ?>"><br>
									<p style="white-space: nowrap;text-overflow: ellipsis;overflow:hidden"><small><?php echo $attachment['filename']; ?></small></p>
								</a>
							<?php else : ?>
								<a href="/tracker/projects/download/<?php echo $attachment['id']; ?>" target="_blank">
									<img src="/img/lgnoimage.gif"><br>
									<p style="white-space: nowrap;text-overflow: ellipsis;overflow:hidden"><small><?php echo $attachment['filename']; ?></small>
									<p style="position:absolute; top:15%; left: 35%; color: #666; font-weight: bold;"><?php echo strtoupper(pathinfo($attachment['filename'], PATHINFO_EXTENSION)); ?></p>
								</a>
							<?php endif; ?>
							</div>
						<?php endforeach; ?>
						<div id="comment-update-form-<?php echo $story['comments'][$i]['id']; ?>" class="collapse">
							<?php echo $this->Form->create('Comment', array('type' => 'put', 'url' => array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'comment'))); ?>
							<?php echo $this->Form->input('Comment.id', array('value' => $story['comments'][$i]['id'], 'type' => 'hidden')); ?>
							<?php echo $this->Form->input('Comment.project_id', array('value' => $story['project_id'], 'type' => 'hidden')); ?>
							<?php echo $this->Form->input('Comment.story_id', array('value' => $story['id'], 'type' => 'hidden')); ?>
							<?php echo $this->Form->input('Comment.text', array('value' => $story['comments'][$i]['text'], 'type' => 'textarea', 'label' => false)); ?>
							<?php echo $this->Form->end('Submit'); ?>
						</div>
					</div>
				</div>
			<?php endfor; ?>
		<?php else : ?>
			<p>no discussion yet</p>
		<?php endif; ?>
	</div>
</div>

<?php
// set the contextual breadcrumb items
$this->set('context_crumbs', array('crumbs' => array(
	$this->Html->link(__('Project Dashboard'), array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'view', $story['project_id'])),
	$this->Html->link(__('All Scope Items'), array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'messages', $story['project_id'])),
	'Scope Item',
)));

// set the contextual menu items
$this->set('menus', array(
	'<li><a href="/tracker/projects/view/' . $story['project_id'] . '">Dashboard</a></li>',
	'<li class="active"><a href="/tracker/projects/messages/' . $story['project_id'] . '">Scope</a></li>',
	'<li><a href="/tracker/projects/files/' . $story['project_id'] . '">Files</a></li>'
	));
	
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Go To',
		'items' => array(
			$this->Html->link(__('Pivotal Tracker'), 'https://www.pivotaltracker.com/projects/' . $project['id']),
			$this->Html->link(__('Story'), 'https://www.pivotaltracker.com/projects/' . $project['id'] . '/stories/' . $story['id']),
			)
		)
	)));
