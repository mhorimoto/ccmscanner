<?php
class CCMScanner {
    private $apiUrl;

    /**
     * コンストラクタで API の URL を指定します．
     */
    public function __construct($apiUrl='https://foo.bar.com/api/hogehoge.php') {
        $this->apiUrl = $apiUrl;
    }

    /**
     * リモート API からデータを取得し，フィルタリングして返します．
     */
    public function scan($filters = []) {
	$options = [
	    'http' => [
                'header' => "Host: uecsfarm.smart-agri.jp\r\n",
		'follow_location' => 1,
                'timeout' => 5
	    ],
	    'ssl' => [
                // IPアドレスでのアクセスによる「ドメイン名不一致」エラーを回避
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ];
        $context = stream_context_create($options);

	// API から JSON を取得
        $json = @file_get_contents($this->apiUrl,false, $context);
        if ($json === false) {
            return [];
        }

        $allData = json_decode($json, true);
        if (!is_array($allData)) {
            return [];
        }

        // フィルタリング処理
        $ccmdata = [];
        foreach ($allData as $item) {
            if ($this->matchFilters($item, $filters)) {
                $ccmdata[] = $item;
            }
        }

        return $ccmdata;
    }

    private function matchFilters($item, $filters) {
        foreach ($filters as $key => $value) {
            if (isset($item[$key]) && $item[$key] != $value) {
                return false;
            }
        }
        return true;
    }
}
