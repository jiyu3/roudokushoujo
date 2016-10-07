<?php

App::uses('AppController', 'Controller');

class PaymentController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('UserPaymentInformation', 'User', 'Payment');

	public function beforeFilter() {
		if($this->action === 'cancel') {
			unset($this->User->validates);
		}
		parent::beforeFilter();
		$this->Auth->deny();
		$payment = $this->Payment->findByUserId($this->Auth->user('id'));
		$this->Security->unlockedActions = array('index', 'cancel');
	}

	/**
	 * 決済情報入力ページを表示する。
	 */
	public function index() {
		$amount = 324;

		$this->set('title_for_layout', 'クレジットカード情報入力 - ' . SERVICE_NAME);
		$this->Session->delete('Payment');

		if($this->Payment->isPaying($this->Auth->user('id'))) {
			$this->Session->delete('Payment');
			$this->redirect('/play');
		}

		$payment = $this->Session->read('Payment.payment_data');

		$this->set('user_id', $this->Auth->user('id'));
		$payment = $this->Session->read('Payment.payment_data');
		$this->set('amount', $amount);
		$this->set('public_key', $this->Payment->getPublicKey());
		$user_payment_information = $this->UserPaymentInformation->findByUserId($this->Auth->user('id'),
			'webpay_customer_id', null, -1);
		if(isset($user_payment_information['UserPaymentInformation']['webpay_customer_id'])) {
			$webpay_customer_id = $user_payment_information['UserPaymentInformation']['webpay_customer_id'];
			$this->set('card_info', $this->Payment->getCardInfo('webpay', $webpay_customer_id));
		}

		if($this->request->is('post')) {
			$this->Payment->id = false;
			$now = date("Y-m-d H:i:s");
			preg_match("/(\d+)-(\d+)-(\d+) /", $now, $match);
			list($year, $month, $day) = array($match[1], $match[2], $match[3]);
			$this->request->data['Payment'] = array(
				'amount' => $amount,
				'user_id' => $this->Auth->user('id'),
				'method' => 'webpay',
				'commencement' => $now
			);
			$result = $this->Payment->payAndSave($this->request->data);

			if($result === true) {
				$content = '支払いが完了しました。以下が支払情報です。' . "\n" . 
					'支払方法' . ': ' . 'クレジットカード' . "\n" .
					'支払額' . ': ' . $this->request->data['Payment']['amount'] . "(月額)". "\n" .
					'種別' . ': ' . '月額課金（毎月1日にその月の利用料を一括支払）';
				$this->MyEmail = $this->Components->load('MyEmail');
				$this->MyEmail->send($content, '[' . SERVICE_NAME . ']' . '支払が完了しました。', $this->Auth->user('email'));

				$this->Session->write('Payment.from_index', true);
				$this->redirect("done");
			} else if($result === "CardException") {
				$error = 'カード決済が何らかの理由で拒否されました。お手数ですが、カード情報を確認の上もう一度やり直してください。';
			} else if($result === "InvalidRequestException") {
				$error = 'カード情報に誤りがあったため、決済に失敗しました。お手数ですが、カード情報を確認の上もう一度やり直してください。';
			} else if($result === "AuthenticationException") {
				$error = '決済の際に必要な認証に失敗したため、決済に失敗しました。お手数ですが、少し時間をおいてやり直してください。';
			} else if($result === "APIConnectionException") {
				$error = '決済用のサーバーに接続できなかったため、決済に失敗しました。お手数ですが、少し時間をおいてやり直してください。';
			} else if($result === "APIException") {
				$error = '決済用のサーバーでエラーが発生したため、決済に失敗しました。お手数ですが、少し時間をおいてやり直してください。';
			} else if($result === false) {
				$error = '決済関係の情報がデータベースに保存できなかったため、決済に失敗しました。お手数ですがカード情報を確認の上もう一度やり直してください。';
			} else {
				$error = 'カード決済が何らかの理由で拒否されました。お手数ですが、カード情報を確認の上もう一度やり直してください。';
			}
			$this->Session->setFlash($error);
			$this->Session->delete('Payment');
			$this->redirect('/page/error');
		}
	}

	/**
	 * 決済完了ページを表示する。
	 */
	public function done() {
		$this->set('title_for_layout', 'クレジットカード情報入力完了 - ' . SERVICE_NAME);
		if(!$this->Session->read('Payment.from_index')) {
			$this->redirect('/play');
		}
		$this->Session->delete('Payment');
	}

	/**
	 * 決済取消ページを表示する。
	 */
	public function cancel() {
		$this->set('title_for_layout', '課金停止 - ' . SERVICE_NAME);
		$this->Session->delete('Payment');

		if(!$this->Payment->isPaying($this->Auth->user('id'))) {
			$this->redirect('/');
		}

		if($this->request->is('post')) {
			if(!$this->User->passwordCheck($this->Auth->user('id'), $this->request->data['User']['password'])) {
				$this->set('error', __('パスワードが違います。正しいパスワードを入力してください。'));
				return false;
			}

			$result = $this->Payment->cancel($this->Auth->user('id'));
			if($result) {
				$this->Session->write('Payment.from_cancel', true);
			}
					if($result === true) {
				$content = SERVICE_NAME.'の課金停止が完了しました。ご利用ありがとうございました。' . "\n" . 
				$this->MyEmail = $this->Components->load('MyEmail');
				$this->MyEmail->send($content, '[' . SERVICE_NAME . ']' . '課金停止が完了しました。', $this->Auth->user('email'));
				$this->redirect("cancel_finish");
			} else if($result === "CardException") {
				$error = 'カード決済が何らかの理由で拒否されました。お手数ですが、カード情報を確認の上もう一度やり直してください。';
			} else if($result === "InvalidRequestException") {
				$error = 'カード情報に誤りがあったため、決済に失敗しました。お手数ですが、カード情報を確認の上もう一度やり直してください。';
			} else if($result === "AuthenticationException") {
				$error = '決済の際に必要な認証に失敗したため、決済に失敗しました。お手数ですが、少し時間をおいてやり直してください。';
			} else if($result === "APIConnectionException") {
				$error = '決済用のサーバーに接続できなかったため、決済に失敗しました。お手数ですが、少し時間をおいてやり直してください。';
			} else if($result === "APIException") {
				$error = '決済用のサーバーでエラーが発生したため、決済に失敗しました。お手数ですが、少し時間をおいてやり直してください。';
			} else if($result === false) {
				$error = '決済関係の情報がデータベースに保存できなかったため、決済に失敗しました。お手数ですがカード情報を確認の上もう一度やり直してください。';
			} else {
				$error = 'カード決済が何らかの理由で拒否されました。お手数ですが、カード情報を確認の上もう一度やり直してください。';
			}
			$this->Session->setFlash($error);
			$this->Session->delete('Payment');
			$this->redirect('/page/error');
		}
	}

	/**
	 * 決済取消完了力ページを表示する。
	 */
	public function cancel_finish() {
		$this->set('title_for_layout', '課金停止完了 - ' . SERVICE_NAME);
		if(!$this->Session->read('Payment.from_cancel')) {
			$this->redirect('/');
		}
		$this->Session->delete('Payment');
	}
}