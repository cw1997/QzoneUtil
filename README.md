# QzoneUtil
QQ空间工具类

支持发表说说，定时说说，主页点赞，签到，留言，构造方法支持传入QQ号，skey和sid进行登录。

关于richval发表说说带上图片部分的说明，

————————————————————————————————————

上传图片：

提交地址：http://xaup.photo.qq.com/cgi-bin/upload/cgi_upload_image
然后到字节集 图片

根据返回的内容

  <?xml version="1.0" encoding="utf-8" ?> 
- <data>
- <data>
  <albumid>V10s5tfV3MAjfe</albumid> 
  <contentlen>5284</contentlen> 
  <height>37</height> 
  <limitpic>2000</limitpic> 
  <lloc>NDN0PRpRZtxmDVQyXF0ysi.AcnAQAAA!</lloc> 
  <pre>http://b192.photo.store.qq.com/psb?/V10s5tfV3MAjfe/TdNrZTMz7y2w.zRX0kyLumvQuMeE3mlU4m4Is6BzV5k!/a/dLIvgHJwEAAA&bo=SQAlAAAAAAAFAE8!</pre> 
  <sloc>NDN0PRpRZtxmDVQyXF0ysi.AcnAQAAA!</sloc> 
  <totalpic>1</totalpic> 
  <type>5</type> 
  <url>http://b192.photo.store.qq.com/psb?/V10s5tfV3MAjfe/TdNrZTMz7y2w.zRX0kyLumvQuMeE3mlU4m4Is6BzV5k!/b/dLIvgHJwEAAA&bo=SQAlAAAAAAAFAE8!</url> 
  <width>73</width> 
  </data>
  </data>

发布说说：

POST /q/taotao/cgi-bin/emotion_cgi_publish_v6?g_tk=2048430070 HTTP/1.1
Accept: */*
Accept-Language: zh-cn
Referer: http://user.qzone.qq.com/1716591165/infocenter
Content-Type: application/x-www-form-urlencoded;charset=utf-8
x-real-url: http://taotao.qq.com/cgi-bin/emo ... _v6?g_tk=2048430070
Accept-Encoding: gzip, deflate
User-Agent: Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET4.0C; .NET4.0E)
Host: user.qzone.qq.com
Connection: Keep-Alive
Cache-Control: no-cache
Cookie: 

syn_tweet_verson=1&paramstr=1&pic_template=&richtype=1&richval=%2CV10s5tfV3MAjfe%2CNDN0PRpRZtxmDVQyXF0ysi.AcnAQAAA!%2CNDN0PRpRZtxmDVQyXF0ysi.AcnAQAAA!%2C5%2C37%2C73%2C%2C37%2C73&special_url=&subrichtype=1&pic_bo=SQAlAAAAAAAFAE8!%09SQAlAAAAAAAFAE8!&who=1&con=happly&feedversion=1&ver=1&ugc_right=1&to_tweet=0&to_sign=0&hostuin=1716591165&code_version=1&format=fs&qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F1716591165%2Finfocenter
