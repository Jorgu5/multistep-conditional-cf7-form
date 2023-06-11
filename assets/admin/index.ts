interface ResponseData {
    success: boolean;
    html: string;
    message?: string;
}

document.querySelector('.cf7lms-add-step')?.addEventListener('click', (e: Event) => {
    e.preventDefault();
    const url = new URL(window.location.href);
    const postId = url.searchParams.get("post");

    // Create a new FormData instance
    const formData = new FormData();

    // Append each piece of data
    formData.append('action', 'add_new_step');
    formData.append('form_id', postId || '');
    formData.append('nonce', cf7lms.nonce);

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
        .then((response: Response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json() as Promise<ResponseData>;
        })
        .then((resp: ResponseData) => {
            if (resp.success) {
                console.log(resp);
                const formContainer: HTMLElement | null = document.querySelector('.cf7lms-steps-wrapper');
                let wrap = document.createElement('div');
                // append wrap after formContainer
                formContainer?.parentNode?.insertBefore(wrap, formContainer.nextSibling);
                if (formContainer?.parentNode) {
                    wrap.appendChild(formContainer.cloneNode(true));
                }
            } else {
                // Handle error
                console.error(resp);
            }
        })
        .catch((error: Error) => {
            console.error('Request error...', error);
        });
});
