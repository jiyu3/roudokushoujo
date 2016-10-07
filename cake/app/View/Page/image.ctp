<article>
	<h2>【アップロード】</h2>
	<p>間違ってファイルをアップロードした場合は、<a href='/page/init/image'>初期化してください</a>。</p>
	<?php if(isset($result)) : ?>
		<p>アップロードが完了しました。<a href='/play'>確認する</a></p>
	<?php else : ?>
		<form action="/page/image" method="post" enctype="multipart/form-data">
			<div id='select_audio' onchange='$("#upload_image, #submit").css("display", "inline");'>
				<p>画像をアップロードする作品を選択して下さい。<span style='color:red;'>画像の拡張子はpngにして下さい。</span></p>
				<select name="title">
					<?php foreach($titles as $key => $title) : ?>
						<option value="<?php echo $key; ?>"><?php echo $title; ?></option>
					<?php endforeach; ?>
				</select></p>
			</div>
			<div id='upload_image'>
				<p>次に、画像をアップロードして、何ページから何ページまで表示するか指定して下さい。</p>
				<?php for($i=0; $i<15; $i++) : ?>
					<?php echo "<input name='image[]' type='file' style='width:200px;'>を<input name='image_start[]' class='image' type='number' style='width:50px;'>ページから<input name='image_end[]' class='image' type='number' style='width:50px;'>ページまで表示<br />"; ?>
				<?php endfor; ?>
			</div>
			<div id='submit'>
				<p>必要項目を全て入れ終わったら、アップロードしてください:</p>
				<input type="submit" value="ファイルをアップロード" style="margin-bottom:100px;" />
			</div>
		</form>
	<?php endif; ?>
</article>