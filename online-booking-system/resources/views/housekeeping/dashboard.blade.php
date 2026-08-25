@extends('housekeeping.layout')

@section('content')

<style>
	.hk-dashboard {
		width: 100%;
		font-family: 'Poppins', sans-serif;
		color: #111827;
	}

	.hk-dashboard * {
		box-sizing: border-box;
	}

	.dashboard-header {
		background: linear-gradient(135deg, #8d1010 0%, #620808 100%);
		border-radius: 16px;
		padding: 20px 24px 18px;
		color: #fff;
		margin-bottom: 18px;
		box-shadow: 0 8px 24px rgba(98, 8, 8, 0.08);
	}

	.header-content {
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 18px;
	}

	.header-label {
		font-size: 10px;
		letter-spacing: 0.14em;
		text-transform: uppercase;
		color: #f9d8c7;
		font-weight: 700;
		margin-bottom: 8px;
	}

	.dashboard-header h1 {
		margin: 0;
		font-size: 2.1rem;
		line-height: 1.15;
		font-weight: 700;
	}

	.dashboard-header p {
		margin: 6px 0 0;
		font-size: 0.82rem;
		color: rgba(255,255,255,0.76);
	}

	.date-card {
		min-width: 170px;
		padding: 10px 14px;
		border-radius: 12px;
		background: rgba(255,255,255,0.06);
		border: 1px solid rgba(255,255,255,0.12);
		text-align: center;
	}

	.date-title {
		font-size: 0.62rem;
		letter-spacing: 0.12em;
		text-transform: uppercase;
		color: #f9d8c7;
		font-weight: 700;
	}

	.date-value {
		margin-top: 6px;
		font-size: 0.86rem;
		font-weight: 600;
	}

	.stats-grid {
		display: grid;
		grid-template-columns: repeat(5, minmax(0, 1fr));
		gap: 14px;
		margin-bottom: 18px;
	}

	.stat-card {
		position: relative;
		background: #fff;
		border: 1px solid #e7e5e4;
		border-radius: 12px;
		padding: 14px 14px 12px;
		overflow: hidden;
	}

	.stat-card::before {
		content: "";
		position: absolute;
		left: 0;
		top: 0;
		bottom: 0;
		width: 4px;
		background: #890f0f;
	}

	.stat-top {
		display: flex;
		justify-content: flex-end;
		margin-bottom: 10px;
	}

	.stat-icon {
		width: 38px;
		height: 38px;
		border-radius: 10px;
		display: grid;
		place-items: center;
		font-size: 1rem;
	}

	.stat-card.total .stat-icon { background: #f3f4f6; color: #374151; }
	.stat-card.clean .stat-icon { background: #dcfce7; color: #15803d; }
	.stat-card.dirty .stat-icon { background: #fee2e2; color: #dc2626; }
	.stat-card.progress .stat-icon { background: #fef3c7; color: #d97706; }
	.stat-card.occupied .stat-icon { background: #ede9fe; color: #7c3aed; }

	.stat-label {
		font-size: 0.7rem;
		color: #6b7280;
		margin-bottom: 2px;
	}

	.stat-number {
		font-size: 2rem;
		line-height: 1.1;
		font-weight: 700;
		color: #111827;
	}

	.stat-card.clean .stat-number { color: #15803d; }
	.stat-card.dirty .stat-number { color: #dc2626; }
	.stat-card.progress .stat-number { color: #d97706; }
	.stat-card.occupied .stat-number { color: #7c3aed; }

	.sub-stat {
		margin-top: 6px;
		color: #94a3b8;
		font-size: 0.68rem;
	}

	.sub-stat strong {
		color: #111827;
	}

	.progress-row {
		display: grid;
		grid-template-columns: 1.5fr 1fr;
		gap: 18px;
		margin-bottom: 18px;
	}

	.panel {
		background: #fff;
		border: 1px solid #e7e5e4;
		border-radius: 14px;
		padding: 16px 18px 14px;
	}

	.mini-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 12px;
		margin-bottom: 14px;
	}

	.mini-header h3,
	.card-head h3 {
		margin: 0;
		font-size: 1.05rem;
		font-weight: 700;
		color: #111827;
	}

	.mini-status {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		color: #4b5563;
		font-size: 0.72rem;
		font-weight: 600;
	}

	.live-dot {
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: #10b981;
		box-shadow: 0 0 0 4px rgba(16,185,129,0.12);
	}

	.update-time {
		color: #6b7280;
		font-size: 0.68rem;
	}

	.progress-main {
		display: flex;
		align-items: center;
		gap: 18px;
	}

	.donut-wrap {
		width: 126px;
		height: 126px;
		display: grid;
		place-items: center;
		flex-shrink: 0;
	}

	.donut-chart {
		position: relative;
		width: 100%;
		height: 100%;
		border-radius: 50%;
		background: conic-gradient(#e5e7eb 0 100%);
		display: grid;
		place-items: center;
	}

	.donut-chart::before {
		content: "";
		position: absolute;
		inset: 16px;
		border-radius: 50%;
		background: #fff;
	}

	.donut-inner {
		position: relative;
		z-index: 1;
		font-size: 1.1rem;
		font-weight: 700;
		color: #111827;
	}

	.progress-copy {
		flex: 1;
	}

	.progress-label {
		font-size: 0.8rem;
		color: #6b7280;
		margin-bottom: 10px;
	}

	.progress-label strong {
		font-size: 1.08rem;
		color: #111827;
	}

	.progress-bar {
		height: 9px;
		width: 100%;
		background: #e5e7eb;
		border-radius: 999px;
		overflow: hidden;
		margin-bottom: 14px;
	}

	.progress-bar span {
		display: block;
		height: 100%;
		width: 0;
		background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
		border-radius: inherit;
	}

	.progress-legend {
		display: grid;
		grid-template-columns: repeat(4, minmax(0, 1fr));
		gap: 8px;
	}

	.legend-block {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 4px;
		font-size: 0.68rem;
		color: #6b7280;
	}

	.legend-block strong {
		font-size: 1rem;
		color: #111827;
	}

	.dot {
		width: 8px;
		height: 8px;
		border-radius: 50%;
		display: inline-block;
	}

	.dot.clean { background: #10b981; }
	.dot.progress { background: #f59e0b; }
	.dot.dirty { background: #ef4444; }
	.dot.pending { background: #d1d5db; }

	.card-head {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 12px;
		margin-bottom: 12px;
	}

	.card-head a {
		text-decoration: none;
		color: #6b7280;
		font-size: 0.72rem;
		font-weight: 600;
	}

	.task-list {
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.task-item {
		display: flex;
		align-items: center;
		gap: 10px;
		padding: 8px 6px;
		border-radius: 10px;
		border: 1px solid #f1f5f9;
	}

	.task-icon {
		width: 34px;
		height: 34px;
		border-radius: 8px;
		display: grid;
		place-items: center;
		font-size: 0.75rem;
		flex-shrink: 0;
	}

	.task-icon.high { background: #fee2e2; color: #dc2626; }
	.task-icon.medium { background: #fef3c7; color: #d97706; }
	.task-icon.low { background: #ede9fe; color: #7c3aed; }

	.task-copy {
		flex: 1;
		min-width: 0;
	}

	.task-copy strong {
		display: block;
		font-size: 0.78rem;
		color: #111827;
		margin-bottom: 2px;
	}

	.task-copy span {
		display: block;
		font-size: 0.68rem;
		color: #6b7280;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.task-meta {
		display: flex;
		flex-direction: column;
		align-items: flex-end;
		gap: 3px;
		flex-shrink: 0;
	}

	.tag {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 3px 7px;
		border-radius: 999px;
		font-size: 0.58rem;
		font-weight: 700;
	}

	.tag.high { background: #fee2e2; color: #b91c1c; }
	.tag.medium { background: #fef3c7; color: #b45309; }
	.tag.low { background: #ede9fe; color: #6d28d9; }

	.task-meta time {
		font-size: 0.62rem;
		color: #6b7280;
	}

	.rooms-section {
		background: #fff;
		border: 1px solid #e7e5e4;
		border-radius: 14px;
		overflow: hidden;
	}

	.rooms-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 12px;
		padding: 16px 18px;
		border-bottom: 1px solid #f1f5f9;
	}

	.rooms-title h2 {
		margin: 0 0 4px;
		font-size: 1.05rem;
		font-weight: 700;
		color: #111827;
	}

	.rooms-title p {
		margin: 0;
		font-size: 0.72rem;
		color: #6b7280;
	}

	.status-legend {
		display: flex;
		align-items: center;
		gap: 8px;
		flex-wrap: wrap;
	}

	.legend-item {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 5px 9px;
		border-radius: 999px;
		font-size: 0.68rem;
		font-weight: 600;
	}

	.legend-item::before {
		content: "";
		width: 8px;
		height: 8px;
		border-radius: 50%;
		display: inline-block;
	}

	.legend-clean { background: #ecfdf5; color: #047857; }
	.legend-clean::before { background: #10b981; }
	.legend-dirty { background: #fef2f2; color: #b91c1c; }
	.legend-dirty::before { background: #ef4444; }
	.legend-progress { background: #fffbeb; color: #b45309; }
	.legend-progress::before { background: #f59e0b; }

	.table-wrapper { overflow-x: auto; }

	.rooms-table {
		width: 100%;
		border-collapse: collapse;
		min-width: 760px;
	}

	.rooms-table thead {
		background: #f8fafc;
	}

	.rooms-table th {
		padding: 12px 16px;
		text-align: left;
		border-bottom: 1px solid #e7e5e4;
		font-size: 0.64rem;
		color: #64748b;
		text-transform: uppercase;
		letter-spacing: 0.08em;
		font-weight: 700;
	}

	.rooms-table td {
		padding: 14px 16px;
		border-bottom: 1px solid #f1f5f9;
		font-size: 0.82rem;
		color: #374151;
	}

	.empty-state {
		text-align: center;
		padding: 44px 20px;
	}

	.empty-icon {
		width: 52px;
		height: 52px;
		margin: 0 auto 12px;
		border-radius: 14px;
		background: #f9ecec;
		color: #8d1010;
		display: grid;
		place-items: center;
		font-size: 1.4rem;
	}

	.empty-state h3 {
		margin: 0 0 6px;
		font-size: 1rem;
		font-weight: 700;
		color: #111827;
	}

	.empty-state p {
		margin: 0 auto;
		max-width: 420px;
		font-size: 0.72rem;
		color: #6b7280;
		line-height: 1.6;
	}

	@media (max-width: 1100px) {
		.stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
		.progress-row { grid-template-columns: 1fr; }
	}

	@media (max-width: 700px) {
		.stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
		.header-content { flex-direction: column; align-items: flex-start; }
		.date-card { width: 100%; }
		.rooms-header { flex-direction: column; align-items: flex-start; }
	}

	@media (max-width: 480px) {
		.dashboard-header { padding: 16px; }
		.dashboard-header h1 { font-size: 1.7rem; }
		.stats-grid { grid-template-columns: 1fr; }
		.progress-main { flex-direction: column; align-items: flex-start; }
		.progress-legend { grid-template-columns: repeat(2, minmax(0, 1fr)); }
		.task-item { flex-wrap: wrap; }
		.task-meta { width: 100%; align-items: flex-start; }
	}
</style>

<div class="hk-dashboard">
	<div class="dashboard-header">
		<div class="header-content">
			<div>
				<div class="header-label">Housekeeping Dashboard</div>
				<h1>Room Cleaning Overview</h1>
				<p>Monitor room readiness, cleaning progress, and housekeeping tasks.</p>
			</div>

			<div class="date-card">
				<div class="date-title">Today</div>
				<div class="date-value">{{ now()->format('F j, Y') }}</div>
			</div>
		</div>
	</div>

	<div class="progress-row">
		<div class="panel">
			<div class="mini-header">
				<h3>Today's Cleaning Progress</h3>
				<div class="mini-status"><span class="live-dot"></span>Live</div>
				<div class="update-time">Updated 2:53 PM</div>
			</div>

			<div class="progress-main">
				<div class="donut-wrap">
					<div class="donut-chart">
						<div class="donut-inner">0%</div>
					</div>
				</div>

				<div class="progress-copy">
					<div class="progress-label"><strong>0 / 0</strong> rooms cleaned</div>
					<div class="progress-bar"><span></span></div>

					<div class="progress-legend">
						<div class="legend-block"><span class="dot clean"></span><strong>0</strong><span>Clean</span></div>
						<div class="legend-block"><span class="dot progress"></span><strong>0</strong><span>In Progress</span></div>
						<div class="legend-block"><span class="dot dirty"></span><strong>0</strong><span>Dirty</span></div>
						<div class="legend-block"><span class="dot pending"></span><strong>0</strong><span>Pending</span></div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<div class="card-head">
				<h3>Priority Tasks</h3>
				<a href="{{ route('housekeeping.assigned-rooms') }}">View all</a>
			</div>

			<div class="task-list">
				<div class="empty-state">
					<div class="empty-icon"><i class="fas fa-clipboard-check"></i></div>
					<h3>No priority tasks</h3>
					<p>Priority housekeeping tasks will appear here when assigned.</p>
				</div>
			</div>
		</div>
	</div>

	<div class="rooms-section">
		<div class="rooms-header">
			<div class="rooms-title">
				<h2>Room Cleaning Status</h2>
				<p>Room information will appear here once rooms are assigned.</p>
			</div>

			<div class="status-legend">
				<span class="legend-item legend-clean">Clean</span>
				<span class="legend-item legend-dirty">Dirty</span>
				<span class="legend-item legend-progress">In Progress</span>
			</div>
		</div>

		<div class="table-wrapper">
			<table class="rooms-table">
				<thead>
					<tr>
						<th>Room</th>
						<th>Floor</th>
						<th>Room Status</th>
						<th>Cleaning Status</th>
						<th>Last Updated</th>
						<th>Update Cleaning</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="6">
							<div class="empty-state">
								<div class="empty-icon"><i class="fas fa-bed"></i></div>
								<h3>No rooms assigned yet</h3>
								<p>Rooms will appear here once they are assigned to housekeeping staff.</p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

@endsection
