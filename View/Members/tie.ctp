<div class="row">
	<?php if (!empty($accounts)) : ?>
		<h3 class="text-center">Please choose the Pivotal Tracker account that we are working with.</h3>
		<div class="list-group col-sm-4 col-sm-offset-4">
		<?php foreach ($accounts as $id => $name) : ?>
			<?php echo $this->Html->link($name, array('action' => 'tie', $id), array('class' => 'list-group-item')); ?>
		<?php endforeach; ?>
		</div>
	<?php elseif (!empty($accountId) && empty($personId)) : ?>
		<h3 class="text-center">Please choose the Client Bot / catch all, that we are working with.</h3>
		<div class="list-group col-sm-4 col-sm-offset-4">
			<?php foreach ($persons as $person) : ?>
				<?php echo $this->Html->link($person['person']['name'], array('action' => 'tie', $accountId, $person['id']), array('class' => 'list-group-item')); ?>
			<?php endforeach; ?>
		</div>
	<?php elseif (defined('__TRACKER_CLIENT_TOKEN')) : ?>
		<h3 class="text-center">Associate Client Bot with Projects <img src="//project.buildrr.com/img/logo-pt.png" alt="Pivotal Tracker" style="width: 230px;" class="image-responsive"></h3>
		<div class="col-sm-4 col-sm-offset-2">
			
			<div class="well">
				<p>Only projects that the Client Bot is associated with can have real client's associated with them.</p>
				<?php if (!empty($existingProjects)) : ?>
					<h5>Client Bot is already apart of these projects...</h5>
					<ul>
					<?php foreach ($existingProjects as $project) : ?>
						<li><?php echo $project; ?></li>
					<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>		
			
		</div>
		
		<div class="col-sm-4">
			<?php echo $this->Form->create(); ?>
			<?php echo $this->Form->input('Tracker.person_id', array('type' => 'hidden', 'value' => $personId)); ?>
			<?php echo $this->Form->input('Tracker.projects', array('label' => 'Add Client Bot to Projects <br><small>(Hold shift to select multiple projects)</small>', 'options' => $projects, 'type' => 'select', 'multiple' => true)); ?>
			<p><small class="text-muted">It's usually a good idea to select all projects</small></p>
			<?php echo $this->Form->end('Submit'); ?>
		</div>
		
	<?php else : ?>
		<h3 class="text-center">Finish up integration with <img src="//project.buildrr.com/img/logo-pt.png" alt="Pivotal Tracker" style="width: 230px;" class="image-responsive"></h3>
		<div class="col-sm-4 col-sm-offset-2">
			
			<p class="well">We need to get an API Key for the Client Bot's account so that this user can send comments from PROJECT buildrr to Pivotal Tracker.</p>
			
			<p class="well">Only projects that the Client Bot is associated with can have real client's associated with them.</p>
			
			<p><strong>Important Note : </strong> The username for this account is <?php echo $person['person']['username']; ?>.  To send a comment that you create in Pivotal Tracker to PROJECT buildrr, you will prefix your comment with @<?php echo $person['person']['username']; ?>. All comments without that prefix will be private to members who have access to Pivotal Tracker. </p>
		</div>
		
		<div class="col-sm-4">
			<?php echo $this->Form->create(); ?>
			<?php echo $this->Form->input('Tracker.person_id', array('type' => 'hidden', 'value' => $personId)); ?>
			<?php echo $this->Form->input('Tracker.username', array('label' => $person['person']['name'] . '\'s Pivotal Tracker Email <small>(* this is not stored)</small>', 'value' => $person['person']['email'])); ?>
			<?php echo $this->Form->input('Tracker.password', array('label' => $person['person']['name'] . '\'s Pivotal Tracker Password <small>(* this is not stored)</small>', 'type' => 'password')); ?>
			<?php echo $this->Form->input('Tracker.projects', array('label' => 'Add Client Bot to These Projects <br><small class="text-muted">It\'s usually a good idea to select all projects</small>', 'options' => $projects, 'type' => 'select', 'multiple' => 'checkbox')); ?>
			<?php echo $this->Form->end('Submit'); ?>
		</div>
	<?php endif; ?>
</div>