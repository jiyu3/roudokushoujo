<script type="text/javascript">
	function userConfirm() {
		if($('#UserPassword').val() != $('#UserPasswordConfirm').val()) {
			alert('<?php echo '２回入力したパスワードが同じものではありません。修正してください。'; ?>');
			return false;
		}
	}
</script>
<article>
	<h2>ユーザ情報の変更</h2>
	<?php
		echo $this->Form->create('User',
			array(
				'url' => 'edit',
				'inputDefaults' => array(
					'label' => false,
				),
				'onSubmit' => 'return userConfirm();'
			)
		);
		echo $this->Form->input(
			'name',
			array(
				'type' => 'text',
				'label' => 'アカウント名',
				'default' => $name
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
			'password_confirm',
			array(
				'type' => 'password',
				'label' => 'パスワード（確認）',
				'default' => ''
			)
		);
		echo $this->Form->input(
			'send_ad_mail',
			array(
				'type' => 'checkbox',
				'label' => 'お知らせメールを受信する',
				'default' => $send_ad_mail
			)
		);
	?>
	<?php
		echo $this->Form->end('/img/button/change.png');
	?>
	<a href="/user/index">前のページに戻る</a>
</article>