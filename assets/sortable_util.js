
// Default SortableJS
import Sortable from 'sortablejs';

export default class SortableUtil {
    constructor() {
        this.orderChanged = false;
    }

    initSortable() {
        const list = document.getElementById('section-list');
        if (!list) return;

        // console.log('list => ', list);
        new Sortable(list, {
            animation: 150,
            dragClass: '!rounded-none',
            onEnd: (evt) => {
                this.orderChanged = true;
                const items = Array.from(list.children).map(li => li.dataset.id);
                // console.log('items => ', items);
                // Envoyer les nouvelles positions au backend
                fetch('/update-section-order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order: items })
                }).then(response => {
                    if (!response.ok) {
                        alert('Erreur lors de la mise à jour de l\'ordre.');
                    } else {
                        // console.log('Order updated successfully');
                    }
                });
            }
        });
    
    }

    hasChanged() {
        return this.orderChanged;
    }
}
