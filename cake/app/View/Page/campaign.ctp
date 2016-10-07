<?php if($_GET['logged_in'] && !$_GET['is_paying']) : ?>
<a href='/payment'>324円で</a>、<br />時をかける少女が聴けますよ！
<?php elseif(!$_GET['logged_in']) : ?>
時をかける少女が聴けますよ！<br />
まずは<a href='/register'>新規登録してみて下さい</a>。<br />
登録済みの方は<a href='<?php echo $this->Html->url("/user/login"); ?>'>ログインして下さい</a>。
<?php endif; ?>