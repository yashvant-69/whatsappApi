<?php
session_start();
$mainUrl = 'http://' . $_SERVER['HTTP_HOST'].'/whatsappApi/';
$phoneNumberErr = $countryCodeErr = $callbackDataErr = $typeErr = $templateNameErr = $languageCodeErr = $headerValuesErr = $bodyValuesErr = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sendMessage = isset($_POST['sendMessage']) ? $_POST['sendMessage'] : '';
    $createUser = isset($_POST['userCreate']) ? $_POST['userCreate'] : '';
    if ($sendMessage == 'whatsappApi') {
        $countryCode = isset($_POST['countryCode']) ? $_POST['countryCode'] : "";
        $callbackData = isset($_POST['callbackData']) ? $_POST['callbackData'] : "";
        $type = isset($_POST['type']) ? $_POST['type'] : "";
        $templateName = isset($_POST['templateName']) ? $_POST['templateName'] : "";
        $languageCode = isset($_POST['languageCode']) ? $_POST['languageCode'] : "";
        $headerValues = isset($_POST['headerValues']) ? $_POST['headerValues'] : "";
        $bodyValues = isset($_POST['bodyValues']) ? $_POST['bodyValues'] : "";
       // print_r(($_POST['user']));die;

        if (empty($countryCode)) {
            $countryCodeErr = "Country code is required";
        }

        if (empty($countryCodeErr) && empty($callbackDataErr) && empty($typeErr) && empty($templateNameErr) && empty($languageCodeErr) && empty($headerValuesErr) && empty($bodyValuesErr)) {
            $url = "https://api.interakt.ai/v1/public/message/";
            $headers = array(
                'Authorization: Basic aUdSZmZEZGljeHNEX1ZmYzlZUWRaZFl5RFhDUWo2eUdTc3pQMmpzNGY2czo=',
                'Content-Type: application/json'
            );
            
            // $multipleUser = ['6263668091','9770961013','9575388527','7580834383','9407907838','9755742883'];
            $multipleUser = $_POST['user'];
            foreach($multipleUser as $number){
                $phoneNumber = $number;
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
                $data_string = json_encode($data);
                $ch = curl_init();
                if ($ch) {
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
                    $result = curl_exec($ch);
    
                    if (curl_errno($ch)) {
                        echo 'Curl error: ' . curl_error($ch);
                    } else {
                        $data = json_decode($result, true);
                        // Handle the response as needed
                    }
    
                    curl_close($ch);
                }
            }
            $_SESSION['success_message'] = "Messages sent to all users";
            header("Location: " . $mainUrl . "sendWhatsappMessage.php");
            exit();
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
            header("Location: " . $mainUrl . "createUser.php");
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
