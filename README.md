# webman-tech/laravel-validation

Laravel [illuminate/validation]((https://packagist.org/packages/illuminate/validation)) for webman

## 介绍

站在巨人（laravel）的肩膀上使文件存储使用更加*可靠*和*便捷*

所有方法和配置与 laravel 几乎一模一样，因此使用方式完全参考 [Laravel文档](http://laravel.p2hp.com/cndocs/8.x/validation) 即可

# 安装

```bash
composer require webman-tech/laravel-validation
```

> `illuminate/validation` 依赖 `illuminate/translation` 的实现，因此需要配置 `Translator` 实例，
在 `config/plugin/webman-tech/laravel-validation/app.php` 中配置

## 使用

所有 API 同 laravel，以下仅对有些特殊的操作做说明

### $request->validate

### file/mimeType 相关的 rule
