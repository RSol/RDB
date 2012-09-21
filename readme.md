RDB
=====

Yii extention for MySQL database with:
- INSERT INTO table (field) VALUES (1),(2)....
- INSERT IGNORE
- INSERT ... ON DUPLICATE KEY UPDATE

Requirements
------------

- MySQL Database
- Server should be able to run [Yii framework](http://www.yiiframework.com/).

Install
-------

- Copy files "RDbCommand.php" and "RDbConnection.php" into your "components" directory.
- In your main config write in db section 'class' param like this
~~~
[php]
'db' => array(
	'class' => 'RDbConnection',
    'connectionString' => 'mysql:host=localhost;dbname=database',
    'emulatePrepare' => true,
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
	'schemaCachingDuration' => 3600,
    'enableProfiling' => true,
	'tablePrefix' => '',
	'enableParamLogging' => true,
);
~~~

License
-------

Fayiiles is licensed under New BSD license. That allows proprietary use, and for
the software released under the license to be incorporated into proprietary
products. Works based on the material may be released under a proprietary license
or as closed source software. It is possible for something to be distributed
with the BSD License and some other license to apply as well.
