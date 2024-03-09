# Money History Auto

![License](https://img.shields.io/github/license/Mattoids/flarum-ext-money-history-auto) [![Latest Stable Version](https://img.shields.io/packagist/v/mattoid/flarum-ext-money-history.svg)](https://packagist.org/packages/mattoid/flarum-ext-money-history) [![Total Downloads](https://img.shields.io/packagist/dt/mattoid/flarum-ext-money-history.svg)](https://packagist.org/packages/mattoid/flarum-ext-money-history)

A [Flarum](http://flarum.org) extension to automatically notify the [Money History](https://github.com/Mattoids/flarum-ext-money-history) extension of any changes make to the 'money' field, facilitating the recording of user's money change.

这是 [Money History](https://github.com/Mattoids/flarum-ext-money-history) 的一个拓展插件，用于监听`money`字段变化后通知 [Money History](https://github.com/Mattoids/flarum-ext-money-history) 插件来达到记录用户资产变化。

#### Please note: This extension serves as a temporary solution until other extensions are updated to fully integrate with [Money History](https://github.com/Mattoids/flarum-ext-money-history).

#### 注意：本插件只是一个在其他插件适配 [Money History](https://github.com/Mattoids/flarum-ext-money-history) 前过渡用的临时解决方案。

## Problem
This extension has been developed and tested only on Chinese forums and did not take into account that there will be multiple languages on the forums at the same time. Therefore, there may be problems when being used on multilingual forums. PRs are welcomed!😊

本插件仅在中文论坛上进行开发与测试，并未考虑到在论坛上同时存在多种语言的情况，因此在多语言论坛上可能存在问题。欢迎PR！😊

## Screenshots
![YC3SWVB3DL$YERVVRTFOIQ1](https://github.com/huoxin233/flarum-ext-money-history-auto/assets/23447157/e31d8048-e8ef-46bf-b44b-94b0792f77f2)

![RUO SWWVYBPMG~8{Z({UU$6](https://github.com/huoxin233/flarum-ext-money-history-auto/assets/23447157/d5ebcb29-de3e-4d1e-a780-e7be838c7444)


## Installation

Install with composer:

```sh
composer require mattoid/flarum-ext-money-history-auto:"*"
```

## Updating

```sh
composer update mattoid/flarum-ext-money-history-auto:"*"
php flarum migrate
php flarum cache:clear
```

## Links

- [Packagist](https://packagist.org/packages/mattoid/flarum-ext-money-history)
- [GitHub](https://github.com/mattoid/flarum-ext-money-history)
- [Discuss](https://discuss.flarum.org/d/PUT_DISCUSS_SLUG_HERE)
