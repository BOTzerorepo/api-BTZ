<?php

namespace App\Services;

use App\Models\cntr as CntrModel;
use App\Models\User;
use App\Repositories\CargaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CargaService
{
    public function __construct(private CargaRepository $repository) {}

    /**
     * Adjunta los contenedores con sus puntos de interés a cada carga.
     */
    private function attachInterestPoints($cargas, string $keyBy = 'cntr_number', string $relation = 'interestPoints'): void
    {
        $cntrs = CntrModel::whereIn('cntr_number', $cargas->pluck('cntr_number'))
            ->with($relation)
            ->get()
            ->keyBy('cntr_number');

        $cargas->each(function ($carga) use ($cntrs) {
            $carga->cntrs = $cntrs->get($carga->cntr_number);
        });
    }

    /**
     * Cargas de esta semana para el usuario dado.
     */
    public function loadThisWeek(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $cargas = $this->repository->getThisWeek($user);
        $this->attachInterestPoints($cargas);

        return $cargas;
    }

    /**
     * Cargas de la semana anterior para el usuario dado.
     */
    public function loadLastWeek(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $cargas = $this->repository->getLastWeek($user);
        $this->attachInterestPoints($cargas);

        return $cargas;
    }

    /**
     * Cargas de la próxima semana para el usuario dado.
     */
    public function loadNextWeek(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $cargas = $this->repository->getNextWeek($user);
        $this->attachInterestPoints($cargas);

        return $cargas;
    }

    /**
     * Cargas asociadas a un número de contenedor específico.
     */
    public function loadForCntr(string $cntrNumber)
    {
        $cargas = $this->repository->getByCntr($cntrNumber);

        $cntrs = CntrModel::whereIn('id_cntr', $cargas->pluck('id_cntr'))
            ->with('interestPointsCntr')
            ->get()
            ->keyBy('cntr_number');

        $cargas->each(function ($carga) use ($cntrs) {
            $carga->cntrs = $cntrs->get($carga->cntr_number);
        });

        return $cargas;
    }
}
