<script type='text/javascript'>
	var device = '<?php echo $this->App->isMobile(true); ?>';
	var is_mobile = <?php echo $this->App->isMobile() ? 1 : 0; ?>;
	var fps = <?php echo $fps; ?>; //フレームレート(1フレーム=1/fps秒)
	var fps_correction_lip = is_mobile ? fps/2 : fps/4; //実行時間の誤差を補正する値。この数だけ先回りして喋らせる。
	var nb_frame = 0; //現在のフレーム数
	var audio = [];
	var audio_tags = document.getElementsByTagName('audio');
	var audio_books_folder_name = '<?php echo AUDIO_BOOKS_FOLDER_NAME; ?>';
	for(var i=0; i<audio_tags.length; i++) {
		audio[audio_tags[i].id] = audio_tags[i];
	}
	var otoha = {'state':'sit', 'feature':'none'};
	var touch = {'region':'', 'state':'', 'feature':''};
	var rand = [];
	function rand_init() {
		// Math.floor(Moth.round((max - min + 1) + min*fps) で max〜minの 乱数生成
		rand['b'] = Math.floor(Math.random()*(15 - 1 + 1)*fps) + 1*fps;
		rand['ptssr'] = Math.floor(Math.random()*(15 - 6 + 1)*fps) + 6*fps;
	};
	rand_init();
	var current_frame = 0;
	var lip = [];
	var subtitles;
	var evnt = [];
	var current_a_tag_id = '<?php echo $current_filename ?>';
	var next_a_tag_id;
	var affiliate_txt;
	var nb_img = 1;
	var nb_img_max;
	var clocks = [];
	var page_change_stop = false;
	var onclick_text = [];
	<?php for($i=0; $i<count($onclick_text); $i++) : ?>
		onclick_text['<?php echo $i; ?>'] = '<?php echo $onclick_text[$i]; ?>';
	<?php endfor; ?>
	var ua = window.navigator.userAgent.toLowerCase();
	var url = false;
	var logged_in = <?php echo $logged_in ? 1 : 0; ?>;
	var is_paying = <?php echo $is_paying ? 1 : 0; ?>;
	if(!is_mobile) {
		setTopByHours();
	}
	var unsupported = false;

	/*
	 * DOMが全て読み込み終わった際の初期化関数。
	 * boolean skip 再生ボタンの表示をスキップし、いきなり再生するかどうか。モバイルでは常にfalse
	 */
	function init1(skip) {
		if(is_mobile) {
			skip = false;
		}

		if(ua.indexOf('windows')!=-1 && (ua.indexOf('6.2')!=-1 || ua.indexOf('6.3')!=-1) && ua.indexOf('firefox')!=-1) { // Windows8/8.1 の Firefox の場合を除外
			unsupported = true;
		} else {
			if(ua.indexOf("msie")!=-1 || ua.indexOf("rident")!=-1 || ua.indexOf('firefox')!=-1) { // firefox か IE の場合
				$('#main_screen img').css('display', 'inline');
			}
			for(key in evnt) {
				if(evnt[key]['img_path']) {
					$('#event_background').css('background-image', 'url("'+evnt[key]['img_path']+'")');
				}
			}
		}

		$(document).ajaxSuccess(function(){
			if(url) {
			    ga('send','pageview', url);
			}
			url = false;
		});

		setImagesByHours();
		$('.balloon').hideBalloon();
		$('#skip').css('display', 'none');

		if(!skip) {
			$('#bumper, #loading').css('display', 'inline');
		} else {
			$('#bumper, #loading').css('display', 'none');
			$('#main_screen').css('display', 'inline');
		}

		$('input#search').quicksearch('table tbody tr');

		<?php foreach($lip as $key => $url) : ?>
			$.get('<?php echo $url; ?>', function(json) {
				lip['<?php echo $key; ?>'] = json;
			});
		<?php endforeach; ?>

		refresh("<?php echo $current_filename; ?>");

		if(document.getElementById('mep_0') === null) {
			if(!is_mobile && ua.indexOf('safari')!=-1 && ua.indexOf('chrome')==-1) {
				unsupported = true;
			} else {
				$('#'+audio_books_folder_name).mediaelementplayer({
					audioWidth: 300,
				});
			}
			$('.mejs-playpause-button button').attr('id', 'play_button');
		} else {
			document.getElementById(audio_books_folder_name).pause();
			$('#'+audio_books_folder_name).attr('src', '/audio/'+audio_books_folder_name+'/'+<?php echo $current_filename; ?>+'.m4a');
		}
		if(!is_mobile) {
	 		$('.mejs-time-rail').width(90);
	 		$('.mejs-time-total.mejs-time-slider').width(79);
	 		setTimeout(function(){
	 	 		$('.mejs-button.mejs-volume-button.mejs-mute button').attr('onclick',
					'document.getElementById("'+audio_books_folder_name+'").volume=0;');
	 		}, 1000);
		}
	}

	/*
	 * データが全て読み込み終わった際の初期化措置。
	 * boolean skip	再生ボタンの表示をスキップし、いきなり再生するかどうか。モバイルでは常にfalse
	 */
	function init2(skip) {
		if(is_mobile) {
			skip = false;
		}

		if(!skip) {
			if(is_mobile) {
				$('#loading > img').attr({'src':'<?php echo Router::url('/', false); ?>img/play.png'});
				$('#loaded').remove();
				$('#loading > img').css('display', 'inline');
			} else {
				$('#loading > img, #bumper').fadeOut(1000);
				$('#loaded').fadeIn(1000);
			}
			document.getElementById('loading').addEventListener('click', function() {
				document.getElementById(audio_books_folder_name).load();
				time = getTime(['month', 'day']);
				if(<?php echo isset($_GET['first']) && $_GET['first']==='listen' ? 1 : 0 ?> || !$.cookie('first')) {
					document.getElementById('first').load();
				} else if(<?php echo isset($_GET['today']) && $_GET['today']==='listen' ? 1 : 0 ?> ||
					!$.cookie(time['month']+'/'+time['day'])
				) {
					document.getElementById('today').load();
				}
				if(document.getElementById('ending')) {
					document.getElementById('ending').load();
				}
// 				for(var i=0; i<evnt.length; i++) {
// 					if(evnt[i]['audio_id'] !== "") {
// 						audio[evnt[i]['audio_id']].load();
// 					}
// 				}

				init2_2(false);
				$('#bumper').fadeOut('slow', function(){$(this).remove();});
				document.getElementById(audio_books_folder_name).addEventListener('ended', function(){
					$('.balloon').hideBalloon();
					if(logged_in && !is_paying) {
						affiliate_txt = '<a href="/payment">月々324円</a>で全ての朗読を<br />聴けますよ。';
					}
					if(affiliate_txt) {
						setTimeout(function(){
							$("#affiliate").showBalloon({
								contents: affiliate_txt,
								position: 'bottom',
								css: {
									zIndex: "6",
									opacity: "0.7"
								}
							}).addClass('balloon');
	
							interruptPlay(audio['ending'].id);
						}, 1000);
					}
				}, false);
			}, false);
		} else {
			init2_2(true);
		}
	}

	/**
	 * init2の一部を切り出した関数（記述を短くするため）。
 	 * boolean skip	再生ボタンの表示をスキップし、いきなり再生するかどうか。モバイルでは常にfalse
	 */
	function init2_2(skip) {
		if(is_mobile) {
			skip = false;
		}

		if(!is_mobile) {
			$('#sidebar, #search_box').fadeIn('slow');
		}

		if($.cookie('volume') === void 0) {
			$.cookie('volume', 0.5);
		}
		$('audio').prop('volume', $.cookie('volume'));
		if(!is_mobile) {
			document.getElementById('BGM').play();
		}


		if(skip) {
			document.getElementById(audio_books_folder_name).play();
			<?php if(isset($_GET['skip']) && $_GET['skip']==="true") : ?>
				loop();
			<?php endif; ?>
		} else {
			showImage(1, 1);
			$('#loading').fadeOut(1000, function(){
				loop();

				if(unsupported) {
					if($.cookie('unsupport_warned') !== 'true') {
						$("#affiliate").showBalloon({
							contents: 'このブラウザでは動作が<br />不安定になるかもしれません。<br /><a href="https://www.google.com/chrome/browser/desktop/">Chromeが推奨ブラウザです</a>。',
							position: 'bottom',
							css: {
								zIndex: "6",
								opacity: "0.7"
							}
						}).addClass('balloon');
						$.cookie('unsupport_warned', 'true');
					}
				} else {
					$.get('/page/campaign?is_paying='+is_paying+'&logged_in='+logged_in, function(campaign_txt){
						time = getTime(['month', 'day']);
//						if(campaign_txt) {
// 							$("#affiliate").showBalloon({
// 								contents: campaign_txt,
// 								position: 'bottom',
// 								css: {
// 									zIndex: "6",
// 									opacity: "0.7"
// 								}
// 							}).addClass('balloon');
//						} else if(<?php echo isset($_GET['today']) && $_GET['today']==='listen' ? 1 : 0 ?> ||
						if(<?php echo isset($_GET['first']) && $_GET['first']==='listen' ? 1 : 0 ?> || !$.cookie('first')) {
							$.cookie('first', 'first_played', {expires:99999});
							interruptPlay('first');
						} else if(<?php echo isset($_GET['today']) && $_GET['today']==='listen' ? 1 : 0 ?> ||
							!$.cookie(time['month']+'/'+time['day'])
						){
							$.cookie(time['month']+'/'+time['day'], 'today_played', {expires:1});
							interruptPlay('today');
						} else if(is_mobile) {
							document.getElementById(audio_books_folder_name).play();
						}
					});
				}

				$('#loading').remove();
			});
		}
		$('#event_background').css('display', 'none');
	}

	/**
	 * オーディオブック関係のデータを更新する。
	 * string title オーディオブックのタイトル
	 */
	function refresh(title) {
		for (var i=0; i<$('#audio_links a').length; i++) {
			regexp = new RegExp(title);
			if($('#audio_links a').eq(i).attr('class').match(regexp)) {
				current_a_tag_id = $('#audio_links a').eq(i).attr('id');
				next_a_tag = $('#audio_links a').eq(i+1);
				if(next_a_tag.length) {
					next_a_tag_id = next_a_tag.attr('id');
				} else {
					next_a_tag_id = $('#audio_links a').eq(0).attr('id');
				}
				$.cookie('last_read', current_a_tag_id);
				$('#audio_links a').css('font-weight', 'normal');
				$('#'+current_a_tag_id).css('font-weight', 'bold');
				$('#next').attr('onclick', $('#'+next_a_tag_id).attr('onclick'));
				break;
			} else {
				$.removeCookie('last_read');
			}
		}

		$.get('/audio/'+audio_books_folder_name+'/'+title+'.json', function(json) {
			lip[audio_books_folder_name] = json;
		});

		document.getElementById(audio_books_folder_name).src = '/audio/'+audio_books_folder_name+'/'+title+'.m4a';

		$.get('/audio/ending/'+title+'.affiliate', function(txt,b,c) {
			affiliate_txt = txt;
		}).fail(function(){
			affiliate_txt = '';
		});
		$.get('/audio/ending/'+title+'_ending.json', function(json) {
			lip['ending'] = json;
		});
		if(audio['ending']) {
			audio['ending'].src = '/audio/ending/'+title+'_ending.m4a';
		} else {
			$("<audio/>").attr('id', 'ending').attr('src', '/audio/ending/'+title+'_ending.m4a').appendTo('body');
			audio['ending'] = document.getElementById('ending');
		}

		$.getScript('/audio/'+audio_books_folder_name+'/'+title+'.subtitles', function() {
			forward = subtitles.start[0];
			for(key in subtitles.start) {
				subtitles.start[key] -= forward;
				subtitles.start[key] = Math.round(subtitles.start[key]/1000*fps);
			}
			for(key in subtitles.end) {
				subtitles.end[key] -= forward;
				subtitles.end[key] = Math.round(subtitles.end[key]/1000*fps);
			}
		});

// 		for(key in evnt) {
// 			if(evnt[key]['audio_id']) {
// 				$('#'+evnt[key]['audio_id']).remove();
// 			}
// 		}
		evnt = [];
		$.getScript('/audio/event/'+title+'_roudoku_frame.event', function() {
			for(key in evnt) {
				if(evnt[key]['img_path']) {
					$('#event_background').css('background-image', 'url("' + evnt[key]['img_path'] + '")');
				}
// 				if(evnt[key]['audio_id']) {
// 					$("<audio/>").attr('id', evnt[key]['audio_id'])
// 						.attr('src', '/audio/event/'+evnt[key]['audio_id']+'.m4a').appendTo('body');
// 					audio[evnt[key]['audio_id']] = document.getElementById(evnt[key]['audio_id']);
// 				}
			}
		});
	}

	$(function(){
		<?php if(isset($_GET['skip']) && $_GET['skip'] === 'true') : ?>
			init1(true);
		<?php else : ?>
			init1(false);
		<?php endif; ?>
	});

	window.onload = function() {
		<?php if(isset($_GET['skip']) && $_GET['skip'] === 'true') : ?>
			init2(true);
		<?php else : ?>
			init2(false);
		<?php endif; ?>
	};

	/**
	 * このループの中で「どのアニメーションを」「いつ」やるかを制御する。
	 * string audio_id この関数の実行時に再生するオーディオファイルのID名。
	 */
	function loop() {
		current_frame = Math.round(getPlayingAudio().currentTime*fps)+fps_correction_lip;
		setImagesByHours();
		eval(otoha['state']+'("'+otoha['feature']+'")');
		nb_img_max = $('.'+otoha['state']+'.'+otoha['feature']+'.t').length;
		nb_frame++;
		showSubtitle();
 		return setTimeout(arguments.callee, 1000/fps);
	}

	/**
	 * 座っていて、本を読んでない状態を制御する。
	 * string feature 特徴を示す単語。内容は以下の通り。
	 *                none     何もしていない
	 *                blinking まばたきしている
	 */
	function sit(feature) {
		if(feature=='none') {
			showImage(1, 1, 'sit', 'none');
			if(nb_frame%rand['b']==0) {
				rand_init();
				otoha['feature'] = 'blinking';
			}
			return true;
		}

		if(feature=='blinking') {
			if(nb_img>2) {
				showImage(1, 1, 'sit', 'none');
			} else {
				if(nb_img==1) {
					showImage(2, 2);
				} else {
					showImage(1, 3);
				}
			}
			return true;
		}

		if(feature=='touch') {
			touch_animation();
			return true;
		}

		if(feature=='clock') {
			answerTime();
			return true;
		}

		console.log('Error: invaild feature "'+feature+'" in sit()');
		return false;
	}
	
	/**
	 * 座っていて、本を読んでいる状態を制御する。
	 * string feature 特徴を示す単語。内容は以下の通り。
	 *                none 何もしていない
	 *                to_read  本を読み始めようとしている
	 *                to_sit   本を読み終えようとしている（＝座る状態に戻ろうとしている）
	 *                blinking まばたきしている
	 *                paging   本を読み始めようとしている
	 */
	function read(feature) {
		if(feature=='none') {
			showImage(1, 1);
			if(nb_frame%rand['b']==0) {
				rand_init();
				otoha['feature'] = 'blinking';
			}
			if(nb_frame%rand['ptssr']==0) {
				rand_init();
				if(rand['ptssr']%2) {
					otoha['feature'] = 'paging';
				} else {
					otoha['feature'] = 'to_sit';
				}
			}
			return true;
		}

		if(feature=='to_read') {
			if(nb_img > nb_img_max) {
				showImage(1, 1, 'read', 'none');
				$('*').css('pointer-events', '');
			} else {
				showImage(nb_img);
			}
			return true;
		}
		
		if(feature=='blinking') {
			if(nb_img>2) {
				showImage(1, 1, 'read', 'none');
			} else {
				if(nb_img==1) {
					showImage(2, 2);
				} else {
					showImage(1, 3);
				}
			}
			return true;
		}

		if(feature=='paging') {
			if(nb_img > nb_img_max) {
				showImage(1, 1, 'read', 'none');
			} else {
				showImage(nb_img);
			}
			return true;
		}

		if(feature=='to_sit') {
			if(nb_img > nb_img_max) {
				if(getPlayingAudio() === false) {
					showImage(1, 1, 'sit', 'none');
					$('*').css('pointer-events', '');
				} else {
					showImage(--nb_img);
					if(nb_frame%rand['ptssr']==0) {
						rand_init();
						showImage(1, 1, 'read', 'to_read');
					}
				}
			} else {
				showImage(nb_img);
			}
			return true;
		}

		if(feature=='touch') {
			touch_animation();
			return true;
		}
		
		console.log('Error: invaild feature "'+feature+'" in read()');
		return false;
	}

	function start_reading() {
		$('.balloon').hideBalloon();
		if(!is_mobile) {
			document.getElementById('BGM').pause();
		}

		otoha['state'] = 'read';
		otoha['feature'] = 'to_read';
	}

	function stop_reading() {
		if(!is_mobile) {
			document.getElementById('BGM').play();
		}

		if(/touch/.test(otoha['feature'])) {
			otoha['state'] = 'sit';
		} else {
			otoha['feature'] = 'to_sit';
		}
	}
	
	/**
	 * キャラにタッチした際の挙動を制御する。
	 * string region  タッチした身体の部位
	 * string state   タッチした際に表示する画像のstate
	 * string feature タッチした際に表示する画像のfeature
	 */
	function touch_body(region, state, feature) {
		return setTimeout(touch_body_delay, 2*1000/fps, region, state, feature);
	}

	function touch_body_delay(region, state, feature) {
		otoha['feature'] = 'touch';
		touch['region'] = region;
		touch['state'] = state;
		touch['feature'] = feature;
		interruptPlay(region, false);
	}

	/**
	 * タッチした際にアニメーションさせる。
	 */
	function touch_animation() {
		var current_img = '#'+touch['state']+'_'+touch['feature']+'_t_'+nb_img;
		var current_lip = '#'+touch['state']+'_'+touch['feature']+'_'+getLip()+'_'+(nb_img-1);
		var nb_img_max = $('.'+touch['state']+'.'+touch['feature']+'.t').length;
		var max_img = '#'+touch['state']+'_'+touch['feature']+'_t_'+nb_img_max;
		if(nb_img > nb_img_max) {
			if(!getPlayingAudio()) {
				showImage(1, 1, 'sit', 'none');
				return true;
			}
			$(max_img).css('display', 'inline');
			$(current_lip).css('display', 'inline');
			noneAll(max_img+','+current_lip);
		} else {
			$(current_img).css('display', 'inline');
			$(current_lip).css('display', 'inline');
			noneAll(current_img+','+current_lip);
			nb_img++;
		}
		return true;
	}

	/**
	 * 時刻を取得する。
	 * string require_lists   求める時刻が格納された１次元配列。以下が入る。
                                year    年
                                month   月
                                day     日
                                date    曜日
                                hours   時間（0〜23）
                                minutes	分
                                seconds	秒
	 * bool two_digit         1桁の数字の文頭に0をつけて2桁にするかどうか
	 * bool twelve_hour_clock 時間が12を超えた時に0リセットするか（リセットするのは午後から午前に切り替わるときのみ）
	 */
	function getTime(require_lists, two_digit, twelve_hour_clock) {
		var d = new Date();
		var time = [];
		var value = '';
		for(var key in require_lists) {
			value = require_lists[key];
			t = eval('d.get'+value.substring(0,1).toUpperCase()+value.substr(1)+'()');
			if(value=='month') {
				t++;
			}
		    if(twelve_hour_clock && value=='hours') {
			    if(t>12) {
				    t -= 12;
			    }
		    }
		    if(two_digit && value!='year' && value!='date') {
			    if(t<10) {
				    t = '0'+t;
			    }
		    }
		    time[value] = t;
		}
		return time;
	}

	/**
	 * 時刻に基づいて背景画像をセットする。
	 */
	function setImagesByHours() {
		var time = getTime(['hours'], false);
		var num;

		if(time['hours']>6 && time['hours']<=15) {
			num = 1;
		} else if(time['hours']>15 && time['hours']<=18) {
			num = 2;
		} else {
			num = 3;
		}

		main_img_url = '<?php echo Router::url('/', false); ?>img/BG_default_L_'+num+'.png';
		chair_img_url = '<?php echo Router::url('/', false); ?>img/chair_L_'+num+'.png';
		$('#main_background').css('background-image', 'url("' + main_img_url + '")');
		$('#chair').css('background-image', 'url("' + chair_img_url + '")');
	}

	/**
	 * 時刻に基づいてトップ画像をセットする。
	 */
	function setTopByHours() {
		var time = getTime(['hours'], false);
		var num;

		if(time['hours']>6 && time['hours']<=15) {
			num = 1;
		} else if(time['hours']>15 && time['hours']<=18) {
			num = 2;
		} else {
			num = 3;
		}

		$('#loaded').attr('src', '<?php echo Router::url('/', false); ?>img/play_'+num+'.png');
	}

	/**
	 * 複数のファイルを連続して再生することにより、現在の時刻を答える。
	 */
	function clock() {
		if(otoha['state']!="sit") {
			return false;
		}
		otoha['feature'] = "clock";
		clocks.push('event_clock_start_001');

		time = getTime(['hours']);
		if(time['hours']<12) {
			document.getElementById('event_clock_am').load();
			clocks.push('event_clock_am');
		} else {
			document.getElementById('event_clock_pm').load();
			clocks.push('event_clock_pm');
		}

		time = getTime(['hours'], true, true);
		document.getElementById('event_clock_h_'+time['hours']).load();
		clocks.push('event_clock_h_'+time['hours']);

		time = getTime(['minutes'], true);
		if(time['minutes']!=='00') {
			document.getElementById('event_clock_m_'+time['minutes']).load();
			clocks.push('event_clock_m_'+time['minutes']);
		}

		document.getElementById('event_clock_end_001').load();
		clocks.push('event_clock_end_001');

		interruptPlay(clocks[0], false);
		answerTime();
	}

	/**
	 * 順々に時刻を表すファイルを再生する。
	 */
	function answerTime() {
		showImage(1, 1, 'sit', 'none');
		otoha['feature'] = 'clock';

		if(clocks.length===0) {
			otoha['feature'] = 'none';
			return true;
		}

		if(document.getElementById(clocks[0]).paused) {
			clocks.shift();
			if(clocks.length>0) {
				interruptPlay(clocks[0], false);
			}
		}

		return true;
	}
	
	/**
	 * 割り込み再生をする。このメソッドを実行すると、以下のことが起こる。
	 * 1. 今再生しているオーディオを一時停止する。
	 * 2. 全ての要素のマウスイベントを無効にする（再生が終わるとマウスイベントは自動的に有効化される）
	 * 3. スキップボタンを表示する。
	 * string  interrupt_audio_id 割り込み再生の対象となるaudioオブジェクトのid名
	 * boolean skippable          スキップできるか。デフォルトはtrue
	 */
	function interruptPlay(interrupt_audio_id, skippable) {
		if(skippable === void 0) {
			skippable = true;
		}
		$('*:not(html, body, header, ul, li, table, tbody, tr, td, div, p, a, .no_disabled)').css('pointer-events', 'none');
		$('.mejs-button').css('pointer-events', 'none');

		document.getElementById(audio_books_folder_name).pause();
		if(!document.getElementById(interrupt_audio_id).duration) {
			$('*').css('pointer-events', '');
			return false;
		}
		document.getElementById(interrupt_audio_id).play();

		if(skippable) {
			$('#skip').css({'display':'inline', 'pointer-events':''});
		}

		setTimeout(function(){$('*').css('pointer-events', '');}, document.getElementById(interrupt_audio_id).duration*1000);
		setTimeout(function(){$('#skip').css('display', 'none');}, document.getElementById(interrupt_audio_id).duration*1000);
	}

	/**
	 * 再生をスキップする。
	 */
	function skip() {
		page_change_stop = true;
		stopAll();
		$('*').css('pointer-events', '');
		$('#skip').css('display', 'none');
	}

	/**
	 * 現在再生中のオーディオを取得する。ただし、BGMは除く。
	 */
	function getPlayingAudio() {
		for(key in audio) {
			if(document.getElementById(key) && !document.getElementById(key).paused) {
				if(document.getElementById(key).id==='BGM') {
					continue;
				}
				return document.getElementById(key);
			}
		}
		return false;
	}

	/**
	 * 現在再生中のオーディオを全て停止する。ただし、BGMは除く。
	 */
	function stopAll() {
		var playing;
		while(1) {
			playing = getPlayingAudio();
			if(playing) {
				playing.pause();
				playing.currentTime = 0;
			} else {
				break;
			}
		}
	}

	/**
	 * 現在の唇の状態を取得する。n, t, a のいずれかが返る。
	 */
	function getLip() {
		var audio = document.getElementById(audio_books_folder_name);
		var audio2 = getPlayingAudio();
		if(!audio.paused && audio.id in lip) {
			return lip[audio.id][Math.round(audio.currentTime*fps)+fps_correction_lip];
		} else if(audio2 && audio2.id in lip) {
			return lip[audio2.id][Math.round(audio2.currentTime*fps)+fps_correction_lip];
		}
		return 't';
	}

	/**
	 * display:inline と display:none によって画像を切り替える。
	 * number __nb_img 	  display:inline にすべき画像の番号。
	 * number nb_img_done グローバル変数nb_img の次の値を入れる。デフォルトは nb_img+1
	 * string state       指定しない場合 otoha['state'] がセットされる。指定した場合、otoha['state'] にstate が代入される。
	 * string feature     指定しない場合 otoha['feature'] がセットされる。指定した場合、otoha['feature'] にfeature が代入される。
	 * （注）画像を表示した後に同じmode に属する他の画像は display:none となる
	 * （注）lip=true で、getLip() の値が t の場合は何もしない（既にそのコードの直前後でt がinline されているだろうから）
	 */
	function showImage(__nb_img, nb_img_done, state, feature) {
		var lip = getLip();
		if(!state) {
			state = otoha['state'];
		} else {
			otoha['state'] = state;
		}
		if(!feature) {
			feature = otoha['feature'];
		} else {
			otoha['feature'] = feature;
		}
		if(nb_img_done===void 0) {
			nb_img_done = __nb_img+1;
		}
		var target = '#'+state+'_'+feature+'_'+lip+'_'+__nb_img;
		if(lip!='t') {
			target += ', #'+state+'_'+feature+'_t_'+__nb_img;
		}
		$(target).css('display', 'inline');
		noneAll(target);
		nb_img = nb_img_done;
	}

	/**
	 * 字幕を表示する。
	 */
	function showSubtitle() {
		if(!current_frame || getPlayingAudio().id != audio_books_folder_name) {
			$('#subtitles').html($('#'+current_a_tag_id).html() + '<br />を朗読します。');
			showEvent(0);
			return false;
		}
		var insert_space = false;
		for (var i=0; i<subtitles['text'].length; i++) {
			insert_space = current_frame <= subtitles['end'][0] ? true : false;
			if(current_frame > subtitles['start'][i] && current_frame <= subtitles['end'][i]) {
				if(is_mobile) {
					$('#subtitles').html(subtitles['text'][i].replace(/@n/g, "<br />"));
				} else {
					insert_space ?
						$('#subtitles').html(subtitles['text'][i].replace(/@n/g, "　")) :
						$('#subtitles').html(subtitles['text'][i].replace(/@n/g, ""));
				}
				showEvent(subtitles['page'][i]);
				return true;
			}
		}
		showEvent(0);
		return false;
	}

	/**
	 * イベント用の背景及びSEを出力する。
	 * param string  現在のページ数
	 */
	function showEvent(page) {
		for(key in evnt) {
			if(evnt[key]['start'] <= page && page <= evnt[key]['end']) {
				if(!evnt[key]['img_path']) {
					$('#event_background').fadeOut(300);
				} else {
					if($('#event_background').css('display') === 'none') {
						$('#event_background').css('background-image', 'url("' + evnt[key]['img_path'] + '")').fadeIn(300);
					} else {
						$('#event_background').css('background-image', 'url("' + evnt[key]['img_path'] + '")').fadeIn(300);
					}
				}
// 				if(evnt[key]['audio_id']) {
// 					if(audio[evnt[key]['audio_id']] && audio[evnt[key]['audio_id']].paused) {
// 						audio[evnt[key]['audio_id']].play();
// 					}
// 				}
				return true;
			}
		}
		$('#event_background').fadeOut(300);
		return false;
	}

	/**
	 * 指定したセレクタ配下の特定の要素を全てdisplay:noneにする。ただし、例外のセレクタは除く。
	 * e_selecter 例外となる（noneにしない）タグのセレクタ default:""
	 * b_selecter 基準となるセレクタ                   default:".character"
	 * d_selecter 削除するセレクタ                     default:"img"
	 */
	function noneAll(e_selecter, b_selecter, d_selecter) {
		if(!e_selecter) {
			e_selecter = '';
		}
		if(!b_selecter) {
			b_selecter = '.character';
		}
		if(!d_selecter) {
			d_selecter = 'img';
		}
		$(b_selecter+' '+d_selecter+':not('+e_selecter+')').css('display', 'none');
	}

	/**
	 * 現在地から天気を取得し、表示する。
	 */
	function weather() {
		$("#weather_display").showBalloon({
			contents: '情報取得中…',
			position: 'top',
			css: {
				zIndex: "6",
				opacity: "0.7"
			}
		}).addClass('balloon');

		function success(pos) {
			var crd = pos.coords;
			var lat = Math.round(crd.latitude);
			var lon = Math.round(crd.longitude);
			$.get('http://api.openweathermap.org/data/2.5/weather?lat='+lat+'&lon='+lon, function(w) {
				var max_temp = Math.round((w.main.temp_max - 273.15)*10)/10;
				var min_temp = Math.round((w.main.temp_min - 273.15)*10)/10;
				$("#weather_display").showBalloon({
					contents: '今日のお天気は ' + w.weather[0]["main"] + '<br />' +
							'最高気温／最低気温は '  + max_temp + '／' + min_temp + '度です。<br />' + 
							'今日も一日、頑張ってください。',
					position: 'top',
					css: {
						zIndex: "6",
						opacity: "0.7"
					}
				}).addClass('balloon');
				setTimeout(function(){$('#weather_display').hideBalloon();}, 5000);
			});
		};

		function error(err) {
			console.warn('ERROR(' + err.code + '): ' + err.message);
			$('#weather_display').hideBalloon();
		};

		navigator.geolocation.getCurrentPosition(success, error);
	}
</script>