<?php
/**
 * QZoneUtil （QQ空间工具类）
 * @version 1.0.0
 * @author 快乐是福
 * 基于快乐秒赞qzone.class.php改写，规范变量名，增加注释 ———— 2016-06-09 16:22:29 by 昌维
 */
class qzone
{
	/**
	 * @error [msg] 错误消息
	 * @error [int] 错误码
	 */
	public $msg;
	public $error;

	/**
	 * [__construct 构造方法]
	 * @param [string] $uin  [QQ号]
	 * @param [string] $sid  [sid]
	 * @param [string] $skey [skey]
	 */
	public function __construct($uin, $sid, $skey)
	{
		$this->uin = $uin;
		$this->sid = $sid;
		$this->skey = $skey;
		$this->gtk = $this->getGTK($skey);
		$this->cookie = 'uin=o0' . $uin . '; skey=' . $skey . ';';
	}
	/**
	 * [zyzan 主页点赞]
	 * @param  [string] $uin [目标QQ]
	 * @return [type]      [description]
	 */
	public function zyzan($uin)
	{
		$url = 'http://w.qzone.qq.com/cgi-bin/likes/internal_dolike_app?g_tk=' . $this->gtk;
		$post = 'qzreferrer=http://user.qzone.qq.com/' . $uin . '/main&appid=7030&face=0&fupdate=1&from=1&query_count=200&format=json&opuin=' . $this->uin . '&unikey=http://user.qzone.qq.com/' . $uin . '&curkey=http://user.qzone.qq.com/' . $uin . '&zb_url=';
		$json = $this->get_curl($url, $post, 'http://user.qzone.qq.com/' . $uin, $this->cookie);
		$arr = json_decode($json, true);
		if (@array_key_exists('code', $arr) && ($arr['code'] == 0)) {
			$this->msg[] = $this->uin . '赞' . $uin . '主页成功[PC]';
		}
		else if ($arr[code] == -3000) {
			$this->skeyzt = 1;
			$this->error[] = $this->uin . '赞' . $uin . '主页失败[PC]！原因:' . $arr[message];
		}
		else {
			$this->error[] = $this->uin . '赞' . $uin . '主页失败[PC]！原因:' . $arr['message'];
		}
	}
	/**
	 * [pczhuanfa 电脑版转发说说]
	 * @param  [string] $con   [转发评论]
	 * @param  [string] $touin [转发到谁]
	 * @param  [string] $tid   [说说id]
	 * @return [type]        [description]
	 */
	public function pczhuanfa($con, $touin, $tid)
	{
		$url = 'http://user.qzone.qq.com/q/taotao/cgi-bin/emotion_cgi_forward_v6?g_tk=' . $this->gtk;
		$post = 'tid=' . $tid . '&t1_source=1&t1_uin=' . $touin . '&signin=0&con=' . $con . '&with_cmt=0&fwdToWeibo=0&forward_source=2&code_version=1&format=json&out_charset=UTF-8&hostuin=' . $this->uin . '&qzreferrer=http://user.qzone.qq.com/' . $this->uin . '/infocenter';
		$json = $this->get_curl($url, $post, 'http://user.qzone.qq.com/' . $this->uin . '/infocenter', $this->cookie);

		if ($json) {
			$arr = json_decode($json, true);

			if ($arr[code] == 0) {
				$this->shuotid = $arr[tid];
				$this->msg[] = $this->uin . '转发' . $touin . '说说成功[PC]';
			}
			else if ($arr[code] == -3000) {
				$this->skeyzt = 1;
				$this->error[] = $this->uin . '转发' . $touin . '说说失败[PC]！原因:' . $arr[message];
			}
			else {
				$this->error[] = $this->uin . '转发' . $touin . '说说失败[PC]！原因' . $json;
			}
		}
		else {
			$this->error[] = $this->uin . '获取转发' . $touin . '说说结果失败[PC]';
		}
	}
	/**
	 * [cpzhuanfa 手机版转发]
	 * @param  [string] $con   [转发评论]
	 * @param  [string] $touin [转发到谁]
	 * @param  [string] $tid   [说说id]
	 * @return [type]        [description]
	 */
	public function cpzhuanfa($con, $touin, $tid)
	{
		$url = 'http://m.qzone.com/operation/operation_add';
		$post = 'res_id=' . $tid . '&res_uin=' . $touin . '&format=json&reason=' . $con . '&res_type=311&opr_type=forward&operate=1&sid=' . $this->sid;
		$json = $this->get_curl($url, $post, 1);

		if ($json) {
			$arr = json_decode($json, true);
			if (@array_key_exists('code', $arr) && ($arr['code'] == 0)) {
				$this->msg[] = $this->uin . '转发' . $touin . '说说成功[CP]';
			}
			else if ($arr[code] == -3000) {
				$this->sidzt = 1;
				$this->error[] = $this->uin . '转发' . $touin . '说说失败[CP]！原因:' . $arr['message'];
			}
			else {
				$this->error[] = $this->uin . '转发' . $touin . '说说失败[CP]！原因:' . $arr['message'];
			}
		}
		else {
			$this->error[] = $this->uin . '获取转发' . $touin . '说说结果失败[CP]';
		}
	}
	/**
	 * [zhuanfa 转发]
	 * @param  integer $do  [description]
	 * @param  integer $ok  [description]
	 * @param  string  $con [description]
	 * @return [type]       [description]
	 */
	public function zhuanfa($do = 0, $ok = 0, $con = ".")
	{
		if ($shuos = $this->getnew()) {
			foreach ($shuos as $shuo ) {
				$uin = $shuo['userinfo']['user']['uin'];

				if (stripos('z' . $ok, $uin)) {
					$cellid = $shuo['id']['cellid'];

					if ($do) {
						$this->pczhuanfa($con, $uin, $cellid);

						if ($this->skeyzt) {
							break;
						}
					}
					else {
						$this->cpzhuanfa($con, $uin, $cellid);

						if ($this->sidzt) {
							break;
						}
					}
				}
			}
		}
	}
	/**
	 * [timeshuo 发表定时说说]
	 * @param  string $content [说说内容]
	 * @param  int $time    [定时说说发表时间戳]
	 * @param  string $richval [说说引用数据，比如说发说说带上的照片]
	 * @return [type]          [description]
	 */
	public function timeshuo($content = "", $time, $richval = "")
	{
		$url = 'http://user.qzone.qq.com/q/taotao/cgi-bin/emotion_cgi_publish_timershuoshuo_v6?g_tk=' . $this->gtk;
		$post = 'syn_tweet_verson=1&paramstr=1&pic_template=';

		if ($richval) {
			$post .= '&richtype=1&richval=,' . $richval . '&pic_bo=bgBuAAAAAAADACU! bgBuAAAAAAADACU!';
		}

		$post .= '&special_url=&subrichtype=1&con=' . $content . '&feedversion=1&ver=1&ugc_right=1&to_tweet=0&to_sign=0&time=' . $time . '&hostuin=' . $this->uin . '&code_version=1&format=json';
		$json = $this->get_curl($url, $post, 0, $this->cookie);

		if ($json) {
			$arr = json_decode($json, true);
			print_r($arr);

			if ($arr[code] == 0) {
				$this->shuotid = $arr[tid];
				$this->msg[] = $this->uin . '发布定时说说成功[PC]';
			}
			else if ($arr[code] == -3000) {
				$this->skeyzt = 1;
				$this->error[] = '发布定时说说失败[PC]！原因:' . $arr[message];
			}
			else {
				$this->error[] = $this->uin . '发布定时说说失败[PC]！原因' . $json;
			}
		}
		else {
			$this->error[] = $this->uin . '获取发布定时说说结果失败[PC]';
		}
	}
	/**
	 * [timedel 删除定时说说]
	 * @return [type] [description]
	 */
	public function timedel()
	{
		$sendurl = 'http://user.qzone.qq.com/q/taotao/cgi-bin/emotion_cgi_pubnow_timershuoshuo_v6?g_tk=' . $this->gtk;
		$url = 'http://user.qzone.qq.com/q/taotao/cgi-bin/emotion_cgi_del_timershuoshuo_v6?g_tk=' . $this->gtk;
		$post = 'hostuin=' . $this->uin . '&tid=1&time=1426176000&code_version=1&format=json&qzreferrer=http://user.qzone.qq.com/' . $this->uin . '/311';
	}

