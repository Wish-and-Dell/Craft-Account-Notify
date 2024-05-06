# Craft Account Notify

Notify users when their account details are updated, such as email address, password, etc.

## Installation

    composer require wishanddell/craft-accountnotify

## Field Comparisons

This plugin will compare all custom fields assigned to users via Crafts field layout + username, password, email and fullName.

## Email Template

The email template can be customised by creating a file in your projects templates folder called `_accountnotify.twig`
This template works like any other Craft twig template with the addition of the `diff` variable which is an array of
field changes on the user.

See the default template in `src/templates/_email.twig` as a starting point.