// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Handles expand/collapse toggling of report categories and remembers the user’s state using local storage.
 *
 * @module      report_lifestory/togglecategories
 * @copyright   2025 Datacurso
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
            // Default behavior: sync state with current visibility.
            const visible = categoryRows.length > 0 && categoryRows[0].style.display !== "none";
            button.setAttribute("aria-expanded", visible ? "true" : "false");
            rotateIcon(icon, visible);
        }

        // Click handler: toggle visibility and persist new state.
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
