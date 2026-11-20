const damagePage = document.getElementById('lapor-kerusakan-page');

if (damagePage) {
    const { reports, facilities, statuses, reporter, openForm } = JSON.parse(document.getElementById('damage-page-data').textContent);
    const search = document.getElementById('damage-search');
    const statusFilter = document.getElementById('damage-status-filter');
    const tbody = document.getElementById('damage-report-rows');
    const rowTemplate = document.getElementById('damage-row-template');
    const evidenceTemplate = document.getElementById('damage-evidence-template');
    const detailDialog = document.getElementById('damage-detail-dialog');
    const imageDialog = document.getElementById('damage-image-dialog');
    const formDialog = document.getElementById('damage-form-dialog');
    const form = document.getElementById('damage-report-form');
    const facilityField = document.getElementById('damage-facility');
    const facilityTypeField = document.getElementById('damage-facility-type');
    const descriptionField = document.getElementById('damage-description');
    const photoField = document.getElementById('damage-photo');
    const dateFormatter = new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric', timeZone: 'Asia/Jakarta' });
    const photoUrls = new Set();
    let draftPhotoUrl = null;
    let selectedReport = null;
    let selectedStage = null;
    let selectedPhotos = [];
    const workflow = [
        { title: 'Laporan Diajukan', dateField: 'tanggal_lapor', dateLabel: 'Tanggal pengajuan', pending: 'Belum diajukan', icon: 'description' },
        { title: 'Disposisi & Teknisi', dateField: 'tanggal_disposisi', dateLabel: 'Tanggal penugasan', pending: 'Menunggu disposisi', icon: 'assignment_ind' },
        { title: 'Dalam Pengerjaan', dateField: 'tanggal_pengerjaan', dateLabel: 'Tanggal pengerjaan', pending: 'Belum dimulai', icon: 'build' },
        { title: 'Selesai', dateField: 'tanggal_selesai', dateLabel: 'Tanggal selesai', pending: 'Belum selesai', icon: 'task_alt' },
    ];

    const date = value => value ? dateFormatter.format(new Date(`${value}T12:00:00+07:00`)) : '—';
    const ticket = report => `TKT-${report.tanggal_lapor.slice(0, 4)}-${String(report.id_laporan).padStart(4, '0')}`;
    const text = (id, value) => document.getElementById(id).textContent = value;

    function renderReports() {
        const query = search.value.trim().toLocaleLowerCase('id-ID');
        const filtered = reports.filter(report => {
            const facility = facilities[report.id_fasilitas];
            const haystack = `${ticket(report)} ${report.deskripsi} ${facility.nama_fasilitas} ${facility.gedung} ${facility.lokasi}`.toLocaleLowerCase('id-ID');
            return (statusFilter.value === 'semua' || statusFilter.value === report.status_perbaikan) && haystack.includes(query);
        });
        const fragment = document.createDocumentFragment();
        for (const report of filtered) {
            const row = rowTemplate.content.firstElementChild.cloneNode(true);
            const facility = facilities[report.id_fasilitas];
            const status = statuses[report.status_perbaikan];
            const values = { description: report.deskripsi, facility: facility.nama_fasilitas, location: facility.lokasi, building: facility.gedung, date: date(report.tanggal_lapor), status: status.label, completed: date(report.tanggal_selesai) };
            row.dataset.reportId = report.id_laporan;
            row.querySelectorAll('[data-report-field]').forEach(element => element.textContent = values[element.dataset.reportField]);
            row.querySelector('[data-report-field="status"]').className = `inline-flex rounded-full px-2.5 py-1 text-[11px] font-medium whitespace-nowrap ${status.warna}`;
            const button = row.querySelector('[data-open-report]');
            button.dataset.openReport = report.id_laporan;
            button.setAttribute('aria-label', `Lihat detail laporan ${ticket(report)}`);
            fragment.append(row);
        }
        tbody.replaceChildren(fragment);
        document.getElementById('damage-history-empty').hidden = filtered.length !== 0;
        text('damage-history-count', `Menampilkan ${filtered.length} dari ${reports.length} laporan`);
        const counts = { total: reports.length, menunggu: 0, dalam_pengerjaan: 0, selesai: 0 };
        reports.forEach(report => {
            if (statuses[report.status_perbaikan].stage <= 2) counts.menunggu++;
            else counts[report.status_perbaikan]++;
        });
        damagePage.querySelectorAll('[data-damage-count]').forEach(element => element.textContent = counts[element.dataset.damageCount]);
    }

    function renderEvidence(report, stage) {
        const section = document.getElementById('damage-documentation-section');
        const grid = document.getElementById('damage-documentation-grid');
        grid.replaceChildren();
        selectedPhotos = [];
        section.hidden = stage < 3;
        // Tahap 1 dan 2 sama sekali tidak membuat elemen foto pada modal status.
        if (stage < 3) return;

        selectedPhotos = report.photos || [];
        document.getElementById('damage-documentation-empty').hidden = selectedPhotos.length !== 0;
        grid.className = 'grid grid-cols-1 sm:grid-cols-2 gap-4';
        text('damage-documentation-count', `${selectedPhotos.length} foto`);
        text('damage-documentation-description', 'Foto yang dilampirkan pada laporan kerusakan.');
        selectedPhotos.forEach((photo, index) => {
            const card = evidenceTemplate.content.firstElementChild.cloneNode(true);
            const image = card.querySelector('[data-evidence-image]');
            image.src = photo.src;
            image.alt = photo.title;
            card.querySelector('[data-evidence-label]').textContent = photo.label;
            card.querySelector('[data-evidence-title]').textContent = photo.title;
            card.querySelector('[data-evidence-description]').textContent = photo.description;
            card.querySelector('[data-evidence-date]').textContent = date(photo.date);
            card.querySelector('[data-evidence-source]').textContent = photo.source;
            const button = card.querySelector('[data-evidence-index]');
            button.dataset.evidenceIndex = index;
            button.setAttribute('aria-label', `Perbesar foto: ${photo.title}`);
            grid.append(card);
        });
    }

    function renderSelectedStage(stage) {
        if (!selectedReport || !Number.isInteger(stage) || stage < 1 || stage > workflow.length) return;
        selectedStage = stage;
        const report = selectedReport;
        const currentStage = statuses[report.status_perbaikan].stage;
        const reached = stage <= currentStage;
        const stepInfo = workflow[stage - 1];
        detailDialog.querySelectorAll('[data-workflow-stage]').forEach(step => {
            const isSelected = Number(step.dataset.workflowStage) === stage;
            step.classList.toggle('is-selected', isSelected);
            step.querySelector('[data-select-stage]').setAttribute('aria-pressed', String(isSelected));
        });
        text('damage-stage-title', `Tahap ${stage} · ${stepInfo.title}`);
        text('damage-stage-state', !reached ? 'Belum dimulai' : stage === currentStage && currentStage < 4 ? 'Sedang aktif' : 'Sudah dilalui');
        const facility = facilities[report.id_fasilitas];
        const notes = [
            `${reporter} melaporkan kerusakan ${facility.nama_fasilitas.toLocaleLowerCase('id-ID')} pada ${date(report.tanggal_lapor)}. ${report.deskripsi}`,
            report.teknisi ? `Teknisi yang tercatat menangani laporan ini: ${report.teknisi}.` : 'Nama teknisi belum tercatat pada laporan ini.',
            currentStage === 3
                ? `Status laporan saat ini: Dalam Pengerjaan.${report.teknisi ? ` Teknisi yang ditugaskan: ${report.teknisi}.` : ' Teknisi belum tercatat.'}`
                : 'Laporan telah melewati tahap pengerjaan berdasarkan status perbaikan yang tersimpan.',
            report.tanggal_selesai ? `Laporan ditandai selesai pada ${date(report.tanggal_selesai)}.` : 'Laporan ditandai selesai. Tanggal selesai belum tercatat.',
        ];
        const upcoming = [
            'Laporan belum diajukan.',
            'Laporan menunggu pemeriksaan Admin Layanan. Teknisi dan jadwal penanganan belum ditentukan.',
            'Status laporan belum mencapai tahap pengerjaan.',
            'Laporan belum ditandai selesai.',
        ];
        text('damage-detail-work-note', reached ? notes[stage - 1] : upcoming[stage - 1]);
        text('damage-detail-completed', reached ? (report[stepInfo.dateField] ? `${stepInfo.dateLabel}: ${date(report[stepInfo.dateField])}` : 'Tanggal tahap ini belum tercatat.') : stepInfo.pending);
        const hasTechnician = stage >= 2 && currentStage >= 2 && report.teknisi;
        text('damage-detail-technician', hasTechnician ? report.teknisi : 'Belum ditugaskan');
        text('damage-detail-technician-description', hasTechnician ? 'Sesuai penugasan yang tercatat' : 'Belum ada teknisi tercatat pada tahap ini');
        renderEvidence(report, reached ? stage : 0);
    }

    function openReport(id) {
        const report = reports.find(item => item.id_laporan === Number(id));
        if (!report) return;
        selectedReport = report;
        const facility = facilities[report.id_fasilitas];
        const status = statuses[report.status_perbaikan];
        const stage = status.stage;
        text('damage-detail-ticket', ticket(report));
        text('damage-detail-title', report.deskripsi.split(/\n|\.\s/)[0]);
        text('damage-detail-location', `${facility.gedung} · ${facility.lokasi}`);
        text('damage-detail-date', `Dilaporkan: ${date(report.tanggal_lapor)}`);
        text('damage-detail-status', status.label);
        document.getElementById('damage-detail-status').className = `inline-flex rounded-full px-3 py-1 text-caption-sm font-medium ${status.warna}`;
        text('damage-step-label', stage === 0 ? 'Status belum dipetakan ke tahapan' : stage === 4 ? '4 dari 4 tahap selesai' : `Tahap ${stage} dari 4 · Aktif`);
        text('damage-detail-description', report.deskripsi);
        const descriptions = [
            `Diajukan oleh ${reporter} melalui portal Client.`,
            report.teknisi ? `Teknisi tercatat: ${report.teknisi}.` : 'Teknisi belum tercatat.',
            stage >= 3 ? 'Sesuai status perbaikan yang tersimpan.' : 'Belum mencapai tahap pengerjaan.',
            stage === 4 ? 'Laporan telah ditandai selesai.' : 'Laporan belum ditandai selesai.',
        ];
        detailDialog.querySelectorAll('[data-workflow-stage]').forEach(step => {
            const position = Number(step.dataset.workflowStage);
            const isComplete = position < stage || stage === 4;
            const isCurrent = position === stage;
            step.className = `damage-step${isComplete ? ' is-complete' : ''}${isCurrent ? ' is-current' : ''}${isCurrent && stage === 3 ? ' is-working' : ''}${isCurrent && stage === 4 ? ' is-finished' : ''}`;
            if (isCurrent) step.setAttribute('aria-current', 'step');
            else step.removeAttribute('aria-current');
            const stepInfo = workflow[position - 1];
            step.querySelector('[data-step-icon]').textContent = isComplete ? 'check' : stepInfo.icon;
            step.querySelector('[data-step-date]').textContent = report[stepInfo.dateField] ? date(report[stepInfo.dateField]) : position <= stage ? 'Tanggal belum tercatat' : stepInfo.pending;
            step.querySelector('[data-step-date]').className = `block text-caption-sm mt-1 ${position > stage ? 'text-outline' : isCurrent && stage === 3 ? 'text-[#99620a]' : 'text-secondary'}`;
            step.querySelector('[data-step-description]').textContent = descriptions[position - 1];
            step.querySelector('[data-step-title]').className = `block text-label-md font-semibold ${position > stage ? 'text-outline' : 'text-primary'}`;
        });
        renderSelectedStage(Math.max(1, stage));
        detailDialog.showModal();
        document.getElementById('damage-detail-body').scrollTop = 0;
    }

    function discardDraftPhoto() {
        if (draftPhotoUrl) {
            URL.revokeObjectURL(draftPhotoUrl);
            photoUrls.delete(draftPhotoUrl);
            draftPhotoUrl = null;
        }
        document.getElementById('damage-photo-preview').hidden = true;
        document.getElementById('damage-photo-thumbnail').removeAttribute('src');
    }

    function updateFacilityOptions() {
        const selected = facilityField.value;
        const type = facilityTypeField.value;
        const options = Object.entries(facilities).filter(([, facility]) => facility.available && facility.jenis_fasilitas === type);
        const placeholder = !type ? 'Pilih jenis fasilitas terlebih dahulu' : options.length ? 'Pilih fasilitas' : 'Belum ada fasilitas untuk jenis ini';
        facilityField.replaceChildren(new Option(placeholder, ''));
        for (const [id, facility] of options) {
            const option = new Option(facility.nama_fasilitas, id);
            option.selected = id === selected;
            facilityField.append(option);
        }
        facilityField.disabled = !type || options.length === 0;
        form.querySelector('[type="submit"]').disabled = facilityField.disabled;
        updateFacilityLocation();
    }

    function updateFacilityLocation() {
        const facility = facilities[facilityField.value];
        const type = facilityTypeField.value;
        const description = facility ? `${facility.gedung} · ${facility.lokasi}`
            : !type ? 'Pilih jenis fasilitas untuk menampilkan daftar fasilitas yang sesuai.'
                : facilityField.disabled ? 'Belum ada fasilitas jenis ini yang tercatat. Hubungi Admin Layanan.'
                    : type === 'kamar' ? 'Daftar hanya memuat fasilitas kamar yang sedang kamu tempati.'
                        : 'Daftar hanya memuat fasilitas bersama di gedung tempat kamu tinggal.';
        text('damage-facility-location', description);
    }

    function resetForm() {
        discardDraftPhoto();
        form.reset();
        photoField.setCustomValidity('');
        descriptionField.setCustomValidity('');
        text('damage-photo-error', '');
        form.querySelector('[type="submit"]').textContent = 'Tambahkan Laporan';
        updateFacilityOptions();
    }

    document.getElementById('damage-add-report').addEventListener('click', () => {
        resetForm();
        formDialog.showModal();
        facilityTypeField.focus();
        document.getElementById('damage-form-body').scrollTop = 0;
    });
    damagePage.querySelectorAll('[data-close-damage-form]').forEach(button => button.addEventListener('click', () => formDialog.close()));
    formDialog.addEventListener('close', resetForm);
    facilityTypeField.addEventListener('change', updateFacilityOptions);
    facilityField.addEventListener('change', updateFacilityLocation);
    descriptionField.addEventListener('input', () => descriptionField.setCustomValidity(''));
    photoField.addEventListener('change', () => {
        discardDraftPhoto();
        photoField.setCustomValidity('');
        text('damage-photo-error', '');
        const file = photoField.files[0];
        if (!file) return;
        const allowed = ['image/jpeg', 'image/png'].includes(file.type) && /\.(jpe?g|png)$/i.test(file.name);
        const error = !allowed ? 'Pilih foto JPG, JPEG, atau PNG.' : file.size > 2 * 1024 * 1024 ? 'Ukuran foto maksimal 2 MB.' : file.size === 0 ? 'Foto tidak boleh kosong.' : '';
        if (error) {
            photoField.setCustomValidity(error);
            text('damage-photo-error', error);
            photoField.reportValidity();
            return;
        }
        draftPhotoUrl = URL.createObjectURL(file);
        photoUrls.add(draftPhotoUrl);
        document.getElementById('damage-photo-thumbnail').src = draftPhotoUrl;
        text('damage-photo-name', file.name);
        document.getElementById('damage-photo-preview').hidden = false;
    });
    form.addEventListener('submit', event => {
        const description = descriptionField.value.trim();
        descriptionField.setCustomValidity(description.length < 10 ? 'Jelaskan kerusakan minimal 10 karakter.' : '');
        if (!form.reportValidity()) {
            event.preventDefault();
            return;
        }
        const facilityId = Number(facilityField.value);
        if (!facilities[facilityId]?.available || facilities[facilityId].jenis_fasilitas !== facilityTypeField.value) {
            event.preventDefault();
            return;
        }
        descriptionField.value = description;
        const submit = form.querySelector('[type="submit"]');
        submit.disabled = true;
        submit.textContent = 'Mengirim laporan...';
    });

    damagePage.addEventListener('click', event => {
        const button = event.target.closest('[data-open-report]');
        if (button) openReport(button.dataset.openReport);
    });
    search.addEventListener('input', renderReports);
    statusFilter.addEventListener('change', renderReports);
    document.getElementById('damage-workflow').addEventListener('click', event => {
        const button = event.target.closest('[data-select-stage]');
        if (button) renderSelectedStage(Number(button.dataset.selectStage));
    });
    detailDialog.querySelectorAll('[data-close-damage-detail]').forEach(button => button.addEventListener('click', () => detailDialog.close()));
    detailDialog.addEventListener('close', () => {
        if (imageDialog.open) imageDialog.close();
        document.getElementById('damage-documentation-grid').replaceChildren();
        document.getElementById('damage-documentation-section').hidden = true;
        selectedReport = null;
        selectedStage = null;
        selectedPhotos = [];
    });
    document.getElementById('damage-documentation-grid').addEventListener('click', event => {
        const button = event.target.closest('[data-evidence-index]');
        if (!button || !selectedReport || selectedStage < 3 || selectedStage > statuses[selectedReport.status_perbaikan].stage) return;
        const photo = selectedPhotos[Number(button.dataset.evidenceIndex)];
        if (!photo) return;
        const image = document.getElementById('damage-full-image');
        image.src = photo.src;
        image.alt = photo.title;
        text('damage-image-title', photo.title);
        imageDialog.showModal();
    });
    document.getElementById('damage-close-image').addEventListener('click', () => imageDialog.close());
    imageDialog.addEventListener('close', () => document.getElementById('damage-full-image').removeAttribute('src'));
    window.addEventListener('beforeunload', () => photoUrls.forEach(url => URL.revokeObjectURL(url)));
    renderReports();
    updateFacilityOptions();
    if (openForm) formDialog.showModal();
}
