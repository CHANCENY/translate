# Simp Translate Package

A PHP library for easy text translation between different languages using Google Translate API with caching support.

## Installation

Install the package via composer:

```bash
   composer require simp/translate
```

## Usage

### Basic usage

```php

    require_once 'vendor/autoload.php';
    
    // Note: this will use the default translation languages, i.e., en to fr
    echo Translate::translate('Hello, How are you?');

```

### Advance usage
  
```php
   
   require_once 'vendor/autoload.php';
   
   //Note: here we are manually setting the output language ie the original text is english which will be translated to Nyanja (Chichewa)
   echo Translate::translate('Hello, How are you?', to: 'ny');
   
   // This is also possible.
   echo Translate::translate('Moni', from: 'ny', to: 'en');
   
   // Sometimes you will need to just save the translation result some where to reuse it, in that way you dont have ask google to translate everytime.
   echo Translate::translate('Hello, How are you?', to: 'ny', cacheLocation: __DIR__.'/cache');

```

## Maintainer
[Chance Nyasulu](https://github.com/CHANCENY)


