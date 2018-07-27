<?php

require('../include/mellivora.inc.php');


// Check if we have the 'email' and 'code' in the query string
if(isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['code']) && !empty($_GET['code'])) {
    // Valid link structure.
    // Now check if the email and code are valid.
    $user = db_select_one(
        'users',
        array(
            'id'
        ),
        array(
            'email' => $_GET['email'],
            'email_validation_code' => $_GET['code']
        )
    );

    if (!$user) {
        message_error(lang_get('not_a_valid_link'));
    }


    // If there's a logged in user and the user is not the current user, log them out
    if ($logged_in_user_id = user_is_logged_in()) {
        // Get the logged in user email
        $logged_in_user = db_select_one(
            'users',
            array(
                'email'
            ),
            array(
                'id' => $logged_in_user_id
            )
        );

        // Compare the logged in user email with the email from the query string.
        if($logged_in_user['email'] !== $_GET['email']) {
            // The logged in user is not the same as the current user, log out but not to redirect to the a different page
            logout(1);
        }
        else {
            // The logged in user is the same as the current user, redirect to the home page
            redirect('home');
        }
    }

    // Email, validation code and logged-in user have been validated.
    // Now update the email_validated field in the DB for the current user.
    update_user_email_validated($user['id']);


    message_generic(lang_get('signup_email_validation_success_message'), Config::get('MELLIVORA_CONFIG_ACCOUNTS_DEFAULT_ENABLED') ?
        lang_get('signup_email_account_availability_message_login_now') :
        lang_get('signup_email_account_availability_message_login_later'));

} else {
    // Invalid link structure
    message_error(lang_get('not_a_valid_link'));

}


