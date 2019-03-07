<?php
/**
 * Created by PhpStorm.
 * User: Pashted
 * Date: 29.11.2017
 * Time: 22:14
 */


$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => "https://min-api.cryptocompare.com/data/pricemulti?fsyms=BTC,USD,ETH,ZEC,SNT,CRC**,SUMO,TZC,ETP,RYO,BTG,XMR,ZEN,HUSH,XRP&tsyms=USD,RUB,BTC",
));
$cc = json_decode(curl_exec($curl));
curl_close($curl);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => "https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml",
    CURLOPT_POSTFIELDS => null
));
$xml = curl_exec($curl);
curl_close($curl);


$curs = new SimpleXMLElement($xml);


$usd = (array)$curs->Cube->Cube->Cube[0];
$usd = $usd['@attributes']['rate']; // стоимость евро в долларах
$rub = (array)$curs->Cube->Cube->Cube[14];
$rub = (float)$rub['@attributes']['rate']; // стоимость евро в рублях

$result = [
    "usd" => [
        'name' => "RUB-USD",
        'price' => $rub / $usd
    ],
    "btc" => [
        'name' => "USD-BTC",
        'price' => $cc->BTC->USD
    ]
];

$need = [
//    22, // cryptonight
    30, // cryptonightv7
    20, // daggerhashimoto
    24, // equihash
    14, // lyra2rev2
    8, // neoscrypt
//    7, // nist5
    33, // x16r
//    5 // keccak
    32 // keccak
];

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => "https://api.nicehash.com/api?method=simplemultialgo.info",
    CURLOPT_POSTFIELDS => null
));
$algo = json_decode(curl_exec($curl));
curl_close($curl);


foreach ($need as $i) {
    $result[$i] = [
        'price' => 0
    ];

    $algo->result->simplemultialgo[$i]->paying /= $i == 24 || $i == 30 ? 1000000000 : 1000;

    $result[$algo->result->simplemultialgo[$i]->algo] = [
//        'name'  => str_replace("hashimoto", "+pascal", $algo->result->simplemultialgo[$i]->name),
        'name' => $algo->result->simplemultialgo[$i]->name,
        'price' => $algo->result->simplemultialgo[$i]->paying
    ];
}

//$result[22]['price'] /= 1000000000;
//$result[30]['price'] /= 1000000000;
//$result[20]['price'] = ($result[20]['price'] + ($result[20]['price'] * 0.16)) / 1000;
//$result[20]['price'] /= 1000;
//$result[24]['price'] /= 1000000000;
//$result[14]['price'] /= 1000;
//$result[8]['price'] /= 1000;
//$result[7]['price'] /= 1000;
//$result[5]['price'] /= 1000;

foreach ($need as $n) {
    $result[$n]['price'] = number_format($result[$n]['price'], 10);
}
$result['eth'] = [
    'name' => "ETH-BTC",
    'price' => $cc->ETH->BTC
];
$result['zec'] = [
    'name' => "ZEC-BTC",
    'price' => $cc->ZEC->BTC
];
$result['snt'] = [
    'name' => "SNT-USD",
    'price' => $cc->SNT->USD
];
$result['tzc'] = [
    'name' => "TZC-USD",
    'price' => $cc->TZC->USD
];
$result['sumo'] = [
    'name' => "SUMO-USD",
    'price' => $cc->SUMO->USD
];
$result['ryo'] = [
    'name' => "RYO-USD",
    'price' => $cc->RYO->USD
];
$result['crc'] = [
    'name' => "CRC-USD",
    'price' => $cc->{'CRC**'}->USD
];
$result['etp'] = [
    'name' => "ETP-USD",
    'price' => $cc->ETP->USD
];
$result['btg'] = [
    'name' => "BTG-USD",
    'price' => $cc->BTG->USD
];
$result['xmr'] = [
    'name' => "XMR-USD",
    'price' => $cc->XMR->USD
];
$result['zen'] = [
    'name' => "ZEN-USD",
    'price' => $cc->ZEN->USD
];
$result['hush'] = [
    'name' => "HUSH-USD",
    'price' => $cc->HUSH->USD
];
$result['xrp'] = [
    'name' => "XRP-USD",
    'price' => $cc->XRP->USD
];
?>

<style>
    td, th {
        text-transform: capitalize;
        padding: 5px;
        font-family: "Calibri";
        font-size: 14px;
        text-align: right;
    }

    <?php foreach ($result as $code => $item) {
        echo "
    .{$item['name']}:before{
        content: '{$item['name']}'
    }";
     } ?>

    td[class]:before {
        color: #999;
        margin-right: 15px;
    }

    ::selection {
        background-color: #ccc;
    }
</style>

<table cellspacing="0">
    <?php foreach ($result as $code => $item) { ?>
        <tr>
            <?php // echo "<td>" . $item['name'] . "</td>"; ?>
            <td title="<?php echo $item['name']; ?>"
                class="<?php echo $item['name']; ?>"><?php echo str_replace(".", ",", $item['price']); ?></td>
        </tr>
    <? } ?>
</table>

<script type="text/javascript" src="/media/jui/js/jquery.js"></script>
<script type="text/javascript" src="/nh/script.js"></script>