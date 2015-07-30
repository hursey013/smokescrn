## smokescrn

##### So what's this thing for?

I'll cut right to it. I built this app for my own selfish reasons. Every time I share a password, account number, or personal information with someone, I cringe the moment I push the send button. I know my message might be stored on some server forever, or exposed for the world to see if our accounts are ever compromised. Call me paranoid, but I wanted something a little more secure. That's where smokescrn comes in.

##### Ok, how does it work?

When you enter your information into smokescrn we use the password you choose to encrypt the message to the highest standards available. It's so secure that if you forget your password we have no way of recovering the information because we only store the encrypted data. On top of that, once the message is viewed by the recipient it is permanently wiped off the face of the internet.

##### Requirements:

- PHP 5.4 or newer
- [Orchestrate.io](http://orchestrate.io) account
- [SendGrid](http://sendgrid.com) account

###### All of the following dependencies can be installed using composer:

- [defuse/php-encryption](https://github.com/defuse/php-encryption)
- [andrefelipe/orchestrate-php](https://github.com/andrefelipe/orchestrate-php)
- [sendgrid/sendgrid](https://github.com/sendgrid/sendgrid-php)
- [katzgrau/klogger](https://github.com/katzgrau/klogger)
