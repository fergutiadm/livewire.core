<?php

namespace App\Modules\Configuracion\SetupConfig\Actions;

use App\Models\SetupConfig;
use App\Modules\Configuracion\SetupConfig\DTOs\CreateSetupConfigDTO;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Support\Facades\DB;

class CreateSetupConfigAction
{
    public function __construct(
        private SetupConfigService $setupConfigService
    ) {}

    public function __invoke(CreateSetupConfigDTO $dto): SetupConfig
    {
        $config = DB::transaction(
            fn () => SetupConfig::create($dto->toArray())
        );

        $this->setupConfigService->forgetCache();

        return $config;
    }
}
