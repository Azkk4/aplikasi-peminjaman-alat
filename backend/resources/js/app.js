import './bootstrap';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

const cropImage = document.getElementById('image-crop-source');
const cropModal = document.getElementById('image-crop-modal');
const cropCancel = document.getElementById('image-crop-cancel');
const cropApply = document.getElementById('image-crop-apply');
let cropper = null;
let cropInput = null;
let cropObjectUrl = null;

const closeCropper = () => {
	cropper?.destroy();
	cropper = null;
	cropInput = null;
	cropModal?.classList.add('hidden');
	cropModal?.classList.remove('flex');
	if (cropObjectUrl) URL.revokeObjectURL(cropObjectUrl);
	cropObjectUrl = null;
};

document.querySelectorAll('[data-crop-input]').forEach((input) => {
	input.addEventListener('change', () => {
		const file = input.files?.[0];
		if (!file) return;

		const error = input.parentElement.querySelector('[data-crop-error]');
		error?.remove();
		if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
			const message = document.createElement('p');
			message.dataset.cropError = 'true';
			message.className = 'mt-1 text-sm text-red-600';
			message.setAttribute('role', 'alert');
			message.textContent = file.size > 2 * 1024 * 1024
				? 'Ukuran gambar maksimal 2 MB.'
				: 'Pilih file gambar JPEG, PNG, atau WebP.';
			input.insertAdjacentElement('afterend', message);
			input.value = '';
			return;
		}

		cropInput = input;
		cropObjectUrl = URL.createObjectURL(file);
		cropImage.src = cropObjectUrl;
		cropModal.classList.remove('hidden');
		cropModal.classList.add('flex');
		cropper?.destroy();
		cropper = new Cropper(cropImage, {
			viewMode: 1,
			responsive: true,
			autoCropArea: 0.9,
			aspectRatio: input.dataset.cropAspect === 'square' ? 1 : NaN,
		});
	});
});

cropCancel?.addEventListener('click', () => {
	if (cropInput) cropInput.value = '';
	closeCropper();
});

cropApply?.addEventListener('click', () => {
	if (!cropper || !cropInput) return;
	const input = cropInput;
	const originalFile = input.files[0];
	cropper.getCroppedCanvas({ maxWidth: 2048, maxHeight: 2048 }).toBlob((blob) => {
		if (!blob) return;
		const croppedFile = new File([blob], `${originalFile.name.replace(/\.[^.]+$/, '')}.jpg`, { type: 'image/jpeg' });
		const transfer = new DataTransfer();
		transfer.items.add(croppedFile);
		input.files = transfer.files;

		const preview = document.querySelector(`[data-crop-preview="${input.dataset.cropInput}"]`);
		if (preview) {
			preview.src = URL.createObjectURL(croppedFile);
			preview.classList.remove('hidden');
		}
		closeCropper();
	}, 'image/jpeg', 0.9);
});

document.querySelectorAll('input[name="search"]').forEach((input, index) => {
	const form = input.closest('form');
	const suggestions = document.createElement('datalist');
	suggestions.id = `search-suggestions-${index}`;
	input.setAttribute('list', suggestions.id);
	form?.append(suggestions);

	const candidates = Array.from(document.querySelectorAll('main tbody td, main article h2, main article p'))
		.map((element) => element.textContent.trim())
		.flatMap((text) => text.split(/[·,]/).map((part) => part.trim()))
		.filter((text, itemIndex, values) => text.length > 1 && !/^\d+$/.test(text) && values.indexOf(text) === itemIndex);

	const updateSuggestions = () => {
		const query = input.value.trim().toLocaleLowerCase();
		suggestions.replaceChildren();
		candidates
			.filter((candidate) => !query || candidate.toLocaleLowerCase().includes(query))
			.slice(0, 8)
			.forEach((candidate) => {
				const option = document.createElement('option');
				option.value = candidate;
				suggestions.append(option);
			});
	};

	input.addEventListener('input', updateSuggestions);
	updateSuggestions();
});

document.querySelectorAll('form[method="GET"]').forEach((form) => {
	form.addEventListener('submit', () => {
		document.getElementById('page-loading-skeleton')?.classList.remove('hidden');
	});
});
