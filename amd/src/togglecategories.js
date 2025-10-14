/**
 * Controlador JS para expandir/colapsar categorías en el historial de estudiante.
 * Mantiene el estado expandido/colapsado usando localStorage por usuario y curso.
 */
export const init = () => {
    const params = new URLSearchParams(window.location.search);
    const userid = params.get('userid') || '0';
    const courseid = params.get('id') || 'all';
    const STORAGE_KEY = `history_student_ai_state_${userid}_${courseid}`;

    // Cargar estado previo
    let state = {};
    try {
        state = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};
    } catch (e) {
        state = {};
    }

    // Inicializa botones y aplica estado guardado
    document.querySelectorAll(".toggle-category").forEach(button => {
        const categoryId = button.dataset.categoryid;
        const dataTarget = button.dataset.target;
        let categoryRows = dataTarget ? document.querySelectorAll(dataTarget) : [];

        if ((!dataTarget || categoryRows.length === 0) && categoryId) {
            categoryRows = document.querySelectorAll("tr.cat_" + categoryId);
        }

        // Aplica estado guardado (expandido o colapsado)
        const savedExpanded = state[categoryId];
        if (savedExpanded === false) {
            categoryRows.forEach(row => (row.style.display = "none"));
            button.setAttribute("aria-expanded", "false");
            button.querySelector(".collapsed")?.classList.remove("d-none");
            button.querySelector(".expanded")?.classList.add("d-none");
        } else if (savedExpanded === true) {
            categoryRows.forEach(row => (row.style.display = ""));
            button.setAttribute("aria-expanded", "true");
            button.querySelector(".collapsed")?.classList.add("d-none");
            button.querySelector(".expanded")?.classList.remove("d-none");
        }

        // Evento de click para alternar y guardar estado
        button.addEventListener("click", e => {
            e.preventDefault();
            e.stopPropagation();

            const isExpanded = button.getAttribute("aria-expanded") === "true";

            if (isExpanded) {
                // Colapsar
                categoryRows.forEach(row => (row.style.display = "none"));
                button.setAttribute("aria-expanded", "false");
                button.querySelector(".collapsed")?.classList.remove("d-none");
                button.querySelector(".expanded")?.classList.add("d-none");
                state[categoryId] = false;
            } else {
                // Expandir
                categoryRows.forEach(row => (row.style.display = ""));
                button.setAttribute("aria-expanded", "true");
                button.querySelector(".collapsed")?.classList.add("d-none");
                button.querySelector(".expanded")?.classList.remove("d-none");
                state[categoryId] = true;
            }

            // Guardar estado actualizado
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        });
    });

    // Asegurar íconos iniciales correctos
    document.querySelectorAll(".toggle-category").forEach(button => {
        const isExpanded = button.getAttribute("aria-expanded") === "true";
        button.querySelector(".collapsed")?.classList.toggle("d-none", isExpanded);
        button.querySelector(".expanded")?.classList.toggle("d-none", !isExpanded);
    });
};
