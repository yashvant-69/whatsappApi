<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- <title>Document</title> -->
    <style>
        .userForm {
            border: 2px solid grey;
            box-shadow: 5px 10px #888888;
            padding: 30px;
        }
    </style>
</head>

<body>

    <div class="container mt-5 userForm">
        <div class="d-flex text-align-center mt-5">
            <h4>Send Whatsapp Notification</h4>
            <a href="createUser.php" class="btn btn-primary" style="margin-left:auto;">Add User
            </a>
        </div>
        <?php
        session_start();
        if (isset($_SESSION['success_message'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
            // Success message ko ek baar display karne ke baad, usko unset kare
            unset($_SESSION['success_message']);
        }
        ?>

        <form method="post" action="controller.php">
            <label for="form-check-label">Select User</label>
            <?php
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
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Authorization: Basic aUdSZmZEZGljeHNEX1ZmYzlZUWRaZFl5RFhDUWo2eUdTc3pQMmpzNGY2czo=',
                'Content-Type: application/json'
            ));

            $userList = curl_exec($curl);
            if ($userList === false) {
                echo 'Curl error: ' . curl_error($curl);
            } else {
                $data = json_decode($userList, true);
            }
            curl_close($curl);

            if ($data && isset($data['data']['customers']) && !empty($data['data']['customers'])) {

                foreach ($data['data']['customers'] as $user) { ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="<?php echo $user['phone_number'] ?>" id="user" name="user[]">
                        <label class="form-check-label" for="flexCheckDefault">
                            <?php echo $user['traits']['name'] ?>
                        </label>
                    </div>
            <?php   }
            }
            ?>

            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">CountryCode</label>
                <input type="text" class="form-control" required value="+91" name="countryCode" id="exampleFormControlInput1" placeholder="ex. +91">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">CallbackData</label>
                <input type="text" class="form-control" required name="callbackData" id="exampleFormControlInput1" placeholder="ex. View full Profile">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Type</label>
                <input type="text" class="form-control" value="Template" required name="type" id="exampleFormControlInput1" placeholder="ex. Template">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Template Name</label>
                <input type="text" class="form-control" required name="templateName" id="exampleFormControlInput1" placeholder="ex. Template Name">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">LanguageCode</label>
                <input type="text" class="form-control" required value="en" name="languageCode" id="exampleFormControlInput1" placeholder="ex. en">
            </div>

            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">header Values</label>
                <input type="text" class="form-control" required name="headerValues" id="exampleFormControlInput1" placeholder="ex. Hi Yash">
            </div>

            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">bodyValues</label>
                <input type="text" class="form-control" required name="bodyValues" id="exampleFormControlInput1" placeholder="ex. Nice to meet you">
            </div>
            <div class="mb-3">

                <button value="whatsappApi" id="submit" name="sendMessage" class="btn btn-primary">Submit</button>
            </div>

        </form>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.alert-success').remove();
        }, 10000);

        $(document).on('click', '#submit', function() {
            let checked = $('input[type="checkbox"]:checked').length;
            if (checked < 1) {
                alert('Please select at least one user.');
                return false;
            } else {
                $('#submit').submit();
            }
        });
    </script>


</body>

</html>