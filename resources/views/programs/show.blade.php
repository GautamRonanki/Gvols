@extends('layouts.frontend')

@section('title', $program->title . ' — Gvols University')
@section('description', strip_tags(Str::limit($program->overview, 160)))

@php use Illuminate\Support\Str; @endphp

@section('content')

    {{-- Hero banner --}}
    <div class="relative bg-gray-900 text-white overflow-hidden">
        @if($program->featured_image)
            <img
                src="{{ Storage::disk('public')->url($program->featured_image) }}"
                alt="{{ $program->title }}"
                class="absolute inset-0 w-full h-full object-cover opacity-30"
            >
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-blue-800 to-indigo-900 opacity-80"></div>
        @endif

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-blue-300 mb-6">
                <a href="{{ route('programs.index') }}" class="hover:text-white transition-colors">Programs</a>
                <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-white">{{ $program->title }}</span>
            </nav>

            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-600 text-white">{{ $program->programType->name }}</span>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-white/10 text-white">
                    {{ ucfirst($program->program_format) }}
                </span>
                @foreach($program->areasOfInterest as $area)
                    <span class="text-xs px-2 py-1 rounded-full bg-white/10 text-blue-200">{{ $area->name }}</span>
                @endforeach
            </div>

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-3">
                {{ $program->title }}
            </h1>
            @if($program->degree_coursework_name || $program->program_major)
                <p class="text-blue-200 text-lg mb-2">
                    {{ $program->degree_coursework_name }}@if($program->degree_coursework_name && $program->program_major) — @endif{{ $program->program_major }}
                </p>
            @endif
            <p class="text-blue-300 text-sm">{{ $program->college->name }}</p>
        </div>
    </div>

    {{-- Main layout: content + sidebar --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-10">

            {{-- LEFT: Main content --}}
            <div class="flex-1 min-w-0 space-y-12">

                {{-- Overview --}}
                @if($program->overview)
                <section id="overview">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 rounded-full bg-blue-600 inline-block"></span>
                        Program Overview
                    </h2>
                    <div class="prose prose-gray max-w-none text-gray-700 leading-relaxed">
                        {!! $program->overview !!}
                    </div>
                </section>
                @endif

                {{-- Concentrations --}}
                @if($program->concentrations->isNotEmpty())
                <section id="concentrations">
                    <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-1 h-6 rounded-full bg-blue-600 inline-block"></span>
                        Concentrations
                    </h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($program->concentrations as $conc)
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                            @if($conc->image)
                                <img src="{{ Storage::disk('public')->url($conc->image) }}" alt="{{ $conc->name }}" class="w-full h-32 object-cover rounded-lg mb-3">
                            @endif
                            <h3 class="font-bold text-gray-900 mb-1">{{ $conc->name }}</h3>
                            @if($conc->description)
                                <p class="text-sm text-gray-600">{{ $conc->description }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- Featured Courses --}}
                @if($program->featuredCourses->isNotEmpty())
                <section id="courses">
                    <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-1 h-6 rounded-full bg-blue-600 inline-block"></span>
                        Featured Courses
                    </h2>
                    <div class="space-y-4">
                        @foreach($program->featuredCourses as $course)
                        <div class="flex gap-4 p-4 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            @if($course->image)
                                <img src="{{ Storage::disk('public')->url($course->image) }}" alt="{{ $course->title }}" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-indigo-100 flex-shrink-0 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-0.5">{{ $course->title }}</h3>
                                @if($course->description)
                                    <p class="text-sm text-gray-600">{{ $course->description }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- Requirements --}}
                @if($program->requirements->isNotEmpty())
                <section id="requirements">
                    <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-1 h-6 rounded-full bg-blue-600 inline-block"></span>
                        Admission Requirements
                    </h2>
                    <ul class="space-y-3">
                        @foreach($program->requirements as $req)
                        <li class="flex gap-3">
                            <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-3 h-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            <span class="text-gray-700 text-sm leading-relaxed">{{ $req->requirement }}</span>
                        </li>
                        @endforeach
                    </ul>
                </section>
                @endif

                {{-- Faculty --}}
                @if($program->faculty->isNotEmpty())
                <section id="faculty">
                    <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-1 h-6 rounded-full bg-blue-600 inline-block"></span>
                        Featured Faculty
                    </h2>
                    <div class="grid sm:grid-cols-2 gap-5">
                        @foreach($program->faculty as $member)
                        <div class="flex gap-4 p-5 bg-white border border-gray-100 rounded-xl shadow-sm">
                            @if($member->photo)
                                <img src="{{ Storage::disk('public')->url($member->photo) }}" alt="{{ $member->name }}" class="w-16 h-16 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-full bg-gray-100 flex-shrink-0 flex items-center justify-center">
                                    <span class="text-lg font-bold text-gray-400">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="font-bold text-gray-900 text-sm leading-tight">{{ $member->name }}</h3>
                                @if($member->department)
                                    <p class="text-xs text-blue-600 mt-0.5">{{ $member->department }}</p>
                                @endif
                                @if($member->courses_taught)
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span class="font-medium">Teaches:</span> {{ $member->courses_taught }}
                                    </p>
                                @endif
                                @if($member->description)
                                    <p class="text-xs text-gray-600 mt-2 leading-relaxed">{{ Str::limit($member->description, 160) }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- Testimonials --}}
                @if($program->testimonials->isNotEmpty())
                <section id="testimonials">
                    <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-1 h-6 rounded-full bg-blue-600 inline-block"></span>
                        Student Stories
                    </h2>
                    <div class="space-y-5">
                        @foreach($program->testimonials as $t)
                        <blockquote class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6">
                            <svg class="w-8 h-8 text-blue-300 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                            </svg>
                            <p class="text-gray-700 text-sm leading-relaxed italic mb-4">{{ $t->testimonial }}</p>
                            <footer class="flex items-center gap-3">
                                @if($t->image)
                                    <img src="{{ Storage::disk('public')->url($t->image) }}" alt="{{ $t->student_name }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-blue-700">{{ strtoupper(substr($t->student_name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $t->student_name }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if($t->program_taken){{ $t->program_taken }}@endif
                                        @if($t->graduation_year) · Class of {{ $t->graduation_year }}@endif
                                    </p>
                                </div>
                            </footer>
                        </blockquote>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- Related Programs --}}
                @if($program->relatedPrograms->isNotEmpty())
                <section id="related">
                    <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-1 h-6 rounded-full bg-blue-600 inline-block"></span>
                        Related Programs
                    </h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($program->relatedPrograms as $related)
                        <a href="{{ route('programs.show', $related->slug) }}"
                           class="group flex gap-3 items-center p-4 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md hover:border-blue-200 transition-all">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex-shrink-0 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm group-hover:text-blue-700 transition-colors">{{ $related->title }}</p>
                                <p class="text-xs text-gray-500">{{ $related->college->name }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 ml-auto group-hover:text-blue-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- Request for Information Form --}}
                <section id="rfi" class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100 rounded-2xl p-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <span class="w-1 h-6 rounded-full bg-indigo-600 inline-block"></span>
                            Request for Information
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Fill out the form below and we'll send you more details about this program.</p>
                    </div>

                    @if(session('rfi_success'))
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg">Thank you!</h3>
                            <p class="text-gray-500 text-sm mt-1">Your request has been received. We'll be in touch soon.</p>
                        </div>
                    @else
                        <form action="{{ route('rfi.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="program_id" value="{{ $program->id }}">

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="rfi_full_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="rfi_full_name"
                                        name="full_name"
                                        value="{{ old('full_name') }}"
                                        required
                                        placeholder="Jane Smith"
                                        class="w-full text-sm border {{ $errors->has('full_name') ? 'border-red-400' : 'border-gray-200' }} rounded-xl px-4 py-2.5 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                    >
                                    @error('full_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="rfi_email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        id="rfi_email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        placeholder="jane@example.com"
                                        class="w-full text-sm border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }} rounded-xl px-4 py-2.5 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                    >
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="rfi_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Phone Number
                                    </label>
                                    <input
                                        type="tel"
                                        id="rfi_phone"
                                        name="phone_number"
                                        value="{{ old('phone_number') }}"
                                        placeholder="+1 (555) 000-0000"
                                        class="w-full text-sm border {{ $errors->has('phone_number') ? 'border-red-400' : 'border-gray-200' }} rounded-xl px-4 py-2.5 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                    >
                                    @error('phone_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="rfi_term" class="block text-sm font-medium text-gray-700 mb-1">
                                        When do you want to start?
                                    </label>
                                    <select
                                        id="rfi_term"
                                        name="admission_term_id"
                                        class="w-full text-sm border {{ $errors->has('admission_term_id') ? 'border-red-400' : 'border-gray-200' }} rounded-xl px-4 py-2.5 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                    >
                                        <option value="">Select a term...</option>
                                        @foreach($admissionTerms as $term)
                                            <option value="{{ $term->id }}" {{ old('admission_term_id') == $term->id ? 'selected' : '' }}>
                                                {{ $term->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('admission_term_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Program Interest
                                    </label>
                                    <input
                                        type="text"
                                        value="{{ $program->title }}"
                                        disabled
                                        class="w-full text-sm border border-gray-100 rounded-xl px-4 py-2.5 bg-gray-50 text-gray-500 cursor-not-allowed"
                                    >
                                </div>
                            </div>

                            <div class="mt-5">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Request Information
                                </button>
                            </div>
                        </form>
                    @endif
                </section>

            </div>

            {{-- RIGHT: Sticky sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0">
                <div class="sticky top-24 space-y-5">

                    {{-- Quick facts card --}}
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                        <div class="bg-blue-600 px-5 py-4">
                            <h3 class="text-white font-bold text-sm">Program Details</h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <div class="flex items-center gap-3 px-5 py-3.5">
                                <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 10V5a2 2 0 012-2z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-400">Type</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $program->programType->name }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 px-5 py-3.5">
                                <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-400">College</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $program->college->name }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 px-5 py-3.5">
                                <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-400">Format</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ ucfirst($program->program_format) }}</p>
                                </div>
                            </div>

                            @if($program->duration)
                            <div class="flex items-center gap-3 px-5 py-3.5">
                                <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-400">Duration</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $program->duration }}</p>
                                </div>
                            </div>
                            @endif

                            @if($program->credit_hours)
                            <div class="flex items-center gap-3 px-5 py-3.5">
                                <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-400">Credit Hours</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $program->credit_hours }} credits</p>
                                </div>
                            </div>
                            @endif

                            @if($program->program_fees)
                            <div class="flex items-center gap-3 px-5 py-3.5">
                                <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-400">Program Fees</p>
                                    <p class="text-sm font-semibold text-gray-800">${{ number_format($program->program_fees, 0) }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Admission Terms + Deadlines --}}
                    @if($program->admissionTerms->isNotEmpty())
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                        <div class="bg-green-600 px-5 py-4">
                            <h3 class="text-white font-bold text-sm">Admission Terms & Deadlines</h3>
                        </div>
                        <div class="p-5 space-y-3">
                            @foreach($program->admissionTerms as $term)
                                @php
                                    $deadline = $program->deadlines->firstWhere('admission_term_id', $term->id);
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">{{ $term->name }}</span>
                                    @if($deadline)
                                        <span class="text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-lg">
                                            {{ $deadline->deadline_date->format('M j, Y') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Rolling</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Areas of Interest tags --}}
                    @if($program->areasOfInterest->isNotEmpty())
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                        <h3 class="text-sm font-bold text-gray-800 mb-3">Areas of Interest</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($program->areasOfInterest as $area)
                                <span class="text-xs px-3 py-1 bg-blue-50 text-blue-700 rounded-full font-medium">{{ $area->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- CTA --}}
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white text-center">
                        <h3 class="font-bold text-base mb-1">Ready to Apply?</h3>
                        <p class="text-blue-200 text-xs mb-4">Take the next step in your academic journey.</p>
                        <a href="#" class="block w-full bg-white text-blue-700 font-bold text-sm py-2.5 rounded-xl hover:bg-blue-50 transition-colors">
                            Apply Now
                        </a>
                        <a href="{{ route('programs.index') }}" class="mt-2 block text-xs text-blue-300 hover:text-white transition-colors">
                            ← Back to all programs
                        </a>
                    </div>

                </div>
            </aside>

        </div>
    </div>

@endsection
