/**
 * Controlador reutilizable para mostrar un loader y deshabilitar un botón mientras se procesa una acción.
 */
export const init = () => {
    const buttons = document.querySelectorAll("[data-loader-text]");

    buttons.forEach(button => {
        button.addEventListener("click", e => {
            if (button.classList.contains('report_lifestory-btnloading')) {
                e.preventDefault();
                return;
            }

            e.preventDefault();

            const loaderText = button.dataset.loaderText || "Procesando...";
            const redirect = button.href || button.dataset.redirect;
            const originalText = button.textContent.trim();
            button.dataset.originalText = originalText;

            // Mostrar loader
            button.classList.add('report_lifestory-btnloading');
            button.setAttribute("aria-disabled", "true");
            button.style.pointerEvents = "none";
            button.textContent = loaderText;

            if (!redirect) {
                return;
            }

            if (redirect.includes("action=csv")) {
                const iframe = document.createElement("iframe");
                iframe.style.display = "none";
                iframe.src = redirect;
                document.body.appendChild(iframe);

                setTimeout(() => {
                    button.classList.remove('report_lifestory-btnloading');
                    button.removeAttribute("aria-disabled");
                    button.style.pointerEvents = "";
                    button.textContent = originalText;
                    iframe.remove();
                }, 500);
            } else {
                window.location.href = redirect;
            }
        });
    });
};
