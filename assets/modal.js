export default class Modal {
  // ...existing code...
}

// document.addEventListener("DOMContentLoaded", function() {
//   try {
//     const modal = new Modal(document.getElementById('control-modal'), {
//       placement: 'bottom-right',
//       backdrop: 'dynamic',
//       backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
//       closable: true,
//       onHide: () => {
//         console.log('modal is hidden');
//         window.location.reload();
//       },
//       onShow: () => {
//         console.log('modal is shown');
//       },
//       onToggle: () => {
//         console.log('modal has been toggled');
//       }
//     });

//     const loadModalContent = (action) => {
//       const modalContent = document.querySelector('#control-modal .modal-content');
//       if (typeof Routing !== 'undefined') {
//         if (action === 'reorganize') {
//           fetch(Routing.generate('admin_sections_list')) // Utilisez le générateur de routes de Symfony
//             .then(response => response.text())
//             .then(html => {
//               modalContent.innerHTML = html;
//               modal.show();
//             })
//             .catch(error => console.error('Error loading modal content:', error)); // Ajoutez un gestionnaire d'erreurs
//         } else if (action === 'add-section') {
//           fetch(Routing.generate('admin_section_form')) // Utilisez le générateur de routes de Symfony
//             .then(response => response.text())
//             .then(html => {
//               modalContent.innerHTML = html;
//               modal.show();
//             })
//             .catch(error => console.error('Error loading modal content:', error)); // Ajoutez un gestionnaire d'erreurs
//         }
//       } else {
//         console.error('Routing is not defined');
//       }
//     };

//     document.querySelectorAll('#dropdownMenuEdit a').forEach(function (element) {
//       element.addEventListener('click', function (event) {
//         event.preventDefault();
//         const action = this.dataset.action;
//         loadModalContent(action);
//       });
//     });
//   } catch (error) {
//     console.error('Error initializing modal:', error);
//   }
// });
