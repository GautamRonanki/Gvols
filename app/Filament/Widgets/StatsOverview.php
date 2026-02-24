<?php

namespace App\Filament\Widgets;

use App\Models\Program;
use App\Models\ProgramType;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total    = Program::count();
        $active   = Program::where('is_active', true)->count();
        $inactive = Program::where('is_active', false)->count();

        $byType = ProgramType::withCount('programs')
            ->having('programs_count', '>', 0)
            ->get()
            ->map(fn ($t) => "{$t->name}: {$t->programs_count}")
            ->join(' · ');

        return [
            Stat::make('Total Programs', $total)
                ->icon('heroicon-o-academic-cap')
                ->color('primary'),

            Stat::make('Active Programs', $active)
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Inactive Programs', $inactive)
                ->icon('heroicon-o-pause-circle')
                ->color('gray'),

            Stat::make('Programs by Type', $byType ?: 'None yet')
                ->icon('heroicon-o-tag')
                ->color('info'),
        ];
    }
}
