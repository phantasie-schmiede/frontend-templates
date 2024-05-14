var ready = (callback) => {
    if (document.readyState != "loading") {
        callback();
    } else {
        document.addEventListener("DOMContentLoaded", callback);
    }
}

ready(() => {
    // Bind click events to all elements with #dynamicModal as target modal.
    var elements = document.querySelectorAll('[data-bs-target="#dynamicModal"]');
    elements.forEach((element) => {
        element.addEventListener('click', function(event) {
            event.preventDefault();

            // XHR request to the href attribute of the clicked element
            let xhr = new XMLHttpRequest();
            xhr.open('GET', element.href, true);
            xhr.responseType = 'json';
            xhr.send();
            xhr.onload = function() {
                if (xhr.status != 200) {
                    console.error(`Error ${xhr.status}: ${xhr.statusText}`);
                } else {
                    let modal = checkIfModalExists();
                    let response = xhr.response;

                    modal.querySelector('.modal-title').textContent = response.title;
                    modal.querySelector('.modal-body').innerHTML = response.body;
                    modal.querySelector('.modal-footer').innerHTML = response.footer;
                }
            }
        });
    });

    function checkIfModalExists() {
        let modal = document.getElementById("dynamicModal");

        if (null === modal) {
            return createModal();
        }

        return modal;
    }

    function createModal() {
        var modal = document.createElement("div");
        modal.classList.add("fade", "modal");
        modal.id = "dynamicModal";
        modal.setAttribute("aria-hidden", "true");
        modal.setAttribute("aria-labelledby", "dynamicModalLabel");
        modal.tabIndex = -1;
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1
                            class="modal-title fs-5"
                            id="dynamicModalLabel"></h1>
                        <button
                            aria-label="Close"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            type="button"></button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        return modal;
    }
});
