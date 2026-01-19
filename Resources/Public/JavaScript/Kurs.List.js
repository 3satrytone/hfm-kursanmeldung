// Kopiert beim Klick auf Links mit Klasse "copyLink" den href-Wert in die Zwischenablage
// Datei wird in Templates/Kurs/List.html via f:asset.script eingebunden

(function () {
  function copyTextToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      return navigator.clipboard.writeText(text);
    }
    return new Promise(function (resolve, reject) {
      try {
        var textarea = document.createElement('textarea');
        textarea.value = text;
        // außerhalb des sichtbaren Bereichs
        textarea.style.position = 'fixed';
        textarea.style.top = '-1000px';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        var ok = document.execCommand('copy');
        document.body.removeChild(textarea);
        ok ? resolve() : reject(new Error('copy command failed'));
      } catch (e) {
        reject(e);
      }
    });
  }

  function onCopyLinkClick(e) {
    // nur linke Maustaste/Enter ohne Modifikatoren behandeln
    if (e.type === 'click') {
      if (e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) {
        return; // normales Browser-Verhalten zulassen
      }
    }

    e.preventDefault();
    e.stopPropagation();
    var a = e.currentTarget;
    var href = a.getAttribute('href');
    if (!href) { return; }

    copyTextToClipboard(href)
      .then(function () {
        // kurzes visuelles Feedback am Button
        var previousTitle = a.getAttribute('title');
        a.setAttribute('title', 'Link kopiert');
        a.classList.add('copied');
        // Optional: kurz die Opacity ändern, falls Bootstrap vorhanden ist
        a.style.opacity = '0.6';
        setTimeout(function () {
          if (previousTitle != null) {
            a.setAttribute('title', previousTitle);
          } else {
            a.removeAttribute('title');
          }
          a.classList.remove('copied');
          a.style.opacity = '';
        }, 1200);
      })
      .catch(function () {
        // Fallback Feedback
        alert('Link wurde nicht automatisch kopiert. Bitte manuell kopieren:\n' + href);
      });
  }

  function bindCopyLinks(root) {
    var links = (root || document).querySelectorAll('a.copyLink');
    links.forEach(function (link) {
      // Doppelte Handler vermeiden
      link.removeEventListener('click', onCopyLinkClick);
      link.addEventListener('click', onCopyLinkClick);
      // Tastatur: Enter/Space beim Fokus auf dem Link ebenfalls abfangen
      link.removeEventListener('keydown', onKeyDownHandler);
      link.addEventListener('keydown', onKeyDownHandler);
    });
  }

  function onKeyDownHandler(e) {
    if (e.key === 'Enter' || e.key === ' ') {
      onCopyLinkClick(e);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { bindCopyLinks(); bindDeleteConfirm(); });
  } else {
    bindCopyLinks();
    bindDeleteConfirm();
  }
})();

// -------------------------------------------------------------
// Lösch-Bestätigung per Bootstrap-Modal für Links mit title="löschen"
// -------------------------------------------------------------
(function () {
  var pendingDeleteHref = null;
  var modalInstance = null;
  var modalElement = null;
  var confirmBtn = null;

  function ensureModal() {
    if (modalElement) { return; }

    // Modal-Markup erstellen
    var wrapper = document.createElement('div');
    wrapper.innerHTML = `
      <div class="modal fade" tabindex="-1" id="kursDeleteConfirmModal" aria-labelledby="kursDeleteConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="kursDeleteConfirmLabel">Löschen bestätigen</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Möchten Sie diesen Eintrag wirklich löschen?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
              <button type="button" class="btn btn-danger" id="kursDeleteConfirmBtn">Löschen</button>
            </div>
          </div>
        </div>
      </div>`;

    modalElement = wrapper.firstElementChild;
    document.body.appendChild(modalElement);

    // Bootstrap 5 Modal initialisieren, falls vorhanden
    if (window.bootstrap && window.bootstrap.Modal) {
      modalInstance = new window.bootstrap.Modal(modalElement, {
        backdrop: 'static',
        keyboard: true
      });
    }

    confirmBtn = modalElement.querySelector('#kursDeleteConfirmBtn');
    if (confirmBtn) {
      confirmBtn.addEventListener('click', function () {
        if (pendingDeleteHref) {
          var href = pendingDeleteHref;
          pendingDeleteHref = null;
          if (modalInstance) { modalInstance.hide(); }
          // Navigieren
          window.location.href = href;
        }
      });
    }
  }

  function onDeleteClick(e) {
    // Nur linke Maustaste ohne Modifikatoren abfangen
    if (e.type === 'click') {
      if (e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) {
        return; // Standardnavigation zulassen
      }
    }

    var a = e.currentTarget;
    var href = a.getAttribute('href');
    if (!href) { return; }

    // Wenn kein Bootstrap vorhanden ist, auf natives confirm() zurückfallen
    if (!(window.bootstrap && window.bootstrap.Modal)) {
      var ok = window.confirm('Möchten Sie diesen Eintrag wirklich löschen?');
      if (!ok) {
        e.preventDefault();
        e.stopPropagation();
      }
      return;
    }

    // Bootstrap-Modal verwenden
    e.preventDefault();
    e.stopPropagation();
    ensureModal();
    pendingDeleteHref = href;
    if (modalInstance) {
      modalInstance.show();
    }
  }

  function bindDeleteConfirm(root) {
    var scope = root || document;
    // Titel ist im Template explizit auf "löschen" gesetzt
    var deleteLinks = scope.querySelectorAll('a[title="löschen"]');
    deleteLinks.forEach(function (link) {
      link.removeEventListener('click', onDeleteClick);
      link.addEventListener('click', onDeleteClick);
      // Tastaturbedienung
      link.removeEventListener('keydown', onDeleteKeyDown);
      link.addEventListener('keydown', onDeleteKeyDown);
    });
  }

  function onDeleteKeyDown(e) {
    if (e.key === 'Enter' || e.key === ' ') {
      onDeleteClick(e);
    }
  }

  // Expose Binder in den äußeren Scope, damit obiger DOMContentLoaded darauf zugreifen kann
  if (typeof window.bindDeleteConfirm === 'undefined') {
    window.bindDeleteConfirm = bindDeleteConfirm;
  }
})();
