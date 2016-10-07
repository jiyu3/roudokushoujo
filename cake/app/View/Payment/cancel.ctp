<script type="text/javascript">
	function cancelConfirm() {
		var notice = '<?php echo '課金停止すると、ほとんどの朗読コンテンツを自由に聴くできなくなります。'; ?>\n' +
			'<?php echo '課金停止した瞬間に月額課金は無効となり、たとえ月の途中であっても無課金ユーザと同様に扱われます。'; ?>\n' +
			'<?php echo '再度課金する際には、たとえ以前支払っていたとしても、再びその月分の課金をする必要があります。'; ?>\n' +
			'<?php echo '課金を停止しますか？'; ?> ';
		if(window.confirm(notice)) {
			return cancelConfirmAgain();
		}
		return false;
	}

	function cancelConfirmAgain() {
		var notice = '<?php echo 'いったん課金停止すると、取り消すことができません。'; ?>\n' +
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
	<h2>【課金停止】</h2>
	<p>課金停止するためにはパスワードを以下に入力してください。</p>
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
		echo $this->Form->end('/img/button/payment_cancel.png');
	?>
	<a href="/"><?php echo 'トップページへ'; ?></a>
</article>