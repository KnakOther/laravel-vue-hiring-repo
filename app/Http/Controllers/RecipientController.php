<?php

namespace App\Http\Controllers;

use App\Models\Recipient;
use Illuminate\Http\Request;

class RecipientController extends Controller
{
    public function index()
    {
        $recipients = Recipient::all();

        return response()->json($recipients);
    }

    public function store(Request $request)
    {
        $recipient = Recipient::create($request->all());

        return response()->json($recipient);
    }

    public function show($id)
    {
        $recipient = Recipient::find($id);

        return response()->json($recipient);
    }

    public function update(Request $request, $id)
    {
        $recipient = Recipient::find($id);
        $recipient->update($request->all());

        return response()->json($recipient);
    }

    public function destroy($id)
    {
        $recipient = Recipient::find($id);
        $recipient->delete();

        return response()->json(['message' => 'Recipient deleted']);
    }

    public function bulk(Request $request)
    {
        $recipients = [];
        $csv = array_map('str_getcsv', file(storage_path('Final.csv')));

        foreach ($csv as $key => $row) {
            if ($key === 0) {
                continue;
            }
            // if department is null skip
            if ($row[6] == null || $row[3] == null) {
                continue;
            }

            //split name into first and last
            $name = explode(' ', $row[1]);

            $recipients[] = Recipient::upsert([
                'bamboo_id' => $row[0],
                'email' => $row[6],
                'first_name' => $name[0],
                'last_name' => $name[1],
                'department' => $row[3],
                'position' => $row[4],
                'location' => $row[8],
                'supervisor_id' => $row[2],
                'avatar_url' => $row[7],
            ],uniqueBy: ['email']);
        }

        return response()->json($recipients);
    }

    public function byFilter(Request $request)
    {
        $filterType = $request->filterType;
        $filterValue = $request->filterValue;

        if ($filterType === 'user') {
            $recipients = Recipient::whereIn('id', $filterValue)->get();
        } else {
            $recipients = Recipient::whereIn($filterType, $filterValue)->get();
        }

        return response()->json(['recipients' => $recipients]);
    }

}
