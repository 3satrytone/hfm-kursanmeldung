import AjaxRequest from '@typo3/core/ajax/ajax-request.js';

/**
 * Lädt die Exportliste via TYPO3 AJAX-Route und rendert sie in das Zielelement.
 *
 * @param {string[]} selectedFields - Liste der ausgewählten Felder
 * @param {HTMLElement} targetElement - Zielelement für den geladenen HTML-Inhalt
 * @param {Object} [filters] - Optionale Filterwerte (field => value)
 */
export function loadExportliste(selectedFields, targetElement, filters) {
    if (!selectedFields || selectedFields.length === 0) {
        targetElement.innerHTML = '<div class="alert alert-warning">Bitte wählen Sie mindestens eine Spalte im Setup aus.</div>';
        return;
    }

    targetElement.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Laden...</span></div>';

    const queryArgs = {fields: selectedFields.join(',')};
    if (filters) {
        Object.keys(filters).forEach(function(field) {
            if (filters[field] !== '') {
                queryArgs['filters[' + field + ']'] = filters[field];
            }
        });
    }

    new AjaxRequest(TYPO3.settings.ajaxUrls.kursanmeldung_exportlist_ajaxlist)
        .withQueryArguments(queryArgs)
        .get()
        .then(async function (response) {
            const html = await response.resolve('text/html');
            targetElement.innerHTML = html;

            // Blur-basierte Filterlogik: bei Verlassen des Feldes neuen AJAX-Call senden
            const colFilters = targetElement.querySelectorAll('.exportliste-col-filter');
            colFilters.forEach(function(input) {
                input.addEventListener('blur', function() {
                    const currentFilters = {};
                    colFilters.forEach(function(inp) {
                        if (inp.value !== '') {
                            currentFilters[inp.dataset.col] = inp.value;
                        }
                    });
                    loadExportliste(selectedFields, targetElement, currentFilters);
                });
            });
        })
        .catch(function () {
            targetElement.innerHTML = '<div class="alert alert-danger">Fehler beim Laden der Exportliste.</div>';
        });
}
