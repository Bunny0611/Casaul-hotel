@extends('app')

@section('content')

@php($guest = auth('guest')->user())

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
    .confirmation-card{ position:relative; width:min(760px,100%); max-height:calc(100vh - 40px); overflow-y:auto; box-sizing:border-box; border-radius:20px; padding:28px; background:linear-gradient(180deg,#fff,#fbfdff); box-shadow: 0 32px 80px rgba(2,6,23,0.18); border:1px solid rgba(15,23,42,0.06); }
    .confirmation-item{ border-radius:12px; padding:12px; background:#f8fafc; color:#0f172a; }
    .confirmation-total-amount{ font-size:1.6rem; color:#0f172a; }

    .payment-method-section { margin:20px 0; }
    .payment-method-section h4 { margin:0 0 12px; color:#111827; }
    .payment-method-options { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
    .payment-method-option { padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; background:#fff; color:#374151; cursor:pointer; font-weight:600; }
    .payment-method-option.selected { border-color:#dc2626; background:#fff7f7; color:#111827; }

    .confirm-submit-btn{ background: var(--accent); color:white; border-radius:999px; padding:12px 22px; }
    .confirm-cancel-btn{ background: #f3f4f6; border-radius:999px; padding:12px 22px; }

    .notification-toast { position:absolute; top:14px; left:50%; transform:translateX(-50%); display:none; align-items:center; gap:10px; max-width:calc(100% - 32px); padding:12px 18px; border-radius:10px; border:2px solid #d92d2d; background:#fff; color:#c33; box-shadow:0 10px 22px rgba(15,23,42,0.12); font-size:14px; font-weight:700; line-height:1.4; z-index:10; pointer-events:none; animation:slideDown 0.3s ease-out; }
    .notification-toast.show { display:flex; }
    .notification-toast i { font-size:16px; }
    @keyframes slideDown { from { opacity:0; transform:translateX(-50%) translateY(-10px); } to { opacity:1; transform:translateX(-50%) translateY(0); } }
    @keyframes slideUp { from { opacity:1; transform:translateX(-50%) translateY(0); } to { opacity:0; transform:translateX(-50%) translateY(-10px); } }
    .notification-toast.hide { animation:slideUp 0.3s ease-out forwards; }

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
    .summary-receipt-btn { border:1px solid #ff9aa7; border-radius:7px; padding:9px; background:#fff; color:#d20b26; font-size:10px; font-weight:700; cursor:pointer; }
    .receipt-modal { position:fixed; inset:0; display:none; align-items:center; justify-content:center; padding:20px; background:rgba(15,23,42,.6); z-index:10000; }
    .receipt-modal.open { display:flex; }
    .receipt-card { width:min(720px,100%); max-height:calc(100vh - 40px); overflow-y:auto; padding:28px; border-radius:4px; background:#fff; box-shadow:0 25px 60px rgba(15,23,42,.2); }
    .receipt-header { position:relative; display:block; padding-bottom:14px; border-bottom:4px solid #c7d8e8; }
    .receipt-brand { margin:0; color:#07549a; font-size:32px; font-weight:400; text-align:center; }
    .receipt-contact { display:flex; justify-content:center; flex-wrap:wrap; gap:18px; margin:12px 0 4px; color:#727b85; font-size:12px; }
    .receipt-contact span { white-space:nowrap; }
    .receipt-contact i { margin-right:5px; color:#727b85; }
    .receipt-subcontact { margin:0; color:#727b85; font-size:12px; text-align:center; }
    .receipt-subcontact i { margin-right:5px; }
    .receipt-heading-row { margin:24px 0 16px; text-align:center; }
    .receipt-title-row { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; margin:0 0 18px; }
    .receipt-paid-by h4, .receipt-booking h4 { margin:0 0 8px; color:#07549a; font-size:14px; }
    .receipt-paid-by p, .receipt-booking p { margin:3px 0; color:#4b5563; font-size:13px; }
    .receipt-heading { margin:0; color:#07549a; font-size:32px; letter-spacing:.04em; }
    .receipt-booking { min-width:220px; }
    .receipt-booking-details { margin:0 0 18px; }
    .receipt-booking p { display:flex; justify-content:space-between; gap:18px; }
    .receipt-booking strong { color:#4b5563; font-weight:600; }
    .receipt-close { position:absolute; top:-8px; right:-8px; border:0; background:transparent; color:#64748b; font-size:22px; cursor:pointer; }
    .receipt-content { color:#566176; font-size:12px; line-height:1.5; }
    .receipt-table { width:100%; border:1px solid #7fa9d0; border-radius:4px; border-spacing:0; overflow:hidden; }
    .receipt-table th { padding:9px 8px; color:#fff; background:#07549a; font-size:11px; text-align:left; }
    .receipt-table td { padding:9px 8px; border-top:1px solid #d9e5ef; color:#4b5563; font-size:12px; }
    .receipt-table th:not(:first-child), .receipt-table td:not(:first-child) { text-align:right; }
    .receipt-table .receipt-total-row td { border-top:2px solid #7fa9d0; color:#07549a; font-weight:800; }
    .receipt-notes { margin-top:18px; }
    .receipt-notes h4 { margin:0 0 6px; color:#07549a; font-size:14px; }
    .receipt-notes p { margin:0; color:#4b5563; font-size:12px; }
    .receipt-actions { display:flex; justify-content:flex-end; gap:8px; padding-top:14px; border-top:1px solid #e5e7eb; }
    .receipt-actions button { border:0; border-radius:7px; padding:10px 16px; color:#fff; background:#d20b26; font-size:11px; font-weight:700; cursor:pointer; }
    .receipt-actions .receipt-print-btn { background:#253570; }
    @media (max-width:700px) { .receipt-card { padding:18px; } .receipt-brand,.receipt-heading { font-size:25px; } .receipt-title-row { align-items:flex-start; flex-direction:column; } .receipt-booking { min-width:0; width:100%; } .receipt-actions button { flex:1; } }
    .summary-clear { border:1px solid #ff9aa7; border-radius:7px; padding:9px; background:#fff; color:#d20b26; font-size:10px; font-weight:700; }

    .dining-qty { display:inline-flex; align-items:center; gap:4px; margin:0; color:#000; }
    .qty-btn { width:20px; min-width:20px; height:20px; display:flex; align-items:center; justify-content:center; border:none !important; border-radius:0; background:transparent !important; color:#000 !important; font-size:18px; font-weight:400; cursor:pointer; line-height:1; padding:0; font-family:inherit; box-shadow:none !important; text-shadow:none !important; -webkit-appearance:none; appearance:none; }
    .dining-quantity { width:22px; min-width:22px; height:20px; padding:0; text-align:center; font-size:12px; font-weight:500; border:none !important; border-radius:0; background:transparent !important; color:#000 !important; font-family:inherit; box-shadow:none !important; text-shadow:none !important; -moz-appearance:textfield; appearance:textfield; -webkit-appearance:none; }
    .dining-quantity::-webkit-outer-spin-button, .dining-quantity::-webkit-inner-spin-button { -webkit-appearance:none; appearance:none; margin:0; }
    .dining-card-footer { display:flex; align-items:center; justify-content:space-between; gap:10px; }
    .dining-card-footer .price { display:inline-block; margin:0; color:#111827; }

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
    .details-form-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; width:100%; margin:12px 0; }
    .details-form-section { min-width:0; padding:12px; border:1px solid #e1e6ef; border-radius:7px; background:#fff; box-shadow:0 2px 5px rgba(24,36,64,.025); }
    .details-form-section h4 { display:flex; align-items:center; gap:5px; margin:0 0 9px; color:#273047; font-size:10px; text-transform:uppercase; letter-spacing:.01em; }
    .details-form-section label { display:block; margin:6px 0 3px; color:#384359; font-size:9px; font-weight:700; }
    .details-form-section input, .details-form-section select, .details-form-section textarea { width:100%; box-sizing:border-box; border:1px solid #dfe4ec; border-radius:5px; padding:7px; color:#5d687d; font-size:9px; }
    .details-form-section textarea { min-height:48px; resize:vertical; }
    .details-form-section input[type="checkbox"] { width:auto; margin-right:5px; accent-color:#d20b26; }
    .details-form-section > small { display:block; margin-top:6px; color:#788398; font-size:9px; }
    .details-form-section input[readonly] { background:#f8fafc; color:#526078; }
    .details-services-summary { min-height:0; }
    .details-services-summary small { line-height:1.35; overflow-wrap:anywhere; }
    .details-summary-title { display:block; margin:0 0 14px; color:#172033; font-size:20px; font-weight:800; }
    .details-summary-room { display:flex; flex-direction:column; gap:0; }
    .details-summary-room-name { margin:0 0 10px; color:#172033; font-size:18px; font-weight:800; }
    .details-summary-row { display:flex; align-items:center; gap:10px; min-width:0; padding:10px 0; border-top:1px solid #edf0f4; }
    .details-summary-row i { width:20px; flex:0 0 20px; color:#2874df; text-align:center; font-size:16px; }
    .details-summary-row span { min-width:0; overflow-wrap:anywhere; }
    .details-summary-label { color:#68738a; font-size:13px; }
    .details-summary-value { margin-left:auto; color:#172033; font-size:13px; font-weight:700; text-align:right; }
    .details-service-item { display:grid; grid-template-columns:38px minmax(0,1fr); gap:12px; padding:10px 0; border-top:1px solid #edf0f4; }
    .details-service-icon { display:grid; place-items:center; width:38px; height:38px; border-radius:10px; background:#f0f3ff; color:#2874df; font-size:18px; }
    .details-service-item.event-service .details-service-icon { background:#f7effb; color:#7b3ca5; }
    .details-service-item.dining-service .details-service-icon { background:#fff4e8; color:#f28c18; }
    .details-service-label { margin:0 0 3px; color:#2874df; font-size:12px; font-weight:800; text-transform:uppercase; }
    .details-service-item.event-service .details-service-label { color:#7b3ca5; }
    .details-service-item.dining-service .details-service-label { color:#f28c18; }
    .details-service-value { display:block; color:#172033; font-size:14px; font-weight:700; line-height:1.4; overflow-wrap:anywhere; }
    .details-service-meta { display:block; margin-top:4px; color:#566176; font-size:12px; line-height:1.5; overflow-wrap:anywhere; }
    .details-guest-info { grid-column:1 / -1; width:100%; box-sizing:border-box; }
    .details-secondary-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; margin:10px 0 12px; }
    .details-secondary-grid .details-form-section { min-width:0; }
    .details-guest-info .guest-info-fields { gap:12px 16px; }
    .guest-info-fields { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; }
    .guest-request-field { grid-column:1 / -1; }
    .guest-info-fields .guest-request-field small { margin-top:4px; }
    .details-addons { display:flex; flex-direction:column; gap:6px; }
    .details-addons label { margin:0; }
    @media (max-width:700px){ .details-mode .confirmation-grid,.details-form-grid,.details-secondary-grid{ grid-template-columns:1fr !important; } .details-mode .payment-method-options{ grid-template-columns:1fr 1fr; } }

    @media (min-width:701px){
        .details-mode .confirmation-card { padding:20px !important; }
        .details-mode .confirmation-header h3 { font-size:17px !important; }
        .details-mode .confirmation-text { margin-bottom:16px !important; }
        .details-mode .details-form-grid { align-items:start; gap:9px; margin:9px 0; }
        .details-mode .details-form-section { min-height:0; }
        .details-mode .details-form-grid:first-of-type { display:block; margin-bottom:10px; }
        .details-mode .details-secondary-grid { grid-template-columns:1fr 1fr; gap:9px; }
            .details-mode .details-request-grid { grid-template-columns:1fr !important; }
            .details-mode .details-request-grid .details-form-section { grid-column:1 / -1; width:100%; box-sizing:border-box; }
    }

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
    .payment-method-panel { margin-top:14px; padding:14px 12px; border-top:1px solid #f0d6da; }
    .payment-method-panel h5 { margin:0 0 12px; color:#d20b26; font-size:13px; text-transform:uppercase; letter-spacing:.04em; }
    .payment-fields { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px 14px; }
    .payment-field label { display:block; margin-bottom:5px; color:#384359; font-size:10px; font-weight:700; }
    .payment-field input, .payment-field select { width:100%; box-sizing:border-box; min-height:34px; border:1px solid #dfe4ec; border-radius:6px; padding:7px 10px; color:#4b5563; background:#fff; font-size:10px; }
    .payment-field input[type="file"] { padding:6px; }
    .payment-field.full-width { grid-column:1 / -1; }
    @media (max-width:700px){ .payment-fields, .guest-info-fields { grid-template-columns:1fr; } .payment-field.full-width, .guest-request-field { grid-column:auto; } }
    .details-mode.confirm-step .reservation-progress .progress-step:nth-of-type(2) .progress-number { background:#e3e7f0; color:#71809a; }
    .details-mode.confirm-step .reservation-progress .progress-step:nth-of-type(3) .progress-number { background:#d20b26; color:#fff; }
    .confirm-step .details-form-grid,
    .confirm-step .payment-method-section { display:none; }
    .details-mode.confirm-step .confirmation-grid { display:grid !important; }
    .details-mode.confirm-step .details-form-grid,
    .details-mode.confirm-step .payment-method-section { display:block; }
    .details-mode.confirm-step .details-guest-info { margin-top:10px; }
    .details-mode.confirm-step .details-secondary-grid { display:grid; }
    .details-mode.confirm-step .details-request-grid { display:grid; }
    .details-mode.confirm-step .payment-method-section { margin-top:12px; }
    .details-mode.confirm-step .payment-method-option { pointer-events:none; }
    .details-mode.confirm-step .details-form-grid,
    .details-mode.confirm-step .details-secondary-grid,
    .details-mode.confirm-step .payment-method-section { display:none !important; }
    .confirmation-review { display:none; }
    .details-mode.confirm-step .confirmation-review { display:block; }
    .review-section { padding:16px 0; border-bottom:1px solid #e5e9f0; }
    .review-section:last-of-type { border-bottom:0; }
    .review-section h4 { display:flex; align-items:center; gap:8px; margin:0 0 12px; color:#172033; font-size:14px; text-transform:uppercase; }
    .review-section h4 i { color:#d20b26; }
    .review-information-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
    .review-field { min-width:0; padding:11px 12px; border:1px solid #e1e6ef; border-radius:7px; background:#f8fafc; }
    .review-field-label { display:block; margin-bottom:4px; color:#788398; font-size:10px; font-weight:700; text-transform:uppercase; }
    .review-field-value { display:block; color:#172033; font-size:13px; font-weight:700; overflow-wrap:anywhere; }
    .review-room-name { margin:0 0 10px; color:#172033; font-size:18px; }
    .review-row { display:flex; justify-content:space-between; gap:16px; padding:8px 0; border-top:1px solid #edf0f4; color:#566176; font-size:12px; }
    .review-row strong { color:#172033; text-align:right; }
    .review-service { display:grid; grid-template-columns:34px minmax(0,1fr); gap:10px; padding:11px 0; border-top:1px solid #edf0f4; }
    .review-service-icon { display:grid; place-items:center; width:34px; height:34px; border-radius:8px; background:#f0f3ff; color:#2874df; }
    .review-service.event-review .review-service-icon { background:#f7effb; color:#7b3ca5; }
    .review-service.dining-review .review-service-icon { background:#fff4e8; color:#f28c18; }
    .review-service h5 { margin:0 0 3px; color:#2874df; font-size:10px; text-transform:uppercase; }
    .review-service.event-review h5 { color:#7b3ca5; }
    .review-service.dining-review h5 { color:#f28c18; }
    .review-service strong, .review-service span { display:block; overflow-wrap:anywhere; }
    .review-service strong { color:#172033; font-size:13px; }
    .review-service span { margin-top:3px; color:#566176; font-size:11px; line-height:1.45; }
    .review-payment-row { display:flex; justify-content:space-between; gap:14px; padding:7px 0; color:#566176; font-size:12px; }
    .review-payment-row strong { color:#172033; text-align:right; }
    .review-payment-total { margin-top:7px; padding-top:11px; border-top:1px solid #dfe4ec; font-size:14px; font-weight:800; }
    .review-payment-total strong { color:#d20b26; font-size:17px; }
    .confirm-payment-details-card { margin-top:10px; padding:10px 12px; border:1px solid #e1e6ef; border-radius:8px; background:#fff; }
    .confirm-payment-detail-row { display:grid; grid-template-columns:20px 125px 12px minmax(0,1fr); align-items:center; gap:6px; min-height:34px; border-bottom:1px solid #edf0f4; color:#172033; font-size:11px; }
    .confirm-payment-detail-row:last-child { border-bottom:0; }
    .confirm-payment-detail-row i { color:#7b61d1; text-align:center; }
    .confirm-payment-detail-label { color:#4b5563; }
    .confirm-payment-detail-value { min-width:0; overflow-wrap:anywhere; font-weight:700; }
    .confirm-payment-proof { display:none; max-width:150px; max-height:90px; margin:10px 0 2px 26px; border:1px solid #e1e6ef; border-radius:5px; object-fit:contain; }
    .review-policy { color:#566176; font-size:12px; line-height:1.5; }
    .review-policy strong { display:block; margin-top:7px; color:#172033; }
    .details-mode.confirm-step .payment-method-panel { display:none; }
    .details-mode.confirm-step .payment-method-panel:not([hidden]) { display:block; }
    .payment-method-section { padding:14px 12px; border:1px solid #f0d6da; border-radius:8px; background:#fff; }
    .payment-method-section > h4 { color:#d20b26; font-size:13px; text-transform:uppercase; letter-spacing:.04em; }
    .payment-method-section > p { margin:-7px 0 12px; color:#788398; font-size:10px; }
    .payment-details { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin-top:14px; padding-top:12px; border-top:1px solid #f0d6da; }
    .payment-details label { display:block; margin-bottom:5px; color:#384359; font-size:10px; font-weight:700; }
    .payment-details input, .payment-details select { width:100%; box-sizing:border-box; min-height:34px; border:1px solid #dfe4ec; border-radius:6px; padding:7px 10px; color:#4b5563; background:#fff; font-size:10px; }
    .payment-details input[readonly] { background:#f3f4f6; }
    @media (max-width:700px){ .payment-details { grid-template-columns:1fr; } }

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
                <button type="button" class="tab-btn" data-tab="event-place-tab"><span class="tab-icon"><i class="fas fa-calendar-check"></i></span>Event Place</button>
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
                <div class="panel-row room-date-row">
                    <div class="panel-field">
                        <label class="field-label">Check-in</label>
                        <input id="checkIn" type="date" class="field-input" />
                    </div>
                    <div class="panel-field">
                        <label class="field-label">Check-out</label>
                        <input id="checkOut" type="date" class="field-input" />
                    </div>
                    <div class="panel-field">
                        <label class="field-label" for="arrivalTime">Arrival Time</label>
                        <input id="arrivalTime" type="time" class="field-input" value="15:00" />
                    </div>
                </div>

                <div class="reservation-card-grid">
                    @foreach($rooms as $room)
                        @php($extraGuestPrice = str_contains(strtolower($room->room_type), 'standard') ? 500 : 650)
                        <article class="reservation-card" data-category="room" data-price="{{ $room->price }}" data-name="{{ $room->room_type }}" data-room-id="{{ $room->id }}" data-extra-guest-price="{{ $extraGuestPrice }}">
                            <img src="{{ $room->image ? asset(str_starts_with($room->image, 'rooms/') ? 'storage/' . $room->image : $room->image) : asset('image/Royal-Suite-room.jpg') }}" alt="{{ $room->room_type }}">
                            <div class="reservation-card-body">
                                <h4>{{ $room->room_type }}</h4>
                                <div class="reservation-card-meta">
                                    <span><i class="fas fa-users"></i>{{ $room->capacity ?? 2 }} Guests</span>
                                    <span><i class="fas fa-bed"></i>1 Queen Bed</span>
                                </div>
                                <p>{{ $room->description ?? 'Premium stay with comfortable bedding and modern amenities.' }}</p>
                                <label class="field-label" for="extraGuests-{{ $room->id }}">Add a Person</label>
                                <select id="extraGuests-{{ $room->id }}" class="field-input room-extra-guests">
                                    <option value="0" selected>No extra persons</option>
                                    <option value="1">1 Person (₱{{ number_format($extraGuestPrice, 0) }})</option>
                                    <option value="2">2 Persons (₱{{ number_format($extraGuestPrice * 2, 0) }})</option>
                                    <option value="3">3 Persons (₱{{ number_format($extraGuestPrice * 3, 0) }})</option>
                                    <option value="4">4 Persons (₱{{ number_format($extraGuestPrice * 4, 0) }})</option>
                                </select>
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
                        <article class="reservation-card" data-category="amenities" data-price="{{ $amenity->price }}" data-title="{{ $amenity->name }}" data-amenity-id="{{ $amenity->id }}" data-capacity="{{ $amenity->capacity ?? '' }}" data-scheduling="{{ $amenity->scheduling_requirement ?? 'No Additional Schedule' }}">
                            <img src="{{ $amenity->image ? asset('storage/' . $amenity->image) : asset('image/Royal-Suite-room.jpg') }}" alt="{{ $amenity->name }}">
                            <div class="reservation-card-body">
                                <h4>{{ $amenity->name }}</h4>
                                <p>{{ $amenity->description ?: 'Premium guest add-on for your stay.' }}</p>
                                <p class="text-muted">₱{{ number_format($amenity->price, 0) }} / {{ strtolower(str_replace('Per ', '', $amenity->pricing_basis ?? 'Stay')) }}</p>
                                @if(($amenity->capacity ?? null) || ($amenity->scheduling_requirement ?? 'No Additional Schedule') !== 'No Additional Schedule')
                                    <div class="amenity-options">
                                        @if($amenity->capacity)
                                            <label class="field-label" for="amenityQuantity-{{ $amenity->id }}">Quantity</label>
                                            <select id="amenityQuantity-{{ $amenity->id }}" class="field-input amenity-quantity" max="{{ $amenity->capacity }}">
                                                @for($quantity = 1; $quantity <= $amenity->capacity; $quantity++)<option value="{{ $quantity }}">{{ $quantity }}</option>@endfor
                                            </select>
                                        @endif
                                        @if(($amenity->scheduling_requirement ?? 'No Additional Schedule') !== 'No Additional Schedule')
                                            <label class="field-label" for="amenityDate-{{ $amenity->id }}">Date</label>
                                            <input id="amenityDate-{{ $amenity->id }}" class="field-input amenity-date" type="date">
                                        @endif
                                        @if(($amenity->scheduling_requirement ?? '') === 'Date & Time Required')
                                            <label class="field-label" for="amenityTime-{{ $amenity->id }}">Time</label>
                                            <input id="amenityTime-{{ $amenity->id }}" class="field-input amenity-time" type="time">
                                        @endif
                                    </div>
                                @endif
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
                        <article class="reservation-card" data-category="event_place" data-price="{{ $event->price }}" data-title="{{ $event->name }}" data-event-id="{{ $event->id }}" data-event-type="{{ $event->event_type }}" data-capacity="{{ $event->capacity }}">
                            <img src="{{ $event->image ? asset('storage/' . $event->image) : asset('image/Royal-Suite-room.jpg') }}" alt="{{ $event->name }}">
                            <div class="reservation-card-body">
                                <h4>{{ $event->name }}</h4>
                                <p>{{ $event->description ?: 'Flexible event venue for your occasion.' }}</p>
                                <p class="text-muted">{{ $event->event_type }} · ₱{{ number_format($event->price, 0) }} / {{ strtolower(str_replace('Per ', '', $event->pricing_basis ?? 'Event')) }} · Maximum {{ $event->capacity }} guests</p>
                                <div class="event-options">
                                    <label class="field-label" for="eventDate-{{ $event->id }}">Event Date</label>
                                    <input id="eventDate-{{ $event->id }}" class="field-input event-date" type="date">
                                    <label class="field-label" for="eventStart-{{ $event->id }}">Start Time</label>
                                    <input id="eventStart-{{ $event->id }}" class="field-input event-start-time" type="time">
                                    <label class="field-label" for="eventEnd-{{ $event->id }}">End Time</label>
                                    <input id="eventEnd-{{ $event->id }}" class="field-input event-end-time" type="time">
                                    <label class="field-label" for="eventGuests-{{ $event->id }}">Number of Guests</label>
                                    <select id="eventGuests-{{ $event->id }}" class="field-input event-guests">
                                        @for($guests = 1; $guests <= $event->capacity; $guests++)<option value="{{ $guests }}">{{ $guests }}</option>@endfor
                                    </select>
                                </div>
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
                <div class="panel-row dining-selection-row">
                    <div>
                        <label class="field-label" for="diningSchedule">Meal Schedule</label>
                        <select id="diningSchedule" class="field-input">
                            <option value="">Select schedule</option>
                            @foreach($diningSchedules as $schedule)
                                <option value="{{ $schedule->period }}">{{ $schedule->period }} ({{ \Carbon\Carbon::parse($schedule->available_from)->format('g:i A') }} - {{ \Carbon\Carbon::parse($schedule->available_to)->format('g:i A') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label" for="diningTable">Dining Table</label>
                        <select id="diningTable" class="field-input">
                            <option value="">Select available table</option>
                            @foreach($diningTables as $table)
                                <option value="{{ $table->table_no }}">{{ $table->table_no }} - {{ $table->type }} ({{ $table->capacity }} seats{{ $table->location ? ', ' . $table->location : '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label" for="diningDate">Dining Date</label>
                        <input id="diningDate" class="field-input" type="date" aria-label="Dining date" placeholder="MM/DD/YY">
                    </div>
                </div>
                <div class="reservation-card-grid">
                    @foreach($dining as $meal)
                        <article class="reservation-card" data-category="dining" data-price="{{ $meal->price }}" data-title="{{ $meal->name }}" data-dining-id="{{ $meal->id }}" data-schedule="{{ $meal->diningSchedule?->period }}">
                            <img src="{{ $meal->image ? asset('storage/' . $meal->image) : asset('image/Royal-Suite-room.jpg') }}" alt="{{ $meal->name }}">
                            <div class="reservation-card-body">
                                <h4>{{ $meal->name }}</h4>
                                <p>{{ $meal->description ?: 'A dining option for your stay.' }}</p>
                                <p class="text-muted">{{ $meal->category ?: 'Dining package' }}</p>
                                @if($meal->diningSchedule)
                                    <p class="text-muted">{{ $meal->diningSchedule->period }}: {{ \Carbon\Carbon::parse($meal->diningSchedule->available_from)->format('g:i A') }} - {{ \Carbon\Carbon::parse($meal->diningSchedule->available_to)->format('g:i A') }}</p>
                                @endif
                                <div class="reservation-card-footer dining-card-footer">
                                    <span class="price">₱{{ number_format($meal->price, 0) }}</span>
                                    <div class="dining-qty">
                                        <button type="button" class="qty-btn qty-decrease" data-dining-id="{{ $meal->id }}" aria-label="Decrease quantity">−</button>
                                        <input type="number" min="1" value="1" class="dining-quantity" data-dining-id="{{ $meal->id }}">
                                        <button type="button" class="qty-btn qty-increase" data-dining-id="{{ $meal->id }}" aria-label="Increase quantity">+</button>
                                    </div>
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
                    <button type="button" id="seeReceiptBtn" class="summary-receipt-btn" hidden>See Receipt <i class="fas fa-receipt"></i></button>
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
            <div class="notification-toast" id="notificationToast" style="position:relative; top:auto; left:auto; transform:none; margin-bottom:20px;">
                <i class="fas fa-exclamation-circle"></i>
                <span id="notificationMessage"></span>
            </div>
            <p class="confirmation-text" style="margin:0 0 24px; color:#4b5563; font-size:0.98rem; line-height:1.6;">Review all reservation details before submitting.</p>
            <div class="confirmation-review">
                <section class="review-section">
                    <h4><i class="fas fa-receipt"></i> Reservation Information</h4>
                    <div class="review-information-grid">
                        <div class="review-field"><span class="review-field-label">Reservation Number</span><strong class="review-field-value" id="confirmReservationId">RES-0000</strong></div>
                        <div class="review-field"><span class="review-field-label">Status</span><strong class="review-field-value" id="confirmStatus">Reserved</strong></div>
                    </div>
                </section>
                <section class="review-section">
                    <h4><i class="fas fa-bed"></i> Room Summary</h4>
                    <h5 class="review-room-name" id="confirmRoom">None</h5>
                    <div class="review-row"><span>Check-in</span><strong id="confirmArrivingOn">—</strong></div>
                    <div class="review-row"><span>Arrival time</span><strong id="confirmArrivalTime">—</strong></div>
                    <div class="review-row"><span>Check-out</span><strong id="confirmCheckOut">—</strong></div>
                    <div class="review-row"><span>Guests</span><strong id="confirmGuests">2 Guests</strong></div>
                </section>
                <section class="review-section">
                    <h4><i class="fas fa-list-check"></i> Selected Services</h4>
                    <div class="review-service amenity-review"><div class="review-service-icon"><i class="fas fa-concierge-bell"></i></div><div><h5>Amenity</h5><strong id="confirmAmenitiesTitle">None</strong><span id="confirmAmenities">No amenities selected</span></div></div>
                    <div class="review-service event-review"><div class="review-service-icon"><i class="fas fa-ring"></i></div><div><h5>Event</h5><strong id="confirmEventTitle">None</strong><span id="confirmEventDining">No event selected</span></div></div>
                    <div class="review-service dining-review"><div class="review-service-icon"><i class="fas fa-utensils"></i></div><div><h5>Dining</h5><strong id="confirmDiningTitle">None</strong><span id="confirmDiningDetails">No dining selected</span></div></div>
                </section>
                <section class="review-section">
                    <h4><i class="fas fa-user"></i> Guest Information</h4>
                    <div class="review-information-grid">
                        <div class="review-field"><span class="review-field-label">Guest Name</span><strong class="review-field-value" id="confirmGuestName">—</strong></div>
                        <div class="review-field"><span class="review-field-label">Email</span><strong class="review-field-value" id="confirmGuestEmail">—</strong></div>
                        <div class="review-field"><span class="review-field-label">Phone</span><strong class="review-field-value" id="confirmGuestPhone">—</strong></div>
                        <div class="review-field"><span class="review-field-label">Special Request</span><strong class="review-field-value" id="confirmSpecialRequest">None</strong></div>
                    </div>
                </section>
                <section class="review-section">
                    <h4><i class="fas fa-credit-card"></i> Payment Summary</h4>
                    <div class="review-payment-row"><span>Room</span><strong id="confirmRoomCharge">₱0</strong></div>
                    <div class="review-payment-row"><span>Amenities</span><strong id="confirmAmenitiesCharge">₱0</strong></div>
                    <div class="review-payment-row"><span>Event</span><strong id="confirmEventCharge">₱0</strong></div>
                    <div class="review-payment-row"><span>Dining</span><strong id="confirmDiningCharge">₱0</strong></div>
                    <div class="review-payment-row"><span>Extra person</span><strong id="confirmExtraGuestCharge">₱0</strong></div>
                    <div class="review-payment-row review-payment-total"><span>Total Amount</span><strong id="confirmTotalAmount">₱0</strong></div>
                    <div class="review-payment-row"><span>Payment method</span><strong id="confirmPaymentMethod">Cash / Pay at Hotel</strong></div>
                    <div class="confirm-payment-details-card" id="confirmPaymentDetailsRow" hidden>
                        <div id="confirmPaymentDetails"></div>
                        <img class="confirm-payment-proof" id="confirmPaymentProof" alt="Uploaded payment proof preview">
                    </div>
                </section>
                <section class="review-section review-policy">
                    <h4><i class="fas fa-shield-alt"></i> Cancellation Policy / Terms &amp; Conditions</h4>
                    Free cancellation until 24 hours before check-in.
                    <strong>By confirming, I agree to the Terms &amp; Conditions.</strong>
                </section>
            </div>

            <div class="details-form-grid">
                <section class="details-form-section details-guest-info">
                    <h4><i class="fas fa-user"></i> Guest Information</h4>
                    <div class="guest-info-fields">
                        <div><label for="detailsGuestName">Guest Name</label><input id="detailsGuestName" type="text" value="{{ $guest?->name ?? '' }}" readonly></div>
                        <div><label for="detailsGuestEmail">Email Address</label><input id="detailsGuestEmail" type="email" value="{{ $guest?->email ?? '' }}" readonly></div>
                        <div><label for="detailsGuestPhone">Mobile Number</label><input id="detailsGuestPhone" type="tel" value="{{ $guest?->contact_no ?? '' }}" readonly></div>
                        <div class="guest-request-field"><label for="detailsSpecialRequest">Special Request (Optional)</label><textarea id="detailsSpecialRequest" placeholder="Enter any special request..."></textarea><small>Ex. Early check-in, extra bed, etc.</small></div>
                    </div>
                </section>
            </div>
            <div class="details-secondary-grid">
                <section class="details-form-section details-summary-room">
                    <h4 class="details-summary-title"><i class="fas fa-bed"></i> Room Summary</h4>
                    <strong class="details-summary-room-name" id="detailsRoomName">None selected</strong>
                    <div class="details-summary-row"><i class="fas fa-calendar-check"></i><span class="details-summary-label">Check-in</span><span class="details-summary-value" id="detailsCheckIn">—</span></div>
                    <div class="details-summary-row"><i class="fas fa-clock"></i><span class="details-summary-label">Arrival time</span><span class="details-summary-value" id="detailsArrivalTime">—</span></div>
                    <div class="details-summary-row"><i class="fas fa-calendar-check"></i><span class="details-summary-label">Check-out</span><span class="details-summary-value" id="detailsCheckOut">—</span></div>
                    <div class="details-summary-row"><i class="fas fa-users"></i><span class="details-summary-label">Guests</span><span class="details-summary-value" id="detailsRoomGuests">2 Guests</span></div>
                </section>
                <section class="details-form-section details-services-summary">
                    <h4 class="details-summary-title"><i class="fas fa-list-check"></i> Selected Services</h4>
                    <div class="details-service-item amenity-service">
                        <div class="details-service-icon"><i class="fas fa-square-parking"></i></div>
                        <div><p class="details-service-label">Amenity</p><strong class="details-service-value" id="detailsAmenitiesTitle">None</strong><span class="details-service-meta" id="detailsAmenitiesSummary"></span></div>
                    </div>
                    <div class="details-service-item event-service">
                        <div class="details-service-icon"><i class="fas fa-ring"></i></div>
                        <div><p class="details-service-label">Event</p><strong class="details-service-value" id="detailsEventTitle">None</strong><span class="details-service-meta" id="detailsEventSummary"></span></div>
                    </div>
                    <div class="details-service-item dining-service">
                        <div class="details-service-icon"><i class="fas fa-utensils"></i></div>
                        <div><p class="details-service-label">Dining</p><strong class="details-service-value" id="detailsDiningTitle">None</strong><span class="details-service-meta" id="detailsDiningSummary"></span></div>
                    </div>
                </section>
            </div>
            <div class="details-form-grid details-request-grid">
                <section class="details-form-section">
                    <h4><i class="fas fa-shield-alt"></i> Cancellation Policy</h4>
                    <p style="margin:0;color:#788398;font-size:9px;line-height:1.4;">Free cancellation until 24 hours before check-in.</p>
                    <label><input id="detailsTerms" type="checkbox"> I agree to the Terms & Conditions</label>
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
                <p>Please select your preferred payment method.</p>
                <div class="payment-method-options">
                    <button type="button" class="payment-method-option selected" data-payment-method="Cash / Pay at Hotel">Cash / Pay at Hotel</button>
                    <button type="button" class="payment-method-option" data-payment-method="GCash">GCash</button>
                    <button type="button" class="payment-method-option" data-payment-method="Maya">Maya</button>
                    <button type="button" class="payment-method-option" data-payment-method="Credit / Debit Card">Credit / Debit Card</button>
                    <button type="button" class="payment-method-option" data-payment-method="Bank Transfer">Bank Transfer</button>
                </div>
                <div class="payment-method-panel" data-payment-panel="GCash" hidden>
                    <h5>GCash Payment</h5>
                    <div class="payment-fields">
                        <div class="payment-field"><label for="gcashAccountName">GCash Account Name</label><input id="gcashAccountName" type="text" placeholder="Enter account name"></div>
                        <div class="payment-field"><label for="gcashNumber">GCash Number</label><input id="gcashNumber" type="tel" placeholder="09XX XXX XXXX"></div>
                        <div class="payment-field"><label for="gcashReferenceNumber">Reference Number</label><input id="gcashReferenceNumber" type="text" placeholder="Enter reference number"></div>
                        <div class="payment-field"><label for="gcashPaymentAmount">Payment Amount</label><input id="gcashPaymentAmount" type="text" inputmode="decimal" placeholder="Enter amount"></div>
                        <div class="payment-field full-width"><label for="gcashPaymentProof">Upload Payment Proof</label><input id="gcashPaymentProof" type="file" accept="image/*,.pdf"></div>
                    </div>
                </div>
                <div class="payment-method-panel" data-payment-panel="Maya" hidden>
                    <h5>Maya Payment</h5>
                    <div class="payment-fields">
                        <div class="payment-field"><label for="mayaAccountName">Maya Account Name</label><input id="mayaAccountName" type="text" placeholder="Enter account name"></div>
                        <div class="payment-field"><label for="mayaNumber">Maya Number</label><input id="mayaNumber" type="tel" placeholder="09XX XXX XXXX"></div>
                        <div class="payment-field"><label for="mayaReferenceNumber">Reference Number</label><input id="mayaReferenceNumber" type="text" placeholder="Enter reference number"></div>
                        <div class="payment-field"><label for="mayaPaymentAmount">Payment Amount</label><input id="mayaPaymentAmount" type="text" inputmode="decimal" placeholder="Enter amount"></div>
                        <div class="payment-field full-width"><label for="mayaPaymentProof">Upload Payment Proof</label><input id="mayaPaymentProof" type="file" accept="image/*,.pdf"></div>
                    </div>
                </div>
                <div class="payment-method-panel" data-payment-panel="Credit / Debit Card" hidden>
                    <h5>Card Payment</h5>
                    <div class="payment-fields">
                        <div class="payment-field full-width"><label for="cardholderName">Cardholder Name</label><input id="cardholderName" type="text" placeholder="Enter cardholder name"></div>
                        <div class="payment-field full-width"><label for="cardNumber">Card Number</label><input id="cardNumber" type="text" inputmode="numeric" placeholder="**** **** **** ****"></div>
                        <div class="payment-field"><label for="cardExpiration">Expiration Date</label><input id="cardExpiration" type="text" placeholder="MM / YY"></div>
                        <div class="payment-field"><label for="cardCvv">CVV</label><input id="cardCvv" type="password" inputmode="numeric" placeholder="***"></div>
                        <div class="payment-field full-width"><label for="cardPaymentAmount">Payment Amount</label><input id="cardPaymentAmount" type="text" inputmode="decimal" placeholder="Enter amount"></div>
                    </div>
                </div>
                <div class="payment-method-panel" data-payment-panel="Bank Transfer" hidden>
                    <h5>Bank Transfer</h5>
                    <div class="payment-fields">
                        <div class="payment-field"><label for="bankAccountName">Account Name</label><input id="bankAccountName" type="text" placeholder="Enter account name"></div>
                        <div class="payment-field"><label for="bankName">Bank</label><select id="bankName"><option value="">Select Bank</option><option>BDO</option><option>BPI</option><option>Metrobank</option><option>Other</option></select></div>
                        <div class="payment-field"><label for="bankReferenceNumber">Reference Number</label><input id="bankReferenceNumber" type="text" placeholder="Enter reference number"></div>
                        <div class="payment-field"><label for="transferDate">Transfer Date</label><input id="transferDate" type="date"></div>
                        <div class="payment-field"><label for="transferAmount">Amount Transferred</label><input id="transferAmount" type="text" inputmode="decimal" placeholder="Enter amount"></div>
                        <div class="payment-field"><label for="bankPaymentProof">Upload Payment Proof</label><input id="bankPaymentProof" type="file" accept="image/*,.pdf"></div>
                    </div>
                </div>
            </div>

            <div class="confirmation-actions" style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" id="modalConfirmBtn" class="confirm-submit-btn" style="border-radius:999px; padding:14px 26px; font-weight:700; border:none; cursor:pointer; background:#dc2626; color:#ffffff;">Confirm Reservation</button>
                <button type="button" id="modalCancelBtn" class="confirm-cancel-btn" style="border-radius:999px; padding:14px 26px; font-weight:700; border:none; cursor:pointer; background:#f3f4f6; color:#111827;">Back to Select</button>
            </div>
        </div>
    </div>

    <div class="receipt-modal" id="receiptModal" aria-hidden="true">
        <div class="receipt-card" role="dialog" aria-modal="true" aria-labelledby="receiptTitle">
            <div class="receipt-header">
                <h3 class="receipt-brand">Casaul Hotel</h3>
                <div class="receipt-contact">
                    <span><i class="fas fa-map-marker-alt"></i> Casaul Hotel</span>
                    <span><i class="fas fa-phone"></i> +63 912 345 6789</span>
                    <span><i class="fas fa-envelope"></i> reservations@casaulhotel.com</span>
                </div>
                <p class="receipt-subcontact"><i class="fas fa-globe"></i> www.casaulhotel.com</p>
                <button type="button" class="receipt-close" id="receiptCloseBtn" aria-label="Close receipt">&times;</button>
            </div>
            <div class="receipt-heading-row">
                <h3 class="receipt-heading">RECEIPT</h3>
            </div>
            <div class="receipt-title-row">
                <div class="receipt-paid-by">
                    <h4>Paid By</h4>
                    <p id="receiptGuestName">Guest</p>
                    <p id="receiptGuestEmail">guest@example.com</p>
                </div>
                <div class="receipt-booking">
                    <p><span>Receipt #</span><strong id="receiptNumber">RES-0000</strong></p>
                    <p><span>Receipt Date</span><strong id="receiptDate"></strong></p>
                </div>
            </div>
            <div class="receipt-booking receipt-booking-details">
                <h4>Booking Details</h4>
                <p><span>Check-in</span><strong id="receiptCheckIn">—</strong></p>
                <p><span>Check-out</span><strong id="receiptCheckOut">—</strong></p>
                <p><span>Guests</span><strong id="receiptGuests">2 Guests</strong></p>
                <p><span>Room</span><strong id="receiptRoom">None</strong></p>
            </div>
            <div class="receipt-content" id="receiptContent"></div>
            <div class="receipt-notes">
                <h4>Notes</h4>
                <p>Thank you for choosing Casaul Hotel. We look forward to your next visit.</p>
            </div>
            <div class="receipt-actions">
                <button type="button" id="receiptDownloadBtn"><i class="fas fa-download"></i> Download</button>
                <button type="button" class="receipt-print-btn" id="receiptPrintBtn"><i class="fas fa-print"></i> Print</button>
            </div>
        </div>
    </div>
</div>

<form id="reservationForm" action="{{ route('reservation.store') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="submission_token" value="{{ \Illuminate\Support\Str::uuid() }}">
    <input type="hidden" name="room_id" id="reservationRoomId">
    <input type="hidden" name="check_in" id="reservationCheckIn">
    <input type="hidden" name="check_in_time" id="reservationCheckInTime">
    <input type="hidden" name="check_out" id="reservationCheckOut">
    <input type="hidden" name="check_out_time" id="reservationCheckOutTime">
    <input type="hidden" name="guest_name" id="reservationGuestName" value="{{ $guest?->name ?? 'Guest' }}">
    <input type="hidden" name="guest_email" id="reservationGuestEmail" value="{{ $guest?->email ?? 'guest@example.com' }}">
    <input type="hidden" name="guest_phone" id="reservationGuestPhone" value="{{ $guest?->contact_no ?? '0000000000' }}">
    <input type="hidden" name="total_amount" id="reservationTotalAmount">
    <input type="hidden" name="amount_paid" id="reservationAmountPaid" value="0">
    <input type="hidden" name="special_requests" id="reservationSpecialRequests" value="">
    <input type="hidden" name="dining_id" id="reservationDiningId">
    <input type="hidden" name="dining_items" id="reservationDiningItems">
    <input type="hidden" name="dining_area" id="reservationDiningArea">
    <input type="hidden" name="dining_schedule" id="reservationDiningSchedule">
    <input type="hidden" name="quantity" id="reservationDiningQuantity">
    <input type="hidden" name="amenity_id" id="reservationAmenityId">
    <input type="hidden" name="event_place_id" id="reservationEventPlaceId">
    <input type="hidden" name="event_type" id="reservationEventType">
    <input type="hidden" name="number_of_guests" id="reservationEventGuests">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reservationPage = document.querySelector('.reservation-page');
        const reservationLeft = document.querySelector('.reservation-left');
        const tabs = document.querySelectorAll('.tab-btn');
        const panels = document.querySelectorAll('.reservation-panel');
        const notificationToast = document.getElementById('notificationToast');
        const notificationMessage = document.getElementById('notificationMessage');
        const checkIn = document.getElementById('checkIn');
        const checkOut = document.getElementById('checkOut');
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
        const seeReceiptBtn = document.getElementById('seeReceiptBtn');
        const clearBtn = document.getElementById('clearReservationBtn');
        const reservationForm = document.getElementById('reservationForm');
        const reservationRoomId = document.getElementById('reservationRoomId');
        const reservationCheckIn = document.getElementById('reservationCheckIn');
        const reservationCheckInTime = document.getElementById('reservationCheckInTime');
        const reservationCheckOut = document.getElementById('reservationCheckOut');
        const reservationCheckOutTime = document.getElementById('reservationCheckOutTime');
        const reservationGuestName = document.getElementById('reservationGuestName');
        const reservationGuestEmail = document.getElementById('reservationGuestEmail');
        const reservationGuestPhone = document.getElementById('reservationGuestPhone');
        const reservationTotalAmount = document.getElementById('reservationTotalAmount');
        const reservationSpecialRequests = document.getElementById('reservationSpecialRequests');
        const reservationDiningId = document.getElementById('reservationDiningId');
        const reservationDiningItems = document.getElementById('reservationDiningItems');
        const reservationDiningArea = document.getElementById('reservationDiningArea');
        const reservationDiningSchedule = document.getElementById('reservationDiningSchedule');
        const reservationDiningQuantity = document.getElementById('reservationDiningQuantity');
        const reservationAmenityId = document.getElementById('reservationAmenityId');
        const reservationEventPlaceId = document.getElementById('reservationEventPlaceId');
        const reservationEventType = document.getElementById('reservationEventType');
        const reservationEventGuests = document.getElementById('reservationEventGuests');
        const diningSchedule = document.getElementById('diningSchedule');
        const diningTable = document.getElementById('diningTable');
        const paymentMethodChoices = document.querySelectorAll('.payment-method-option');
        const confirmationModal = document.getElementById('confirmationModal');
        const receiptModal = document.getElementById('receiptModal');
        const receiptContent = document.getElementById('receiptContent');
        const receiptGuestName = document.getElementById('receiptGuestName');
        const receiptGuestEmail = document.getElementById('receiptGuestEmail');
        const receiptNumber = document.getElementById('receiptNumber');
        const receiptDate = document.getElementById('receiptDate');
        const receiptCheckIn = document.getElementById('receiptCheckIn');
        const receiptCheckOut = document.getElementById('receiptCheckOut');
        const receiptGuests = document.getElementById('receiptGuests');
        const receiptRoom = document.getElementById('receiptRoom');
        const receiptCloseBtn = document.getElementById('receiptCloseBtn');
        const receiptDownloadBtn = document.getElementById('receiptDownloadBtn');
        const receiptPrintBtn = document.getElementById('receiptPrintBtn');
        const confirmationOriginalParent = confirmationModal.parentElement;
        const confirmationCloseBtn = document.getElementById('confirmationCloseBtn');
        const modalConfirmBtn = document.getElementById('modalConfirmBtn');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        const confirmReservationId = document.getElementById('confirmReservationId');
        const confirmRoom = document.getElementById('confirmRoom');
        const confirmGuests = document.getElementById('confirmGuests');
        const confirmArrivingOn = document.getElementById('confirmArrivingOn');
        const confirmArrivalTime = document.getElementById('confirmArrivalTime');
        const confirmCheckOut = document.getElementById('confirmCheckOut');
        const confirmStatus = document.getElementById('confirmStatus');
        const confirmPaymentMethod = document.getElementById('confirmPaymentMethod');
        const confirmAmenitiesTitle = document.getElementById('confirmAmenitiesTitle');
        const confirmAmenities = document.getElementById('confirmAmenities');
        const confirmEventTitle = document.getElementById('confirmEventTitle');
        const confirmEventDining = document.getElementById('confirmEventDining');
        const confirmDiningTitle = document.getElementById('confirmDiningTitle');
        const confirmDiningDetails = document.getElementById('confirmDiningDetails');
        const confirmGuestName = document.getElementById('confirmGuestName');
        const confirmGuestEmail = document.getElementById('confirmGuestEmail');
        const confirmGuestPhone = document.getElementById('confirmGuestPhone');
        const confirmSpecialRequest = document.getElementById('confirmSpecialRequest');
        const confirmRoomCharge = document.getElementById('confirmRoomCharge');
        const confirmAmenitiesCharge = document.getElementById('confirmAmenitiesCharge');
        const confirmEventCharge = document.getElementById('confirmEventCharge');
        const confirmDiningCharge = document.getElementById('confirmDiningCharge');
        const confirmExtraGuestCharge = document.getElementById('confirmExtraGuestCharge');
        const confirmTotalAmount = document.getElementById('confirmTotalAmount');
        const confirmPaymentDetailsRow = document.getElementById('confirmPaymentDetailsRow');
        const confirmPaymentDetails = document.getElementById('confirmPaymentDetails');
        const confirmPaymentProof = document.getElementById('confirmPaymentProof');
        const detailsRoomName = document.getElementById('detailsRoomName');
        const detailsCheckIn = document.getElementById('detailsCheckIn');
        const detailsArrivalTime = document.getElementById('detailsArrivalTime');
        const detailsCheckOut = document.getElementById('detailsCheckOut');
        const detailsRoomGuests = document.getElementById('detailsRoomGuests');
        const detailsAmenitiesTitle = document.getElementById('detailsAmenitiesTitle');
        const detailsAmenitiesSummary = document.getElementById('detailsAmenitiesSummary');
        const detailsEventTitle = document.getElementById('detailsEventTitle');
        const detailsEventSummary = document.getElementById('detailsEventSummary');
        const detailsDiningTitle = document.getElementById('detailsDiningTitle');
        const detailsDiningSummary = document.getElementById('detailsDiningSummary');
        const detailsGuestName = document.getElementById('detailsGuestName');
        const detailsGuestEmail = document.getElementById('detailsGuestEmail');
        const detailsGuestPhone = document.getElementById('detailsGuestPhone');
        const detailsSpecialRequest = document.getElementById('detailsSpecialRequest');
        const arrivalTime = document.getElementById('arrivalTime');
        const detailsTerms = document.getElementById('detailsTerms');
        const paymentPanels = document.querySelectorAll('[data-payment-panel]');
        const methodPaymentAmounts = document.querySelectorAll('#gcashPaymentAmount, #mayaPaymentAmount, #cardPaymentAmount, #transferAmount');

        let selectedRoom = null;
        let roomPrice = 0;
        let selectedAmenities = [];
        let selectedEvent = [];
        let selectedDining = [];
        let selectedExtraGuests = 0;
        let selectedExtraGuestPrice = 650;
        let selectedPaymentMethod = 'Cash / Pay at Hotel';
        let confirmPaymentProofUrl = null;

        const items = document.querySelectorAll('.select-option-btn');
        const sumItemTotal = (itemsList) => itemsList.reduce((sum, item) => sum + (Number(item.price || 0) * (Number(item.quantity || 1))), 0);
        const getSelectedEventCount = () => selectedEvent.length;
        const getSelectedDiningCount = () => selectedDining.length;

        const formatDisplayDate = (dateValue) => {
            if (!dateValue) {
                return '—';
            }

            const [year, month, day] = dateValue.split('-').map(Number);
            const date = new Date(year, month - 1, day);
            return date.toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'short',
                day: 'numeric',
                year: 'numeric',
            });
        };

        const formatDisplayTime = (timeValue) => {
            if (!timeValue) {
                return '—';
            }

            const [hours, minutes] = timeValue.split(':').map(Number);
            const period = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            return `${displayHours}:${String(minutes).padStart(2, '0')} ${period}`;
        };

        const escapeHtml = value => String(value).replace(/[&<>'"]/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' })[character]);
        const formatCurrencyValue = (value) => {
            const numericValue = Number(value) || 0;
            return `₱${numericValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        };
        const formatCurrencyInput = (input) => {
            if (!input) {
                return;
            }

            const rawNumber = String(input.value || '').replace(/[^\d.-]/g, '');
            if (!rawNumber || rawNumber === '-' || rawNumber === '.') {
                input.value = '';
                return;
            }

            const numericValue = Number(rawNumber);
            if (!Number.isFinite(numericValue)) {
                input.value = '';
                return;
            }

            input.value = `₱${numericValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        };
        const getPaymentAmountValue = (inputId) => {
            const input = document.getElementById(inputId);
            if (!input) {
                return 0;
            }
            const cleanValue = Number(String(input.value).replace(/[^\d.-]/g, ''));
            return Number.isFinite(cleanValue) ? cleanValue : 0;
        };

        const getPaymentDetails = () => {
            const value = id => document.getElementById(id)?.value?.trim() || '';
            const fileName = id => document.getElementById(id)?.files?.[0]?.name || '';
            const amountValue = (inputId) => formatCurrencyValue(getPaymentAmountValue(inputId));

            if (selectedPaymentMethod === 'GCash') {
                return `Account: ${value('gcashAccountName') || '—'} • Number: ${value('gcashNumber') || '—'} • Amount: ${amountValue('gcashPaymentAmount')} • Reference: ${value('gcashReferenceNumber') || '—'}${fileName('gcashPaymentProof') ? ` • Proof: ${fileName('gcashPaymentProof')}` : ''}`;
            }
            if (selectedPaymentMethod === 'Maya') {
                return `Account: ${value('mayaAccountName') || '—'} • Number: ${value('mayaNumber') || '—'} • Amount: ${amountValue('mayaPaymentAmount')} • Reference: ${value('mayaReferenceNumber') || '—'}${fileName('mayaPaymentProof') ? ` • Proof: ${fileName('mayaPaymentProof')}` : ''}`;
            }
            if (selectedPaymentMethod === 'Credit / Debit Card') {
                const cardNumber = value('cardNumber').replace(/\D/g, '');
                return `Cardholder: ${value('cardholderName') || '—'} • Card: ${cardNumber ? `•••• ${cardNumber.slice(-4)}` : '—'} • Amount: ${amountValue('cardPaymentAmount')} • Expiration: ${value('cardExpiration') || '—'}`;
            }
            if (selectedPaymentMethod === 'Bank Transfer') {
                return `Account: ${value('bankAccountName') || '—'} • Bank: ${value('bankName') || '—'} • Amount: ${amountValue('transferAmount')} • Reference: ${value('bankReferenceNumber') || '—'} • Date: ${value('transferDate') || '—'}${fileName('bankPaymentProof') ? ` • Proof: ${fileName('bankPaymentProof')}` : ''}`;
            }
            return 'Pay at hotel';
        };

        const showNotification = (message) => {
            if (!detailsTerms || detailsTerms.checked) {
                notificationToast.classList.remove('show', 'hide');
                notificationToast.style.display = 'none';
                return;
            }

            notificationMessage.textContent = message;
            notificationToast.style.display = 'flex';
            notificationToast.classList.remove('hide');
            notificationToast.classList.add('show');
            setTimeout(() => {
                notificationToast.classList.add('hide');
                setTimeout(() => {
                    notificationToast.classList.remove('show');
                    notificationToast.style.display = 'none';
                }, 300);
            }, 4500);
        };

        const getPaymentDetailRows = () => {
            const value = id => document.getElementById(id)?.value?.trim() || '';
            const file = id => document.getElementById(id)?.files?.[0] || null;
            if (selectedPaymentMethod === 'GCash') {
                return [['fa-user', 'Account Name', value('gcashAccountName') || '—'], ['fa-money-bill-wave', 'Amount', formatCurrencyValue(getPaymentAmountValue('gcashPaymentAmount'))], ['fa-phone', 'Account Number', value('gcashNumber') || '—'], ['fa-receipt', 'Reference Number', value('gcashReferenceNumber') || '—'], ['fa-shield-alt', 'Proof of Payment', file('gcashPaymentProof')?.name || 'Not uploaded']];
            }
            if (selectedPaymentMethod === 'Maya') {
                return [['fa-user', 'Account Name', value('mayaAccountName') || '—'], ['fa-money-bill-wave', 'Amount', formatCurrencyValue(getPaymentAmountValue('mayaPaymentAmount'))], ['fa-phone', 'Account Number', value('mayaNumber') || '—'], ['fa-receipt', 'Reference Number', value('mayaReferenceNumber') || '—'], ['fa-shield-alt', 'Proof of Payment', file('mayaPaymentProof')?.name || 'Not uploaded']];
            }
            if (selectedPaymentMethod === 'Credit / Debit Card') {
                const cardNumber = value('cardNumber').replace(/\D/g, '');
                return [['fa-user', 'Cardholder Name', value('cardholderName') || '—'], ['fa-credit-card', 'Card Number', cardNumber ? `•••• ${cardNumber.slice(-4)}` : '—'], ['fa-money-bill-wave', 'Amount', formatCurrencyValue(getPaymentAmountValue('cardPaymentAmount'))], ['fa-calendar', 'Expiration Date', value('cardExpiration') || '—']];
            }
            if (selectedPaymentMethod === 'Bank Transfer') {
                return [['fa-user', 'Account Name', value('bankAccountName') || '—'], ['fa-university', 'Bank', value('bankName') || '—'], ['fa-money-bill-wave', 'Amount', formatCurrencyValue(getPaymentAmountValue('transferAmount'))], ['fa-receipt', 'Reference Number', value('bankReferenceNumber') || '—'], ['fa-calendar', 'Transfer Date', value('transferDate') || '—'], ['fa-shield-alt', 'Proof of Payment', file('bankPaymentProof')?.name || 'Not uploaded']];
            }
            return [];
        };

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                tabs.forEach(btn => btn.classList.remove('active'));
                panels.forEach(panel => panel.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });

        const updateSummary = () => {
            const selectedEventTitles = selectedEvent.map(item => item.title).join(', ');
            const selectedDiningTitles = selectedDining.map(item => `${item.title}${Number(item.quantity || 1) > 1 ? ` x${item.quantity}` : ''}`).join(', ');
            const selectedDiningSchedule = [...new Set(selectedDining.map(item => item.schedule).filter(Boolean))].join(', ');
            const selectedDiningTable = [...new Set(selectedDining.map(item => item.table).filter(Boolean))].join(', ');
            const selectedDiningDate = [...new Set(selectedDining.map(item => item.date).filter(Boolean).map(date => formatDisplayDate(date)))].join(', ');
            const selectedEventGuests = selectedEvent.reduce((sum, item) => sum + (Number(item.guests || 0)), 0);
            const selectedDiningQuantity = selectedDining.reduce((sum, item) => sum + (Number(item.quantity || 1)), 0);

            summaryRoom.textContent = selectedRoom ? selectedRoom : 'None';
            summaryRoomDetails.textContent = selectedRoom ? `${formatDisplayDate(checkIn.value)} – ${formatDisplayDate(checkOut.value)}${selectedExtraGuests > 0 ? ` • ${selectedExtraGuests} Extra Person(s)` : ''}` : 'Choose a room and dates';
            summaryRoomPrice.textContent = `₱${roomPrice.toLocaleString()}`;
            summaryItems.textContent = selectedAmenities.length > 0 ? `${selectedAmenities.length} selected` : '0 selected';
            summaryAdditionalGuests.textContent = selectedExtraGuests > 0 ? `${selectedExtraGuests} added` : 'None';
            summaryEvent.textContent = selectedEvent.length ? `${selectedEvent.length} selected${selectedEventTitles ? ` • ${selectedEventTitles}` : ''}` : 'None';
            summaryDining.textContent = selectedDining.length ? `${selectedDining.length} selected${selectedDiningTitles ? ` • ${selectedDiningTitles}` : ''}${selectedDiningSchedule ? ` / ${selectedDiningSchedule}` : ''}${selectedDiningTable ? ` / ${selectedDiningTable}` : ''}` : 'None';
            summaryAmenitiesPrice.textContent = `₱${sumItemTotal(selectedAmenities).toLocaleString()}`;
            summaryAdditionalGuestsPrice.textContent = `₱${(selectedExtraGuests * selectedExtraGuestPrice).toLocaleString()}`;
            summaryEventPrice.textContent = `₱${selectedEvent.reduce((sum, item) => sum + (Number(item.price || 0)), 0).toLocaleString()}`;
            summaryDiningPrice.textContent = `₱${selectedDining.reduce((sum, item) => sum + ((Number(item.price || 0)) * (Number(item.quantity || 1))), 0).toLocaleString()}`;

            const total = calculateTotal();
            const hasRoomSelection = Boolean(selectedRoom);
            detailsRoomName.textContent = hasRoomSelection ? selectedRoom : 'None selected';
            detailsCheckIn.textContent = hasRoomSelection ? formatDisplayDate(checkIn.value) : '—';
            detailsArrivalTime.textContent = hasRoomSelection ? formatDisplayTime(arrivalTime.value) : '—';
            detailsCheckOut.textContent = hasRoomSelection ? formatDisplayDate(checkOut.value) : '—';
            detailsRoomGuests.textContent = hasRoomSelection ? `${selectedExtraGuests + 2} Guests` : '—';
            detailsAmenitiesTitle.textContent = selectedAmenities.length ? selectedAmenities.map(item => item.title).join(', ') : 'None';
            detailsAmenitiesSummary.textContent = selectedAmenities.length
                ? selectedAmenities.map(item => `${item.quantity} ${item.quantity === 1 ? 'slot' : 'slots'}${item.date ? ` • ${formatDisplayDate(item.date)}` : ''}${item.time ? ` • ${formatDisplayTime(item.time)}` : ''} • ₱${(item.price * item.quantity).toLocaleString()}`).join(', ')
                : '';
            detailsEventTitle.textContent = selectedEvent.length ? selectedEvent.map(item => item.title).join(', ') : 'None';
            detailsEventSummary.textContent = selectedEvent.length
                ? selectedEvent.map(item => `${item.guests} guests${item.date ? ` • ${formatDisplayDate(item.date)}` : ''}${item.startTime ? ` • ${formatDisplayTime(item.startTime)} - ${formatDisplayTime(item.endTime)}` : ''}`).join(', ')
                : '';
            detailsDiningTitle.textContent = selectedDining.length
                ? selectedDining.map(item => `${item.title}${Number(item.quantity || 1) > 1 ? ` x${item.quantity}` : ''}`).join(', ')
                : 'None';
            detailsDiningSummary.textContent = selectedDining.length
                ? [selectedDiningTable ? `Table ${selectedDiningTable}` : null, selectedDiningSchedule || null, selectedDiningDate || null].filter(Boolean).join(' • ') || 'No table selected'
                : '';

            confirmReservationId.textContent = selectedRoom ? `RES-${Math.floor(Math.random() * 9000) + 1000}` : 'RES-0000';
            confirmRoom.textContent = selectedRoom ? selectedRoom : 'None';
            confirmArrivingOn.textContent = selectedRoom ? formatDisplayDate(checkIn.value) : '—';
            confirmArrivalTime.textContent = selectedRoom ? formatDisplayTime(arrivalTime.value) : '—';
            confirmCheckOut.textContent = selectedRoom ? formatDisplayDate(checkOut.value) : '—';
            confirmGuests.textContent = selectedRoom ? `${selectedExtraGuests + 2} Guests` : '—';
            confirmStatus.textContent = 'Reserved';
            confirmPaymentMethod.textContent = selectedPaymentMethod;
            const paymentDetailRows = getPaymentDetailRows();
            confirmPaymentDetails.innerHTML = paymentDetailRows.map(([icon, label, value]) => `<div class="confirm-payment-detail-row"><i class="fas ${icon}"></i><span class="confirm-payment-detail-label">${escapeHtml(label)}</span><span>:</span><strong class="confirm-payment-detail-value">${escapeHtml(value)}</strong></div>`).join('');
            confirmPaymentDetailsRow.hidden = selectedPaymentMethod === 'Cash / Pay at Hotel';
            const proofInputId = selectedPaymentMethod === 'GCash' ? 'gcashPaymentProof' : selectedPaymentMethod === 'Maya' ? 'mayaPaymentProof' : selectedPaymentMethod === 'Bank Transfer' ? 'bankPaymentProof' : '';
            const proofFile = proofInputId ? document.getElementById(proofInputId)?.files?.[0] : null;
            if (confirmPaymentProofUrl) {
                URL.revokeObjectURL(confirmPaymentProofUrl);
                confirmPaymentProofUrl = null;
            }
            if (proofFile && proofFile.type.startsWith('image/')) {
                confirmPaymentProofUrl = URL.createObjectURL(proofFile);
                confirmPaymentProof.src = confirmPaymentProofUrl;
                confirmPaymentProof.style.display = 'block';
            } else {
                confirmPaymentProof.removeAttribute('src');
                confirmPaymentProof.style.display = 'none';
            }
            confirmAmenitiesTitle.textContent = selectedAmenities.length ? selectedAmenities.map(item => item.title).join(', ') : 'None';
            confirmAmenities.textContent = selectedAmenities.length
                ? selectedAmenities.map(item => `${item.quantity} ${item.quantity === 1 ? 'slot' : 'slots'} • ₱${(item.price * item.quantity).toLocaleString()}`).join(', ')
                : 'No amenities selected';
            confirmEventTitle.textContent = selectedEvent.length ? selectedEvent.map(item => item.title).join(', ') : 'None';
            confirmEventDining.textContent = selectedEvent.length
                ? selectedEvent.map(item => `${item.type || 'Event'} • ${item.guests} guests${item.date ? ` • ${formatDisplayDate(item.date)}` : ''}${item.startTime ? ` • ${formatDisplayTime(item.startTime)} - ${formatDisplayTime(item.endTime)}` : ''}`).join(', ')
                : 'No event selected';
            confirmDiningTitle.textContent = selectedDining.length
                ? selectedDining.map(item => `${item.title}${Number(item.quantity || 1) > 1 ? ` x${item.quantity}` : ''}`).join(', ')
                : 'None';
            confirmDiningDetails.textContent = selectedDining.length
                ? [selectedDiningTable ? `Table ${selectedDiningTable}` : 'Table not selected', selectedDiningSchedule || null, selectedDiningDate || null].filter(Boolean).join(' • ') || 'No dining selected'
                : 'No dining selected';
            confirmGuestName.textContent = detailsGuestName.value || 'Guest';
            confirmGuestEmail.textContent = detailsGuestEmail.value || 'guest@example.com';
            confirmGuestPhone.textContent = detailsGuestPhone.value || '0000000000';
            confirmSpecialRequest.textContent = detailsSpecialRequest.value || 'None';
            confirmRoomCharge.textContent = `₱${roomPrice.toLocaleString()}`;
            confirmAmenitiesCharge.textContent = `₱${sumItemTotal(selectedAmenities).toLocaleString()}`;
            confirmEventCharge.textContent = `₱${selectedEvent.reduce((sum, item) => sum + (Number(item.price || 0)), 0).toLocaleString()}`;
            confirmDiningCharge.textContent = `₱${selectedDining.reduce((sum, item) => sum + ((Number(item.price || 0)) * (Number(item.quantity || 1))), 0).toLocaleString()}`;
            confirmExtraGuestCharge.textContent = `₱${(selectedExtraGuests * selectedExtraGuestPrice).toLocaleString()}`;
            confirmTotalAmount.textContent = `₱${calculateTotal().toLocaleString()}`;
            methodPaymentAmounts.forEach(input => {
                input.placeholder = 'Enter amount';
                if (input.dataset.userEdited !== 'true') {
                    input.value = '';
                }
            });

            summaryTotal.textContent = `₱${total.toLocaleString()}`;
            reservationTotalAmount.value = total;
            reservationCheckIn.value = checkIn.value;
            reservationCheckOut.value = checkOut.value;
            reservationCheckInTime.value = arrivalTime.value || '';
            reservationCheckOutTime.value = '';
            reservationDiningId.value = selectedDining.map(item => item.id).filter(Boolean).join(',');
            reservationDiningItems.value = JSON.stringify(selectedDining.map(item => ({
                dining_id: item.id,
                quantity: Number(item.quantity || 1),
                dining_area: item.table || null,
                dining_schedule: item.schedule || null,
                dining_date: item.date || null,
            })));
            reservationDiningArea.value = selectedDining.map(item => item.table).filter(Boolean).join(',');
            reservationDiningSchedule.value = selectedDining.map(item => item.schedule).filter(Boolean).join(',');
            reservationDiningQuantity.value = selectedDiningQuantity || '';
            reservationAmenityId.value = selectedAmenities.map(item => item.id).filter(Boolean).join(',');
            reservationEventPlaceId.value = selectedEvent.map(item => item.id).filter(Boolean).join(',');
            reservationEventType.value = selectedEvent.map(item => item.type).filter(Boolean).join(',');
            reservationEventGuests.value = selectedEventGuests || '';
            reservationTotalAmount.value = total;
            reservationCheckIn.value = checkIn.value;
            reservationCheckOut.value = checkOut.value;
        }

        checkIn.addEventListener('change', updateSummary);
        checkOut.addEventListener('change', updateSummary);
        paymentMethodChoices.forEach(choice => choice.addEventListener('click', function () {
            selectedPaymentMethod = this.dataset.paymentMethod;
            paymentMethodChoices.forEach(button => button.classList.toggle('selected', button === this));
            paymentPanels.forEach(panel => {
                panel.hidden = panel.dataset.paymentPanel !== selectedPaymentMethod;
            });
            updateSummary();
        }));
        document.querySelectorAll('.payment-method-section input, .payment-method-section select').forEach(field => {
            field.addEventListener('input', function () {
                if (field.matches('#gcashPaymentAmount, #mayaPaymentAmount, #cardPaymentAmount, #transferAmount')) {
                    field.dataset.userEdited = 'true';
                    const rawValue = String(field.value || '').replace(/[^\d.-]/g, '');
                    field.value = rawValue;
                }
                updateSummary();
            });
            field.addEventListener('blur', function () {
                if (field.matches('#gcashPaymentAmount, #mayaPaymentAmount, #cardPaymentAmount, #transferAmount')) {
                    field.dataset.userEdited = 'true';
                    formatCurrencyInput(field);
                }
                updateSummary();
            });
            field.addEventListener('change', function () {
                if (field.matches('#gcashPaymentAmount, #mayaPaymentAmount, #cardPaymentAmount, #transferAmount')) {
                    field.dataset.userEdited = 'true';
                    formatCurrencyInput(field);
                }
                updateSummary();
            });
        });

        const calculateTotal = () => {
            const amenitiesTotal = sumItemTotal(selectedAmenities);
            const eventTotal = selectedEvent.reduce((sum, item) => sum + (Number(item.price || 0)), 0);
            const diningTotal = selectedDining.reduce((sum, item) => sum + ((Number(item.price || 0)) * (Number(item.quantity || 1))), 0);
            const extraGuestsTotal = selectedExtraGuests * selectedExtraGuestPrice;
            return roomPrice + amenitiesTotal + eventTotal + diningTotal + extraGuestsTotal;
        };

        const syncDiningQuantity = (card, nextValue) => {
            const input = card.querySelector('.dining-quantity');
            if (!input) {
                return;
            }
            const validValue = Math.max(1, Number(nextValue) || 1);
            input.value = validValue;
            const selectedDiningItem = selectedDining.find(item => item.id === card.dataset.diningId);
            if (selectedDiningItem) {
                selectedDiningItem.quantity = validValue;
                updateSummary();
            }
        };

        document.querySelectorAll('.qty-btn').forEach(button => {
            button.addEventListener('click', function () {
                const card = this.closest('.reservation-card');
                const input = card?.querySelector('.dining-quantity');
                if (!input) {
                    return;
                }
                const currentValue = Number(input.value) || 1;
                const delta = this.classList.contains('qty-increase') ? 1 : -1;
                syncDiningQuantity(card, currentValue + delta);
            });
        });

        document.querySelectorAll('.dining-quantity').forEach(input => {
            input.addEventListener('change', function () {
                const card = this.closest('.reservation-card');
                syncDiningQuantity(card, this.value);
            });
        });

        diningSchedule.addEventListener('change', function () {
            selectedDining.forEach(item => {
                item.schedule = this.value;
            });
            updateSummary();
        });
        diningTable.addEventListener('change', function () {
            selectedDining.forEach(item => {
                item.table = this.value;
            });
            updateSummary();
        });

        document.querySelectorAll('.amenity-quantity, .amenity-date, .amenity-time').forEach(input => {
            input.addEventListener('change', function () {
                const card = this.closest('.reservation-card');
                const amenity = selectedAmenities.find(item => item.id === card.dataset.amenityId);
                if (amenity) {
                    amenity.quantity = Number(card.querySelector('.amenity-quantity')?.value || 1);
                    amenity.date = card.querySelector('.amenity-date')?.value || '';
                    amenity.time = card.querySelector('.amenity-time')?.value || '';
                    updateSummary();
                }
            });
        });

        document.querySelectorAll('.event-date, .event-start-time, .event-end-time, .event-guests').forEach(input => {
            input.addEventListener('change', function () {
                const card = this.closest('.reservation-card');
                const eventItem = selectedEvent.find(item => item.id === card.dataset.eventId);
                if (eventItem) {
                    eventItem.guests = Number(card.querySelector('.event-guests')?.value || 1);
                    eventItem.date = card.querySelector('.event-date')?.value || '';
                    eventItem.startTime = card.querySelector('.event-start-time')?.value || '';
                    eventItem.endTime = card.querySelector('.event-end-time')?.value || '';
                    updateSummary();
                }
            });
        });

        document.getElementById('diningDate').addEventListener('change', function () {
            selectedDining.forEach(item => {
                item.date = this.value;
            });
            updateSummary();
        });

        document.querySelectorAll('.room-extra-guests').forEach(select => {
            select.addEventListener('change', function () {
                const card = this.closest('.reservation-card');
                if (selectedRoom === card.dataset.name) {
                    selectedExtraGuests = Number(this.value);
                    selectedExtraGuestPrice = Number(card.dataset.extraGuestPrice);
                    updateSummary();
                }
            });
        });

        items.forEach(button => {
            button.addEventListener('click', function () {
                const title = this.dataset.title;
                const price = Number(this.dataset.price);
                const card = this.closest('.reservation-card');
                const category = card.dataset.category;

                if (category === 'room') {
                    const isSameRoomSelected = selectedRoom === title && roomPrice === price;

                    if (isSameRoomSelected) {
                        selectedRoom = null;
                        roomPrice = 0;
                        selectedAmenities = [];
                        reservationRoomId.value = '';
                        selectedExtraGuests = 0;
                        selectedExtraGuestPrice = Number(card.dataset.extraGuestPrice || 650);
                        document.querySelectorAll('.room-extra-guests').forEach(select => select.value = '0');
                        items.forEach(btn => {
                            if (btn.closest('.reservation-card').dataset.category === 'room') {
                                btn.textContent = 'Add to Reservation';
                            }
                            if (btn.closest('.reservation-card').dataset.category === 'amenities') {
                                btn.textContent = 'Add to Reservation';
                                btn.disabled = false;
                            }
                        });
                    } else {
                        selectedRoom = title;
                        roomPrice = price;
                        selectedExtraGuestPrice = Number(card.dataset.extraGuestPrice || 650);
                        selectedExtraGuests = Number(card.querySelector('.room-extra-guests')?.value || 0);
                        reservationRoomId.value = card.dataset.roomId || '';
                        items.forEach(btn => {
                            if (btn.closest('.reservation-card').dataset.category === 'room') {
                                btn.textContent = 'Add to Reservation';
                            }
                            if (btn.closest('.reservation-card').dataset.category === 'amenities') {
                                btn.disabled = false;
                                if (selectedAmenities.some(item => item.id === btn.closest('.reservation-card').dataset.amenityId)) {
                                    btn.textContent = 'Selected';
                                } else {
                                    btn.textContent = 'Add to Reservation';
                                }
                            }
                        });
                        this.textContent = 'Selected';
                    }
                } else if (category === 'amenities') {
                    if (!selectedRoom) {
                        alert('Amenities can only be selected when a room is booked.');
                        return;
                    }

                    const itemIndex = selectedAmenities.findIndex(item => item.id === card.dataset.amenityId);
                    if (itemIndex === -1) {
                        const quantity = Number(card.querySelector('.amenity-quantity')?.value || 1);
                        selectedAmenities.push({
                            id: card.dataset.amenityId,
                            title,
                            price,
                            quantity,
                            date: card.querySelector('.amenity-date')?.value || '',
                            time: card.querySelector('.amenity-time')?.value || '',
                        });
                        this.textContent = 'Selected';
                    } else {
                        selectedAmenities.splice(itemIndex, 1);
                        this.textContent = 'Add to Reservation';
                    }
                } else if (category === 'event_place') {
                    const eventIndex = selectedEvent.findIndex(item => item.id === card.dataset.eventId);
                    if (eventIndex === -1) {
                        selectedEvent.push({
                            id: card.dataset.eventId,
                            type: card.dataset.eventType,
                            title,
                            price,
                            guests: Number(card.querySelector('.event-guests')?.value || 1),
                            date: card.querySelector('.event-date')?.value || '',
                            startTime: card.querySelector('.event-start-time')?.value || '',
                            endTime: card.querySelector('.event-end-time')?.value || '',
                        });
                        this.textContent = 'Selected';
                    } else {
                        selectedEvent.splice(eventIndex, 1);
                        this.textContent = 'Add to Reservation';
                    }
                } else if (category === 'dining') {
                    const diningIndex = selectedDining.findIndex(item => item.id === card.dataset.diningId);
                    const quantity = Number(card.querySelector('.dining-quantity')?.value || 1);
                    if (diningIndex === -1) {
                        selectedDining.push({
                            id: card.dataset.diningId,
                            title,
                            price,
                            schedule: diningSchedule.value || card.dataset.schedule || '',
                            table: diningTable.value || '',
                            date: document.getElementById('diningDate')?.value || '',
                            quantity,
                        });
                        this.textContent = 'Selected';
                    } else {
                        selectedDining[diningIndex].quantity = quantity;
                        selectedDining.splice(diningIndex, 1);
                        this.textContent = 'Add to Reservation';
                    }
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
            confirmBtn.innerHTML = 'Continue to Confirm <i class="fas fa-arrow-right"></i>';
            confirmationModal.classList.add('open');
            confirmationModal.style.display = 'flex';
            confirmationModal.setAttribute('aria-hidden', 'false');
            notificationToast.classList.remove('show', 'hide');
            notificationToast.style.display = 'none';
        };

        const closeConfirmationModal = () => {
            confirmationOriginalParent.appendChild(confirmationModal);
            reservationPage.classList.remove('details-mode');
            reservationPage.classList.remove('confirm-step');
            confirmBtn.innerHTML = 'Continue to Details <i class="fas fa-arrow-right"></i>';
            confirmBtn.hidden = false;
            seeReceiptBtn.hidden = true;
            confirmationModal.classList.remove('open');
            confirmationModal.style.display = 'none';
            confirmationModal.setAttribute('aria-hidden', 'true');
            notificationToast.classList.remove('show', 'hide');
            notificationToast.style.display = 'none';
        };

        confirmBtn.addEventListener('click', function () {
            if (reservationPage.classList.contains('details-mode')) {
                if (!reservationPage.classList.contains('confirm-step')) {
                    reservationPage.classList.add('confirm-step');
                    modalConfirmBtn.textContent = 'Submit Reservation';
                    modalCancelBtn.textContent = 'Back to Details';
                    confirmBtn.hidden = true;
                    seeReceiptBtn.hidden = false;
                }
                return;
            }

            if (selectedAmenities.length > 0 && !selectedRoom) {
                alert('Amenities can only be selected when a room is booked.');
                return;
            }

            if (!selectedRoom && selectedEvent.length === 0 && selectedDining.length === 0) {
                alert('Please select a room, event place, or dining before confirming your reservation.');
                return;
            }

            if (selectedRoom && (!checkIn.value || !checkOut.value)) {
                alert('Please select check-in and check-out dates.');
                return;
            }
            openConfirmationModal();
        });

        confirmationCloseBtn.addEventListener('click', closeConfirmationModal);
        modalCancelBtn.addEventListener('click', function () {
            if (reservationPage.classList.contains('confirm-step')) {
                reservationPage.classList.remove('confirm-step');
                modalConfirmBtn.textContent = 'Confirm Reservation';
                modalCancelBtn.textContent = 'Back to Select';
                confirmBtn.hidden = false;
                seeReceiptBtn.hidden = true;
                return;
            }

            closeConfirmationModal();
        });
        confirmationModal.addEventListener('click', function (event) {
            if (event.target === confirmationModal) {
                closeConfirmationModal();
            }
        });

        seeReceiptBtn.addEventListener('click', function () {
            const receiptItems = [
                ['1', 'Room - ' + confirmRoom.textContent, confirmRoomCharge.textContent, confirmRoomCharge.textContent],
                ['1', 'Amenities - ' + confirmAmenitiesTitle.textContent, confirmAmenitiesCharge.textContent, confirmAmenitiesCharge.textContent],
                ['1', 'Event - ' + confirmEventTitle.textContent, confirmEventCharge.textContent, confirmEventCharge.textContent],
                ['1', 'Dining - ' + confirmDiningTitle.textContent, confirmDiningCharge.textContent, confirmDiningCharge.textContent],
                [String(selectedExtraGuests), 'Extra person', `₱${selectedExtraGuests * selectedExtraGuestPrice}`, confirmExtraGuestCharge.textContent],
            ];
            receiptGuestName.textContent = detailsGuestName.value || 'Guest';
            receiptGuestEmail.textContent = detailsGuestEmail.value || 'guest@example.com';
            receiptNumber.textContent = confirmReservationId.textContent;
            receiptDate.textContent = new Date().toLocaleDateString('en-US');
            receiptCheckIn.textContent = confirmArrivingOn.textContent;
            receiptCheckOut.textContent = confirmCheckOut.textContent;
            receiptGuests.textContent = confirmGuests.textContent;
            receiptRoom.textContent = confirmRoom.textContent;
            receiptContent.innerHTML = `<table class="receipt-table"><thead><tr><th>Quantity</th><th>Description</th><th>Unit Price</th><th>Amount</th></tr></thead><tbody>${receiptItems.map(([quantity, description, unitPrice, amount]) => `<tr><td>${escapeHtml(quantity)}</td><td>${escapeHtml(description)}</td><td>${escapeHtml(unitPrice)}</td><td>${escapeHtml(amount)}</td></tr>`).join('')}<tr class="receipt-total-row"><td colspan="3">Total</td><td>${escapeHtml(confirmTotalAmount.textContent)}</td></tr></tbody></table>`;
            receiptModal.classList.add('open');
            receiptModal.setAttribute('aria-hidden', 'false');
        });

        const closeReceiptModal = () => {
            receiptModal.classList.remove('open');
            receiptModal.setAttribute('aria-hidden', 'true');
        };

        receiptCloseBtn.addEventListener('click', closeReceiptModal);
        receiptModal.addEventListener('click', function (event) {
            if (event.target === receiptModal) {
                closeReceiptModal();
            }
        });

        detailsTerms.addEventListener('change', function () {
            if (this.checked) {
                notificationToast.classList.remove('show', 'hide');
                notificationToast.style.display = 'none';
            }
        });

        const loadReceiptPdfTools = () => Promise.all([
            new Promise((resolve, reject) => {
                if (window.html2canvas) {
                    resolve();
                    return;
                }
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            }),
            new Promise((resolve, reject) => {
                if (window.jspdf?.jsPDF) {
                    resolve();
                    return;
                }
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            }),
        ]);

        const downloadReceiptAsPdf = async () => {
            const downloadButtonLabel = receiptDownloadBtn.innerHTML;
            receiptDownloadBtn.disabled = true;
            receiptDownloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing...';
            try {
                await loadReceiptPdfTools();
                const receiptCard = receiptModal.querySelector('.receipt-card');
                const printableReceipt = receiptCard.cloneNode(true);
                printableReceipt.querySelector('.receipt-close')?.remove();
                printableReceipt.querySelector('.receipt-actions')?.remove();
                printableReceipt.style.position = 'absolute';
                printableReceipt.style.left = '-10000px';
                printableReceipt.style.top = '0';
                printableReceipt.style.width = `${receiptCard.offsetWidth}px`;
                printableReceipt.style.maxHeight = 'none';
                printableReceipt.style.height = 'auto';
                printableReceipt.style.overflow = 'visible';
                document.body.appendChild(printableReceipt);
                const canvas = await window.html2canvas(printableReceipt, { scale: 2, backgroundColor: '#ffffff' });
                printableReceipt.remove();
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const margin = 10;
                const imageWidth = pageWidth - (margin * 2);
                const imageHeight = (canvas.height * imageWidth) / canvas.width;
                const imageData = canvas.toDataURL('image/jpeg', 0.95);
                let remainingHeight = imageHeight;
                let offset = 0;
                pdf.addImage(imageData, 'JPEG', margin, margin, imageWidth, imageHeight);
                remainingHeight -= pageHeight - (margin * 2);
                while (remainingHeight > 0) {
                    offset += pageHeight - (margin * 2);
                    pdf.addPage();
                    pdf.addImage(imageData, 'JPEG', margin, margin - offset, imageWidth, imageHeight);
                    remainingHeight -= pageHeight - (margin * 2);
                }
                pdf.save(`${confirmReservationId.textContent || 'reservation'}-receipt.pdf`);
            } catch (error) {
                alert('The receipt PDF could not be downloaded. Please try again.');
            } finally {
                receiptDownloadBtn.disabled = false;
                receiptDownloadBtn.innerHTML = downloadButtonLabel;
            }
        };

        const openReceiptPrintWindow = () => {
            const printWindow = window.open('', '_blank', 'width=600,height=700');
            if (!printWindow) {
                return;
            }
            const printableReceipt = receiptModal.querySelector('.receipt-card').cloneNode(true);
            printableReceipt.querySelector('.receipt-close')?.remove();
            printableReceipt.querySelector('.receipt-actions')?.remove();
            printWindow.document.write(`<html><head><title>${confirmReservationId.textContent} Receipt</title><style>
                *{box-sizing:border-box}body{margin:0;padding:24px;background:#fff;font-family:Arial,sans-serif;color:#172033}.receipt-card{width:100%;padding:28px;background:#fff}.receipt-header{position:relative;display:block;padding-bottom:14px;border-bottom:4px solid #c7d8e8}.receipt-brand{margin:0;color:#07549a;font-size:32px;font-weight:400;text-align:center}.receipt-contact{display:flex;justify-content:center;gap:18px;margin:12px 0 4px;color:#727b85;font-size:12px}.receipt-subcontact{text-align:center;margin:0;color:#727b85;font-size:12px}.receipt-heading-row{margin:24px 0 16px;text-align:center}.receipt-heading{margin:0;color:#07549a;font-size:32px;letter-spacing:.04em}.receipt-title-row{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin:0 0 18px}.receipt-paid-by h4,.receipt-booking h4,.receipt-notes h4{margin:0 0 8px;color:#07549a;font-size:14px}.receipt-paid-by p,.receipt-booking p,.receipt-notes p{margin:3px 0;color:#4b5563;font-size:13px}.receipt-booking{min-width:220px}.receipt-booking p{display:flex;justify-content:space-between;gap:18px}.receipt-booking strong{color:#4b5563;font-weight:600}.receipt-content{color:#566176;font-size:12px}.receipt-table{width:100%;border:1px solid #7fa9d0;border-spacing:0}.receipt-table th{padding:9px 8px;color:#fff;background:#07549a;font-size:11px;text-align:left}.receipt-table td{padding:9px 8px;border-top:1px solid #d9e5ef;color:#4b5563;font-size:12px}.receipt-table th:not(:first-child),.receipt-table td:not(:first-child){text-align:right}.receipt-table .receipt-total-row td{border-top:2px solid #7fa9d0;color:#07549a;font-weight:800}.receipt-notes{margin-top:18px}@media print{body{padding:0}.receipt-card{padding:0}}
                </style></head><body>${printableReceipt.outerHTML}</body></html>`);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        };

        receiptDownloadBtn.addEventListener('click', downloadReceiptAsPdf);
        receiptPrintBtn.addEventListener('click', openReceiptPrintWindow);

        modalConfirmBtn.addEventListener('click', function () {
            if (modalConfirmBtn.disabled) {
                return;
            }

            if (!reservationPage.classList.contains('confirm-step')) {
                if (!detailsTerms.checked) {
                    showNotification('Please check Terms & Conditions before proceeding.');
                    return;
                }
                reservationPage.classList.add('confirm-step');
                modalConfirmBtn.textContent = 'Submit Reservation';
                modalCancelBtn.textContent = 'Back to Details';
                confirmBtn.hidden = true;
                seeReceiptBtn.hidden = false;
                return;
            }

            modalConfirmBtn.disabled = true;
            modalConfirmBtn.textContent = 'Submitting...';

            reservationGuestName.value = detailsGuestName.value || 'Guest';
            reservationGuestEmail.value = detailsGuestEmail.value || 'guest@example.com';
            reservationGuestPhone.value = detailsGuestPhone.value || '0000000000';
            reservationSpecialRequests.value = detailsSpecialRequest.value;
            const firstEventSelection = selectedEvent[0] || null;
            const firstAmenitySelection = selectedAmenities[0] || null;
            const hasRoomSelection = Boolean(selectedRoom);

            if (hasRoomSelection) {
                reservationCheckInTime.value = arrivalTime.value || '';
                reservationCheckOutTime.value = '';
            } else if (firstEventSelection) {
                reservationCheckInTime.value = firstEventSelection.startTime || '';
                reservationCheckOutTime.value = firstEventSelection.endTime || '';
            } else if (firstAmenitySelection) {
                reservationCheckInTime.value = firstAmenitySelection.time || '';
                reservationCheckOutTime.value = '';
            } else {
                reservationCheckInTime.value = arrivalTime.value || '';
                reservationCheckOutTime.value = '';
            }

            const existingPaymentMethodInput = reservationForm.querySelector('input[name="payment_method"]');
            const existingPaymentDetailsInput = reservationForm.querySelector('input[name="payment_details"]');
            const existingAmountPaidInput = reservationForm.querySelector('input[name="amount_paid"]');
            if (existingPaymentMethodInput) existingPaymentMethodInput.remove();
            if (existingPaymentDetailsInput) existingPaymentDetailsInput.remove();
            if (existingAmountPaidInput) existingAmountPaidInput.remove();

            const paymentMethodInput = document.createElement('input');
            paymentMethodInput.type = 'hidden';
            paymentMethodInput.name = 'payment_method';
            paymentMethodInput.value = selectedPaymentMethod;
            reservationForm.appendChild(paymentMethodInput);

            const paymentDetailsInput = document.createElement('input');
            paymentDetailsInput.type = 'hidden';
            paymentDetailsInput.name = 'payment_details';
            paymentDetailsInput.value = getPaymentDetails();
            reservationForm.appendChild(paymentDetailsInput);

            const amountPaidInput = document.createElement('input');
            amountPaidInput.type = 'hidden';
            amountPaidInput.name = 'amount_paid';
            amountPaidInput.value = selectedPaymentMethod === 'Cash / Pay at Hotel' ? Number(reservationTotalAmount.value || 0) : (
                selectedPaymentMethod === 'GCash' ? getPaymentAmountValue('gcashPaymentAmount') :
                selectedPaymentMethod === 'Maya' ? getPaymentAmountValue('mayaPaymentAmount') :
                selectedPaymentMethod === 'Credit / Debit Card' ? getPaymentAmountValue('cardPaymentAmount') :
                getPaymentAmountValue('transferAmount')
            );
            reservationForm.appendChild(amountPaidInput);

            reservationForm.submit();
        });

        clearBtn.addEventListener('click', function () {
            selectedRoom = null;
            selectedAmenities = [];
            selectedEvent = [];
            selectedDining = [];
            diningSchedule.value = '';
            diningTable.value = '';
            selectedPaymentMethod = 'Cash / Pay at Hotel';
            roomPrice = 0;
            checkIn.value = '';
            checkOut.value = '';
            arrivalTime.value = '15:00';
            selectedExtraGuests = 0;
            selectedExtraGuestPrice = 650;
            document.querySelectorAll('.room-extra-guests').forEach(select => select.value = '0');
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
