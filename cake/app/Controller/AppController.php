<?php

App::uses('Controller', 'Controller');

class AppController extends Controller {
	/**
	 * Webサイト全体で使うコンポーネントを定義。
	 */
	public $components = array(
		'Session',
		'Security',
		'Auth' => array(
			'loginRedirect' => array('controller' => 'play', 'action' => 'index'),
			'logoutRedirect' => array('controller' => 'user', 'action' => 'login'),
			'loginAction' => array('controller' => 'user', 'action' => 'login'),
			'authError' => 'ログインしてください',
			'authenticate' => array(
				'Form' => array(
					'fields' => array('username' => 'email'),
				),
			)
		),
		'RequestHandler',
	);
	
	/**
	 * 全てのコントローラで以下を行う。
	 * ・wwwありをwwwなしにリダイレクトする。
	 * ・メンテナンス中にメンテナンス画面にリダイレクトする。
	 * ・ログイン直前のページにリダイレクトするために、セッションに値を格納する。
	 * ・全てのページに、ログイン無しでアクセスできるようにする
	 * （ログインが必要なページは、そのページのコントローラでログイン画面に飛ばす($this->Auth->deny()する)ようにする）
	 * ・CSRF対策を行う
	 * ・SSLを必須とする
	 * ・ログインしてるユーザの情報をセッションに格納し、全てのページで使えるようにする
	 */
	public function beforeFilter(){
		if(strpos($_SERVER['SERVER_NAME'], 'www.') === 0) {
			$server_name = substr($_SERVER['SERVER_NAME'], 4);
			$this->redirect("https://{$server_name}");
		}
		if(substr($_SERVER['REQUEST_URI'], 1, 9) !== 'register') {
			if($this->action !== 'login') {
				$this->Session->write('request_url', $_SERVER['REQUEST_URI']);
			} else {
				$this->Session->delete('redirect_url');
			}
		}

		if(isset($_GET['debug'])) {
			Configure::write('debug', $_GET['debug']);
		}

		$this->Auth->allow();
		if($this->name !== 'Play' && $this->name !== 'Page') {
			if(!env('HTTPS')) {
				$this->redirect('https://'.$_SERVER['SERVER_NAME'].$this->here);
			}
			$this->Security->blackHoleCallback = 'blackhole';
			$this->Security->requireSecure();
		}
		$this->set('logged_in', $this->Auth->loggedIn());
		$this->set('is_mobile', $this->isMobile());
	}

	/**
	 * フォームのHTMLコードをハッシュ変換してキーにすることにより、CSRF対策を行う。
	 * @param string $type 不正なアクセスの種類
	 */
	public function blackhole($type){
		$this->log("{$type} error occured in user=" . $this->Auth->user('id'), 'blackhole');
		$this->log($this->Auth->user(), 'blackhole');
		$this->log($this->request, 'blackhole');
		if($type === 'secure') {
			$this->redirect("https://{$_SERVER['SERVER_NAME']}/");
		}
		$this->redirect('/page/error');
	}

	/**
	 * エラーの際にログ出力とAuth以外のセッションの削除を行う。
	 */
	public function appError($error) {
		header("HTTP/1.0 404 Not Found");

		if($this->Auth->loggedIn()) {
			$this->log("User id:" . $this->Auth->user('id'), 'app_error');
			$this->log($this->Auth->user(), 'blackhole');
		} else {
			$this->set('user', $this->Auth->user());
			$this->Session->delete('request_url');
			$this->Session->delete('redirect_url');
		}
		$this->log($this->request, 'blackhole');
		$this->log("{$error}", 'app_error');
		foreach($_SESSION as $key => $value) {
			if($key !== 'Auth') {
				$this->Session->delete($key);
			}
		}
		echo file_get_contents("https://{$_SERVER['SERVER_NAME']}/page/error");
		exit;
	}

	/**
	 * エラーメッセージをビューにセットする。
	 * @param string $message 共通エラーメッセージ以外のメッセージを送りたいときのメッセージ文
	 */
	public function setErrorMessage($message=null) {
		if(!isset($message)) {
			$error = "処理に失敗しました。お手数ですがもう一度やり直してください。" . "<br />";
		}
		if(is_array($message)) {
			foreach($message as $key) {
				foreach($key as $value) {
					$error .= $value . '<br />';
				}
			}
		} else {
			$error = $message;
		}
		$this->set('error', $error);
	}

	/**
	 * 指定したフォルダの中身のパス一覧の配列を作る。
	 * @param  string $dir				フォルダ名
	 * @param  bool   $get_hidden_file	隠しファイル(.で始まるファイル)を取得するか
	 * @return ファイルの絶対パスの配列
	 */
	public function getFileList($dir, $get_hidden_file=false) {
		$files = scandir($dir);
		$files = array_filter($files, function ($file) {
			return !in_array($file, array('.', '..'));
		});
	
			$list = array();
			foreach ($files as $file) {
				if(!$get_hidden_file && preg_match('/^\./',$file)) {
					continue;
				}
				$fullpath = rtrim($dir, '/') . '/' . $file;
				if (is_file($fullpath)) {
					$list[] = $fullpath;
				}
				if (is_dir($fullpath)) {
					$list = array_merge($list, $this->getFileList($fullpath));
				}
			}
			return $list;
	}
	
	/**
	 * URLからコントローラを取得する。
	 * @param  string $url URL。nullの場合現在アクセスしているURL
	 * @return string      コントローラ名の文字列
	 */
	public function getController($url = null) {
		if(!isset($url)) {
			$url = $_SERVER['REQUEST_URI'];
		}
		$url = explode("/", $_SERVER['REQUEST_URI']);
		return $url[1];
	}

	/**
	 * URLからアクションを取得する。
	 * @param  string $url URL。nullの場合現在アクセスしているURL
	 * @return string      アクション名の文字列
	 */
	public function getAction($url = null) {
		if(!isset($url)) {
			$url = $_SERVER['REQUEST_URI'];
		}
		$url = explode("/", $_SERVER['REQUEST_URI']);
		if(isset(explode("?", $url[2])[0])) {
			return explode("?", $url[2])[0];
		}
		return $url[2];
	}

	/**
	 * スマホ(Android/iPhone)かどうかを判定する。
	 * @param boolean $get_mobile_type モバイル端末のデバイス名を返り値として受け取るか。デフォルトはfalse
	 * @return string/boolean	スマホならデバイス名（$get_mobile_typeがfalseの場合はtrue）, それ以外はfalse
	 */
	public function isMobile($get_mobile_type = false) {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$matched = preg_match('/iPod|iPhone|iPad|Android|Windows Phone|BlackBerry|Symbian/', $user_agent, $match);
		if($get_mobile_type) {
			return $matched ? $match[0] : 'not smartphone';
		} else {
			return $matched ? true : false;
		}
	}

	/**
	 * デバッグ関数dBugでデータをダンプする。
	 * @param array $data ダンプしたいデータ
	 */
	public function d($data) {
		foreach(get_included_files() as $value) {
			if(preg_match('/dBug.php/', $value)) {
				new dBug($data);
				return true;
			}
		}
		require '../webroot/dBug.php';
		new dBug($data);
	}

	/**
	 * デバッグ関数dBugでデータをダンプしてexitする。
	 * @param array $data ダンプしたいデータ
	 */
	public function de($data) {
		$this->d($data);
		exit;
	}
}