
// Default SortableJS
import Sortable from 'sortablejs';

export default class SortableUtil {
    // ...existing code...
    initSortable() {
        const list = document.getElementById('section-list');
        if (list) {
            console.log('list => ', list);
            new Sortable(list, {
                animation: 150,
                dragClass: '!rounded-none',
                onEnd: function (evt) {
                    const items = Array.from(list.children).map(item => item.dataset.id);
                    console.log('items => ', items);
                    // Envoyer les nouvelles positions au backend
                    fetch('/update-section-order', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order: items })
                    }).then(response => {
                        if (!response.ok) {
                            alert('Erreur lors de la mise à jour de l\'ordre.');
                        } else {
                            console.log('Order updated successfully');
                        }
                    });
                }
            });
        }
    
    }
}
