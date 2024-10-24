// script.js
document.getElementById('notifications-link').addEventListener('click', fetchNotifications);




var map = L.map('notifications-map').setView([0, 0], 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 18
}).addTo(map);

// Function to plot markers on the map
function plotEventsOnMap(events) {
    events.forEach(event => {
        let coordinates = event.geometries[0].coordinates; // Get coordinates for the event
        let title = event.title;
        let category = event.categories[0].title;

        // Check if the event is a wildfire and set marker color/icon
        
            L.marker([coordinates[1], coordinates[0]]).addTo(map)
                .bindPopup(`<b>${title}</b><br>Category: ${category}`);
    });
}






async function fetchNotifications() {
    const apiUrl = 'https://eonet.gsfc.nasa.gov/api/v2.1/events'; // EONET API endpoint
    const notificationsContainer = document.getElementById('notifications');
    notificationsContainer.innerHTML = '<p>Loading...</p>'; // Show loading message

    try {
        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        const filteredEvents = filterRecentEvents(data.events);
        plotEventsOnMap(filteredEvents); // Plot the events on the map
        displayNotifications(filteredEvents);
        
    } catch (error) {
        notificationsContainer.innerHTML = `<p class="error">Error: ${error.message}</p>`;
    }
}

function filterRecentEvents(events) {
    const currentTime = new Date();
    const past72Hours = new Date(currentTime.getTime() - (144 * 60 * 60 * 1000)); // 72 hours back from now

    return events.filter(event => {
        const eventTime = new Date(event.geometries[0].date);
        return eventTime >= past72Hours && eventTime <= currentTime; // Only events within the last 72 hours
    });
}

function displayNotifications(events) {
    const notificationsContainer = document.getElementById('notifications');
    notificationsContainer.innerHTML = ''; // Clear previous notifications

    // Iterate over all filtered events and display them
    events.forEach(event => {
        const eventDiv = document.createElement('div');
        eventDiv.className = 'notification'; // General notification CSS class

        // Check if the event title contains "Wildfire" and apply fire-like background if true
        if (event.title.toLowerCase().includes('wildfire')) {
            eventDiv.classList.add('wildfire-notification');
        }
        else{
            eventDiv.classList.add('notification-card'); // Default notification CSS class
        }

        eventDiv.innerHTML = `
            <h4>${event.title}</h4>
            <p>Location: ${event.geometries[0].coordinates.join(', ')}</p>
            <p>Date: ${new Date(event.geometries[0].date).toLocaleString()}</p>
        `;
        notificationsContainer.appendChild(eventDiv);
    });
}
