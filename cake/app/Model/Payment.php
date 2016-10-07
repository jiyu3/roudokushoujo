<?php

use WebPay\Exception\CardException;
App::uses('AppModel', 'Model');

class Payment extends AppModel {
	/**
	 * MAX_AMOUNT                一度に支払える最大額
	 * MIN_AMOUNT                一度に支払える最低額
	 * SECRET_KEY                WebPay決済用の秘密鍵
	 * PUBLIC_KEY                WebPay決済用の公開鍵
	 * OVERSEAS_DELIVERY_CHARGES 海外配送料
	 */
	const MAX_AMOUNT = 9999999;
	const MIN_AMOUNT = 50;
	const PUBLIC_KEY = "live_public_7hW8SWdAdco92DX2pBcczaSR";
	const SECRET_KEY = "live_secret_8pfgB832A7uG47YdtTgh45Zs";
	public $name = 'Payment';
	public $belongsTo = array('User');
	public $validate = array(
		'amount' => array(
			'rule1' => array(
				'rule' => array('comparison', 'greater or equal', self::MIN_AMOUNT),
				'message' => ''
			),
			'rule2' => array(
				'rule' => array('comparison', 'less or equal', self::MAX_AMOUNT),
				'message' => ''
			)
		)
	);

	/**
	 * 一度に支払える最大額を返す。
	 * @return int 一度に支払える最大額
	 */
	public function getMaxAmount() {
		return self::MAX_AMOUNT;
	}

	/**
	 * 一度に支払える最低額を返す。
	 * @return int 一度に支払える最低額
	 */
	public function getMinAmount() {
		return self::MIN_AMOUNT;
	}

	/**
	 * WebPay公開鍵を返す。localはテスト環境とみなし、テスト用の鍵を返す。
	 * @return int WebPay公開鍵
	 */
	public function getPublicKey() {
		if(substr($_SERVER['SERVER_NAME'], 0, 5) !== 'local') {
			return self::PUBLIC_KEY;
		}
		return "test_public_9oy4uKas6f6VgF6bl9dyF4rI";
	}

	/**
	 * WebPay秘密鍵を返す。localはテスト環境とみなし、テスト用の鍵を返す。
	 * @return int WebPay公開鍵
	 */
	private function getSecretKey() {
		if(substr($_SERVER['SERVER_NAME'], 0, 5) !== 'local') {
			return self::SECRET_KEY;
		}
		return "test_secret_ave6TD8FEeBJ3Sh2zpcRocCu";
	}

	/**
	 * 決済情報のDBへの保存を行い、その後外部サービスを利用した実決済を行う。
	 * 前者の処理の途中で失敗した場合、全ての処理は取り消され、後者は実行されない。
	 * 後者の処理の途中で失敗した場合、すべての処理は取り消される。
	 * つまり、両方失敗か両方成功のどちらか2つしかない。
	 * @param  array $data             課金に必要な情報。課金が成功すれば、DBに保存される。
	 * @return boolean                 全て成功した場合true, それ以外は失敗を識別する名称が入る
	 */
	public function payAndSave($data) {
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		$payment = $data['Payment'];
		$payment['webpay-token'] = $data['webpay-token'];

		if(($result = $this->pay($payment)) !== true) {
			$dataSource->rollback();
			return $result;
		}

		$dataSource->commit();
		return true;
	}
	
	/**
	 * 所定の方法で課金する。
	 * @param  array  $payment         支払情報
	 * @param  string $type            支払方法 
	 * @return string or boolean       支払方法が見つからないときにfalse,それ以外は支払い結果を返す
	 */
	public function pay($data, $type = "webpay") {
		if($type == "webpay") {
			return $this->webpay($data);
		}
		if($type == "spike") {
			return $this->spike($data);
		}
		if($type == "paypal") {
			return $this->paypal($data);
		}
		return false;
	}

	/**
	 * 所定の方法で課金停止する。
	 * @param  string $user_id         月額課金停止対象者のユーザID
	 * @param  string $type            支払方法 
	 * @return string or boolean       支払方法が見つからないときにfalse,それ以外は支払い結果を返す
	 */
	public function cancel($user_id, $type = "webpay") {
		if($type == "webpay") {
			return $this->webpay_cancel($user_id);
		}
		if($type == "spike") {
			return $this->spike_cancel($user_id);
		}
		if($type == "paypal") {
			return $this->paypal_cancel($user_id);
		}
		return false;
	}

