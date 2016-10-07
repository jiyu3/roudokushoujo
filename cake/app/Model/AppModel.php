<?php

App::uses('Model', 'Model');

class AppModel extends Model {
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
}
