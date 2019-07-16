# Session
Session manager package.

[![Build Status](https://travis-ci.org/DBSeller/Session.svg?branch=master)](https://travis-ci.org/DBSeller/Session)

## Install
```bash
# composer require dbseller/session
```

## Usage:
```php
$session = DBseller\Session\Session::getInstance()->start()
$session->addParameters(['PARAM_TO' => 'SESSION'])
```
