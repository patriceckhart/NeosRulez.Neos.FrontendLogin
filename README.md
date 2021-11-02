# Frontend Login for Neos CMS

A Neos CMS package that handles the frontend login and user management. The forms are created with custom finishers and validators in the Neos Form Builder..

## Installation

The NeosRulez.Neos.FrontendLogin package is listed on packagist (https://packagist.org/packages/neosrulez/neos-frontendlogin) - therefore you don't have to include the package in your "repositories" entry any more.

Just run:

```
composer require neosrulez/neos-frontendlogin
```

## Configuration

```yaml
NeosRulez:
  Neos:
    FrontendLogin:
      senderMail: 'noreply@foo.com'
      createUser:
        templatePathAndFilename: 'resource://NeosRulez.Neos.FrontendLogin/Private/Templates/Mail/Create.html'
      resetPassword:
        templatePathAndFilename: 'resource://NeosRulez.Neos.FrontendLogin/Private/Templates/Mail/Reset.html'
```

## Usage

There are three different finishers for the Neos Form Builder. The `Create User Finisher`, the `Update User Finisher` and the `Reset Password Finisher`. That is self-explanatory, use it as you need it. 

In addition, there is a validator for the Neos Form Builder which checks whether a username already exists or not. The behavior of this validator can be influenced in the node properties.

The user data from the forms are saved in the database as a json string. Property getters and setters are in the user model and can be used in your own extensions or backend modules.

## Author

* E-Mail: mail@patriceckhart.com
* URL: http://www.patriceckhart.com
