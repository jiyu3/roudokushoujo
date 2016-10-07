<article>
	<h2>【パスワード再設定】</h2>

	<?php if(isset($error)) : ?>
		<div style="margin-bottom:20px; text-align:center; color:red"><?php echo $error; ?></div>
	<?php else : ?>
		<div style="margin-bottom:20px; text-align:center;">パスワード再設定用のURLを送りますので、<br />登録したメールアドレスを入力してください。</div>
	<?php endif; ?>

	<?php 
		echo $this->Form->create('User',
			array(
				'url' => 'password_reset',
			)
		);
		echo $this->Form->input(
			'email',
			array(
					'type' => 'email',
					'label' => false,
			)
		);
		echo $this->Form->end('/img/button/send.png');
	?>
</article>