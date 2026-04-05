// ES6: Copy-to-Clipboard für Teilnehmer Edit Formular
// Kopiert beim Klick auf Buttons mit Klasse .copyButton den Wert des
// zugehörigen Inputs mit Klasse .linkToCopy in die Zwischenablage.

document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('.copyButton');
  if (!buttons.length) return;

  const copyText = async (text) => {
    // Bevorzugt moderne Clipboard API
    if (navigator.clipboard && navigator.clipboard.writeText) {
      await navigator.clipboard.writeText(text);
      return true;
    }
    // Fallback für ältere Browser: temporäres Textarea verwenden
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.style.position = 'absolute';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try {
      const ok = document.execCommand('copy');
      document.body.removeChild(ta);
      return ok;
    } catch (e) {
      document.body.removeChild(ta);
      return false;
    }
  };

  const findAssociatedInput = (btn) => {
    // 1) Suche im gleichen Container nach .linkToCopy
    const container = btn.closest('.joFormWrapper, td, .form-group, div') || btn.parentElement;
    if (container) {
      const inputInContainer = container.querySelector('input.linkToCopy, .linkToCopy');
      if (inputInContainer) return inputInContainer;
    }
    // 2) Direkter vorheriger/benachbarter Knoten
    let sib = btn.previousElementSibling;
    while (sib) {
      if (sib.matches && sib.matches('input.linkToCopy, .linkToCopy')) return sib;
      sib = sib.previousElementSibling;
    }
    // 3) Globaler Fallback: erstes Element mit .linkToCopy
    return document.querySelector('input.linkToCopy, .linkToCopy');
  };

  buttons.forEach((btn) => {
    if (btn.dataset.copyBound === '1') return;
    btn.dataset.copyBound = '1';
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const input = findAssociatedInput(btn);
      if (!input) return;
      const value = (input.value !== undefined) ? input.value : (input.textContent || '').trim();
      if (!value) return;

      const originalHtml = btn.innerHTML;
      try {
        const ok = await copyText(value);
        if (ok) {
          btn.innerHTML = 'kopiert';
        } else {
          btn.innerHTML = 'Fehler';
        }
      } catch (ex) {
        btn.innerHTML = 'Fehler';
      }
      // Nach kurzer Zeit Button-Beschriftung zurücksetzen
      setTimeout(() => {
        btn.innerHTML = originalHtml;
      }, 1500);
    });
  });

  // Bootstrap Confirm-Modal für Delete-Links einrichten
  const ensureConfirmModal = () => {
    let modal = document.getElementById('hfmConfirmModal');
    if (modal) return modal;
    const wrapper = document.createElement('div');
    wrapper.innerHTML = `
<div class="modal fade" id="hfmConfirmModal" tabindex="-1" aria-labelledby="hfmConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hfmConfirmModalLabel">Löschen bestätigen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
      </div>
      <div class="modal-body">
        <p id="hfmConfirmModalMessage">Möchten Sie diesen Datensatz wirklich löschen?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
        <button type="button" class="btn btn-danger" id="hfmConfirmModalOk">Löschen</button>
      </div>
    </div>
  </div>
</div>`;
    const el = wrapper.firstElementChild;
    document.body.appendChild(el);
    return el;
  };

  const attachDeleteConfirm = () => {
    const links = document.querySelectorAll('a.js-confirm-delete');
    if (!links.length) return;

    links.forEach((a) => {
      if (a.dataset.confirmBound === '1') return;
      a.dataset.confirmBound = '1';
      a.addEventListener('click', (e) => {
        const href = a.getAttribute('href');
        if (!href) return;
        try {
          if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            // Fallback auf native confirm
            if (window.confirm('Möchten Sie diesen Datensatz wirklich löschen?')) {
              window.location.href = href;
            }
            e.preventDefault();
            return;
          }
          e.preventDefault();
          const modalEl = ensureConfirmModal();
          const okBtn = modalEl.querySelector('#hfmConfirmModalOk');
          const modal = bootstrap.Modal.getOrCreateInstance(modalEl, {backdrop: 'static'});

          // Vorherige Handler entfernen
          const newOkHandler = () => {
            modal.hide();
            window.location.href = href;
          };
          // setze einmaligen Handler
          okBtn.replaceWith(okBtn.cloneNode(true));
          const okBtnFresh = modalEl.querySelector('#hfmConfirmModalOk');
          okBtnFresh.addEventListener('click', newOkHandler, {once: true});

          modal.show();
        } catch (err) {
          // falls irgendetwas schiefgeht, nativen Confirm verwenden
          if (window.confirm('Möchten Sie diesen Datensatz wirklich löschen?')) {
            window.location.href = href;
          }
          e.preventDefault();
        }
      });
    });
  };

  attachDeleteConfirm();

});
