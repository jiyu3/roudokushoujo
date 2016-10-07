<?php

App::uses('AppController', 'Controller');

class PageController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	/**
	 * ログインが必要なページを指定する。
	 * 該当ページに未ログイン状態でアクセスすると、
	 * /user/loginにリダイレクトされる。
	 */
	public function beforeFilter() {
		$this->Security->csrfCheck = false;
		$this->Security->csrfUseOnce = false;
		$this->Security->validatePost = false;
		$this->Security->unlockedActions = array('upload', 'init');

		if($this->Session->read('from.login')) {
			$this->Session->delete('from.login');
			$this->redirect('index');
		}

		if(env('HTTPS')) {
			$this->redirect('http://' . env('SERVER_NAME') . $this->here);
		}
		parent::beforeFilter();
	}

	/**
	 * プレイ画面にリダイレクトする。
	 */
	public function index() {
		if($this->Auth->loggedIn()) {
			$this->redirect('/play/index');
		} else {
			$this->redirect('/play');
		}
	}

	/**
	 * 特定商取引法に基づく表示
	 */
	public function law() {
		$this->set('title_for_layout', '特定商取引法に基づく表示 - ' . SERVICE_NAME);
	}

	/**
	 * キャンペーン情報表示ページ(javascriptからの読み込み専用)
	 */
	public function campaign() {
		$this->set('title_for_layout', 'キャンペーン - ' . SERVICE_NAME);
	}

	/**
	 * 管理用画面。オーディオブックファイル等をアップロードする。
	 */
	public function upload() {
		if($_SERVER['HTTP_HOST'] !== 'roudoku' && $_SERVER['HTTP_HOST'] !== '133.130.59.45') {
			$this->redirect('index');
		}

		if($this->request->is('post')) {
			foreach($_FILES['userfile']['size'] as $key => $file) {
				if(empty($file)) {
					continue;
				}
				move_uploaded_file($_FILES['userfile']['tmp_name'][$key],
					"audio/".AUDIO_BOOKS_FOLDER_NAME."/{$_FILES['userfile']['name'][$key]}");
			}
	
			if(!empty($_POST['twitter_link']) && !empty($_FILES['userfile']['name'][0])) {
				preg_match("/(.+)(_\d{3})?\./", $_FILES['userfile']['name'][0], $match);
				$url = $_POST["twitter_link"];
				$affiliate_txt = "<a href='{$url}'>ツイートみてくださいね。</a>";
				file_put_contents("audio/ending/{$match[1]}.affiliate", $affiliate_txt, LOCK_EX);
			}
			$this->set('result', true);
		}
	}

	/**
	 * 朗読時に表示する画像をアップロードする。
	 */
	public function image() {
		$this->layout = 'admin';

		if($_SERVER['HTTP_HOST'] !== 'roudoku' && $_SERVER['HTTP_HOST'] !== '133.130.59.45') {
			$this->redirect('index');
		}

		if($this->request->is('post')) {
			$event = '';
			foreach($_FILES['image']['size'] as $key => $file) {
				if(empty($file)) {
					continue;
				}
				$img_name = $_POST['title']. "_" . sprintf("%03d", $key+1) . '.png';
				move_uploaded_file($_FILES['image']['tmp_name'][$key],
						"audio/event/{$img_name}");
				
	 			$event .= "evnt[{$key}] = {'start':'', 'end':'', 'img_path':'', 'audio_id':''};\n";
				$event .= "evnt[{$key}]['start'] = '" . $_POST["image_start"][$key] . "';\n";
				$event .= "evnt[{$key}]['end'] = '" . $_POST["image_end"][$key] . "';\n";
				$event .= "evnt[{$key}]['img_path'] = '/audio/event/{$img_name}';\n";
				$event .= "evnt[{$key}]['audio_id'] = '';\n";
			}
			file_put_contents("audio/event/{$_POST['title']}_".AUDIO_BOOKS_FOLDER_NAME."_frame.event", $event, LOCK_EX);

			chmod("audio/event/{$img_name}", 0707);
			chmod("audio/event/{$_POST['title']}_".AUDIO_BOOKS_FOLDER_NAME."_frame.event", 0707);

			$this->set('result', true);
			return true;
		}

		$files = $this->getFileList("audio/".AUDIO_BOOKS_FOLDER_NAME);
		$titles = array();
		foreach($files as $file) {
			if(substr($file, -5, 5) === 'title') {
				$titles[basename($file, '.title')] = trim(file_get_contents($file));
			}
		}
		$this->set('titles', $titles);
	}

	/**
	 * オーディオブックフォルダ及びイベントフォルダを初期化する（羅生門、ごん狐は残す）
	 */
	public function init($target = null) {
		if($_SERVER['HTTP_HOST'] !== 'roudoku' && $_SERVER['HTTP_HOST'] !== '133.130.59.45') {
			$this->redirect('index');
		}

		if($target === null) {
			$this->redirect('/');
		}

		if($target === 'upload') {
			$files = $this->getFileList(getcwd().'/audio/'.AUDIO_BOOKS_FOLDER_NAME);
			foreach($files as $file) {
				$f = basename($file);
				$p1 = preg_match("/gongitune/", $f);
				$p2 = preg_match("/rashomon/", $f);
				if(!$p1 && !$p2) {
					unlink($file);
				}
			}
		}

		if($target === 'image') {
			$files = $this->getFileList(getcwd().'/audio/event');
			foreach($files as $file) {
				$f = basename($file);
				$p1 = preg_match("/gongitune/", $f);
				$p2 = preg_match("/rashomon/", $f);
				if(!$p1 && !$p2) {
					unlink($file);
				}
			}
		}
	}

	/**
	 * PC用ヘルプ画面
	 */
	public function help() {
		$this->set('title_for_layout', 'ヘルプ - ' . SERVICE_NAME);
	}

	/**
	 * 共通エラー画面
	 */
	public function error() {
		$this->set('title_for_layout', 'エラー - ' . SERVICE_NAME);
	}

