<?php

declare(strict_types=1);


try {
    // Disable SSL verification for localhost (not recommended for production)
    $options = [
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
    ];

    // Fetch the HTML content from the URL
    $url = "http://localhost/api/debug/env";
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
