<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            <h4>Create User</h4>
        </div>

        <?php
        session_start();
        if (isset($_SESSION['success_message'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
            // Success message ko ek baar display karne ke baad, usko unset kare
            unset($_SESSION['success_message']);
        }
        ?>
        <form id="traitForm" method="post" action="controller.php">
            <div class="form-group">
                <label for="exampleInputUserId">User ID (optional)</label>
                <input type="text" class="form-control" name="userId" id="exampleInputUserId" aria-describedby="emailHelp" placeholder="Enter User ID">
            </div>
            <div class="form-group">
                <label for="exampleInputPhone">Phone</label>
                <input type="tel" class="form-control" name="phone" id="exampleInputPhone" placeholder="Ex. 9876543218" required>
            </div>
            <div class="form-group">
                <label for="exampleInputCountryCode">Country Code</label>
                <input type="text" class="form-control" name="countryCode" id="exampleInputCountryCode" placeholder="example +91" value="+91" required>
            </div>
            <div id="traitsContainer">
            </div>
            <button type="button" class="btn btn-primary" onclick="addTrait()">Add Trait</button>
            <button type="submit" class="btn btn-primary" name="userCreate" value="userCreate">Submit</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
    </script>
      <script>
    setTimeout(() => {
        document.querySelector('.alert-success').remove();
    }, 10000);
</script>

</body>

</html>