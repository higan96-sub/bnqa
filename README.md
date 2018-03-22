# bnqa

![スクショ](http://higan96.info/bnqa.jpg)

- 過去に（2013年ごろに）公開していたWebアプリ「bnqa」のソースコード
- 職業プログラマになる前の作品。
- 当時のコードのため、動作は保証しません。
- リリース当時のブログ
  - [bnqaというWebサービスを作りました(2013-8-30)](http://higan96.hatenablog.com/entry/2013/08/30/073352 "bnqaというWebサービスを作りました")
  - [独学で2度目のWebサービスリリースまでにやったこと(2013-8-31)](http://higan96.hatenablog.com/entry/2013/08/31/224143 "Qiita")

# 概要
- 技術書や専門書などの学習コストの大きな本を中心としたソーシャルリーディングサービスを目指したもの。

# 技術
Linux(CentOS)/Apache/PHP5.4/Symfony2.3/Memcached/jQuery

# 挑戦したポイント
- フレームワークはSymfonyのLTSである2.3を採用。2016年までサポートの予定になっている。
- オブジェクトキャッシュとしてはMemcachedを採用。AmazonのProduct Advertising APIで取得したデータをAPIの規約を破らないように24時間だけ保存。ページの読み込みが700msから80ms程度まで短縮。
- AjaxにはjQuery、デザインはTwitterBootstrap3を使用。サーバーはAWSでEC2+RDSの使用を考えたが5000円/月程度かかるため、コストの面でさくらVPSを選択。

# License
Copyright (c) 2018 Norihiko Oba
Released under the MIT license