	public function cpqd($content = "签到", $sealid = "10761")
	{
		$url = 'http://m.qzone.com/mood/publish_signin';
		$post = 'opr_type=publish_signin&res_uin=' . $this->uin . '&content=' . $content . '&lat=0&lon=0&lbsid=&seal_id=' . $sealid . '&seal_proxy=&is_winphone=0&source_name=&format=json&sid=' . $this->sid;
		$json = $this->get_curl($url, $post, 'http://m.qzone.com/infocenter?g_ut=3&g_f=6676', 0, 0, $ua);
		$arr = json_decode($json, true);
		if (@array_key_exists('code', $arr) && ($arr['code'] == 0)) {
			$this->msg[] = $this->uin . '签到成功[CP]';
		}
		else if ($arr['code'] == -11210) {
			$this->error[] = $this->uin . '签到失败[CP]！原因:' . $arr['message'];
		}
		else {
			$this->error[] = $this->uin . '签到失败[CP]！原因:' . $arr['message'];
		}
	}
	/**
	 * [pcqd 电脑版签到]
	 * @param  string $content [签到内容]
	 * @param  string $sealid  [sealid]
	 * @return [type]          [description]
	 */
	public function pcqd($content = "", $sealid = "10761")
	{
		$url = 'http://snsapp.qzone.qq.com/cgi-bin/signin/checkin_cgi_publish?g_tk=' . $this->gtk;
		$post = 'qzreferrer=http://cm.qzs.qq.com/qzone/app/checkin_v4/html/checkin.html&plattype=1&hostuin=' . $this->uin . '&seal_proxy=&ttype=1&termtype=1&content=' . $content . '&seal_id=' . $sealid . '&uin=' . $this->uin . '&time_for_qq_tips=' . time() . '&paramstr=1';
		$get = $this->get_curl($url, $post, 0, $this->cookie);
		preg_match('/callback\((.*?)\)\; <\/script>/is', $get, $json);

		if ($json = $json[1]) {
			$arr = json_decode($json, true);
			$arr[feedinfo] = '';

			if ($arr[code] == 0) {
				$this->msg[] = $this->uin . '签到成功[PC]';
			}
			else if ($arr[code] == -3000) {
				$this->skeyzt = 1;
				$this->error[] = $this->uin . '签到失败[PC]！原因:' . $arr[message];
			}
			else {
				$this->error[] = $this->uin . '签到失败[PC]！原因' . $json;
			}
		}
		else {
			$this->error[] = $this->uin . '获取签到结果失败[PC]';
		}
	}
	/**
	 * [qiandao 签到]
	 * @param  integer $do      [description]
	 * @param  string  $content [签到内容]
	 * @param  integer $sealid  [sealid]
	 * @return [type]           [description]
	 */
	public function qiandao($do = 0, $content = "签到", $sealid = 10319)
	{
		if ($do) {
			$this->pcqd($content, $sealid);

			if ($this->skeyzt) {
				break;
			}
		}
		else {
			$this->cpqd($content, $sealid);
		}
	}
	/**
	 * [cpshuo 发表说说]
	 * @param  [type] $content [说说内容]
	 * @param  string $richval [说说引用内容，比如说发说说时带上的照片]
	 * @param  string $sname   [sname]
	 * @param  string $lon     [lon]
	 * @param  string $lat     [lat]
	 * @return [type]          [description]
	 */
	public function cpshuo($content, $richval = "", $sname = "", $lon = "", $lat = "")
	{
		$url = 'http://m.qzone.com/mood/publish_mood';
		$post = 'opr_type=publish_shuoshuo&res_uin=' . $this->uin . '&content=' . $content . '&richval=' . $richval . '&lat=' . $lat . '&lon=' . $lon . '&lbsid=&issyncweibo=0&is_winphone=2&format=json&source_name=' . $sname . '&sid=' . $this->sid;
		$result = $this->get_curl($url, $post);
		$json = json_decode($result, true);
		if (@array_key_exists('code', $json) && ($json[code] == 0)) {
			$this->msg[] = $this->uin . '发布说说成功[CP]';
		}
		else {
			$this->error[] = $this->uin . '发布说说失败[CP]，原因：' . $json[message];
		}
	}

