
<?php
require_once 'sendOTP.php';
if (isset($_POST['submit'])) {
  // Sanitize and validate inputs
  $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  $last = filter_input(INPUT_POST, 'last', FILTER_SANITIZE_STRING);
  $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
  $dob = filter_input(INPUT_POST, 'dbirth', FILTER_SANITIZE_STRING);
  $nationality = filter_input(INPUT_POST, 'nationality', FILTER_SANITIZE_STRING);
  $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
  $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
  $company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);
  $caddress = filter_input(INPUT_POST, 'caddress', FILTER_SANITIZE_STRING);
  $zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
  $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
  $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
  $password = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);

  // Validate that the email is valid
  if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
      $_SESSION['ERRMSG_ARR'][] = "Invalid email format.";
  }

  // Validate date of birth
  $dobDate = DateTime::createFromFormat('Y-m-d', $dob);
  $today = new DateTime();
  $ageInterval = $today->diff($dobDate);
  $age = $ageInterval->y;

  if ($age < 18) {
      $_SESSION['ERRMSG_ARR'][] = 'You must be at least 18 years old.';
  }

  // File upload handling
  $targetDirectory = "../images/user_avatar/";  // Directory where uploaded images will be stored
  $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
  $fileName = basename($_FILES["image"]["name"]);
  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
  $maxFileSize = 2 * 1024 * 1024; // 2MB

  // Check if file is an actual image
  $check = getimagesize($_FILES["image"]["tmp_name"]);
  if ($check === false) {
      $_SESSION['ERRMSG_ARR'][] = "File is not an image.";
      $uploadOk = 0;
  }

  // Check file size
  if ($_FILES["image"]["size"] > $maxFileSize) {
      $_SESSION['ERRMSG_ARR'][] = "Sorry, your file is too large.";
      $uploadOk = 0;
  }

  // Allow only certain file formats
  if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
      $_SESSION['ERRMSG_ARR'][] = "Sorry, only JPG, JPEG, and PNG files are allowed.";
      $uploadOk = 0;
  }

  // Attempt to upload file if no errors
  if ($uploadOk === 1) {
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
          echo "The file " . htmlspecialchars($fileName) . " has been uploaded.";
      } else {
          $_SESSION['ERRMSG_ARR'][] = "Sorry, there was an error uploading your file.";
      }
  }
      // Check for whitespace in inputs
      $whitespaceFields = [];
      if (preg_match('/\s/', $name)) {
          $whitespaceFields[] = "First Name cannot contain whitespace.";
      }
      if (preg_match('/\s/', $last)) {
          $whitespaceFields[] = "Last Name cannot contain whitespace.";
      }
      if (preg_match('/\s/', $gender)) {
          $whitespaceFields[] = "Gender cannot contain whitespace.";
      }
      if (preg_match('/\s/', $dob)) {
          $whitespaceFields[] = "Date of Birth cannot contain whitespace.";
      }
      if (preg_match('/\s/', $nationality)) {
          $whitespaceFields[] = "Nationality cannot contain whitespace.";
      }
      if (preg_match('/\s/', $city)) {
          $whitespaceFields[] = "City cannot contain whitespace.";
      }
      if (preg_match('/\s/', $address)) {
          $whitespaceFields[] = "Address cannot contain whitespace.";
      }
      if (preg_match('/\s/', $company)) {
          $whitespaceFields[] = "Company cannot contain whitespace.";
      }
      if (preg_match('/\s/', $caddress)) {
          $whitespaceFields[] = "Company Address cannot contain whitespace.";
      }
      if (preg_match('/\s/', $zip)) {
          $whitespaceFields[] = "Zip Code cannot contain whitespace.";
      }
      if (preg_match('/\s/', $phone)) {
          $whitespaceFields[] = "Phone cannot contain whitespace.";
      }
      if (preg_match('/\s/', $username)) {
          $whitespaceFields[] = "Email cannot contain whitespace.";
      }
      if (preg_match('/\s/', $password)) {
          $whitespaceFields[] = "Password cannot contain whitespace.";
      }
  
      // If there are any whitespace errors, store them in the session
      if (!empty($whitespaceFields)) {
          $_SESSION['ERRMSG_ARR'] = array_merge($_SESSION['ERRMSG_ARR'], $whitespaceFields);
      }
  // Password validation
