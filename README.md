[![Build Status](https://www.travis-ci.org/aipng/json-serializer.svg?branch=master)](https://www.travis-ci.org/aipng/json-serializer)

# JSON Serializer

- [JMS Serializer](https://github.com/schmittjoh/serializer), limited to JSON,
- custom serialization handlers,
- [Nette](https://nette.org) extension

## How to install
```
composer require aipng/json-serializer
```

# Usage
Could be used standalone or as Nette extension.

## Nette extension
config.neon
```neon
extensions:
	serializer: AipNg\JsonSerializer\DI\JsonSerializerExtension

serializer:
	temporaryDirectory: %tempDir%/cache
	productionMode: true # optional
	validator: # optional
	serializationHandlers: # optional
```

## Parameters
### temporaryDirectory
Use writable path

### productionMode
For debugging purposes, true by default

### validator
- class, that implements `AipNg\JsonSerializer\Validator`
- `AipNg\JsonSerializer\Validator\NullValidator` is used by default

It could be combined with `symfony/validator` with provided adapter. With [contributte/validator](https://github.com/contributte/validator) use this configuration:

```neon
extensions:
	serializer: AipNg\JsonSerializer\DI\JsonSerializerExtension
	validator: Contributte\Validator\DI\ValidatorExtension

serializer:
	temporaryDirectory: %tempDir%/cache
	validator: AipNg\JsonSerializer\Validator\SymfonyValidator
```

### serializationHandlers
Specific serialization handlers for _JMS serializer_. More id [JMS documentation](http://jmsyst.com/libs/serializer/master/handlers).