	public function pcshuo($content, $richval = 0, $sname = "")
	{
		$url = 'http://user.qzone.qq.com/q/taotao/cgi-bin/emotion_cgi_publish_v6?g_tk=' . $this->gtk;
		$post = 'syn_tweet_verson=1&paramstr=1&pic_template=';

		if ($richval) {
			$post .= '&richtype=1&richval=,' . $richval . '&pic_bo=bgBuAAAAAAADACU! bgBuAAAAAAADACU!';
		}

		$post .= '&special_url=&subrichtype=1&who=1&con=' . $content . '&feedversion=1&ver=1&ugc_right=1&to_tweet=0&to_sign=0&hostuin=' . $this->uin . '&code_version=1&format=json&qzreferrer=http://user.qzone.qq.com/' . $this->uin . '/infocenter';
		$json = $this->get_curl($url, $post, 0, $this->cookie);

		if ($json) {
			$arr = json_decode($json, true);
			$arr[feedinfo] = '';

			if ($arr[code] == 0) {
				$this->msg[] = $this->uin . '发布说说成功[PC]';
			}
			else if ($arr[code] == -3000) {
				$this->skeyzt = 1;
				$this->error[] = '发布说说失败[PC]！原因:' . $arr[message];
			}
			else if ($arr[code] == -10045) {
				$this->error[] = $this->uin . '发布说说失败[PC]！原因:' . $arr[message];
			}
			else {
				$this->error[] = $this->uin . '发布说说失败[PC]！原因' . $json;
			}
		}
		else {
			$this->error[] = $this->uin . '获取发布说说结果失败[PC]';
		}
	}