	/**
	 * WebPayを用いて月額課金を行う。
	 * @param  array $data             支払情報
	 * @return string or boolean       支払いが成功したらtrue、例外で失敗したらその例外名を返す
	 * @throws CardException		   カード決済が拒否されたため決済が失敗した場合の例外
	 * @throws InvalidRequestException WebPayのAPIの入力パラメータが不正なため決済が失敗した場合の例外
	 * @throws AuthenticationException カード決済に必要な認証が失敗したため決済が失敗した場合の例外
	 * @throws APIConnectionException  APIの接続に失敗したため決済が失敗した場合の例外
	 * @throws APIException			   WebPayのAPIで異常が発生したため決済が失敗した場合の例外
	 * @throws UnexpectedException	   決済が失敗した原因が不明な場合の例外
	 */
	public function webpay($payment) {
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		App::import('Model', 'UserPaymentInformation');
		$this->UserPaymentInformation = new UserPaymentInformation();
		require APP . 'Vendor/autoload.php';
		$webpay = new WebPay\WebPay($this->getSecretKey());

		try {
			$customer = $webpay->customer->create(array("card"=>$payment['webpay-token']));
		} catch (\WebPay\Exception\CardException $e) {
			$error = "CardException\n" .
				'Status is:' . $e->getStatus() . "\n" .
				'Code is:' . $e->getCardErrorCode() . "\n" .
				'Message is:' . $e->getMessage() . "\n" .
				'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay);
			return "CardException";
		} catch (\WebPay\Exception\InvalidRequestException $e) {
			$error = "InvalidRequestException\n" .
				'Message is:' . $e->getMessage() . "\n" .
				'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay);
			return "InvalidRequestException";
		} catch (\WebPay\Exception\AuthenticationException $e) {
			$error = "AuthenticationException\n" .
				'Message is:' . $e->getMessage() . "\n" .
				'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay);
			return "AuthenticationException";
		} catch (\WebPay\Exception\APIConnectionException $e) {
			$error = "APIConnectionException\n" .
				'Message is:' . $e->getMessage() . "\n" .
				'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay);
			return "APIConnectionException";
		} catch (\WebPay\Exception\APIException $e) {
			$error = "APIException\n" .
				'Message is:' . $e->getMessage() . "\n" .
				'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay);
			return "APIException";
		} catch (Exception $e) {
			$error = "UnexpectedException\n" .
				'Message is:' . $e->getMessage() . "\n" .
				'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay);
			return "UnexpectedException";
		}
		$customer_id = $customer->__get('id');

		if(!$user_payment_information = $this->UserPaymentInformation->findByUserId($payment['user_id'], 'id', null, -1)) {
			$this->UserPaymentInformation->id = false;
		}
		$user_payment_information['UserPaymentInformation']['user_id'] = $payment['user_id'];
		$user_payment_information['UserPaymentInformation']['webpay_customer_id'] = $customer_id;

		if(!$this->UserPaymentInformation->save($user_payment_information)) {
			$dataSource->rollback();
			$this->putLog("failed to save webpay_customer_id on User.", $payment, $webpay);
			return false;
		}

		$payment['webpay_customer_id'] = $customer_id;
		if(!$payment = $this->save($payment)) {
			$dataSource->rollback();
			$this->putLog("failed to save Payment with customer_id.", $payment, $webpay);
			return false;
		}
		$payment = $payment['Payment'];

		$year = substr($payment['commencement'], 0, 4);
		$month = substr($payment['commencement'], 5, 2);
		$unixstanp_of_first_day = strtotime("{$year}-{$month}-01 00:00:00");
		$recursion_info = array(
				'amount' => intval($payment['amount']),
				'currency' => 'jpy',
				"customer" => $customer_id,
				"created" => strtotime($payment['commencement']),
				"first_scheduled" => $unixstanp_of_first_day,
				"period" => "month",
				"description" => SERVICE_NAME . "　月額課金"
		);
		try {
			$recursion = $webpay->recursion->create($recursion_info);
		} catch (\WebPay\Exception\CardException $e) {
			$error = "CardException\n" .
					'Status is:' . $e->getStatus() . "\n" .
					'Code is:' . $e->getCardErrorCode() . "\n" .
					'Message is:' . $e->getMessage() . "\n" .
					'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay, null, $recursion);
			return "CardException";
		} catch (\WebPay\Exception\InvalidRequestException $e) {
			$error = "InvalidRequestException\n" .
					'Message is:' . $e->getMessage() . "\n" .
					'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay, null, $recursion);
			return "InvalidRequestException";
		} catch (\WebPay\Exception\AuthenticationException $e) {
			$error = "AuthenticationException\n" .
					'Message is:' . $e->getMessage() . "\n" .
					'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay, null, $recursion);
			return "AuthenticationException";
		} catch (\WebPay\Exception\APIConnectionException $e) {
			$error = "APIConnectionException\n" .
					'Message is:' . $e->getMessage() . "\n" .
					'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay, null, $recursion);
			return "APIConnectionException";
		} catch (\WebPay\Exception\APIException $e) {
			$error = "APIException\n" .
					'Message is:' . $e->getMessage() . "\n" .
					'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay, null, $recursion);
			return "APIException";
		} catch (Exception $e) {
			$error = "UnexpectedException\n" .
					'Message is:' . $e->getMessage() . "\n" .
					'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay, null, $recursion);
			return "UnexpectedException";
		}

		$payment['webpay_recursion_id'] = $recursion->__get('id');
		if(!$this->save($payment)) {
			$dataSource->rollback();
			$this->putLog("failed to save Payment.", $payment, $webpay);
			return false;
		}

		$dataSource->commit();
		return true;
	}

