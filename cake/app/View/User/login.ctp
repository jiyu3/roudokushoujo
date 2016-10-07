<article>
	<h2>【ログイン】</h2>
	<?php if(isset($error)) : ?>
		<div style="color:red;">
			<?php echo $error; ?>
		</div>
	<?php endif; ?>
	<?php echo $this->Form->create('User');
		echo $this->Form->input(
			'email',
			array(
				'type' => 'text',
				'label' => 'メールアドレス',
				'default' => isset($email) ? $email : ''
			)
		);
		echo $this->Form->input(
			'password',
			array(
				'type' => 'password',
				'label' => 'パスワード',
			)
		);
	?>
	<div style='margin-top:-15px; font-size:12px; margin-bottom:20px;'>
		<a href="/user/password_reset">パスワードを忘れた方はこちら</a>
	</div>
	<?php
		echo $this->Form->input(
			'remember_me',
			array(
				'type' => 'hidden',
				'label' => 'ログインしたままにする',
				'default' => '1'
			)
		);
	?>
	<?php echo $this->Form->end(__('/img/button/login.png')); ?>
	<div>
		<p><a href="/register/index">ユーザ登録をされていない方はこちら</a></p>
	</div>
	<?php if($this->App->isMobile()) : ?>
		<div style='margin-top:20px;'>
			<a href='/'>トップページへ</a>
		</div>
	<?php endif; ?>
</article>