# webman-tech/laravel-validation

> Split from [webman-tech/laravel-monorepo](https://github.com/webman-tech/laravel-monorepo)

适用于 webman 的 Laravel 验证组件，基于 illuminate/validation 实现。

## 安装

```bash
composer require webman-tech/laravel-validation
```

## 简介

该组件将 Laravel 强大的验证功能引入 webman 框架中，提供了完整的数据验证解决方案。

所有方法和配置与 Laravel 几乎一致，因此使用方式可完全参考 [Laravel Validation 文档](https://laravel.com/docs/validation)。

## 特殊使用说明

### 1. 基本使用

```php
$validator = validator($request->post(), [
    'title' => 'required|unique:posts|max:255',
    'body' => 'required',
]);

if ($validator->fails()) {
    return json($validator->errors()->first());
}
```

### 2. 文件验证

由于 laravel 的 validation 验证 file/image 等文件时，都是使用的 Symfony 的 UploadedFile，而 webman 里 `$request->file()`
得到的是 `Webman\UploadFile`，因此无法直接使用相关的 rules。

需要使用 [`webman-tech/laravel-http`](https://github.com/webman-tech/laravel-http) 来支持：

```php
use WebmanTech\LaravelHttp\Facades\LaravelRequest;

$validator = validator(LaravelRequest::all(), [
   'file' => 'required|file|image',
]);
```

### 3. $request->validate

需要使用 [`webman-tech/laravel-http`](https://github.com/webman-tech/laravel-http) 来支持：

```php
use WebmanTech\LaravelHttp\Facades\LaravelRequest;

LaravelRequest::validate([
    'file' => 'required|file|image',
]);
```

### 4. 语言切换

本扩展已经做到根据 `locale()` 自动切换验证器的语言包，无需开发手动设置。

### 5. unique 验证器

unique 依赖数据库，本扩展对已经安装 `illuminate/database` 的 webman 应用自动支持。

如果不支持，比如报错：`Presence verifier has not been set.` 时，请手动安装 `illuminate/database`。

> 原则上不一定强依赖于 Laravel 的 database， TP 的应该也是可以的（实现 DatabasePresenceVerifierInterface），目前暂未实现，欢迎PR
