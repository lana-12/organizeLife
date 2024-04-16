class CalendarManager {

    constructor() {}

    initialize() {
        document.addEventListener('DOMContentLoaded', () => {
            let calendarEl = document.getElementById('calendar');
            let myCalendar = new FullCalendar.Calendar(calendarEl, {

                customButtons: {
                    multiMonth: {
                        text: 'Multi Mois',
                        views: {
                            multiMonthYear: {
                                type: 'dayGrid',
                                duration: { months: 4 }, // Affiche 4 mois
                                buttonText: 'Multi Mois'
                            }
                        },
                        click: function() {
                            // Changer la vue du calendrier pour afficher multiMonthYear
                            myCalendar.changeView('multiMonthYear');
                           
                        }
                    }
                },

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
                // initialView: 'dayGridFourWeek',
                // initialView: 'listWeek',
                // initialView: 'multiMonthYear',
                // initialView: 'month',
                locale: 'fr',
                headerToolbar: {
                    start: 'prev,next today',
                    center: 'title',
                    end: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek, multiMonthFourMonth, multiMonth'
                },
                events: [
                    {
                        title: 'Event 1',
                        start: '2024-03-01T08:00:00',
                        end: '2024-03-01T10:00:00',
                        description: 'Description Event 1'
                    }, 
                    {
                        title: 'Event 2',
                        start: '2024-03-05T10:50:00',
                        end: '2024-03-05T12:30:00',
                        description: 'Description Event 2'
                    }
                ],
                
                
                selectable: true,
                editable: true,
                select: (arg) => {
                    let title = prompt('Nom de l\'événement :');
                    if (title) {
                        myCalendar.addEvent({
                            title: title,
                            start: arg.startStr,
                            end: arg.endStr,
                            description: ''
                        });
                    }
                }
            });
console.log('calendar ' , myCalendar.event)
            let existingEvents = myCalendar.getEvents().map(event => ({
                title: event.title,
                start: event.start,
                end: event.end,
                description: event.extendedProps.description || ''
            }));

            console.log('Evénements existants :', existingEvents);

            myCalendar.render();
        });
    }
}

// Utilisation de la classe CalendarManager
// const calendarManagerInstance = new CalendarManager();
// calendarManagerInstance.initialize();
