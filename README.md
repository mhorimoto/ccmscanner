# CCMScanner

CCMデータ一覧を取得するPHPクラスライブラリです。リモートAPIからJSON形式でデータを取得し、条件によるフィルタリングを行います。

- Version 1.00  [20th Jan. 2026]

## 動作環境

- PHP 8.1 以上

## インストール

プロジェクトディレクトリに `CCMScanner.php` を配置してください。  
たとえば、/usr/local/lib/phpの配下など。

```bash
git clone git@github.com:mhorimoto/ccmscanner.git
cd ccmscanner
```

## 基本的な使い方

### 1. クラスの読み込み

```php
require_once('CCMScanner.php');
```

### 2. インスタンスの作成

```php
// デフォルトのAPIエンドポイントを使用
$scanner = new CCMScanner();

// または、カスタムAPIエンドポイントを指定
$scanner = new CCMScanner('https://your-api-endpoint.com/api/data.php');
```

### 3. データの取得

```php
// すべてのデータを取得
$allData = $scanner->scan();

// 条件を指定してフィルタリング
$filteredData = $scanner->scan(['ROOM' => 45]);

// 複数の条件でフィルタリング
$filteredData = $scanner->scan([
    'ROOM' => 45,
    'CCMTYPE' => 'temperature'
]);
```

### 4. 取得したデータの利用

```php
foreach ($filteredData as $item) {
    echo "ROOM: {$item['ROOM']}, VALUE: {$item['VALUE']}\n";
}
```

## サンプルコード

完全な動作例は `ccmscan_test.php` を参照してください。

```php
#!/usr/bin/php
<?php
require_once('CCMScanner.php');
$scanner = new CCMScanner();

// ROOM番号45のデータを取得
$data = $scanner->scan(['ROOM' => 45]);

// 結果を表示
foreach ($data as $n => $item) {
    $dateStr = date('Y-m-d H:i:s', $item['FTIME']); 
    echo "[$n] {$item['ROOM']}-{$item['REGION']}-{$item['ORDER']}-{$item['PRIORITY']}/{$item['CCMTYPE']}/{$item['VALUE']}/ {$dateStr}\n";
}
?>
```

### 実行結果の例

```
[0] 45-1-1-15/WAirHumid/93.61/ 2026-01-20 00:11:20
[1] 45-1-1-15/WAirTemp/6.48/ 2026-01-20 00:11:20
[2] 45-1-1-15/WRadation/0/ 2026-01-20 00:11:20
[3] 45-1-1-15/WRadition/0/ 2026-01-20 00:11:20
[4] 45-1-1-29/cnd/0/ 2026-01-20 00:11:29
[5] 45-114-10-15/FLOW.mNB/0.0/ 2026-01-20 00:11:03
[6] 45-114-10-29/cnd.mNB/0/ 2026-01-20 00:11:31
[7] 45-114-10-5/CPUTEMP.mNB/23725/ 2026-01-20 00:11:03
[8] 45-2-1-15/EC_BULK/0.63/ 2026-01-20 00:10:47
[9] 45-2-1-15/EC_PORE/2.59/ 2026-01-20 00:10:47
[10] 45-2-1-15/InAirCO2/788/ 2026-01-20 00:11:24
[11] 45-2-1-15/InAirHumid/98.95/ 2026-01-20 00:11:13
[12] 45-2-1-15/InAirTemp/13.54/ 2026-01-20 00:11:13
[13] 45-2-1-15/PPFD/0/ 2026-01-20 00:10:47
```
上記はROOM=45という条件をつけているので少ないが、実際は数百個に達することもある。

```

### 実行方法

```bash
chmod +x ccmscan_test.php
./ccmscan_test.php
```

または

```bash
php ccmscan_test.php
```

## データ構造

APIから取得されるデータは以下のような構造を持ちます:

```php
[
    [
        'ROOM' => 45,
        'REGION' => 1,
        'ORDER' => 1,
        'PRIORITY' => 15,
        'CCMTYPE' => 'WAirHumid',
        'VALUE' => 93.61,
        'FTIME' => 1737331880  // Unixタイムスタンプ
    ],
    [
        'ROOM' => 45,
        'REGION' => 2,
        'ORDER' => 1,
        'PRIORITY' => 15,
        'CCMTYPE' => 'InAirTemp',
        'VALUE' => 13.54,
        'FTIME' => 1737331873
    ],
    // ...
]
```

### 主なCCMTYPE（センサー種別）

実際のデータから取得される主なセンサータイプ:

- `WAirHumid`: 外気湿度
- `WAirTemp`: 外気温度
- `WRadation`, `WRadition`: 放射量
- `InAirHumid`: 室内湿度
- `InAirTemp`: 室内温度
- `InAirCO2`: CO2濃度
- `EC_BULK`, `EC_PORE`: 電気伝導度
- `PPFD`: 光合成有効光量子束密度
- `FLOW.mNB`: 流量
- `cnd`, `cnd.mNB`: 導電率
- `CPUTEMP.mNB`: CPU温度

**UECSサーバに受信されているすべてのCCMを取得することができる。**

## メソッド

### `__construct($apiUrl = 'https://foo.bar.com/api/hogehoge.php')`

コンストラクタ。APIエンドポイントのURLを指定します。

**パラメータ:**
- `$apiUrl` (string, optional): APIエンドポイントのURL

### `scan($filters = [])`

リモートAPIからデータを取得し、指定された条件でフィルタリングして返します。

**パラメータ:**
- `$filters` (array, optional): フィルタリング条件の連想配列

**戻り値:**
- `array`: フィルタリングされたデータの配列。エラー時は空配列を返します。

**例:**

```php
// フィルタなし
$allData = $scanner->scan();

// 単一条件
$data = $scanner->scan(['ROOM' => 45]);

// 複数条件（AND条件）
$data = $scanner->scan([
    'ROOM' => 45,
    'REGION' => 1
]);
```

## エラーハンドリング

- API接続に失敗した場合や、JSONのパースに失敗した場合は、空の配列 `[]` を返します。
- タイムアウトは5秒に設定されています。

## 注意事項

- このライブラリは、SSL証明書の検証を無効化しています（`verify_peer` および `verify_peer_name` が `false`）。本番環境では適切な証明書検証を行うことを推奨します。
- APIエンドポイントへのアクセスには適切な認証・認可が必要な場合があります。

## ライセンス

MIT License

## 作者

堀本　正文 (<mh@ys-lab.tech>)

## 貢献

プルリクエストを歓迎します。大きな変更の場合は、まずissueを開いて変更内容について議論してください。