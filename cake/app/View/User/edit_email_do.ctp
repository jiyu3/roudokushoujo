<article class="big" style="text-align:center;">
	<h2>【メールアドレスの再設定】</h2>
	<p>メールアドレスを変更するためにパスワードを入力してください。</p>
	<?php if(isset($error)) : ?>
		<p style="color:red;"><?php echo $error; ?></p>
	<?php endif; ?>
	<?php
		echo $this->Form->create('User',
			array(
				'url' => $url,
				'inputDefaults' =>
				array(
					'label' => false,
				),
			)
		);
		echo $this->Form->input(
			'password',
			array(
				'type' => 'password',
			)
		);
		echo $this->Form->end('/img/button/change.png');
	?>
</article>