class CalendarManager {

    constructor(eventService, projectId, colorMap, collaborators) {
        this.eventService = eventService;
        this.myCalendar = null; 
        this.projectId = projectId;
        this.colorMap = colorMap;
        this.collaborators = collaborators;
    }
    
    async initialize() {
        document.addEventListener('DOMContentLoaded', async () => {
            const calendarEl = document.getElementById('calendar-holder');

            try {
                const events = await this.eventService.getEvents(this.projectId);
                // console.log('events chargés :', events);

                this.myCalendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'fr',
                    timeZone: 'Europe/Paris',
                    headerToolbar: {
                        start: 'prev,next today',
                        center: 'title',
                        end: 'dayGridMonth timeGridWeek'
                    },
                    height: "auto",
                    events: events.map(event => {
                        // console.log('event debut ', event)
                        const collaborator = event.collaborateur_id;
                        const color = this.colorMap[collaborator]?.background || 'gray';
                        const start = `${event.date_event_start}T${event.hour_event_start}`;
                        const end = `${event.date_event_end}T${event.hour_event_end}`;

                        return {
                            title: event.title,
                            start: start,
                            end: end,
                            description: event.description,
                            color: color,
                            display: 'block',
                            extendedProps: {
                                description: event.description,
                                name_colaborator: event.collaboratorName,
                                type: event.type,
                                id_event: event.id_event,
                                collaborateur_id: event.collaborateur_id,
                                date_start: event.date_event_start,
                                hour_start: event.hour_event_start,
                                date_end: event.date_event_end,
                                hour_end: event.hour_event_end
                            }
                            
                        };
                    }),
                    selectable: true,
                    editable: true,

                    eventClick: function (info) {
                        const event = info.event;
                        Swal.fire({
                            title: `<strong>${event.title}</strong>`,
                            icon: "info",
                            html: `
                                <p><b>Collaborateur :</b> ${event.extendedProps.name_colaborator}</p>
                                <p><b>Début :</b> ${event.extendedProps.date_start} - ${event.extendedProps.hour_start}</p>
                                <p><b>Fin :</b> ${event.extendedProps.date_end} - ${event.extendedProps.hour_end} </p>
                                <p><b>Description :</b> ${event.extendedProps.description || 'Aucune Description'}</p>
                                <p><b>Type :</b> ${event.extendedProps.type || 'N/A'}</p>
                                <div class='d-flex justify-content-center gap-2 mt-3 flex-wrap'>
                                    <a href='/event/nouveau/${projectId}' class='btn btn-primary'>
                                        <i class='fa fa-plus'></i> Créer
                                    </a>
                                    <a href='/event/edit/${event.extendedProps.id_event}' class='btn btn-warning'>
                                        <i class='fa fa-edit'></i> Modifier
                                    </a>
                                    <a href='/event/delete/${event.extendedProps.id_event}' class='btn btn-danger'onsubmit="return confirm('Supprimer cet évènement ?');">
                                        <i class='fa fa-edit'></i> Supprimer
                                    </a>
                                    
                                </div>
                            `,
                            showCloseButton: true,
                            showCancelButton: false,
                            showConfirmButton: false,

                        });
                    },
                    select: (arg) => {
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        const selectedDate = new Date(arg.startStr);
                        if (today <= selectedDate) {
                            Swal.fire({
                                title: 'Voulez-vous créer un événement ?',
                                showCancelButton: true,
                                confirmButtonText: 'Oui',
                                cancelButtonText: 'Non'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const startDate = arg.startStr;
                                    const endDate = arg.endStr;
                                    const url = `/event/nouveau/${this.projectId}?start=${startDate}&end=${endDate}`
                                    window.location.href = url;
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Cette date est déjà passée !!",
                                text: "Merci de choisir une autre date",
                            });
                        }
                    },

                    eventDrop: async (info) => {
                        const event = info.event;
                        const newStart = event.start;
                        const newEnd = event.end;
                        const formattedStartDate = newStart.toISOString().split('T')[0];
                        const formattedEndDate = newEnd.toISOString().split('T')[0];
                        const result = await Swal.fire({
                            title: "Confirmer la modification",
                            html: `
                                <p><strong>${event.title}</strong></p>
                                <p>Nouvelle date de début : <code>${formattedStartDate}</code></p>
                                <input type="time" id="start-time" class="swal2-input" placeholder="Heure de début" value="${newStart.toTimeString().slice(0, 5)}">
                                <p>Nouvelle date de fin : <code>${formattedEndDate}</code></p>
                                <input type="time" id="end-time" class="swal2-input" placeholder="Heure de fin" value="${newEnd.toTimeString().slice(0, 5)}">
                            `,
                            focusConfirm: false,
                            showCancelButton: true,
                            confirmButtonText: "Oui, enregistrer",
                            cancelButtonText: "Annuler",
                            preConfirm: () => {
                                const startTime = document.getElementById('start-time').value;
                                const endTime = document.getElementById('end-time').value;

                                if (!startTime || !endTime) {
                                    Swal.showValidationMessage("Merci de renseigner les deux heures");
                                    return false;
                                }

                                return {
                                    hour_event_start: startTime,
                                    hour_event_end: endTime
                                };
                            }
                        });

                        if (result.isConfirmed && result.value) {
                            const { hour_event_start, hour_event_end } = result.value;

                            const response = await fetch(`/event/update-time/${event.extendedProps.id_event}`, {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify({
                                    date_event_start: formattedStartDate,
                                    hour_event_start: hour_event_start,
                                    date_event_end: formattedEndDate,
                                    hour_event_end: hour_event_end
                                })
                            });

                            if (!response.ok) {
                                Swal.fire("Erreur", "La mise à jour a échoué. Merci de réessayer.", "error");
                                info.revert();
                            } else {
                                Swal.fire("Mis à jour", "L’événement a été mis à jour avec succès.", "success");
                            }
                        } else {
                            info.revert();
                        }
                    }


                });

                this.myCalendar.render();

            } catch (error) {
                console.error("Erreur lors du chargement des événements :", error);
            }
        });
    }
}