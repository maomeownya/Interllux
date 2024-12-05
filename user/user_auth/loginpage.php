<!doctype html>
<html lang="en">


<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Signin</title>


  <!--###### BOOTSTRAP ICON LINK #####-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">


  <style>
    input {
      box-shadow: none !important;
    }

    .img-fluid h1 {
      z-index: 2;
      font-weight: 600;
      color: rgba(0, 0, 0, 0.80) !important;
      font-family: 'Montserrat' !important;
      top: 180px !important;
    }

    .btn {
      margin-bottom: 15px;
    }

    input {
      border-color: rgba(0, 0, 0, 0.562) !important;
    }

    .form-signin {
      height: 650px;
      max-width: 400px;
      padding: 15px;
      place-self: center;
    }

    .form-signin .checkbox {
      font-weight: 400;
    }

    .form-signin .form-floating:focus-within {
      z-index: 2;
    }

    .form-signin input[type="email"] {
      margin-bottom: 10px;
      border-bottom-right-radius: 0;
      border-bottom-left-radius: 0;
    }

    .form-signin input[type="password"] {
      margin-bottom: 10px;
      border-top-left-radius: 0;
      border-top-right-radius: 0;
    }

    form {
      width: 100%;
      padding: 20px;
      background: none !important;
    }

    .form-signup {
      display: none;
    }

    input:focus+label.fill,
    input:valid+label.fill {
      color: black !important;
      background: transparent !important;
      /* Ensure background stays transparent */
    }

    #admin-login {
      background-image: url('../../assets/image/clothing-store-with-blurred-effect.jpg');
      background-size: cover;
      background-position: center;
    }

    #typing-text {
      white-space: nowrap;
      overflow: hidden;
      display: inline-block;
      font-family: 'Arial', sans-serif;
      /* Adjust font if needed */
      animation: blink 0.7s steps(1) infinite;
    }

    @keyframes blink {

      from,
      to {
        border-color: transparent;
      }

      50% {
        border-color: black;
      }
    }

    .blur-background {
      right: 0;
      background-image: url('../../assets/image/bag-hanging-from-furniture-item-indoors.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      width: 100%;
      height: 100%;
      filter: blur(2px);
      /* Adjust the blur level as needed */
    }

    .gradient-text {
      background: linear-gradient(90deg, #ae8625, #f7ef8a, #d2ac47, #edc967);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: bold;
      /* Optional for emphasis */
    }

    .overlay {
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.4);
      z-index: 1;
    }
  </style>
</head>


<body class="vh-100">
  <nav class="navbar bg-body-tertiary fixed-top shadow-sm py-0">
    <div class="container-fluid">
      <a class="navbar-brand" href="#" id="back-button">
        <button class="btn btn-sm px-1 p-0 m-0">
          <i class="bi bi-arrow-left-short text-dark fs-1 fw-bold" style="font-size: 1.5rem;"></i>
        </button>
      </a>
      <a class="navbar-brand mx-auto dm-serif-display letter-spacing-1 text-dark" href="../../user/user_auth/index.php">
        <img src="../../assets/image/logo.png" alt="Interllux Logo" width="30" height="24"> Interllux
      </a>
    </div>
  </nav>
  <div class="container-fluid position-fixed w-100">
    <div class="d-flex justify-content-center  w-100 row pe-5">
      <!-- typing -->
      <div class="img-fluid position-relative d-none d-md-block col w-100">
        <div class="h-100 w-100 position-absolute blur-background"></div>
        <div class="w-100 overlay"></div>
        <h1 class="position-absolute" id="typing-text"></h1>
      </div>
            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
      <div class="ms-5 form-signin  mt-5 pt-5">
        <!-- Sign In Form -->
        <form class="mb-5" id="signInForm" action="../user_auth/Signin.php" method="POST" style="background:none;">
          <div class="w-100 d-flex justify-content-center align-items-center"><img class="mb-4 " src="../../assets/image/logo.png" alt="" width="72" height="57"></div>
          <h1 class="h3 mb-4 fw-normal text-dark text-center">Sign in</h1>


          <div class="form-floating w-100">
            <input type="email" class="form-control rounded-pill" id="signInEmail" name="email" placeholder="Email" required>
            <label class="text-dark fill" for="signInEmail">Email</label>
          </div>
          <div class="form-floating w-100">
            <input type="password" class="form-control rounded-pill" id="signInPassword" name="password" placeholder="Password" required>
            <label class="text-dark fill" for="signInPassword">Password</label>
          </div>
          <div class="checkbox mb-3 mt-3 text-center">
            <label class="text-dark"><input type="checkbox" name="remember_me" value="remember-me"> Remember me</label>
          </div>
          <button class="btn btn-md w-100 btn-dark rounded-pill" type="submit" name="signIn">Sign in</button>
          <p class="text-center">Don't have an account? <a href="#" onclick="toggleForms()">Sign up</a></p>
        </form>


        <!-- Sign Up Form -->
        <form id="signUpForm" action="../user_auth/Signup.php" method="POST" style="display:none; background-color:rgba(255, 255, 255, 0.212); backdrop-filter: blur(15px);">
          <div class="w-100 d-flex justify-content-center align-items-center"><img class="mb-4 " src="../../assets/image/logo.png" alt="" width="72" height="57"></div>
          <h1 class="h3 mb-3 fw-normal text-dark text-center">Sign up</h1>
          <div class="form-floating w-100">
            <input type="text" class="form-control rounded-pill mb-1" id="signUpFirstName" name="first_name" placeholder="First Name" required>
            <label class="text-dark fill" for="signUpFirstName">First Name</label>
          </div>
          <div class="form-floating w-100">
            <input type="text" class="form-control rounded-pill mb-1" id="signUpLastName" name="last_name" placeholder="Last Name" required>
            <label class="text-dark fill" for="signUpLastName">Last Name</label>
          </div>
          <div class="form-floating w-100">
            <input type="email" class="form-control rounded-pill mb-1" id="signUpEmail" name="email" placeholder="Email" required>
            <label class="text-dark fill" for="signUpEmail">Email Address</label>
          </div>
          <div class="form-floating w-100">
            <input type="password" class="form-control rounded-pill" id="signUpPassword" name="password" placeholder="Password" required>
            <label class="text-dark fill" for="signUpPassword">Password</label>
          </div>
          <div class="form-floating w-100">
            <input type="password" class="form-control rounded-pill" id="signUpConfirmPassword" name="confirm_password" placeholder="Confirm Password" required>
            <label class="text-dark fill" for="signUpConfirmPassword">Confirm Password</label>
          </div>
          <button class="btn btn-md w-100 btn-dark rounded-pill" type="submit" name="signUp">Sign up</button>
          <p class="text-center">Already have an account? <a href="#" onclick="toggleForms()">Sign in</a></p>
        </form>
      </div>


      <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="errorModalLabel">Error</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalMessage"></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.getElementById('back-button').addEventListener('click', function(e) {
      e.preventDefault();
      window.location.href = '../../user/user_auth/logout.php'; // Redirect to logout
    });

    document.addEventListener("DOMContentLoaded", () => {
      const text = "Where Luxury Finds You";
      const element = document.getElementById("typing-text");
      let index = 0;

      function type() {
        if (index < text.length) {
          if (text.substring(index, index + 3) === "You") {
            element.innerHTML += `<span class="gradient-text">You</span>`;
            index += 3; // Skip "You"
          } else {
            // Add line break after "Where Luxury"
            if (text.charAt(index) === " " && index === 12) {
              element.innerHTML += "<br>";
            } else {
              element.innerHTML += text.charAt(index);
            }
            index++;
          }
          setTimeout(type, 150); // Adjust typing speed
        } else {
          setTimeout(resetTyping, 2000); // Pause before restarting
        }
      }

      function resetTyping() {
        element.innerHTML = "";
        index = 0;
        type();
      }

      type(); // Start typing
    });
  </script>
  </script>
  <script src="../../assets/js/loginpage.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>


</html>