<?php

namespace App\Http\Controllers;

use App\Models\RfiSubmission;
use Illuminate\Http\Request;

class RfiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'phone_number'      => 'nullable|string|max:50',
            'admission_term_id' => 'nullable|exists:admission_terms,id',
            'program_id'        => 'required|exists:programs,id',
        ]);

        RfiSubmission::create($validated);

        return redirect()->back()->with('rfi_success', true);
    }
}
