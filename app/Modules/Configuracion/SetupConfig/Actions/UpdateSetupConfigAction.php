<?php

namespace App\Modules\Configuracion\SetupConfig\Actions;

use App\Models\SetupConfig;
use App\Modules\Configuracion\SetupConfig\DTOs\UpdateSetupConfigDTO;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Support\Facades\DB;

class UpdateSetupConfigAction
{
    public function __construct(
        private SetupConfigService $setupConfigService
    ) {}

    public function __invoke(UpdateSetupConfigDTO $dto): SetupConfig
    {
        $config = DB::transaction(function () use ($dto) {
            $config = SetupConfig::findOrFail($dto->id);

            $config->update($dto->attributes());

            return $config->refresh();
        });

        $this->setupConfigService->forgetCache();

        return $config;
    }
}
