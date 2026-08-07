document.addEventListener('DOMContentLoaded', function () {
    const rows = Array.from(document.querySelectorAll('[data-room-row]'));
    const searchRoomInput = document.getElementById('search-room-number');
    const searchTypeInput = document.getElementById('search-room-type');
    const statusSelect = document.getElementById('filter-status');
    const floorSelect = document.getElementById('filter-floor');
    const resetBtn = document.getElementById('reset-filters');
    const emptyState = document.getElementById('empty-state');
    const modalOverlay = document.getElementById('details-modal');
    const modalClose = document.getElementById('close-modal');
    const detailFields = {
        roomNumber: document.getElementById('detail-room-number'),
        roomType: document.getElementById('detail-room-type'),
        floor: document.getElementById('detail-floor'),
        capacity: document.getElementById('detail-capacity'),
        status: document.getElementById('detail-status'),
        housekeepingStatus: document.getElementById('detail-housekeeping-status'),
        guest: document.getElementById('detail-guest'),
        checkin: document.getElementById('detail-checkin'),
        checkout: document.getElementById('detail-checkout'),
        housekeeper: document.getElementById('detail-housekeeper'),
        notes: document.getElementById('detail-notes')
    };

    const applyFilters = () => {
        const queryRoom = (searchRoomInput.value || '').trim().toLowerCase();
        const queryType = (searchTypeInput.value || '').trim().toLowerCase();
        const selectedStatus = (statusSelect.value || '').toLowerCase();
        const selectedFloor = (floorSelect.value || '').toLowerCase();
        let visibleCount = 0;

        rows.forEach((row) => {
            const roomNumber = (row.dataset.roomNumber || '').toLowerCase();
            const roomType = (row.dataset.roomType || '').toLowerCase();
            const status = (row.dataset.status || '').toLowerCase();
            const floor = (row.dataset.floor || '').toLowerCase();

            const matchesRoom = roomNumber.includes(queryRoom);
            const matchesType = roomType.includes(queryType);
            const matchesStatus = !selectedStatus || status === selectedStatus;
            const matchesFloor = !selectedFloor || floor === selectedFloor;

            const showRow = matchesRoom && matchesType && matchesStatus && matchesFloor;
            row.style.display = showRow ? '' : 'none';
            if (showRow) visibleCount += 1;
        });

        emptyState.style.display = visibleCount === 0 ? 'flex' : 'none';
    };

    [searchRoomInput, searchTypeInput, statusSelect, floorSelect].forEach((element) => {
        element.addEventListener('input', applyFilters);
        element.addEventListener('change', applyFilters);
    });

    resetBtn.addEventListener('click', () => {
        searchRoomInput.value = '';
        searchTypeInput.value = '';
        statusSelect.value = '';
        floorSelect.value = '';
        applyFilters();
    });

    document.querySelectorAll('[data-action="view"]').forEach((button) => {
        button.addEventListener('click', function () {
            const row = this.closest('[data-room-row]');
            activeRoomNumber = row.dataset.roomNumber;
            const record = {
                roomNumber: row.dataset.roomNumber,
                roomType: row.dataset.roomType,
                floor: row.dataset.floor,
                capacity: row.dataset.capacity,
                status: row.dataset.statusLabel,
                housekeepingStatus: row.dataset.housekeepingStatus,
                guest: row.dataset.guest,
                checkin: row.dataset.checkin,
                checkout: row.dataset.checkout,
                housekeeper: row.dataset.housekeeper,
                notes: row.dataset.notes
            };

            Object.entries(detailFields).forEach(([key, element]) => {
                element.textContent = record[key] || '—';
            });
            modalOverlay.classList.add('open');
        });
    });

    modalClose.addEventListener('click', () => modalOverlay.classList.remove('open'));
    modalOverlay.addEventListener('click', function (event) {
        if (event.target === this) {
            this.classList.remove('open');
        }
    });

    const modalChangeStatus = document.getElementById('modal-change-status');
    const modalAssignHousekeeper = document.getElementById('modal-assign-housekeeper');
    let activeRoomNumber = null;

    modalChangeStatus.addEventListener('click', () => {
        if (!activeRoomNumber) {
            return;
        }
        alert(`Change status for room ${activeRoomNumber}`);
    });

    modalAssignHousekeeper.addEventListener('click', () => {
        if (!activeRoomNumber) {
            return;
        }
        alert(`Assign housekeeper for room ${activeRoomNumber}`);
    });

    document.getElementById('refresh-button').addEventListener('click', () => {
        rows.forEach((row) => {
            row.style.display = '';
        });
        emptyState.style.display = 'none';
        searchRoomInput.value = '';
        searchTypeInput.value = '';
        statusSelect.value = '';
        floorSelect.value = '';
    });

    applyFilters();
});