$missingRequirements = [];

if (strlen($password) < 8 || strlen($password) > 12) {
    $missingRequirements[] = "Password must be between 8 and 12 characters long.";
}
if (!preg_match('/[A-Z]/', $password)) {
    $missingRequirements[] = "Password must contain at least one uppercase letter.";
}
if (!preg_match('/[a-z]/', $password)) {
    $missingRequirements[] = "Password must contain at least one lowercase letter.";
}
if (!preg_match('/\d/', $password)) {
    $missingRequirements[] = "Password must contain at least one number.";
}
if (!preg_match('/[@$!%*?&]/', $password)) {
    $missingRequirements[] = "Password must contain at least one special character.";
}

// If there are any missing requirements, store them in the session
if (!empty($missingRequirements)) {
    $_SESSION['ERRMSG_ARR'] = array_merge($_SESSION['ERRMSG_ARR'], $missingRequirements);
}

  // Store sanitized inputs in session
  $_SESSION['image'] = $fileName;
  $_SESSION['name'] = $name;
  $_SESSION['last'] = $last;
  $_SESSION['gender'] = $gender;
  $_SESSION['dbirth'] = $dob;
  $_SESSION['nationality'] = $nationality;
  $_SESSION['city'] = $city;
  $_SESSION['address'] = $address;
  $_SESSION['company'] = $company;
  $_SESSION['caddress'] = $caddress;
  $_SESSION['zip'] = $zip;
  $_SESSION['phone'] = $phone;
  $_SESSION['username'] = $username;
  $_SESSION['pass'] = $password;
  $_SESSION['pending'] = 'pending';

  // Send OTP
  $_SESSION['otp'] = sendOTP($username, $name, $last);

  // Redirect to payment page
  redirect('index?view=payment&verify=true');
}

// Display error messages if any
if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION[' ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
  echo '<ul class="err">';
  foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
      echo '<li>' . htmlspecialchars($msg) . '</li>';
  }
  echo '</ul>';
  unset($_SESSION['ERRMSG_ARR']);
}
?>
   
         		<form class="form-horizontal" action="index?view=logininfo" method="post"  name="personal" enctype="multipart/form-data">
					 <h2>Personal Details</h2> 

           <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label class ="control-label" for="image">Avatar</label>
        <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png" onchange="validateImage(event)" required>
        <img id="imagePreview" src="#" alt="Image Preview" style="display: none; max-width: 150px; max-height: 150px;">
      </div>
      <style>
  /* Ensure the image preview is fixed at 300x300 pixels (2x2) */
  #imagePreview {
    display: none;
    width: 150px;
    height: 150px;
    object-fit: cover; /* Ensures the image fits inside the preview box */
    border: 2px solid #ddd;
    margin-top: 10px;
  }
</style>
<!-- <script>
	// document.getElementById('image').disabled = true;
