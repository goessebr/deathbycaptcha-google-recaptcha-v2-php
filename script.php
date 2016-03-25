<?
//curl http://www.google.com/recaptcha/api/noscript?k=6LeC3O0SAAAAAC_YcSZKQChES7yidMTP_h-6uTTX for manual recaptcha challenge
$ch = curl_init();                 
curl_setopt($ch, CURLOPT_URL,"http://www.google.com/recaptcha/api/noscript?k=6LeC3O0SAAAAAC_YcSZKQChES7yidMTP_h-6uTTX");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 45);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'torrentcookies.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

if(curl_exec($ch) === false)
{
    echo 'Curl error: ' . curl_error($ch);
}


$recaptcha_page_response = curl_exec($ch);

curl_close($ch);

//find recaptcha image file using regex
preg_match("/03AHJ_.+?(?=&amp;)/", $recaptcha_page_response,$captchastring);
$captchachallengefield = $captchastring[0];
//download to local server
$captchastring = "http://www.google.com/recaptcha/api/image?c=$captchastring[0]";
$captchastring = file_get_contents($captchastring);
file_put_contents("captcha.jpg", $captchastring);

//step1 dbc upload of captcha.jpg
$dbcpostfield = array(
"username"=>"un",
"password"=>"pw",
"captchafile"=>"@captcha.jpg",
);
$ch = curl_init();                  
curl_setopt($ch, CURLOPT_URL,"http://api.dbcapi.me/api/captcha");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 45);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $dbcpostfield);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

if(curl_exec($ch) === false)
{
    echo 'Curl error: ' . curl_error($ch);
}


$deathbycaptcha_page_response = curl_exec($ch);
$headers = curl_getinfo($ch);

curl_close($ch);
var_dump ($deathbycaptcha_page_response);
sleep (165);// time needed for ch2 to work & captcha to be solved
//step 2 curl solved captcha url
$ch2 = curl_init();                   
curl_setopt($ch2, CURLOPT_URL,$headers[url]);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_TIMEOUT, 45);
curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json')); 

if(curl_exec($ch2) === false)
{
    echo 'Curl error: ' . curl_error($ch2);
}


$deathbycaptcha_page_response_2 = curl_exec($ch2);
curl_close($ch2);
$deathbycaptcha_page_response_2 = json_decode($deathbycaptcha_page_response_2,true);
//step 3 poll for unsolved captchas

//solve recaptcha using DBC & submit to Google Recaptcha

$deathbycaptcha_page_response_text = http_build_query ($deathbycaptcha_page_response_2['text']);
print_r($deathbycaptcha_page_response_text);
$googlenoscriptpostfield = array(
"recaptcha_challenge_field"=>"$captchachallengefield",
"recaptcha_response_field"=>"$deathbycaptcha_page_response_text",
"submit"=>"I%27m+a+human"
);

$ch = curl_init();                 
curl_setopt($ch, CURLOPT_URL,"http://www.google.com/recaptcha/api/noscript?k=6LeC3O0SAAAAAC_YcSZKQChES7yidMTP_h-6uTTX");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 45);
curl_setopt ($ch, CURLOPT_REFERER, "http://www.google.com/recaptcha/api/noscript?k=6LeC3O0SAAAAAC_YcSZKQChES7yidMTP_h-6uTTX" );
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

curl_setopt($ch, CURLOPT_COOKIEFILE, 'torrentcookies.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $googlenoscriptpostfield);

if(curl_exec($ch) === false)
{
    echo 'Curl error: ' . curl_error($ch);
}


$googlenoscriptpost_page_response = curl_exec($ch);

curl_close($ch);
var_dump ($googlenoscriptpost_page_response);
var_dump ($googlenoscriptpostfield);
?>