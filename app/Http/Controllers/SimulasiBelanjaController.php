<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Belanja\PenghitungBelanja;
use App\Http\Requests\SimulasiBelanjaRequest;
use App\Http\Resources\RincianTokoResource;
use App\Services\PenyusunBelanja;
use Illuminate\Http\JsonResponse;

final class SimulasiBelanjaController extends Controller
{
    public function __invoke(
        SimulasiBelanjaRequest $request,
        PenyusunBelanja $penyusunBelanja,
    ): JsonResponse {
        $rincian = $penyusunBelanja->susun($request->butir());

        return response()->json([
            'data' => RincianTokoResource::collection($rincian)->resolve(),
            'total_bayar' => PenghitungBelanja::totalBayar($rincian),
        ]);
    }
}