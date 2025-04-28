window.onload= () => {

    function Supp(link) {
        console.log('j ai cliquer');
        if (confirm('Confirmez la suppression du projet ?')) {
            document.location.href = link;
        }
    }
}


/* *Message Alert if not collaborator */
document.addEventListener('DOMContentLoaded', function () {
    var noCollaboratorElement = document.querySelector('.no-collaborators');
    if (noCollaboratorElement) {
        // Display alert if no collaborator is present
        Swal.fire({
            title: 'Aucun collaborateur pour ce projet',
            text: 'Souhaitez-vous en ajouter un ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui',
            cancelButtonText: 'Non'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the collaborator creation page
                window.location.href = noCollaboratorElement.dataset.url;
            }
        });
    }
});