<?php
session_start();
$errorMessage = '';
$headers = array(
    'Authorization: Basic aUdSZmZEZGljeHNEX1ZmYzlZUWRaZFl5RFhDUWo2eUdTc3pQMmpzNGY2czo=',
    'Content-Type: application/json'
);
$mainUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/whatsappApi/';
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
        $phones = isset($_POST['phone']) ? $_POST['phone'] : "";


        if (empty($countryCode)) {
            $countryCodeErr = "Country code is required";
        }



        // get all user
        $curl = curl_init();
        $api_url = "https://api.interakt.ai/v1/public/apis/users/";

        $data = array(
            "filters" => array(
                array(
                    "trait" => "created_at_utc",
                    "op" => "gt",
                    "val" => "2024-05-01"
                )
            )
        );
        $json_output = json_encode($data);

        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_output);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $userList = curl_exec($curl);
        if ($userList === false) {
            echo 'Curl error: ' . curl_error($curl);
        } else {
            $data = json_decode($userList, true);
        }
        $userNumber = [];
        if ($data && isset($data['data']['customers']) && !empty($data['data']['customers'])) {

            foreach ($data['data']['customers'] as $user) {
                $userNumber[] = $user['phone_number'];
            }
        }


        // if not exist user so created user 
        if ($phones) {
            foreach ($phones as $key => $phone) {
                if (empty($phone)) {
                    continue;
                }

                if (!in_array($phone, $userNumber)) {
                    $api_url = "https://api.interakt.ai/v1/public/track/users/";

                    $json_data = array(
                        'phoneNumber' => $phone,
                        'countryCode' => $_POST['countryCode'],
                    );

                    if (isset($_POST['traitValue'])) {
                        $json_data['traits'] = [];
                        $json_data['traits']['name'] = $_POST['traitValue'][$key];
                    }

                    $json_data = json_encode($json_data, true);

                    $curl = curl_init();

                    curl_setopt($curl, CURLOPT_URL, $api_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                    $createUser = curl_exec($curl);

                    if (curl_errno($curl)) {
                        echo 'Curl error: ' . curl_error($curl);
                    } else {

                        $res = json_decode($createUser, true);

                        if ($res['result'] != 1) {
                            $errorMessage =  $data['message'];
                        }
                    }

                    curl_close($curl);
                }

                $multipleUser = $_POST['user'];
                if (!empty($phone) && !in_array($phone, $multipleUser)) {
                    array_push($multipleUser, $phone);
                }
            }
        }




        if (empty($countryCodeErr) && empty($callbackDataErr) && empty($typeErr) && empty($templateNameErr) && empty($languageCodeErr) && empty($headerValuesErr) && empty($bodyValuesErr)) {
            $url = "https://api.interakt.ai/v1/public/message/";

            foreach ($multipleUser as $number) {
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
                        $result = $data['result'];
                        $message = $data['message'];
                        // Handle the response as needed
                    }

                    curl_close($ch);
                }
            }
            $_SESSION['success_message'] = isset($message) ? $message : '';
            if ($errorMessage) {
                $_SESSION['error_message'] = $errorMessage;
            }

            header("Location: " . $mainUrl . "sendWhatsappMessage.php");
            exit();
        }
    }

    if ($createUser == 'userCreate') {
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

            $result = $data['result'];
            $message = $data['message'];
            $_SESSION['success_message'] = $message;
            header("Location: " . $mainUrl . "createUser.php");
            exit();
        }

        curl_close($curl);


        if ($response === false) {
            echo "Error occurred while sending data to the API.";
        } else {
            echo "Data successfully sent to the API.";
        }
    }
}
