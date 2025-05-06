/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// enable the interactive UI components from Flowbite
import 'flowbite';
import { Modal, Dropdown } from 'flowbite';

// Importation de Masonry
import Masonry from 'masonry-layout';
import './modal.js'; // Import the updated modal handling script

import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

import SortableUtil from './sortable_util.js';
const sortableUtil = new SortableUtil();

// DOM ready
document.addEventListener('DOMContentLoaded', () => {
  // --- Masonry init ---
  const grid = document.querySelector('.grid_masonry');
  if (grid) {
    const masonryTarget = document.querySelector('.grid-item');
    if (masonryTarget) {
      new Masonry(grid, {
        itemSelector: '.grid-item',
        columnWidth: '.grid-sizer',
        percentPosition: true,
      });
    }
  }

  // --- Flowbite dropdowns ---
  function initDropdowns() {
    document.querySelectorAll('[data-dropdown-toggle]').forEach(el => new Dropdown(el));
  }
  initDropdowns();

  let deletionOccurred = false;

  // --- Modal init ---
  const targetEl = document.getElementById('control-modal');
  if (targetEl) {
    const modalOptions = {
      placement: 'bottom-right',
      backdrop: 'dynamic',
      backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
      closable: true,
      onHide: () => {
        console.log('deletionOccurred on hide:', deletionOccurred);
        if (deletionOccurred) {
          console.log('Reloading page due to deletion...');
          window.location.reload();
        }
      },
    };
    const modal = new Modal(targetEl, modalOptions);

    // Load modal content
    function loadModalContent(projectId, action) {
      const modalContent = document.querySelector('#control-modal .modal-content');
      fetch(`/admin/projet/modal/${projectId}/${action}`)
        .then(r => r.text())
        .then(html => {
          modalContent.innerHTML = html;
          modal.show();
          modalContent
            .querySelectorAll('[data-modal-hide="control-modal"]')
            .forEach(btn => {
              btn.addEventListener('click', () => {
                modal.hide();
                const role = btn.dataset.action;
                console.log('Modal action:', role);
                console.log('hasChanged:', sortableUtil.hasChanged());
                // Si on a re-ordonné, on reload
                if (role === 'accept' && sortableUtil.hasChanged()) {
                  console.log('Order changed, reloading...');
                  // si on a re-ordonné, on reload
                  window.location.reload();
                }
              });
            });
            // ** 2) Si on est en mode “delete_section”, on bind les corbeilles **
            if (action === 'delete_section') {
              // Bind des corbeilles
              modalContent
                .querySelectorAll('.js-delete-section-btn')
                .forEach(btn => {
                  btn.addEventListener('click', async () => {
                    const sectionId = btn.dataset.sectionId;
                    try {
                      const res = await fetch(
                        `/admin/projet/delete/section/modal/${projectId}/${sectionId}`, 
                        { method: 'DELETE' }
                      );
                      if (!res.ok) throw new Error();
                      // on marque qu’une suppression a eu lieu
                      deletionOccurred = true;
                      // retire l’<li> du DOM
                      btn.closest('li').remove();
                    } catch (e) {
                      console.error(e);
                      alert('La suppression a échoué.');
                    }
                  });
                });
            }
          sortableUtil.initSortable();
        })
        .catch(err => console.error('Modal load error:', err));
    }
    document.querySelectorAll('#dropdownMenuEdit a').forEach(el => {
      el.addEventListener('click', e => {
        e.preventDefault();
        const projectId = document.querySelector('#project-id').value;
        const action = el.getAttribute('data-action');
        loadModalContent(projectId, action);
      });
    });
  }

  // --- Sortable init ---
  if (typeof sortableUtil.initSortable === 'function') {
    sortableUtil.initSortable();
  }

  // --- Crop / Upload Handlers ---
  const uploadForm        = document.getElementById('uploadForm');
  const projectIdEl       = document.getElementById('project-id');
  const imageInput        = document.getElementById('imageInput');
  const imagePreview      = document.getElementById('imagePreview');
  const cropButton        = document.getElementById('cropButton');
  const flowbiteArea      = document.getElementById('flowbite_area');
  const label             = document.querySelector("label[for='imageInput']");
  const coverImgContainer = document.getElementById('cover_img_container');
  const cancelBtn         = document.getElementById('cancelBtn');

  if (uploadForm && projectIdEl && imageInput && cropButton && imagePreview) {
    let cropper;
    let isValidForUpdate = false;
    const dpr = window.devicePixelRatio || 1;

    // Initial display: hide uploadForm if an existing cover image is valid
    const existingSrc = document.getElementById('cover_img')?.src || '';
    if (existingSrc) {
      checkImage(existingSrc,
        // onSuccess : l’image existe bien, on reste en update
        () => {
          isValidForUpdate = true;
          uploadForm.style.display = 'none';
          cancelBtn.style.display = 'none';
          console.log('Image is valid for update');
        },
        // onError : l’image ne charge pas, on repasse en create
        () => {
          console.log('Image is not valid for update');
          isValidForUpdate = false;  
          if (coverImgContainer) coverImgContainer.style.display = 'none';
          uploadForm.style.display = 'initial';
          cancelBtn.style.display = 'initial';
        }
      );
    }

    // Allow clicking cover image to re-open form
    if (coverImgContainer) {
      coverImgContainer.addEventListener('click', () => {
        uploadForm.style.display = 'block';
        cancelBtn.style.display = 'block';
        coverImgContainer.style.display = 'none';
        isValidForUpdate = true;
      });
    }

    if (cancelBtn) {
      cancelBtn.addEventListener('click', () => {
        uploadForm.style.display = 'none';
        cancelBtn.style.display = 'none';
        if (coverImgContainer) coverImgContainer.style.display = 'block';
        isValidForUpdate = true;
      }); 
      
    }

    // On file select: initialize Cropper
    imageInput.addEventListener('change', (event) => {
      const file = event.target.files[0];
      if (!file) return;

      if (flowbiteArea) flowbiteArea.style.display = 'none';
      if (label) {
        label.removeAttribute('for');
        label.style.display = 'none';
      }
      cropButton.hidden = false;

      const reader = new FileReader();
      reader.onload = (e) => {
        imagePreview.src = e.target.result;
        imagePreview.style.display = 'block';
        if (coverImgContainer) coverImgContainer.style.display = 'none';

        if (cropper) cropper.destroy();
        cropper = new Cropper(imagePreview, {
          viewMode: 2,
          aspectRatio: NaN,
          responsive: true,
          minContainerWidth: 200,
          minContainerHeight: 200,
          minCanvasWidth: 200,
          minCanvasHeight: 200,
          minCropBoxWidth: 100,
          minCropBoxHeight: 100,
          autoCropArea: 1,
          background: false,
          modal: true,
          guides: true,
          center: true,
          highlight: true,
          cropBoxMovable: true,
          cropBoxResizable: true,
          ready() {
            const containerData = cropper.getContainerData();
            const imageData     = cropper.getImageData();
            const zoomRatio     = Math.min(
              containerData.width  / imageData.naturalWidth,
              containerData.height / imageData.naturalHeight
            );
            cropper.zoomTo(zoomRatio);
          }
        });
      };
      reader.readAsDataURL(file);
    });

    // On crop click: upload blob
    cropButton.addEventListener('click', () => {
      console.log('Crop button clicked');
      if (!cropper) return;

      const data          = cropper.getData(true);
      const targetWidth   = Math.min(Math.round(460 * dpr), Math.round(data.width * dpr));
      const targetHeight  = Math.round(targetWidth * (data.height / data.width));
      const croppedCanvas = cropper.getCroppedCanvas({
        width:  targetWidth,
        height: targetHeight,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
      });
      console.log(`Canvas size: ${croppedCanvas.width}×${croppedCanvas.height}`);

      croppedCanvas.toBlob((blob) => {
        console.log('Blob size:', blob.size);
        const projectId = projectIdEl.value;
        const formData  = new FormData(uploadForm);
        formData.set('projet_id', projectId);
        formData.set('croppedImage', blob, 'cover.png');

        const url = isValidForUpdate
          ? '/upload-cropped-image-update'
          : '/upload-cropped-image';

          console.log('isValidForUpdate:', isValidForUpdate);
          console.log('Uploading to:', url);
        fetch(url, { method: 'POST', body: formData })
          .then(res => res.json())
          .then(json => {
            console.log('Upload response:', json);
            if (json.url) {
              imagePreview.src = json.url;
              uploadForm.style.display = 'none';
              if (coverImgContainer) coverImgContainer.style.display = 'block';
            }
          })
          .catch(err => console.error('Upload error:', err));
      }, 'image/png');
    });
  }
});

window.onresize = () => {
  // Responsive JS if needed
};

console.log('app.js loaded');

// Utility to check image validity
function checkImage(url, onSuccess, onError) {
  const img = new Image();
  img.onload  = onSuccess;
  img.onerror = onError;
  img.src     = url;
}
