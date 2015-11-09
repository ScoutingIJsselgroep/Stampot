<?PHP
$url = 'http://natsirt.nl/Codiad/workspace/Whatsapp-Webhook/index.php';

$optional_headers = null;
$params = array('http' => array(
                'method' => 'POST',
                'content' => http_build_query(array('key' => '7pjfKP0EbQ179Ynej31EB3eEz2o0I720', 'message' => 'Hoi! Je saldo bij de Stampot op Scouting IJsselgroep is €  neem dus geld mee!' , 'phone' => '31655922753'))));
 
if ($optional_headers !== null) {
    $params['http']['header'] = $optional_headers;
}
 
$ctx = stream_context_create($params);
$fp = @fopen($url, 'rb', false, $ctx);
?>