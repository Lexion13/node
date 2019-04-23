<?php

$html = file_get_contents("https://www.nikkei.com/markets/ranking/page/?bd=caphigh");
$url_list = [];

for($k=1;$k<=2;$k++){
  //関数で分離する方法
  $dom = new DOMDocument('1.0', 'UTF-8');
  $html = mb_convert_encoding($html, "HTML-ENTITIES", 'auto');
  @$dom->loadHTML($html);
  $xpath = new DOMXPath($dom);
  $xpath->registerNamespace("php", "http://php.net/xpath");
  $xpath->registerPHPFunctions();
  //ここまで

  for($j=1;$j<=5;$j++){
    for($i=1;$i<=10;$i++){
      $a = $xpath->query('//*[@id="CONTENTS_MARROW"]/div[2]/div[2]/table/tbody['.$j.']/tr['.$i.']/td[4]/a')->item(0);
      $name = $a->nodeValue;
      $about_url = str_replace("/nkd/company/", "https://www.nikkei.com/nkd/company/gaiyo/", $a->getAttribute('href'));
      $account_url = str_replace("/nkd/company/", "https://www.nikkei.com/nkd/company/kessan/", $a->getAttribute('href'));
      $stock_url = str_replace("/nkd/company/", "https://www.nikkei.com/nkd/company/history/dprice/", $a->getAttribute('href'));

      $url_list[] = [$name,$about_url,$account_url, $stock_url];
      //$url_list = [["会社名","概要URL","決算URL","株価URL"],["会社名","概要URL","決算URL","株価URL"],["会社名","概要URL","決算URL","株価URL"]]...
    }
    $i = 1;
  }
  $html = file_get_contents("https://www.nikkei.com/markets/ranking/page/?bd=caphigh&ba=00&Gcode=&excflag=1&hm=2");
  $i = 1;
  $j = 1;
}

$content_list = [];



