nette-translation
=================

[![Build Status](https://secure.travis-ci.org/bazo/nette-translation.png?branch=master)](http://travis-ci.org/bazo/nette-translation)

This is a companion extension for [kdyby/translation](https://github.com/Kdyby/Translation)

For now it has two commands:
Extract - will extract translatable tokens from php and latte files in your project

Compile - compiles kdyby/translation compatible catalogs **without overwriting** existing translations. automatically creates catalogs by domains if using keyword identifiers

Usage:

just add this line to your config.neon 

```
extensions:
    - Bazo\Translation\DI\TranslationExtension
```

tests are coming back soon
