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
import { Dropdown } from 'flowbite'
// import { initFlowbite } from 'flowbite';

// Importation de Masonry
import Masonry from 'masonry-layout';

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

    // set the modal menu element
    const $targetEl = document.getElementById('control-modal');

    // // options with default values
    const options = {
      placement: 'bottom-right',
      backdrop: 'dynamic',
      backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
      closable: true,
      onHide: () => {
          console.log('modal is hidden');
          //Mise à jour page reload
          window.location.reload();
      },
      onShow: () => {
          console.log('modal is shown');
      },
      onToggle: () => {
          console.log('modal has been toggled');
      }
    };

  /*
  * $targetEl: required
  * options: optional
  */
  const modal = new Modal($targetEl, options);
  console.log(modal);
  

  document.querySelectorAll('#dropdownMenuEdit a').forEach(function (element) {
    element.addEventListener('click', function (event) {
      event.preventDefault();
      const action = this.dataset.action;
      if (action === 'reorganize') {
        modal.show();
      } else if (action === 'add-section') {
        // Add your logic for adding a section here
        alert('Ajouter une section clicked');
      }

    });

  });
  

});

window.onresize = () => {
    // console.log('onresize =>', window.innerWidth);
}

console.log('app.js loaded');

