<?php

declare(strict_types=1);


try {
    // Proxy settings
    $proxyUrl = "http://wsa-ln10.npu.np.work:3128"; // Replace with your proxy URL and port
    $proxyUser = "user.name"; // Replace with your proxy username
    $proxyPass = "PassWord"; // Replace with your proxy password

    // Create the context with proxy and basic authentication
    $auth = base64_encode("$proxyUser:$proxyPass");

    $options = [
        "http" => [
            "proxy" => $proxyUrl,
            "request_fulluri" => true,
            "header" => "Proxy-Authorization: Basic $auth",
        ],
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
    ];

    // Fetch the HTML content from the URL
    $url = "https://zoning.npu.np.work/api/debug/env";
    $htmlContent = @file_get_contents($url, false, stream_context_create($options));

    // Check if the request was successful
    if ($htmlContent === false) {
        throw new Exception("Failed to retrieve content from $url");
    }

    // Use regex to extract the REQUEST_TIME value
    preg_match('/<tr>\s*<td>REQUEST_TIME<\/td>\s*<td>(\d+)<\/td>\s*<\/tr>/', $htmlContent, $matches);

    // Check if REQUEST_TIME was found
    if (isset($matches[1])) {
        $requestTime = $matches[1];
        echo "REQUEST_TIME: $requestTime" . PHP_EOL;
    } else {
        echo "REQUEST_TIME not found." . PHP_EOL;
    }

} catch (Exception $e) {
    // Handle exceptions and print the error message
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
