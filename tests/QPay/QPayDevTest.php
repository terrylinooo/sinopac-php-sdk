<?php

namespace Sinopac\Test\QPay;

use PHPUnit\Framework\TestCase;
use Sinopac\QPay\Algorithm;

class QPayDevTest extends TestCase
{
    use Algorithm;

    public function test_method_getHashedMessageBody()
    {
        $json = '{
            "Amount": 50000,
            "BackendURL": "http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess",
            "CurrencyID": "TWD",
            "OrderNo": "A201804270001",
            "PayType": "A",
            "PrdtName": "虛擬帳號訂單",
            "ReturnURL": "http://10.11.22.113:8803/QPay.ApiClient/Store/Return",
            "ShopNo": "BA0026_001"
        }';
          
        $string = rawurldecode(http_build_query(json_decode($json, true)));
        $result = $this->getHashedMessageBody($this->getMessageBody());

        $this->assertSame($string, $result);
    }

    public function test_method_getSha256()
    {
        $raw = 'Amount=50000&BackendURL=http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess&CurrencyID=TWD&OrderNo=A201804270001&PayType=A&PrdtName=虛擬帳號訂單&ReturnURL=http://10.11.22.113:8803/QPay.ApiClient/Store/Return&ShopNo=BA0026_001';
        $raw .= 'NjM2NjA0MzI4ODIyODguMzo3NzI0ZDg4ZmI5Nzc2YzQ1MTNhYzg2MTk3NDBlYTRhNGU0N2IxM2Q2M2JkMTIwOGU5YzZhMGFmNGY5MjA5YzVm';
        $raw .= '17D8E6558DC60E702A6B57E1B9B7060D';

        $string = strtoupper(hash('sha256', $raw));

        $this->assertSame($string, $this->getSha256($raw));
    }

    public function test_method_getIV()
    {
        $nonce = 'NjM2NjA0MzI4ODIyODguMzo3NzI0ZDg4ZmI5Nzc2YzQ1MTNhYzg2MTk3NDBlYTRhNGU0N2IxM2Q2M2JkMTIwOGU5YzZhMGFmNGY5MjA5YzVm';
        $string = strtoupper(hash('sha256', $nonce));
        $string = substr($string, -16);

        $this->assertSame($string, $this->getIV($nonce));
    }

    public function test_method_getHashId()
    {
        $A1 = '4D9709D699CA40EE';
        $A2 = '5A4FEF83140C4E9E';
        $B1 = 'BC74301945134CB4';
        $B2 = '961F67F8FCA44AB9';

        $result = $this->getHashId($A1, $A2, $B1, $B2);

        $this->assertSame('17D8E6558DC60E702A6B57E1B9B7060D', $result);
    }

    public function test_method_aesEncrypt()
    {
        $nonce = 'NjM2NjA0MzI4ODIyODguMzo3NzI0ZDg4ZmI5Nzc2YzQ1MTNhYzg2MTk3NDBlYTRhNGU0N2IxM2Q2M2JkMTIwOGU5YzZhMGFmNGY5MjA5YzVm';
        $hashId = '17D8E6558DC60E702A6B57E1B9B7060D';
        $iv = $this->getIV($nonce);
        $data = $this->getMessageBody();

        $encryptedString = $this->aesEncrypt($data, $hashId, $iv);
        $expectedString = '2C236A4E91DB2F7670E79BBCE3A626EB728916919012681FF92BE0B4BBF57F5519AF1A469A1D8710B202CB2C2F3C12A770788D825AD0F0A22AED518545A0D244AD0F9C37C7C693EFFABE78B606BCDAED6284902F7F522BBA85D9BE7EFEF46C6793FB6A5D6624C2642A74EB312034BEA931EE3A5F3C660F3ABAA9032949AE86DEFEB452545807561D282C7B7C8E9102CE134639CF8172577B4250CD4BF4AC30589A4B34BBDF0A2DF8F908E9FA42E22DA13C5294C5E6C48DE6662B145CAE29249203343D53C35F76C21FFA492DB33E12E14B731956ABD92D40B6C9D12F122132A84FE39D0D213486037EC4923F689BF0805D38EEF06B8D5E8C441CF8AD76A29E0CEF9E06715E608095CE6B0A86F3DD702795B0C1C9E488C61F6D07F9FCD84EE7D1508523E8365B44EFC9B99A4BF3FD42D13B9742F48E055602D55736F083F5367FC05378430FA56D28BFF12660636EC32FE054987DAB24F51D1341D9514A1BFBC64C8917DBEE6D19D351803088A7963F1346223F968A237B29AF19BE98EED176A5';
        $this->assertSame($expectedString, $encryptedString);
    }

    public function test_method_getSign()
    {
        $nonce = 'NjM2NjA0MzI4ODIyODguMzo3NzI0ZDg4ZmI5Nzc2YzQ1MTNhYzg2MTk3NDBlYTRhNGU0N2IxM2Q2M2JkMTIwOGU5YzZhMGFmNGY5MjA5YzVm';
        $hashId = '17D8E6558DC60E702A6B57E1B9B7060D';
        $data = $this->getMessageBody();

        $this->assertSame(
            'A3EAEE3B361B7E7E9B0F6422B954ECA5D54CEC6EAB0880CB484AA6FDA4154331',
            $this->getSign($data, $nonce, $hashId)
        );
    }

    /**
     * Data provider for the test `getHashedMessageBody`.
     *
     * @return array
     */
    private function getMessageBody(): array
    {
        $json = '{
            "ShopNo": "BA0026_001",
            "OrderNo": "A201804270001",
            "Amount": 50000,
            "CurrencyID": "TWD",
            "PayType": "A",
            "ATMParam": { "ExpireDate": "20180502" },
            "CardParam": { },
            "ConvStoreParam": { },
            "PrdtName": "虛擬帳號訂單",
            "ReturnURL": "http://10.11.22.113:8803/QPay.ApiClient/Store/Return",
            "BackendURL": "http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess"
        }';

        /*
            $data = [
                'ShopNo'         => 'BA0026_001',
                'OrderNo'        => 'A201804270001',
                'Amount'         => 50000,
                'CurrencyID'     => 'TWD',
                'PayType'        => 'A',
                'ATMParam'       => ['ExpireDate' => '20180502'],
                'CardParam'      => [],
                'ConvStoreParam' => [],
                'PrdtName'       => '虛擬帳號訂單',
                'ReturnURL'      => 'http://10.11.22.113:8803/QPay.ApiClient/Store/Return',
                'BackendURL'     => 'http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess',
            ];
        */

        return json_decode($json, true);
    }


    public function getNonce()
    {
        $fields = [
            'ShopNo' => 'NA0249_001',
        ];

        // This is old-version sandbox API
        //$ch = curl_init('https://apisbx.sinopac.com/funBIZ/QPay.WebAPI/api/Nonce');

        $ch = curl_init('https://apisbx.sinopac.com/funBIZ-Sbx/QPay.WebAPI/api/Nonce');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-KeyID: b5e6986d-8636-4aa0-8c93-441ad14b2098',
        ]);

        $results = curl_exec($ch);
        $data = json_decode($results, true);

        return $data['Nonce'];
    }

    public function testGetNonce()
    {
        $data = $this->getNonce();
        echo "Get Nonce from API: $data \n";

        $this->assertTrue(!empty($data));
    }

    public function test_createOrder()
    {
        // Begin - 建立付款訂單模擬資料 ok

        $A1 = '86D50DEF3EB7400E';
        $A2 = '01FD27C09E5549E5';
        $B1 = '9E004965F4244953';
        $B2 = '7FB3385F414E4F91';

        $orderNo = 'TEST' . date('YmdHis');
        $expireDate = date('Ymd', strtotime('+1 day'));

        $data = [
            'ShopNo'         => 'NA0249_001',
            'OrderNo'        => $orderNo,
            'Amount'         => 50000,
            'CurrencyID'     => 'TWD',
            'PayType'        => 'A',
            'ATMParam'       => ['ExpireDate' => $expireDate],
            'PrdtName'       => '虛擬帳號訂單',
            'ReturnURL'      => 'http://10.11.22.113:8803/QPay.ApiClient/Store/Return',
            'BackendURL'     => 'http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess',
        ];

        // End - 建立付款訂單模擬資料

        $hashId = $this->getHashId($A1, $A2, $B1, $B2);
        $nonce = $this->getNonce();
        $iv = $this->getIV($nonce);
        $sign = $this->getSign($data, $nonce, $hashId);
        $message = $this->aesEncrypt($data, $hashId, $iv);

        $fields = [
            'Version'    => '1.0.0',
            'ShopNo'     => 'NA0249_001',
            'APIService' => 'OrderCreate',
            'Nonce'      => $nonce,
            'Sign'       => $sign,
            'Message'    => $message,
        ];

        $ch = curl_init('https://apisbx.sinopac.com/funBIZ-Sbx/QPay.WebAPI/api/Nonce');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-KeyID: b5e6986d-8636-4aa0-8c93-441ad14b2098',
        ]);

        $results = curl_exec($ch);

        echo "\n";
        echo "測試建立訂單結果如下：\n";
        echo $results;
        echo "\n";
    }

    public function test_method_aesDecrypt()
    {
        $A1 = '86D50DEF3EB7400E';
        $A2 = '01FD27C09E5549E5';
        $B1 = '9E004965F4244953';
        $B2 = '7FB3385F414E4F91';

        $json = '{
            "Version": "1.0.0",
            "ShopNo": "NA0249_001",
            "APIService": "OrderCreate",
            "Sign": "C8929F1A92550ED05E471B2C938EE19A0817F289197D9027696134BEDFD3961A",
            "Nonce": "NjM3Njc5NTg1MTE2NjUuNDozZGE0ODA2YmEyZGJmYjMyMDg4ODg3YjNkMmNiNmE2YjU5OWNhZjNhZmEzYWVlZGYwM2U5M2Y3Y2UyOWYyY2Yy",
            "Message": "A25E3DC32840C249955DD84177CC988E8D670F7218AF85F2479D26F80F26AF383FCC5399A56516D737556EDBDD0C4BBE4FD75856169014E453226408F044D6E63C5EC6FE4B386D5D0877899591CA0B7E5E7846F21396154AFC654E2FBD66D0E299D94FF2494582A78E3D2859F5C27FB631912229845DA38261171CA8B201EE5AE45B1EA299D76D8E001BC19DA698915E10A87F556653E0ABE266791989F954DAFF091DAE82A0FC331636BD602750ECBACFAD045C278DDA726CF1343E4E2193714BE19C35381ECF1ECD763E448063CF1FEA2382A27F2E74F6C43ED2A8DC3CB31A2AF28922E48C627774A07CAB18B3EACB4A284D2F6DFE7507640A851FE02D0B89F3D1C875CF969D75C8935FE01F048A621EF578D960AADD1EA86CD2481F0CD075D66CDEAFDE04DC086D3674FEFBED2B71913886319C5FF632AFDB8150523DA8FC1CBAB81B1C1C6A7C9419A6ED96990220F7103C7215FA6E0514EC1E48CE6F76EAB22E71D4D956172D3EE09AE392A64EBACFEF3F43458036E2A87DD02C730FE9F178F939E80B19A9279567B43967D585B9B8DFAF81265CB5522574EF8A67F415E978EE8F1F8717FF79A901F1E7293D9F925897E0D9FABA03187C1C9F61A1402AC24AF7E88F573790C1C3F1816C03649979"
        }';

        $data = json_decode($json, true);

        $nonce = $data['Nonce'];
        $message = $data['Message'];
        $iv = $this->getIV($nonce);
        $hashId = $this->getHashId($A1, $A2, $B1, $B2);

        $string = $this->aesDecrypt($message, $hashId, $iv);

        $expectedString = '{"OrderNo":"TEST20210922174825","ShopNo":"NA0249_001","TSNo":"NA024900000210","Amount":50000,"Status":"S","Description":"S0000 – 處理成功","PayType":"A","ATMParam":{"AtmPayNo":"99922530174617","WebAtmURL":"https://sandbox.sinopac.com/QPay.WebPaySite/Bridge/PayWebATM?TD=NA024900000210&TK=4d9057aa-3df1-48cb-8165-e5f7a12cc775","OtpURL":"https://sandbox.sinopac.com/QPay.WebPaySite/Bridge/PayOTP?TD=NA024900000210&TK=4d9057aa-3df1-48cb-8165-e5f7a12cc775"}}';

        $this->assertSame($expectedString, $string);

        echo "\n";
        echo "解密還原結果如下：\n";
        echo $string;
        echo "\n";
    }
}
