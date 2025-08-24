<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanRequest;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index()
    {
        return Plan::orderBy('id')->get();
    }

    public function store(PlanRequest $r)
    {
        return Plan::create($r->validated());
    }

    public function show(Plan $plan)
    {
        return $plan;
    }

    public function update(PlanRequest $r, Plan $plan)
    {
        $plan->update($r->validated());
        return $plan;
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json(['deleted' => true]);
    }
}

