# Office365ADFSProxyAuthenticator
A small PHP class that can authenticate a Office365 user using a ADFS proxy service following O365 SSO recommended guidelines via CURL.

# Usage:
$auth = new Office365ADFSProxyAuthenticator(); <br/>
$authenticated = $auth -> authenticate("firstname.lastname@yourcompany.net", "password"); //true or false.

# To do:
Expand class with more functions.
