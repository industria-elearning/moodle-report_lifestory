/**
 * Controlador JS para expandir/colapsar categorías en el historial de estudiante.
 */
export const init = () => {

    // Manejar clicks en botones de categoría
    document.querySelectorAll(".toggle-category").forEach(button => {
        button.addEventListener("click", e => {
            e.preventDefault();
            e.stopPropagation();

            const categoryId = button.dataset.categoryid;
            const dataTarget = button.dataset.target;
            let categoryRows = dataTarget ? document.querySelectorAll(dataTarget) : [];

            if ((!dataTarget || categoryRows.length === 0) && categoryId) {
                categoryRows = document.querySelectorAll("tr.cat_" + categoryId);
            }

            if (categoryRows.length > 0) {
                const isExpanded = button.getAttribute("aria-expanded") === "true";

                if (isExpanded) {
                    // Colapsar
                    categoryRows.forEach(row => {
                        row.style.display = "none";
                    });
                    button.setAttribute("aria-expanded", "false");
                    button.querySelector(".collapsed")?.classList.remove("d-none");
                    button.querySelector(".expanded")?.classList.add("d-none");
                } else {
                    // Expandir
                    categoryRows.forEach(row => {
                        row.style.display = "";
                    });
                    button.setAttribute("aria-expanded", "true");
                    button.querySelector(".collapsed")?.classList.add("d-none");
                    button.querySelector(".expanded")?.classList.remove("d-none");
                }
            }
        });
    });

    // Estado inicial de los botones
    document.querySelectorAll(".toggle-category").forEach(button => {
        const isExpanded = button.getAttribute("aria-expanded") === "true";
        button.querySelector(".collapsed")?.classList.toggle("d-none", isExpanded);
        button.querySelector(".expanded")?.classList.toggle("d-none", !isExpanded);
    });
};
