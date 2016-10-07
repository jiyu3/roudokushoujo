<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class UserController extends AppController {
	public $uses = array('User', 'Payment');
	public $components = array('MyEmail', 'Cookie');
	
	/**
	 * ログインが必要なページを指定する。
	 * 該当ページに未ログイン状態でアクセスすると、
	 * /user/loginにリダイレクトされる。
	 */
	public function beforeFilter() {
		if($this->action === 'edit') {
			$this->User->validate = $this->User->validate_edit;
		}
		parent::beforeFilter();
 		$this->Auth->deny('index', 'edit', 'edit_email', 'edit_email_sent', 'edit_email_do',
 			'edit_email_finish', 'finish', 'cancel');
 		$this->Security->unlockedActions = array('login');
	}

	/**
	 * ログインを行う。
	 */
	public function login() {
		$this->set('title_for_layout', 'ログイン - ' . SERVICE_NAME);

		if($this->Auth->loggedIn()) {
			$this->redirect('/');
		}
		$redirect_url = $this->Session->read('redirect_url');
		$request_url = $this->Session->read('request_url');
		if($redirect_url === null) {
			if($request_url === null) {
				$this->Session->write('redirect_url', $this->Auth->redirect());
			} else {
				$this->Session->write('redirect_url', $request_url);
			}
		}

		if($this->Cookie->check('Auth')) {
			$this->request->data = $this->Cookie->read('Auth');
			if($this->Auth->login()) {
				if(!$this->Auth->user('deleted')) {
					$this->Session->write('from.login', true);
					$this->redirect($this->Session->read('redirect_url'));
				}
			}
			$this->Cookie->delete('Auth');
		}

		if($this->request->is('post')) {
			if($this->Auth->login()) {
				if(!$this->Auth->user('deleted')) {
					$data = $this->request->data;
					$this->Cookie->write('Auth', $data, true, '+1000 years');
					$this->Session->delete('User.email');
					$this->Session->write('from.login', true);
					$this->redirect($this->Session->read('redirect_url'));
				}
				$this->Auth->logout();
			}
			$this->set('error', 'ユーザ名かパスワードが違います。');
		}

		$this->set('email', $this->Session->read('User.email'));
	}
	
	/**
	 * ログアウトを行う。
	 */
	public function logout() {
		$this->Cookie->destroy();
		$this->Session->destroy();
		$this->redirect($this->Auth->logout());
	}
	
	/**
	 * ユーザ情報を表示する。
	 * @param array $user ユーザ情報。ニックネーム、メールアドレス、生年月日、性別を含む。   
	 */
	public function index($id = null) {
		$this->set('title_for_layout', 'ユーザ情報 - ' . SERVICE_NAME);

		$this->Session->delete('User');

		$user = $this->User->findById($this->Auth->user('id'), array('id', 'name', 'email'), null);
		$user['User'] = $this->Auth->user();
		$this->set('paying', $this->Payment->isPaying($this->Auth->user('id')));
		if($user) {
			$this->__setUserData($user['User']);
		} else {
			$this->logout();
			$this->redirect('login');
		}
	}

	/**
	 * ユーザ情報を編集する。
	 */
	public function edit() {
		$this->set('title_for_layout', 'ユーザ情報編集 - ' . SERVICE_NAME);

		$this->Session->delete('User');

		$user = $this->Auth->user();
		$this->__setUserData($user);
		if($this->request->is('post')) {
			$this->User->id = $user['id'];
			if($this->User->save($this->request->data)) {
				$this->Session->write('User.from_edit', true);
				$this->redirect('edit_finish');
			} else {
				$this->setErrorMessage($this->User->validationErrors);
			}
		}
	}

	/**
	 * ユーザ情報を実際に更新する。
	 */
	public function edit_finish() {
		$this->set('title_for_layout', '編集完了 - ' . SERVICE_NAME);

		if($this->Session->read('User.from_edit') != true) {
			$this->redirect('index');
		}
		$this->Session->delete('User');
	}

	/**
	 * メールアドレスを変更する。
	 */
	public function edit_email() {
		$this->set('title_for_layout', 'メールアドレス変更 - ' . SERVICE_NAME);

		$this->Session->delete('User');

		if($this->request->is('post')) {
			$regist_email = $this->request->data['User']['regist_email'];
			if(!$this->User->isUniqueEmail($regist_email)) {
				$this->set('error', 'このメールアドレスは使えません。');
				return false;
			}
			$this->User->set($this->request->data);
			if(!$regist_email) {
				$this->set('error', '正しいメールアドレスを入力してください。');
				return false;
			}				
			$user = $this->Auth->user();
			$regist_key = $this->User->provisionalEditEmail($user['email'], $regist_email);
			if(!$regist_key) {
				$this->setErrorMessage();
				return false;
			}
			$content = sprintf('%sさん、', $user['name']) . "\n\n\n" .
				'登録されているメールアドレスを変更します。' . "\n" .
				'旧メールアドレス' . ': ' . $user['email'] . "\n" .
				'以下のリンクをクリックしてください。' . "\n" .
				"https://{$_SERVER['SERVER_NAME']}/user/edit_email_do/?key={$regist_key}";
			$sent = $this->MyEmail->send($content, '['. SERVICE_NAME .']' . 'メールアドレス変更', $regist_email);
			if(!$sent) {
				$this->setErrorMessage();
				return false;
			}
			$this->Session->setFlash(sprintf("「%s」にメールアドレス変更用URLが記載されたメールを送信しました。メールボックスを確認してください。", $regist_email));
			$this->Session->write('User.from_edit_email', true);
			$this->redirect('edit_email_sent');
		}
	}

	/**
	 * メールアドレスを変更する為の承認メールを送る。
	 */
	public function edit_email_sent() {
		$this->set('title_for_layout', '送信完了 - ' . SERVICE_NAME);

		if(!$this->Session->read('User.from_edit_email')) {
			$this->redirect('index');
		}
		$this->Session->delete('User');
	}

	/**
	 * 変更したメールアドレスをDBに保存する。
	 */
	public function edit_email_do() {
		$this->set('title_for_layout', 'メールアドレス変更 - ' . SERVICE_NAME);

		$this->Session->delete('User');

		if(!isset($this->params['url']['key'])) {
			$this->redirect('/page/error');
		}

		$this->set('url', $this->here . '/?key=' . $this->params['url']['key']);

		if($this->request->is('post')) {
			if(!$this->User->passwordCheck($this->Auth->user('id'), $this->request->data['User']['password'])) {
				$this->set('error', 'パスワードが違います。正しいパスワードを入力してください。');
				return false;
			}
			if($this->User->changeEmail($this->Auth->user('id'), $this->params['url']['key'])) {
				$this->Session->write('User.from_edit_email_do', true);
				$this->redirect('edit_email_finish');
			}
			$this->setErrorMessage();
			return false;
		}
		if(!$this->User->findUserWithRegistEmail($this->params['url']['key'])) {
			$this->redirect('/page/error');
		}
	}

	/**
	 * メールアドレス変更終了画面を表示する。
	 */
	public function edit_email_finish() {
		$this->set('title_for_layout', '変更完了 - ' . SERVICE_NAME);

		if(!$this->Session->read('User.from_edit_email_do')) {
			$this->redirect('index');
		}
		$this->Session->delete('User');
	}

	/**
	 * 退会処理（ユーザの論理削除）を行う。
	 */
	public function cancel() {
		$this->set('title_for_layout', '退会 - ' . SERVICE_NAME);

		$this->Session->delete('User');

		if($this->request->is('post')) {
			if(!$this->User->passwordCheck($this->Auth->user('id'), $this->request->data['User']['password'])) {
				$this->set('error', 'パスワードが違います。正しいパスワードを入力してください。');
				return false;
			}
			$this->Session->write('User.from_cancel', true);

			$this->User->id = $this->Auth->user('id');
			$this->request->data['User']['email'] = str_replace('@', '#', $this->Auth->user('email'));
			if($this->Payment->isPaying($this->Auth->user('id'))) {
				if($this->Payment->cancel($this->Auth->user('id')) && $this->User->save($this->request->data, false)) {
					$this->Auth->logout();
					$this->redirect('cancel_finish');
				}
			} else {
				if($this->User->save($this->request->data, false)) {
					$this->Auth->logout();
					$this->redirect('cancel_finish');
				}
			}
			$this->redirect('/page/error');
		}
	}

	/**
	 * 退会終了ページ
	 */
	public function cancel_finish(){
		$this->set('title_for_layout', '退会完了 - ' . SERVICE_NAME);

		if(!$this->Session->read('User.from_cancel')) {
			$this->redirect('/');
		}
		$this->Cookie->delete('Auth');
		$this->Session->destroy();
	}
	
	/**
	 * パスワードの再設定用のメールを送る。
	 */
	public function password_reset() {
		$this->set('title_for_layout', 'パスワード変更 - ' . SERVICE_NAME);

		if($this->Auth->loggedIn()) {
			$this->redirect($this->Auth->redirect());
		}
		$this->Session->delete('User');

		if($this->request->is('post')) {
			$user = $this->User->findByEmail($this->request->data['User']['email'], array('id','email'), null, -1);
			if($user) {
				$user['User']['regist_key'] = $this->User->makeRegistrationKey();
				$result = $this->User->save($user);
				$regist_key = $this->User->getRegistKey($user['User']['email']);
				if(!$result || !$regist_key) {
					$this->setErrorMessage();
					return false;
				}

				$message = 'パスワード再設定用URLをメールで送信しました。<br />メールを確認してください。';
				$content = "以下のURLをクリックしてパスワードを変更してください。" . "\n" . 
					"https://{$_SERVER['SERVER_NAME']}/user/password_change/?key=$regist_key";
				$sent = $this->MyEmail->send($content, '['. SERVICE_NAME .']' . 'パスワード変更用メール', $user['User']['email']);

				if(!$sent) {
					$this->setErrorMessage();
					return true;
				}
				$this->Session->setFlash($message);
				$this->Session->write('User.from_password_reset', true);
				$this->redirect('password_reset_sent');
			} else {
				$error = '入力したメールアドレスではユーザが見つかりません。<br />正しいメールアドレスを入れてください。';
				$this->set('error', $error);
			}
		}
	}

	/**
	 * パスワードの再設定用のメールの送信完了画面。
	 */
	public function password_reset_sent() {
		$this->set('title_for_layout', '送信完了 - ' . SERVICE_NAME);

		if($this->Session->read('User.from_password_reset') != true) {
			$this->redirect('index');
		}
		$this->Session->delete('User');
	}

	/**
	 * パスワードの変更を行う。
	 */
	public function password_change() {
		$this->set('title_for_layout', 'パスワード変更 - ' . SERVICE_NAME);

		$this->Auth->logout();

		if($this->request->is('post')) {
			if($this->User->changePassword(
				$this->request->data['User']['email'],
				$this->request->data['User']['password'],
				$this->request->data['User']['regist_key']
			)) {
				$user = $this->User->findByRegistKey($this->request->data['User']['regist_key'], 'id', null, -1);
				$user['User']['regist_key'] = null;
				if($this->User->save($user)) {
					$this->Session->write('User.from_password_change', true);
					$this->redirect('password_change_finish');
				}
			}
			$this->__findAndsetRegistKeyAndEmail($this->params['url']['key']);
			$this->setErrorMessage();
			return false;
		}

		list($regist_key_url, $email) = $this->__findAndsetRegistKeyAndEmail($this->params['url']['key']);
		if(!$regist_key_url || !$email) {
			$this->redirect('/');
		}
	}

	/**
	 * パスワード変更完了ページ。
	 */
	public function password_change_finish(){
		$this->set('title_for_layout', '変更完了 - ' . SERVICE_NAME);

		if($this->Session->read('User.from_password_change') != true) {
			$this->redirect('index');
		}
		$this->Session->delete('User');
	}

	/**
	 * DBから取得したユーザ情報をビューにセットする。
	 * @param array $user ユーザ情報の配列
	 */
	private function __setUserData($user) {
		foreach($user as $key => $value) {
			$this->set($key, $value);
		}
	}

	/**
	 * regist_key を元に、emailを取得し、regist_keyと共にビューにセットする。
	 * @param  string $regist_key キー文字列
	 * @return array              セットしたregist_key_urlとemailの配列
	 */
	private function __findAndsetRegistKeyAndEmail($regist_key) {
		$email = $this->User->getEmail($regist_key);
		$this->set('email', $email);
		$this->set('regist_key', $regist_key);
		return array($regist_key, $email);
	}
}