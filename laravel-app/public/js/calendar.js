document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    const modalEl = document.getElementById("eventModal");
    const modalStart = document.getElementById("modalStart");
    const modalEnd = document.getElementById("modalEnd");
    const eventModal = new bootstrap.Modal(modalEl);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        locale: "ja",
        events: window.scheduleEvents,
        eventClick: function (info) {
            modalStart.textContent = info.event.start.toLocaleString();
            modalEnd.textContent = info.event.end
                ? info.event.end.toLocaleString()
                : "";
            eventModal.show();
        },
    });

    calendar.render();
});
