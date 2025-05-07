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
                    contentHeight: "auto",
                    events: events.map(event => {
                        const collaborator = event.collaborateur_id;
                        const color = this.colorMap[collaborator]?.background || 'gray';
                        const start = new Date(`${event.date_event_start}T${event.hour_event_start}`);
                        const end = new Date(`${event.date_event_end}T${event.hour_event_end}`);
                        return {
                            title: event.title,
                            start: start.toISOString(),
                            end: end.toISOString(),
                            description: event.description,
                            color: color,
                            display: 'block',
                            extendedProps: {
                                description: event.description,
                                name_colaborator: event.collaboratorName,
                                type: event.type,
                                id_event: event.id_event,
                                collaborateur_id: event.collaborateur_id
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
                                <p><b>Début :</b> ${event.start.toLocaleString()}</p>
                                <p><b>Fin :</b> ${event.end.toLocaleString()}</p>
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
                });

                this.myCalendar.render();

            } catch (error) {
                console.error("Erreur lors du chargement des événements :", error);
            }
        });
    }
}