<?php

namespace App\Modules\Configuracion\SetupConfig\Actions;

use App\Models\SetupConfig;
use App\Modules\Configuracion\SetupConfig\DTOs\DeleteSetupConfigDTO;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Support\Facades\DB;

class DeleteSetupConfigAction
{
    public function __construct(
        private SetupConfigService $setupConfigService
    ) {}

    public function __invoke(DeleteSetupConfigDTO $dto): bool
    {
        $deleted = DB::transaction(function () use ($dto) {
            $config = SetupConfig::findOrFail($dto->id);

            return (bool) $config->delete();
        });

        if ($deleted) {
            $this->setupConfigService->forgetCache();
        }

        return $deleted;
    }
}
