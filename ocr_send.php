<?php

header('Content-Type: application/json');
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function extractTextFromImage($filePath)
{

    $apiKey = 'K84757022588957'; // Replace with your OCR.Space API key
    $apiUrl = 'https://api.ocr.space/parse/image';

    // Prepare the POST request
    $data = [
        'apikey' => $apiKey,
        'file' => new CURLFile($filePath),
        'language' => 'eng'
    ];

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $result = curl_exec($ch);

    curl_close($ch);

    // Check for errors in the result
    if ($result === FALSE) {

        echo ('Error occurred while fetching data');
    }

    // Decode the JSON response
    return json_decode($result, true);
}

function identifyDocumentType($extractedText)
{
    // Initialize an associative array to hold the document data
    $documentData = [];

    $documentData['dob'] = $dobMatches[1] ?? '';

    // Check for keywords to identify the document type
    if ((stripos($extractedText, 'aadhaar') !== false) || (stristr($extractedText, 'VID') !== false)) {
        $documentData['type'] = 'Aadhaar Card';

        // Extract relevant fields
        preg_match('/\d{4}\s\d{4}\s\d{4}/', $extractedText, $aadhaarMatches); // Aadhaar number
        $documentData['idNumber'] = $aadhaarMatches[0] ?? '';

        preg_match('/NAME:\s*([A-Za-z\'\-]+(?:\s[A-Za-z\'\-]+)*)/', $extractedText, $nameMatches);

        if ($nameMatches) {
            $documentData['name'] = trim($nameMatches[1] ?? '');
        } else {
            preg_match('/([A-Z][a-z]+(?:\s[A-Z][a-z]+)*)/', $extractedText, $nameMatches);
            $documentData['name'] = trim($nameMatches[1] ?? '');
        }

        preg_match('/DOB:\s*(\d{2}\/\d{2}\/\d{4})/i', $extractedText, $dobMatches);
        if ($dobMatches) {
            $documentData['dob'] = $dobMatches[1] ?? '';
        } else {
            preg_match('/(\d{2}\/\d{2}\/\d{4})/', $extractedText, $dobMatches);
            $documentData['dob'] = $dobMatches[1] ?? '';
        }

        if ((strpos($extractedText, 'female') !== false) || (strpos($extractedText, 'FEMALE') !== false)) {
            $documentData['gender'] = 'FEMALE' ?? '';
        } elseif ((strpos($extractedText, 'male') !== false) || (strpos($extractedText, 'MALE') !== false)) {
            $documentData['gender'] = 'MALE' ?? '';
        }

    } elseif (stristr($extractedText, 'TAX') !== false || stristr($extractedText, 'INCOME') !== false) {
        $documentData['type'] = 'PAN Card';

        preg_match('/[A-Z]{5}\d{4}[A-Z]/', $extractedText, $panMatches); // PAN number
        $documentData['idNumber'] = $panMatches[0] ?? '';

        preg_match('/NAME:\s*([A-Z][a-z]+(?:\s[A-Z][a-z]+)*)/', $extractedText, $nameMatches);// Name
        if ($nameMatches) {
            $documentData['name'] = trim($nameMatches[1] ?? '');
        } else {
            preg_match('/NAME:\s*([A-Z][a-z]+(?:\s[A-Z][a-z]+)*)/', $extractedText, $nameMatches);

            $documentData['name'] = trim($nameMatches[1] ?? '');
        }
        preg_match('/DOB:\s*(\d{2}\/\d{2}\/\d{4})/i', $extractedText, $dobMatches);
        if ($dobMatches) {
            $documentData['dob'] = $dobMatches[1] ?? '';
        } else {
            preg_match('/(\d{2}\/\d{2}\/\d{4})/', $extractedText, $dobMatches);
            $documentData['dob'] = $dobMatches[1] ?? '';

        }
        if ((strpos($extractedText, 'female') !== false) || (strpos($extractedText, 'FEMALE') !== false)) {
            $documentData['gender'] = 'FEMALE' ?? '';
        } elseif ((strpos($extractedText, 'male') !== false) || (strpos($extractedText, 'MALE') !== false)) {
            $documentData['gender'] = 'MALE' ?? '';
        }
        // echo json_encode($documentData);
    } elseif (stripos($extractedText, 'REPUBLIC OF') !== false) {
        $documentData['type'] = 'Passport';

        preg_match('/[A-Z]{1}\d{7}/', $extractedText, $passportMatches); // Passport number
        $documentData['idNumber'] = $passportMatches[0] ?? '';

        preg_match('/Name\s*:\s*(.*)/i', $extractedText, $nameMatches); // Name
        $documentData['name'] = trim($nameMatches[1] ?? '');

        // echo json_encode($documentData);
        if ((strpos($extractedText, 'female') !== false) || (strpos($extractedText, 'FEMALE') !== false)) {
            $documentData['gender'] = 'FEMALE' ?? '';
        } elseif ((strpos($extractedText, 'male') !== false) || (strpos($extractedText, 'MALE') !== false)) {
            $documentData['gender'] = 'MALE' ?? '';
        }
    } elseif (stripos($extractedText, 'voter id') !== false) {
        $documentData['type'] = 'Voter ID';

        preg_match('/\d{10}/', $extractedText, $voterIdMatches); // Voter ID number
        $documentData['idNumber'] = $voterIdMatches[0] ?? '';

        preg_match('/Name\s*:\s*(.*)/i', $extractedText, $nameMatches); // Name
        $documentData['name'] = trim($nameMatches[1] ?? '');

        if ((strpos($extractedText, 'female') !== false) || (strpos($extractedText, 'FEMALE') !== false)) {
            $documentData['gender'] = 'FEMALE' ?? '';
        } elseif ((strpos($extractedText, 'male') !== false) || (strpos($extractedText, 'MALE') !== false)) {
            $documentData['dob'] = 'MALE' ?? '';
        }
    } else {
        $documentData['type'] = 'Unknown Document Type';
    }

    return $documentData;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if a file was uploaded
    if (isset($_FILES['idfile']) && $_FILES['idfile']['error'] === UPLOAD_ERR_OK) {

        // File was uploaded successfully
        $fileTmpPath = $_FILES['idfile']['tmp_name'];

        // Move the uploaded file to the specified directory
        $uploadFile = $uploadDir . basename($_FILES['idfile']['name']);

        if (move_uploaded_file($fileTmpPath, $uploadFile)) {

            // echo "File is valid, and was successfully uploaded.\n";

            // Extract text from the uploaded image
            $response = extractTextFromImage($uploadFile);

            // Display the extracted data
            if (isset($response['ParsedResults'][0]['ParsedText'])) {
                $extractedText = $response['ParsedResults'][0]['ParsedText'];

                // echo "Extracted Data:\n";
                // echo nl2br($extractedText);

                // Identify the document type and get the data
                $documentData = identifyDocumentType($extractedText);

                // echo "\nIdentified Document Type: " . $documentData['type'] . "\n";
                echo json_encode($documentData);
                // Display the extracted fields
                foreach ($documentData as $key => $value) {
                    if ($key !== 'type') { // Skip the type field
                        // echo ucfirst(str_replace('_', ' ', $key)) . ": " . $value . "\n";
                    }
                }
            } else {
                echo "No data extracted.";
            }
        } else {
            echo "File upload failed.";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
}
?>