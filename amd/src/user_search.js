// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * User search functionality for student_life_story_ai report.
 *
 * @module     report_student_life_story_ai/user_search
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function ($, Ajax, Notification) {

    let searchTimeout = null;
    const SEARCH_DELAY = 300;

    /**
     * Search for students
     * @param {string} query Search query
     * @return {Promise}
     */
    const searchStudents = function (query) {
        return Ajax.call([{
            methodname: 'report_student_life_story_ai_search_students',
            args: { query: query }
        }])[0];
    };

    /**
     * Render search results
     * @param {Array} results Array of student objects
     * @param {string} baseUrl Base URL for links
     */
    const renderResults = function (results, baseUrl) {
        const container = $('#search-results');
        container.empty();

        if (!results || results.length === 0) {
            container.addClass('d-none');
            return;
        }

        let html = '';
        results.forEach(function (student) {
            const avatar = student.profileimageurl
                ? `<img src="${student.profileimageurl}" alt="${student.fullname}"
                         class="rounded-circle me-3" style="width:40px; height:40px; object-fit:cover;">`
                : `<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                         style="width:40px; height:40px; flex-shrink:0; font-size:18px; font-weight:bold;">
                         ${student.fullname.charAt(0).toUpperCase()}
                   </div>`;

            html += `
                <a href="${baseUrl}?userid=${student.id}"
                   class="d-block p-2 text-decoration-none border-bottom"
                   style="color: inherit; transition: background-color 0.15s;">
                    <div class="d-flex align-items-center">
                        ${avatar}
                        <div>
                            <div class="fw-bold">${student.fullname}</div>
                            <small class="text-muted">${student.email}</small>
                        </div>
                    </div>
                </a>
            `;
        });

        container.html(html).removeClass('d-none');

        // Hover effect similar to Moodle
        container.find('a').hover(
            function () { $(this).css('background-color', '#f8f9fa'); },
            function () { $(this).css('background-color', ''); }
        );
    };

    /**
     * Initialize the user search
     * @param {string} baseUrl Base URL for the page
     */
    const init = function (baseUrl) {
        const searchInput = $('#usersearch');
        const clearButton = $('#clearsearch');
        const resultsContainer = $('#search-results');

        if (searchInput.length === 0) {
            return;
        }

        // --- Input event ---
        searchInput.on('input', function () {
            const query = $(this).val().trim();

            // Mostrar / ocultar la X
            if (query.length > 0) {
                clearButton.show();
            } else {
                clearButton.hide();
                resultsContainer.addClass('d-none').empty();
                return;
            }

            // Cancelar búsqueda anterior
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Esperar un poco antes de buscar
            searchTimeout = setTimeout(function () {
                searchStudents(query)
                    .then(function (response) {
                        const students = response && response.students ? response.students : [];
                        renderResults(students, baseUrl);
                    })
                    .catch(Notification.exception);
            }, SEARCH_DELAY);
        });

        // --- Botón limpiar dentro del input ---
        clearButton.on('click', function (e) {
            e.preventDefault();
            searchInput.val('');
            clearButton.hide();
            resultsContainer.addClass('d-none').empty();
            searchInput.focus();
        });

        // --- Mostrar resultados al enfocar ---
        searchInput.on('focus', function () {
            const query = $(this).val().trim();
            if (query.length > 0 && resultsContainer.children().length > 0) {
                resultsContainer.removeClass('d-none');
            }
        });

        // --- Ocultar resultados al hacer clic fuera ---
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#usersearch-wrapper').length) {
                resultsContainer.addClass('d-none');
            }
        });

        // --- Evitar submit con Enter ---
        searchInput.on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    };

    return { init: init };
});
