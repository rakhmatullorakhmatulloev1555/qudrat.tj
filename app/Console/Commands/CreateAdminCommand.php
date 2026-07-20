<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Создать администратора панели управления';

    public function handle(): void
    {
        $this->info('=== Создание администратора QUDRAT CRM ===');
        $this->newLine();

        $name = $this->ask('Имя администратора');

        $email = $this->ask('Email');
        while (User::where('email', $email)->exists()) {
            $this->error("Email {$email} уже используется.");
            $email = $this->ask('Введите другой email');
        }

        $password = $this->secret('Пароль (минимум 8 символов)');
        while (strlen($password) < 8) {
            $this->error('Пароль должен быть не менее 8 символов.');
            $password = $this->secret('Пароль');
        }

        $phone = $this->ask('Телефон (необязательно)', null);

        $user = User::create([
            'name'      => $name,
            'email'     => $email,
            'password'  => Hash::make($password),
            'role'      => 'admin',
            'phone'     => $phone,
            'is_active' => true,
        ]);

        $this->newLine();
        $this->info('✅ Администратор создан успешно!');
        $this->table(
            ['Поле', 'Значение'],
            [
                ['ID',    $user->id],
                ['Имя',   $user->name],
                ['Email', $user->email],
                ['Роль',  $user->role],
            ]
        );
        $this->newLine();
        $this->line('Войдите по адресу: <info>/admin/login</info>');
    }
}
