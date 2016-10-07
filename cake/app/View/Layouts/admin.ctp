<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('favicon.ico', '/img/favicon.ico', array('type'=>'icon'));
		$css_prefix = $is_mobile ? 'mobile/' : '';
		echo $this->Html->css("{$css_prefix}common");
		echo $this->Html->css($css_prefix.strtolower($this->name));
		echo $scripts_for_layout;
		echo $this->Html->script('jquery-2.1.3.min');
		echo $this->Html->script('jquery.cookie.min');
		echo $this->Html->script('jquery.balloon.min');
		echo $this->Html->script('jquery.quicksearch.min');
		echo $this->Html->script('jquery.pjax.min');
		echo $this->Html->script('common.js');
	?>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<meta property="og:title" content="<?php echo SERVICE_NAME; ?>">
	<meta property="og:description" content="オーディオブック朗読サービス"/>
	<meta property="og:url" content="https://<?php echo $_SERVER['SERVER_NAME']; ?>/play">
	<meta property="og:image" content="http://<?php echo $_SERVER['SERVER_NAME']; ?>/img/play_3.png"/>
	<meta property="og:site_name" content="<?php echo SERVICE_NAME; ?>">
	<meta property="og:email" content="<?php echo COMPANY_EMAIL; ?>">
	<meta property="og:phone_number" content="050-5585-1095">
	<meta property="og:type" content="book"/>
	<meta property="og:site_name" content="<?php echo SERVICE_NAME; ?>"/>
	<meta property="fb:app_id" content="1450212918616270"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="twitter:widgets:csp" content="on">
	<?php include_once(APP . 'Vendor/analyticstracking.php'); ?>
	<script type='text/javascript'>
		$(function() {
			$("ul a").pjax("#content", {
				link: 'a:not([target]):not(.no_pjax)',
				fragment: "#content"
			});
		});
	</script>
</head>
<body>
	<div style='text-align:center;'>
		<?php echo $content_for_layout; ?>
	</div>
</body>
</html>