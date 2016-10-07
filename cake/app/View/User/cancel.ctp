<script type="text/javascript">
	function cancelConfirm() {
		var notice = '<?php echo '退会すると、あなたのユーザ情報の一切は削除されます。'; ?>\n' +
			'<?php echo '課金したサービスは全て利用できなくなります。'; ?>\n' +
			'<?php echo 'いかなる理由でも退会後に返金の請求はできません。'; ?>\n' +
			'<?php echo 'ユーザ情報を消しますか？'; ?> ';
		if(window.confirm(notice)) {
			return cancelConfirmAgain();
		}
		return false;
	}

	function cancelConfirmAgain() {
		var notice = '<?php echo '退会した後にユーザを復元することはできません。'; ?>\n' +
			'<?php echo '本当にいいですね？'; ?>';
		if(window.confirm(notice)) {
			return cancelConfirmFinal();
		}
		return false;
	}
	
	function cancelConfirmFinal() {
		var notice = '<?php echo '後悔しませんね？'; ?>';
		if(window.confirm(notice)) {
			$('.button').prop('disabled', 'disabled');
			return true;
		}
		return false;
	}
</script>
<article>
	<h2>【退会】</h2>
	<p>退会するためにはパスワードを以下に入力してください。</p>
	<?php if(isset($error)) : ?>
		<div style="color:red; margin-top:20px;"><?php echo $error; ?></div>
	<?php endif; ?>
	<?php
		echo $this->Form->create('User',
			array(
				'url' => 'cancel',
				'inputDefaults' => array(
					'label' => false,
				),
				'onSubmit' => 'return cancelConfirm();'
			)
		);
		echo $this->Form->input(
			'password',
			array(
				'type' => 'password',
			)
		);
		echo $this->Form->end('/img/button/user_cancel.png');
	?>
	<a href="/">トップページへ</a>
</article>