for($l=0;$l<=99;$l++){

  $name = $url_list[$l][0];

  //概要URL
  $html = file_get_contents($url_list[$l][1]);
  $dom = new DOMDocument('1.0', 'UTF-8');
  $html = mb_convert_encoding($html, "HTML-ENTITIES", 'auto');
  @$dom->loadHTML($html);
  $xpath = new DOMXPath($dom);
  $xpath->registerNamespace("php", "http://php.net/xpath");
  $xpath->registerPHPFunctions();
  //発行済み株
  $hakko_kabu = $xpath->query('//*[@id="basicInformation"]/div/div[2]/div/div/table/tbody/tr[13]/td')->item(0)->nodeValue;
  $hakko_kabu = preg_replace('/[^0-9]/', '', $hakko_kabu);
  //日経業種分類
  $nikkei_bunrui = $xpath->query('//*[@id="basicInformation"]/div/div[2]/div/div/table/tbody/tr[8]/td')->item(0)->nodeValue;
  //東証業種分類
  $tosho_bunrui = $xpath->query('//*[@id="basicInformation"]/div/div[2]/div/div/table/tbody/tr[9]/td')->item(0)->nodeValue;


  //決算URL
  $html = file_get_contents($url_list[$l][2]);
  $dom = new DOMDocument('1.0', 'UTF-8');
  $html = mb_convert_encoding($html, "HTML-ENTITIES", 'auto');
  @$dom->loadHTML($html);
  $xpath = new DOMXPath($dom);
  $xpath->registerNamespace("php", "http://php.net/xpath");
  $xpath->registerPHPFunctions();
  //売上高
  $uriage = $xpath->query('//*[@id="CONTENTS_MAIN"]/div[7]/div/div[4]/div[2]/table/tbody[1]/tr[1]/td[5]')->item(0)->nodeValue;
  if ($uriage == NULL){
    $uriage = $xpath->query('//*[@id="CONTENTS_MAIN"]/div[8]/div/div[4]/div[2]/table/tbody[1]/tr[1]/td[5]')->item(0)->nodeValue;
    if($uriage == NULL){
      $uriage = $xpath->query('//*[@id="CONTENTS_MAIN"]/div[6]/div/div[4]/div[2]/table/tbody[1]/tr[1]/td[5]')->item(0)->nodeValue;
    }
  }
  //営業利益
  $eigyo = $xpath->query('//*[@id="CONTENTS_MAIN"]/div[7]/div/div[4]/div[2]/table/tbody[1]/tr[2]/td[5]')->item(0)->nodeValue;
  if ($eigyo == NULL){
    $eigyo = $xpath->query('//*[@id="CONTENTS_MAIN"]/div[8]/div/div[4]/div[2]/table/tbody[1]/tr[2]/td[5]')->item(0)->nodeValue;
    if($eigyo == NULL){
      $eigyo = $xpath->query('//*[@id="CONTENTS_MAIN"]/div[6]/div/div[4]/div[2]/table/tbody[1]/tr[2]/td[5]')->item(0)->nodeValue;
    }
  }
  //経常利益
  $keijo = $xpath->query('//*[@id="CONTENTS_MAIN"]/div[7]/div/div[4]/div[2]/table/tbody[1]/tr[3]/td[5]')->item(0)->nodeValue;
  if ($keijo == NULL){
    $keijo = $xpath->query('//*[@id="CONTENTS_MAIN"]/div[8]/div/div[4]/div[2]/table/tbody[1]/tr[3]/td[5]')->item(0)->nodeValue;
    if($keijo == NULL){
      $keijo = $xpath->query('//*[@id="CONTENTS_MAIN"]/div[6]/div/div[4]/div[2]/table/tbody[1]/tr[3]/td[5]')->item(0)->nodeValue;
    }
  }

  //株価URL
  $html = file_get_contents($url_list[$l][3]);
  $dom = new DOMDocument('1.0', 'UTF-8');
  $html = mb_convert_encoding($html, "HTML-ENTITIES", 'auto');
  @$dom->loadHTML($html);
  $xpath = new DOMXPath($dom);
  $xpath->registerNamespace("php", "http://php.net/xpath");
  $xpath->registerPHPFunctions();
  //終値
  $owarine = $xpath->query('//*[@id="CONTENTS_MAIN"]/div[6]/div/div/div[2]/div/table/tbody/tr[1]/td[6]')->item(0)->nodeValue;
  //時価総額
  $jikaso = $hakko_kabu * $owarine;
  $jikaso = number_format($jikaso);

  $content_list[] = [$name,$hakko_kabu,$nikkei_bunrui,$tosho_bunrui,$uriage,$eigyo,$keijo,$owarine,$jikaso];
}



?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.2.0/css/tableexport.min.css">
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.1/xlsx.core.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.2.0/js/tableexport.min.js"></script>

<table>
    <thead>
        <tr>
            <th>インデックス</th>
            <th>企業名</th>
            <th>日経業種分類</th>
            <th>東証業種分類</th>
            <th>発行株数</th>
            <th>終値</th>
            <th>時価総額</th>
            <th>売上高</th>
            <th>営業利益</th>
            <th>経常利益</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($content_list as $key => $val): ?>
            <tr>
                <td><?php echo $key + 1; ?></td>
                <td><?php echo $val[0]; ?></td>
                <td><?php echo $val[2]; ?></td>
                <td><?php echo $val[3]; ?></td>
                <td><?php echo $val[1]; ?></td>
                <td><?php echo $val[7]; ?></td>
                <td><?php echo $val[8]; ?></td>
                <td><?php echo $val[4]; ?></td>
                <td><?php echo $val[5]; ?></td>
                <td><?php echo $val[6]; ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>


<script>
    $(function(){
        $("table").tableExport({
          formats:["xlsx","csv"],
          position: "top",
          filename: "会社情報"
        });
      });
</script>
