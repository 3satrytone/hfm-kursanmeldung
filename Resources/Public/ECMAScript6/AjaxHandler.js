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

/**
 * Speichert ein benanntes Export-Setup (ausgewählte Felder inkl. Reihenfolge) via TYPO3 AJAX-Route.
 * Es können mehrere Setups unter verschiedenen Namen gespeichert werden.
 *
 * @param {string} name - Name, unter dem das Setup gespeichert werden soll
 * @param {string[]} selectedFields - Liste der ausgewählten Felder in gewünschter Reihenfolge
 * @returns {Promise<Object>} - Promise mit dem JSON-Ergebnis der Speicherung
 */
export function saveSetup(name, selectedFields) {
    const fields = (selectedFields || []).join(',');

    return new AjaxRequest(TYPO3.settings.ajaxUrls.kursanmeldung_exportlist_savesetup)
        .post({name: name || 'default', fields: fields})
        .then(async function (response) {
            return await response.resolve();
        });
}

/**
 * Lädt ein zuvor gespeichertes, benanntes Export-Setup (ausgewählte Felder inkl. Reihenfolge) via TYPO3 AJAX-Route.
 *
 * @param {string} name - Name des zu ladenden Setups
 * @returns {Promise<string[]>} - Promise mit der Liste der gespeicherten Felder
 */
export function loadSetup(name) {
    return new AjaxRequest(TYPO3.settings.ajaxUrls.kursanmeldung_exportlist_loadsetup)
        .withQueryArguments({name: name || 'default'})
        .get()
        .then(async function (response) {
            const json = await response.resolve();
            return (json && Array.isArray(json.fields)) ? json.fields : [];
        })
        .catch(function () {
            return [];
        });
}

/**
 * Lädt die Namen aller gespeicherten Export-Setups via TYPO3 AJAX-Route.
 *
 * @returns {Promise<string[]>} - Promise mit der Liste der gespeicherten Setup-Namen
 */
export function listSetups() {
    return new AjaxRequest(TYPO3.settings.ajaxUrls.kursanmeldung_exportlist_listsetups)
        .get()
        .then(async function (response) {
            const json = await response.resolve();
            return (json && Array.isArray(json.names)) ? json.names : [];
        })
        .catch(function () {
            return [];
        });
}

/**
 * Löscht ein benanntes Export-Setup via TYPO3 AJAX-Route.
 *
 * @param {string} name - Name des zu löschenden Setups
 * @returns {Promise<Object>} - Promise mit dem JSON-Ergebnis der Löschung
 */
export function deleteSetup(name) {
    return new AjaxRequest(TYPO3.settings.ajaxUrls.kursanmeldung_exportlist_deletesetup)
        .post({name: name || ''})
        .then(async function (response) {
            return await response.resolve();
        });
}
