class CalendarManager {

    constructor(eventService, projectId) {
        this.eventService = eventService;
        this.myCalendar = null; 
        this.projectId = projectId;
    }



    async initialize() {
        document.addEventListener('DOMContentLoaded', () => {
            let calendarEl = document.getElementById('calendar-holder');
            this.myCalendar = new FullCalendar.Calendar(calendarEl, {

                // Display btn left
                // buttonText: {
                //     prev: 'Précédent',
                //     next: 'Suivant',
                //     today: 'Aujourd\'hui',
                //     month: 'Mois',
                //     // week: 'Semaine',
                //     // day: 'Jour',
                //     // list: 'Liste',
                // },
                
                initialView: 'dayGridMonth',
                locale: 'fr',
                timeZone: 'Europe/Paris',
                headerToolbar: {
                    start: 'prev,next today',
                    center: 'title',
                    // end: 'dayGridMonth timeGridWeek timeGridDay listWeek'
                    end: 'dayGridMonth timeGridWeek'
                },
                height: 700,
                width: 800,
                
                events: [],
                
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
                                console.log(url)
                                
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
            
            this.loadEventsFromServer();
            this.myCalendar.render();


        });
    
    }


    async loadEventsFromServer() {

        try {
            const events = await this.eventService.getEvents(this.projectId);
            console.log('events ',events)
            events.forEach((event) => {
                this.myCalendar.addEvent({
                    title: event.title,
                    start: event.date_event + 'T' + event.hour_event,
                    description: event.description,
                });
            });
        } catch (error) {
            console.error("Erreur lors du chargement des événements:", error);
        }
    }
}

// const eventService = new EventService();
// const calendarManagerInstance = new CalendarManager(eventService);
// calendarManagerInstance.initialize();
