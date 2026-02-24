@extends('layouts.frontend')

@section('title', 'Explore Programs — Gvols University')
@section('description', 'Browse all graduate, undergraduate, and certificate programs.')

@section('content')

    {{-- Hero --}}
    <section class="bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 text-white py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <p class="text-blue-200 text-sm font-semibold tracking-widest uppercase mb-3">Gvols University</p>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                Find Your Program
            </h1>
            <p class="text-blue-100 text-lg max-w-xl mx-auto mb-8">
                Explore degrees, certificates, and continuing education options designed for working professionals.
            </p>

            {{-- Search bar --}}
            <form method="GET" action="{{ route('programs.index') }}" class="flex items-center bg-white rounded-xl shadow-lg overflow-hidden max-w-lg mx-auto">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search programs..."
                    class="flex-1 px-5 py-3.5 text-gray-900 text-sm outline-none"
                >
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3.5 text-sm font-medium transition-colors">
                    Search
                </button>
            </form>
        </div>
    </section>

    {{-- Filters + Grid --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Filter bar --}}
        <form method="GET" action="{{ route('programs.index') }}" class="flex flex-wrap gap-3 mb-8 items-end">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Program Type</label>
                <select name="type" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($programTypes as $type)
                        <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">College</label>
                <select name="college" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Colleges</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" {{ request('college') == $college->id ? 'selected' : '' }}>
                            {{ $college->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Format</label>
                <select name="format" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Formats</option>
                    <option value="asynchronous" {{ request('format') === 'asynchronous' ? 'selected' : '' }}>Asynchronous</option>
                    <option value="synchronous" {{ request('format') === 'synchronous' ? 'selected' : '' }}>Synchronous</option>
                    <option value="mixed" {{ request('format') === 'mixed' ? 'selected' : '' }}>Mixed</option>
                    <option value="hybrid" {{ request('format') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
            </div>

            @if(request('search') || request('type') || request('college') || request('format'))
                <a href="{{ route('programs.index') }}" class="self-end text-sm text-gray-500 hover:text-red-500 flex items-center gap-1 px-3 py-2 border border-gray-200 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
            @endif

            <span class="self-end ml-auto text-sm text-gray-400">{{ $programs->total() }} program{{ $programs->total() !== 1 ? 's' : '' }}</span>
        </form>

        {{-- No results --}}
        @if($programs->isEmpty())
            <div class="text-center py-24">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 font-medium">No programs found.</p>
                <a href="{{ route('programs.index') }}" class="mt-3 inline-block text-blue-600 text-sm hover:underline">Clear filters</a>
            </div>
        @else

        {{-- Program cards grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($programs as $program)
            <a href="{{ route('programs.show', $program->slug) }}"
               class="group bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col">

                {{-- Card image --}}
                <div class="relative h-44 bg-gradient-to-br from-blue-50 to-indigo-100 overflow-hidden">
                    @if($program->featured_image)
                        <img
                            src="{{ Storage::disk('public')->url($program->featured_image) }}"
                            alt="{{ $program->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                    @else
                        {{-- Placeholder with initials --}}
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-4xl font-bold text-blue-200">
                                {{ strtoupper(substr($program->title, 0, 2)) }}
                            </span>
                        </div>
                    @endif

                    {{-- Format badge --}}
                    <span class="absolute top-3 right-3 text-xs font-semibold px-2.5 py-1 rounded-full backdrop-blur-sm
                        {{ match($program->program_format) {
                            'asynchronous' => 'bg-blue-600/90 text-white',
                            'synchronous'  => 'bg-green-600/90 text-white',
                            'hybrid'       => 'bg-purple-600/90 text-white',
                            'mixed'        => 'bg-amber-500/90 text-white',
                            default        => 'bg-gray-600/90 text-white',
                        } }}">
                        {{ ucfirst($program->program_format) }}
                    </span>
                </div>

                {{-- Card body --}}
                <div class="p-5 flex flex-col flex-1">
                    {{-- Type + College --}}
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">
                            {{ $program->programType->name }}
                        </span>
                    </div>

                    <h2 class="text-base font-bold text-gray-900 leading-snug mb-1 group-hover:text-blue-700 transition-colors">
                        {{ $program->title }}
                    </h2>

                    <p class="text-xs text-gray-500 mb-3">{{ $program->college->name }}</p>

                    {{-- Stats row --}}
                    <div class="mt-auto pt-3 border-t border-gray-50 flex items-center gap-4 text-xs text-gray-500">
                        @if($program->credit_hours)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            {{ $program->credit_hours }} credits
                        </span>
                        @endif

                        @if($program->duration)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $program->duration }}
                        </span>
                        @endif

                        @if($program->program_fees)
                        <span class="ml-auto font-semibold text-gray-700">${{ number_format($program->program_fees, 0) }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($programs->hasPages())
            <div class="mt-10">
                {{ $programs->links() }}
            </div>
        @endif

        @endif
    </section>

@endsection
