<?php

namespace App\Http\Controllers;

use App\Models\ClienteComercial;
use App\Models\Diagnostico;
use App\Traits\ApiResponse;

class IntelController extends Controller
{
    use ApiResponse;

    public function stats()
    {
        try {
            $user = auth('api')->user();
            $isAdmin = $user->hasRole('AdminComercial');

            $clientesQuery = ClienteComercial::query();
            if (!$isAdmin) {
                $clientesQuery->where('empresa', $user->empresa);
            }
            $totalClientes = $clientesQuery->count();

            $diagQuery = Diagnostico::query();
            if (!$isAdmin) {
                $diagQuery->whereHas('cliente', function ($q) use ($user) {
                    $q->where('empresa', $user->empresa);
                });
            }

            $totalConDiagnostico = (clone $diagQuery)->distinct('cliente_id')->count('cliente_id');

            $diagnosticos = (clone $diagQuery)->with('cliente')
                ->get(['id', 'cliente_id', 'uso_frecuencia', 'valores_encontrados', 'barreras', 'funcionalidades_pedidas']);

            $usoMap = [0 => 'nunca', 1 => 'poco', 2 => 'frecuente', 3 => 'diario'];
            $uso = ['nunca' => 0, 'poco' => 0, 'frecuente' => 0, 'diario' => 0];

            $valoresCount = [];
            $barrerasCount = [];
            $roadmapCount = [];

            foreach ($diagnosticos as $d) {
                if (!is_null($d->uso_frecuencia) && isset($usoMap[$d->uso_frecuencia])) {
                    $uso[$usoMap[$d->uso_frecuencia]]++;
                }

                foreach (($d->valores_encontrados ?? []) as $v) {
                    $valoresCount[$v] = ($valoresCount[$v] ?? 0) + 1;
                }
                foreach (($d->barreras ?? []) as $b) {
                    $barrerasCount[$b] = ($barrerasCount[$b] ?? 0) + 1;
                }
                foreach (($d->funcionalidades_pedidas ?? []) as $f) {
                    $roadmapCount[$f] = ($roadmapCount[$f] ?? 0) + 1;
                }
            }

            $totalDiag = $diagnosticos->count();

            $toRanking = function (array $counts, string $keyName, bool $withPct) use ($totalDiag) {
                return collect($counts)
                    ->map(function ($count, $key) use ($keyName, $withPct, $totalDiag) {
                        $row = [$keyName => $key, 'count' => $count];
                        if ($withPct) {
                            $row['pct'] = $totalDiag > 0 ? round($count / $totalDiag * 100, 1) : 0;
                        }
                        return $row;
                    })
                    ->values()
                    ->sortByDesc('count')
                    ->values();
            };

            $valores = $toRanking($valoresCount, 'feature', true);
            $barreras = $toRanking($barrerasCount, 'barrera', true);
            $roadmap = $toRanking($roadmapCount, 'funcionalidad', false);

            $healthPorSegmento = [];
            foreach (['A', 'B', 'C'] as $segmento) {
                $segDiags = $diagnosticos->filter(fn ($d) => ($d->cliente->segmento ?? null) === $segmento);
                $totalSeg = $segDiags->count();
                $sanosSeg = $segDiags->filter(fn ($d) => ($d->uso_frecuencia ?? 0) >= 2)->count();
                $healthPorSegmento[$segmento] = $totalSeg > 0 ? round($sanosSeg / $totalSeg * 100) : 0;
            }

            return $this->success([
                'total_clientes'        => $totalClientes,
                'total_con_diagnostico' => $totalConDiagnostico,
                'uso'                   => $uso,
                'valores'               => $valores,
                'barreras'              => $barreras,
                'roadmap'               => $roadmap,
                'health_por_segmento'   => $healthPorSegmento,
            ]);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }
}
