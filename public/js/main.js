// Function to reset all navigation link colors
function resetNavColors() {
    const navLinks = document.querySelectorAll("nav ul li a");
    navLinks.forEach(link => {
        link.style.color = "white";
    });
}

// Function to highlight the active navigation link
function setActiveNav(linkId) {
    resetNavColors();
    const activeLink = document.getElementById(linkId);
    if (activeLink) {
        activeLink.style.color = "rgba(251, 20, 197, 1)";
    } else {
        console.error(`Element with ID "${linkId}" not found.`);
    }
}

// Navigation functions
function home() {
    setActiveNav("homes");
}

function program() {
    setActiveNav("programs");
}

function plan() {
    setActiveNav("plans");
}

function blog() {
    setActiveNav("blogs");
}

function contact() {
    setActiveNav("contacts");
}

// Submit form validation
function submitForm() {
    const name = document.getElementById("name");
    const email = document.getElementById("email");
    const password = document.getElementById("password");

    // Validate name
    if (!name || name.value.trim() === "") {
        alert("Please enter your name.");
        if (name) name.focus();
        return false;
    }

    // Validate email
    if (!email || email.value.trim() === "") {
        alert("Please enter your email address.");
        if (email) email.focus();
        return false;
    } else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
        alert("Please enter a valid email address.");
        email.focus();
        return false;
    }

    // Validate password
    if (!password || password.value.trim() === "") {
        alert("Please enter your password.");
        if (password) password.focus();
        return false;
    } else if (password.value.length < 6) {
        alert("Password must be at least 6 characters long.");
        password.focus();
        return false;
    }

    // Success message
    alert(`Thanks for registering, ${name.value}!`);
    return true;
}