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

    /*
     * reset user password.
     *
     */

    'resetsubject'  => 'Réinitialisation de votre mot de passe',
    'resetgreeting' => 'Salut!',
    'resetmessage'  => 'Vous recevez cet e-mail, car nous avons reçu une demande de réinitialisation de votre mot de passe.',
    'resetbutton'   => 'réinitialiser le mot de passe',
    'resetmsg'      => "Si vous n'avez pas demandé de réinitialisation, aucune autre action n'est requise.",
    'footer_text'   => 'Tous les droits sont réservés.',

    'activationSubject'  => 'Authentification requise',
    'activationGreeting' => 'Bienvenue sur NYOTA!',
    'activationMessage'  => 'Afin d’activer votre compte et bénéficier de l’ensemble de nos services, prière de cliquer sur le bouton ci-dessous',
    'activationButton'   => 'Valider !', 
    'activationThanks'   => "Merci d’avoir téléchargé notre application.",
    'footer_text'       => 'Tous les droits sont réservés.',

    'activateSuccessmsg' => 'Je vous remercie! Votre compte Hans: Susasssasfuli activé.',
    'linkbefore'        => 'Si vous ne parvenez pas à cliquer sur le bouton',
    'linkafter'         => "copiez et collez l'URL ci-dessous dans votre navigateur Web",
    /*
     * Goobye email.
     *
     */
    'goodbyeSubject'  => 'Désolé de vous voir partir...',
    'goodbyeGreeting' => 'Bonjour :username,',
    'goodbyeMessage'  => 'Nous vous confirmons la suppression de votre compte.'.
                           'Nous sommes désolés de vous voir partir.'.
                           'Merci pour le temps que nous avons passé ensemble.'.
                           'Vous pouvez récupérer votre compte dans les '.config('settings.restoreUserCutoff').' jours à venir.',
    'goodbyeButton' => 'Récupérer votre compte',
    'goodbyeThanks' => 'Nous espérons vous revoir bientôt.',

];
