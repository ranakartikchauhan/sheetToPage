<?php
require __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

// Function to get subdomain information
function getSubdomainInfo() {
    $host = $_SERVER['HTTP_HOST'];
    $domainParts = explode('.', $host);
    if (count($domainParts) == 2) {
        // For format: country.domainname.com
        $data = explode('-', $domainParts[0]);
       
        
        if(count($data) == 2){
            $cityCode = strtolower($data[0]);
            $countryCode = strtolower($data[1]);
        }else{
            $countryCode = strtolower($data[0]);
            $cityCode = null;

        }
    } else {
        $cityCode = null;
        $countryCode = null;
    }

    return [$cityCode, $countryCode];
}

// Replace with the path to your service account key file
$keyFileLocation = './hi.json';

// Create the client
$client = new Client();
$client->setApplicationName('Your Application Name');
$client->setScopes(Sheets::SPREADSHEETS);
$client->setAuthConfig($keyFileLocation);
$client->setAccessType('offline');

// Create the Sheets service
$service = new Sheets($client);

// Replace with your spreadsheet ID and range
$spreadsheetId = '1thP8LgYZwfzTMidI4ru7p7JYM47Yv9PRuZR-WQYT8kI';
$range = 'Sheet1!A1:L';

// Read data from the spreadsheet
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

// Get city and country from subdomain
list($cityCode, $countryCode) = getSubdomainInfo();

// Filter data based on city and country short codes
$filteredValues = [];
foreach ($values as $index => $row) {
    if ($index == 0) { // Headers
        $filteredValues[] = $row;
        continue;
    }

    $rowCityCode = strtolower($row[5]); // City Code column
    $rowCountryCode = strtolower($row[6]); // Country Code column

    // Filter based on city and/or country code
    if (($cityCode && strtolower($cityCode) === $rowCityCode) && ($countryCode && strtolower($countryCode) === $rowCountryCode)) {
        $filteredValues[] = $row;
    } elseif (!$cityCode && $countryCode && strtolower($countryCode) === $rowCountryCode) {
        $filteredValues[] = $row;
    }
}

// Check if filtered data is available
if (empty($filteredValues)) {
    echo "No data found for the specified city or country.\n";
} else {
    // Print the data in a Bootstrap-styled table
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Information</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">City Information</h2>';

    // Display information
    foreach ($filteredValues as $index => $row) {
        if ($index == 0) continue; // Skip headers

        echo '<div class="card mb-4">
            <img src="' . htmlspecialchars($row[11]) . '" class="card-img-top" alt="Image of ' . htmlspecialchars($row[0]) . '">
            <div class="card-body">
                <h5 class="card-title">' . htmlspecialchars($row[7]) . '</h5>
                <p class="card-text"><strong>About Us:</strong> ' . htmlspecialchars($row[1]) . '</p>
                <p class="card-text"><strong>Location:</strong> ' . htmlspecialchars($row[2]) . '</p>
                <p class="card-text"><strong>Phone Number:</strong> ' . htmlspecialchars($row[3]) . '</p>
                <p class="card-text"><strong>Country:</strong> ' . htmlspecialchars($row[4]) . '</p>
                <p class="card-text"><strong>Reviews:</strong> ' . htmlspecialchars($row[8]) . '</p>
                <p class="card-text"><strong>Contact Info:</strong> ' . htmlspecialchars($row[9]) . '</p>
                <p class="card-text"><strong>Description:</strong> ' . htmlspecialchars($row[10]) . '</p>
            </div>
        </div>';
    }

    echo '</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>';
}
?>
