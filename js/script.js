document.getElementById('add-task-form').addEventListener('submit', function(e) {
    const title = document.querySelector('input[name="title"]').value.trim();
    if (title.length < 3) {
        e.preventDefault();
        alert('Le titre doit contenir au moins 3 caractères.');
    }
});

document.querySelectorAll('.delete-task').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        if (!confirm('Supprimer cette tâche ?')) return;

        const id = this.dataset.id;
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;
        fetch('delete-ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&csrf_token=${csrfToken}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`li[data-id="${id}"]`).remove();
            } else {
                alert(data.error || 'Erreur lors de la suppression');
            }
        })
        .catch(() => alert('Erreur réseau'));
    });
});