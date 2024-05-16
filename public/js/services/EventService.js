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

    
}
