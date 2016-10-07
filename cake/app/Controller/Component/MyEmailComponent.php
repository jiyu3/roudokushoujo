<?php

App::uses('Component', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class MyEmailComponent extends Component {
	/**
	 * メールを送信する。
	 * @param  string $content 本文
	 * @param  string $subject 件名
	 * @param  string $to      送信先メールアドレス
	 * @param  string $from    送信元メールアドレス
	 * @return boolean         送信に成功すればtrue、そうでなければfalse
	 */
	public function send($content, $subject, $to, $from=NULL) {
		if(!isset($from)) {
			$from = COMPANY_EMAIL;
		}
		$email_sender = new CakeEmail();
		$email_sender->config('noumenon');
		$sent = $email_sender->from(array($from => PROVIDER_NAME))
			->to($to)
			->subject($subject)
			->emailFormat('text')
			->template('default')
			->send($content);				
		if(!$sent) {
			return false;
		}
		return true;
	}
}