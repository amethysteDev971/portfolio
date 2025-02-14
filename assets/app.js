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
import { Modal } from 'flowbite';
import { Dropdown } from 'flowbite';
// import { initFlowbite } from 'flowbite';

// Importation de Masonry
import Masonry from 'masonry-layout';
import './modal.js'; // Import the updated modal handling script

import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';




import SortableUtil from './sortable_util.js';
 // Create an instance of SortableUtil and call initSortable
 const sortableUtil = new SortableUtil();

// Initialisation de Masonry sur un conteneur spécifique
document.addEventListener("DOMContentLoaded", function() {
  const grid = document.querySelector('.grid_masonry'); // Sélectionner l'élément contenant la grille

  if (grid) {
    const masonryTarget = document.querySelector('.grid-item'); // Vérifier s'il y a des éléments dans la grille
    if (masonryTarget) {
      // Initialiser Masonry
      const masonry = new Masonry(grid, {
        itemSelector: '.grid-item', // Sélectionner chaque élément de la grille
        columnWidth: '.grid-sizer', // Définir la largeur de la colonne
        percentPosition: true
      });
    }
  }

  

  //** */ Flowbite elements init functions
  // initFlowbite();
  initDropdowns();

  const $targetEl = document.getElementById('control-modal');
  
  const options = {
    placement: 'bottom-right',
    backdrop: 'dynamic',
    backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
    closable: true,
    onHide: () => {
      console.log('modal is hidden');
      // Mise à jour page reload
      window.location.reload();
    },
    onShow: () => {
      console.log('modal is shown');
    },
    onToggle: () => {
      console.log('modal has been toggled');
    }
  };
  const modal = new Modal($targetEl, options);
  console.log('Modal instance:', modal);

  const loadModalContent = (projectId, action) => {
    const modalContent = document.querySelector('#control-modal .modal-content');
    if (!modalContent) {
      console.error('Modal content element not found');
      return;
    }
    const url = `/admin/projet/modal/${projectId}/${action}`;

    fetch(url)
        .then(response => response.text())
        .then(html => {
            console.log('Loaded HTML:', html); // Log the loaded HTML
            modalContent.innerHTML = html;
            console.log('Modal content updated');
            modal.show();
            console.log('Modal should be shown now');
            hundleForceCloseModal();
            // Vérifier les classes CSS
            console.log('Modal classes:', $targetEl.classList);
            sortableUtil.initSortable();
        })
        .catch(error => console.error('Error loading modal content:', error));
  };

  document.querySelectorAll('#dropdownMenuEdit a').forEach(function (element) {
    element.addEventListener('click', function (event) {
      event.preventDefault();
      const action = this.getAttribute('data-action');
      const projectId = document.querySelector('#project-id').value; // Assurez-vous que l'ID du projet est disponible
      loadModalContent(projectId, action);
    });
  });

  function hundleForceCloseModal() {
    // const buttonToHide = document.querySelector('button[data-modal-hide = control-modal]');
    // console.log('buttonToHide =>', buttonToHide);
    document.querySelectorAll('button[data-modal-hide = control-modal]').forEach(function (element) {
      element.addEventListener('click', function (event) {
        event.preventDefault();

        modal.hide();
      });
    });
    
    
  }

 
  // Sortable.js
  if (typeof sortableUtil.initSortable === 'function') {
    sortableUtil.initSortable();
  } else {
    console.error('initSortable is not a function in sortableUtil');
  }
  
  let isValidForUpdate = false;
  // Upload cover image
  let cropper;
  const imageInput = document.getElementById("imageInput");
  const imagePreview = document.getElementById("imagePreview");
  const cropButton = document.getElementById("cropButton");
  const flowbiteArea = document.getElementById("flowbite_area");
  const label = document.querySelector("label[for='imageInput']");

  imageInput.addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (file) {
        // Masquer l'élément Flowbite
        flowbiteArea.style.display = "none"; 
        label.removeAttribute("for");
        label.style.display = "none"; // Cache complètement le label
        cropButton.hidden = false; // Affiche le bouton de recadrage

        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = "block";

            // Détruit l'ancien cropper s'il existe
            if (cropper) {
                cropper.destroy();
            }

            // Initialise Cropper avec la taille max 416x416
            cropper = new Cropper(imagePreview, {
                // aspectRatio: NaN, // Désactive le ratio fixe
                // viewMode: 2, // Permet à l'image de remplir le cadre
                // autoCropArea: 1, // Utilise toute la zone dispo
                // minCropBoxWidth: 100, // Largeur mini
                // minCropBoxHeight: 100, // Hauteur mini
                // cropBoxResizable: true, // Permet à l'utilisateur de redimensionner
                viewMode: 2, // Restreint le canvas à l'intérieur du conteneur
                aspectRatio: NaN, // Permet un ratio libre si souhaité
                responsive: true, // Rend le cropper réactif aux changements de taille du conteneur
                minContainerWidth: 200, // Largeur minimale du conteneur
                minContainerHeight: 200, // Hauteur minimale du conteneur
                minCanvasWidth: 200, // Largeur minimale du canvas
                minCanvasHeight: 200, // Hauteur minimale du canvas
                minCropBoxWidth: 100, // Largeur minimale de la zone de recadrage
                minCropBoxHeight: 100, // Hauteur minimale de la zone de recadrage
                autoCropArea: 1, // Utilise toute la zone disponible pour le recadrage
                background: false, // Désactive l'arrière-plan du conteneur
                modal: true, // Affiche le modal noir au-dessus de l'image et sous la zone de recadrage
                guides: true, // Affiche les lignes pointillées au-dessus de la zone de recadrage
                center: true, // Affiche l'indicateur central au-dessus de la zone de recadrage
                highlight: true, // Affiche le modal blanc au-dessus de la zone de recadrage
                cropBoxMovable: true, // Permet de déplacer la zone de recadrage
                cropBoxResizable: true, // Permet de redimensionner la zone de recadrage
                ready() {
                    // Force la taille de la zone de crop à max 416px
                    // const cropBoxData = cropper.getCropBoxData();
                    // const newWidth = Math.min(cropBoxData.width, 416);
                    // const newHeight = Math.min(cropBoxData.height, 416);
                    
                    // cropper.setCropBoxData({
                    //     left: cropBoxData.left,
                    //     top: cropBoxData.top,
                    //     width: newWidth,
                    //     height: newHeight
                    // });
                    // Ajuste le zoom pour que l'image s'adapte au conteneur
                    const containerData = cropper.getContainerData();
                    const imageData = cropper.getImageData();
                    const zoomRatio = Math.min(
                        containerData.width / imageData.naturalWidth,
                        containerData.height / imageData.naturalHeight
                    );
                    cropper.zoomTo(zoomRatio);
                }
                
            });
            
        };
        reader.readAsDataURL(file);
        
    }
  });

  cropButton.addEventListener("click", function () {
    if (cropper) {
        const croppedCanvas = cropper.getCroppedCanvas({
            width: 416,
            height: Math.min(416, cropper.getCropBoxData().height)
        });

        croppedCanvas.toBlob((blob) => {
            const formData = new FormData(document.getElementById("uploadForm")); // Récupère tous les inputs du form
            formData.set("croppedImage", blob, "cropped.jpg"); // Remplace le fichier image
            
            const url = isValidForUpdate ? "/upload-cropped-image-update" : "/upload-cropped-image";
            console.log('isValidForUpdate =>', isValidForUpdate);
            console.log('url =>', url);
            
            fetch(url, {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                console.log("Réponse:", data);
                // alert("Image recadrée enregistrée !");
                document.getElementById("imagePreview").src = data.url;
                window.location.reload();
                // uploadForm.style.display = "none";
                // coverImg.style.display = "inherit";
            })
            .catch(error => {
                console.error("Erreur:", error);
            });
        }, "image/jpeg");
    }
  });

  let srcImg = '';
  
  if (document.getElementById("cover_img")) {
    srcImg = document.getElementById("cover_img").src;
  }
  
  const uploadForm = document.getElementById("uploadForm");
  const coverImg = document.getElementById("cover_img_container");
  checkImage(srcImg, () => {
    console.log('Image is valid');
    if (uploadForm) {
      isValidForUpdate = true;
      uploadForm.style.display = "none";
    }
    console.log('Image is valid');
    
  }, () => {
    if (coverImg) {
      coverImg.style.display = "none";
    }
    
    console.log('Image is invalid');
  });

  if (coverImg) {
    coverImg.addEventListener("click", function () {
      coverImg.style.display = "none";
      uploadForm.style.display = "initial";
    });
  }
  



});
// End document.addEventListener("DOMContentLoaded", function() 

window.onresize = () => {
    // console.log('onresize =>', window.innerWidth);
}

console.log('app.js loaded');

// check valid img
function checkImage(url, onSuccess, onError) {
  const img = new Image();
  img.onload = function() {
      // L'image est valide et a été chargée avec succès
      onSuccess();
  };
  img.onerror = function() {
      // L'image est invalide ou n'a pas pu être chargée
      onError();
  };
  img.src = url;
}


