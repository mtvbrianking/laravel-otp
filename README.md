Features

- Registration is free.

- 2FA is optional ie users can opt-in and opt-out at anytime.
  
  A user needs to confirm their password in order to enable or disable 2FA

- Recovery codes

- Google Authenticator

- Middleware to protect certain routes. Enforced on login by default

{b} matovu, [2/9/22 11:55 PM]
You can enforce 2FA

APP_ENFORCE_2FA=true

ie a user must setup 2fa after setting up a password

If 2fa isn’t enabled — show opt-in page
Else show enter otp screen

{b} matovu, [2/9/22 11:57 PM]
2FA can be used on secure routes

If 2fa is enabled — accept otp
Else — use password

{b} matovu, [2/10/22 12:01 AM]
Unit and feature tests for otp functionality


Tutorials

- https://dev.to/roxie/how-to-add-google-s-two-factor-authentication-to-a-laravel-8-application-4jjp

- https://shouts.dev/laravel-two-factor-authentication-with-google-authenticator

- https://www.youtube.com/watch?v=B-Iu1QGkP-o

- https://www.youtube.com/watch?v=jJKWDDj1Wgw

- https://www.youtube.com/watch?v=kPopzdiGR-U
