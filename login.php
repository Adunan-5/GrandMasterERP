<?php
$PAGE_ID = "LOGIN_PAGE";
include_once "includes/baseIncludes.php";
?>
<!doctype html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once "includes/auth_head_section.php" ?>
    <title>GrandMaster ERP | Login</title>
</head>
<body>
<!-- Content -->
<div class="authentication-wrapper authentication-cover">
    <!-- Logo -->
    <a href="/" class="app-brand auth-cover-brand">
        <span class="app-brand-logo">
          <img src="/assets/img/gmmsa_logo.png" width="250" alt="GrandMaster">
        </span> <span class="app-brand-text demo text-heading fw-bold">ERP</span> </a>
    <!-- /Logo -->
    <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        <div class="d-none d-lg-flex col-lg-8 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                <img src="/assets/img/branding/G-Logo@3x.png" alt="auth-login-cover" class="my-5 auth-illustration" data-app-light-img="branding/G-Logo@3x.png" data-app-dark-img="branding/G-Logo@3x.png"/>
                <img src="/assets/img/illustrations/bg-shape-image-light.png" alt="auth-login-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png"/>
            </div>
        </div>
        <!-- /Left Text -->
        <!-- Login -->
        <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6 form-block">
            <div class="w-px-400 mx-auto mt-12 pt-5">
                <h4 class="mb-1">Welcome to GrandMaster ERP! 👋</h4>
                <p class="mb-6">Please sign-in to your account</p>
                <form id="formAuthentication" class="mb-6" action="/login" method="POST">
                    <div class="col-12 d-inline-flex"  style="display: none !important;">
                        <div class="mb-3 col-6">
                            <label for="selectpickerHeader" class="form-label">Change Company</label>
                            <select id="selectpickerHeader" class="selectpicker w-100" data-style="btn-default" data-header="Select a Company">
                                <option>Spare parts Co.</option>
                                <option>Maintenance Co.</option>
                                <option>E-Commerce Co.</option>
                                <option>Consultation Co.</option>
                                <option>Real estate Co.</option>
                                <option>Property management Co.</option>
                            </select>
                        </div>
                        <div class="mb-3 col-6">
                            <label for="selectpickerHeader" class="form-label">Role</label>
                            <select id="selectpickerHeader" class="selectpicker w-100" data-style="btn-default" data-header="Select a Role">
                                <option>CEO Login</option>
                                <option>Human resource</option>
                                <option>Accountant</option>
                                <option>Company Asset</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="email" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="email-username" placeholder="Enter your username" autofocus/>
                    </div>
                    <div class="mb-6 form-password-toggle">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password"/>
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                    </div>
                    <div class="my-8">
                        <div class="d-flex justify-content-between">
                            <div class="form-check mb-0 ms-2">
                                <input class="form-check-input" type="checkbox" id="remember-me"/>
                                <label class="form-check-label" for="remember-me"> Remember Me</label>
                            </div>
<!--                            <a href="auth-forgot-password-cover.html">-->
<!--                                <p class="mb-0">Forgot Password?</p>-->
<!--                            </a>-->
                        </div>
                    </div>
                    <button class="btn btn-primary d-grid w-100 btn-form-block">Sign in</button>
                </form>


                <!-- A button to trigger a test error -->
<!--                <button id="test-error">Trigger Test Error</button>-->
<!--                <script>-->
<!--                    const button = document.getElementById('test-error');-->
<!--                    button.addEventListener('click', () => {-->
<!--                        throw new Error('This is a test error');-->
<!--                    });-->
<!--                </script>-->

                <!--            <p class="text-center">-->
                <!--              <span>New on our platform?</span>-->
                <!--              <a href="auth-register-cover.html">-->
                <!--                <span>Create an account</span>-->
                <!--              </a>-->
                <!--            </p>-->
                <!--            <div class="divider my-6">-->
                <!--              <div class="divider-text">or</div>-->
                <!--            </div>-->
                <!--            <div class="d-flex justify-content-center">-->
                <!--              <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-facebook me-1_5">-->
                <!--                <i class="tf-icons ti ti-brand-facebook-filled"></i>-->
                <!--              </a>-->
                <!---->
                <!--              <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-twitter me-1_5">-->
                <!--                <i class="tf-icons ti ti-brand-twitter-filled"></i>-->
                <!--              </a>-->
                <!---->
                <!--              <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-github me-1_5">-->
                <!--                <i class="tf-icons ti ti-brand-github-filled"></i>-->
                <!--              </a>-->
                <!---->
                <!--              <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-google-plus">-->
                <!--                <i class="tf-icons ti ti-brand-google-filled"></i>-->
                <!--              </a>-->
                <!--            </div>-->
            </div>
        </div>
        <!-- /Login -->
    </div>
</div>
<!-- / Content -->
<?php include_once __DIR__ . "/includes/auth_footer_scripts.php"; ?>
<script>
    $(document).ready(function () {

        $(".selectpicker").selectpicker();

        $("#formAuthentication").submit(function (e) {
            e.preventDefault();

            blockArea($('.form-block'));

            var form = $('form')[0]; // You need to use standard javascript object here
            var formData = new FormData(form);
            $.ajax({
                url: '/ajax/ajax_auth.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data, status) {
                    console.log(data);
                    console.log(status);
                    unBlockArea($('.form-block'));
                    var statusmessage = data.trim().split("|")[0];
                    var message = data.trim().split("|")[1];

                    if (statusmessage == "SUCCESS") {
                        // window.location = "/dashboard";
                        // Use the echoed URL (message) for redirect
                        var redirectUrl = message || '/dashboard';
                        window.location = redirectUrl;
                    }

                    if (statusmessage == "ERROR") {

                        Swal.fire({
                            title: 'Oops!',
                            icon: 'error',
                            text: message,
                            type: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            showClass: {
                                popup: 'animate__animated animate__shakeX'
                            },

                            buttonsStyling: false
                        });


                    }
                },

                error: function (error) {

                    console.log(error);
                }
            });


            return false;
        });

    });
</script>
</body>
</html>