
document.addEventListener('DOMContentLoaded', function () {
let btn = document.getElementById('clikbtn');

btn.addEventListener('click', ()=>{
    alert('hello')

} )


})


document.addEventListener('DOMContentLoaded', function () {
    let calendarEl = document.getElementById('calendar');
    let calendar = new FullCalendar.Calendar(calendarEl,
        {
            initialView: 'dayGridMonth',
            // initialView: 'timeGridWeek', 
            events: [
                {
                    title: 'Event 1',
                    start: '2024-03-01T08:00:00',
                    end: '2024-03-01T10:00:00',
                    description: 'Description Event 1',
                    // extendedProps: {
                    //     description: 'Description Event 2'
                    // }
                }, {
                    title: 'Event 2',
                    start: '2024-03-05T10:50:00',
                    end: '2024-03-05T12:30:00',
                    description: 'Description Event 2'
                }
            ],
            // Activer la selection des cellules
            selectable: true,
            // Fonction appelée lors de la sélection d'une cellule
            select: function (arg) {
                // Titre event en prompt mais peut-être Modal
                let title = prompt('Nom de l\'événement :');
                if (title) {
                    // Add new event avec le titre spécifié
                    // Faire les autres champs
                    calendar.addEvent({
                        title: title,
                        start: arg.startStr,
                        end: arg.endStr,
                        description: ''
                    });
                }
            }
        });
    let existingEvents = calendar.getEvents().map(event => ({
        title: event.title,
        start: event.start,
        end: event.end,
        description: event.extendedProps.description || ''
    }));
    console.log('Evénements existants :', existingEvents);

    calendar.render();
});
