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
        const queryRoom = (searchRoomInput?.value || '').trim().toLowerCase();
        const queryType = (searchTypeInput?.value || '').trim().toLowerCase();
        const selectedStatus = (statusSelect?.value || '').toLowerCase();
        const selectedFloor = (floorSelect?.value || '').toLowerCase();
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

        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'flex' : 'none';
        }
    };

        [searchRoomInput, searchTypeInput, statusSelect, floorSelect].filter(Boolean).forEach((element) => {
        element.addEventListener('input', applyFilters);
        element.addEventListener('change', applyFilters);
    });

        resetBtn?.addEventListener('click', () => {
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

    modalClose?.addEventListener('click', () => modalOverlay.classList.remove('open'));
    modalOverlay?.addEventListener('click', function (event) {
        if (event.target === this) {
            this.classList.remove('open');
        }
    });

    const modalChangeStatus = document.getElementById('modal-change-status');
    const modalAssignHousekeeper = document.getElementById('modal-assign-housekeeper');
    let activeRoomNumber = null;

    modalChangeStatus?.addEventListener('click', () => {
        if (!activeRoomNumber) {
            return;
        }
        alert(`Change status for room ${activeRoomNumber}`);
    });

    modalAssignHousekeeper?.addEventListener('click', () => {
        if (!activeRoomNumber) {
            return;
        }
        alert(`Assign housekeeper for room ${activeRoomNumber}`);
    });

    document.getElementById('refresh-button')?.addEventListener('click', () => {
        rows.forEach((row) => {
            row.style.display = '';
        });
        emptyState.style.display = 'none';
        searchRoomInput.value = '';
        searchTypeInput.value = '';
        statusSelect.value = '';
        floorSelect.value = '';
    });

    const modalFields = {
        amenity: {
            modal: 'amenity-details-modal',
            row: '[data-action="view-amenity"]',
            fields: {
                name: 'amenityName', type: 'amenityType', location: 'amenityLocation', capacity: 'amenityCapacity', status: 'amenityStatus', hours: 'amenityHours', description: 'amenityDescription', reservationStatus: 'reservationStatus', guest: 'guest', reservationId: 'reservationId', reservationDate: 'reservationDate', startTime: 'startTime', endTime: 'endTime', guests: 'guests', lastCleaned: 'lastCleaned', maintenance: 'maintenanceStatus', notes: 'notes'
            },
            ids: { name: 'amenity-detail-name', type: 'amenity-detail-type', location: 'amenity-detail-location', capacity: 'amenity-detail-capacity', status: 'amenity-detail-status', hours: 'amenity-detail-hours', description: 'amenity-detail-description', reservationStatus: 'amenity-detail-reservation-status', guest: 'amenity-detail-guest', reservationId: 'amenity-detail-reservation-id', reservationDate: 'amenity-detail-reservation-date', startTime: 'amenity-detail-start-time', endTime: 'amenity-detail-end-time', guests: 'amenity-detail-guests', lastCleaned: 'amenity-detail-last-cleaned', maintenance: 'amenity-detail-maintenance', notes: 'amenity-detail-notes' }
        },
        event: {
            modal: 'event-details-modal',
            row: '[data-action="view-event"]',
            fields: { name: 'eventName', type: 'eventType', location: 'eventLocation', capacity: 'eventCapacity', status: 'eventStatus', size: 'eventSize', description: 'eventDescription', reservationStatus: 'reservationStatus', guest: 'guest', reservationId: 'reservationId', date: 'eventDate', startTime: 'startTime', endTime: 'endTime', guests: 'expectedGuests', setup: 'setupStatus', cleaning: 'cleaningStatus', maintenance: 'maintenanceStatus', notes: 'notes' },
            ids: { name: 'event-detail-name', type: 'event-detail-type', location: 'event-detail-location', capacity: 'event-detail-capacity', status: 'event-detail-status', size: 'event-detail-size', description: 'event-detail-description', reservationStatus: 'event-detail-reservation-status', guest: 'event-detail-guest', reservationId: 'event-detail-reservation-id', date: 'event-detail-date', startTime: 'event-detail-start-time', endTime: 'event-detail-end-time', guests: 'event-detail-guests', setup: 'event-detail-setup', cleaning: 'event-detail-cleaning', maintenance: 'event-detail-maintenance', notes: 'event-detail-notes' }
        },
        dining: {
            modal: 'dining-details-modal',
            row: '[data-action="view-dining"]',
            fields: { name: 'diningName', type: 'diningType', location: 'diningLocation', capacity: 'diningCapacity', status: 'diningStatus', reservationStatus: 'reservationStatus', guest: 'guest', reservationId: 'reservationId', date: 'reservationDate', time: 'reservationTime', guests: 'guests', order: 'order', seating: 'seatingStatus', requests: 'specialRequests', notes: 'notes' },
            ids: { name: 'dining-detail-name', type: 'dining-detail-type', location: 'dining-detail-location', capacity: 'dining-detail-capacity', status: 'dining-detail-status', reservationStatus: 'dining-detail-reservation-status', guest: 'dining-detail-guest', reservationId: 'dining-detail-reservation-id', date: 'dining-detail-date', time: 'dining-detail-time', guests: 'dining-detail-guests', order: 'dining-detail-order', seating: 'dining-detail-seating', requests: 'dining-detail-requests', notes: 'dining-detail-notes' }
        }
    };

    Object.values(modalFields).forEach((config) => {
        document.querySelectorAll(config.row).forEach((button) => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                Object.entries(config.fields).forEach(([key, dataKey]) => {
                    const target = document.getElementById(config.ids[key]);
                    if (target) target.textContent = row.dataset[dataKey] || '—';
                });
                document.getElementById(config.modal)?.classList.add('open');
            });
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => document.getElementById(button.dataset.closeModal)?.classList.remove('open'));
    });

    document.querySelectorAll('.modal-overlay').forEach((overlay) => {
        overlay.addEventListener('click', function (event) {
            if (event.target === this) this.classList.remove('open');
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach((modal) => modal.classList.remove('open'));
        }
    });

    applyFilters();
});
