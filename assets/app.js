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
  

});

window.onresize = () => {
    // console.log('onresize =>', window.innerWidth);
}

console.log('app.js loaded');