	public function shuo($do = 0, $content, $image = 0, $type = 0, $sname = "")
	{
		if (!$type && $image) {
			if ($pic = $this->get_curl($image)) {
				$richval = $this->uploadimg($pic);
			}
		}
		else {
			$richval = $image;
		}

		if ($do) {
			$this->pcshuo($content, $richval, $sname);
		}
		else {
			$this->cpshuo($content, $richval, $sname);
		}
	}

	public function pcdel($cellid, $appid)
	{
		$url = 'http://user.qzone.qq.com/q/taotao/cgi-bin/emotion_cgi_delete_v6?g_tk=' . $this->gtk;
		$post = 'hostuin=' . $this->uin . '&tid=' . $cellid . '&t1_source=1&code_version=1&format=json&qzreferrer=http://user.qzone.qq.com/' . $this->uin . '/' . $appid . '';
		$json = $this->get_curl($url, $post, 0, $this->cookie);

		if ($json) {
			$arr = json_decode($json, true);
			if (@array_key_exists('code', $arr) && ($arr['code'] == 0)) {
				$this->msg[] = '删除说说' . $cellid . '成功[PC]';
			}
			else if ($arr[code] == -3000) {
				$this->skeyzt = 1;
				$this->error[] = '删除说说' . $this->touin . '失败[PC]！原因' . $arr[message];
			}
			else {
				$this->error[] = '删除说说' . $this->touin . '失败[PC]！原因' . $json;
			}
		}
		else {
			$this->error[] = $this->uin . '获取删除结果失败[PC]';
		}
	}

	public function cpdel($cellid, $appid)
	{
		$url = 'http://m.qzone.com/operation/operation_add?g_tk=' . $this->gtk;
		$post = 'opr_type=delugc&res_type=' . $appid . '&res_id=' . $cellid . '&real_del=0&res_uin=' . $this->uin . '&format=json&sid=' . $this->sid;
		$ua = 'Mozilla/5.0 (Linux; U; Android 4.0.3; zh-CN; Lenovo A390t Build/IML74K) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 UCBrowser/9.8.9.457 U3/0.8.0 Mobile Safari/533.1';
		$json = $this->get_curl($url, $post, 'http://m.qzone.com/infocenter?g_ut=3&g_f=6676', 0, 0, $ua);
		$arr = json_decode($json, true);
		if (@array_key_exists('code', $arr) && ($arr['code'] == 0)) {
			$this->msg[] = '删除说说' . $cellid . '成功[CP]';
		}
		else {
			$this->error[] = '删除说说' . $cellid . '失败[CP]！原因:' . $arr['message'];
		}
	}

