## smokescrn
[https://smokescrn.com](https://smokescrn.com)

##### So what's this thing for?

I'll cut right to it. I built this app for my own selfish reasons. Every time I share a password, account number, or personal information with someone, I cringe the moment I push the send button. I know my message might be stored on some server forever, or exposed for the world to see if our accounts are ever compromised. Call me paranoid, but I wanted something a little more secure. That's where smokescrn comes in.

##### Ok, how does it work?

When you enter your information into smokescrn we use the password you choose to encrypt the message to the highest standards available. It's so secure that if you forget your password we have no way of recovering the information because we only store the encrypted data. On top of that, once the message is viewed by the recipient it is permanently wiped off the face of the internet.

##### Requirements:

- PHP 5.5 or newer
- [Orchestrate.io](http://orchestrate.io) account
- [SendGrid](http://sendgrid.com) account

##### Installation
Clone the entire project to your server and use [composer](https://getcomposer.org) to download the PHP dependencies.  All of the settings for the script can be found in config.sample.php - you will need to update this with your own API keys for Orchestrate.io and SendGrid and then rename it to config.php.  You'll also need to edit LOGGING_BASE_DIR to point to a writeable log directory (not publically accessible).  Lastly, set up a reoccuring cron job to run the cron.php file - this is responsible for deleting expired messages.  I set it to run every night at midnight.

###### PHP:

- [defuse/php-encryption](https://github.com/defuse/php-encryption)
- [andrefelipe/orchestrate-php](https://github.com/andrefelipe/orchestrate-php)
- [sendgrid/sendgrid](https://github.com/sendgrid/sendgrid-php)
- [katzgrau/klogger](https://github.com/katzgrau/klogger)

###### jQuery:

- [hakimel/Ladda](https://github.com/hakimel/Ladda)
- [eternicode/bootstrap-datepicker](https://github.com/eternicode/bootstrap-datepicker)
- [1000hz/bootstrap-validator](https://github.com/1000hz/bootstrap-validator)