//	public function mail() {
// 		$this->set('title_for_layout', 'メール - ' . SERVICE_NAME);
//  		$email = explode(",","zou13mito@gmail.com,yutaka@bc9.ne.jp,yukineko1984@outlook.jp,yuki101610161016@yahoo.co.jp,yoshinonaka0408@gmail.com,yorokobi.taro.0514@gmail.com,ynr090@gmail.com,ykrn@gol.com,ye24243e72tqhzE05SE@softbank.ne.jp,y.m.4160@gmail.com,x2@crazysound.jp,www@smilestudio-jp.com,wakao_kohji@mac.com,ulqtakuz@gmail.com,ukky3134@yahoo.co.jp,to_indy@hotmail.com,toyohatagumo@nifty.com,tom_wat1900@ybb.ne.jp,teracha11@gmail.com,tam@sea.plala.or.jp,takutaku3279@gmail.com,t.ohtsuka.mobile@gmail.com,t.kiyose@eisys.co.jp,stead2013@yahoo.co.jp,stationlovejp@ybb.ne.jp,souma_ryuto@yahoo.co.jp,souichirou.nakatsu@gmail.com,sigureminato124@gmail.com,shinya.ina2@gmail.com,schon.mofa@gmail.com,salkasumi119@gmail.com,sakayuki0178@gmail.com,rxn05543@nifty.com,RX-104ff@hotmail.co.jp,rostrs12345@docomo.ne.jp,popowajp@yahoo.co.jp,osanekohu@gmail.com,omi_omi_zodiac_libra@yahoo.co.jp,okina-ka@cosmos.ne.jp,notfound333@outlook.jp,nishioka.tomohiro@gmail.com,naked_feels0508@yahoo.co.jp,murahama@msc.biglobe.ne.jp,murahama@me.com,murahama@lmd.co.jp,mrday12@gmail.com,modesty.is.virtue@gmail.com,mino2357@gmail.com,maxx0902@gmail.com,masaki.m1108@docomo.ne.jp,maborosi_zexy@yahoo.co.jp,m@noumenon.jp,lucky_mild@yahoo.co.jp,lrf@mue.biglobe.ne.jp,liyuray@gmail.com,linux.truth@gmail.com,limms@chollian.net,kotaron66@gmail.com,kohaku.miyabino@gmail.com,kiyohara.qlo-96@i.softbank.jp,kentakano@mac.com,keiba1@sakalab.org,katsuya317@gmail.com,kametaho@gmail.com,j@noumenon.jp,isima99@gmail.com,isei@shinri.biz,info@noumenon.jp,imachan.t4blueimpulse.10thanv@docomo.ne.jp,hiyo_ko_tori@yahoo.co.jp,hisyoku_soukyu_sora_hare_ke@yahoo.co.jp,haruhiko1217@gmail.com,hamboneress@gmail.com,hab24300@syd.odn.ne.jp,gotoysl37@gmail.com,giogio416jojo@i.softbank.jp,gen_info@hotmail.com,gaxuuuuuu@gmail.com,fumyufumyu927@yahoo.co.jp,fiasse.crystela@gmail.com,fbhoych@yahoo.co.jp,etouchida@gmail.com,eltjam3@mac.com,efax@lmd.co.jp,davyjablonski@yahoo.co.jp,daisuke.teol0926@gmail.com,CQE03363@nifty.ne.jp,cian_inthepool@live.jp,chipmunk9117@yahoo.co.jp,cawboy-bebop@docomo.ne.jp,blackrockpepper@gmail.com,awy5k4uo6z5syhjct6ery86a7@gmail.com,aokero@io.ocn.ne.jp,ant-spiral0720bussan0422@docomo.ne.jp,alfonce@goo.jp,726749804@qq.com");
// 		$content = "こんにちは。

// 朗読少女　サポートセンターです。

// 2015年9月3日から、同年9月11日までの間、課金を行っても有料会員になれない不具合があることが判明しました。
// これらの課金に関しては、全額返金の処置を取りました。
// この期間内に課金を行ったお客様には、ご面倒をお掛けし申し訳ありません。

// 今回の件の謝罪として、お客様のアカウントにおいて、無料で有料会員の全ての機能を使えるように致しました。
// 2015年9月度限りとなりますが、全ての機能がお使いいただけます。

// この度は重ね重ね申し訳ありませんでした。
// 再発を防ぐよう努力していきますので、今後も朗読少女をお使いいただけると幸いです。";
//  		$this->MyEmail = $this->Components->load('MyEmail');
//  		foreach($email as $e) {
//  			$this->MyEmail->send($content, '[' . SERVICE_NAME . ']' . '課金機能の不具合に関して', $e);
//  		}
// 	}
}