function validateImage(event) {
    const fileInput = event.target;
    const filePath = fileInput.value;
    const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i; // Allowed file extensions

    // Check if the file is an image
    if (!allowedExtensions.exec(filePath)) {
        // Show SweetAlert2 error message
        Swal.fire({
            icon: 'error',
            title: 'Invalid File Type',
            text: 'Please upload an image file (JPG, JPEG, PNG).',
            confirmButtonText: 'OK'
        });
        fileInput.value = ""; // Clear the input
        document.getElementById('imagePreview').style.display = 'none'; // Hide the preview
        return false;
    } else {
        // If valid, show the image preview
        const reader = new FileReader();
        reader.onload = function (e) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.style.display = 'block';
            imagePreview.src = e.target.result;
        };
        reader.readAsDataURL(fileInput.files[0]);
    }
}
</script> -->
<script>
        function validateImage(event) {
            const fileInput = event.target;
            const filePath = fileInput.value;
            const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i; // Allowed file extensions

            // Check for double extensions
            const fileName = fileInput.files[0].name;
            const lastDotIndex = fileName.lastIndexOf('.');
            const firstDotIndex = fileName.indexOf('.');

            // Check if there are multiple dots and if the last extension is valid
            if (fileName.split('.').length > 2 || !allowedExtensions.exec(filePath)) {
                // Show SweetAlert2 error message
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please upload a valid image file (JPG, JPEG, PNG).',
                    confirmButtonText: 'OK'
                });
                fileInput.value = ""; // Clear the input
                document.getElementById('imagePreview').style.display = 'none'; // Hide the preview
                return false;
            } else {
                // If valid, show the image preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imagePreview = document.getElementById('imagePreview');
                    imagePreview.style.display = 'block';
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    </script>
<!-- <script>
  function previewImage(event) {
    const input = event.target;
    const imagePreview = document.getElementById('imagePreview');

    if (input.files && input.files[0]) {
      const reader = new FileReader();

      reader.onload = function (e) {
        imagePreview.style.display = 'block';
        imagePreview.src = e.target.result;
      };

      reader.readAsDataURL(input.files[0]);
    } else {
      imagePreview.style.display = 'none';
      imagePreview.src = '#';
    }
  }
</script> -->
									
			            </div>
			          </div> 
<br>
									
					   <!-- Form Fields in Two Columns -->
  <div class="row">
    <!-- First Column -->
    <div class="col-md-6 col-sm-12">
      <div class="form-group">
        <label class ="control-label" for="name">First Name:</label>
        <input name="name" type="text" class="form-control input-sm" id="name" pattern="^(?!\s*$)[A-Za-z\s.,]+$" maxlength="16" onkeyup="capitalizeInput(this)" required>
      </div>

      <div class="form-group">
        <label class ="control-label" for="last">Last Name:</label>
        <input name="last" type="text" class="form-control input-sm" id="last" pattern="^(?!\s*$)[A-Za-z\s.,]+$" maxlength="16" onkeyup="capitalizeInput(this)" required>
      </div>

      <div class="form-group">
        <label class ="control-label" for="gender">Gender:</label>
        <select name="gender" class="form-control input-sm" id="gender" required>
          <option value="" disabled selected>Select Gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>

      <div class="form-group">
    <label class ="control-label" for="dbirth">Date of Birth:</label>
    <input type="date" name="dbirth" class="form-control input-sm" 
           max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>" 
           onchange="validateDOB(this)" required>
    <span id="dob-error" style="color: red;"></span>
</div>

      <div class="form-group">
        <label class ="control-label" for="phone">Phone:</label>
        <input name="phone" type="tel" class="form-control input-sm" pattern="09\d{9}" id="phone" value="09" required oninput="this.value = this.value.replace(/\D/, ''); if(this.value.length > 11) this.value = this.value.slice(0, 11);">
      </div>
    <!-- </div> -->

    <!-- Second Column -->
    
      <div class="form-group">
        <label class ="control-label" for="city">City:</label>
        <input name="city" type="text" class="form-control input-sm" id="city" pattern="^(?!\s*$)[A-Za-z\s.,]+$" onkeyup="capitalizeInput(this)">
      </div>

      
      <div class="form-group">
        <label class ="control-label" for="address">Address:</label>
        <input name="address" type="text" class="form-control input-sm" id="address" pattern="^(?!\s*$)[A-Za-z\s.,]+$" maxlength="50" onkeyup="capitalizeInput(this)">
      </div>
	  </div>
	  <!-- Second Column -->
	  <div class="col-md-6 col-sm-12">
      
      <div class="form-group">
        <label class ="control-label" for="zip">Zip Code:</label>
        <input name="zip" type="text" class="form-control input-sm" id="zip"  maxlength="4" required oninput="this.value = this.value.replace(/\D/, ''); if(this.value.length > 4) this.value = this.value.slice(0, 4);">
      </div>
	  
      <div class="form-group">
        <label class ="control-label" for="nationality">Nationality:</label>
        <input name="nationality" type="text" class="form-control input-sm" id="nationality" pattern="^(?!\s*$)[A-Za-z\s.,]+$"  maxlength="17" onkeyup="capitalizeInput(this)">
      </div>

      <div class="form-group">
        <label class ="control-label" for="company">Company:</label>
        <input name="company" type="text" class="form-control input-sm" id="company"  pattern="^(?!\s*$)[A-Za-z\s.,]+$" required onkeyup="capitalizeInput(this)">
      </div>

      <div class="form-group">
        <label class ="control-label" for="caddress">Company Address:</label>
        <input name="caddress" type="text" class="form-control input-sm" id="caddress" pattern="^(?!\s*$)[A-Za-z\s.,]+$" required onkeyup="capitalizeInput(this)">
      </div>

      <div class="form-group">
        <label  class ="control-label" for="username" >Email:</label>
        <input name="username" type="email" class="form-control input-sm" id="username"  required  placeholder="User@gmail.com">
        <span id="email-status"></span>
      </div>
      <div class="form-group">
    <label class="control-label" for="password">Password:</label>
    <input 
        name="pass" 
        type="password" 
        class="form-control input-sm" 
        id="password" 
        
        oninput="clearErrorMessage()" 
        oninvalid="setCustomErrorMessage(event)" 
        minlength="8" 
        maxlength="12" 
        pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$" title="Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character."
        required  
        placeholder="Ex@mple123">
        <span id="password-status"></span>
    <span id="password-error" style="color: red;"></span>
</div>
      <!-- <div class="form-group">
    <label  class ="control-label" for="password">Password:</label>
    <input name="pass" type="password" class="form-control input-sm" id="password" oninput="validatePassword()" minlength="8" maxlength="12" required  placeholder="Ex@mple123">
					            <span id="password-error" style="color: red;"></span> 
</div> -->
			            </div>
			          </div>
 
					 &nbsp; &nbsp;
				 <div class="form-group">
			        <div class="col-md-6">
					<p>
				I <input type="checkbox" name="condition" value="checkbox" />
					 <small>Agree the <a class="toggle-modal"  onclick="OpenPopupCenter('terms_condition.php','Terms And Codition','600','600')" ><b>TERMS AND CONDITION</b></a> of this Hotel</small>
			
					 <br />
						<!-- <img src="captcha_code_file.php?rand=<?php echo rand(); ?>" id='captchaimg' ><a href='javascript: refreshCaptcha();'><img src="<?php echo WEB_ROOT;?>images/refresh.png" alt="refresh" border="0" style="margin-top:5px; margin-left:5px;" /></a>
						<br /><small>If you are a Human Enter the code above here :</small><input id="6_letters_code" name="6_letters_code" type="text" class="form-control input-sm" width="20"></p><br/>
					 -->	<div class="col-md-4">
					    	<input name="submit" type="submit" value="Confirm"  class="btn btn-primary" onclick="return personalInfo();"/>
					    </div>
					</div>
					NOTE: 
					We recommend that your password should be at least 8 characters long and should be different from your username.
					Your e-mail address must be valid. We use e-mail for communication purposes (order notifications, etc). Therefore, it is essential to provide a valid e-mail address to be able to use our services correctly.
					All your private data is confidential. We will never sell, exchange or market it in any way. For further information on the responsibilities of both parties, you may refer to us.
			    </div>

			</form>   


<script type="text/javascript">
function capitalizeInput(input) {
    var inputValue = input.value;
    input.value = inputValue.charAt(0).toUpperCase() + inputValue.slice(1);
}
</script>
<script>
function validateDOB(input) {
    const selectedDate = new Date(input.value);
    const todayMinus18 = new Date();
    todayMinus18.setFullYear(todayMinus18.getFullYear() - 18);

    const dobError = document.getElementById('dob-error');
    if (selectedDate > todayMinus18) {
        dobError.textContent = "You must be at least 18 years old.";
        input.setCustomValidity("You must be at least 18 years old.");
    } else {
        dobError.textContent = "";
        input.setCustomValidity("");
    }
}
</script>

<script>
$(document).ready(function() {
    $('#username').on('input', function() {
        var email = $(this).val();
        if (email.length > 0) {
            $.ajax({
                type: 'POST',
                url: 'check_email.php',
                data: { email: email },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'available') {
                        $('#email-status').text('Email is available').css('color', 'green');
                    } else {
                        $('#email-status').text('Email is already in use').css('color', 'red');
                    }
                }
            });
        } else {
            $('#email-status').text(''); // Clear the message if the input is empty
        }
    });
});
// Password criteria check
$('#pass').on('input', function() {
        var password = $(this).val();
        var messages = [];

        // Check password length
        if (password.length < 8 || password.length > 12) {
            messages.push("Password must be between 8 and 12 characters long.");
        }
        // Check for uppercase letter
        if (!/[A-Z]/.test(password)) {
            messages.push("Password must contain at least one uppercase letter.");
        }
        // Check for lowercase letter
        if (!/[a-z]/.test(password)) {
            messages.push("Password must contain at least one lowercase letter.");
        }
        // Check for number
        if (!/\d/.test(password)) {
            messages.push("Password must contain at least one number.");
        }
        // Check for special character
        if (!/[@$!%*?&]/.test(password)) {
            messages.push("Password must contain at least one special character.");
        }

        // Display messages
        if (messages.length > 0) {
            $('#password-status').html(messages.join('<br>')).css('color', 'red');
        } else {
            $('#password-status').text('Password meets all criteria').css('color', 'green');
        }
    });

