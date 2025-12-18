let arrivalDay = null;
let departureDay = null;

const arrivalInput = document.getElementById("arrivalInput");
const departureInput = document.getElementById("departureInput");

const calendarDays = document.querySelectorAll(".calendar .day");

/**
 * Check if a day is booked
 */
function isDayBooked(dayNumber) {
    return bookedDays.includes(dayNumber);
}

/**
 * Convert day number to YYYY-MM-DD
 */
function formatDate(dayNumber) {
    return `${calendarYear}-01-${String(dayNumber).padStart(2, "0")}`;
}

/**
 * Reset selection
 */
function resetSelection() {
    arrivalDay = null;
    departureDay = null;

    arrivalInput.value = "";
    departureInput.value = "";

    calendarDays.forEach(day => {
        day.classList.remove("selected", "in-range");
    });
}

/**
 * Check if range crosses booked dates
 */
function rangeIsValid(startDay, endDay) {
    for (let day = startDay; day <= endDay; day++) {
        if (isDayBooked(day)) {
            return false;
        }
    }
    return true;
}

/**
 * Highlight selected range
 */
function highlightRange(startDay, endDay) {
    calendarDays.forEach(dayElement => {
        const dayNumber = Number(dayElement.dataset.day);

        if (dayNumber >= startDay && dayNumber <= endDay) {
            dayElement.classList.add("in-range");
        }
    });
}

/**
 * Handle day click
 */
function onDayClick(event) {
    const dayElement = event.currentTarget;
    const dayNumber = Number(dayElement.dataset.day);

    if (isDayBooked(dayNumber)) {
        return;
    }

    if (arrivalDay === null) {
        resetSelection();
        arrivalDay = dayNumber;
        arrivalInput.value = formatDate(dayNumber);
        dayElement.classList.add("selected");
        return;
    }

    if (dayNumber <= arrivalDay) {
        return;
    }

    if (!rangeIsValid(arrivalDay, dayNumber)) {
        alert("Selected range includes booked dates");
        return;
    }

    departureDay = dayNumber;
    departureInput.value = formatDate(dayNumber);
    highlightRange(arrivalDay, departureDay);
}

calendarDays.forEach(day => {
    day.addEventListener("click", onDayClick);
});
