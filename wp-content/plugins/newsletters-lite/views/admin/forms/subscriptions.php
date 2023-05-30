<!-- Form Subscriptions -->
<?php // phpcs:ignoreFile ?>

<div class="wrap newsletters">	
	<h2><?php esc_html_e('Form Subscriptions', 'wp-mailinglist'); ?></h2>
	
	<?php $this -> render('forms' . DS . 'navigation', array('form' => $form), true, 'admin'); ?>
	
	<?php $this -> render('subscribers' . DS . 'loop', array('subscribers' => $subscribers, 'paginate' => $paginate), true, 'admin'); ?>
</div>