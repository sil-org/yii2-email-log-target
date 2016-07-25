# yii2-email-log-target
Custom version of yii\log\EmailTarget to exclude trace information from messages

## What & Why
The built in ```yii\log\EmailTarget``` class does a nice job of serializing and sending log and 
exception messages via email. Unfortunately when the message is an exception itself the exception 
is serialized to string using default``` \Exception::__toString()``` method which includes stack 
trace information. The Yii logger component allows configuration of whether or not to include trace 
information but it does not apply to exception messages.

This class is a modified version of ```yii\log\EmailTarget``` to ensure no trace information 
is set in the email. 

## Have the log prefix (if used)

Example configuration
```['components']['log']['targets']``` array):

    [
        'class' => 'Sil\Log\EmailTarget',
        'levels' => ['error'],
        'except' => [
            'yii\web\HttpException:401',
            'yii\web\HttpException:404',
        ],
        'logVars' => [], // Disable logging of _SERVER, _POST, etc.
        'prefix' => function($message) use ($appEnv) {
            $prefix = 'env=' . $appEnv . PHP_EOL;

            // There is no user when a console command is run
            try {
                $appUser = \Yii::$app->user;
            } catch (\Exception $e) {
                $appUser = Null;
            }
            if ($appUser && ! \Yii::$app->user->isGuest){
                $prefix .= 'user='.\Yii::$app->user->identity->email . PHP_EOL;
            }

            // Try to get requested url and method
            try {
                $request = \Yii::$app->request;
                $prefix .= 'Requested URL: ' . $request->getUrl() . PHP_EOL;
                $prefix .= 'Request method: ' . $request->getMethod() . PHP_EOL;
            } catch (\Exception $e) {
                $prefix .= 'Requested URL: not available';
            }

            return PHP_EOL . $prefix;
        },
    ],

## License

This is released under the MIT license (see LICENSE file).