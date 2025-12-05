<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HolidayController extends Controller
{
    /**
     * GET /api/holidays/{year?}?country=CO
     * Proxy con caché a la API pública de feriados (Nager.Date) y devuelve
     * un JSON compacto con las fechas en formato YYYY-MM-DD.
     */
    public function index(Request $request, ?int $year = null)
    {
        $year = $year ?: now()->year;
        $country = strtoupper((string) $request->query('country', 'CO'));

        $cacheKey = sprintf('holidays:%s:%d', $country, $year);
        $ttl = now()->addHours(12); // refresco dos veces al día

        $payload = Cache::remember($cacheKey, $ttl, function () use ($year, $country) {
            try {
                $url = sprintf('https://date.nager.at/api/v3/PublicHolidays/%d/%s', $year, $country);
                $res = Http::timeout(10)->acceptJson()->get($url);
                if ($res->ok()) {
                    $arr = $res->json();
                    $dates = collect($arr)
                        ->pluck('date')
                        ->filter(fn ($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d))
                        ->values()
                        ->all();
                    return [
                        'ok' => true,
                        'source' => 'nager.at',
                        'country' => $country,
                        'year' => $year,
                        'dates' => $dates,
                    ];
                }
            } catch (\Throwable $e) {
                // cae al fallback
            }

            // Fallback: si falla la API, devolvemos lo configurado (si existe)
            $cfg = config('app.feriados', []);
            $cfgDates = is_array($cfg) ? array_values($cfg) : [];
            return [
                'ok' => false,
                'source' => 'fallback:config(app.feriados)',
                'country' => $country,
                'year' => $year,
                'dates' => $cfgDates,
            ];
        });

        return response()->json($payload);
    }
}
