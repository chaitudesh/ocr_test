<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload with AJAX</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-file-input:lang(en)~.custom-file-label::after {
            content: "Browse";
        }

        .progress {
            height: 20px;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-5" id="upform">
        <h2 class="text-center">File Upload</h2>
        <?php
        session_start();
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            session_destroy();
        }
        ?>
        <form id="uploadForm" method="post" enctype="multipart/form-data" class="mt-4">
            <div class="mb-3">
                <label for="idfile" class="form-label">Choose a file:</label>
                <div class="input-group">
                    <input type="file" name="idfile" id="idfile" class="form-control" required>
                    <button class="btn btn-outline-secondary" type="button" id="clearFile">Clear</button>
                </div>
                <div class="form-text">Please upload a valid file. Max size: 2MB.</div>
            </div>
            <button type="submit" class="btn btn-primary">Upload <i class="fas fa-upload"></i></button>
        </form>

        <div class="progress mt-3" id="uploadProgress" style="display: none;">
            <div class="progress-bar" role="progressbar" style="width: 0%;" id="progressBar" aria-valuenow="0"
                aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>

        <div id="feedback" class="mt-3"></div>
    </div>

    <!-- <div id="response"></div> -->
    <div style="justify-content : center">
        <div id="loader" style="display: none;">
            <div class="spinner-border" role="status">
                <span class="sr-only"></span>
            </div>
        </div>
    </div>
    <div class="container mt-5 d-none" id="main">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">User Information Form</h4>
            </div>
            <div class="card-body">
                <form id="userform" action="./form_submit.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name:</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name"
                            pattern="[A-Za-z\s]+" required>
                        <div class="invalid-feedback">Please enter your name.</div>
                    </div>

                    <div class="mb-3">
                        <label for="idType" class="form-label">Type of ID:</label>
                        <select id="idType" name="idType" class="form-select" required>
                            <option value="" disabled selected>Select ID type</option>
                            <option value="passport">Passport</option>
                            <option value="Aadhaar Card">Aadhaar</option>
                            <option value="PAN Card">PAN</option>
                            <option value="voterId">Voter ID</option>
                            <option value="driverLicense">Driver's License</option>
                        </select>
                        <div class="invalid-feedback">Please select your ID type.</div>
                    </div>

                    <div class="mb-3">
                        <label for="idNumber" class="form-label">ID Number:</label>
                        <input type="text" id="idNumber" name="idNumber" class="form-control"
                            placeholder="Enter your ID number" required>
                        <div class="invalid-feedback">Please enter your ID number.</div>
                    </div>

                    <div class="mb-3">
                        <label for="dob" class="form-label">Date of Birth:</label>
                        <input type="date" id="dob" name="dob" class="form-control" required>
                        <div class="invalid-feedback">Please select your date of birth.</div>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            (() => {
                'use strict';
                const forms = document.querySelectorAll('.needs-validation');
                Array.from(forms).forEach(form => {
                    form.addEventListener('submit', event => {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            })();
            $(document).ready(function () {
                $('#userform').hide();
                $('#uploadForm').on('submit', function (e) {
                    e.preventDefault(); // Prevent the default form submission

                    var formData = new FormData(this); // Create a FormData object


                    // console.log($("#idType"));

                    $.ajax({
                        url: './ocr_send.php', // URL to the PHP file that will handle the upload
                        type: 'POST',
                        data: formData,
                        contentType: false, // Prevent jQuery from overriding the Content-Type
                        processData: false, // Prevent jQuery from processing the data
                        beforeSend: function () {
                            // Show the loader before sending the request
                            $('#loader').show();
                        },
                        success: function (response) {
                            $('#uploadForm').hide();
                            console.log(response.idNumber);

                            $('#name').val(response.name);
                            $('#idNumber').val(response.idNumber);
                            const [month, day, year] = response.dob.split("/");
                            if (year) {
                                const formattedDate = `${year}-${month.padStart(2, "0")}-${day.padStart(2, "0")}`;
                                $('#dob').val(formattedDate);
                            }
                            $('#idType option').each(function () {
                                if ($(this).val().toLowerCase() == response.type.toLowerCase()) {
                                    $(this).prop('selected', true);
                                }

                            });
                            $('#main').removeClass('d-none');
                            $('#upform').addClass('d-none');
                            $('#userform').show();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            // Handle errors
                            $('#response').html('Error: ' + textStatus);
                        },
                        complete: function () {
                            // Hide the loader after the request is complete
                            $('#loader').hide();
                        }
                    });
                });
            });
        </script>

</body>

</html>