</script>

<!-- <script>
function validatePassword() {
    var passwordInput = document.getElementById("password");
    var password = passwordInput.value;
    var passwordError = document.getElementById("password-error");
    
    // Reset custom validity message and error message
    passwordInput.setCustomValidity("");
    passwordError.textContent = "";

    // Validation checks
    if (password.length < 8) {
        passwordError.textContent = "Password must be at least 8 characters long.";
        passwordInput.setCustomValidity("Password must be at least 8 characters long.");
    } 
    else if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/.test(password)) {
        passwordError.textContent = "Password must contain at least one special character.";
        passwordInput.setCustomValidity("Password must contain at least one special character.");
    } 
    else if (!/\d/.test(password)) {
        passwordError.textContent = "Password must contain at least one number.";
        passwordInput.setCustomValidity("Password must contain at least one number.");
    } 
    else if (!/[A-Z]/.test(password)) {
        passwordError.textContent = "Password must contain at least one capital letter.";
        passwordInput.setCustomValidity("Password must contain at least one capital letter.");
    } 
    else {
        // All checks passed
        passwordInput.setCustomValidity(""); // Clear any custom validity
    }
    
    // Trigger native validation after setting custom validity
    passwordInput.reportValidity();
}
</script> -->
<script>
function validatePassword() {
    var passwordInput = document.getElementById("password");
    var password = passwordInput.value;
    var passwordError = document.getElementById("password-error");

    // Reset custom validity message and error message
    passwordInput.setCustomValidity("");
    passwordError.textContent = "";

    // Validation checks
    if (password.length < 8) {
        passwordError.textContent = "Password must be at least 8 characters long.";
        passwordInput.setCustomValidity("Password must be at least 8 characters long.");
    } 
    else if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/.test(password)) {
        passwordError.textContent = "Password must contain at least one special character.";
        passwordInput.setCustomValidity("Password must contain at least one special character.");
    } 
    else if (!/\d/.test(password)) {
        passwordError.textContent = "Password must contain at least one number.";
        passwordInput.setCustomValidity("Password must contain at least one number.");
    } 
    else if (!/[A-Z]/.test(password)) {
        passwordError.textContent = "Password must contain at least one capital letter.";
        passwordInput.setCustomValidity("Password must contain at least one capital letter.");
    } 

    // Trigger native validation after setting custom validity
    passwordInput.reportValidity();
}

