# php-interactor

A single purpose object library built for PHP. Heavily inspired by the [interactor](https://github.com/collectiveidea/interactor) gem for Ruby.

## Requirements

- PHP >= 5.6

## What is an Interactor?

An Interactor is, in simple words, a single purpose object. That means a class having a single responsibility according to
what SOLID principles describes. An Interactor usually represents an action, such as _SaveUser_, _BuildAttributes_, _GetExternalAPIResource_ and so forth.
A _SaveUser_ interactor would only literally mean saving a user record in some storage (a database, for instance) therefore it won't be responsible for
doing anything else.

## OK, but why is a big deal to use it?

You may be asking yourself why this library is relevant to you. Of course, you can go ahead and start creating your own single-purpose
object implementation though we all know that not establishing a well-designed pattern in the very beginning of a development
cycle can result in totally chaos where everybody does whatever they want without a pattern. Plus, why reinvent the wheel whilst someone has done the
heavy job for you?

By giving a chance to this library you will find yourself creating straight forward and maintainable services while adopting a conventional pattern among them.
I assure you that you will save some considerable time. Give it a shot! :)

## Creating your first Interactor

Say we're about to create a component responsible for saving a `User` model record:

```php
<?php

class SaveUser
{
}
```

To make it an Interactor, all you need to do is to simply import the `Interactable` trait. 
To do that, you only need to simply import it in your class:

```php
<?php

use MatheusRosa\PhpInteractor\Interactable;
use MatheusRosa\PhpInteractor\Context;

class SaveUser
{
    use Interactable;
    
    protected function execute(Context $context)
    {
        // When using the Interactable trait, the execute method
        // needs to be implemented. 
    }
}
```

There we go! You can put all of your business logic within the `execute` method. The `SaveUser` can be invoked like this:

```php
<?php

SaveUser::call([
    'name' => 'John Doe',
    'email' => 'john.doe@email.com',
]);
```

**Note** we have just passed an array as an argument to the static `call` method. You can pass any values to your associative array
or even leaving it as blank (not passing anything to it at all, e.g. `SaveUser::call()`).

**Note 2**: think of `call` method as a public API while `execute` method is how your business logic will be handled internally.
Each `Interactor` needs to implement the `execute` method.

You can retrieve the informed parameters in your `SaveUser` class like this:

```php

use MatheusRosa\PhpInteractor\Interactable;
use MatheusRosa\PhpInteractor\Context;

class SaveUser
{
    use Interactable;
    
    protected function execute(Context $context)
    {
        // All values passed to SaveUser::call are accessible here
        // within the current context object.
        var_dump($context->name, $context->email);
        
        // You can even create brand-new values and assign them to the current context
        $context->currentTime = time();
        
        $context->user = new User($context->name, $context->email);
        $context->user->save();
    }
}
```

### Checking Interactor success

If an Interactor does not call the `fail` method with an error message, it is considered as a success scenario.

You can check if by invoking the `success` method from the returned context:

```php
<?php

$context = SaveUser::call([
    'name' => 'John Doe',
    'email' => 'john.doe@email.com',
]);

$context->success(); // returns either true or false
```

### Failing an Interactor

Interactors can be set as failure like this:

```php

use MatheusRosa\PhpInteractor\Interactable;
use MatheusRosa\PhpInteractor\Context;

class SaveUser
{
    use Interactable;
    
    protected function execute(Context $context)
    {
        $context->user = new User($context->name, $context->email);
        
        if (!$context->user->save()) {
            $context->fail('custom error message | model error message');
        }
    }
}
```

Once the `fail` method is invoked **the execution flow will immediately stop**. That means any code after the `if` condition in the example above
will become unreachable.

By default, the `fail` method does not throw any exception though you can change its behavior by setting its second argument (`$strict`)
as true:

```php
$context->fail('an error message', true);
```

That way, from now on the `ContextFailureException` will be raised.

### The Context

Describe the context.
