<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Emails Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for various emails that
    | we need to display to the user. You are free to modify these
    | language lines according to your application's requirements.
    |
    */

    /*
     * Activate new user account email.
     *
     */

    'activationSubject'  => 'Activation required',
    'activationGreeting' => 'Welcome!',
    'activationMessage'  => 'You need to activate your email before you can start using all of our services.',
    'activationButton'   => 'Activate',
    'activationThanks'   => 'Thank you for using our application!',
    'footer_text'        => 'All rights reserved.',
    'activateSuccessmsg' => 'Thank you! Your account has successfully activated.',
    /*
     * reset user password.
     *
     */

    'resetsubject'  => 'Reset Password Notification',
    'resetgreeting' => 'Hello!',
    'resetmessage'  => 'You are receiving this email because we received a password reset request for your account.',
    'resetbutton'   => 'Reset Password',
    'resetmsg'      => 'If you did not request a password reset, no further action is required.',
    'footer_text'   => 'All rights reserved.',
    'linkbefore'    => 'If you’re having trouble clicking the',
    'linkafter'     => 'button, copy and paste the URL below into your web browser',

    /*
     * Goobye email.
     *
     */
    'goodbyeSubject'  => 'Sorry to see you go...',
    'goodbyeGreeting' => 'Hello :username,',
    'goodbyeMessage'  => 'We are very sorry to see you go. We wanted to let you know that your account has been deleted. Thank for the time we shared. You have '.config('settings.restoreUserCutoff').' days to restore your account.',
    'goodbyeButton'   => 'Restore Account',
    'goodbyeThanks'   => 'We hope to see you again!',

];
