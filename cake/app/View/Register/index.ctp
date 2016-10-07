<article>
	<h2>【無料会員登録】</h2>
	<p>※登録済みの方は<a href='/user/login'>こちら</a>からログインできます。</p>
	<p>無料会員登録を行います。お使いのメールアドレスを入力してください。</p>
	<?php if(isset($error)) : ?>
		<div style="color:red; margin-top: 20px;">
			<?php echo $error . '<br>'; ?>
		</div>
	<?php endif; ?>
	<?php
		echo $this->Form->create('ProvisionalRegistration',
			array(
				'url' => 'index',
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
	