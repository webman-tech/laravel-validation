# webman-tech/laravel-validation

Laravel [illuminate/validation](https://packagist.org/packages/illuminate/validation) for webman

## 介绍

站在巨人（laravel）的肩膀上使文件存储使用更加*可靠*和*便捷*

所有方法和配置与 laravel 几乎一模一样，因此使用方式完全参考 [Laravel文档](http://laravel.p2hp.com/cndocs/8.x/validation) 即可

## 安装

```bash
composer require webman-tech/laravel-validation
```

## 使用

所有 API 同 laravel，以下仅对有些特殊的操作做说明

常规使用如下：

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function bar(Request $request) 
    {
        $validator = valiator($request->post(), [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
        ]);
        if ($validator->fails()) {
            return json($validator->errors->first());
        }
        return json('ok');
    }
}
```

### file/mimeType 相关的 rule

由于 laravel 的 validation 验证 file/image 等文件时（包括文件 mimeType）时，都是使用的 Symfony 的 UploadedFile，而 webman 里 `$request->file()` 得到的是 `Webman\UploadFile`，
因此无法直接使用相关的 rules

需要使用 [`webman-tech/polyfill`](https://github.com/webman-tech/polyfill) 来支持

安装

```bash
composer require webman-tech/polyfill illuminate/http
```

使用

```php
<?php
namespace app\controller;

use support\Request;
use WebmanTech\Polyfill\LaravelRequest;

class FooController
{
    public function bar(Request $request) 
    {
        $validator = valiator(LaravelRequest::wrapper($request)->all(), [
           'file' => 'required|file|image',
        ]);
        if ($validator->fails()) {
            return json($validator->errors->first());
        }
        return json('ok');
    }
}
```

### $request->validate

需要使用 [`webman-tech/polyfill`](https://github.com/webman-tech/polyfill) 来支持

安装

```bash
composer require webman-tech/polyfill illuminate/http
```

使用

```bash
<?php
namespace app\controller;

use support\Request;
use WebmanTech\Polyfill\LaravelRequest;

class FooController
{
    public function bar(Request $request) 
    {
        LaravelRequest::wrapper($request)->validate([
            'file' => 'required|file|image',
        ]);
        
        return json('ok');
    }
}
```

### 自定义验证规则

在 `config/plugin/webman-tech/laravel-validation/app.php` 的 `extends` 字段中配置即可

配置形式同 Laravel

> 目前暂未提供 make:rule 的 command，需要自己写 Rule 类

### 手动切换 locale

因为没有 Laravel App 的存在，所以不能通过 `App::setLocale()` 和 `App::currentLocale()` 来切换验证器的语言

且由于 webman 建议的多语言是使用的 `symfony/translation`，并且全局 `locale` 函数也是使用其实现的

因此本扩展基于此原因，已经做到了根据 `locale()` 自动切换 `validator()` 下使用的语言包，无需开发手动设置
