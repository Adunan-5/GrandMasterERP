<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
<meta name="description" content=""/><!-- Favicon -->
<link rel="icon" type="image/x-icon" href="/assets/img/branding/G-Logo.png"/><!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap" rel="stylesheet"/><!-- Icons -->
<link rel="stylesheet" href="/assets/vendor/fonts/fontawesome.css"/>
<link rel="stylesheet" href="/assets/vendor/fonts/tabler-icons.css"/>
<link rel="stylesheet" href="/assets/vendor/fonts/flag-icons.css"/><!-- Core CSS -->
<link rel="stylesheet" href="/assets/vendor/css/rtl/core.css" class="template-customizer-core-css"/>
<link rel="stylesheet" href="/assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css"/>
<link rel="stylesheet" href="/assets/css/demo.css"/><!-- Vendors CSS -->
<link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css"/>
<link rel="stylesheet" href="/assets/vendor/libs/spinkit/spinkit.css"/>
<link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>
<link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css"/>
<link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
<link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
<link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/><!-- Vendor -->
<link rel="stylesheet" href="/assets/vendor/libs/@form-validation/form-validation.css"/><!-- Page CSS --><!-- Page -->
<link rel="stylesheet" href="/assets/vendor/css/pages/page-auth.css"/><!-- Helpers -->
<script src="/assets/vendor/js/helpers.js"></script><!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section --><!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
<script src="/assets/vendor/js/template-customizer.js"></script><!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
<script src="/assets/js/config.js"></script>

<!--Microsoft Clarity-->
<script type="text/javascript">
    (function (c, l, a, r, i, t, y) {
        c[a] = c[a] || function () {
            (c[a].q = c[a].q || []).push(arguments)
        };
        t = l.createElement(r);
        t.async = 1;
        t.src = "https://www.clarity.ms/tag/" + i;
        y = l.getElementsByTagName(r)[0];
        y.parentNode.insertBefore(t, y);
    })(window, document, "clarity", "script", "q57hzptbrs");
</script>

<!--Sentry Session Replay-->
<script src="https://eyewitness.zeenara.com/js-sdk-loader/b62e9e034d3fd24c8f0f977c01c68d87.min.js" crossorigin="anonymous"></script>
<script>
    Sentry.onLoad(function () {
        Sentry.init({
            debug: false,
            integrations: [
                Sentry.replayIntegration(),
            ],
            // Session Replay
            replaysSessionSampleRate: 1.0, // This sets the sample rate at 10%. You may want to change it to 100% while in development and then sample at a lower rate in production.
            replaysOnErrorSampleRate: 1.0, // If you're not already sampling the entire session, change the sample rate to 100% when sampling sessions where errors occur.
        });
    });
</script>