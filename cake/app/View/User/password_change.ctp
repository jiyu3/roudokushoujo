<script type="text/javascript">
	function userConfirm() {
		if($('#UserPassword').val() != $('#UserPasswordConfirm').val()) {
			alert('<?php echo '２回入力したパスワードが同じものではありません。修正してください。'; ?>');
			return false;
		}
	}
</script>
<article>
	<h2>パスワード再設定</h2>
	<?php if(isset($error)) : ?>
		<p style="color:red;"><?php echo $error; ?></p>
	<?php endif; ?>
	<?php
		echo $this->Form->create('User',
			array(
				'url' => "password_change/?key={$regist_key}",
				'onSubmit' => 'return userConfirm();'
			)
		);
		echo $this->Form->input(
			'email',
			array(
				'type' => 'hidden',
				'default' => $email
			)
		);
		echo $this->Form->input(
			'regist_key',
			array(
				'type' => 'hidden',
				'default' => $regist_key
			)
		);
		echo $this->Form->input(
			'password',
			array(
				'type' => 'password',
				'label' => '新しいパスワード',
			)
		);
		echo $this->Form->input(
			'password_confirm',
			array(
				'type' => 'password',
				'label' => '新しいパスワード（確認）',
			)
		);
		echo $this->Form->end('/img/button/change.png');
	?>
</article>