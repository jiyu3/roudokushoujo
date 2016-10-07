<script type="text/javascript" async src="//platform.twitter.com/widgets.js"></script>
<div id='sns'>
	<a id="twitter" title="Twitterでシェア" href="" <?php echo $is_mobile ? "target='_blank'" : ''; ?>><img src='/img/sns/twitter.png'></a>
	<a id="facebook" title="Facebookでシェア" href=""><img src='/img/sns/facebook.png'></a>
	<a id="line" title="LINEでシェア" href="" target='_blank'><img src='/img/sns/line.png'></a>
	<a id="pinterest" title="Pinterestでシェア" href="" target='_blank'><img src='/img/sns/pinterest.png'></a>
	<a id="email" title="メールで問い合わせ" href="mailto:<?php echo COMPANY_EMAIL; ?>"><img src='/img/sns/email.png'></a>
</div>
<script type="text/javascript" async src="//platform.twitter.com/widgets.js"></script>
<script type='text/javascript'>
	var share_url = 'https://<?php echo $_SERVER['SERVER_NAME']; ?>/play/index/' + encodeURIComponent(encodeURIComponent(document.title.slice(7)));
	var share_text = '<?php echo CHARACTER_NAME; ?>が『' + document.title.slice(7) + '』を朗読します。';
	var via = "otohashiori";
	var related = "";
	var hashtags = "朗読少女";
	var fb_onclick = "window.open(this.href, 'FBwindow', 'width=650, height=450, menubar=no, toolbar=no, scrollbars=yes'); return false;";
	$('#twitter').attr('href', 'https://twitter.com/intent/tweet?url=' + share_url + '&text=' + share_text + '&via=' + via + '&related=' + related +
		'&hashtags=' + hashtags);
	$('#facebook').attr({'href':'http://www.facebook.com/share.php?u=' + share_url, 'onclick':fb_onclick});
	$('#line').attr('href', 'http://line.me/R/msg/text/?' + share_text + share_url);
	$('#pinterest').attr('href', "http://www.pinterest.com/pin/create/button/?url=" + share_url + "&media=" + 'https://<?php echo $_SERVER['SERVER_NAME']; ?>/img/title.jpg' + "&description=" + share_text);
</script>