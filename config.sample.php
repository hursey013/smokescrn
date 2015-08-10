<?php
// General Settings
define("SITE_NAME", "SuperSecret");
define("SITE_TAGLINE", "Self-destructing encrypted messages");
define("SITE_URL", "https://secretwebsite.com"); // No trailing slash
define("PASSWORD_ITERATIONS", "100000");
define("DEBUG_MODE", false);

// Email Settings
define("EMAIL_SUBJECT_SENT", "New " . SITE_NAME . " message");
define("EMAIL_SUBJECT_VIEWED", SITE_NAME . " message viewed");
define("EMAIL_BODY_SENT", "A secret message has been sent to you.");
define("EMAIL_BODY_VIEWED", "The secret message you sent has been viewed.");
define("EMAIL_FROM_ADDRESS", "me@secretwebsite.com");

// Logging Settings
define("LOGGING_BASE_DIR", __DIR__."/../logs"); // Writable directory

// SendGrid API Settings
define("SENDGRID_API_KEY", "INSERT YOUR API");

// Orchestrate.io API Settings
define("ORCHESTRATE_API_KEY", "INSERT YOUR API");
define("ORCHESTRATE_COLLECTION", "secrets");

// Numerous API Settings
//define("NUMEROUS_API_KEY", "INSERT YOUR API");
//define("NUMEROUS_METRIC_ID", "12345");

// User Notifications
define("VALIDATION_POST", "Form data not received as a POST request.");
define("VALIDATION_AJAX", "Form data not received as an AJAX request.");
define("VALIDATION_REQUIRED_FIELDS", "All fields are required.");
define("VALIDATION_TEXTAREA_LENGTH", "Your message is too long.");
define("VALIDATION_PASSWORD_LENGTH", "Your password is not long enough.");
define("VALIDATION_PASSWORD_MISMATCH", "Your passwords do not match.");
define("VALIDATION_PASSWORD_HINT_LENGTH", "Your password hint is too long.");
define("VALIDATION_EMAIL_INVALID", "Please provide a valid email address.");
define("VALIDATION_DATE_INVALID", "Please provide a valid expiration date.");
define("VALIDATION_MESSAGE_LENGTH", "Your message ID is too long.");
define("VALIDATION_MESSAGE_NOTFOUND", "The message ID you entered has expired or cannot be found.");
define("VALIDATION_TOO_MANY_ATTEMPTS", "This message has been temporarily locked.");
define("ENCRYPTION_UNSAFE", "Cannot safely perform encryption.");
define("DECRYPTION_UNSAFE", "Cannot safely perform decryption.");
define("DECRYPTION_PASSWORD_WRONG", "Something's wrong, please double check your password.");
define("SUCCESS_ENCRYPTION", "Your message has been successfully created.");
define("SUCCESS_DECRYPTION", "Your message has been permanently deleted.");
define("INTERNAL_ERROR", "An internal error has occured.");