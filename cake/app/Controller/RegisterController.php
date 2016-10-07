<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class RegisterController extends AppController {
	public $uses = array('User', 'ProvisionalRegistration');
	public $components = array('MyEmail');

	public function beforeFilter(){
		parent::beforeFilter();
		if($this->Auth->loggedIn()) {
			$this->redirect('/');
		}
		$this->set('hide_share_box', true);
	}

	/**
	 * 認証用のメールを送る為のアドレスを入力させる。
	 */
	public function index() {
		$this->set('title_for_layout', '新規登録 - ' . SERVICE_NAME);
		$this->Session->delete('Register');

		if($this->request->is('post')) {
			$email = $this->request->data['ProvisionalRegistration']['email'];
			if($this->__provisionalRegister($email)) {
				$regist_key = $this->ProvisionalRegistration->getRegistKey($email);
				if(!$regist_key) {
					$this->setErrorMessage('仮登録に失敗しました。お手数ですが、もう一度やり直して下さい。');
					return false;
				}

				$content = "このたびは「朗読少女」にご登録いただき、" . "\n" .
						"誠にありがとうございます。" . "\n" .
						"仮登録の手続きが完了しましたのでご連絡いたします。" . "\n" .
						"―――――――――――――――――――――――――――――――――――" . "\n" .
						"以下のURLをクリックして本登録に進んでください。" . "\n" .
						"URLが改行されてしまっている場合は、" . "\n" .
						"お手数ですが全てつなげて閲覧していただけますようお願い申し上げます。" . "\n" .
						"https://{$_SERVER['SERVER_NAME']}/register/add/?key={$regist_key}" . "\n\n" .
						"※このメールに心当たりが無い場合は破棄して頂いて構いません。\n";
				$sent = $this->MyEmail->send(
					$content,
					'['. SERVICE_NAME .']' . '仮登録完了',
					 $email
				);
				if(!$sent) {
					$this->setErrorMessage('仮登録に失敗しました。お手数ですが、もう一度やり直して下さい。');
					return false;
				}

				$message = '入力されたメールアドレスにメールを送りましたので、<br />メールボックスを確認してください。';
				$this->Session->setFlash($message);
				$this->Session->write('Register.from_index', true);
				$this->redirect('/register/sent');
			}
			$this->setErrorMessage('このメールアドレスは使えません。' . $email);
			return false;
		}

		$this->Session->delete('Register');
		if(!$this->Session->read('Register.register_before')) {
			$this->Session->write('Register.register_before', $this->referer());
		}
	}
	
	/**
	 * 認証用のメール送信後の画面。
	 */
	public function sent() {
		$this->set('title_for_layout', 'メール送信完了 - ' . SERVICE_NAME);
		if($this->Session->read('Register.from_index') != true) {
			$this->redirect('/');
		}
		$this->Session->delete('Register.from_index');
	}

	/**
	 * 認証後、新規登録の為にユーザ情報を入力させる。
	 */	
	public function add() {
		$this->set('title_for_layout', 'ユーザ情報入力 - ' . SERVICE_NAME);
		if(!isset($this->params['url']['key'])) {
			$this->redirect('/page/error');
		}
		$regist_key_url = $this->params['url']['key'];

		if(!$email = $this->ProvisionalRegistration->getEmail($regist_key_url)) {
			$this->redirect('/page/error');
		}

		$this->set('email', $email);
		$this->set('regist_key', $regist_key_url);

		if($this->request->is('post')) {
			if($this->__createUser($email)) {
				$this->ProvisionalRegistration->deleteProvisionalRegistration($regist_key_url);
				$content = SERVICE_NAME . "への新規登録が完了しました。" . "\n" .
					'メールアドレス' . ': ' . $email . "\n" .
					'アカウント名' . ': ' . $this->request->data['User']['name'] . "\n" .
					'パスワード' . ': ' . "安全のため表示しません" . "\n";
				$this->MyEmail->send($content, '[' . SERVICE_NAME . ']' . '新規登録完了', $email);
				$this->Session->write('Register.from_add', true);
				$this->Session->write('User.email', $email);
				$this->redirect('finish');
			} else {
				$this->setErrorMessage();
				return true;
			}
		}
	}
	
	/**
	 * 本登録の結果の画面
	 */
	public function finish() {
		$this->set('title_for_layout', '新規登録完了 - ' . SERVICE_NAME);
		$this->Auth->logout();
		if($this->Session->read('Register.from_add') != true) {
			$this->redirect('/');
		}
		$this->Session->delete('Register');
	}

	/**
	 * 認証用のメールを送信する。
	 */
	private function __send() {
		$this->__provisionalRegister($email);
	}

	/**
	 * 仮登録を行う。
	 * @param  string $email メールアドレス 
	 * @return boolean       仮登録に成功したらtru,失敗したらfalse
	 */	
	private function __provisionalRegister($email) {
		if(!$this->User->isUniqueEmail($email)) {
			return false;
		}
		return $this->ProvisionalRegistration->provisionalCreateUser($email);
	}

	/**
	 * ユーザを作成する。
	 * @param  string $email メールアドレス
	 * @return boolean       登録に成功すればtrue、そうでなければfalse
	 */
	private function __createUser($email) {
		$password = $this->request->data['User']['password'];
		$name = $this->request->data['User']['name'];
		return $this->User->createUser($email, $name, $password);
	}
}
