import AjaxRequest from '@typo3/core/ajax/ajax-request.js';

/**
 * Lädt die Exportliste via TYPO3 AJAX-Route und rendert sie in das Zielelement.
 *
 * @param {string[]} selectedFields - Liste der ausgewählten Felder
 * @param {HTMLElement} targetElement - Zielelement für den geladenen HTML-Inhalt
 */
export function loadExportliste(selectedFields, targetElement) {
    if (!selectedFields || selectedFields.length === 0) {
        targetElement.innerHTML = '<div class="alert alert-warning">Bitte wählen Sie mindestens eine Spalte im Setup aus.</div>';
        return;
    }

    targetElement.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Laden...</span></div>';

    new AjaxRequest(TYPO3.settings.ajaxUrls.kursanmeldung_exportlist_ajaxlist)
        .withQueryArguments({fields: selectedFields.join(',')})
        .get()
        .then(async function (response) {
            const html = await response.resolve('text/html');
            targetElement.innerHTML = html;

            // Filterlogik für die geladene Tabelle einrichten
            const filterInput = targetElement.querySelector('#exportlisteFilter');
            const table = targetElement.querySelector('#exportlisteTable');
            if (filterInput && table) {
                filterInput.addEventListener('input', function () {
                    const filter = filterInput.value.toLowerCase();
                    Array.from(table.tBodies[0].rows).forEach(function (row) {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(filter) ? '' : 'none';
                    });
                });
            }
        })
        .catch(function () {
            targetElement.innerHTML = '<div class="alert alert-danger">Fehler beim Laden der Exportliste.</div>';
        });
}
