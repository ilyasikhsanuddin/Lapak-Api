<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SimulasiBelanjaRequest;
use App\Http\Resources\RincianTokoResource;
use App\Services\PenyusunBelanja;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class SimulasiBelanjaController extends Controller
{
    public function __invoke(
        SimulasiBelanjaRequest $request,
        PenyusunBelanja $penyusunBelanja,
    ): AnonymousResourceCollection|JsonResponse {
        $rincian = $penyusunBelanja->susun($request->butir());

        return RincianTokoResource::collection($rincian);
    }
}