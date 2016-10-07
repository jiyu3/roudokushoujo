<?php if($this->App->isMobile()) : ?>
	<p><a href='/'>トップページへ</a></p>
<?php endif; ?>
<table>
	<colgroup>
		<col width="160">
		<col width="500">
	</colgroup>
	<tbody>
		<tr>
			<td>事業者名<br />代表者・運営責任者</td>
			<td><a href='http://noumenon.jp'>ヌーメノン合同会社</a>　村濱　章司</td>
		</tr>
		<tr>
			<td>住所・電話番号</td>
			<td>東京都新宿区西早稲田2丁目18番23号　050-5585-1095</td>
        </tr>
		<tr>
			<td>お問い合わせ先</td>
			<td><a href='mailto:<?php echo COMPANY_EMAIL; ?>'><?php echo COMPANY_EMAIL; ?></a></td>
        </tr>
		<tr>
			<td>商品詳細・料金</td>
			<td>本商品は、<a href="http://rodokushojo.jp/site/infos/whatisrodokushojo/" target="_blank">iPhone/iPadアプリ「<?php echo SERVICE_NAME; ?>」</a>のWeb版です。
				月々300円（税抜）を支払うことにより、乙葉しおりの朗読をいつでも視聴することができます。</td>
        <tr>
			<td>商品代金以外に必要な費用</td>
			<td>なし</td>
		</tr>
		<tr>
			<td>商品の受け渡し時期・方法</td>
			<td>クレジットカード決済の手続き終了後、直ちにご利用いただけます。</td>
		</tr>
		<tr>
			<td>事業者の責任<br />不良品の取扱条件</td>
			<td>特に定めない限り責任の範囲は法令によるものとします。商品の性質上、法令の定めがある場合を除いて返品は承っておりません。</td>
		</tr>
		<tr>
			<td>返品・解約条件</td>
			<td>電子商品としての性質上、法令の定めがある場合を除いて返品には応じられません。
			解約（課金停止）を希望される場合、解約希望月の末日までに<a href='/payment/cancel'>月額課金停止フォーム</a>で課金停止処理を行って下さい。
			末日までにメールを送信した場合、その月の月末での解約となり、翌月以降の課金はされません。
			アカウント自体を削除したい場合には、<a href='/user/cancel'>アカウント削除フォーム</a>でアカウントの削除をして下さい（課金をしている場合、課金も同時に停止されます）。</td>
		</tr>
		<tr>
			<td>不良品の取扱条件</td>
			<td>商品の性質上、法令の定めがある場合を除いて返品は承っておりません。</td>
		</tr>
		<tr>
			<td>代金の支払時期・方法</td>
			<td>支払い時期は、初月のお支払いの際は即日引き落とされ、翌月以降は毎月1日にその月の代金が引き落とされます。
			支払い方法はクレジットカード決済のみ対応しています。</td>
		</tr>
	</tbody>
</table>
<?php if($this->App->isMobile()) : ?>
	<p><a href='/'>トップページへ</a></p>
<?php endif; ?>