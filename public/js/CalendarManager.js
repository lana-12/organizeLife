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

                // customButtons: {
                //     multiMonth: {
                //         text: 'Multi Mois',
                //         views: {
                //             multiMonthYear: {
                //                 type: 'dayGrid',
                //                 duration: { months: 4 },
                //                 buttonText: 'Multi Mois'
                //             }
                //         },
                //         click: function() {
                //             this.myCalendar.changeView('multiMonthYear');
                //         }
                //     }
                // },

                buttonText: {
                    prev: 'Précédent',
                    next: 'Suivant',
                    today: 'Aujourd\'hui',
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste',
                },
                
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    start: 'prev,next today',
                    center: 'title',
                    end: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                
                events: [],
                
                selectable: true,
                editable: true,
                select: (arg) => {
                    let title = prompt('Nom de l\'événement :');
                    if (title) {
                        this.myCalendar.addEvent({
                            title: title,
                            start: arg.startStr,
                            end: arg.endStr,
                            description: ''
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
