<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visitors = Auth::user()->visitors()->orderBy('expected_arrival', 'desc')->get();
        return view('visitors.index', compact('visitors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('visitors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'visit_purpose' => 'required|string|max:255',
            'expected_arrival' => 'required|date',
            'vehicle_number' => 'nullable|string|max:20',
        ]);

        $visitor = Auth::user()->visitors()->create([
            'name' => $request->name,
            'visit_purpose' => $request->visit_purpose,
            'expected_arrival' => $request->expected_arrival,
            'vehicle_number' => $request->vehicle_number,
            'pass_code' => strtoupper(Str::random(8)), // Simple unique code
            'status' => 'approved', // Auto-approve for now
        ]);

        return redirect()->route('visitors.show', $visitor)->with('success', 'Visitor registered successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Visitor $visitor)
    {
        // Ensure the user owns this visitor record
        if ($visitor->user_id !== Auth::id()) {
            abort(403);
        }

        return view('visitors.show', compact('visitor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visitor $visitor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visitor $visitor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visitor $visitor)
    {
        if ($visitor->user_id !== Auth::id()) {
            abort(403);
        }

        $visitor->delete();
        return redirect()->route('visitors.index')->with('success', 'Visitor record deleted.');
    }
}
