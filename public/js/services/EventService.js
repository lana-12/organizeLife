class EventService {

    async getEvents(projectId) {
        const response = await fetch(`/load-events/${projectId}`, {  
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                filters: {},
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data.formatEvent;
    }

    async updateEventTime(eventId, startDate, startTime, endDate, endTime) {
        const response = await fetch(`/event/update-time/${eventId}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                date_event_start: startDate,
                hour_event_start: startTime,
                date_event_end: endDate,
                hour_event_end: endTime
            }),
        });

        if (!response.ok) {
            console.log('la', response )
            throw new Error("La mise à jour a échoué");
        }

        return await response.json(); 
    }

    
}
