<?php

namespace App\Modules\Seguridad\User\Actions;

use App\Models\User;
use App\Modules\Seguridad\User\DTOs\UpdateUserDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UpdateUserAction
{
    public function __invoke(
        UpdateUserDTO $dto
    ): User {
        return DB::transaction(
            function () use ($dto) {

                $model = User::findOrFail($dto->id);

                $attributes = $dto->attributes();

                if ($dto->password !== null && $dto->password !== '') {
                    $attributes['password'] = Hash::make($dto->password);
                }

                $model->update($attributes);

                $model->syncRoles([
                    $dto->role,
                ]);

                $model->refresh();

                return $model;
            }
        );
    }
}