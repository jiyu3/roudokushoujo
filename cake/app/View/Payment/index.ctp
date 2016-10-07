<script type="text/javascript">
	function showSubmit() {
		$('#payment_notice').fadeIn(200);
		$('#due_date').fadeOut(200);
	}

	function paymentConfirm() {
		var notice = '<?php echo '以下の内容で支払いを行います。よろしいですか？'; ?> \n\n' +
			'<?php echo '金額' . ': '; ?> ' + $('#PaymentAmount').val() + '<?php echo '円'; ?>\n' +
			'<?php echo '取得コンテンツ' . ': '; ?> ' + '「<?php echo SERVICE_NAME; ?>」有料コンテンツ利用料' + '\n' + 
			'<?php echo '課金形態' . ': '; ?> ' + '月額課金（自動継続）（毎月1日に当月分支払）' + '\n' +
			'<?php echo '課金開始日' . ': '; ?> ' + '「<?php echo SERVICE_NAME; ?>」有料コンテンツ利用料' + '\n';
		if(window.confirm(notice)) {
			$('#payment_submit').css('display', 'inline').prop('disabled', 'true').text('お待ちください');
			return true;
		}
		return false;
	}
</script>
<article>
	<h2>【カード情報入力】</h2>
	<div id='available_cards'>
		<img src='/img/brands.png' width='30%' alt='<?php echo '利用可能カードは、VISA, MasterCard, JCB, Amex, DinersClubです。'; ?> ' />
	</div>

	<?php if(isset($error)) : ?>
		<p style="color:red;"><?php echo $error; ?></p>
	<?php endif; ?>

	<div id='price'>
		支払額：324円（税込）<br />
	</div>

	<?php
		echo $this->Form->create('Payment',
			array(
				'url' => "index",
				'inputDefaults' => array(
					'label' => false,
					'div' => array(
						'class' => 'payment_form'
					)
				),
				'onSubmit' => 'return paymentConfirm()'
			)
		);
		echo $this->Form->input(
			'amount',
			array(
				'type' => 'hidden',
				'default' => number_format($amount)
			)
		);
		echo $this->Form->input(
			'method',
			array(
				'type' => 'hidden',
				'default' => 'webpay'
			)
		);
	?>

	<span id="credit_card_form" class="check">
		<script src="https://checkout.webpay.jp/v2/" class="webpay-button"
			data-key="<?php echo $public_key; ?>" data-lang="ja" 
			data-partial="true" data-on-created="showSubmit">
		</script>
		<p id="due_date" class="check">
			翌月以降は毎月1日に自動的に課金されます。<br />
			※JCB, American Express, Diners Clubは近日対応予定です。
		</p>
	</span>
	<div id='payment_notice' style='display:none; margin-top:40px;'>
		<div style='color:red; margin-bottom:10px; font-weight:bold; text-align:center;'>※以下のボタンを押すと、課金が完了します。<br />↓</div>
		<?php echo $this->Form->end('/img/button/payment.png'); ?>
	</div>
	<?php if($this->App->isMobile()) : ?>
		<a class="cancel" href="/"><?php echo 'トップページへ戻る';?></a>
	<?php endif; ?>
</article>