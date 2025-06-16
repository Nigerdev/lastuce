document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Partenariats JavaScript initialisé');
    
    // Vérifier le CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('🔐 CSRF Token:', csrfToken ? 'Présent' : 'Manquant');
});

function showNotification(message, type = 'success') {
    // Créer une notification simple
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
        'bg-red-100 text-red-800 border border-red-200'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                ${type === 'success' ? 
                    '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>' :
                    '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>'
                }
            </div>
            <div class="text-sm">${message}</div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 100);
    
    // Supprimer après 5 secondes
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function selectAll() {
    console.log('📋 Sélection/désélection de tous les partenariats');
    const checkboxes = document.querySelectorAll('input[name="selected_partenariats[]"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    console.log(`✅ ${checkboxes.length} checkboxes ${allChecked ? 'désélectionnées' : 'sélectionnées'}`);
}

async function bulkAction(action) {
    console.log(`🔄 Action en lot: ${action}`);
    
    const selected = Array.from(document.querySelectorAll('input[name="selected_partenariats[]"]:checked')).map(cb => cb.value);
    
    if (selected.length === 0) {
        showNotification('Veuillez sélectionner au moins un partenariat.', 'error');
        return;
    }
    
    let actionText = '';
    switch(action) {
        case 'approve': actionText = 'accepter'; break;
        case 'reject': actionText = 'refuser'; break;
        case 'en_cours': actionText = 'marquer en cours'; break;
        default: actionText = action;
    }
    
    if (!confirm(`Êtes-vous sûr de vouloir ${actionText} ${selected.length} partenariat(s) ?`)) {
        return;
    }
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('Token CSRF manquant');
        }
        
        console.log('📡 Envoi de la requête...');
        
        const response = await fetch('/admin/partenariats/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                action: action,
                partenariats: selected
            })
        });
        
        console.log('📡 Réponse serveur:', response.status);
        
        if (!response.ok) {
            if (response.status === 401) {
                showNotification('Session expirée. Redirection vers la connexion...', 'error');
                setTimeout(() => window.location.href = '/login', 2000);
                return;
            }
            
            const errorText = await response.text();
            console.error('❌ Erreur serveur:', errorText);
            throw new Error(`Erreur serveur: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('✅ Données reçues:', data);
        
        if (data.success) {
            showNotification(data.message, 'success');
            // Recharger la page après 2 secondes
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showNotification(data.message || 'Une erreur est survenue', 'error');
        }
        
    } catch (error) {
        console.error('❌ Erreur lors de l\'action en lot:', error);
        showNotification('Erreur lors de l\'action: ' + error.message, 'error');
    }
}

async function approvePartenariat(id) {
    console.log(`✅ Approbation du partenariat ${id}`);
    
    if (!confirm('Êtes-vous sûr de vouloir accepter ce partenariat ?')) {
        return;
    }
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('Token CSRF manquant');
        }
        
        const response = await fetch(`/admin/partenariats/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                send_notification: false
            })
        });
        
        console.log('📡 Réponse serveur:', response.status);
        
        if (!response.ok) {
            if (response.status === 401) {
                showNotification('Session expirée. Redirection vers la connexion...', 'error');
                setTimeout(() => window.location.href = '/login', 2000);
                return;
            }
            
            const errorText = await response.text();
            console.error('❌ Erreur serveur:', errorText);
            throw new Error(`Erreur serveur: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('✅ Données reçues:', data);
        
        if (data.success) {
            showNotification(data.message, 'success');
            // Recharger la page après 2 secondes
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showNotification(data.message || 'Une erreur est survenue', 'error');
        }
        
    } catch (error) {
        console.error('❌ Erreur lors de l\'approbation:', error);
        showNotification('Erreur lors de l\'approbation: ' + error.message, 'error');
    }
}

async function rejectPartenariat(id) {
    console.log(`❌ Refus du partenariat ${id}`);
    
    const reason = prompt('Raison du refus (obligatoire):');
    if (!reason || reason.trim() === '') {
        showNotification('La raison du refus est obligatoire.', 'error');
        return;
    }
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('Token CSRF manquant');
        }
        
        const response = await fetch(`/admin/partenariats/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                notes_internes: reason,
                send_notification: false
            })
        });
        
        console.log('📡 Réponse serveur:', response.status);
        
        if (!response.ok) {
            if (response.status === 401) {
                showNotification('Session expirée. Redirection vers la connexion...', 'error');
                setTimeout(() => window.location.href = '/login', 2000);
                return;
            }
            
            const errorText = await response.text();
            console.error('❌ Erreur serveur:', errorText);
            throw new Error(`Erreur serveur: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('✅ Données reçues:', data);
        
        if (data.success) {
            showNotification(data.message, 'success');
            // Recharger la page après 2 secondes
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showNotification(data.message || 'Une erreur est survenue', 'error');
        }
        
    } catch (error) {
        console.error('❌ Erreur lors du refus:', error);
        showNotification('Erreur lors du refus: ' + error.message, 'error');
    }
}