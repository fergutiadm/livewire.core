<?php

namespace App\Modules\Seguridad\User\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Modules\Seguridad\User\DTOs\DeleteUserDTO;

class DeleteUserAction
{
    public function __invoke(
        DeleteUserDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = User::findOrFail(
                    $dto->id
                );

                /*
                |--------------------------------------------------------------------------
                | Future business rules
                |--------------------------------------------------------------------------
                */

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(UserService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}