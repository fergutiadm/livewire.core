<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ChangeUserPassword extends Command
{
    protected $signature = 'user:change-password
                            {email : Correo electrónico del usuario}';

    protected $description = 'Cambia la contraseña de un usuario por su correo electrónico';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No existe un usuario con el correo: {$email}");

            return self::FAILURE;
        }

        $this->info("Usuario encontrado: {$user->email}");

        $password = $this->secret('Nueva contraseña');

        if (!$password) {
            $this->error('La contraseña no puede estar vacía.');

            return self::FAILURE;
        }

        $confirmation = $this->secret('Confirma la nueva contraseña');

        if ($password !== $confirmation) {
            $this->error('Las contraseñas no coinciden.');

            return self::FAILURE;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info('Contraseña actualizada correctamente.');

        return self::SUCCESS;
    }
}