document.addEventListener('DOMContentLoaded', () => {
/* *Message Alert if not collaborator */
    const checkNoCollaborators = () => {
        const noCollaboratorElement = document.querySelector('.no-collaborators');
        // Display alert if no collaborator is present
        if (noCollaboratorElement) {
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
    };
    
/* *Message Alert confirm delete collab */
    const initCollaboratorDeletion = () => {
        document.querySelectorAll('.btnDeleteCollaborator').forEach(button => {
            button.addEventListener('click', () => {
                const url = button.getAttribute('data-url');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Voulez-vous vraiment supprimer ce collaborateur ? Cette action est irréversible.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    };

/* *Message Alert confirm delete Project */
    const initProjectDeletion = () => {
        document.querySelectorAll('.btnDeleteProject').forEach(button => {
            button.addEventListener('click', () => {
                const url = button.getAttribute('data-url');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Voulez-vous vraiment supprimer ce projet ? Cette action est irréversible.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    };


    // Initialisations
    initProjectDeletion();
    checkNoCollaborators();
    initCollaboratorDeletion();

});


