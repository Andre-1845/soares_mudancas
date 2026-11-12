/* Soares Mudanças — comportamento do site (sem dependências). */
(function () {
  'use strict';
  var csrf = document.querySelector('meta[name="csrf-token"]').content;

  // Estatística de cliques (WhatsApp, Waze, Maps, telefone) para o painel.
  function track(type, source) {
    try {
      var fd = new FormData();
      fd.append('_token', csrf); fd.append('type', type); fd.append('source', source || '');
      if (navigator.sendBeacon) navigator.sendBeacon('/t', fd);
      else fetch('/t', { method: 'POST', body: fd, keepalive: true });
    } catch (e) {}
  }
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-track]');
    if (el && !el.classList.contains('js-wa-open')) track(el.dataset.track, el.dataset.source);
  });

  // Banner do topo: alterna as fotos quando houver mais de uma.
  var slides = document.querySelectorAll('.hero__slide');
  if (slides.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    var cur = 0;
    setInterval(function () {
      slides[cur].classList.remove('is-on');
      cur = (cur + 1) % slides.length;
      slides[cur].classList.add('is-on');
    }, 6000);
  }

  // Menu mobile
  var btn = document.querySelector('.menu-btn'), menu = document.getElementById('menu');
  if (btn && menu) {
    btn.addEventListener('click', function () {
      var open = menu.classList.toggle('is-open');
      btn.setAttribute('aria-expanded', open);
      btn.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
    });
    menu.addEventListener('click', function (e) {
      if (e.target.closest('a')) { menu.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); }
    });
  }

  // Widget WhatsApp: janela na própria página que inicia a conversa.
  var wa = document.querySelector('.wa');
  if (wa) {
    var panel = wa.querySelector('.wa__panel'), fab = wa.querySelector('.wa__fab'),
        form = wa.querySelector('.wa__form'), text = wa.querySelector('textarea'),
        number = wa.dataset.number, lastSource = 'widget';

    function setOpen(open, source) {
      panel.hidden = !open;
      fab.setAttribute('aria-expanded', open);
      if (open) { lastSource = source || 'widget'; setTimeout(function () { text.focus(); text.select(); }, 50); }
    }
    fab.addEventListener('click', function () { setOpen(panel.hidden, 'widget'); });
    wa.querySelector('.wa__close').addEventListener('click', function () { setOpen(false); fab.focus(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !panel.hidden) setOpen(false); });

    wa.querySelectorAll('.wa__chip').forEach(function (chip) {
      chip.addEventListener('click', function () {
        wa.querySelectorAll('.wa__chip').forEach(function (c) { c.classList.remove('is-on'); });
        chip.classList.add('is-on');
        text.value = 'Olá! ' + chip.textContent.trim() + '.';
        text.focus();
      });
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var msg = text.value.trim() || 'Olá!';
      track('whatsapp_click', lastSource);
      window.open('https://wa.me/' + number + '?text=' + encodeURIComponent(msg), '_blank', 'noopener');
      setOpen(false);
    });
    text.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.requestSubmit(); }
    });

    // Os outros botões de WhatsApp da página abrem a mesma janela (se JS estiver ativo).
    document.querySelectorAll('.js-wa-open').forEach(function (a) {
      a.addEventListener('click', function (e) { e.preventDefault(); setOpen(true, a.dataset.source); });
    });
  }

  // Formulário de orçamento: envia sem recarregar a página nem abrir app de e-mail.
  var lead = document.getElementById('lead-form');
  if (lead && window.fetch) {
    var status = lead.querySelector('.form__status');
    lead.addEventListener('submit', function (e) {
      e.preventDefault();
      lead.querySelectorAll('.err').forEach(function (el) { el.textContent = ''; });
      lead.querySelectorAll('.has-error').forEach(function (el) { el.classList.remove('has-error'); });
      status.className = 'form__status'; status.textContent = '';
      lead.classList.add('is-sending');

      fetch(lead.action, { method: 'POST', body: new FormData(lead), headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) { return r.json().then(function (d) { return { status: r.status, data: d }; }); })
        .then(function (res) {
          if (res.status === 200 && res.data.ok) {
            lead.reset();
            status.className = 'form__status alert alert--ok';
            status.textContent = res.data.message;
            if (window.turnstile) window.turnstile.reset();
          } else if (res.status === 422) {
            var errors = res.data.errors || {}, first = null;
            Object.keys(errors).forEach(function (k) {
              var slot = lead.querySelector('.err[data-for="' + k + '"]');
              if (slot) slot.textContent = errors[k][0];
              var input = lead.querySelector('[name="' + k + '"]');
              if (input) { var f = input.closest('.field'); if (f) f.classList.add('has-error'); first = first || input; }
            });
            status.className = 'form__status alert alert--err';
            status.textContent = 'Confira os campos destacados.';
            if (first) first.focus();
            if (window.turnstile) window.turnstile.reset();
          } else if (res.status === 429) {
            throw new Error('Muitas tentativas. Aguarde um minuto ou fale pelo WhatsApp.');
          } else {
            throw new Error();
          }
        })
        .catch(function (err) {
          status.className = 'form__status alert alert--err';
          status.textContent = (err && err.message) || 'Não foi possível enviar agora. Tente novamente ou fale pelo WhatsApp.';
        })
        .finally(function () { lead.classList.remove('is-sending'); status.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); });
    });
  }
})();
