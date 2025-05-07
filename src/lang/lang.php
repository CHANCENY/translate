<?php

$languages = "https://raw.githubusercontent.com/itsecurityco/to-google-translate/refs/heads/master/supported_languages.json";
$curl = curl_init($languages);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);
if (!empty($response)) {
    $response = json_decode($response, true);
    $response = array_values($response);
    $mixed = [];
    $languages_storage = __DIR__ . "/languages.php";
    if (!file_exists($languages_storage)) {
        touch($languages_storage);
    }
    $content = '<?php'.PHP_EOL.' return ' . var_export($response, true) . ";\n\n";
    if (file_put_contents($languages_storage, $content)) {
        echo PHP_EOL."updated supported languages".PHP_EOL;
    }
}

