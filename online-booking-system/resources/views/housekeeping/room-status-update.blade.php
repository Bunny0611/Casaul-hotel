@extends('housekeeping.layout')

@section('content')
@php
    $rooms = $rooms ?? collect();
    $statusCodeCount = 13;

    $statusCounts = [
        'VR' => $rooms->filter(function ($room) {
            $status = strtolower((string) ($room->status ?? ''));
            $cleaning = strtolower((string) ($room->cleaning_status ?? ''));
            return in_array($status, ['vr', 'vacant_ready', 'available', 'vacant'], true)
                && in_array($cleaning, ['clean', 'ready', '', null], true);
        })->count(),
        'VD' => $rooms->filter(function ($room) {
            $status = strtolower((string) ($room->status ?? ''));
            $cleaning = strtolower((string) ($room->cleaning_status ?? ''));
            return in_array($status, ['vd', 'vacant_dirty', 'available', 'vacant'], true)
                && $cleaning === 'dirty';
        })->count(),
        'OC' => $rooms->filter(function ($room) {
            $status = strtolower((string) ($room->status ?? ''));
            $cleaning = strtolower((string) ($room->cleaning_status ?? ''));
            return in_array($status, ['oc', 'occupied_clean', 'occupied'], true)
                && in_array($cleaning, ['clean', 'ready'], true);
        })->count(),
        'OD' => $rooms->filter(function ($room) {
            $status = strtolower((string) ($room->status ?? ''));
            $cleaning = strtolower((string) ($room->cleaning_status ?? ''));
            return in_array($status, ['od', 'occupied_dirty', 'occupied'], true)
                && $cleaning === 'dirty';
        })->count(),
        'OOO' => $rooms->filter(function ($room) {
            $status = strtolower((string) ($room->status ?? ''));
            $cleaning = strtolower((string) ($room->cleaning_status ?? ''));
            return in_array($status, ['ooo', 'out_of_order', 'maintenance', 'out of order'], true)
                || in_array($cleaning, ['out_of_order', 'out of order'], true);
        })->count(),
        'BNO' => $rooms->filter(function ($room) {
            $status = strtolower((string) ($room->status ?? ''));
            $cleaning = strtolower((string) ($room->cleaning_status ?? ''));
            return in_array($status, ['blo', 'blocked', 'unavailable'], true)
                || $cleaning === 'blocked';
        })->count(),
    ];

    $statusStyles = [
        'VR' => ['class' => 'badge-green', 'label' => 'Vacant Ready', 'dot' => '#2c8653'],
        'VD' => ['class' => 'badge-amber', 'label' => 'Vacant Dirty', 'dot' => '#b77a18'],
        'OC' => ['class' => 'badge-oc', 'label' => 'Occupied Clean', 'dot' => '#2f6aa5'],
        'OD' => ['class' => 'badge-red', 'label' => 'Occupied Dirty', 'dot' => '#d24b5a'],
        'OOO' => ['class' => 'badge-purple', 'label' => 'Out of Order', 'dot' => '#604bb9'],
        'BNO' => ['class' => 'badge-slate', 'label' => 'Blocked', 'dot' => '#4b5864'],
    ];
@endphp