function clearErrorMessage() {
    var passwordError = document.getElementById("password-error");
    passwordError.textContent = ""; // Clear error message
}

function setCustomErrorMessage(event) {
    var passwordInput = event.target;
    var passwordError = document.getElementById("password-error");

    // Display custom message for the required field
    if (passwordInput.validity.valueMissing) {
        passwordError.textContent = "Password is required.";
        passwordInput.setCustomValidity("Password is required.");
    } else {
        passwordInput.setCustomValidity(""); // Reset custom validity
    }
}
</script>
<script>
document.querySelector('form').addEventListener('submit', function(event) {
        // Check each required input field for empty or space-only values
        const requiredFields = document.querySelectorAll('input[required], select[required]');
        let isValid = true;
    
        requiredFields.forEach(function(field) {
            const value = field.value.trim(); // Remove leading/trailing spaces
            if (value === '') {
                // Show a custom alert or display the error message
                alert(Please fill out the required field: ${field.placeholder || field.name});
                isValid = false;
                field.focus(); // Focus on the first empty required field
            }
        });
    
        if (!isValid) {
            event.preventDefault(); // Prevent form submission if there are invalid fields
        }
    });
</script>
<script>
    document.getElementById('username').addEventListener('input', function() {
        const emailInput = this.value;
        const gmailDomain = 'gmail.com';

        // Find the index of '@' in the email input
        const atIndex = emailInput.indexOf('@');

        // If '@' is found and the input is longer than '@'
        if (atIndex !== -1 && emailInput.length > atIndex + 1) {
            // Extract the domain part of the email
            const domainPart = emailInput.slice(atIndex + 1);

            // Check if the domain part starts with 'gmail.com' or if it matches letter by letter
            if (!gmailDomain.startsWith(domainPart)) {
                // Show SweetAlert2 warning if it does not match
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Email',
                    text: 'Please enter a valid Gmail address that ends with @gmail.com.',
                    showConfirmButton: true
                });
            }
        }
    });
