<?php

namespace App\Modules\Configuracion\SetupConfig\Services;

use App\Models\SetupConfig;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use UnexpectedValueException;

class SetupConfigService
{
    private const CACHE_KEY = 'setup_configs.all';
    private const CACHE_TTL_MINUTES = 60;

    /**
     * Obtener el valor convertido según el tipo de configuración.
     */
    public function get(string $itemId, mixed $default = null): mixed
    {
        $config = $this->getConfig($itemId);

        return $config === null
            ? $default
            : $config['valor'];
    }

    /**
     * Obtener el registro completo, con el valor convertido.
     */
    public function getConfig(string $itemId): ?array
    {
        $config = $this->getRawConfigs()[$itemId] ?? null;

        if ($config === null) {
            return null;
        }

        $config['valor'] = $this->convertirValor(
            (int) $config['tipo'],
            (string) $config['valor']
        );

        return $config;
    }

    /**
     * Obtener todas las configuraciones indexadas por item_id.
     */
    public function all(): array
    {
        $configs = [];

        foreach ($this->getRawConfigs() as $itemId => $config) {
            $config['valor'] = $this->convertirValor(
                (int) $config['tipo'],
                (string) $config['valor']
            );

            $configs[$itemId] = $config;
        }

        return $configs;
    }

    /**
     * Obtener un valor entero.
     */
    public function getInt(
        string $itemId,
        ?int $default = null
    ): ?int {
        $value = $this->get($itemId, $default);

        if ($value === $default && !is_string($value)) {
            return $default;
        }

        if (is_int($value)) {
            return $value;
        }

        if (
            is_string($value)
            && filter_var($value, FILTER_VALIDATE_INT) !== false
        ) {
            return (int) $value;
        }

        throw new UnexpectedValueException(
            "La configuración [{$itemId}] no contiene un entero válido."
        );
    }

    /**
     * Obtener un decimal como cadena para preservar su precisión.
     */
    public function getDecimal(
        string $itemId,
        ?string $default = null
    ): ?string {
        $value = $this->get($itemId, $default);

        if ($value === $default && !is_string($value)) {
            return $default;
        }

        if (!is_numeric($value)) {
            throw new UnexpectedValueException(
                "La configuración [{$itemId}] no contiene un número válido."
            );
        }

        return (string) $value;
    }

    /**
     * Invalidar la caché después de modificar configuraciones.
     */
    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Cargar todos los registros una sola vez por periodo de caché.
     */
    private function getRawConfigs(): array
    {
        return Cache::remember(
            self::CACHE_KEY,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn () => SetupConfig::query()
                ->get([
                    'id',
                    'nombre',
                    'item_id',
                    'descripcion',
                    'tipo',
                    'valor',
                    'valor_inicial',
                    'valor_minimo',
                    'valor_maximo',
                ])
                ->keyBy('item_id')
                ->map(fn (SetupConfig $config) => $config->toArray())
                ->all()
        );
    }

    /**
     * Convertir el valor de acuerdo con su tipo.
     */
    private function convertirValor(int $tipo, string $valor): mixed
    {
        return match ($tipo) {
            SetupConfig::TIPO_NUMERO => $this->convertirNumero($valor),

            SetupConfig::TIPO_TEXTO => $valor,

            SetupConfig::TIPO_ARREGLO => array_values(
                array_filter(
                    array_map('trim', explode('|', $valor)),
                    static fn ($item) => $item !== ''
                )
            ),

            default => throw new InvalidArgumentException(
                "Tipo de configuración no válido: {$tipo}."
            ),
        };
    }

    /**
     * Los enteros se devuelven como int.
     * Los decimales permanecen como cadenas para no perder precisión.
     */
    private function convertirNumero(string $valor): int|string
    {
        $valor = trim($valor);

        if (filter_var($valor, FILTER_VALIDATE_INT) !== false) {
            return (int) $valor;
        }

        if (is_numeric($valor)) {
            return $valor;
        }

        throw new UnexpectedValueException(
            "El valor [{$valor}] no es numérico."
        );
    }
}
