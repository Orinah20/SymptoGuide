// Function to display the remaining time
function displayRemainingTime() {
    const sessionExpireTime = 3600; // Time limit in seconds (1 hour)
    let remainingTime = sessionExpireTime; // Initial remaining time in seconds

    const timerElement = document.getElementById('timer'); // Get the timer element
    const sessionExpireElement = document.getElementById('session-expire'); // Get the session expire element
    let countdown; // Variable to hold the countdown timeout

    // Function to update the countdown timer
    function updateTimer() {
        var minutes = Math.floor(remainingTime / 60); // Calculate minutes
        var seconds = remainingTime % 60; // Calculate seconds

        // Display the remaining time on the page when it's 30 seconds or less
        if (remainingTime <= 30) {
            timerElement.textContent = minutes + 'm ' + seconds + 's';
            timerElement.style.display = 'block'; // Show the timer element
            sessionExpireElement.style.display = 'block'; // Show the session expire element
        }

        // Update the timer every second
        remainingTime--; // Decrease the remaining time by 1 second
        if (remainingTime >= 0) {
            countdown = setTimeout(updateTimer, 1000);
        } else {
            // Session expired, redirect to logout page
            window.location.href = 'logout.php';
        }
    }

    // Function to reset the countdown
    function resetCountdown() {
        clearTimeout(countdown); // Clear the existing timeout
        remainingTime = sessionExpireTime; // Reset the remaining time
        timerElement.style.display = 'none'; // Hide the timer element
        sessionExpireElement.style.display = 'none'; // Hide the session expire element
        countdown = setTimeout(updateTimer, 1000); // Start a new countdown
    }

    // Attach event listeners to different types of user activity
    window.addEventListener('click', resetCountdown);
    window.addEventListener('keydown', resetCountdown);
    window.addEventListener('mousemove', resetCountdown);

    // Start the initial countdown
    countdown = setTimeout(updateTimer, 1000);
}

// Call the displayRemainingTime function when the page loads
window.addEventListener('load', displayRemainingTime);

function togglePasswordVisibility() {
    var passwordInput = document.getElementById("password");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    } else {
        passwordInput.type = "password";
    }
}

function setMaxDate() {
    // Get the current date
    const currentDate = new Date();

    // Calculate the maximum allowed date (22 years ago)
    const maxDate = new Date();
    maxDate.setFullYear(currentDate.getFullYear() - 22);

    // Convert the maximum date to a string in yyyy-mm-dd format
    const maxDateString = maxDate.toISOString().split('T')[0];

    // Set the maximum date for the date picker
    document.getElementById("date_of_birth").setAttribute("max", maxDateString);
}

// Call the function to set the maximum date when the page loads
window.onload = setMaxDate;