	public function shuodel($do = 0)
	{
		if ($shuos = $this->getnew('my')) {
			foreach ($shuos as $shuo ) {
				$appid = $shuo['comm']['appid'];
				$cellid = $shuo['id']['cellid'];

				if ($do) {
					$this->pcdel($cellid, $appid);

					if ($this->skeyzt) {
						break;
					}
				}
				else {
					$this->cpdel($cellid, $appid);
				}
			}
		}
	}

	public function cpreply($content, $uin, $cellid, $type, $param)
	{
		$post = 'res_id=' . $cellid . '&res_uin=' . $uin . '&format=json&res_type=' . $type . '&content=' . $content . '&busi_param=' . $param . '&opr_type=addcomment&sid=' . $this->sid;
		$url = 'http://m.qzone.com/operation/publish_addcomment?g_tk=' . $this->gtk;
		$ua = 'Mozilla/5.0 (Linux; U; Android 4.0.3; zh-CN; Lenovo A390t Build/IML74K) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 UCBrowser/9.8.9.457 U3/0.8.0 Mobile Safari/533.1';
		$json = $this->get_curl($url, $post, 'http://m.qzone.com/infocenter?g_ut=3&g_f=6676', 0, 0, $ua);

		if ($json) {
			$arr = json_decode($json, true);
			if (@array_key_exists('code', $arr) && ($arr['code'] == 0)) {
				$this->msg[] = '评论' . $uin . '的说说成功[CP]！';
			}
			else {
				$this->error[] = '评论' . $uin . '的说说失败[CP]！原因:' . $arr['message'];
			}
		}
		else {
			$this->error[] = '获取评论' . $uin . '的说说结果失败[CP]！';
		}
	}

	public function pcreply($content, $uin, $cellid, $from, $richval = 0)
	{
		$richtype = ($richval ? '1' : '');
		$post = 'topicId=' . $uin . '_' . $cellid . '__' . $from . '&feedsType=100&inCharset=utf-8&outCharset=utf-8&plat=qzone&source=ic&hostUin=' . $uin . '&isSignIn=&platformid=52&uin=' . $this->uin . '&format=json&ref=feeds&content=' . $content . '&private=0&paramstr=1&qzreferrer=http://user.qzone.qq.com/' . $this->uin;
		$url = 'http://user.qzone.qq.com/q/taotao/cgi-bin/emotion_cgi_re_feeds?g_tk=' . $this->gtk;
		$json = $this->get_curl($url, $post, 0, $this->cookie);

		if ($json) {
			$arr = json_decode($json, true);
			$arr[data][feeds] = '';

			if ($arr[code] == 0) {
				$this->msg[] = '评论' . $uin . '的说说成功[PC]';
			}
			else if ($arr[code] == -3000) {
				$this->skeyzt = 1;
				$this->error[] = '评论' . $uin . '的说说失败[PC]！原因:' . $arr[message];
			}
			else if ($arr[code] == -10052) {
				$this->error[] = '评论' . $uin . '的说说失败[PC]！原因:' . $arr[message];
			}
			else if ($arr[code] == -10025) {
				$this->error[] = '评论' . $uin . '的说说失败[PC]！原因:' . $arr[message];
			}
			else {
				$this->error[] = '评论' . $uin . '的说说失败[PC]！原因' . $json;
			}
		}
		else {
			$this->error[] = $this->uin . '获取评论结果失败[PC]';
		}
	}

