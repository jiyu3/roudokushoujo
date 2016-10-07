<?php

App::uses('AppModel', 'Model');

class ProvisionalRegistration extends AppModel {
	public $name = 'ProvisionalRegistration';
	public $validate = array(
		'email' => array(
			'rule' => 'email',
			'message' => '正しい形式のメールアドレスを入力してください。'
		),
	);
	
	/**
	 * regist_keyに対応するメールアドレスを返す関数
	 * @param  string $regist_key DBにある登録キー
	 * @return string or boolean  メールアドレス(対応するメールアドレスがない場合はfalse)
	 */
	public function getEmail($regist_key) {
		if(!$data = $this->findByRegistKey($regist_key, 'email', null, -1)) {
			return false;
		}
		return $data['ProvisionalRegistration']['email'];
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
		return $data['ProvisionalRegistration']['regist_key'];
	}
	
	/**
	 * 仮登録を実際に行う。
	 * @param  string $email 登録するメールアドレス
	 * @return boolean       登録に成功したらtrue、そうでなければfalse
	 */
	public function provisionalCreateUser($email) {
		$key = $this->makeRegistrationKey();

		$data = array('email' => $email, 'regist_key' => $key);		
	
		if($prev_registration = $this->findByEmail($email, 'id', null, -1)) {
   			$data['id'] = $prev_registration['ProvisionalRegistration']['id'];
		}
	
		if($this->save($data)) {
			return true;
		}
		return false;
	}
	
	/**
	 * 仮登録情報をregist_keyを指定して削除する
	 * @param string $regist_key DBにある登録キー
	 */
	public function deleteProvisionalRegistration($regist_key) {
		$del_registration = $this->findByRegistKey($regist_key, 'id', null, -1);
		if(!$this->delete($del_registration['ProvisionalRegistration']['id'], true)) {
			$this->log("delete error on user id={$del_registration['ProvisionalRegistration']['id']},
				regist_key={$regist_key}", 'provisional_registration');
		}
	}
	
	/**
	 * ランダム文字列を作成する
	 * @param  string $length 生成する文字列長(半角)
	 * @return string         生成された文字列
	 */
	public function makeRegistrationKey($length = 30) {
		return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, $length);
	}
}