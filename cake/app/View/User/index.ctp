<article id='index'>
	<h2>【ユーザ情報】</h2>

	<div class='user_info'>
		<label>メールアドレス</label>
		<div><?php echo $email; ?>　<a href="/user/edit_email"><img src='/img/button/change_small.png'></a></div>
	</div>
	<div class='user_info'>
		<label>アカウント名</label>
		<div><?php echo $name; ?></div>
	</div>
	<div class='user_info'>
		<label>お知らせ情報</label>
		<div><?php echo $send_ad_mail ? '受信する' : '受信しない'; ?></div>
	</div>
	<div class='user_info'>
		<label><?php echo $paying ? '有料会員' : '無料会員' ?></label>
		<?php echo $paying ? '' : "<div><a href='/payment'><img src='/img/button/payment.png'></a></div>"; ?>
	</div>
	<div style='margin-top:-10px'>
		<a class="button" href="/user/edit" style="margin-left:auto; margin-right:auto;"><img src='/img/button/edit_user.png'></a>
	</div>
	<?php if($this->App->isMobile()) : ?>
		<div style='margin-top:20px;'>
			<a href='/'>トップページへ</a>
		</div>
		<div style='margin-top:10px;'>
			<a href='/user/logout'>ログアウト</a>
		</div>
	<?php endif; ?>
</article>