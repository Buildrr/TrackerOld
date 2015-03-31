

<div class="row">
	<div class="col-md-8">
		<h1><?php echo $project['name']; ?></h1>
		<hr> 
		
		<h4>Click a task to see description and discussion.</h4>
		
		<?php for ($i=0; $i < count($delivered); $i++) : ?>
			<p style="white-space: nowrap;overflow:hidden;text-overflow:ellipsis;">
				<span class="label label-warning">Ready to Review</span>
				<span class="badge"><?php echo !empty($delivered[$i]['labels'][0]['name']) ? $delivered[$i]['labels'][0]['name'] : null; ?></span>
				<?php echo $this->Html->link($delivered[$i]['name'], array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'message', $delivered[$i]['project_id'], $delivered[$i]['id'])); ?>
			</p>
		<?php endfor; ?>
		
		
		<?php for ($i=0; $i < count($stories); $i++) : ?>
			<div class="row">
				<div class="col-md-12">
				<?php if ($stories[$i]['story_type'] == 'release') : ?>
				<p class="label label-primary col-md-12" style="font-weight: normal; text-align: left; padding: 8px; white-space: nowrap; overflow:hidden; text-overflow:ellipsis;">
					<?php echo $stories[$i]['name']; ?>
				</p>
				<?php else : ?>
				<p style="white-space: nowrap;overflow:hidden;text-overflow:ellipsis;">
					<span class="label label-primary">In-Progress Scope Item</span>
					<span class="badge"><?php echo !empty($stories[$i]['labels'][0]['name']) ? $stories[$i]['labels'][0]['name'] : null; ?></span>
					<?php echo $this->Html->link($stories[$i]['name'], array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'message', $stories[$i]['project_id'], $stories[$i]['id'])); ?>
				</p>
				<?php endif; ?>
				</div>
			</div>
		<?php endfor; ?>
		
		<div class="row">
			<hr class="col-md-12" />
		</div>
		
		<?php for ($i=0; $i < count($unscheduled); $i++) : ?>
			<div class="row">
				<div class="col-md-12">
					<?php if ($unscheduled[$i]['story_type'] == 'release') : ?>
						<p class="label label-primary col-md-12" style="font-weight: normal; text-align: left; padding: 8px; white-space: nowrap; overflow:hidden; text-overflow:ellipsis;">
							<?php echo $unscheduled[$i]['name']; ?>
						</p>
					<?php else : ?>
						<p style="white-space: nowrap;overflow:hidden;text-overflow:ellipsis;">
							<span class="label label-info">Future Enhancement</span>
							<span class="badge"><?php echo !empty($unscheduled[$i]['labels'][0]['name']) ? $unscheduled[$i]['labels'][0]['name'] : null; ?></span>
							<?php echo $this->Html->link($unscheduled[$i]['name'], array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'message', $unscheduled[$i]['project_id'], $unscheduled[$i]['id'])); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		<?php endfor; ?>
		
		<?php for ($i=0; $i < count($accepted); $i++) : ?>
			<p style="white-space: nowrap;overflow:hidden;text-overflow:ellipsis;">
				<span class="label label-success">Accepted Scope Item </span>&nbsp;
				<span class="badge"><?php echo !empty($accepted[$i]['labels'][0]['name']) ? $accepted[$i]['labels'][0]['name'] : null; ?></span>
				<?php echo $this->Html->link($accepted[$i]['name'], array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'message', $accepted[$i]['project_id'], $accepted[$i]['id'])); ?>
			</p>
		<?php endfor; ?>
	</div>
	
	
	<div class="col-md-4" style="overflow:hidden;text-overflow:ellipsis;">
		<?php echo $this->element('asset-space-01'); ?>
		<?php echo $this->element('asset-space-02'); ?>
		
		<?php if (defined('__TRACKER_ALLOW_ADD') && __TRACKER_ALLOW_ADD == true) : ?>
			<hr>
			<h4>Submit a New Scope Item</h4>
			<?php echo $this->Form->create('Tracker', array('url' => array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'story'))); ?>
			<?php echo $this->Form->input('Tracker.project_id', array('type' => 'hidden', 'value' => $project['id'])); ?>
			<?php echo $this->Form->input('Tracker.name', array('label' => 'Descriptive Title <br><small class="text-muted">eg. Customers should be able to... </small>')); ?>
			<?php echo $this->Form->input('Tracker.description', array('type' => 'textarea', 'label' => 'Description	<small><a href="http://markdown-here.com/livedemo.html" target="_blank">Supports Markdown Syntax <span class="glyphicon glyphicon-new-window"></span></a></small>')); ?>
			<?php echo $this->Form->end('Submit'); ?>
		<?php endif; ?>
	</div>
</div>


<?php
// set the contextual menu items
$this->set('menus', array(
	'<li><a href="/tracker/projects/view/' . $project['id'] . '">Dashboard</a></li>',
	'<li class="active"><a href="/tracker/projects/messages/' . $project['id'] . '">Scope</a></li>',
	'<li><a href="/tracker/projects/files/' . $project['id'] . '">Files</a></li>'
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
