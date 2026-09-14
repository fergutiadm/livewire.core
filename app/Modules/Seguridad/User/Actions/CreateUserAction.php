<?php

namespace App\Modules\Seguridad\User\Actions;

use App\Models\User;
use App\Modules\Seguridad\User\DTOs\CreateUserDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public function __invoke(
        CreateUserDTO $dto
    ): User {
        return DB::transaction(
            function () use ($dto) {

                $model = User::create([
                    'name' => $dto->name,
                    'email' => $dto->email,
                    'password' => Hash::make($dto->password),
                    'movil' => $dto->movil,
                ]);

                $model->assignRole($dto->role);

                return $model;
            }
        );
    }
}