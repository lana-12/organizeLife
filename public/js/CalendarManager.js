// class CalendarManager {

//     constructor() {}

//     initialize() {
//         document.addEventListener('DOMContentLoaded', () => {
//             let calendarEl = document.getElementById('calendar-holder');
//             let myCalendar = new FullCalendar.Calendar(calendarEl, {

//                 customButtons: {
//                     multiMonth: {
//                         text: 'Multi Mois',
//                         views: {
//                             multiMonthYear: {
//                                 type: 'dayGrid',
//                                 duration: { months: 4 }, // Affiche 4 mois
//                                 buttonText: 'Multi Mois'
//                             }
//                         },
//                         click: function() {
//                             // Changer la vue du calendrier pour afficher multiMonthYear
//                             myCalendar.changeView('multiMonthYear');
                           
//                         }
//                     }
//                 },

//                 buttonText: {
//                     prev: 'Précédent',
//                     next: 'Suivant',
//                     today: 'Aujourd\'hui',
//                     month: 'Mois',
//                     week: 'Semaine',
//                     day: 'Jour',
//                     list: 'Liste',
//                 },
                
//                 initialView: 'dayGridMonth',
//                 // initialView: 'dayGridFourWeek',
//                 // initialView: 'listWeek',
//                 // initialView: 'multiMonthYear',
//                 // initialView: 'month',
//                 locale: 'fr',
//                 headerToolbar: {
//                     start: 'prev,next today',
//                     center: 'title',
//                     end: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek, multiMonthFourMonth, multiMonth'
//                 },
//                 // events: [
//                 //     {
//                 //         title: 'Event 1',
//                 //         start: '2024-03-01T08:00:00',
//                 //         end: '2024-03-01T10:00:00',
//                 //         description: 'Description Event 1'
//                 //     }, 
//                 //     {
//                 //         title: 'Event 2',
//                 //         start: '2024-03-05T10:50:00',
//                 //         end: '2024-03-05T12:30:00',
//                 //         description: 'Description Event 2'
//                 //     }
//                 // ],
                
//                 events: [],
                
                
//                 selectable: true,
//                 editable: true,
//                 select: (arg) => {
//                     let title = prompt('Nom de l\'événement :');
//                     if (title) {
//                         myCalendar.addEvent({
//                             title: title,
//                             start: arg.startStr,
//                             end: arg.endStr,
//                             description: ''
//                         });
//                     }
//                 },
//             });
//             // let existingEvents = myCalendar.getEvents().map(event => ({
//             //     title: event.title,
//             //     start: event.start,
//             //     end: event.end,
//             //     description: event.extendedProps.description || ''
//             // }));

//             // console.log('Evénements existants :', existingEvents);
// // Écouter l'événement personnalisé pour charger les événements du serveur
// document.addEventListener('loadCalendarEvents', () => {
//     this.loadEventsFromServer();
// })
//             myCalendar.render();
//         });
//     }


//     loadEventsFromServer() {
//         // Déclencher l'événement pour charger les données du serveur
//         let event = new CustomEvent('loadCalendarEvents');
//         document.dispatchEvent(event);
//     }
// }

// // Utilisation de la classe CalendarManager
// // const calendarManagerInstance = new CalendarManager();
// // calendarManagerInstance.initialize();



class CalendarManager {

    constructor() {
        this.myCalendar = null; 
    }

    initialize() {
        document.addEventListener('DOMContentLoaded', () => {
            let calendarEl = document.getElementById('calendar-holder');
            this.myCalendar = new FullCalendar.Calendar(calendarEl, {

                customButtons: {
                    multiMonth: {
                        text: 'Multi Mois',
                        views: {
                            multiMonthYear: {
                                type: 'dayGrid',
                                duration: { months: 4 },
                                buttonText: 'Multi Mois'
                            }
                        },
                        click: function() {
                            this.myCalendar.changeView('multiMonthYear');
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
                locale: 'fr',
                headerToolbar: {
                    start: 'prev,next today',
                    center: 'title',
                    end: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek, multiMonthFourMonth, multiMonth'
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

            // Écouter l'événement personnalisé pour charger les événements du serveur
           
                this.loadEventsFromServer();
          

            this.myCalendar.render();
        });
    }

    loadEventsFromServer() {
        fetch("/load-events", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                filters: {},
            }),
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            console.log('data ', data)
            // Assurez-vous que data.formatEvent est un tableau
            if (Array.isArray(data.formatEvent)) {
                data.formatEvent.forEach((event) => {
                    this.myCalendar.addEvent({
                        title: event.title,
                        start: event.date_event + 'T' + event.hour_event,
                        description: event.description,
                    });
                });
            } else {
                console.error("Les données reçues ne sont pas au format attendu.");
            }
        })
        .catch((error) => {
            console.error("Erreur lors du chargement des événements:", error);
        });
    }
}

// Utilisation de la classe CalendarManager
// const calendarManagerInstance = new CalendarManager();
// calendarManagerInstance.initialize();