	public function reply($content = "", $do = 0, $richval = 0)
	{
		if ($shuos = $this->getnew()) {
			foreach ($shuos as $shuo ) {
				if ($this->is_comment($this->uin, $shuo['comment']['comments'])) {
					$appid = $shuo['comm']['appid'];
					$typeid = $shuo['comm']['feedstype'];
					$curkey = urlencode($shuo['comm']['curlikekey']);
					$uinkey = urlencode($shuo['comm']['orglikekey']);
					$uin = $shuo['userinfo']['user']['uin'];
					$from = $shuo['userinfo']['user']['from'];
					$cellid = $shuo['id']['cellid'];
					$this->touin = $uin;

					if ($do) {
						$this->pcreply($content, $uin, $cellid, $from, $richval);

						if ($this->skeyzt) {
							break;
						}
					}
					else {
						$param = $this->array_str($shuo['operation']['busi_param']);
						$this->cpreply($content, $uin, $cellid, $appid, $param);
					}
				}
			}
		}
	}

	public function cplike($uin, $type, $uinkey, $curkey)
	{
		$post = 'opr_type=like&action=0&res_uin=' . $uin . '&res_type=' . $type . '&uin_key=' . $uinkey . '&cur_key=' . $curkey . '&format=json&sid=' . $this->sid;
		$url = 'http://m.qzone.com/praise/like?g_tk=' . $this->gtk;
		$ua = 'Mozilla/5.0 (Linux; U; Android 4.0.3; zh-CN; Lenovo A390t Build/IML74K) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 UCBrowser/9.8.9.457 U3/0.8.0 Mobile Safari/533.1';
		$json = $this->get_curl($url, $post, 'http://m.qzone.com/infocenter?g_ut=3&g_f=6676', 0, 0, $ua);

		if ($json) {
			$arr = json_decode($json, true);
			if (@array_key_exists('code', $arr) && ($arr['code'] == 0)) {
				$this->msg[] = '赞' . $uin . '的说说成功[CP]';
			}
			else if ($arr[code] == -3000) {
				$this->sidzt = 1;
				$this->error[] = '赞' . $uin . '的说说失败[CP]！原因:' . $arr['message'];
			}
			else if ($arr['code'] == -11210) {
				$this->error[] = '赞' . $uin . '的说说失败[CP]！原因:' . $arr['message'];
			}
			else {
				$this->error[] = '赞' . $uin . '的说说失败[CP]！原因:' . $arr['message'];
			}
		}
		else {
			$this->error[] = '获取赞' . $uin . '的说说结果失败[CP]！';
		}
	}

	public function pclike($curkey, $uinkey, $from, $appid, $typeid, $abstime, $fid)
	{
		$post = 'qzreferrer=http://user.qzone.qq.com/' . $this->uin . '&opuin=' . $this->uin . '&unikey=' . $uinkey . '&curkey=' . $curkey . '&from=' . $from . '&appid=' . $appid . '&typeid=' . $typeid . '&abstime=' . $abstime . '&fid=' . $fid . '&active=0&fupdate=1';
		$url = 'http://w.qzone.qq.com/cgi-bin/likes/internal_dolike_app?g_tk=' . $this->gtk;
		$get = $this->get_curl($url, $post, 0, $this->cookie);
		preg_match('/callback\((.*?)\)\;/is', $get, $json);

		if ($json = $json[1]) {
			$arr = json_decode($json, true);
			if (($arr[message] == 'succ') || ($arr[msg] == 'succ')) {
				$this->msg[] = '赞' . $this->touin . '的说说成功[PC]';
			}
			else if ($arr[code] == -3000) {
				$this->skeyzt = 1;
				$this->error[] = '赞' . $this->touin . '的说说失败[PC]！原因:' . $arr[message];
			}
			else {
				$this->error[] = '赞' . $this->touin . '的说说失败[PC]！原因' . $json;
			}
		}
		else {
			$this->error[] = $this->uin . '获取赞结果失败[PC]';
		}
	}

