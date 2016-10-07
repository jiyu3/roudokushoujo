<article>
	<h2>【新規登録】</h2>
	<?php if(isset($error)) : ?>
		<?php echo $error . '<br>'; ?>
	<?php endif; ?>
	<?php	
		echo $this->Form->create('User', 
			array(
				'url' => 'add/?key=' . $regist_key,
				'inputDefaults' => array(
						'label' => 'ユーザ情報入力',
				),
				'onSubmit' => 'return userConfirm();'
			)
		);
	?>
	<div class='input'>
		<label>メールアドレス</label>
		<div style='font-size:14px; margin-top:10px;'><?php echo $email; ?></div>
	</div>
	<?php
		echo $this->Form->input(
				'email',
				array(
						'type' => 'hidden',
						'label' => 'メールアドレス',
						'default' => $email,
				)
		);
		echo $this->Form->input(
			'name',
			array(
				'type' => 'text',
				'label' => 'アカウント名'
			)
		);
		echo $this->Form->input(
			'password',
			array(
				'type' => 'password',
				'label' => 'パスワード',
				'default' => ''
			)
		);
		echo $this->Form->input(
			'send_ad_mail',
			array(
				'type' => 'checkbox',
				'label' => 'お知らせメールを受信する',
				'default' => true
			)
		);
		echo $this->Form->end('/img/button/send.png');
	?>
</article>