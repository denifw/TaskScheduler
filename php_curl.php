<?php
$arg = $argv[1];
if (substr($arg, 1, 1) !== ':') {
    $ch = curl_init();
    # set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $arg);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    # Grab URL and pass it to the browser
    $result = curl_exec($ch);

    # close cURL resource, and free up system resources
    curl_close($ch);
} else {
    exec("php " . $arg, $response, $return);
}
