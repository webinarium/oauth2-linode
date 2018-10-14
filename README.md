# Linode Provider for OAuth 2.0 Client

[![PHP](https://img.shields.io/badge/PHP-5.6%2B-blue.svg)](https://secure.php.net/migration56)
[![Latest Stable Version](https://poser.pugx.org/webinarium/oauth2-linode/v/stable)](https://packagist.org/packages/webinarium/oauth2-linode)
[![Build Status](https://travis-ci.org/webinarium/oauth2-linode.svg?branch=master)](https://travis-ci.org/webinarium/oauth2-linode)
[![Code Coverage](https://scrutinizer-ci.com/g/webinarium/oauth2-linode/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/webinarium/oauth2-linode/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webinarium/oauth2-linode/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webinarium/oauth2-linode/?branch=master)

This package provides Linode OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```bash
composer require webinarium/oauth2-linode
```

## Usage

Usage is the same as The League's OAuth client, using `\Linode\OAuth2\Client\Provider\Linode` as the provider.

### Authorization Code Flow

```php
$provider = new Linode\OAuth2\Client\Provider\Linode([
    'clientId'     => '{linode-client-id}',
    'clientSecret' => '{linode-client-secret}',
    'redirectUri'  => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        /** @var \Linode\OAuth2\Client\Provider\LinodeResourceOwner $owner */
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getFirstName());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
```

### Managing Scopes

When creating your Linode authorization URL, you can specify the state and scopes your application may authorize.

```php
$options = [
    'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
    'scope' => [
        Linode\OAuth2\Client\Provider\Linode::SCOPE_ACCOUNT_READ_ONLY,
        Linode\OAuth2\Client\Provider\Linode::SCOPE_LINODES_READ_WRITE,
    ]
];

$authorizationUrl = $provider->getAuthorizationUrl($options);
```

If neither are defined, the provider will utilize internal defaults.

At the time of authoring this documentation, the [following scopes are available](https://developers.linode.com/api/v4#section/OAuth):

- SCOPE_ACCOUNT_READ_ONLY
- SCOPE_ACCOUNT_READ_WRITE
- SCOPE_DOMAINS_READ_ONLY
- SCOPE_DOMAINS_READ_WRITE
- SCOPE_EVENTS_READ_ONLY
- SCOPE_EVENTS_READ_WRITE
- SCOPE_IMAGES_READ_ONLY
- SCOPE_IMAGES_READ_WRITE
- SCOPE_IPS_READ_ONLY
- SCOPE_IPS_READ_WRITE
- SCOPE_LINODES_READ_ONLY
- SCOPE_LINODES_READ_WRITE
- SCOPE_LONGVIEW_READ_ONLY
- SCOPE_LONGVIEW_READ_WRITE
- SCOPE_NODEBALANCERS_READ_ONLY
- SCOPE_NODEBALANCERS_READ_WRITE
- SCOPE_STACKSCRIPTS_READ_ONLY
- SCOPE_STACKSCRIPTS_READ_WRITE
- SCOPE_VOLUMES_READ_ONLY
- SCOPE_VOLUMES_READ_WRITE

## Development

``` bash
./bin/php-cs-fixer fix
./bin/phpunit --coverage-text
```

## Contributing

Please see [CONTRIBUTING](https://github.com/webinarium/oauth2-linode/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Artem Rodygin](https://github.com/webinarium)

## License

The MIT License (MIT). Please see [License File](https://github.com/webinarium/oauth2-linode/blob/master/LICENSE) for more information.
