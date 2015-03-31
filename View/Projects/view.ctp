<div class="row">
	<div class="col-md-8">
		<h1><?php echo $project['name']; ?></h1>
		<hr> 
		<h4>Project overview &amp; activity</h4>
		<?php for ($i=0; $i < count($activities); $i++) : ?>
			<?php $labelType = 'default'; ?>
			<?php $labelType = $activities[$i]['kind'] == 'story_create_activity' ? 'primary' : $labelType; ?>
			<?php $labelType = $activities[$i]['kind'] == 'comment_create_activity' ? 'warning' : $labelType; ?>
			<?php $labelType = $activities[$i]['kind'] == 'story_delete_activity' ? 'danger' : $labelType; ?>
			<?php $labelType = $activities[$i]['kind'] == 'story_update_activity' && $activities[$i]['highlight'] == 'accepted' ? 'success' : $labelType; ?>
			<?php $activities[$i]['kind'] = $activities[$i]['kind'] == 'story_update_activity' && $activities[$i]['highlight'] == 'accepted' ? 'scope_item_accepte' : $activities[$i]['kind']; ?>
 			<p style="white-space: nowrap;overflow:hidden;text-overflow:ellipsis;">
				<span class="label label-<?php echo $labelType; ?>">
					<?php echo Inflector::humanize(str_replace(array('_activity', 'story'), array('', 'scope_item'), $activities[$i]['kind'])) . 'd'; ?>
					<small><?php //echo $this->Time->timeagoinwords($activities[$i]['occurred_at']); ?></small>
				</span> &nbsp; 
				<?php echo $this->Html->link($activities[$i]['primary_resources'][0]['name'], array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'message', $activities[$i]['project']['id'], $activities[$i]['primary_resources'][0]['id'])); ?>
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
	'<li class="active"><a href="/tracker/projects/view/' . $project['id'] . '">Dashboard</a></li>',
	'<li><a href="/tracker/projects/messages/' . $project['id'] . '">Scope</a></li>',
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