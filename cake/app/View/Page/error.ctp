<article>
	<h2>【エラー】</h2>
	<?php empty($error) ? $error = $this->Session->flash() : ''; ?>
	<?php if(!empty($error)) : ?>
		<p><?php echo $error; ?></p>
	<?php else : ?>
		<p>エラーが発生しました。恐れ入りますが、もう一度最初からやり直して下さい。</p>
	<?php endif; ?>
	<a class="button" href="/"><?php echo 'トップページへ'; ?></a>
</article>