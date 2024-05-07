<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
            unset($_SESSION['error_message']);
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

                foreach ($data['data']['customers'] as $user) {
            ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="<?php echo $user['phone_number'] ?>" id="user" name="user[]">
                        <label class="form-check-label" for="flexCheckDefault">
                            <?php echo isset($user['traits']['name']) ? $user['traits']['name'] : $user['phone_number']; ?>

                        </label>
                    </div>
            <?php   }
            }
            ?>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-10">
                        <label for="exampleInputPhone">Phone</label>
                        <input type="tel" class="form-control" name="phone[]" id="phone" placeholder="Ex. 9876543218">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-primary my-2" style="float: right;" onclick="addNumber()">Add Number</button>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-10">
                        <label for="traitValue">Name</label>
                        <input type="text" class="form-control" name="traitValue[]" placeholder="Enter Your Name">
                    </div>
                </div>
            </div>
            <div id="NumberContainer">
            </div>
            <div id="traitsContainer">
            </div>

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
        function addTrait() {
            var traitsContainer = document.getElementById('traitsContainer');
            var traitHtml = `
            <div class="row">
                <div class="form-group col-6">
                    <label for="traitKey">Trait Key</label>
                    <input type="text" class="form-control" name="traitKey[]" placeholder="Enter Trait Key">
                </div>
                <div class="form-group col-6">
                    <label for="traitValue">Trait Value</label>
                    <input type="text" class="form-control" name="traitValue[]" placeholder="Enter Trait Value">
                </div>
            </div>
        `;
            traitsContainer.insertAdjacentHTML('beforeend', traitHtml);
        }

        function addNumber() {
            let row_number = Math.floor(Math.random() * 98765);
            var NumberContainer = document.getElementById('NumberContainer');
            var NumberHtml = `<div class="row_${row_number}">
                  <div class="row">
                <div class="col-sm-10">
                    <label for="exampleInputPhone_${row_number}">Phone</label>
                    <input type="tel"  class="form-control" name="phone[]" id="phone" placeholder="Ex. 9876543218">
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger my-2" style="float: right;" onclick="removeNumber(${row_number})">Remove</button>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-10">
                    <label for="traitValue">Name Value</label>
                    <input type="text" class="form-control" name="traitValue[]" placeholder="Enter Your Name">
                </div>
            </div>
            </div>
      `;
            NumberContainer.insertAdjacentHTML('beforeend', NumberHtml);
        }

        function removeNumber(row_number) {
            var row = document.querySelector(`.row_${row_number}`);
            row.remove();
        }

        setTimeout(() => {
            document.querySelector('.alert-success').remove();
        }, 10000);

        setTimeout(() => {
            document.querySelector('.alert-danger').remove();
        }, 20000);

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