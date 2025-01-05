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
// Importation de Masonry
import Masonry from 'masonry-layout';

// Initialisation de Masonry sur un conteneur spécifique
document.addEventListener("DOMContentLoaded", function() {
  const grid = document.querySelector('.grid_masonry'); // Sélectionner l'élément contenant les éléments à agencer

  // Initialiser Masonry
  const masonry = new Masonry(grid, {
    itemSelector: '.grid-item', // Sélectionner chaque élément de la grille
    columnWidth: '.grid-sizer', // Définir la largeur de la colonne
    percentPosition: true
  });
});

window.onresize = () => {
    console.log('onresize =>', window.innerWidth);
}

// window.onload = () => {
//     const grid = document.querySelector('.grid_masonry'); 
//     const masonry = new Masonry(grid, {
//         itemSelector: '.grid-item',
//         // gutter: 10,
//         // use element for option
//         columnWidth: '.grid-sizer',
//         percentPosition: true
//     });
// }