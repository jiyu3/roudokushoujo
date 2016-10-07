<?php
require 'dBug.php';

class wave {

	var $fp, $filesize;
	var $data, $blocktotal, $blockfmt, $blocksize;
	var $fps;

	function __construct($file) {
		if(!$this->fp = @fopen($file, 'rb')) {
			return false;
		}
		$this->fps = 8;

		$this->filesize = filesize($file);
		$this->waveData($file);
	}

	function waveChunk() {
		rewind($this->fp);

		$riff_fmt = 'a4ID/VSize/a4Type';
		$riff_cnk = @unpack($riff_fmt, fread($this->fp, 12));

		if($riff_cnk['ID'] != 'RIFF' || $riff_cnk['Type'] != 'WAVE') {
			return -1;
		}

		$format_header_fmt = 'a4ID/VSize';
		$format_header_cnk = @unpack($format_header_fmt, fread($this->fp, 8));

		if($format_header_cnk['ID'] != 'fmt ' || !in_array($format_header_cnk['Size'], array(16, 18))) {
			return -2;
		}

		$format_fmt = 'vFormatTag/vChannels/VSamplesPerSec/VAvgBytesPerSec/vBlockAlign/vBitsPerSample'.($format_header_cnk['Size'] == 18 ? '/vExtra' : '');
		$format_cnk = @unpack($format_fmt, fread($this->fp, $format_header_cnk['Size']));

		if($format_cnk['FormatTag'] != 1) {
			return -3;
		}

		if(!in_array($format_cnk['Channels'], array(1, 2))) {
			return -4;
		}

		$fact_fmt = 'a4ID/VSize/Vdata';
		$fact_cnk = @unpack($fact_fmt, fread($this->fp, 12));

		if($fact_cnk['ID'] != 'fact') {
			fseek($this->fp, ftell($this->fp) - 12);
		}

		$data_fmt = 'a4ID/VSize';
		$data_cnk = @unpack($data_fmt, fread($this->fp, 8));

		if($data_cnk['ID'] != 'data') {
			return -5;
		}

		if($data_cnk['Size'] % $format_cnk['BlockAlign'] != 0) {
			return -6;
		}

		$this->data = fread($this->fp, $data_cnk['Size']);
		$this->blockfmt = $format_cnk['Channels'] == 1 ? 'sLeft' : 'sLeft/sRight';

		$this->blocktotal = $data_cnk['Size'] / 4;
		$this->blocksize = $format_cnk['BlockAlign'];

		$return = array
			(
			'Size' => $data_cnk['Size'] ,
			'Channels' => $format_cnk['Channels'],
			'SamplesPerSec' => $format_cnk['SamplesPerSec'],
			'AvgBytesPerSec' => $format_cnk['AvgBytesPerSec'],
			'BlockAlign' => $format_cnk['BlockAlign'],
			'BitsPerSample' => $format_cnk['BitsPerSample'],
//			'Extra' => $format_cnk['Extra'],
			'seconds' => ($data_cnk['Size'] / $format_cnk['AvgBytesPerSec'])
			);

		return $return;

	}

	function waveData($filename, $channel = 'Left') {
		if(!$this->data) {
			if(!is_array($wavechunk = $this->waveChunk())) {
				return false;
			}
		}

		$num_merge = ceil($this->blocktotal / ($wavechunk['seconds'] * $this->fps));

		$x = $j = $wavetotal = 0;
		$pixels = array();
		for($i = 0; $i < $this->blocktotal; $i++) {
			$blocks[] = @unpack($this->blockfmt, substr($this->data, $i * 4, $this->blocksize));
			if($i%$num_merge === 0) {
				for(; $j<$i; $j++) {
					if(isset($blocks[$j])) {
						$wavetotal += $blocks[$j][$channel];
						unset($blocks[$j]);
					}
				}
				$pixels[++$x] = abs(round($wavetotal / ($j - $i + $num_merge + 1)));
				$wavetotal = 0;
			}
		}
		$pixels[++$x] = 0;

		unset($blocks, $channel, $wavechunk, $num_merge, $wavetotal, $i, $j, $x);

		$mode = 'a';
		foreach($pixels as &$y) {
			if($y) {
				if(!rand(1, 10)%10 && $mode==='n') {
					$y = $mode = 't';
				} else if($mode=='a') {
					$y = $mode = 'n';
				} else {
					$y = $mode = 'a';
				}
				continue;
			}
			$y = 't';
		}
		file_put_contents(str_replace("wav", "json", $filename), json_encode($pixels), LOCK_EX);
		unset($pixels, $average, $mode, $filename);
		return true;
	}
}

?>