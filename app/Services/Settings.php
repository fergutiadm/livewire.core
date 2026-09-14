<?php
namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    protected string $cacheKey = 'app.settings.all';

    public function all(): array
    {
        return Cache::rememberForever($this->cacheKey, function () {
            // devolvemos ['inventory.totals.render_type' => ['raw'=>'simple','type'=>'string']]
            return Setting::query()->get()
                ->mapWithKeys(fn($s) => [
                    $s->key => ['raw' => $s->value, 'type' => $s->type]
                ])->toArray();
        });
    }

    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();
        if (! array_key_exists($key, $all)) return $default;

        $entry = $all[$key];
        $type  = $entry['type'] ?? 'string';
        $raw   = $entry['raw'];

        // normalizamos: en DB value es JSON; para tipos simples guardamos {"v":<valor>}
        $val = is_array($raw) && array_key_exists('v', $raw) ? $raw['v'] : $raw;

        return match ($type) {
            'bool', 'boolean'  => (bool) $val,
            'int',  'integer'  => (int) $val,
            'float','double'   => (float) $val,
            'json', 'array'    => (array) $val,
            default            => (string) $val,
        };
    }

    public function set(string $key, mixed $value, string $type = 'string', ?string $group = null, bool $autoload = true): void
    {
        // guardamos como {"v":<valor>} para tipos simples y como array para 'json'
        $payload = in_array($type, ['json','array']) ? (array) $value : ['v' => $value];

        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $payload, 'type' => $type, 'group' => $group, 'autoload' => $autoload]
        );

        $this->clearCache();
    }
}
