# Sentry Logger

The Sentry Logger extension logs PHP errors to the specified Sentry DSN in the settings.

## Usage
1.  Set the Sentry DSN that errors should log to.  A default implementation is used to create the Raven client object.

2.  Configuration variables can be set using a JSON notation.  For example:

`{
    "environment": "production",
    "tags": {"php_version": "7.0"}
}`

## Special Notes

Third-party addons can sometimes throw a lot of PHP warnings or notices that trigger the log.  It is recommended to implement this in a development environment first before adding it to a production environment.