<?php

require_once __DIR__.'/vendor/autoload.php';

use MatheusRosa\PhpInteractor\{Context, Interactable};

class User
{
    private $username;
    private $email;

    public function __construct($username, $email)
    {
        $this->username = $username;
        $this->email = $email;
    }

    public function save()
    {
        return false;
    }
}

class BuildUser
{
    use Interactable;

    public function rollback(Context $context)
    {
        $context->xpto = $context->xpto.' rollback!';
    }

    public function before(Context $context)
    {
        var_dump('Im the build user before');
        $context->time = time();
        $context->xpto = 'builduser';
    }

    protected function execute(Context $context)
    {
        $context->user = new User($context->username, $context->email);
    }
}

class SaveUser
{
    use Interactable;

    protected function execute(Context $context)
    {
        if (!$context->user->save()) {
            $context->fail('failed to save user');
        }
    }
}

class CreateUser
{
    use \MatheusRosa\PhpInteractor\Organizable;

    protected $continueOnFailure = true;

    protected function around(Context $context)
    {
        var_dump('around of organizer!');
    }

    public function organize()
    {
        return [
            BuildUser::class,
            SaveUser::class,
        ];
    }
}

//$context = CreateUser::call([
//   'email' => 'matheus.alves.rosa@outlook.com',
//   'username' => 'matheus.rosa',
//]);
//
//
//var_dump($context->success(), $context);

try {
    $context2 = CreateUser::call([
        'email' => 'matheus.alves.rosa@outlook.com',
        'username' => 'matheus.rosa',
    ]);

    var_dump($context2);
} catch (Exception $e) {
}