	public function newpclike()
	{
		$url = 'http://ic2.s51.qzone.qq.com/cgi-bin/feeds/feeds3_html_more?format=json&begintime=' . time() . '&count=20&uin=' . $this->uin . '&g_tk=' . $this->gtk;
		$json = $this->get_curl($url, 0, 0, $this->cookie);
		$arr = json_decode($json, true);

		if ($arr[code] == -3000) {
			$this->skeyzt = 1;
			$this->error[] = $this->uin . '获取说说列表失败，原因SKEY过期！[PC]';
		}
		else {
			$this->msg[] = $this->uin . '获取说说列表成功[PC]';
			$json = str_replace(array("\\x22", "\\x3C", "\/"), array('"', '<', '/'), $json);

			if (preg_match_all('/data\-unikey="([0-9A-Za-z\.\-\_\/\:]+)" data\-curkey="([0-9A-Za-z\.\-\_\/\:]+\/([0-9A-Za-z]+))" data\-clicklog="like" href="javascript\:\;"><i class="ui\-icon icon\-praise"><\/i>赞/iUs', $json, $arr)) {
				foreach ($arr[1] as $k => $row ) {
					preg_match('/\/(\d+)\//', $row, $match);
					$this->touin = $match[1];
					$type = 0;
					$key = $arr[2][$k];
					$fid = $arr[3][$k];

					if ($row != $key) {
						$type = 5;
					}

					$this->pclike($key, $row, 1, '311', $type, time(), $fid);

					if ($this->skeyzt) {
						break;
					}
				}
			}
			else {
				$this->msg[] = $this->uin . '没有要赞的说说[PC]';
			}
		}
	}

	public function like($do = 0)
	{
		if ($do) {
			$this->newpclike();
		}
		else if ($shuos = $this->getnew()) {
			foreach ($shuos as $shuo ) {
				$like = $shuo['like']['isliked'];

				if ($like == 0) {
					$appid = $shuo['comm']['appid'];
					$typeid = $shuo['comm']['feedstype'];
					$curkey = urlencode($shuo['comm']['curlikekey']);
					$uinkey = urlencode($shuo['comm']['orglikekey']);
					$uin = $shuo['userinfo']['user']['uin'];
					$from = $shuo['userinfo']['user']['from'];
					$abstime = $shuo['comm']['time'];
					$cellid = $shuo['id']['cellid'];
					$this->touin = $uin;

					if ($do) {
						$this->pclike($curkey, $uinkey, $from, $appid, $typeid, $abstime, $cellid);

						if ($this->skeyzt) {
							break;
						}
					}
					else {
						$this->cplike($uin, $appid, $uinkey, $curkey);
					}
				}
			}
		}
	}

	public function getnew($do = "")
	{
		$ua = 'Mozilla/5.0 (Linux; U; Android 4.0.3; zh-CN; Lenovo A390t Build/IML74K) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 UCBrowser/9.8.9.457 U3/0.8.0 Mobile Safari/533.1';

		if ($do == 'my') {
			$url = 'http://m.qzone.com/list?g_tk=' . $this->gtk . '&res_attach=att%3D10&format=json&list_type=shuoshuo&action=0&res_uin=' . $this->uin . '&count=20&sid=' . $this->sid;
		}
		else {
			$url = 'http://m.qzone.com/get_feeds?res_type=0&res_attach=&refresh_type=2&format=json&sid=' . $this->sid;
		}

		$json = $this->get_curl($url, 0, 0, 0, 0, $ua);
		$json = preg_replace('/([\x80-\xff]*)/i', '', $json);
		$arr = json_decode($json, true);
		if (@array_key_exists('code', $arr) && ($arr['code'] == 0)) {
			$this->msg[] = $this->uin . '获取说说列表成功！';
			return $arr['data']['vFeeds'];
		}
		else if ($arr['code'] == -3000) {
			$this->sidzt = 1;
			$this->error[] = $this->uin . 'SID过期';
			return NULL;
		}
		else {
			$this->error[] = $this->uin . '获取说说列表失败，' . $json;
			return NULL;
		}
	}

	public function vipqd()
	{
		$url = "http://vipfunc.qq.com/growtask/sign.php?cb=vipsign.signCb&action=daysign&actId=16&t=" . time() . "141&g_tk=" . $this->gtk;
		$dd = "http://vipfunc.qq.com/act/client_oz.php?action=client&g_tk=" . $this->gtk;
		$this->get_curl($url,0,0,$this->cookie);
		$this->get_curl($dd,0,0,$this->cookie);
		$this->msg[] = "VIP签到成功~";
	}

