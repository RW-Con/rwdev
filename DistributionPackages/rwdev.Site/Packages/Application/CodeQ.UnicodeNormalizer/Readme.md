# Neos - Unicode Normalizer

This Neos package normalizes Unicode characters, often causing issues when copying text from PDFs into the Neos backend and then viewing it in IE/Edge.

## Requirements

Your PHP version must include the PECL intl >= 1.0.0 extension, which is included by default. We are using the PHP [Normalizer](https://www.php.net/manual/de/normalizer.normalize.php)

## Usage

1. Install with `composer require codeq/unicodenormalizer`

2. Wrap any object with processor to normalize its output,
e.g. you would probably want to normalize full page output like this:

```
prototype(Page).@process.normalize = CodeQ.UnicodeNormalizer:Normalizer
```
