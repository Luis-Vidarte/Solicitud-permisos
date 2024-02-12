

function actualizarEstado(id_solicitud, accion) {
    // Make an AJAX request to your server-side script
    console.log('ID:', id_solicitud, 'Acción:', accion);

    fetch(`aprobar.php?id=${id_solicitud}&accion=${accion}`, {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Display a message to the user (you can replace this with a more sophisticated UI update)
        // You might want to reload the table or update the UI based on the result
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

