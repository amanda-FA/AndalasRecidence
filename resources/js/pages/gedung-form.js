const buildingPhotoInput = document.querySelector('[data-building-photo-input]');

if (buildingPhotoInput) {
    const preview = document.querySelector('[data-building-photo-preview]');
    const wrapper = document.querySelector('[data-building-photo-preview-wrap]');
    let previewUrl = null;

    buildingPhotoInput.addEventListener('change', () => {
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = null;
        const file = buildingPhotoInput.files[0];
        const currentPhoto = preview.dataset.currentPhoto;
        const supported = file && ['image/jpeg', 'image/png', 'image/webp'].includes(file.type);

        if (supported) {
            previewUrl = URL.createObjectURL(file);
            preview.src = previewUrl;
            wrapper.hidden = false;
        } else if (currentPhoto) {
            preview.src = currentPhoto;
            wrapper.hidden = false;
        } else {
            preview.removeAttribute('src');
            wrapper.hidden = true;
        }
    });
}