	public function get_curl($url, $post = 0, $referer = 0, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		if ($post) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}

		if ($header) {
			curl_setopt($ch, CURLOPT_HEADER, true);
		}

		if ($cookie) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}

		if ($referer) {
			if ($referer == 1) {
				curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
			}
			else {
				curl_setopt($ch, CURLOPT_REFERER, $referer);
			}
		}

		if ($ua) {
			curl_setopt($ch, CURLOPT_USERAGENT, $ua);
		}
		else {
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0');
		}

		if ($nobaody) {
			curl_setopt($ch, CURLOPT_NOBODY, 1);
		}
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;
	}

	public function getGTK($skey)
	{
		$len = strlen($skey);
		$hash = 5381;

		for ($i = 0; $i < $len; $i++) {
			$hash += ($hash << 5) + ord($skey[$i]);
		}
		return $hash & 0x7fffffff;
	}

	public function is_comment($uin, $arrs)
	{
		if ($arrs) {
			foreach ($arrs as $arr ) {
				if ($arr['user']['uin'] == $uin) {
					return false;
					break;
				}
			}

			return true;
		}
		else {
			return true;
		}
	}

	public function array_str($array)
	{
		$str = '';

		if ($array[-100]) {
			$array100 = explode(' ', trim($array[-100]));
			$new100 = implode('+', $array100);
			$array[-100] = $new100;
		}

		foreach ($array as $k => $v ) {
			if ($k != '-100') {
				$str = $str . $k . '=' . $v . '&';
			}
		}

		$str = urlencode($str . '-100=') . $array[-100] . '+';
		$str = str_replace(':', '%3A', $str);
		return $str;
	}

	public function uploadimg($image, $image_size = array())
	{
		$url = 'http://up.qzone.com/cgi-bin/upload/cgi_upload_pic_v2';
		$post = 'picture=' . urlencode(base64_encode($image)) . '&base64=1&hd_height=' . $image_size[1] . '&hd_width=' . $image_size[0] . '&hd_quality=90&output_type=json&preupload=1&charset=utf-8&output_charset=utf-8&logintype=sid&Exif_CameraMaker=&Exif_CameraModel=&Exif_Time=&uin=' . $this->uin . '&sid=' . $this->sid;
		$data = preg_replace('/' . "\s" . '/', '', $this->get_curl($url, $post));
		preg_match('/_Callback\((.*)\);/', $data, $arr);
		$data = json_decode($arr[1], true);
		if ($data && array_key_exists("filemd5", $data)) {
			$this->msg[] = '图片上传成功！';
			$post = 'output_type=json&preupload=2&md5=' . $data['filemd5'] . '&filelen=' . $data['filelen'] . '&batchid=' . time() . rand(100000, 999999) . '&currnum=0&uploadNum=1&uploadtime=' . time() . '&uploadtype=1&upload_hd=0&albumtype=7&big_style=1&op_src=15003&charset=utf-8&output_charset=utf-8&uin=' . $this->uin . '&sid=' . $this->sid . '&logintype=sid&refer=shuoshuo';
			$img = preg_replace('/' . "\s" . '/', '', $this->get_curl($url, $post));
			preg_match('/_Callback\(\[(.*)\]\);/', $img, $arr);
			$data = json_decode($arr[1], true);
			if ($data && array_key_exists("picinfo", $data)) {
				if ($data[picinfo][albumid] != '') {
					$this->msg[] = '图片信息获取成功！';
					return '' . $data["picinfo"]["albumid"] . ',' . $data["picinfo"]["lloc"] . ',' . $data["picinfo"]["sloc"] . ',' . $data["picinfo"]["type"] . ',' . $data["picinfo"]["height"] . ',' . $data["picinfo"]["width"] . ',,,';
				}
				else {
					$this->msg[] = '图片信息获取失败！';
					return NULL;
				}
			}
			else {
				$this->error[] = '图片信息获取失败！';
				return NULL;
			}
		}
		else {
			$this->error[] = '图片上传失败！原因：' . $data['msg'];
			return NULL;
		}
	}
}

?>
