<?php

namespace App\Modules\Usuarios\Actions;

use App\Models\User;
use App\Modules\Usuarios\Usuario\DTOs\ChangePasswordUsuarioDTO;
use Illuminate\Support\Facades\Hash;

class ChangePasswordUsuarioAction
{
    public function __invoke(
        ChangePasswordUsuarioDTO $dto
    ): User {

        $user = User::findOrFail($dto->userId);

        $user->password = Hash::make($dto->password);

        $user->save();

        return $user;
    }
}