<?php
/* 画像を全て読み込む */
foreach($files as $state => $value) {
	foreach($value as $feature => $val) {
		$a = $n = $t = 1;
		foreach($val as $filepath) {
			$filepath = Router::url('/', false) . $filepath;
			if(preg_match('/_a_/', $filepath, $match)) {
				echo "<img src='{$filepath}' id='{$state}_{$feature}_a_".($a++).
				"' class='{$state} {$feature} a' />\n";
			}
			if(preg_match('/_n_/', $filepath, $match)) {
				echo "<img src='{$filepath}' id='{$state}_{$feature}_n_".($n++).
				"' class='{$state} {$feature} n' />\n";
			}
			if(preg_match('/_t_/', $filepath, $match)) {
				echo "<img src='{$filepath}' id='{$state}_{$feature}_t_".($t++).
				"' class='{$state} {$feature} t' />\n";
			}
		}
	}
}
?>