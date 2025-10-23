/**
 * Controller for expanding/collapsing grade report categories (compatible with gradereport_user).
 * Fixes the visual state of the toggle icon and preserves the expanded/collapsed state
 * using localStorage for each user and course.
 *
 * @module report_lifestory/togglecategories
 */
export const init = () => {
    const params = new URLSearchParams(window.location.search);
    const userid = params.get('userid') || '0';
    const courseid = params.get('id') || 'all';
    const STORAGE_KEY = `history_student_ai_state_${userid}_${courseid}`;

    let state = {};
    try {
        state = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};
    } catch {
        state = {};
    }

    document.querySelectorAll(".toggle-category").forEach(button => {
        const icons = button.querySelectorAll(".icon, .fa, i");
        if (icons.length > 1) {
            for (let i = 0; i < icons.length - 1; i++) {
                icons[i].remove();
            }
        }

        const categoryId = button.dataset.categoryid;
        const dataTarget = button.dataset.target;
        let categoryRows = dataTarget ? document.querySelectorAll(dataTarget) : [];

        if ((!dataTarget || categoryRows.length === 0) && categoryId) {
            categoryRows = document.querySelectorAll("tr.cat_" + categoryId);
        }

        // Apply the saved expanded/collapsed state.
        const savedExpanded = state[categoryId];
        const icon = button.querySelector(".icon, .fa, i");

        if (savedExpanded === false) {
            categoryRows.forEach(r => (r.style.display = "none"));
            button.setAttribute("aria-expanded", "false");
            rotateIcon(icon, false);
        } else if (savedExpanded === true) {
            categoryRows.forEach(r => (r.style.display = ""));
            button.setAttribute("aria-expanded", "true");
            rotateIcon(icon, true);
        } else {
            // Default behavior: sync the state with current visibility.
            const visible = categoryRows.length > 0 && categoryRows[0].style.display !== "none";
            button.setAttribute("aria-expanded", visible ? "true" : "false");
            rotateIcon(icon, visible);
        }

        // Click handler: toggle visibility and persist the new state.
        button.addEventListener("click", e => {
            e.preventDefault();
            e.stopPropagation();

            const isExpanded = button.getAttribute("aria-expanded") === "true";
            const newExpanded = !isExpanded;

            categoryRows.forEach(r => (r.style.display = newExpanded ? "" : "none"));
            button.setAttribute("aria-expanded", newExpanded ? "true" : "false");
            state[categoryId] = newExpanded;
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
            rotateIcon(icon, newExpanded);
        });
    });
};

/**
 * Rotates the category toggle icon according to its expansion state.
 *
 * @param {HTMLElement|null} icon - The icon element inside the toggle button.
 * @param {boolean} expanded - Whether the category is expanded or collapsed.
 */
function rotateIcon(icon, expanded) {
    if (!icon) {
        return;
    }
    icon.style.transition = "transform 0.2s ease";
    icon.style.transform = expanded ? "rotate(90deg)" : "rotate(0deg)";
}