</script>
<!-- <script>
function validatePassword() {
    const password = document.getElementById('password').value;

    // Regex patterns for validation
    const lengthPattern = /.{8,12}/;  // 8-12 characters
    const capitalPattern = /[A-Z]/;   // At least one uppercase letter
    const numberPattern = /\d/;       // At least one number
    const specialPattern = /[@$!%*?&]/; // At least one special character

    // Select the error messages
    const lengthError = document.getElementById('length-error');
    const capitalError = document.getElementById('capital-error');
    const numberError = document.getElementById('number-error');
    const specialError = document.getElementById('special-error');

    // Validate each rule and hide or show the corresponding error message
    lengthError.style.display = lengthPattern.test(password) ? 'none' : 'list-item';
    capitalError.style.display = capitalPattern.test(password) ? 'none' : 'list-item';
    numberError.style.display = numberPattern.test(password) ? 'none' : 'list-item';
    specialError.style.display = specialPattern.test(password) ? 'none' : 'list-item';

    // Check if all requirements are met
    const allValid = lengthPattern.test(password) &&
                     capitalPattern.test(password) &&
                     numberPattern.test(password) &&
                     specialPattern.test(password);

    // Set form validation state based on all requirements being met or not
    document.getElementById('password').setCustomValidity(allValid ? '' : 'Invalid password');
}
</script> -->
<!-- 
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.3.4/purify.min.js"></script> -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Define allowed characters for each field
    const allowedChars = {
        name: /^[a-zA-Z\s]*$/, // Only letters and spaces
        last: /^[a-zA-Z\s]*$/, // Only letters and spaces
       // phone: /^[0-9]{11}$/, // Only digits (10 digits for a phone number)
        city: /^[a-zA-Z\s]*$/, // Only letters and spaces
        address: /^[a-zA-Z0-9\s,.-]*$/, // Alphanumeric, spaces, commas, periods, hyphens
        //zip: /^[0-9]{5}$/, // Only digits (5 digits for a zip code)
        nationality: /^[a-zA-Z\s]*$/, // Only letters and spaces
        company: /^[a-zA-Z0-9\s]*$/, // Alphanumeric and spaces
        caddress: /^[a-zA-Z0-9\s,.-]*$/, // Alphanumeric, spaces, commas, periods, hyphens
        email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/ // Basic email format
    };

    function validateInput(inputField, fieldName, pattern) {
        inputField.addEventListener('input', function() {
            if (!pattern.test(this.value)) {
                Swal.fire("Invalid Input", `Please enter a valid ${fieldName}.`, "error");
                this.value = ""; // Clear the input if invalid
            }
        });
    }

    // Get input fields
    const firstInput = document.getElementById('name');
    const lastInput = document.getElementById('last');
    // const phoneInput = document.getElementById('phone');
    const cityInput = document.getElementById('city');
    const addressInput = document.getElementById('address');
    const zipInput = document.getElementById('zip');
    const nationalityInput = document.getElementById('nationality');
    const companyInput = document.getElementById('company');
    const caddressInput = document.getElementById('caddress');
    const emailInput = document.getElementById('username');

    // Validate inputs
    validateInput(firstInput, 'First Name', allowedChars.name);
    validateInput(lastInput, 'Last Name', allowedChars.last);
    validateInput(phoneInput, 'Phone', allowedChars.phone);
    validateInput(cityInput, 'City', allowedChars.city);
    validateInput(addressInput, 'Address', allowedChars.address);
    validateInput(zipInput, 'Zip Code', allowedChars.zip);
    validateInput(nationalityInput, 'Nationality', allowedChars.nationality);
    validateInput(companyInput, 'Company', allowedChars.company);
    validateInput(caddressInput, 'Company Address', allowedChars.caddress);
    validateInput(emailInput, 'Email', allowedChars.email);
});
</script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.3.4/purify.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->




                 
			
 
