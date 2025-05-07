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


/* *Message Modal showEvent  */
    const showEventModal = () => {
        document.querySelectorAll('.btnShowEventModal').forEach(button => {
            button.addEventListener('click', () => {
                const title = button.dataset.title;
                const description = button.dataset.description;
                const dateStart = button.dataset.dateStart;
                const dateEnd = button.dataset.dateEnd;
                const user = button.dataset.user;
                const typeEvent = button.dataset.typeevent;
                const project = button.dataset.project;
                const eventId = button.dataset.eventid;
                Swal.fire({
                    title: title,
                    html: `
                    <p><strong>Description :</strong> ${description}</p>
                    <p><strong>Début :</strong> ${dateStart}</p>
                    <p><strong>Fin :</strong> ${dateEnd}</p>
                    <p><strong>Créé par :</strong> ${typeEvent}</p>
                    <p><strong>Créé par :</strong> ${user}</p>
                    <div class='d-flex justify-content-center gap-2 mt-3 flex-wrap'>
                        <a href='/event/nouveau/${project}' class='btn btn-primary'>
                            <i class='fa fa-plus'></i> Créer
                        </a>
                        <a href='/event/edit/${eventId}' class='btn btn-warning'>
                            <i class='fa fa-edit'></i> Modifier
                        </a>
                        <a href='/event/delete/${eventId}' class='btn btn-danger'>
                            <i class='fa fa-edit'></i> Supprimer
                        </a>
                    </div>
                `,
                    icon: 'info',
                    showCloseButton: true,
                    showCancelButton: false,
                    showConfirmButton: false,
                })
            });
        });
    };

    // Initialisations
    showEventModal();
    initProjectDeletion();
    checkNoCollaborators();
    initCollaboratorDeletion();
});