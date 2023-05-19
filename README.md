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

You can check it by invoking the `success` method from the returned context:

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
        
        // some other cool code
        // it will be unreachable if the $context->fail() was invoked
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

The errors itself can be retrievable like this:

```php
$context->errors(); // returns ['an error message']
```

## Hooks

Interactors contain a set of hooks that can run in some circumstances:

### `around`

Think of it like a middleware that will run even before of what's defined in your `execute` method.
You can totally prevent an Interactor to run if some particular rule is not satisfactory. This comes handy when needing to define a bunch of guards
preventing your code to execute:

```php
<?php

use \MatheusRosa\PhpInteractor\Interactable;
use \MatheusRosa\PhpInteractor\Context;

class SaveUser
{
    use Interactable;
    
    protected function around(Context $context)
    {
        // If the `around` method returns false
        // the `execute` method will not even start
        if (empty($context->user->email)) {
            return false;
        }
        
        // you can do whatever you want from this point forward,
        // like creating new variables to the $context or even adding new guards
    }
    
    protected function execute(Context $context)
    {
        if ($context->user->save()) {
            $context->fail('error message');
        }
    }
}
```

### `before`

As the name says for itself, the `before` hook is something that will execute right before what's defined in your `execute` method.

**Important to notice** that this method has a lesser priority than the `around` method.

```php
<?php

use \MatheusRosa\PhpInteractor\Interactable;
use \MatheusRosa\PhpInteractor\Context;

class SaveUser
{
    use Interactable;
    
    protected function before(Context $context)
    {
        // The `before` method will execute before the `execute` method.
        // Unlike the `around` method, it can't stop the execution flow of the current Interactor.
        // It comes more handy to initialize new variables.
        $context->currentTime = time();
    }
    
    protected function execute(Context $context)
    {
        if ($context->user->save()) {
            $context->fail('error message');
        }
    }
}
```

### `after`

Use the `after` method if you want to run anything *after* the `execute` method.

```php
use \MatheusRosa\PhpInteractor\Interactable;
use \MatheusRosa\PhpInteractor\Context;

class SaveUser
{
    use Interactable;
    
    protected function after(Context $context)
    {
        // this will execute after what's defined in your `execute` method
        $context->endTime = time();
    }
    
    protected function execute(Context $context)
    {
        if ($context->user->save()) {
            $context->fail('error message');
        }
    }
```

### Hook precedence

To clarify it even more, the execution order can be represented like below:

`around -> before -> execute -> after`

### Full example of an Interactor with all hooks 

```php
<?php

use \MatheusRosa\PhpInteractor\Interactable;
use \MatheusRosa\PhpInteractor\Context;

class YourClazz
{
    use Interactable;
    
    protected function around(Context $context)
    {
        $context->number += 1;
        
        echo "around | number: {$context->number}\n";
    }
    
    protected function before(Context $context)
    {
        $context->number += 1;
        
        echo "before | number: {$context->number}\n";
    }
    
    protected function execute(Context $context)
    {
        $context->number += 1;
        
        echo "execute | number: {$context->number}\n";
    }
    
    protected function after(Context $context)
    {
        $context->number += 1;
        
        echo "after | number: {$context->number}\n";
    }
}

YourClass::call(['number' => 0]);
```

Will output:

```
around | number: 1
before | number: 2
execute | number: 3
after | number: 4
```

## Organizers

Sometimes a single-purpose Interactor is not enough to embrace everything your business logic requires.

Say you're about to handle a custom flow that will need to do a lot of things. Of course you can call Interactors within themselves although Organizer
exists to make it way easier. With an Organizer you can define a pipeline of Interactors to run in a consecutive order.

To create an Organizer, all you have to do is to use the `Organizable` trait like that:

```php
<?php

use \MatheusRosa\PhpInteractor\Organizable;

class YourClazz
{
    use Organizable;
    
    protected function organize()
    {
        // when using the Organizable trait,
        // the organize method needs to be implemented.
    }
}
```

All right! And then within the `organize` method you can define the execution order of your Interactors:

```php
<?php

use \MatheusRosa\PhpInteractor\Organizable;

class YourOrganizedClazz
{
    use Organizable;
    
    protected function organize()
    {
        return [
            FirstInteractor::class,
            SecondInteractor::class,
            ThirdInteractor::class,
        ];
    }
}
```

Done! Now you've defined your chain and each Interactor will execute in the defined order. Your Organizer can be called the same way you'd call a single Interactor:

```php
$context = YourOrganizedClazz::call(['foo' => 'bar']);

// you can do the same context operations
$context->success(); // returns boolean
$context->failure(); // returns boolean
$context->errors(); // returns an array of errors
```

If you want, you can use the very same hooks present in `Interactor` within your `Organizer`:

```php
<?php

use \MatheusRosa\PhpInteractor\Organizable;
use \MatheusRosa\PhpInteractor\Context;

class YourOrganizedClazz
{
    use Organizable;
    
    protected function around(Context $context)
    {
        // implement an around logic.
        // You can stop this organizer pipeline
        // by returning false.
    }
    
    protected function before(Context $context)
    {
        // implement a before logic
    }
    
    protected function after(Context $context)
    {
        // implement an after logic
    }
    
    protected function organize()
    {
        return [
            FirstInteractor::class,
            SecondInteractor::class,
            ThirdInteractor::class,
        ];
    }
}
```

### Failure within an Organizer pipeline

By default, an Organizer pipeline flow will immediately stop if any Interactor defined on it fails.
When that happens, each Interactor which had run has a chance to `rollback` its applied changes. This will happen in a reversed order (from the last to the first Interactor):

```php
<?php
use \MatheusRosa\PhpInteractor\Interactable;
use \MatheusRosa\PhpInteractor\Context;

class CreateUser
{
    use Interactable;
    
    public function rollback(Context $context)
    {
        $this->user->destroy();
    }
    
    protected function execute(Context $context)
    {
        if ($context->user->save()) {
            $context->fail('error message');
        }
    }
}
```

### Continue an Organizer flow regardless if an Interactor failed

You can totally replace the default behaviour of your organizer by overriding the `continueOnFailure` method:

```php
<?php

use \MatheusRosa\PhpInteractor\Organizable;
use \MatheusRosa\PhpInteractor\Context;

class YourOrganizedClazz
{
    use Organizable;
    
    protected function continueOnFailure()
    {
        return true;
    }
    
    protected function organize()
    {
        return [
            FirstInteractor::class,
            SecondInteractor::class,
            ThirdInteractor::class,
        ];
    }
}
```

## Examples

If you're still not sure how to use it or how it can become valuable to your engineering team, feel free to check out all examples
under the [examples/](examples) directory. Hopefully some of them can clarify the usage better, with real world examples.
