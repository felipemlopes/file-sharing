<?php

namespace App\Http\Controllers\File;

use App\File;
use App\FileReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\ReportRequest;
use Auth;

class ReportController extends Controller
{
    public function index()
    {
        abort_if(!Auth::user()->can('view', FileReport::class), 403, trans('http.403'));

        $reports = FileReport::orderBy('created_at', 'ASC')
            ->with('file', 'user')
            ->paginate();

        return view('file.report.index', compact('reports'))
            ->withTitle('File Reports');
    }

    public function submit(ReportRequest $request, $uuid)
    {
        abort_if(!Auth::user()->can('create', FileReport::class), 403, trans('http.403'));

        // check file first
        $file = File::getByUuid($uuid);

        $report = new FileReport;

        if (Auth::check()) {
            $report->user_id = Auth::id();
        } else {
            $report->name = $request->name;
            $report->email = $request->email;
        }

        $report->file_id = $file->id;
        $report->message = $request->message;
        $report->save();

        return redirect()->route('file.view', $file->uuid);
    }
}
