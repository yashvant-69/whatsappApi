<?php
session_start();
$phoneNumberErr = $countryCodeErr = $callbackDataErr = $typeErr = $templateNameErr = $languageCodeErr = $headerValuesErr = $bodyValuesErr = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sendMessage = isset($_POST['sendMessage']) ? $_POST['sendMessage'] : '';
    $createUser = isset($_POST['userCreate']) ? $_POST['userCreate'] : '';
    if ($sendMessage == 'whatsappApi') {
        echo 11;
        die;
        $phoneNumber = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : "";
        $countryCode = isset($_POST['countryCode']) ? $_POST['countryCode'] : "";
        $callbackData = isset($_POST['callbackData']) ? $_POST['callbackData'] : "";
        $type = isset($_POST['type']) ? $_POST['type'] : "";
        $templateName = isset($_POST['templateName']) ? $_POST['templateName'] : "";
        $languageCode = isset($_POST['languageCode']) ? $_POST['languageCode'] : "";
        $headerValues = isset($_POST['headerValues']) ? $_POST['headerValues'] : "";
        $bodyValues = isset($_POST['bodyValues']) ? $_POST['bodyValues'] : "";

        if (empty($phoneNumber)) {
            $phoneNumberErr = "Phone number is required";
        }

        if (empty($countryCode)) {
            $countryCodeErr = "Country code is required";
        }

        if (empty($phoneNumberErr) && empty($countryCodeErr) && empty($callbackDataErr) && empty($typeErr) && empty($templateNameErr) && empty($languageCodeErr) && empty($headerValuesErr) && empty($bodyValuesErr)) {
            $url = "https://api.interakt.ai/v1/public/message/";
            $data = array(
                "countryCode" => $countryCode,
                "phoneNumber" => $phoneNumber,
                "callbackData" => $callbackData,
                "type" => $type,
                "template" => array(
                    "name" => $templateName,
                    "languageCode" => $languageCode,
                    "headerValues" => array($headerValues),
                    "bodyValues" => array($bodyValues)
                )
            );

            $headers = array(
                'Authorization: Basic aUdSZmZEZGljeHNEX1ZmYzlZUWRaZFl5RFhDUWo2eUdTc3pQMmpzNGY2czo=',
                'Content-Type: application/json'
            );
            $data_string = json_encode($data);

            // Initialize cURL handle
            $ch = curl_init();

            if ($ch) {
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $result = curl_exec($ch);

                // Check for cURL errors
                if (curl_errno($ch)) {
                    echo 'Curl error: ' . curl_error($ch);
                } else {

                    $data = json_decode($result, true);

                    // Accessing the decoded data
                    $result = $data['result'];
                    $message = $data['message'];
                    $id = $data['id'];
                    $_SESSION['success_message'] = $message;
                    header("Location: http://localhost/yashwant_github/whatsapp_api/sendWhatsappMessage.php");
                    exit();
                }

                // Close cURL handle
                curl_close($ch);
            } else {
                echo "Failed to initialize cURL handle";
            }
        }
    }

    if ($createUser == 'userCreate') {
        // echo 22;die;
        $api_url = "https://api.interakt.ai/v1/public/track/users/";

        $json_data = array(
            'userId' => isset($_POST['userId']) ? $_POST['userId'] : '',
            'phoneNumber' => $_POST['phone'],
            'countryCode' => $_POST['countryCode'],
            'traits' => array()
        );

        if (isset($_POST['traitKey']) && isset($_POST['traitValue'])) {
            foreach ($_POST['traitKey'] as $index => $key) {
                $json_data['traits'][$key] = $_POST['traitValue'][$index];
            }
        }

        $json_output = json_encode($json_data, true);

        // echo "<pre>";
        // print_r($json_output);die;


        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_output);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic aUdSZmZEZGljeHNEX1ZmYzlZUWRaZFl5RFhDUWo2eUdTc3pQMmpzNGY2czo=',
            'Content-Type: application/json'
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'Curl error: ' . curl_error($curl);
        } else {

            $data = json_decode($response, true);

            // Accessing the decoded data
            $result = $data['result'];
            $message = $data['message'];
            // $id = $data['id'];
            $_SESSION['success_message'] = $message;
            header("Location: http://localhost/yashwant_github/whatsapp_api/createUser.php");
            exit();
        }

        curl_close($curl);
        print_r($response);
        die;

        if ($response === false) {
            echo "Error occurred while sending data to the API.";
        } else {
            echo "Data successfully sent to the API.";
        }
    }
}
