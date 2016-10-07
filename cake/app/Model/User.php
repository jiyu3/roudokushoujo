<?php

use Guzzle\Http\Message\Request;
App::uses('SimplePasswordHasher', 'Controller/Component/Auth', 'AppModel', 'Model');

class User extends AppModel {
	public $name = 'User';
	public $validate = array(
		'email' => array(
			'1' => array (
				'rule' => 'isUnique',
				'message' => 'このメールアドレスはすでに使用されています。',
			),
			'2' => array (
				'rule' => 'email',
				'message' => '正しいメールアドレスを入力してください。',
			)
		),
		'name' => array(
			'rule' => array('between', 1, 20),
			'message' => '氏名は20文字以内にしてください。',
			'allowEmpty' => false,
			'last' => false
		),
		'password' => array(
			'rule' => array('between', 8, 20),
			'message' => 'パスワードは8文字以上20文字以下にしてください。',
			'allowEmpty' => false
		)
	);

	public $validate_edit = array(
		'email' => array(
			'1' => array (
				'rule' => 'isUnique',
				'message' => 'このメールアドレスはすでに使用されています。'
			),
			'2' => array (
				'rule' => 'email',
				'message' => '正しいメールアドレスを入力してください。',
			)
		),
		'name' => array(
			'rule' => array('between', 1, 20),
			'message' => '氏名は20文字以内にしてください。',
			'allowEmpty' => false,
			'last' => false
		),
		'password' => array(
			'rule' => array('between', 8, 20),
			'message' => 'パスワードは8文字以上20文字以下にしてください。',
			'allowEmpty' => true
		)
	);

	/**
	 * saveの前にパスワードをsha1で40桁の暗号に変える。
	 */
	public function beforeSave($option = array()) {
		parent::beforeSave();
		if(isset($this->data['User']['password']) && strlen($this->data['User']['password']) < 20) {
			$passwordHasher = new SimplePasswordHasher();
			$this->data['User']['password'] = $passwordHasher->hash($this->data['User']['password']);
		}
	}

	/**
	 * saveした際に、セッションの中身も更新する。
	 * セッションはセキュリティ的に取ってきても問題ない情報だけ格納する。
	 */
	public function afterSave($created, $option = array()) {
		if(!$created) {
			if(isset($_SESSION['Auth']['User'])) {
				$user = $this->findById($_SESSION['Auth']['User']['id'],
					array('id','email','name','send_ad_mail'), null, -1);
				$_SESSION['Auth'] = $user;
			}
		}
	}
	
	/**
	 * 実際にユーザを本登録する。
	 * @param  string $email       メールアドレス
	 * @param  string $name        アカウント名
	 * @param  string $password    パスワード(半角8文字以上)
	 * @return boolean             ユーザ登録が成功したらtrue、失敗したらfalse
	 */
	public function createUser($email, $name, $password) {
		if(empty($password)) {
			return false;
		}

		$user = array(
			'User' => array(
				'name' => $name,
				'email' => $email,
				'password' => $password,
			)
		);
		if($this->save($user)) {
			return true;
		}
		return false;
	}
	
	/**
	 * そのメールアドレスアドレスがユニークかどうか、メールアドレスを登録・変更する前にチェックする。
	 * @param  string $email チェックするメールアドレス
	 * @return boolean       メールアドレスがユニークなtrue、そうでなければfalse
	 */
	public function isUniqueEmail($email) {
		if($this->findByEmail($email, 'id', null, -1)) {
			return false;
		}
		return true;
	}

	/**
	 * メールアドレスの仮変更を行う。
	 * @param  string $email        変更前のメールアドレス
	 * @param  string $regist_email 仮変更するメールアドレス
	 * @return string or boolean    登録したregist_key。登録できなかった場合はfalse を返す。
	 */
	public function provisionalEditEmail($email, $regist_email) {
		$regist_key = $this->makeRegistrationKey();
		$user = $this->findByEmail($email, 'id', null, -1);
		$user['User']['regist_email'] = $regist_email;
		$user['User']['regist_key'] = $regist_key;
		if($this->save($user, false)) {
			return $regist_key;
		}
		return false;
	}

	/**
	 * regist_keyを指定してUserテーブルを検索する。取得するのはidとregist_emailのみ。
	 * @param  string $regist_key メールアドレス変更URLを生成する為のキー
	 * @return array              取得できたユーザの配列。取得に失敗したらfalse
	 */
	public function findUserWithRegistEmail($regist_key) {
		return $this->findByRegistKey($regist_key, 'regist_key', null, -1); 
	}

	/**
	 * regist_keyを指定してメールアドレスを実際に変更する。
	 * @param  int    $id         ユーザID
	 * @param  string $regist_key メールアドレス変更URLを生成する為のキー
	 * @return boolean            trueなら成功、falseなら失敗
	 */
	public function changeEmail($id, $regist_key) {
		if(!$user = $this->findByRegistKey($regist_key, array('id', 'regist_email'), null, -1)) {
			return false;
		}

		$user['User']['email'] = $user['User']['regist_email'];
		$user['User']['regist_key'] = null;
		$user['User']['regist_email'] = null;
		if($this->save($user, false)) {
			return true;
		}
		return false;
	}
	
	/**
	 * 入力されたパスワードが正しいか確認する。
	 * @param  int    $id       パスワードを持つユーザID
	 * @param  string $password 入力されたパスワード
	 * @return boolean          正しかったらtrue、そうでなければfalse
	 */
	public function passwordCheck($id, $password) {
		$passwordHasher = new SimplePasswordHasher();
		$password = $passwordHasher->hash($password);
		if($this->findByIdAndPassword($id, $password, 'id', null, -1)) {
			return true;
		}
		return false;
	}

	/**
	 * パスワードを変更する。
	 * @param  string  $email      メールアドレス
	 * @param  string  $password   パスワード
	 * @param  string  $regist_key 仮登録キー
	 * @return boolean          登録に成功すればtrue、そうでなければfalse
	 */
	public function changePassword($email, $password, $regist_key) {
		$user = $this->findByEmailAndRegistKey($email, $regist_key, 'id', null, -1);
		if(!$user) {
			return false;
		}

		$user['User']['password'] = $password;
		if($this->save($user)) {
			return true;
		}
		return false;
	}

	/**
	 * メールアドレスに対応するregist_keyを返す関数。
	 * @param  string $email     探すメールアドレス
	 * @return string or boolean regist_key(対応するregist_keyがない場合はfalse)
	 */
	public function getRegistKey($email) {
		if(!$data = $this->findByEmail($email, 'regist_key', null, -1)) {
			return false;
		}
		return $data['User']['regist_key'];
	}

	/**
	 * regist_keyに対応するメールアドレスを返す関数
	 * @param  string $regist_key DBにある登録キー
	 * @return string or boolean  メールアドレス(対応するメールアドレスがない場合はfalse)
	 */
	public function getEmail($regist_key) {
		if(!$data = $this->findByRegistKey($regist_key, 'email', null, -1)) {
			return false;
		}
		return $data['User']['email'];
	}

	/**
	 * ランダム文字列を生成する。
	 * @param  string $length 生成する文字列長(半角)
	 * @return string 生成された文字列
	 */
	public function makeRegistrationKey($length = 30) {
		return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, $length);
	}
}