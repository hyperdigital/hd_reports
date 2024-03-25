# Reports
TYPO3 extension for external access of CORE information (current version, available update)
## Initialization
After Instalation of the extension is possible to access url YOUR_URL/?type=44&access=[ACCESS_TOKEN]
## ACCESS TOKEN
The access token is one of the security rules. It's possible to set this token inside the extension configuration. The default value is `Protector!42`.
## Additional security
It's possible to lock the accessibility only of specific IP adresses. Just add at least one into extension configuration.
## Automated notification when update is available
Over scheduler is possible to set 2 types of notification: Email and Push notification

The known issue for now is, that the system doesn't check when it was the last time when the notification was send so if the update is still available, it sends notficiation again

### Push notifications
The push notification can be set in a new task of scheduler (Execute console commands -> hdreports:availableUpdatesPushNotification: Push notification: available update)

There you can set the target URL of the notification and if needed also a content of the request. An argument notificationAlways is there for enabling testing - it will allow to send the notification always.

### Email notifications
The email notification can be set in a new task of scheduler (Execute console commands -> hdreports:availableUpdatesEmailNotification: Email notification: available update)