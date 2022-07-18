<?php

namespace App\Http\Controllers;

use App\Report;
use Illuminate\Http\Request;


class ReportController extends Controller
{


    public function data()
    {


        return response()->json(Report::all(), 200);

    }


    public function sendReport(Request $request)
    {

        $this->validate($request, [
            'title' => 'required',
            'message' => 'required'
        ]);

        $report = Report::create([
            'title' => request('title'),
            'message' => request('message')

        ]);


        $data = ['status' => 200, 'message' => 'created successfully', 'body' => $report];

        return response()->json($data, 200);

    }


    public function destroy(Report $report)


    {


        if ($report != null) {
            $report->delete();

            $data = ['status' => 200, 'message' => 'successfully removed',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, $data['status']);
    }
}
