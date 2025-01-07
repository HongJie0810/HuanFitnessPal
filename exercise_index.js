let date = new Date();
let year = date.getFullYear();
let month = date.getMonth();

const day = document.querySelector(".calendar-dates");
const currdate = document.querySelector(".calendar-current-date");
const prenexIcons = document.querySelectorAll(".calendar-navigation span");

// Array of month names
const months = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// Get date from URL parameters (if present)
const urlParams = new URLSearchParams(window.location.search);
const selectedDate = urlParams.get('date');

if (selectedDate) {
    const [selectedYear, selectedMonth, selectedDay] = selectedDate.split('-');
    year = parseInt(selectedYear);
    month = parseInt(selectedMonth) - 1; // Month is zero-based
    date.setDate(parseInt(selectedDay));
} else {
    // Set to current date if no date is selected
    date = new Date();
    year = date.getFullYear();
    month = date.getMonth();
}

// Function to generate the calendar
const manipulate = () => {
    let dayone = new Date(year, month, 1).getDay();
    let lastdate = new Date(year, month + 1, 0).getDate();

    let lit = "";

    // Days from previous month
    for (let i = dayone; i > 0; i--) {
        lit += `<li class="inactive"></li>`;
    }

    // Dates for the current month
    for (let i = 1; i <= lastdate; i++) {
        let isToday = i === date.getDate()
            && month === new Date().getMonth()
            && year === new Date().getFullYear()
            ? "active"
            : "";
        lit += `<li class="${isToday}" onclick="redirectToPage(${i})">${i}</li>`;
    }

    // Empty "inactive" slots for next month days
    let totalDays = dayone + lastdate;
    let remainingDays = totalDays % 7 === 0 ? 0 : 7 - (totalDays % 7);
    for (let i = 1; i <= remainingDays; i++) {
        lit += `<li class="inactive"></li>`;
    }

    currdate.innerText = `${months[month]} ${year}`;
    day.innerHTML = lit;
}

manipulate();

// Attach click event listener to each icon
prenexIcons.forEach(icon => {
    icon.addEventListener("click", () => {
        month = icon.id === "calendar-prev" ? month - 1 : month + 1;

        // Adjust year if month goes out of bounds
        if (month < 0) {
            month = 11;
            year -= 1;
        } else if (month > 11) {
            month = 0;
            year += 1;
        }

        manipulate();
    });
});

function redirectToPage(selectedDay) {
    const selectedDate = `${year}-${month + 1}-${selectedDay}`; // Format: YYYY-MM-DD
    window.location.href = `record_exercise.php?date=${selectedDate}`;
}
