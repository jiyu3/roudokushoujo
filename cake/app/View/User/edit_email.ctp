<article>
	<h2>【メールアドレスの変更】</h2>
	<p>新しいメールアドレスを以下に入力してください。</p>
	<?php if(isset($error)) : ?>
		<p style="color:red;"><?php echo $error; ?></p>
	<?php endif; ?>
	<?php
		echo $this->Form->create('User',
			array(
				'url' => 'edit_email',
				'inputDefaults' => 
				array(
					'label' => false,
				),
			)
		);
		echo $this->Form->input(
			'regist_email',
			array(
				'type' => 'email',
				'required' => 'required'
			)
		);
		echo $this->Form->end('/img/button/change.png');
	?>
	<a href="/user/index">ユーザ情報ページに戻る</a>
</article>