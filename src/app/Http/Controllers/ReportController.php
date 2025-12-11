<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['post', 'user'])->latest()->paginate(20);
        return response()->json($reports);
    }

    public function store(ReportRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $report = Report::create($data);
        return response()->json($report, 201);
    }

    public function update(ReportRequest $request, Report $report)
    {
        $report->update($request->validated());
        return response()->json($report);
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return response()->json(null, 204);
    }
}
