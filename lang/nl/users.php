<?php

return [
    'index' => [
        'title' => 'Gebruikers beheren',
        'add_user' => 'Gebruiker toevoegen',
        'search' => 'Zoeken...',
        'no_users_found' => 'Geen gebruiker gevonden met :search in de naam.',
        'edit' => 'Gebruiker bewerken',
        'delete' => 'Gebruiker verwijderen',
        'confirm_delete' => 'Weet je zeker dat je deze gebruiker wilt verwijderen?',
    ],
    'create' => [
        'title' => 'Gebruiker toevoegen',
        'name' => 'Naam',
        'email' => 'E-mailadres',
        'save' => 'Opslaan',
    ],
    'edit' => [
        'title' => 'Bewerk :name',
        'title_self' => 'Bewerk je profiel',
        'name' => 'Naam',
        'email' => 'E-mailadres',
        'save' => 'Opslaan',
        'delete' => 'Verwijderen',
        'confirm_delete' => 'Weet je zeker dat je deze gebruiker wilt verwijderen?',
    ],
    'password' => [
        'title' => 'Wijzig je wachtwoord',
        'current_password' => 'Huidige wachtwoord',
        'new_password' => 'Nieuwe wachtwoord',
        'confirm_password' => 'Bevestig je nieuwe wachtwoord',
        'save' => 'Opslaan',
    ],
    'flash' => [
        'created_warning' => 'De gebruiker “<i>:name</i>” is succesvol toegevoegd, maar er kon geen email gestuurd worden met instructies om een wachtwoord aan te maken. De volgende melding is terug gegeven: <em>:status</em>',
        'created_success' => 'De gebruiker “<i>:name</i>” is succesvol toegevoegd en er een email gestuurd met instructies om een wachtwoord aan te maken.',
        'updated' => 'De gebruiker “<i>:name</i>” is succesvol aangepast!',
        'deleted' => 'De gebruiker “<i>:name</i>” is succesvol verwijderd!',
        'password_updated' => 'Je wachtwoord is succesvol gewijzigd!',
    ],
];