	/**
	 * WebPayの月額課金を停止する。
	 * @param  string $user_id         月額課金停止対象者のユーザID
	 * @return string or boolean       月額課金停止が成功したらtrue、例外で失敗したらその例外名を返す
	 * @throws CardException		   カード決済が拒否されたため決済が失敗した場合の例外
	 * @throws InvalidRequestException WebPayのAPIの入力パラメータが不正なため決済が失敗した場合の例外
	 * @throws AuthenticationException カード決済に必要な認証が失敗したため決済が失敗した場合の例外
	 * @throws APIConnectionException  APIの接続に失敗したため決済が失敗した場合の例外
	 * @throws APIException			   WebPayのAPIで異常が発生したため決済が失敗した場合の例外
	 * @throws UnexpectedException	   決済が失敗した原因が不明な場合の例外
	 */
	public function webpay_cancel($user_id) {
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		$payment = $this->find('first',
			array(
				'fields' => array('id', 'user_id', 'webpay_recursion_id'),
				'conditions' => array('user_id' => $user_id),
				'order' => array('id' => 'desc'),
			)
		);
		$payment['Payment']['expiration'] = date("Y-m-d H:i:s");
		if(!$payment = $this->save($payment)) {
			$dataSource->rollback();
			$this->putLog("failed to save Payment.", $payment);
			return false;
		}

		require APP . 'Vendor/autoload.php';
		$webpay = new WebPay\WebPay($this->getSecretKey());
		$recursion_id = $payment['Payment']['webpay_recursion_id'];

		try {
			$recursion = $webpay->recursion->delete(array("id"=>$recursion_id));
		} catch (\WebPay\Exception\InvalidRequestException $e) {
			$error = "InvalidRequestException\n" .
				'Message is:' . $e->getMessage() . "\n" .
				'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay, null, $recursion);
			return "InvalidRequestException";
		} catch (Exception $e) {
			$error = "UnexpectedException\n" .
				'Message is:' . $e->getMessage() . "\n" .
				'Params are:';
			$dataSource->rollback();
			$this->putLog($error, $payment, $webpay, null, $recursion);
			return "UnexpectedException";
		}

		$dataSource->commit();
		return true;
	}

	/**
	 * 登録されているクレジットカードの情報を取得する。
	 * @param string $type               登録しているサービスの種類
	 * @param string $customer_id 登録しているサービスで、クレジットカード情報を引き出すための顧客ID
	 */
	public function getCardInfo($type = 'webpay', $customer_id) {
		App::import('Model', 'UserPaymentInformation');
		
		$this->UserPaymentInformation = new UserPaymentInformation();
		require APP . 'Vendor/autoload.php';
		
		$webpay = new WebPay\WebPay($this->getSecretKey());
		$customer = $webpay->customer->retrieve($customer_id);

		return $customer->__get('active_card')->__get();
	}

	/**
	 * 有料会員かどうかを判定する。
	 * @param  int $user_id		ユーザID
	 * @return boolean		有料会員の場合はtrue, そうでない場合は false 
	 */
	public function isPaying($user_id) {
		$payment = $this->find('first',
	        array(
	            'fields' => array('id', 'user_id', 'commencement','expiration', 'deleted'),
	            'conditions' => array('user_id' => $user_id),
	        	'order' => array('id' => 'desc')
	        )
	    );
		if(!$payment || $payment['Payment']['deleted'] || 
			strtotime($payment['Payment']['commencement']) >  strtotime(date("Y-m-d H:i:s")) ||
			isset($payment['Payment']['expiration']) && strtotime($payment['Payment']['expiration']) <  strtotime(date("Y-m-d H:i:s"))) {
			return false;
		}
		return true;
	}

	/**
	 * ログをapp/tmp/ 以下に出力する。
	 * @param string $error		エラー理由。
	 * @param string $payment	SQLにINSERTしようとしたフィールドとカラム
	 * @param string $webpay	WebPayオブジェクトの中身
	 * @param string $charge	Chargeオブジェクトの中身（省略可）
	 * @param string $recursion	Recursionオブジェクトの中身（省略可）
	 */
	private function putLog($error, $payment, $webpay, $charge=null, $recursion=null) {
		$this->log($error, 'payment');
		$this->log($payment, 'payment');
		$this->log($webpay, 'webpay');
		if(isset($charge)) {
			$this->log($charge, 'webpay');
		}
		if(isset($recursion)) {
			$this->log($recursion, 'webpay');
		}
	}	
}