# PHP composer 自动加载机制研究学习
----------------

## 概述

[composer](https://getcomposer.org/) 为我们提供了四种自动加载的机制，通过灵活的运用这些机制可以让我们在项目里省去了手动`include`或`require`的麻烦。

## 目录结构说明

```shell
├── Application.php
├── component
├── composer.json
├── core
├── domain
├── hello
├── helper
├── index.php
├── lego
├── Linux
├── README.md
└── util
```

整个示例中最关键的就是`composer.json`文件，其它文件和目录是为了说明四种加载机制而添加的。下面我们会对`composer.json`文件中`autoload`的四种配置（对应四种加载机制）进行演示。

我们的 `composer.json` 文件内容是这样的：

```shell
{
	"name": "dendi/composer",
	"description": "Composer Autoload Research",
	"version": "1.0.0-dev",
	"autoload": {
		"classmap" : ["component/"],
		"files": ["helper/functions.php"],
		"psr-0": {
			"Linux\\Man\\": "",
			"": "domain/",
			"Hello\\Girl\\": ["hello/"]
		},
		"psr-4": {
			"Dendi\\": "modules/",
			"": ["core/", "util/"],
			"App\\": "",
			"Lego\\": ["lego/src/"]
		}
	},
	"scripts": {
		"post-autoload-dump": [
			"Lego\\Scripts\\ComposerScripts::postAutoloadDump",
			"php -r \"file_exists('log.txt') || file_put_contents('log.txt', 'success');\""
		]
	}
}
```

## 准备

首先我们执行`composer dump-autoload`

```shell
$ composer dump-autoload
Generating autoload files
> Lego\Scripts\ComposerScripts::postAutoloadDump
> php -r "file_exists('log.txt') || file_put_contents('log.txt', 'success');"
```

这时 composer 自动加载器就会在`vendor/composer`目录下生成我们所需要的类或文件列表。

注意：我们的`composer.json`文件中定义了`scripts`，这是 composer 提供给我们的 [Scripts](https://getcomposer.org/doc/articles/scripts.md) 功能。

```shell
"scripts": {
    "post-autoload-dump": [
        "Lego\\Scripts\\ComposerScripts::postAutoloadDump",
        "php -r \"file_exists('log.txt') || file_put_contents('log.txt', 'success');\""
    ]
}
```

## classmap 自动加载

首先 `classmap`字段的值是一个数组，数组中的元素可以是一个文件名也可以是一个文件夹名，文件扩展名必须是`.php`，composer 自动加载器会处理文件夹下所有的`.php`文件，然后把文件中的完整类名提取出来做为`key`，类的路径作为值保存在 **vendor/composer/autoload_classmap.php** 文件数组中，生成className（类名）到 file path（文件路径）的映射。


例如我们的 composer.json 中的 `classmap` 是这样配置的：

```shell
"classmap" : ["component/"]
```

然后我们就可以调用 component 目录下的类了

```shell
<?php

require __DIR__ . '/vendor/autoload.php';

echo MyTimer::SEC_PER_DAY.PHP_EOL;
echo Process::ERR.PHP_EOL;
echo Queue\BeanstalkdQueue::QUEUE_SYSLOG.PHP_EOL;
```

以上示例输出：

```shell
# php index.php 
86400
err
syslog
```

## files 自动加载

composer 只能自动加载类，当想使用全局的函数或常量时，就必须使用`include`或`require`。或者把它们封装到一个类中（推荐）。

`files`字段值为一个数组，数组中每个元素必须都是一个文件不能使用文件夹，composer 自动加载器会把`files`字段设置的文件路径作为值，保存在**vendor/composer/autoload_files.php**中。

`files`字段的值是相对于应用根目录（相对于包）的文件的路径。本例中就是 composer-autoload-research

例如我们的 composer.json 中的 `files` 是这样配置的：

```shell
"files": ["helper/functions.php"]
```

然后我们就可以调用 `helper/functions.php` 文件中的方法了

```php
<?php

require __DIR__ . '/vendor/autoload.php';

echo func().PHP_EOL;
```

以上示例输出：

```shell
# php index.php 
func
```

## psr-0 自动加载

`psr-0`可以分为三种配置，它兼容了已经弃用[PSR-0](https://learnku.com/docs/psr/psr-0-automatic-loading-specification/1603)规范。

* 第一种配置

```shell
"psr-0": {
    "Linux\\Man\\": ""
}
```

上面配置的意思是`root`包目录下必须要有 Linux/Man 目录，Linux/Man 目录下的类必须要有`Linux\Man`这个命名空间前缀，支持伪命名空间（如：Signals_SigTerm 这种下划线进行分隔的）

调用 Linux/Man 目录下的类：

```php
<?php

require __DIR__ . '/vendor/autoload.php';

echo Linux\Man\Errno::EBADF.PHP_EOL;
echo Linux\Man\Signals\SigKill::SIGKILL.PHP_EOL;
echo Linux\Man\Signals_SigTerm::SIGTERM.PHP_EOL; #psr-0 伪命名空间类型
```

以上示例输出：

```shell
# php index.php 
9
9
15
```

* 第二种配置

```shell
"psr-0": {
    "": "domain/"
}
```

上面配置的意思是`domain`目录下的类是在全局命名空间下的，但它支持伪命名空间

调用 domain 目录下的类：

```php
<?php

require __DIR__ . '/vendor/autoload.php';

echo ServerManager::BEANSTALK_SERVER.PHP_EOL;
echo Me_Quan_Zhang::MY_SITE.PHP_EOL; # psr-0 的伪命令空间
```

以上示例输出：

```shell
# php index.php 
beanstalk.servers.dev.ofc:11300
zhangquan.me
```

* 第三种配置

```shell
"psr-0": {
    "Hello\\Girl\\": ["hello/"]
}
```

这种配置就是符合`PSR-4`的规范，意思是`hello`目录下所有的类必须要有`Hello\Girl`这个命令空间前缀

调用 hello 目录下的类：

```php
<?php

require __DIR__ . '/vendor/autoload.php';

echo Hello\Girl\HelloGirl::HELLO_GIRL.PHP_EOL;
```

以上示例输出：

```shell
# php index.php 
hello，girl!
```

## psr-4 自动加载

`psr-4`也可以分为三种配置，它是符合标准的[PSR-4](https://learnku.com/docs/psr/psr-4-autoloader/1608)规范。

* 第一种配置

```shell
"psr-4": {
    "": ["core/", "util/"]
}
```

这种配置的意思是`core`与`util`目录下的类全部在全局命名空间下。


调用`core`与`util`目录下的类：

```php
<?php

require __DIR__ . '/vendor/autoload.php';

echo Condition::EQ.PHP_EOL;
echo FileSystem::FILE_MODE.PHP_EOL;
```

以上示例输出：

```shell
# php index.php 
=
0644
```

* 第二种配置

```shell
"psr-4": {
    "App\\": ""
}
```

这种配置的意思是`root`包（本例是：composer-autoload-research）下的类必须要有 App 这个命令空间前缀才能被自动加载

调用根项目下的类：

```php
<?php

require __DIR__ . '/vendor/autoload.php';

echo App\Application::APP_NAME.PHP_EOL;

```

以上示例输出：

```shell
# php index.php 
notifyagent
```

* 第三种配置

```shell
"psr-4": {
    "Lego\\": ["lego/src/"]
}
```

这种配置的意思是 lego/src/ 目录下的类必须要有`Lego`这个命名空间前缀才能被自动加载。可以有多个目录共用同一个命名空间前缀，比如

```shell
"psr-4": {
    "Lego\\": ["lego/src/", "modules/"]
}
```


调用 lego/src/ 目录下的类：

```php
<?php

require __DIR__ . '/vendor/autoload.php';

echo Lego\Formatter\JsonFormatter::SIMPLE_DATE.PHP_EOL;
```

以上示例输出：

```shell
# php index.php 
Y-m-d H:i:s
```


## 使用总结

- `psr-0`配置中类的`_`被转为目录分隔符，

- `psr-4`是**命名空间前缀与文件基目录的映射，子命名空间用命名空间分隔符分隔开来后与文件基目录下的子目录之间的映射**

- 如果调整了`composer.json`文件中`autoload`字段的配置一定要记得执行`composer dump-autoload`

- 针对 `files` 的自动加载如果修改了文件名，首先要同步修改 `composer.json` 文件中 `files` 字段中的文件名，然后在执行 `composer dump-autoload`


