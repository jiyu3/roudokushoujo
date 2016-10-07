<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {
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
		require 'dBug.php';
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
}
