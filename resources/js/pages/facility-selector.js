document.querySelectorAll('[data-facility-selector]').forEach(selector => {
    const list = selector.querySelector('[data-facility-options]');
    const all = selector.querySelector('[data-facility-all]');
    const dialog = selector.querySelector('[data-facility-dialog]');
    const name = selector.querySelector('[data-facility-name]');
    const error = selector.querySelector('[data-facility-error]');
    const save = selector.querySelector('[data-save-facility]');
    const message = selector.querySelector('[data-facility-message]');
    const inputs = () => [...list.querySelectorAll('input[type="checkbox"]')];

    function updateCount() {
        const fields = inputs();
        const selected = fields.filter(input => input.checked).length;
        all.checked = fields.length > 0 && selected === fields.length;
        all.indeterminate = selected > 0 && selected < fields.length;
        all.disabled = fields.length === 0;
        selector.querySelector('[data-facility-count]').textContent = `${selected} fasilitas dipilih`;
        selector.querySelector('[data-facility-empty]').hidden = fields.length > 0;
    }

    all.addEventListener('change', () => {
        inputs().forEach(input => input.checked = all.checked);
        updateCount();
    });
    list.addEventListener('change', updateCount);
    selector.querySelector('[data-add-facility]').addEventListener('click', () => {
        name.value = '';
        error.hidden = true;
        dialog.showModal();
        name.focus();
    });
    selector.querySelectorAll('[data-close-facility]').forEach(button => button.addEventListener('click', () => dialog.close()));

    async function addFacility() {
        if (save.disabled) return;
        error.hidden = true;
        const value = name.value.trim();
        if (!value) {
            error.textContent = 'Nama fasilitas wajib diisi.';
            error.hidden = false;
            name.focus();
            return;
        }
        save.disabled = true;
        save.textContent = 'Menyimpan...';
        try {
            const response = await fetch(selector.dataset.storeUrl, {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ nama_fasilitas: value, lingkup: selector.dataset.facilityScope }),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(response.status === 419 ? 'Sesi berakhir. Muat ulang halaman sebelum menambah fasilitas.' : data.errors?.nama_fasilitas?.[0] || data.message || 'Fasilitas gagal disimpan. Silakan coba lagi.');
            const id = String(data.id);
            let field = inputs().find(input => input.value === id);
            if (!field) {
                const card = selector.querySelector('[data-facility-option-template]').content.firstElementChild.cloneNode(true);
                field = card.querySelector('input');
                field.value = id;
                card.querySelector('span').textContent = data.name;
                list.append(card);
            }
            field.checked = true;
            updateCount();
            message.textContent = `${data.message} Simpan gedung atau kamar untuk menerapkan pilihan.`;
            message.hidden = false;
            dialog.close();
        } catch (exception) {
            error.textContent = exception.message || 'Tidak dapat terhubung ke server.';
            error.hidden = false;
        } finally {
            save.disabled = false;
            save.textContent = 'Tambah & Pilih';
        }
    }

    save.addEventListener('click', addFacility);
    name.addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            addFacility();
        }
    });
    updateCount();
});
