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
	<?php if($this->action==='help') : ?>
		<?php echo $content_for_layout; ?>
	<?php else : ?>
		<div id="content">
			<?php if($this->name==="Play" && !$is_mobile) : ?>
				<div id='banner'>
					<?php if(!$logged_in) : ?>
						<a href='/register'><img src='/img/banner/banner_0<?php echo mt_rand(1, 4); ?>.jpg'></a>
					<?php else : ?>
						<a href='/payment'><img src='/img/banner/banner_0<?php echo mt_rand(1, 4); ?>.jpg'></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if(!$is_mobile && !(isset($_GET['header']) && $_GET['header']==='none')) : ?>
		 		<header>
					<?php echo $this->element('header'); ?>
				</header>
			<?php endif; ?>
			<?php if(!$is_mobile && $this->name==="Play" && $this->action==="index") : ?>
				<div id='search_box'>
					<input type="text" name="search" value="" id="search" placeholder='検索' />
				</div>
			<?php endif; ?>
			<div id="main">
				<?php echo $content_for_layout; ?>
			</div>
			<?php if($this->name!=="Play" && $this->action!=="law" && $this->action!=="upload" && $this->action!=="init") : ?>
				<footer>
					<?php echo $this->element('footer'); ?>
				</footer>
			<?php endif; ?>
			<?php if($this->name==="Play" && !$is_mobile) : ?>
				<div id='twitter_widget'>
		            <a class="twitter-timeline"  href="https://twitter.com/hashtag/%E6%9C%97%E8%AA%AD%E5%B0%91%E5%A5%B3" data-widget-id="619571884661706752">#朗読少女 のツイート</a>
		            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
 </body>
</html>