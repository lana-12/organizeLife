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
                console.log('events chargés :', events);

                this.myCalendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'fr',
                    timeZone: 'Europe/Paris',
                    headerToolbar: {
                        start: 'prev,next today',
                        center: 'title',
                        end: 'dayGridMonth timeGridWeek'
                    },
                    height: 700,
                    width: 800,
                    events: events.map(event => {
                        const collaborator = event.collaborator;
                        const color = this.colorMap[collaborator]?.background || 'gray';
                        return {
                            type: event.type,
                            title: event.title,
                            start: `${event.date_event_start}T${event.hour_event_start}`,
                            end: `${event.date_event_end}T${event.hour_event_end}`,
                            description: event.description,
                            color: color,
                            display: 'block',
                        };
                    }),
                    selectable: true,
                    editable: true,
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
