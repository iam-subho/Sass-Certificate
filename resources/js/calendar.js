import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

// Initialize calendar when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('fullcalendar');

    if (calendarEl) {
        const eventsUrl = calendarEl.dataset.eventsUrl;

        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            firstDay: 1, // Monday as the first day of the week
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek'
            },
            events: eventsUrl,
            eventDisplay: 'block',
            height: 'auto',
            eventClick: function(info) {
                // Show event details in a tooltip or modal
                const event = info.event;
                const props = event.extendedProps;

                let details = `<strong>${event.title}</strong><br>`;
                details += `<strong>Date:</strong> ${event.start.toLocaleDateString()}`;

                if (event.end) {
                    details += ` - ${event.end.toLocaleDateString()}`;
                }

                if (props.type === 'inter-school') {
                    if (props.venue) details += `<br><strong>Venue:</strong> ${props.venue}`;
                    if (props.category) details += `<br><strong>Category:</strong> ${props.category}`;
                    if (props.status) details += `<br><strong>Status:</strong> ${props.status}`;
                } else if (props.type === 'school') {
                    if (props.description) details += `<br><strong>Description:</strong> ${props.description}`;
                    if (props.event_type) details += `<br><strong>Type:</strong> ${props.event_type}`;
                }

                // Create a simple tooltip
                showEventTooltip(info.jsEvent.clientX, info.jsEvent.clientY, details);
            },
            eventMouseLeave: function() {
                hideEventTooltip();
            }
        });

        calendar.render();
    }
});

// Simple tooltip functions
function showEventTooltip(x, y, content) {
    // Remove existing tooltip
    hideEventTooltip();

    const tooltip = document.createElement('div');
    tooltip.id = 'event-tooltip';
    tooltip.className = 'absolute z-50 bg-gray-900 text-white text-sm rounded-lg shadow-lg p-3 max-w-xs';
    tooltip.innerHTML = content;
    tooltip.style.left = x + 'px';
    tooltip.style.top = (y + 10) + 'px';

    document.body.appendChild(tooltip);
}

function hideEventTooltip() {
    const tooltip = document.getElementById('event-tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}
