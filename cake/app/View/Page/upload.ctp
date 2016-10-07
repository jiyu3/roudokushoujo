<article>
	<h2>【アップロード】</h2>
	<p>間違ってファイルをアップロードした場合は、<a href='/page/init/upload'>初期化してください</a>。</p>
	<?php if(isset($result)) : ?>
		<p>アップロードが完了しました。次に <a href='/play/make'>こちらをクリックして</a>wavをjsonに変換して下さい。</p>
	<?php else : ?>
		<form action="/page/upload" method="post" enctype="multipart/form-data">
			<p>str, m4a, wav, txtを順にアップロードしてください:</p>
			<input name="userfile[]" type="file" /><br />
			<input name="userfile[]" type="file" /><br />
			<input name="userfile[]" type="file" /><br />
			<input name="userfile[]" type="file" /><br /><br />
			<p>読後にしおりちゃんが吹き出しで喋るTwitterのURLを入力して下さい（任意）:</p>
			<input name="twitter_link" type="url" placeholder='関連twitterのURLを入力' /><br />
			<p>必要項目を全て入れ終わったら、アップロードしてください:</p>
			<input type="submit" value="ファイルをアップロード" />
		</form>
	<?php endif; ?>
</article>