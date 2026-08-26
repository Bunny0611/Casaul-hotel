@extends('housekeeping.layout')

@section('content')
<style>
    .history-page { max-width: 1600px; margin: 0 auto; animation: history-fade .35s ease; }
    @keyframes history-fade { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .history-hero { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.35rem; padding: 1.5rem 1.7rem; border: 1px solid #f2d8c9; border-radius: 1rem; background: linear-gradient(135deg, #fff 0%, #fff7f2 100%); box-shadow: 0 8px 24px rgba(73, 52, 42, .06); }
    .history-eyebrow { margin: 0 0 .35rem; color: #c2410c; font-size: .72rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
    .history-hero h2 { margin: 0; color: #3a2520; font-size: 1.8rem; font-weight: 750; }
    .history-hero p { margin: .35rem 0 0; color: #7c706b; font-size: .85rem; }
    .history-hero-icon { display: grid; place-items: center; width: 3.4rem; height: 3.4rem; border-radius: .85rem; background: #def4e6; color: #348152; font-size: 1.3rem; }
    .history-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.35rem; }
    .history-stat { display: flex; align-items: center; gap: .8rem; padding: 1rem 1.1rem; border: 1px solid #e8e1de; border-radius: .8rem; background: #fff; box-shadow: 0 5px 16px rgba(54, 38, 31, .045); }
    .history-stat-icon { display: grid; place-items: center; width: 2.6rem; height: 2.6rem; border-radius: .7rem; background: #def4e6; color: #348152; }
    .history-stat strong { display: block; color: #342521; font-size: 1.35rem; }
    .history-stat span { display: block; margin-top: .15rem; color: #8a7d77; font-size: .75rem; }
    .history-panel { overflow: hidden; border: 1px solid #e8e1de; border-radius: 1rem; background: #fff; box-shadow: 0 7px 24px rgba(54, 38, 31, .055); }
    .history-panel-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.2rem 1.35rem; border-bottom: 1px solid #eee8e5; }
    .history-panel-header h3 { margin: 0; color: #342521; font-size: 1.05rem; font-weight: 750; }
    .history-panel-header p { margin: .25rem 0 0; color: #8a7d77; font-size: .78rem; }
    .history-count { padding: .45rem .75rem; border-radius: 999px; background: #edf8f0; color: #348152; font-size: .75rem; font-weight: 700; white-space: nowrap; }
    .history-table thead { background: #faf9f8; }
    .history-table th { color: #81756f; font-size: .68rem; letter-spacing: .06em; }
    .history-table tbody tr:hover { background: #fbfdfb; }
    .completed-badge { display: inline-flex; align-items: center; gap: .35rem; border-radius: 999px; padding: .35rem .6rem; background: #def4e6; color: #348152; font-size: .7rem; font-weight: 700; }
    @media (max-width: 700px) { .history-stats { grid-template-columns: 1fr; } .history-hero { align-items: flex-start; } }
</style>
<div class="history-page">
    <div class="history-hero"><div><p class="history-eyebrow">Housekeeping records</p><h2>Cleaning History</h2><p>Review completed room cleaning tasks and completion times.</p></div><div class="history-hero-icon"><i class="fas fa-clipboard-check" aria-hidden="true"></i></div></div>
    <div class="history-stats"><div class="history-stat"><div class="history-stat-icon"><i class="fas fa-check"></i></div><div><strong>{{ $tasks->count() }}</strong><span>Completed tasks</span></div></div><div class="history-stat"><div class="history-stat-icon"><i class="fas fa-calendar-check"></i></div><div><strong>{{ $tasks->where('finished_at', '>=', now()->startOfDay())->count() }}</strong><span>Completed today</span></div></div><div class="history-stat"><div class="history-stat-icon"><i class="fas fa-user-check"></i></div><div><strong>{{ $tasks->pluck('assigned_staff_id')->filter()->unique()->count() }}</strong><span>Staff members</span></div></div></div>
    @if (session('success'))<div class="mb-5 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>@endif
    <div class="history-panel"><div class="history-panel-header"><div><h3>Completed Cleaning Records</h3><p>Every completed task remains available for reference.</p></div><span class="history-count">{{ $tasks->count() }} record{{ $tasks->count() === 1 ? '' : 's' }}</span></div><div class="overflow-x-auto"><table class="history-table min-w-[760px] w-full text-left text-sm"><thead class="text-xs uppercase"><tr><th class="p-4">Room</th><th class="p-4">Task</th><th class="p-4">Assigned Staff</th><th class="p-4">Date</th><th class="p-4">Time</th><th class="p-4">Status</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($tasks as $task)<tr><td class="p-4 font-semibold">{{ $task->room->room_number }}</td><td class="p-4">{{ $task->task }}</td><td class="p-4">{{ $task->assignedStaff?->name ?? 'Unassigned' }}</td><td class="p-4">{{ $task->finished_at?->format('M d, Y') ?? $task->scheduled_date->format('M d, Y') }}</td><td class="p-4">{{ $task->finished_at?->format('g:i A') ?? 'N/A' }}</td><td class="p-4"><span class="completed-badge"><i class="fas fa-check-circle"></i>Completed</span></td></tr>@empty<tr><td colspan="6" class="p-10 text-center text-gray-500">No completed cleaning records.</td></tr>@endforelse</tbody></table></div></div>
</div>
@endsection