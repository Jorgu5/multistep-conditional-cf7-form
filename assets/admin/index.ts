const formId = document.location.search.match(/post=(\d+)/)?.[1];
const url: string = `/wp-json/cf7lms/v1/new-step?id=${formId}`;

// Fetch the new HTML from the server.
// set timeout to 1 second to simulate a slow connection.
setTimeout(() => {
    fetch(url)
        .then((response: Response) => {
            // Check that the request was successful.
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then((html: string) => {
            // Create a new element from the HTML.
            const newSection: HTMLDivElement = document.createElement('div');
            newSection.innerHTML = html;

            // Insert the new section into the page.
            const container: HTMLElement | null = document.querySelector('.cf7mls-wrap-form');
            if (container) {
                container.appendChild(newSection);
            }
        })
        .catch((e: Error) => {
            console.error('Error: ' + e.message);
        });
}, 10000);
