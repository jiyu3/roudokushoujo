<div id='navigation'>
	<ul>
		<li>
			<a href='/'><img id='logo' src='/img/header/logo_bata.png'></a>
		</li>
		<li>
			<a class='link_button' id='home_button' href='/'>
				<img src='/img/header/home.png'>
			</a>
		</li>
		<?php if($logged_in) : ?>
			<li>
				<a class='link_button' id='mypage_button' href='/user' style='margin-left:10px;'>
					<img src='/img/header/mypage.png'>
				</a>
			</li>
			<li>
				<a class='link_button' id='logout_button' href='/user/logout' style='margin-left:10px;'>
					<img src='/img/header/logout.png'>
				</a>
			</li>
		<?php else : ?>
			<li>
				<a class='link_button' id='login_button' href='/user/login' style='margin-left:10px;'>
					<img src='/img/header/login.png'>
				</a>
			</li>
			<li>
				<a class='link_button' id='register_button' href='/register' style='margin-left:10px;'>
					<img src='/img/header/register.png'>
				</a>
			</li>
		<?php endif; ?>
		<li>
			<a class='link_button' id='help_button' href='/page/help' target='_blank' style='margin-left:10px;'>
				<img src='/img/header/help.png'>
			</a>
		</li>
	</ul>
</div>
<script type='text/javascript'>
	$('#home_button').hover(
		function(){$('#home_button > img').attr('src', '/img/header/home_hover.png');},
		function(){$('#home_button > img').attr('src', '/img/header/home.png');});
	$('#mypage_button').hover(
		function(){$('#mypage_button > img').attr('src', '/img/header/mypage_hover.png');},
		function(){$('#mypage_button > img').attr('src', '/img/header/mypage.png');});
	$('#login_button').hover(
		function(){$('#login_button > img').attr('src', '/img/header/login_hover.png');},
		function(){$('#login_button > img').attr('src', '/img/header/login.png');});
	$('#logout_button').hover(
		function(){$('#logout_button > img').attr('src', '/img/header/logout_hover.png');},
		function(){$('#logout_button > img').attr('src', '/img/header/logout.png');});
	$('#register_button').hover(
		function(){$('#register_button > img').attr('src', '/img/header/register_hover.png');},
		function(){$('#register_button > img').attr('src', '/img/header/register.png');});
	$('#help_button').hover(
		function(){$('#help_button > img').attr('src', '/img/header/help_hover.png');},
		function(){$('#help_button > img').attr('src', '/img/header/help.png');});
</script>