<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function index()
    {
        try {
            $events = CalendarEvent::where('user_id', Auth::id())->get();
            return response()->json(['data' => $events]);
        } catch (\Exception $e) {
            Log::error('Calendar index: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $event = CalendarEvent::create([
                'user_id' => Auth::id(),
                'company_id' => Auth::user()->company_id ?? 1,
                'title' => $request->title,
                'description' => $request->description,
                'type' => 'event',
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'color' => $request->color ?? '#3b82f6',
                'all_day' => $request->all_day ?? false,
            ]);

            return response()->json([
                'data' => $event,
                'message' => 'Event created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Calendar store: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $event = CalendarEvent::find($id);
            return response()->json(['data' => $event]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $event = CalendarEvent::find($id);
            $event->update($request->all());
            return response()->json(['data' => $event]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            CalendarEvent::destroy($id);
            return response()->json(['message' => 'Deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
