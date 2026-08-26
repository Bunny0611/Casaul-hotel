@extends('app')

@section('content')

<style>
    /* Reservation page - visual refresh (scoped) */
    :root {
        --res-bg: #f6f7fb;
        --card: #ffffff;
        --muted: #6b7280;
        --accent: #dc2626;
        --accent-2: #f97316;
        --radius-lg: 18px;
        --radius-sm: 10px;
        --shadow-soft: 0 10px 30px rgba(16, 24, 40, 0.08);
    }

    .reservation-page { background: var(--res-bg); padding: 28px 0; }

    .reservation-hero { margin: 6px 0 20px; max-width: 1200px; margin-left: auto; margin-right: auto; padding: 28px; background: linear-gradient(180deg, rgba(255,255,255,0.6), rgba(255,255,255,0.35)); border-radius: 14px; box-shadow: var(--shadow-soft); }
    .reservation-hero .eyebrow { color: var(--muted); font-weight:700; letter-spacing:0.06em; }
    .reservation-hero h1 { margin-top:8px; font-size:2rem; color:#0f172a; }
    .reservation-hero p { color: #374151; margin-top:8px; }

    .reservation-shell { display:flex; gap:24px; max-width:1200px; margin:20px auto; padding:0 20px; }
    .reservation-left { flex:1 1 720px; }
    .reservation-summary { width:360px; flex:0 0 360px; }

    .reservation-tabs { display:flex; gap:10px; margin-bottom:16px; }
    .tab-btn { background:transparent; border-radius:999px; padding:10px 14px; font-weight:700; color:#475569; border:1px solid transparent; transition:all .18s ease; }
    .tab-btn.active { background: linear-gradient(90deg,var(--accent),var(--accent-2)); color:white; box-shadow: 0 8px 24px rgba(220,38,38,0.12); }

    .reservation-panel { background: var(--card); border-radius: 12px; padding:14px; box-shadow: 0 8px 20px rgba(12,18,30,0.04); margin-bottom:18px; }

    .reservation-card-grid { display:grid; grid-template-columns: repeat(2,1fr); gap:14px; }
    .reservation-card { border-radius: 12px; overflow:hidden; background: linear-gradient(180deg,#fff,#fbfdff); display:flex; border:1px solid rgba(15,23,42,0.04); box-shadow: 0 6px 18px rgba(16,24,40,0.04); }
    .reservation-card img{ width:40%; object-fit:cover; }
    .reservation-card-body{ padding:14px; display:flex; flex-direction:column; gap:8px; }
    .reservation-card h4{ margin:0; color:#0f172a; font-size:1.05rem; }
    .reservation-card-meta{ color:var(--muted); font-size:0.92rem; display:flex; gap:8px; }
    .reservation-card-footer{ margin-top:auto; display:flex; justify-content:space-between; align-items:center; gap:8px; }

    .select-option-btn{ background:transparent; border:1px solid rgba(15,23,42,0.06); padding:8px 12px; border-radius:999px; color:#0f172a; font-weight:700; cursor:pointer; transition:all .14s ease; }
    .select-option-btn:disabled{ opacity:0.5; cursor:not-allowed; }
    .select-option-btn:hover:not(:disabled){ transform:translateY(-2px); box-shadow:0 8px 18px rgba(16,24,40,0.06); }

    .summary-card{ background: linear-gradient(180deg,#fff,#fbfdff); padding:18px; border-radius:12px; box-shadow: var(--shadow-soft); border:1px solid rgba(15,23,42,0.04); }
    .summary-card h3{ margin:0 0 12px 0; font-size:1.1rem; color:#0f172a; }
    .summary-item-card{ display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px; border-radius:10px; background:transparent; border-bottom:1px solid rgba(15,23,42,0.03); }
    .summary-item-card-left{ display:flex; gap:12px; align-items:center; }
    .summary-item-title{ font-weight:700; color:#0f172a; margin:0; }
    .summary-item-subtitle{ margin:0; color:var(--muted); font-size:0.9rem; }
    .summary-item-price{ font-weight:800; color:#0f172a; }
    .summary-edit-btn{ background:transparent; color:var(--accent); border:none; font-weight:700; cursor:pointer; }

    .summary-total{ display:flex; justify-content:space-between; align-items:center; padding:18px 0 6px 0; }
    .summary-total strong{ font-size:1.4rem; color:#0f172a; }

    .confirmation-modal{ position:fixed; inset:0; display:none; align-items:flex-start; justify-content:center; background:rgba(2,6,23,0.55); z-index:9999; padding:20px; overflow-y:auto; }
    .confirmation-modal.open{ display:flex; }
    .confirmation-card{ width:min(760px,100%); max-height:calc(100vh - 40px); overflow-y:auto; box-sizing:border-box; border-radius:20px; padding:28px; background:linear-gradient(180deg,#fff,#fbfdff); box-shadow: 0 32px 80px rgba(2,6,23,0.18); border:1px solid rgba(15,23,42,0.06); }
    .confirmation-item{ border-radius:12px; padding:12px; background:#f8fafc; color:#0f172a; }
    .confirmation-total-amount{ font-size:1.6rem; color:#0f172a; }

    .payment-method-section { margin:20px 0; }
    .payment-method-section h4 { margin:0 0 12px; color:#111827; }
    .payment-method-options { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
    .payment-method-option { padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; background:#fff; color:#374151; cursor:pointer; font-weight:600; }
    .payment-method-option.selected { border-color:#dc2626; background:#fff7f7; color:#111827; }

    .confirm-submit-btn{ background: var(--accent); color:white; border-radius:999px; padding:12px 22px; }
    .confirm-cancel-btn{ background: #f3f4f6; border-radius:999px; padding:12px 22px; }

    @media (max-width: 640px){
        .confirmation-modal{ padding:12px; }
        .confirmation-card{ max-height:calc(100vh - 24px); padding:20px; }
        .confirmation-grid{ grid-template-columns:1fr !important; }
        .confirmation-actions{ flex-wrap:wrap; }
        .confirmation-actions button{ flex:1 1 100%; }
    }

    /* Responsive */
    @media (max-width: 980px){ .reservation-shell{ flex-direction:column; } .reservation-summary{ width:100%; } .reservation-card-grid{ grid-template-columns: 1fr; } }

    /* Reservation flow layout */
    .reservation-page { min-height:100vh; padding:22px 0 48px; background:#f4f6fb; color:#172033; }
    .reservation-hero { display:grid; grid-template-columns:minmax(0,1fr) 430px; grid-template-rows:auto auto auto; align-items:center; gap:0 32px; max-width:970px; margin:0 auto 12px; padding:0 0 8px; background:transparent; border-radius:0; box-shadow:none; }
    .reservation-hero .eyebrow { margin:0; color:#68738a; font-size:10px; letter-spacing:.08em; text-transform:uppercase; }
    .reservation-hero h1 { max-width:560px; margin:8px 0 10px; color:#172033; font-size:31px; line-height:1.12; letter-spacing:-.02em; }
    .reservation-hero p { max-width:520px; margin:0; color:#69748b; font-size:13px; line-height:1.5; }
    .reservation-hero::after { content:''; display:block; grid-column:2; grid-row:1 / span 3; align-self:stretch; min-height:156px; border-radius:0 0 0 60px; background:linear-gradient(90deg,rgba(244,246,251,0) 0%,rgba(244,246,251,.04) 15%,rgba(244,246,251,0) 35%), url('{{ $rooms->first()?->image ? asset(str_starts_with($rooms->first()->image, 'rooms/') ? 'storage/' . $rooms->first()->image : $rooms->first()->image) : asset('image/Royal-Suite-room.jpg') }}') center/cover; }

    .reservation-shell { display:grid; grid-template-columns:minmax(0,1fr) 260px; gap:18px; max-width:970px; margin:0 auto; padding:0; }
    .reservation-left { min-width:0; }
    .reservation-tabs { display:grid; grid-template-columns:repeat(4,1fr); gap:0; height:48px; margin:0 0 10px; padding:0 8px; overflow:hidden; border-radius:11px; background:#e9edf8; }
    .tab-btn { display:flex; align-items:center; justify-content:center; gap:9px; border:0; border-radius:10px; padding:8px 10px; color:#253570; font-size:12px; }
    .tab-btn.active { background:#cc0925; box-shadow:0 5px 12px rgba(204,9,37,.2); }
    .tab-icon { font-size:14px; }
    .reservation-progress { display:flex; align-items:center; gap:13px; margin-bottom:0; padding:14px 22px; background:#fff; border-bottom:1px solid #edf0f5; }
    .progress-step { display:flex; align-items:center; gap:9px; min-width:0; }
    .progress-number { display:grid; place-items:center; width:30px; height:30px; flex:0 0 30px; border-radius:50%; background:#e3e7f0; color:#71809a; font-weight:800; }
    .progress-step.active .progress-number { background:#d20b26; color:#fff; }
    .progress-copy { display:flex; flex-direction:column; gap:2px; }
    .progress-copy strong { color:#1b2438; font-size:11px; }
    .progress-copy span { color:#8993a6; font-size:9px; line-height:1.2; }
    .progress-line { height:1px; flex:1; min-width:18px; background:#d7dce5; }
    .reservation-panel { margin:0; padding:0 16px 18px; border-radius:0 0 10px 10px; background:#fff; box-shadow:0 5px 14px rgba(24,36,64,.04); }
    .reservation-panel:not(#room-tab) { margin-top:12px; border-radius:10px; }
    .reservation-panel .panel-header { padding:14px 0 10px; }
    .reservation-panel .panel-header h3 { margin:0; font-size:15px; }
    .reservation-panel .panel-header p { margin:3px 0 0; color:#7c8799; font-size:10px; }
    .panel-row { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:14px; }
    .field-label { display:block; margin-bottom:5px; color:#3c465a; font-size:10px; font-weight:700; }
    .field-input { width:100%; min-height:34px; box-sizing:border-box; border:1px solid #e1e5ec; border-radius:7px; padding:7px 10px; color:#8993a6; background:#fff; font-size:10px; }
    .reservation-card-grid { grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; }
    .reservation-card { display:flex; flex-direction:column; min-width:0; overflow:hidden; border:1px solid #edf0f4; border-radius:8px; background:#fff; box-shadow:0 2px 5px rgba(24,36,64,.04); }
    .reservation-card img { width:100%; height:88px; flex:0 0 88px; object-fit:cover; }
    .reservation-card-body { gap:6px; padding:9px; }
    .reservation-card h4 { font-size:11px; }
    .reservation-card-meta { gap:8px; font-size:9px; }
    .reservation-card-body > p { margin:0; color:#788398; font-size:9px; line-height:1.45; }
    .reservation-card-footer { display:block; }
    .reservation-card .price { display:block; margin-bottom:7px; color:#d20b26; font-size:11px; font-weight:800; }
    .select-option-btn { width:100%; padding:7px 8px; border:1px solid #ff9aa7; border-radius:7px; color:#d20b26; background:#fff; font-size:10px; }
    .select-option-btn:disabled { border-color:#d20b26; background:#d20b26; color:#fff; opacity:1; }
    .reservation-summary { width:auto; flex:auto; }
    .summary-card { position:sticky; top:18px; padding:14px; border:0; border-radius:11px; background:#fff; box-shadow:0 6px 16px rgba(24,36,64,.08); }
    .summary-card h3 { margin:0 -14px 7px; padding:0 14px 12px; border-bottom:1px solid #edf0f4; font-size:14px; }
    .summary-item-card { padding:10px 0; border-bottom:1px solid #edf0f4; }
    .summary-item-card-left { gap:9px; }
    .summary-item-icon { display:grid; place-items:center; width:32px; height:32px; border-radius:9px; background:#f0f2f8; color:#253570; font-size:13px; }
    .summary-item-title { font-size:10px; }
    .summary-item-subtitle, .summary-item-caption { font-size:9px; }
    .summary-item-price { font-size:10px; }
    .summary-edit-btn { display:none; }
    .summary-total { padding:16px 0 8px; }
    .summary-total span { font-size:12px; font-weight:700; }
    .summary-total strong { font-size:15px; }
    .summary-note { color:#8993a6; font-size:9px; line-height:1.45; }
    .summary-actions { display:flex; flex-direction:column; gap:8px; }
    #confirmReservationBtn { border:0; border-radius:7px; padding:10px; background:linear-gradient(90deg,#d70a25,#ffab08); color:#fff; font-size:10px; font-weight:800; }
    .summary-clear { border:1px solid #ff9aa7; border-radius:7px; padding:9px; background:#fff; color:#d20b26; font-size:10px; font-weight:700; }

    @media (max-width: 980px){ .reservation-hero,.reservation-shell{ max-width:calc(100% - 30px); } .reservation-hero{ grid-template-columns:1fr 300px; } }
    @media (max-width: 700px){ .reservation-hero{ display:block; } .reservation-hero::after{ display:block; height:120px; min-height:0; margin-top:18px; border-radius:0 0 0 35px; } .reservation-shell{ display:block; } .reservation-summary{ margin-top:14px; } .reservation-tabs{ overflow-x:auto; } .tab-btn{ min-width:120px; } .reservation-progress{ padding:12px; gap:7px; } .progress-copy span{ display:none; } .panel-row,.reservation-card-grid{ grid-template-columns:1fr; } .summary-card{ position:static; } }

    .details-mode .reservation-panel { display:none; }
    .details-mode .reservation-progress .progress-step.active .progress-number { background:#e3e7f0; color:#71809a; }
    .details-mode .reservation-progress .progress-step:nth-of-type(2) .progress-number { background:#d20b26; color:#fff; }
    .details-mode .confirmation-modal { position:relative !important; inset:auto !important; display:block !important; width:100% !important; height:auto !important; min-height:0 !important; margin:0 !important; padding:0 !important; align-items:initial !important; justify-content:initial !important; background:transparent !important; overflow:visible !important; z-index:1 !important; }
    .details-mode .confirmation-card { position:relative !important; top:auto !important; left:auto !important; width:100% !important; max-width:none !important; max-height:none !important; box-sizing:border-box; margin:0 !important; padding:16px !important; border-radius:0 0 10px 10px !important; background:#fff !important; box-shadow:0 5px 14px rgba(24,36,64,.04) !important; }
    .details-mode .confirmation-header { margin-bottom:4px !important; }
    .details-mode .confirmation-header h3 { font-size:14px !important; text-transform:uppercase; }
    .details-mode .confirmation-close { display:none; }
    .details-mode .confirmation-text { margin-bottom:14px !important; font-size:10px !important; }
    .details-mode .confirmation-grid { display:none !important; }
    .details-mode .confirmation-item { min-height:80px; padding:10px !important; border:1px solid #e6eaf0; border-radius:5px; background:#fff; }
    .details-mode .confirmation-item > div { padding:0 !important; border:0 !important; background:transparent !important; font-size:10px; }
    .details-mode .confirmation-total-row { display:none !important; }
    .details-mode .payment-method-section { margin:12px 0; }
    .details-mode .payment-method-options { grid-template-columns:repeat(5,minmax(0,1fr)); gap:6px; }
    .details-mode .payment-method-option { padding:8px 5px; text-align:center; font-size:9px; }
    .details-mode .confirmation-actions { margin-top:14px; }
    .details-mode .confirmation-actions button { padding:9px 14px !important; font-size:10px; }
    .details-form-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; margin:12px 0; }
    .details-form-section { padding:10px; border:1px solid #e6eaf0; border-radius:5px; background:#fff; }
    .details-form-section h4 { margin:0 0 8px; color:#273047; font-size:10px; text-transform:uppercase; }
    .details-form-section label { display:block; margin:6px 0 3px; color:#384359; font-size:9px; font-weight:700; }
    .details-form-section input, .details-form-section select, .details-form-section textarea { width:100%; box-sizing:border-box; border:1px solid #dfe4ec; border-radius:5px; padding:7px; color:#5d687d; font-size:9px; }
    .details-form-section textarea { min-height:48px; resize:vertical; }
    .details-form-section input[type="checkbox"] { width:auto; margin-right:5px; accent-color:#d20b26; }
    .details-form-section > small { display:block; margin-top:6px; color:#788398; font-size:9px; }
    .details-addons { display:flex; flex-direction:column; gap:6px; }
    .details-addons label { margin:0; }
    @media (max-width:700px){ .details-mode .confirmation-grid,.details-form-grid{ grid-template-columns:1fr !important; } .details-mode .payment-method-options{ grid-template-columns:1fr 1fr; } }

    .details-mode .confirmation-header h3 { font-size:16px !important; }
    .details-mode .confirmation-text { font-size:11px !important; }
    .details-mode .confirmation-label { font-size:10px !important; }
    .details-mode .confirmation-item > div { font-size:11px !important; }
    .details-mode .details-form-section h4 { font-size:11px; }
    .details-mode .details-form-section,
    .details-mode .details-form-section strong { font-size:11px; }
    .details-mode .details-form-section label,
    .details-mode .details-form-section input,
    .details-mode .details-form-section select,
    .details-mode .details-form-section textarea { font-size:10px; }
    .details-mode .details-form-section > small { font-size:10px; }
    .details-mode .payment-method-option { font-size:10px; }
    .details-mode .confirmation-actions button { font-size:11px; }

    .reservation-page:not(.details-mode) .tab-btn { font-size:14px; }
    .reservation-page:not(.details-mode) .panel-header h3 { font-size:18px; }
    .reservation-page:not(.details-mode) .panel-header p { font-size:12px; }
    .reservation-page:not(.details-mode) .field-label { font-size:12px; }
    .reservation-page:not(.details-mode) .field-input { font-size:12px; }
    .reservation-page:not(.details-mode) .reservation-card h4 { font-size:14px; }
    .reservation-page:not(.details-mode) .reservation-card-meta { font-size:11px; }
    .reservation-page:not(.details-mode) .reservation-card-body > p { font-size:11px; }
    .reservation-page:not(.details-mode) .reservation-card .price { font-size:13px; }
    .reservation-page:not(.details-mode) .select-option-btn { font-size:12px; }
    .reservation-page:not(.details-mode) .summary-item-title { font-size:12px; }
    .reservation-page:not(.details-mode) .summary-item-subtitle,
    .reservation-page:not(.details-mode) .summary-item-caption { font-size:11px; }
    .reservation-page:not(.details-mode) .summary-item-price { font-size:12px; }
    .reservation-page:not(.details-mode) .summary-note { font-size:11px; }
    .reservation-page:not(.details-mode) #confirmReservationBtn,
    .reservation-page:not(.details-mode) .summary-clear { font-size:12px; }

    .reservation-progress { gap:22px; padding:22px 30px; }
    .progress-step { gap:13px; }
    .progress-number { width:44px; height:44px; flex-basis:44px; font-size:18px; }
    .progress-copy { gap:4px; }
    .progress-copy strong { font-size:15px; }
    .progress-copy span { font-size:12px; }
    .details-mode .details-form-section h4 { font-size:12px; }
    .details-mode .details-form-section,
    .details-mode .details-form-section strong { font-size:12px; }
    .details-mode .details-form-section label,
    .details-mode .details-form-section input,
    .details-mode .details-form-section select,
    .details-mode .details-form-section textarea { font-size:11px; }
    .details-mode .details-form-section > small { font-size:11px; }
    .details-mode .payment-method-option { font-size:11px; }
    .details-mode .confirmation-actions button { font-size:12px; }

    /* Improve readability while preserving the compact layout. */
    .reservation-page .tab-btn { font-size:13px; }
    .reservation-page .panel-header h3 { font-size:17px; }
    .reservation-page .panel-header p { font-size:11px; }
    .reservation-page .field-label { font-size:11px; }
    .reservation-page .field-input { font-size:11px; }
    .reservation-page .reservation-card h4 { font-size:13px; }
    .reservation-page .reservation-card-meta { font-size:10px; }
    .reservation-page .reservation-card-body > p { font-size:10px; }
    .reservation-page .reservation-card .price { font-size:12px; }
    .reservation-page .select-option-btn { font-size:11px; }
    .reservation-page .summary-item-title { font-size:11px; }
    .reservation-page .summary-item-subtitle,
    .reservation-page .summary-item-caption { font-size:10px; }
    .reservation-page .summary-item-price { font-size:11px; }
    .reservation-page .summary-note { font-size:10px; }
    .reservation-page #confirmReservationBtn,
    .reservation-page .summary-clear { font-size:11px; }

    /* Increase summary and details text for easier reading. */
    .reservation-page .summary-card h3 { font-size:16px; }
    .reservation-page .summary-item-title { font-size:13px; }
    .reservation-page .summary-item-subtitle,
    .reservation-page .summary-item-caption { font-size:12px; }
    .reservation-page .summary-item-price { font-size:13px; }
    .reservation-page .summary-total span { font-size:14px; }
    .reservation-page .summary-total strong { font-size:17px; }
    .reservation-page .summary-note { font-size:12px; }
    .reservation-page #confirmReservationBtn,
    .reservation-page .summary-clear { font-size:12px; }
    .details-mode .details-form-section h4 { font-size:13px; }
    .details-mode .details-form-section,
    .details-mode .details-form-section strong { font-size:13px; }
    .details-mode .details-form-section label,
    .details-mode .details-form-section input,
    .details-mode .details-form-section select,
    .details-mode .details-form-section textarea { font-size:12px; }
    .details-mode .details-form-section > small { font-size:12px; }
    .details-mode .details-form-section p { font-size:12px !important; }

    @media (max-width:700px) {
        .reservation-progress { gap:8px; padding:16px 14px; }
        .progress-step { gap:7px; }
        .progress-number { width:36px; height:36px; flex-basis:36px; font-size:15px; }
        .progress-copy strong { font-size:12px; }
        .progress-copy span { display:none; }
    }

</style>

<div class="reservation-page animate-on-scroll">
    <section class="reservation-hero">
        <br>
        <h1>Choose the type of reservation you want to make.</h1>
        <p>Book rooms, add amenities, include an event package, or reserve dining—all in one seamless checkout experience.</p>
    </section>

    @if(session('success'))
        <div class="reservation-alert reservation-alert--success">
            {{ session('success') }}
        </div>
    @endif

    <div class="reservation-shell">
        <div class="reservation-left">
            <div class="reservation-tabs">
                <button type="button" class="tab-btn active" data-tab="room-tab"><span class="tab-icon"><i class="fas fa-bed"></i></span>Rooms</button>
                <button type="button" class="tab-btn" data-tab="amenities-tab"><span class="tab-icon"><i class="fas fa-concierge-bell"></i></span>Amenities</button>
                <button type="button" class="tab-btn" data-tab="event-place-tab"><span class="tab-icon"><i class="fas fa-calendar-check"></i></span>Event/Pool</button>
                <button type="button" class="tab-btn" data-tab="dining-tab"><span class="tab-icon"><i class="fas fa-utensils"></i></span>Dining</button>
            </div>

            <div class="reservation-progress" aria-label="Reservation progress">
                <div class="progress-step active"><span class="progress-number">1</span><span class="progress-copy"><strong>Select</strong><span>Choose your room<br>and dates</span></span></div>
                <span class="progress-line"></span>
                <div class="progress-step"><span class="progress-number">2</span><span class="progress-copy"><strong>Details</strong><span>Provide guest and<br>payment details</span></span></div>
                <span class="progress-line"></span>
                <div class="progress-step"><span class="progress-number">3</span><span class="progress-copy"><strong>Confirm</strong><span>Review and confirm<br>your reservation</span></span></div>
            </div>

            <div id="room-tab" class="reservation-panel active">
                <div class="panel-header">
                    <h3><i class="fas fa-bed"></i> Select Your Room and Dates</h3>
                    <p>Choose your preferred dates and room that suits your needs.</p>
                </div>
                <div class="panel-row">
                    <div class="panel-field">
                        <label class="field-label">Check-in</label>
                        <input id="checkIn" type="date" class="field-input" />
                    </div>
                    <div class="panel-field">
                        <label class="field-label">Check-out</label>
                        <input id="checkOut" type="date" class="field-input" />
                    </div>
                    <div class="panel-field">
                        <label class="field-label">Add a Person</label>
                        <select id="additionalGuests" class="field-input">
                            <option value="0" selected>No extra persons</option>
                            <option value="1">1 Person (₱650)</option>
                            <option value="2">2 Persons (₱1,300)</option>
                            <option value="3">3 Persons (₱1,950)</option>
                            <option value="4">4 Persons (₱2,600)</option>
                        </select>
                    </div>
                </div>

                <div class="reservation-card-grid">
                    @foreach($rooms as $room)
                        <article class="reservation-card" data-category="room" data-price="{{ $room->price }}" data-name="{{ $room->room_type }}" data-room-id="{{ $room->id }}">
                            <img src="{{ $room->image ? asset(str_starts_with($room->image, 'rooms/') ? 'storage/' . $room->image : $room->image) : asset('image/Royal-Suite-room.jpg') }}" alt="{{ $room->room_type }}">
                            <div class="reservation-card-body">
                                <h4>{{ $room->room_type }}</h4>
                                <div class="reservation-card-meta">
                                    <span><i class="fas fa-users"></i>{{ $room->capacity ?? 2 }} Guests</span>
                                    <span><i class="fas fa-bed"></i>1 Queen Bed</span>
                                </div>
                                <p>{{ $room->description ?? 'Premium stay with comfortable bedding and modern amenities.' }}</p>
                                <div class="reservation-card-footer">
                                    <span class="price">₱{{ number_format($room->price, 0) }}/night</span>
                                    <button type="button" class="select-option-btn" data-title="{{ $room->room_type }}" data-price="{{ $room->price }}" data-room-id="{{ $room->id }}">Add to Reservation</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div id="amenities-tab" class="reservation-panel">
                <div class="panel-header">
                    <h3>Amenities</h3>
                    <p>Choose amenities to enhance your stay.</p>
                </div>
                <div class="reservation-card-grid">
                    @foreach($amenities as $amenity)
                        <article class="reservation-card" data-category="amenities" data-price="{{ $amenity->price }}" data-title="{{ $amenity->name }}">
                            <img src="{{ $amenity->image ? asset('storage/' . $amenity->image) : asset('image/Royal-Suite-room.jpg') }}" alt="{{ $amenity->name }}">
                            <div class="reservation-card-body">
                                <h4>{{ $amenity->name }}</h4>
                                <p>{{ $amenity->description ?: 'Premium guest add-on for your stay.' }}</p>
                                <p class="text-muted">{{ $amenity->type ?: 'Available for your reservation' }}</p>
                                <div class="reservation-card-footer">
                                    <span class="price">₱{{ number_format($amenity->price, 0) }}</span>
                                    <button type="button" class="select-option-btn" data-title="{{ $amenity->name }}" data-price="{{ $amenity->price }}">Add to Reservation</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div id="event-place-tab" class="reservation-panel">
                <div class="panel-header">
                    <h3>Event Place</h3>
                    <p>Select an event package for your occasion.</p>
                </div>
                <div class="reservation-card-grid">
                    @foreach($events as $event)
                        <article class="reservation-card" data-category="event_place" data-price="{{ $event->price }}" data-title="{{ $event->name }}">
                            <img src="{{ $event->image ? asset('storage/' . $event->image) : asset('image/Royal-Suite-room.jpg') }}" alt="{{ $event->name }}">
                            <div class="reservation-card-body">
                                <h4>{{ $event->name }}</h4>
                                <p>{{ $event->description ?: 'Flexible event venue for your occasion.' }}</p>
                                <p class="text-muted">{{ $event->type ?: 'Event venue package' }}</p>
                                <div class="reservation-card-footer">
                                    <span class="price">₱{{ number_format($event->price, 0) }}</span>
                                    <button type="button" class="select-option-btn" data-title="{{ $event->name }}" data-price="{{ $event->price }}">Add to Reservation</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div id="dining-tab" class="reservation-panel">
                <div class="panel-header">
                    <h3>Dining</h3>
                    <p>Choose a dining experience for your stay.</p>
                </div>
                <div class="reservation-card-grid">
                    @foreach($dining as $meal)
                        <article class="reservation-card" data-category="dining" data-price="{{ $meal->price }}" data-title="{{ $meal->name }}">
                            <img src="{{ $meal->image ? asset('storage/' . $meal->image) : asset('image/Royal-Suite-room.jpg') }}" alt="{{ $meal->name }}">
                            <div class="reservation-card-body">
                                <h4>{{ $meal->name }}</h4>
                                <p>{{ $meal->description ?: 'A dining option for your stay.' }}</p>
                                <p class="text-muted">{{ $meal->type ?: 'Dining package' }}</p>
                                <div class="reservation-card-footer">
                                    <span class="price">₱{{ number_format($meal->price, 0) }}</span>
                                    <button type="button" class="select-option-btn" data-title="{{ $meal->name }}" data-price="{{ $meal->price }}">Add to Reservation</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <aside class="reservation-summary">
            <div class="summary-card">
                <h3>Reservation Summary</h3>
                <article class="summary-item-card">
                    <div class="summary-item-card-left">
                        <div class="summary-item-icon"><i class="fas fa-bed"></i></div>
                        <div class="summary-item-details">
                            <p class="summary-item-title">Rooms</p>
                            <p class="summary-item-subtitle" id="summaryRoom">None</p>
                            <p class="summary-item-caption" id="summaryRoomDetails">Choose a room and dates</p>
                        </div>
                    </div>
                    <div class="summary-item-card-right">
                        <span class="summary-item-price" id="summaryRoomPrice">₱0</span>
                        <button type="button" class="summary-edit-btn" data-target="room-tab">Edit</button>
                    </div>
                </article>
                <article class="summary-item-card">
                    <div class="summary-item-card-left">
                        <div class="summary-item-icon"><i class="fas fa-concierge-bell"></i></div>
                        <div class="summary-item-details">
                            <p class="summary-item-title">Amenities</p>
                            <p class="summary-item-subtitle" id="summaryItems">0 selected</p>
                        </div>
                    </div>
                    <div class="summary-item-card-right">
                        <span class="summary-item-price" id="summaryAmenitiesPrice">₱0</span>
                        <button type="button" class="summary-edit-btn" data-target="amenities-tab">Edit</button>
                    </div>
                </article>
                <article class="summary-item-card">
                    <div class="summary-item-card-left">
                        <div class="summary-item-icon"><i class="fas fa-user-plus"></i></div>
                        <div class="summary-item-details">
                            <p class="summary-item-title">Extra Person</p>
                            <p class="summary-item-subtitle" id="summaryAdditionalGuests">None</p>
                        </div>
                    </div>
                    <div class="summary-item-card-right">
                        <span class="summary-item-price" id="summaryAdditionalGuestsPrice">₱0</span>
                    </div>
                </article>
                <article class="summary-item-card">
                    <div class="summary-item-card-left">
                        <div class="summary-item-icon"><i class="fas fa-calendar-check"></i></div>
                        <div class="summary-item-details">
                            <p class="summary-item-title">Event Place</p>
                            <p class="summary-item-subtitle" id="summaryEvent">None</p>
                        </div>
                    </div>
                    <div class="summary-item-card-right">
                        <span class="summary-item-price" id="summaryEventPrice">₱0</span>
                        <button type="button" class="summary-edit-btn" data-target="event-place-tab">Edit</button>
                    </div>
                </article>
                <article class="summary-item-card">
                    <div class="summary-item-card-left">
                        <div class="summary-item-icon"><i class="fas fa-utensils"></i></div>
                        <div class="summary-item-details">
                            <p class="summary-item-title">Dining</p>
                            <p class="summary-item-subtitle" id="summaryDining">None</p>
                        </div>
                    </div>
                    <div class="summary-item-card-right">
                        <span class="summary-item-price" id="summaryDiningPrice">₱0</span>
                        <button type="button" class="summary-edit-btn" data-target="dining-tab">Edit</button>
                    </div>
                </article>
                <div class="summary-total">
                    <span>Total</span>
                    <strong id="summaryTotal">₱0</strong>
                </div>
                <p class="summary-note">Your reservation details will be submitted as a request. A staff member will contact you to confirm availability.</p>
                <div class="summary-actions">
                    <button type="button" id="confirmReservationBtn">Continue to Details <i class="fas fa-arrow-right"></i></button>
                    <button type="button" id="clearReservationBtn" class="summary-clear">Clear All</button>
                </div>
            </div>
        </aside>
    </div>

    <div class="confirmation-modal" id="confirmationModal" aria-hidden="true" style="position:fixed; inset:0; justify-content:center; align-items:center; background:rgba(15,23,42,0.6); padding:24px; z-index:9999; display:none;">
        <div class="confirmation-card" style="width:min(720px,100%); background:#ffffff; border-radius:28px; padding:32px; box-shadow:0 32px 80px rgba(15,23,42,0.18); border:1px solid rgba(15,23,42,0.08);">
            <div class="confirmation-header" style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:16px;">
                <h3 style="margin:0; font-size:1.5rem; color:#111827;">Confirm Your Reservation</h3>
                <button type="button" class="confirmation-close" id="confirmationCloseBtn" style="border:none; background:transparent; color:#334155; font-size:1.5rem; cursor:pointer; line-height:1;">×</button>
            </div>
            <p class="confirmation-text" style="margin:0 0 24px; color:#4b5563; font-size:0.98rem; line-height:1.6;">Review all reservation details before submitting.</p>
            <div class="confirmation-grid" style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; margin-bottom:20px;">
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Reservation ID</span>
                    <div id="confirmReservationId" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">RES-0000</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Status</span>
                    <div id="confirmStatus" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">Reserved</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Room</span>
                    <div id="confirmRoom" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">None</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Guests</span>
                    <div id="confirmGuests" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">0 Guests</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Arriving On</span>
                    <div id="confirmArrivingOn" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">—</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Check-out</span>
                    <div id="confirmCheckOut" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">—</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Amenities</span>
                    <div id="confirmAmenities" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">0 selected</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Event / Dining</span>
                    <div id="confirmEventDining" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">None</div>
                </div>
            </div>

            <div class="details-form-grid">
                <section class="details-form-section">
                    <h4><i class="fas fa-bed"></i> Room Summary</h4>
                    <strong id="detailsRoomName">None selected</strong>
                    <small id="detailsRoomDates">Choose your dates</small>
                    <small id="detailsRoomGuests">2 Guests</small>
                </section>
                <section class="details-form-section">
                    <h4><i class="fas fa-user"></i> Guest Information</h4>
                    <label for="detailsGuestName">Guest Name</label>
                    <input id="detailsGuestName" type="text" value="{{ auth()->user()->name ?? '' }}" placeholder="Enter guest name">
                    <label for="detailsGuestEmail">Email</label>
                    <input id="detailsGuestEmail" type="email" value="{{ auth()->user()->email ?? '' }}" placeholder="Enter email address">
                    <label for="detailsGuestPhone">Mobile Number</label>
                    <input id="detailsGuestPhone" type="tel" value="{{ auth()->user()->contact_no ?? '' }}" placeholder="Enter mobile number">
                </section>
                <section class="details-form-section">
                    <h4><i class="fas fa-clock"></i> Estimated Arrival</h4>
                    <select id="detailsArrival">
                        <option value="15:00">3:00 PM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="18:00">6:00 PM</option>
                    </select>
                    <h4 style="margin-top:12px;"><i class="fas fa-plus-circle"></i> Add-ons</h4>
                    <div class="details-addons">
                        <label><input type="checkbox"> Extra Pillow</label>
                        <label><input type="checkbox"> Towels</label>
                        <label><input type="checkbox"> Breakfast</label>
                    </div>
                </section>
            </div>
            <div class="details-form-grid">
                <section class="details-form-section">
                    <h4><i class="fas fa-comment-dots"></i> Special Request</h4>
                    <textarea id="detailsSpecialRequest" placeholder="Enter any special request..."></textarea>
                </section>
                <section class="details-form-section">
                    <h4><i class="fas fa-shield-alt"></i> Cancellation Policy</h4>
                    <p style="margin:0;color:#788398;font-size:9px;line-height:1.4;">Free cancellation until 24 hours before check-in.</p>
                    <label><input id="detailsTerms" type="checkbox"> I agree to the Terms & Conditions</label>
                </section>
                <section class="details-form-section">
                    <h4><i class="fas fa-receipt"></i> Price Summary</h4>
                    <p style="display:flex;justify-content:space-between;margin:4px 0;font-size:9px;">Room <strong id="detailsRoomPrice">₱0</strong></p>
                    <p style="display:flex;justify-content:space-between;margin:4px 0;font-size:9px;">Add-ons <strong id="detailsAddonsPrice">₱0</strong></p>
                    <p style="display:flex;justify-content:space-between;margin:7px 0 0;padding-top:6px;border-top:1px solid #e6eaf0;font-size:10px;">TOTAL <strong id="detailsTotal">₱0</strong></p>
                </section>
            </div>

            <div class="confirmation-total-row" style="display:flex; justify-content:space-between; align-items:center; padding-top:16px; border-top:1px solid #e5e7eb; margin-bottom:20px;">
                <div>
                    <span class="confirmation-total-label" style="color:#6b7280; font-weight:700; font-size:0.9rem;">Total Amount</span>
                    <strong class="confirmation-total-amount" id="confirmTotalAmount" style="font-size:1.5rem; color:#111827; font-weight:800; display:block; margin-top:8px;">₱0</strong>
                </div>
            </div>

            <div class="payment-method-section">
                <h4>Payment Method</h4>
                <div class="payment-method-options">
                    <button type="button" class="payment-method-option selected" data-payment-method="Cash / Pay at Hotel">Cash / Pay at Hotel</button>
                    <button type="button" class="payment-method-option" data-payment-method="GCash">GCash</button>
                    <button type="button" class="payment-method-option" data-payment-method="Maya">Maya</button>
                    <button type="button" class="payment-method-option" data-payment-method="Credit / Debit Card">Credit / Debit Card</button>
                    <button type="button" class="payment-method-option" data-payment-method="Bank Transfer">Bank Transfer</button>
                </div>
            </div>

            <div class="confirmation-actions" style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" id="modalConfirmBtn" class="confirm-submit-btn" style="border-radius:999px; padding:14px 26px; font-weight:700; border:none; cursor:pointer; background:#dc2626; color:#ffffff;">Submit Reservation</button>
                <button type="button" id="modalCancelBtn" class="confirm-cancel-btn" style="border-radius:999px; padding:14px 26px; font-weight:700; border:none; cursor:pointer; background:#f3f4f6; color:#111827;">Back to Select</button>
            </div>
        </div>
    </div>
</div>

<form id="reservationForm" action="{{ route('reservation.store') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="submission_token" value="{{ \Illuminate\Support\Str::uuid() }}">
    <input type="hidden" name="room_id" id="reservationRoomId">
    <input type="hidden" name="check_in" id="reservationCheckIn">
    <input type="hidden" name="check_out" id="reservationCheckOut">
    <input type="hidden" name="guest_name" id="reservationGuestName" value="{{ auth()->user()->name ?? 'Guest' }}">
    <input type="hidden" name="guest_email" id="reservationGuestEmail" value="{{ auth()->user()->email ?? 'guest@example.com' }}">
    <input type="hidden" name="guest_phone" id="reservationGuestPhone" value="{{ auth()->user()->contact_no ?? '0000000000' }}">
    <input type="hidden" name="total_amount" id="reservationTotalAmount">
    <input type="hidden" name="special_requests" id="reservationSpecialRequests" value="">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reservationPage = document.querySelector('.reservation-page');
        const reservationLeft = document.querySelector('.reservation-left');
        const tabs = document.querySelectorAll('.tab-btn');
        const panels = document.querySelectorAll('.reservation-panel');
        const checkIn = document.getElementById('checkIn');
        const checkOut = document.getElementById('checkOut');
        const additionalGuests = document.getElementById('additionalGuests');
        const summaryRoom = document.getElementById('summaryRoom');
        const summaryRoomDetails = document.getElementById('summaryRoomDetails');
        const summaryRoomPrice = document.getElementById('summaryRoomPrice');
        const summaryItems = document.getElementById('summaryItems');
        const summaryAdditionalGuests = document.getElementById('summaryAdditionalGuests');
        const summaryAdditionalGuestsPrice = document.getElementById('summaryAdditionalGuestsPrice');
        const summaryEvent = document.getElementById('summaryEvent');
        const summaryDining = document.getElementById('summaryDining');
        const summaryAmenitiesPrice = document.getElementById('summaryAmenitiesPrice');
        const summaryEventPrice = document.getElementById('summaryEventPrice');
        const summaryDiningPrice = document.getElementById('summaryDiningPrice');
        const summaryTotal = document.getElementById('summaryTotal');
        const summaryEditButtons = document.querySelectorAll('.summary-edit-btn');
        const confirmBtn = document.getElementById('confirmReservationBtn');
        const clearBtn = document.getElementById('clearReservationBtn');
        const reservationForm = document.getElementById('reservationForm');
        const reservationRoomId = document.getElementById('reservationRoomId');
        const reservationCheckIn = document.getElementById('reservationCheckIn');
        const reservationCheckOut = document.getElementById('reservationCheckOut');
        const reservationGuestName = document.getElementById('reservationGuestName');
        const reservationGuestEmail = document.getElementById('reservationGuestEmail');
        const reservationGuestPhone = document.getElementById('reservationGuestPhone');
        const reservationTotalAmount = document.getElementById('reservationTotalAmount');
        const reservationSpecialRequests = document.getElementById('reservationSpecialRequests');
        const paymentMethodChoices = document.querySelectorAll('.payment-method-option');
        const confirmationModal = document.getElementById('confirmationModal');
        const confirmationOriginalParent = confirmationModal.parentElement;
        const confirmationCloseBtn = document.getElementById('confirmationCloseBtn');
        const modalConfirmBtn = document.getElementById('modalConfirmBtn');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        const confirmReservationId = document.getElementById('confirmReservationId');
        const confirmRoom = document.getElementById('confirmRoom');
        const confirmGuests = document.getElementById('confirmGuests');
        const confirmArrivingOn = document.getElementById('confirmArrivingOn');
        const confirmCheckOut = document.getElementById('confirmCheckOut');
        const confirmStatus = document.getElementById('confirmStatus');
        const confirmPaymentMethod = document.getElementById('confirmPaymentMethod');
        const confirmAmenities = document.getElementById('confirmAmenities');
        const confirmEventDining = document.getElementById('confirmEventDining');
        const confirmTotalAmount = document.getElementById('confirmTotalAmount');
        const detailsRoomName = document.getElementById('detailsRoomName');
        const detailsRoomDates = document.getElementById('detailsRoomDates');
        const detailsRoomGuests = document.getElementById('detailsRoomGuests');
        const detailsRoomPrice = document.getElementById('detailsRoomPrice');
        const detailsAddonsPrice = document.getElementById('detailsAddonsPrice');
        const detailsTotal = document.getElementById('detailsTotal');
        const detailsGuestName = document.getElementById('detailsGuestName');
        const detailsGuestEmail = document.getElementById('detailsGuestEmail');
        const detailsGuestPhone = document.getElementById('detailsGuestPhone');
        const detailsSpecialRequest = document.getElementById('detailsSpecialRequest');
        const detailsArrival = document.getElementById('detailsArrival');
        const detailsTerms = document.getElementById('detailsTerms');

        let selectedRoom = null;
        let roomPrice = 0;
        let selectedAmenities = [];
        let selectedEvent = null;
        let selectedDining = null;
        let selectedPaymentMethod = 'Cash / Pay at Hotel';

        const items = document.querySelectorAll('.select-option-btn');

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                tabs.forEach(btn => btn.classList.remove('active'));
                panels.forEach(panel => panel.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });

        const updateSummary = () => {
            summaryRoom.textContent = selectedRoom ? selectedRoom : 'None';
            summaryRoomDetails.textContent = selectedRoom ? `${checkIn.value || 'Date'} – ${checkOut.value || 'Date'}${additionalGuests.value > 0 ? ` • ${additionalGuests.value} Extra Person(s)` : ''}` : 'Choose a room and dates';
            summaryRoomPrice.textContent = `₱${roomPrice.toLocaleString()}`;
            summaryItems.textContent = selectedAmenities.length > 0 ? `${selectedAmenities.length} selected` : '0 selected';
            summaryAdditionalGuests.textContent = additionalGuests.value > 0 ? `${additionalGuests.value} added` : 'None';
            summaryEvent.textContent = selectedEvent ? selectedEvent.title : 'None';
            summaryDining.textContent = selectedDining ? selectedDining.title : 'None';
            summaryAmenitiesPrice.textContent = `₱${selectedAmenities.reduce((sum, item) => sum + item.price, 0).toLocaleString()}`;
            summaryAdditionalGuestsPrice.textContent = `₱${(Number(additionalGuests.value) * 650).toLocaleString()}`;
            summaryEventPrice.textContent = `₱${(selectedEvent ? selectedEvent.price : 0).toLocaleString()}`;
            summaryDiningPrice.textContent = `₱${(selectedDining ? selectedDining.price : 0).toLocaleString()}`;

            const total = calculateTotal();
            detailsRoomName.textContent = selectedRoom || 'None selected';
            detailsRoomDates.textContent = `${checkIn.value || 'Check-in'} to ${checkOut.value || 'Check-out'}`;
            detailsRoomGuests.textContent = `${Number(additionalGuests.value) + 2} Guests`;
            detailsRoomPrice.textContent = `₱${roomPrice.toLocaleString()}`;
            detailsAddonsPrice.textContent = `₱${(total - roomPrice).toLocaleString()}`;
            detailsTotal.textContent = `₱${total.toLocaleString()}`;

            confirmReservationId.textContent = selectedRoom ? `RES-${Math.floor(Math.random() * 9000) + 1000}` : 'RES-0000';
            confirmRoom.textContent = selectedRoom ? selectedRoom : 'None';
            confirmArrivingOn.textContent = checkIn.value || '—';
            confirmCheckOut.textContent = checkOut.value || '—';
            confirmGuests.textContent = additionalGuests.value > 0 ? `${additionalGuests.value} Extra Person(s)` : 'None';
            confirmStatus.textContent = 'Reserved';
            confirmPaymentMethod.textContent = selectedPaymentMethod;
            confirmAmenities.textContent = selectedAmenities.length > 0 ? `${selectedAmenities.length} selected` : '0 selected';
            confirmEventDining.textContent = (() => {
                const parts = [];
                if (selectedEvent) parts.push(selectedEvent.title);
                if (selectedDining) parts.push(selectedDining.title);
                return parts.length ? parts.join(' / ') : 'None';
            })();
            confirmTotalAmount.textContent = `₱${calculateTotal().toLocaleString()}`;

            summaryTotal.textContent = `₱${total.toLocaleString()}`;
            reservationTotalAmount.value = total;
            reservationCheckIn.value = checkIn.value;
            reservationCheckOut.value = checkOut.value;
            reservationTotalAmount.value = total;
            reservationCheckIn.value = checkIn.value;
            reservationCheckOut.value = checkOut.value;
        }

        checkIn.addEventListener('change', updateSummary);
        checkOut.addEventListener('change', updateSummary);
        additionalGuests.addEventListener('change', updateSummary);
        paymentMethodChoices.forEach(choice => choice.addEventListener('click', function () {
            selectedPaymentMethod = this.dataset.paymentMethod;
            paymentMethodChoices.forEach(button => button.classList.toggle('selected', button === this));
            updateSummary();
        }));

        const calculateTotal = () => {
            const amenitiesTotal = selectedAmenities.reduce((sum, item) => sum + item.price, 0);
            const eventTotal = selectedEvent ? selectedEvent.price : 0;
            const diningTotal = selectedDining ? selectedDining.price : 0;
            const extraGuestsTotal = Number(additionalGuests.value) * 650;
            return roomPrice + amenitiesTotal + eventTotal + diningTotal + extraGuestsTotal;
        };

        items.forEach(button => {
            button.addEventListener('click', function () {
                const title = this.dataset.title;
                const price = Number(this.dataset.price);
                const card = this.closest('.reservation-card');
                const category = card.dataset.category;
                const dataId = `${category}:${title}`;

                if (category === 'room') {
                    selectedRoom = title;
                    roomPrice = price;
                    reservationRoomId.value = card.dataset.roomId || '';
                    items.forEach(btn => {
                        if (btn.closest('.reservation-card').dataset.category === 'room') {
                            btn.textContent = 'Add to Reservation';
                            btn.disabled = false;
                        }
                    });
                    this.textContent = 'Selected';
                    this.disabled = true;
                } else if (category === 'amenities') {
                    const itemIndex = selectedAmenities.findIndex(item => item.id === dataId);
                    if (itemIndex === -1) {
                        selectedAmenities.push({ id: dataId, title, price });
                        this.textContent = 'Added';
                        this.disabled = true;
                    }
                } else if (category === 'event_place') {
                    if (selectedEvent) {
                        const previousEventBtn = Array.from(items).find(btn => btn.closest('.reservation-card').dataset.category === 'event_place' && btn.textContent === 'Selected');
                        if (previousEventBtn) {
                            previousEventBtn.textContent = 'Add to Reservation';
                            previousEventBtn.disabled = false;
                        }
                    }
                    selectedEvent = { title, price };
                    this.textContent = 'Selected';
                    this.disabled = true;
                } else if (category === 'dining') {
                    if (selectedDining) {
                        const previousDiningBtn = Array.from(items).find(btn => btn.closest('.reservation-card').dataset.category === 'dining' && btn.textContent === 'Selected');
                        if (previousDiningBtn) {
                            previousDiningBtn.textContent = 'Add to Reservation';
                            previousDiningBtn.disabled = false;
                        }
                    }
                    selectedDining = { title, price };
                    this.textContent = 'Selected';
                    this.disabled = true;
                }

                updateSummary();
            });
        });

        summaryEditButtons.forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.dataset.target;
                if (!targetId) {
                    return;
                }

                tabs.forEach(tab => tab.classList.remove('active'));
                panels.forEach(panel => panel.classList.remove('active'));

                const targetTab = Array.from(tabs).find(tab => tab.dataset.tab === targetId);
                const targetPanel = document.getElementById(targetId);
                if (targetTab && targetPanel) {
                    targetTab.classList.add('active');
                    targetPanel.classList.add('active');
                    targetPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        const openConfirmationModal = () => {
            reservationLeft.appendChild(confirmationModal);
            reservationPage.classList.add('details-mode');
            confirmationModal.classList.add('open');
            confirmationModal.style.display = 'flex';
            confirmationModal.setAttribute('aria-hidden', 'false');
        };

        const closeConfirmationModal = () => {
            confirmationOriginalParent.appendChild(confirmationModal);
            reservationPage.classList.remove('details-mode');
            confirmationModal.classList.remove('open');
            confirmationModal.style.display = 'none';
            confirmationModal.setAttribute('aria-hidden', 'true');
        };

        confirmBtn.addEventListener('click', function () {
            if (!selectedRoom) {
                alert('Please select a room before confirming your reservation.');
                return;
            }
            if (!checkIn.value || !checkOut.value) {
                alert('Please select check-in and check-out dates.');
                return;
            }
            openConfirmationModal();
        });

        confirmationCloseBtn.addEventListener('click', closeConfirmationModal);
        modalCancelBtn.addEventListener('click', closeConfirmationModal);
        confirmationModal.addEventListener('click', function (event) {
            if (event.target === confirmationModal) {
                closeConfirmationModal();
            }
        });

        modalConfirmBtn.addEventListener('click', function () {
            if (modalConfirmBtn.disabled) {
                return;
            }

            modalConfirmBtn.disabled = true;
            modalConfirmBtn.textContent = 'Submitting...';
            if (!detailsTerms.checked) {
                modalConfirmBtn.disabled = false;
                modalConfirmBtn.textContent = 'Confirm Reservation';
                alert('Please agree to the Terms & Conditions before submitting.');
                return;
            }

            reservationGuestName.value = detailsGuestName.value || 'Guest';
            reservationGuestEmail.value = detailsGuestEmail.value || 'guest@example.com';
            reservationGuestPhone.value = detailsGuestPhone.value || '0000000000';
            reservationSpecialRequests.value = detailsSpecialRequest.value;
            reservationForm.appendChild(Object.assign(document.createElement('input'), {
                type: 'hidden', name: 'check_in_time', value: detailsArrival.value
            }));
            const paymentMethodInput = document.createElement('input');
            paymentMethodInput.type = 'hidden';
            paymentMethodInput.name = 'payment_method';
            paymentMethodInput.value = selectedPaymentMethod;
            reservationForm.appendChild(paymentMethodInput);
            reservationForm.submit();
        });

        clearBtn.addEventListener('click', function () {
            selectedRoom = null;
            selectedAmenities = [];
            selectedEvent = null;
            selectedDining = null;
            selectedPaymentMethod = 'Cash / Pay at Hotel';
            roomPrice = 0;
            checkIn.value = '';
            checkOut.value = '';
            additionalGuests.value = '0';
            items.forEach(btn => {
                btn.textContent = 'Add to Reservation';
                btn.disabled = false;
            });
            paymentMethodChoices.forEach(button => button.classList.toggle('selected', button.dataset.paymentMethod === selectedPaymentMethod));
            updateSummary();
        });

        updateSummary();
    });
</script>

@endsection
