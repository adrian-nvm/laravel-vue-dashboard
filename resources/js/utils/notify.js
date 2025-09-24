export function authError(error, toast) {
    let errorMessages = [];
    let detailedMessages = [];

    if (error.response.data.errors) {
        detailedMessages = [].concat.apply(
            [],
            Object.values(error.response.data.errors)
        );
        errorMessages = detailedMessages; // Use detailed messages if available
    } else {
        let errorMessage =
            error.response.data.message || "The given data was invalid.";
        errorMessages.push(errorMessage); // Otherwise, use the general message
    }

    if (toast) {
        errorMessages.forEach(message => {
            toast.error(message);
        });
    } else {
        alert(errorMessages.join("\n")); // Combine all messages into a single alert
    }
}

export function handleError(error, toast) {
    if (error.response && error.response.data && error.response.data.message) {
        toast.error(error.response.data.message);
    } else if (error.message) {
        toast.error(error.message);
    } else {
        toast.error("An unexpected error occurred.");
    }
}