<style>
.room-status-page { display:flex; gap:22px; width:100%; max-width:1500px; margin:0 auto; padding:18px 18px 0; color:#24313b; font-family:"Segoe UI", sans-serif; }
.room-status-main { flex:1 1 auto; min-width:0; background:rgba(255,255,255,0.45); border:1px solid #e6e0de; border-radius:16px; box-shadow:0 12px 28px rgba(24,34,41,0.04); padding:18px 18px 12px; }
.room-status-aside { display:none; width:260px; flex-shrink:0; background:rgba(255,255,255,0.45); border:1px solid #e6e0de; border-radius:16px; box-shadow:0 12px 28px rgba(24,34,41,0.04); padding:16px 14px 10px; }
.room-status-page.has-sidebar { display:flex; }
.room-status-page.has-sidebar .room-status-aside { display:block; }
.status-header { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; }
.status-title-wrap { display:flex; align-items:center; gap:12px; }
.status-title-icon { display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:10px; background:#edf0ee; color:#263a3f; }
.status-title-wrap h2 { margin:0; color:#2a3a46; font-size:24px; font-weight:700; }
.status-title-wrap p { margin:2px 0 0; color:#808c95; font-size:12px; }
.status-view-btn { display:inline-flex; align-items:center; gap:8px; padding:9px 14px; border:1px solid #cfe4d8; border-radius:10px; background:#edf9f2; color:#1f4f41; font-size:12px; font-weight:600; text-decoration:none; }
.status-summary { display:grid; grid-template-columns:repeat(6, minmax(0, 1fr)); gap:12px; margin-bottom:18px; }
.summary-card { min-height:96px; padding:12px 10px 9px; border-radius:10px; border:1px solid transparent; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; }
.summary-card .code { display:inline-flex; align-items:center; justify-content:center; min-width:52px; padding:8px 10px; border-radius:8px; background:rgba(255,255,255,0.5); color:#1f2d38; font-weight:700; font-size:12px; letter-spacing:0.04em; margin-bottom:8px; }
.summary-card .count { color:#1f2d38; font-size:20px; font-weight:800; line-height:1; }
.card-vr { background:#dff5e7; border-color:#ccebd2; }
.card-vd { background:#f3e9c9; border-color:#eedda7; }
.card-oc { background:#dfeaf6; border-color:#cfe0f5; }
.card-od { background:#f7dcdc; border-color:#efc1c4; }
.card-ooo { background:#f0e6ff; border-color:#ddd0ff; }
.card-bno { background:#e8ebee; border-color:#dfe6eb; }
.toolbar { display:flex; align-items:center; gap:12px; padding:10px 0 14px; margin-bottom:12px; }
.toolbar-search { flex:1 1 250px; position:relative; }
.toolbar-search .search-icon { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#89949c; font-size:13px; }
.toolbar-search input, .toolbar-select select { width:100%; min-height:36px; border:1px solid #dfe4e8; border-radius:10px; background:#fff; color:#53606c; font-size:13px; outline:none; padding:0 12px; }
.toolbar-search input { padding-left:38px; }
.toolbar-select { position:relative; flex:0 0 160px; }
.toolbar-select select { appearance:none; background-image:linear-gradient(45deg, transparent 50%, #6e7a83 50%), linear-gradient(135deg, #6e7a83 50%, transparent 50%); background-position:calc(100% - 18px) calc(50% - 2px), calc(100% - 12px) calc(50% - 2px); background-size:6px 6px, 6px 6px; background-repeat:no-repeat; padding-right:32px; }
.filter-clear { border:1px solid #dfe4e8; background:#fff; border-radius:10px; color:#4b5862; font-size:13px; font-weight:600; padding:9px 14px; cursor:pointer; }
.room-table-wrap { border:1px solid #e7e0dd; border-radius:12px; overflow:hidden; background:#fff; }
.room-table { width:100%; border-collapse:collapse; table-layout:fixed; }
.room-table thead th { padding:12px 14px; background:#f2f4f5; border-bottom:1px solid #e7e0dd; color:#66737d; font-size:11px; text-transform:uppercase; font-weight:700; text-align:left; letter-spacing:0.06em; }
.room-table tbody td { padding:12px 14px; border-bottom:1px solid #f0eceb; vertical-align:middle; font-size:13px; color:#3c4b57; }
.room-table tbody tr:last-child td { border-bottom:none; }
.room-cell { display:flex; align-items:center; gap:10px; font-weight:600; color:#2d3740; }
.room-icon { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:#f4e6df; color:#6d4b39; font-size:12px; }
.status-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-size:11px; font-weight:700; white-space:nowrap; }
.badge-dot { width:7px; height:7px; border-radius:50%; display:inline-block; }
.badge-green { background:#dff3e6; color:#2c8653; }
.badge-green .badge-dot { background:#2c8653; }
.badge-amber { background:#f4e6bf; color:#b77a18; }
.badge-amber .badge-dot { background:#b77a18; }
.badge-oc { background:#dfeaf7; color:#2f6aa5; }
.badge-oc .badge-dot { background:#2f6aa5; }
.badge-red { background:#f8dfe3; color:#d24b5a; }
.badge-red .badge-dot { background:#d24b5a; }
.badge-purple { background:#ece4ff; color:#604bb9; }
.badge-purple .badge-dot { background:#604bb9; }
.badge-slate { background:#edf0f3; color:#4b5864; }
.badge-slate .badge-dot { background:#4b5864; }
.cleaning-status { display:inline-flex; align-items:center; justify-content:center; min-width:68px; padding:5px 8px; border-radius:999px; background:#faf5ec; color:#6a5f52; font-size:11px; font-weight:700; }
.cleaning-status.clean { background:#ebf9f0; color:#2b8656; }
.cleaning-status.dirty { background:#fbe7ea; color:#c6505f; }
.cleaning-status.in_progress { background:#faf0d8; color:#98640d; }
.table-update { display:inline-flex; align-items:center; justify-content:center; min-height:32px; padding:0 12px; border:1px solid #e8d6c7; border-radius:8px; background:#fff9f5; color:#9f4d2b; font-size:12px; font-weight:700; cursor:pointer; }
.status-list { display:flex; flex-direction:column; gap:10px; margin-top:16px; }
.status-row { display:flex; align-items:center; gap:10px; color:#485761; font-size:12px; font-weight:600; }
.legend-dot { width:11px; height:11px; border-radius:50%; display:inline-block; }
.legend-title { margin:16px 0 12px; color:#263746; font-size:14px; font-weight:700; }
.legend-note { margin-top:18px; padding:12px 14px; border-radius:12px; background:#f4f7f8; color:#66757f; font-size:12px; line-height:1.55; border:1px solid #e9edf0; }
.modal-overlay { position:fixed; inset:0; z-index:9999; display:none; align-items:center; justify-content:center; padding:20px; background:rgba(39,29,25,0.55); backdrop-filter:blur(4px); }
.modal-overlay.active { display:flex; }
.status-modal { width:100%; max-width:520px; max-height:calc(100vh - 40px); overflow:hidden; background:#ffffff; border-radius:18px; box-shadow:0 25px 70px rgba(39,29,25,0.25); }
.modal-header { display:flex; align-items:center; justify-content:space-between; gap:15px; padding:20px 22px; border-bottom:1px solid #eee6e2; }
.modal-title { display:flex; align-items:center; gap:12px; }
.modal-title-icon { display:flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:11px; background:#fff0e6; color:#a34d2c; }
.modal-title h3 { margin:0; color:#382823; font-size:18px; }
.modal-title p { margin:3px 0 0; color:#91837c; font-size:12px; }
.modal-close { display:flex; align-items:center; justify-content:center; width:34px; height:34px; border:0; border-radius:8px; background:#f6f3f1; color:#766a64; cursor:pointer; }
.modal-body { padding:22px; max-height:calc(100vh - 135px); overflow-y:auto; }
.room-information { display:flex; align-items:center; gap:13px; margin-bottom:20px; padding:14px; border:1px solid #eee5e1; border-radius:12px; background:#faf8f7; }
.room-information-icon { display:flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:10px; background:#f6e9e3; color:#95472d; }
.room-information strong { display:block; color:#3d2d28; font-size:15px; }
.room-information span { display:block; margin-top:3px; color:#897b75; font-size:11px; }
.modal-form-group { margin-bottom:17px; }
.modal-form-group label { display:block; margin-bottom:7px; color:#4b3d37; font-size:12px; font-weight:750; }
.custom-select { position:relative; width:100%; }
.custom-select-trigger {
    width:100%; min-height:44px; padding:0 42px 0 13px; border:1px solid #ddd5d1; border-radius:10px; outline:none; background:#faf9f8; color:#403530; font-size:13px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; text-align:left; position:relative;
}
.custom-select-trigger .custom-select-label { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.custom-select-caret { font-size:12px; color:#665a54; }
.custom-select-menu {
    position:absolute; left:0; right:0; top:calc(100% + 6px); z-index:20; display:none; max-height:260px; overflow-y:auto; background:#fff; border:1px solid #ddd5d1; border-radius:10px; box-shadow:0 18px 35px rgba(45,38,33,0.12); padding:4px 0;
}
.custom-select.open .custom-select-menu { display:block; }
.custom-option {
    width:100%; border:0; background:transparent; padding:10px 13px; text-align:left; font-size:13px; color:#403530; cursor:pointer;
}
.custom-option:hover, .custom-option.selected { background:#f8f2ef; }
.modal-note { display:flex; gap:9px; align-items:flex-start; padding:11px 12px; margin-bottom:20px; border:1px solid #f2dfc8; border-radius:9px; background:#fff8ef; color:#85694e; font-size:11px; line-height:1.5; }
.modal-actions { display:flex; justify-content:flex-end; gap:10px; }
.cancel-button, .confirm-button { min-height:42px; padding:0 17px; border-radius:9px; font-size:12px; font-weight:750; cursor:pointer; }
.cancel-button { border:1px solid #ddd5d1; background:#fff; color:#665a54; }
.confirm-button { border:0; background:linear-gradient(135deg, #8b2d1f, #b84e2c); color:#fff; }
@media (max-width:1200px) { .room-status-page { flex-direction:column; } .room-status-aside { width:100%; } }
@media (max-width:768px) { .status-summary { grid-template-columns:repeat(3, minmax(0, 1fr)); } .toolbar { flex-wrap:wrap; } .toolbar-select { flex:1 1 180px; } }
@media (max-width:560px) { .status-summary { grid-template-columns:repeat(2, minmax(0, 1fr)); } .status-header { flex-direction:column; align-items:flex-start; } }
</style>

<div class="room-status-page" id="roomStatusPage">
    <div class="room-status-main">
        <div class="status-header">
            <div class="status-title-wrap">
                <div class="status-title-icon"><i class="fas fa-bed"></i></div>
                <div>
                    <h2>Room Status Overview</h2>
                    <p>Quick view of current room conditions.</p>
                </div>
            </div>
            <button type="button" class="status-view-btn" id="toggleStatusPanel" aria-expanded="false">
                <i class="fas fa-eye"></i> View All Statuses ({{ $statusCodeCount }})
            </button>
        </div>

        <div class="status-summary">
            <div class="summary-card card-vr"><div class="code">VR</div><div class="count">{{ $statusCounts['VR'] }}</div></div>
            <div class="summary-card card-vd"><div class="code">VD</div><div class="count">{{ $statusCounts['VD'] }}</div></div>
            <div class="summary-card card-oc"><div class="code">OC</div><div class="count">{{ $statusCounts['OC'] }}</div></div>
            <div class="summary-card card-od"><div class="code">OD</div><div class="count">{{ $statusCounts['OD'] }}</div></div>
            <div class="summary-card card-ooo"><div class="code">OOO</div><div class="count">{{ $statusCounts['OOO'] }}</div></div>
            <div class="summary-card card-bno"><div class="code">BNO</div><div class="count">{{ $statusCounts['BNO'] }}</div></div>
        </div>

        <div class="toolbar">
            <div class="toolbar-search"><i class="fas fa-search search-icon"></i><input type="text" placeholder="Search room number..." aria-label="Search room number"></div>
            <div class="toolbar-select"><select aria-label="Room Type"><option>All</option>@foreach($rooms->pluck('room_type')->filter()->unique()->sort() as $type)<option>{{ $type }}</option>@endforeach</select></div>
            <div class="toolbar-select"><select aria-label="Room Status"><option>All</option><option>Vacant Ready</option><option>Vacant Dirty</option><option>Occupied Clean</option><option>Occupied Dirty</option><option>Out of Order</option><option>Blocked</option></select></div>
            <div class="toolbar-select"><select aria-label="Cleaning Status"><option>All</option><option>Clean</option><option>Dirty</option><option>In Progress</option></select></div>
            <button type="button" class="filter-clear"><i class="fas fa-filter"></i> Clear Filters</button>
        </div>

        <div class="room-table-wrap">
            <table class="room-table">
                <thead>
                    <tr>
                        <th style="width:12%;">Room</th>
                        <th style="width:18%;">Room Type</th>
                        <th style="width:18%;">Room Status</th>
                        <th style="width:18%;">Cleaning Status</th>
                        <th style="width:20%;">Last Updated</th>
                        <th style="width:14%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        @php
                            $statusKey = 'VR';
                            $roomStatus = strtolower((string) ($room->status ?? ''));
                            $cleaningStatus = strtolower((string) ($room->cleaning_status ?? ''));

                            if (in_array($roomStatus, ['vr', 'vacant_ready', 'available', 'vacant'], true) && in_array($cleaningStatus, ['clean', 'ready', '', null], true)) {
                                $statusKey = 'VR';
                            } elseif (in_array($roomStatus, ['vd', 'vacant_dirty', 'available', 'vacant'], true) && $cleaningStatus === 'dirty') {
                                $statusKey = 'VD';
                            } elseif (in_array($roomStatus, ['oc', 'occupied_clean', 'occupied'], true) && in_array($cleaningStatus, ['clean', 'ready'], true)) {
                                $statusKey = 'OC';
                            } elseif (in_array($roomStatus, ['od', 'occupied_dirty', 'occupied'], true) && $cleaningStatus === 'dirty') {
                                $statusKey = 'OD';
                            } elseif (in_array($roomStatus, ['ooo', 'out_of_order', 'maintenance', 'out of order'], true) || in_array($cleaningStatus, ['out_of_order', 'out of order'], true)) {
                                $statusKey = 'OOO';
                            } elseif (in_array($roomStatus, ['blo', 'blocked', 'unavailable'], true) || $cleaningStatus === 'blocked') {
                                $statusKey = 'BNO';
                            }

                            $statusMeta = $statusStyles[$statusKey] ?? $statusStyles['VR'];
                            $cleaningLabel = ucfirst(str_replace('_', ' ', $cleaningStatus ?: 'clean'));
                            if ($cleaningStatus === 'in_progress') {
                                $cleaningLabel = 'In Progress';
                            }
                            $cleaningClass = 'clean';
                            if ($cleaningStatus === 'dirty') {
                                $cleaningClass = 'dirty';
                            } elseif ($cleaningStatus === 'in_progress') {
                                $cleaningClass = 'in_progress';
                            }
                        @endphp
                        <tr>
                            <td>
                                <div class="room-cell">
                                    <span class="room-icon"><i class="fas fa-door-open"></i></span>
                                    <span>{{ $room->room_number }}</span>
                                </div>
                            </td>
                            <td>{{ $room->room_type ?? 'Standard Room' }}</td>
                            <td>
                                <span class="status-badge {{ $statusMeta['class'] }}">
                                    <span class="badge-dot" style="background: {{ $statusMeta['dot'] }};"></span>
                                    {{ $statusMeta['label'] }}
                                </span>
                            </td>
                            <td>
                                <span class="cleaning-status {{ $cleaningClass }}">{{ $cleaningLabel }}</span>
                            </td>
                            <td>{{ $room->updated_at ? $room->updated_at->format('M d, Y · h:i A') : '—' }}</td>
                            <td>
                                <button class="table-update" type="button" onclick="openStatusModal({{ $room->id }}, '{{ $room->room_number }}', '{{ addslashes((string) ($room->room_type ?? 'Standard Room')) }}', '{{ $statusMeta['label'] }}', '{{ $cleaningStatus ?: 'clean' }}')"><i class="fas fa-pen"></i> Update</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="padding:26px; text-align:center; color:#677883;">No rooms available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <aside class="room-status-aside" id="statusLegendPanel">
        <div class="legend-title">Room Status Codes</div>
        <div class="status-list">
            <div class="status-row"><span class="legend-dot" style="background:#2c8653;"></span> OC <span style="margin-left:4px;">Occupied Clean</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#d24b5a;"></span> OD <span style="margin-left:4px;">Occupied Dirty</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#2c8653;"></span> VR <span style="margin-left:4px;">Vacant Ready</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#2c8653;"></span> VC <span style="margin-left:4px;">Vacant Clean</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#b77a18;"></span> VD <span style="margin-left:4px;">Vacant Dirty</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#604bb9;"></span> HSD <span style="margin-left:4px;">House Use Dirty</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#2c8653;"></span> HSUC <span style="margin-left:4px;">House Use Clean</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#604bb9;"></span> OOO <span style="margin-left:4px;">Out of Order</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#4b5864;"></span> BLO <span style="margin-left:4px;">Blocked</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#4b5864;"></span> NS <span style="margin-left:4px;">No Show</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#4b5864;"></span> SO <span style="margin-left:4px;">Slept Out</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#4b5864;"></span> HU <span style="margin-left:4px;">House Use</span></div>
            <div class="status-row"><span class="legend-dot" style="background:#4b5864;"></span> DND <span style="margin-left:4px;">Do Not Disturb</span></div>
        </div>
        <div class="legend-note">Room status shows the official hotel status code.<br>Cleaning status shows the current housekeeping condition.</div>
    </aside>
</div>

<div class="modal-overlay" id="statusModal" onclick="closeStatusModal(event)">
    <div class="status-modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div class="modal-title">
                <div class="modal-title-icon"><i class="fas fa-sync-alt"></i></div>
                <div><h3>Update Room Status</h3><p>Change the housekeeping status of this room.</p></div>
            </div>
            <button type="button" class="modal-close" onclick="closeStatusModal()" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="room-information">
                <div class="room-information-icon"><i class="fas fa-door-open"></i></div>
                <div><strong id="modalRoomNumber">Room 101</strong><span id="modalRoomType">Deluxe Room</span></div>
            </div>

            <form id="statusUpdateForm" method="POST" action="{{ route('housekeeping.rooms.cleaning', ['id' => '__ROOM_ID__']) }}" data-action-template="{{ route('housekeeping.rooms.cleaning', ['id' => '__ROOM_ID__']) }}">
                @csrf
                @method('PATCH')
                <div class="modal-form-group">
                    <label for="current_status">Current Status</label>
                    <div class="custom-select" data-custom-select>
                        <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                            <span class="custom-select-label">OC - Occupied Clean</span>
                            <span class="custom-select-caret">▾</span>
                        </button>
                        <div class="custom-select-menu" role="listbox">
                            <button type="button" class="custom-option selected" data-value="OC">OC - Occupied Clean</button>
                            <button type="button" class="custom-option" data-value="OD">OD - Occupied Dirty</button>
                            <button type="button" class="custom-option" data-value="VR">VR - Vacant Ready</button>
                            <button type="button" class="custom-option" data-value="VC">VC - Vacant Clean</button>
                            <button type="button" class="custom-option" data-value="VD">VD - Vacant Dirty</button>
                            <button type="button" class="custom-option" data-value="HSD">HSD - House Use Dirty</button>
                            <button type="button" class="custom-option" data-value="HSUC">HSUC - House Use Clean</button>
                            <button type="button" class="custom-option" data-value="OOO">OOO - Out of Order</button>
                            <button type="button" class="custom-option" data-value="BLO">BLO - Blocked</button>
                            <button type="button" class="custom-option" data-value="NS">NS - No Show</button>
                            <button type="button" class="custom-option" data-value="SO">SO - Slept Out</button>
                            <button type="button" class="custom-option" data-value="HU">HU - House Use</button>
                            <button type="button" class="custom-option" data-value="DND">DND - Do Not Disturb</button>
                        </div>
                        <input type="hidden" id="current_status" name="current_status" value="OC">
                    </div>
                </div>
                <div class="modal-form-group">
                    <label for="update_status">New Room Status</label>
                    <div class="custom-select" data-custom-select>
                        <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                            <span class="custom-select-label">Clean</span>
                            <span class="custom-select-caret">▾</span>
                        </button>
                        <div class="custom-select-menu" role="listbox">
                            <button type="button" class="custom-option selected" data-value="clean">Clean</button>
                            <button type="button" class="custom-option" data-value="dirty">Dirty</button>
                            <button type="button" class="custom-option" data-value="in_progress">In Progress</button>
                            <button type="button" class="custom-option" data-value="ready">Ready</button>
                        </div>
                        <input type="hidden" id="update_status" name="cleaning_status" value="clean">
                    </div>
                </div>
                <div class="modal-note"><i class="fas fa-lightbulb"></i><span>Make sure the new status accurately reflects the actual condition of the room.</span></div>
                <div class="modal-actions"><button type="button" class="cancel-button" onclick="closeStatusModal()">Cancel</button><button type="submit" class="confirm-button"><i class="fas fa-check"></i> Save Status</button></div>
            </form>
        </div>
    </div>
</div>

<script>
const roomStatusPage = document.getElementById('roomStatusPage');
const toggleStatusButton = document.getElementById('toggleStatusPanel');
const statusLegendPanel = document.getElementById('statusLegendPanel');

if (toggleStatusButton && roomStatusPage && statusLegendPanel) {
    toggleStatusButton.addEventListener('click', function () {
        const isVisible = roomStatusPage.classList.toggle('has-sidebar');
        statusLegendPanel.style.display = isVisible ? 'block' : 'none';
        toggleStatusButton.setAttribute('aria-expanded', String(isVisible));
        toggleStatusButton.innerHTML = isVisible
            ? '<i class="fas fa-eye-slash"></i> Hide Statuses ({{ $statusCodeCount }})'
            : '<i class="fas fa-eye"></i> View All Statuses ({{ $statusCodeCount }})';
    });
}

function setCustomSelectValue(customSelect, value) {
    const trigger = customSelect.querySelector('.custom-select-trigger');
    const label = customSelect.querySelector('.custom-select-label');
    const hiddenInput = customSelect.querySelector('input[type="hidden"]');
    const option = customSelect.querySelector('.custom-option[data-value="' + value + '"]');

    customSelect.querySelectorAll('.custom-option').forEach((item) => {
        item.classList.toggle('selected', item === option);
    });

    if (option && label) {
        label.textContent = option.textContent.trim();
    }

    if (hiddenInput) {
        hiddenInput.value = value;
    }

    if (trigger) {
        trigger.setAttribute('aria-expanded', 'false');
    }

    customSelect.classList.remove('open');
}

function bindCustomSelect(customSelect) {
    const trigger = customSelect.querySelector('.custom-select-trigger');
    const options = customSelect.querySelectorAll('.custom-option');

    trigger.addEventListener('click', function (event) {
        event.stopPropagation();
        const isOpen = customSelect.classList.contains('open');
        document.querySelectorAll('.custom-select.open').forEach((select) => {
            if (select !== customSelect) {
                select.classList.remove('open');
            }
        });
        customSelect.classList.toggle('open', !isOpen);
        trigger.setAttribute('aria-expanded', String(!isOpen));
    });

    options.forEach((option) => {
        option.addEventListener('click', function () {
            const value = option.dataset.value;
            setCustomSelectValue(customSelect, value);
        });
    });

    const hiddenInput = customSelect.querySelector('input[type="hidden"]');
    if (hiddenInput && hiddenInput.value) {
        setCustomSelectValue(customSelect, hiddenInput.value);
    }
}

document.querySelectorAll('.custom-select').forEach(bindCustomSelect);
document.addEventListener('click', function () {
    document.querySelectorAll('.custom-select.open').forEach((select) => select.classList.remove('open'));
});

function openStatusModal(roomId, roomNumber, roomType, currentStatus, currentCleaningStatus) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusUpdateForm');
    const roomNumberElement = document.getElementById('modalRoomNumber');
    const roomTypeElement = document.getElementById('modalRoomType');
    const currentStatusInput = document.getElementById('current_status');
    const updateStatusInput = document.getElementById('update_status');

    roomNumberElement.textContent = 'Room ' + roomNumber;
    roomTypeElement.textContent = roomType;
    form.action = form.dataset.actionTemplate.replace('__ROOM_ID__', roomId);

    const roomStatusValueMap = {
        'Vacant Ready': 'VR',
        'Vacant Dirty': 'VD',
        'Occupied Clean': 'OC',
        'Occupied Dirty': 'OD',
        'Out of Order': 'OOO',
        'Blocked': 'BLO',
        'Available': 'VR',
        'Occupied': 'OC',
        'Maintenance': 'OOO',
        'Reserved': 'VR',
        'House Use Dirty': 'HSD',
        'House Use Clean': 'HSUC',
        'No Show': 'NS',
        'Slept Out': 'SO',
        'House Use': 'HU',
        'Do Not Disturb': 'DND',
        'Vacant Clean': 'VC'
    };

    const cleaningStatusValueMap = {
        dirty: 'dirty',
        clean: 'clean',
        ready: 'ready',
        in_progress: 'in_progress',
        'In Progress': 'in_progress',
        'Ready': 'ready',
        'Clean': 'clean',
        'Dirty': 'dirty',
        'Blocked': 'blocked',
        'Out of Order': 'out_of_order'
    };

    if (currentStatusInput) {
        const mappedValue = roomStatusValueMap[currentStatus] || 'VR';
        setCustomSelectValue(currentStatusInput.closest('.custom-select'), mappedValue);
    }

    if (updateStatusInput) {
        const mappedValue = cleaningStatusValueMap[currentCleaningStatus] || 'clean';
        setCustomSelectValue(updateStatusInput.closest('.custom-select'), mappedValue);
    }

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeStatusModal(event) {
    if (event && event.target !== document.getElementById('statusModal')) return;
    const modal = document.getElementById('statusModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeStatusModal();
});
</script>

@endsection