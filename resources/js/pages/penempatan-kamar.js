const placementPage = document.getElementById('penempatan-kamar-page');

if (placementPage) {
    const rows = [...placementPage.querySelectorAll('[data-placement-row]')];
    const period = document.getElementById('placement-period-filter');
    const status = document.getElementById('placement-status-filter');
    const count = document.getElementById('placement-history-count');
    const empty = document.getElementById('placement-history-empty');

    function filterHistory() {
        let visible = 0;
        rows.forEach(row => {
            const matchesPeriod = period.value === 'semua' || row.dataset.period === period.value;
            const matchesStatus = status.value === 'semua' || row.dataset.status === status.value;
            row.hidden = !(matchesPeriod && matchesStatus);
            if (!row.hidden) visible++;
        });
        empty.hidden = visible !== 0;
        count.textContent = `Menampilkan ${visible} dari ${rows.length} catatan penempatan`;
    }

    period.addEventListener('change', filterHistory);
    status.addEventListener('change', filterHistory);
    document.getElementById('placement-reset-filter').addEventListener('click', () => {
        period.value = 'semua';
        status.value = 'semua';
        filterHistory();
    });

    filterHistory();
}
