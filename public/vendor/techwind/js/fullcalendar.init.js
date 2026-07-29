/* Template Name: Techwind - Tailwind CSS Multipurpose Landing & Admin Dashboard Template
   Author: Shreethemes
   Email: support@shreethemes.in
   Website: https://shreethemes.in
   Version: 3.2.0
   Created: May 2022
   File Description: fullcalender.init.js for Calender
*/

document.addEventListener('DOMContentLoaded', function () {

    var calendarEl = document.getElementById('calendar');
    if (!calendarEl || typeof FullCalendar === "undefined") {
        return;
    }

    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendar.Draggable;

    // Elements
    var containerEl = document.getElementById('external-events');
    var checkbox = document.getElementById('drop-remove');

    // -----------------------------------------------------------------
    // External Draggable Events
    // -----------------------------------------------------------------
    if (containerEl) {
        try {
            new Draggable(containerEl, {
                itemSelector: '.fc-event',
                eventData: function (eventEl) {
                    return {
                        title: eventEl.innerText
                    };
                }
            });
        } catch (e) {
            console.warn('FullCalendar Draggable skipped:', e);
        }
    }

    // -----------------------------------------------------------------
    // Calendar Init
    // -----------------------------------------------------------------
    try {
        var calendar = new Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today addEventButton',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },

            businessHours: true,
            editable: true,
            droppable: true,

            events: [
                {
                    title: 'Business Lunch',
                    start: '2027-08-03T13:00:00',
                    constraint: 'businessHours'
                },
                {
                    title: 'Meeting',
                    start: '2027-08-13T11:00:00',
                    color: '#53c797'
                },
                {
                    title: 'Conference',
                    start: '2027-08-18',
                    end: '2027-08-20'
                },
                {
                    title: 'Party',
                    start: '2027-08-29T20:00:00'
                }
            ],

            customButtons: {
                addEventButton: {
                    text: 'Add Event',
                    click: function () {
                        var dateStr = prompt('Enter a date in YYYY-MM-DD format');
                        var date = new Date(dateStr + 'T00:00:00');

                        if (!isNaN(date.valueOf())) {
                            calendar.addEvent({
                                title: 'Dynamic Event',
                                start: date,
                                allDay: true
                            });
                            alert('Event added (DB update pending)');
                        } else {
                            alert('Invalid date');
                        }
                    }
                }
            },

            drop: function (info) {
                if (checkbox && checkbox.checked) {
                    info.draggedEl.parentNode.removeChild(info.draggedEl);
                }
            }
        });

        calendar.render();
    } catch (e) {
        console.warn('FullCalendar skipped:', e);
    }

});