<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Document</title>
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
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Phone Number</label>
                <input type="text" class="form-control" value="6263668091" name="phoneNumber" id="exampleFormControlInput1" placeholder="ex. 6263668091">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">CountryCode</label>
                <input type="text" class="form-control" value="+91" name="countryCode" id="exampleFormControlInput1" placeholder="ex. +91">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">CallbackData</label>
                <input type="text" class="form-control" value="View full Profile" name="callbackData" id="exampleFormControlInput1" placeholder="ex. View full Profile">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Type</label>
                <input type="text" class="form-control" value="Template" name="type" id="exampleFormControlInput1" placeholder="ex. Template">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Template Name</label>
                <input type="text" class="form-control" value="view_profile" name="templateName" id="exampleFormControlInput1" placeholder="ex. Template">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">LanguageCode</label>
                <input type="text" class="form-control" value="en" name="languageCode" id="exampleFormControlInput1" placeholder="ex. en">
            </div>

            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">header Values</label>
                <input type="text" class="form-control" value="Chanchal" name="headerValues" id="exampleFormControlInput1" placeholder="ex. Template">
            </div>

            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">bodyValues</label>
                <input type="text" class="form-control" value="someone has viewed your profile" name="bodyValues" id="exampleFormControlInput1" placeholder="ex. Template">
            </div>
            <div class="mb-3">

                <button type="submit" value="whatsappApi" name="sendMessage" class="btn btn-primary">Submit</button>
            </div>

        </form>
    </div>
    <script>
    setTimeout(() => {
        document.querySelector('.alert-success').remove();
    }, 10000);
</script>


</body>

</html>