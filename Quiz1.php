<?php

/*
 * Change Log:
 * 
 * - Added constants for valid and invalid phone types.
 * - Encapsulated the fraud detection functionality in a class structure.
 * - Introduced a constructor to initialize customerId and apiKey.
 * - Moved logic to validate phone numbers into a dedicated method `isValidPhoneNumber`.
 * - Isolated the API request logic in a private method `makeApiRequest`.
 * - Implemented a `logError` method to handle error logging.
 * - Provided a usage example to demonstrate how to use the class.
 */

// Constants for valid and invalid phone types
define('VALID_PHONE_TYPES', ["FIXED_LINE", "MOBILE", "VALID"]);
define('INVALID_PHONE_TYPES', ["PREPAID", "VOIP", "INVALID", "PAYPHONE", "RESTRICTED"]);

class FraudDetectionService {
    private $customerId;
    private $apiKey;

    public function __construct(string $customerId, string $apiKey) {
        $this->customerId = $customerId;
        $this->apiKey = $apiKey;
    }

    /**
     * Function to check if a phone number is valid according to Telesign Phone ID API
     *
     * @param string $phoneNumber The phone number to validate
     * @return bool True if phone number is valid, otherwise false
     */
    public function isValidPhoneNumber(string $phoneNumber): bool {
        // Perform API request
        $response = $this->makeApiRequest($phoneNumber);

        // Check if the response is valid
        if ($response === false) {
            return false; // If API request failed or response is invalid
        }

        // Check phone type and determine validity
        $phoneType = $response['numbering']['phone_type'] ?? null;
        if ($phoneType === null) {
            return false; // Unexpected API response structure
        }

        // Normalize phone type to uppercase
        $normalizedPhoneType = strtoupper($phoneType);

        // Return true if phone type is valid
        if (in_array($normalizedPhoneType, VALID_PHONE_TYPES)) {
            return true;
        }

        // Return false if phone type is invalid
        if (in_array($normalizedPhoneType, INVALID_PHONE_TYPES)) {
            return false;
        }

        // Default return false for unknown phone types
        return false;
    }

    /**
     * Helper function to make the API request to Telesign
     *
     * @param string $phoneNumber The phone number to check
     * @return array|false The decoded response on success, false on failure
     */
    private function makeApiRequest(string $phoneNumber) {
        $apiUrl = "https://rest-ww.telesign.com/v1/phoneid/{$phoneNumber}";

        // Set headers for API request
        $headers = [
            "Authorization: Basic " . base64_encode("{$this->customerId}:{$this->apiKey}"),
            "Content-Type: application/x-www-form-urlencoded"
        ];

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // If the request fails, log the error and return false
        if ($httpCode !== 200) {
            $this->logError("API request failed with HTTP code: {$httpCode}. cURL error: {$curlError}");
            return false;
        }

        // Decode the response
        $decodedResponse = json_decode($response, true);

        // Check if JSON decoding failed
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logError("JSON decoding failed: " . json_last_error_msg());
            return false;
        }

        // Return the decoded response
        return $decodedResponse;
    }

    /**
     * Log errors for debugging purposes
     *
     * @param string $message The error message to log
     */
    private function logError(string $message) {
        // Log errors to a file, this could be enhanced by using a logging library
        error_log($message, 3, '/var/log/fraud_detection.log');
    }
}

// Usage example
$customerId = 'your_customer_id'; // Replace with actual customer ID
$apiKey = 'your_api_key'; // Replace with actual API key
$phoneNumber = '1234567890'; // Replace with actual phone number

// Instantiate the FraudDetectionService class and validate the phone number
$fraudDetectionService = new FraudDetectionService($customerId, $apiKey);
$result = $fraudDetectionService->isValidPhoneNumber($phoneNumber);

// Output the result (true or false)
var_dump($result);
?>



