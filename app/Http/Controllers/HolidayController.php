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

        // Bump de versión de caché para invalidar claves anteriores
        $cacheKey = sprintf('holidays:v3:%s:%d', $country, $year);
        $ttl = now()->addHours(12); // refresco dos veces al día

        $payload = Cache::remember($cacheKey, $ttl, function () use ($year, $country) {
            // Tabla estática por año para CO (complementa API o actúa como fallback)
            $staticByYear = [];
            if ($country === 'CO') {
                $staticByYear[2026] = [
                    '2026-01-01','2026-01-12','2026-03-23','2026-04-02','2026-04-03','2026-05-01','2026-05-18','2026-06-08','2026-06-15','2026-06-29','2026-07-20','2026-08-07','2026-08-17','2026-10-12','2026-11-02','2026-11-16','2026-12-08','2026-12-25'
                ];
            }
            $staticYear = $staticByYear[$year] ?? [];

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
                    // Unir estáticos si aplica
                    if (!empty($staticYear)) {
                        $dates = array_values(array_unique(array_merge($dates, $staticYear)));
                    }
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

            // Añadir al menos feriados de fecha fija (Colombia) para el año solicitado
            $fixedMd = ['01-01','05-01','07-20','08-07','12-08','12-25'];
            $fixedForYear = array_map(fn($md) => sprintf('%04d-%s', $year, $md), $fixedMd);
            $dates = array_values(array_unique(array_merge($cfgDates, $fixedForYear, $staticYear)));
            return [
                'ok' => false,
                'source' => 'fallback:config+fixed',
                'country' => $country,
                'year' => $year,
                'dates' => $dates,
            ];
        });

        return response()->json($payload);
    }
}
