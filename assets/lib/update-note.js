export default function updateNote(id, note) {
    const route = `/api/admin/note/${id}/processed`;
    fetch(route, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=utf-8'
        },
        body: JSON.stringify({ id: id })
    })
        .then(response => {
            if (!response.ok) {
                alert("L'opération a échoué");
            } else {
                if (note.classList.contains("bg-success")) {
                    note.classList.remove('bg-success');
                    note.classList.add('bg-danger');
                    note.innerHTML = "A Traiter";
                } else {
                    note.classList.remove('bg-danger');
                    note.classList.add('bg-success');
                    note.innerHTML = "Traité";
                }
            }
        })
        .catch(error => alert("L'opération a échoué.") );
}
