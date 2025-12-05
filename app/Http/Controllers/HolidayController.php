<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use DateTime;

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
        $cacheKey = sprintf('holidays:v4:%s:%d', $country, $year);
        $ttl = now()->addHours(12); // refresco dos veces al día

        $payload = Cache::remember($cacheKey, $ttl, function () use ($year, $country) {
            $auto = ($country === 'CO') ? $this->computeColombiaHolidays($year) : [];

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
                    $dates = array_values(array_unique(array_merge($dates, $auto)));
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
            $dates = array_values(array_unique(array_merge($cfgDates, $fixedForYear, $auto)));
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

    private function computeColombiaHolidays(int $year): array
    {
        $dates = [];

        $dates[] = sprintf('%04d-01-01', $year);
        $dates[] = sprintf('%04d-05-01', $year);
        $dates[] = sprintf('%04d-07-20', $year);
        $dates[] = sprintf('%04d-08-07', $year);
        $dates[] = sprintf('%04d-12-08', $year);
        $dates[] = sprintf('%04d-12-25', $year);

        $dates[] = $this->moveToMonday(new DateTime(sprintf('%04d-01-06', $year)))->format('Y-m-d');
        $dates[] = $this->moveToMonday(new DateTime(sprintf('%04d-03-19', $year)))->format('Y-m-d');
        $dates[] = $this->moveToMonday(new DateTime(sprintf('%04d-06-29', $year)))->format('Y-m-d');
        $dates[] = $this->moveToMonday(new DateTime(sprintf('%04d-08-15', $year)))->format('Y-m-d');
        $dates[] = $this->moveToMonday(new DateTime(sprintf('%04d-10-12', $year)))->format('Y-m-d');
        $dates[] = $this->moveToMonday(new DateTime(sprintf('%04d-11-01', $year)))->format('Y-m-d');
        $dates[] = $this->moveToMonday(new DateTime(sprintf('%04d-11-11', $year)))->format('Y-m-d');

        $easter = $this->easterDate($year);
        $dates[] = (clone $easter)->modify('-3 days')->format('Y-m-d');
        $dates[] = (clone $easter)->modify('-2 days')->format('Y-m-d');

        $dates[] = $this->moveToMonday((clone $easter)->modify('+39 days'))->format('Y-m-d');
        $dates[] = $this->moveToMonday((clone $easter)->modify('+60 days'))->format('Y-m-d');
        $dates[] = $this->moveToMonday((clone $easter)->modify('+68 days'))->format('Y-m-d');

        return array_values(array_unique($dates));
    }

    private function moveToMonday(DateTime $date): DateTime
    {
        $d = clone $date;
        if ((int)$d->format('N') === 1) return $d;
        $d->modify('next monday');
        return $d;
    }

    private function easterDate(int $year): DateTime
    {
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $n = intdiv($h + $l - 7 * $m + 114, 31);
        $p = ($h + $l - 7 * $m + 114) % 31;
        $month = $n; // 3=Marzo, 4=Abril
        $day = $p + 1;
        return new DateTime(sprintf('%04d-%02d-%02d', $year, $month, $day));
    }
}
