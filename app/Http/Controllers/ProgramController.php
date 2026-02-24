<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Program;
use App\Models\ProgramType;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::with(['programType', 'college'])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('program_type_id', $request->type);
        }

        if ($request->filled('college')) {
            $query->where('college_id', $request->college);
        }

        if ($request->filled('format')) {
            $query->where('program_format', $request->format);
        }

        $programs     = $query->orderBy('title')->paginate(12)->withQueryString();
        $programTypes = ProgramType::orderBy('name')->get();
        $colleges     = College::orderBy('name')->get();

        return view('programs.index', compact('programs', 'programTypes', 'colleges'));
    }

    public function show(string $slug)
    {
        $program = Program::with([
            'programType',
            'college',
            'admissionTerms',
            'areasOfInterest',
            'requirements',
            'concentrations',
            'featuredCourses',
            'deadlines.admissionTerm',
            'testimonials',
            'faculty',
            'relatedPrograms.programType',
            'relatedPrograms.college',
        ])->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('programs.show', compact('program'));
    }
}
