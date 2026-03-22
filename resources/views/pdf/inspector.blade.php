<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Inspector FM1 — Coordenadas PDF</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg: #0a0a0f; --surface: #12121a; --panel: #16161f;
    --border: rgba(255,255,255,0.07);
    --text: #e8e8f0; --muted: #6b6b85;
    --accent: #6366f1; --rose: #f43f5e; --green: #22c55e; --amber: #f59e0b;
    --sidebar: 295px;
  }
  html, body { height: 100%; overflow: hidden; font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text); }
  .app { display: grid; grid-template-rows: 52px 1fr; grid-template-columns: var(--sidebar) 1fr; height: 100vh; }
  .topbar  { grid-column: 1 / -1; display: flex; align-items: center; gap: 10px; padding: 0 14px; background: var(--surface); border-bottom: 1px solid var(--border); z-index: 10; overflow: hidden; }
  .sidebar { grid-row: 2; overflow-y: auto; background: var(--panel); border-right: 1px solid var(--border); display: flex; flex-direction: column; }
  .canvas-area { grid-row: 2; position: relative; overflow: auto; background: #05050a; }

  /* Topbar */
  .logo { font-weight: 800; font-size: 15px; letter-spacing: -.5px; white-space: nowrap; }
  .logo span { color: var(--accent); }
  .sep { width: 1px; height: 24px; background: var(--border); flex-shrink: 0; }
  .coord-display { font-size: 11px; color: var(--muted); font-family: monospace; white-space: nowrap; }
  .coord-display b { color: var(--text); }
  .form-sel-wrap { display: flex; align-items: center; gap: 7px; flex: 1; min-width: 0; }
  .form-sel-wrap label { font-size: 11px; color: var(--muted); white-space: nowrap; }
  select.fsel { background: var(--surface); border: 1px solid var(--border); color: var(--text); padding: 5px 9px; border-radius: 8px; font-size: 12px; font-family: 'Outfit',sans-serif; cursor: pointer; flex: 1; min-width: 0; }
  select.fsel:focus { outline: none; border-color: var(--accent); }
  .preview-badge { font-size: 11px; padding: 3px 9px; border-radius: 6px; font-weight: 600; white-space: nowrap; }
  .preview-badge.real { background: rgba(34,197,94,.12); color: var(--green); border: 1px solid rgba(34,197,94,.25); }
  .preview-badge.tmpl { background: rgba(107,107,133,.1); color: var(--muted); border: 1px solid var(--border); }
  .btn { display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:8px;font-size:12px;font-weight:600;font-family:'Outfit',sans-serif;cursor:pointer;border:none;transition:all .2s;white-space:nowrap; }
  .btn-primary { background: var(--accent); color: #fff; }
  .btn-primary:hover { background: #4f52d9; }
  .btn-primary:disabled { opacity: .4; cursor: not-allowed; }
  .btn-ghost  { background: transparent; color: var(--muted); border: 1px solid var(--border); }
  .btn-ghost:hover { color: var(--text); border-color: rgba(255,255,255,.2); }
  .dirty-badge { background: var(--amber); color: #000; border-radius:6px; padding:2px 8px; font-size:11px; font-weight:700; display:none; white-space:nowrap; }
  .dirty-badge.show { display:inline-block; }
  .zoom-controls { display:flex;align-items:center;gap:3px; }
  .zbtn { background:var(--surface);border:1px solid var(--border);color:var(--text);width:26px;height:26px;border-radius:7px;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center; }
  .zbtn:hover { border-color:rgba(255,255,255,.2); }
  .zlabel { font-size:11px;color:var(--muted);min-width:36px;text-align:center;font-family:monospace; }

  /* Sidebar */
  .sb-header { padding: 12px 14px 8px; border-bottom: 1px solid var(--border); }
  .sb-header h2 { font-size: 13px; font-weight: 700; }
  .sb-header p  { font-size: 11px; color: var(--muted); margin-top: 2px; }
  .sb-search { padding: 8px 10px; border-bottom: 1px solid var(--border); }
  .sb-search input { width:100%;background:var(--surface);border:1px solid var(--border);color:var(--text);padding:6px 9px;border-radius:8px;font-size:11px;font-family:'Outfit',sans-serif; }
  .sb-search input:focus { outline:none;border-color:var(--accent); }
  .field-list { flex:1;overflow-y:auto;padding:4px 0; }
  .fi { padding:7px 12px;cursor:pointer;border-left:3px solid transparent;transition:all .15s;display:flex;align-items:center;gap:7px; }
  .fi:hover { background: rgba(255,255,255,.04); }
  .fi.active { background: rgba(99,102,241,.1); border-left-color: var(--accent); }
  .fi.dirty  { border-left-color: var(--amber); }
  .fi-dot { width:7px;height:7px;border-radius:50%;background:var(--accent);flex-shrink:0; }
  .fi.dirty .fi-dot { background: var(--amber); }
  .fi-name  { font-size:12px;font-weight:600;line-height:1.2; }
  .fi-val   { font-size:10px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px; }
  .fi-coord { font-size:10px;color:var(--muted);font-family:monospace;margin-left:auto;flex-shrink:0; }

  /* Detail */
  .detail { padding:12px;border-top:1px solid var(--border);background:var(--surface); }
  .detail h3 { font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px; }
  .detail-empty { font-size:12px;color:var(--muted);text-align:center;padding:16px 0; }
  .detail-name { font-size:13px;font-weight:600;margin-bottom:8px; }
  .detail-val { font-size:11px;color:var(--amber);background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:6px;padding:5px 8px;margin-bottom:8px;word-break:break-all; }
  .ci { display:grid;grid-template-columns:1fr 1fr;gap:7px; }
  .ci label { font-size:10px;color:var(--muted);display:block;margin-bottom:3px; }
  .ci input { width:100%;background:var(--bg);border:1px solid var(--border);color:var(--text);padding:6px 8px;border-radius:7px;font-size:13px;font-family:monospace; }
  .ci input:focus { outline:none;border-color:var(--accent); }
  .kbd-hint { font-size:10px;color:var(--muted);margin-top:6px; }
  .kbd-hint kbd { background:rgba(255,255,255,.08);border-radius:4px;padding:1px 4px;font-family:monospace; }

  /* Canvas / overlays */
  #pdf-canvas { display: block; }
  .ov-wrap { position:absolute;top:0;left:0;pointer-events:none; }

  /* Field handle — just a positional marker, no text duplication */
  .fh {
    position: absolute;
    pointer-events: all;
    cursor: grab;
    user-select: none;
    border: 1.5px solid rgba(99,102,241,.55);
    border-radius: 3px;
    background: rgba(99,102,241,.08);
    min-width: 8px;
    min-height: 10px;
    transition: border-color .15s, background .15s;
  }
  .fh:active { cursor: grabbing; }
  .fh:hover  { border-color: var(--accent); background: rgba(99,102,241,.18); z-index: 2; }
  .fh.selected { border-color: var(--accent); background: rgba(99,102,241,.22); box-shadow: 0 0 0 3px rgba(99,102,241,.25); z-index: 3; }
  .fh.dirty-h  { border-color: var(--amber);  background: rgba(245,158,11,.12); }
  .fh.dirty-h.selected { box-shadow: 0 0 0 3px rgba(245,158,11,.25); }

  /* Tooltip on hover/select */
  .fh-tip {
    position:absolute; bottom: calc(100% + 4px); left:0;
    background: rgba(10,10,20,.92); border: 1px solid var(--border);
    color: var(--text); font-family:'Outfit',sans-serif; font-size:10px;
    padding: 3px 8px; border-radius:5px; white-space:nowrap;
    pointer-events:none; display:none; z-index:10;
  }
  .fh:hover .fh-tip, .fh.selected .fh-tip { display:block; }

  /* Loading overlay */
  #pdf-loading {
    position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:12px;background:#05050a;z-index:5;
  }
  .spinner { width:36px;height:36px;border:3px solid rgba(99,102,241,.2);border-top-color:var(--accent);border-radius:50%;animation:spin .8s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
  #pdf-loading p { font-size:13px;color:var(--muted); }

  /* Toast */
  #toast { position:fixed;bottom:22px;left:50%;transform:translateX(-50%) translateY(8px);background:var(--surface);border:1px solid var(--border);color:var(--text);padding:9px 18px;border-radius:12px;font-size:13px;font-weight:600;z-index:9999;opacity:0;transition:all .3s;pointer-events:none;white-space:nowrap; }
  #toast.show { opacity:1;transform:translateX(-50%) translateY(0); }
  #toast.success { border-color:var(--green);color:var(--green); }
  #toast.error   { border-color:var(--rose);color:var(--rose); }

  .no-form-notice { font-size:11px;color:var(--amber);background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);padding:3px 9px;border-radius:6px;white-space:nowrap; }
</style>
</head>
<body>
<div class="app">

  <!-- TOPBAR -->
  <header class="topbar">
    <div class="logo">Inspector <span>FM1</span></div>
    <div class="sep"></div>
    <div class="coord-display">X: <b id="cur-x">—</b> &nbsp;Y: <b id="cur-y">—</b> mm</div>
    <div class="sep"></div>
    <div class="zoom-controls">
      <button class="zbtn" id="zoom-out">−</button>
      <span class="zlabel" id="zoom-label">100%</span>
      <button class="zbtn" id="zoom-in">+</button>
    </div>
    <div class="sep"></div>

    <div class="form-sel-wrap">
      @if($forms->isEmpty())
        <span class="no-form-notice">⚠ No hay formularios guardados. Crea uno primero para usar datos reales.</span>
      @else
        <label>Vista previa con:</label>
        <select class="fsel" id="form-select">
          <option value="">— Plantilla vacía —</option>
          @foreach($forms as $f)
            <option value="{{ $f->id }}">
              #{{ $f->id }} – {{ $f->nombre ?? 'Sin nombre' }}
              @if($f->num_empleado) ({{ $f->num_empleado }})@endif
            </option>
          @endforeach
        </select>
        <span class="preview-badge tmpl" id="preview-badge">Plantilla</span>
      @endif
    </div>

    <div class="sep"></div>
    <span class="dirty-badge" id="dirty-badge">● Sin guardar</span>
    <button class="btn btn-primary" id="save-btn" disabled>Guardar cambios</button>
    <a href="{{ url('/') }}" class="btn btn-ghost">← Dashboard</a>
  </header>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sb-header">
      <h2>Campos del PDF</h2>
      <p id="fields-count">Cargando…</p>
    </div>
    <div class="sb-search">
      <input id="search" placeholder="Buscar campo o valor…" autocomplete="off">
    </div>
    <div class="field-list" id="field-list"></div>
    <div class="detail" id="detail-panel">
      <h3>Campo seleccionado</h3>
      <div class="detail-empty" id="detail-empty">Haz clic en un marcador</div>
      <div id="detail-content" style="display:none">
        <div class="detail-name" id="detail-name"></div>
        <div class="detail-val" id="detail-value" style="display:none"></div>
        <div class="ci">
          <div><label>X (mm)</label><input type="number" id="inp-x" step="0.1"></div>
          <div><label>Y (mm)</label><input type="number" id="inp-y" step="0.1"></div>
        </div>
        <div class="kbd-hint">
          <kbd>↑↓←→</kbd> mueve 0.1 mm &nbsp;·&nbsp; <kbd>Shift</kbd>+flecha = 1 mm
        </div>
      </div>
    </div>
  </aside>

  <!-- CANVAS -->
  <main class="canvas-area" id="canvas-area">
    <div id="canvas-wrap" style="position:relative;display:inline-block">
      <canvas id="pdf-canvas"></canvas>
      <div class="ov-wrap" id="overlay-wrap"></div>
      <div id="pdf-loading">
        <div class="spinner"></div>
        <p id="loading-msg">Cargando PDF…</p>
      </div>
    </div>
  </main>
</div>

<div id="toast"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc =
  'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

const COORDS_URL   = '{{ route("pdf.inspector.coords") }}';
const PREVIEW_URL  = '{{ route("pdf.inspector.preview") }}';
const SAVE_URL     = '{{ route("pdf.inspector.save") }}';
const TEMPLATE_URL = '/fm1_v1_4.pdf';
const CSRF         = document.querySelector('meta[name="csrf-token"]').content;

// ── State ──────────────────────────────────────────────────────────────────
let scale    = 1.4;
let pageW_mm = 216;
let pageH_mm = 279;
let points   = [];
let selected = null;
let dirty    = new Set();
let formId   = '';

const canvas      = document.getElementById('pdf-canvas');
const ctx         = canvas.getContext('2d');
const overlayWrap = document.getElementById('overlay-wrap');
const fieldList   = document.getElementById('field-list');
const loadingEl   = document.getElementById('pdf-loading');
const loadingMsg  = document.getElementById('loading-msg');
const dirtyBadge  = document.getElementById('dirty-badge');
const saveBtn     = document.getElementById('save-btn');
const inpX        = document.getElementById('inp-x');
const inpY        = document.getElementById('inp-y');
const formSel     = document.getElementById('form-select');
const prevBadge   = document.getElementById('preview-badge');

// ── PDF Rendering ──────────────────────────────────────────────────────────
async function renderPDF(source) {
  loadingEl.style.display = 'flex';
  loadingMsg.textContent  = typeof source === 'string'
    ? 'Cargando plantilla…'
    : 'Generando PDF con datos reales…';

  try {
    let pdfDoc;
    if (typeof source === 'string') {
      pdfDoc = await pdfjsLib.getDocument(source).promise;
    } else {
      // ArrayBuffer del PDF generado server-side
      pdfDoc = await pdfjsLib.getDocument({ data: source }).promise;
    }

    const page = await pdfDoc.getPage(1);
    const vp   = page.getViewport({ scale });
    canvas.width  = vp.width;
    canvas.height = vp.height;
    overlayWrap.style.width  = vp.width  + 'px';
    overlayWrap.style.height = vp.height + 'px';

    // Tamaño real en mm
    const rawVp = page.getViewport({ scale: 1 });
    pageW_mm = rawVp.width  * 25.4 / 72;
    pageH_mm = rawVp.height * 25.4 / 72;

    await page.render({ canvasContext: ctx, viewport: vp }).promise;
  } finally {
    loadingEl.style.display = 'none';
  }
}

// ── mm ↔ px ────────────────────────────────────────────────────────────────
const mm2px = (mm, axis) => mm * (axis==='x' ? canvas.width/pageW_mm : canvas.height/pageH_mm);
const px2mm = (px, axis) => px / (axis==='x' ? canvas.width/pageW_mm : canvas.height/pageH_mm);

// ── Cursor coordinates ─────────────────────────────────────────────────────
document.getElementById('canvas-area').addEventListener('mousemove', e => {
  const r = canvas.getBoundingClientRect();
  const px = e.clientX - r.left + document.getElementById('canvas-area').scrollLeft;
  const py = e.clientY - r.top  + document.getElementById('canvas-area').scrollTop;
  if (px < 0 || py < 0) return;
  document.getElementById('cur-x').textContent = px2mm(px,'x').toFixed(1);
  document.getElementById('cur-y').textContent = px2mm(py,'y').toFixed(1);
});

// ── Render overlays (handles only, no text duplication) ───────────────────
function renderOverlays() {
  overlayWrap.innerHTML = '';
  const fontPx = Math.max(7, scale * 6); // approximate FPDI font size in canvas px

  points.forEach(p => {
    const el = document.createElement('div');
    el.className = 'fh'
      + (p === selected  ? ' selected' : '')
      + (dirty.has(p.id) ? ' dirty-h'  : '');

    const pxX = mm2px(p.x, 'x');
    const pxY = mm2px(p.y, 'y');

    // Estimate text width for handle sizing (rough: ~0.55 * fontSize * charCount)
    const valText  = (p.value !== null && p.value !== undefined && p.value !== '') ? p.value : p.field;
    const estWidth = Math.max(16, Math.min(valText.length * fontPx * 0.55, mm2px(60,'x')));

    el.style.left   = pxX + 'px';
    el.style.top    = pxY + 'px';
    el.style.width  = estWidth + 'px';
    el.style.height = (fontPx * 1.4) + 'px';

    // Tooltip
    const tip = document.createElement('div');
    tip.className = 'fh-tip';
    const valPart = (p.value !== null && p.value !== '') ? ` = "${p.value.substring(0,40)}"` : '';
    tip.textContent = `${p.field}${valPart}  →  (${p.x}, ${p.y})`;
    el.appendChild(tip);

    el.addEventListener('mousedown', e => {
      if (e.button !== 0) return;
      selectPoint(p);
      startDrag(e, p, el);
    });

    overlayWrap.appendChild(el);
    p._el = el;
  });
}

// ── Drag ──────────────────────────────────────────────────────────────────
function startDrag(e, p, el) {
  e.preventDefault();
  const area        = document.getElementById('canvas-area');
  const canvasRect  = canvas.getBoundingClientRect();
  const scrollLeft0 = area.scrollLeft;
  const scrollTop0  = area.scrollTop;
  const startX = e.clientX;
  const startY = e.clientY;
  const origPxX = mm2px(p.x, 'x');
  const origPxY = mm2px(p.y, 'y');

  function onMove(ev) {
    const dx = ev.clientX - startX;
    const dy = ev.clientY - startY;
    // Also account for any scroll that happened during drag
    const dscrollX = area.scrollLeft - scrollLeft0;
    const dscrollY = area.scrollTop  - scrollTop0;
    const newPxX = origPxX + dx + dscrollX;
    const newPxY = origPxY + dy + dscrollY;

    el.style.left = newPxX + 'px';
    el.style.top  = newPxY + 'px';
    p.x = Math.round(px2mm(newPxX, 'x') * 10) / 10;
    p.y = Math.round(px2mm(newPxY, 'y') * 10) / 10;

    // Update tooltip live
    const tip = el.querySelector('.fh-tip');
    if (tip) {
      const valPart = (p.value !== null && p.value !== '') ? ` = "${p.value.substring(0,40)}"` : '';
      tip.textContent = `${p.field}${valPart}  →  (${p.x}, ${p.y})`;
    }

    markDirty(p);
    updateDetail(p);
  }

  function onUp() {
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup',   onUp);
    // After drag, reload the filled PDF to confirm visual position
    if (formId) schedulePreviewReload();
  }

  document.addEventListener('mousemove', onMove);
  document.addEventListener('mouseup',   onUp);
}

// ── Auto-reload preview after drag (debounced) ─────────────────────────────
let previewTimer = null;
function schedulePreviewReload() {
  clearTimeout(previewTimer);
  previewTimer = setTimeout(() => {
    if (formId) loadPreviewPdf(formId, false);
  }, 1500);
}

// ── Keyboard movement ──────────────────────────────────────────────────────
document.addEventListener('keydown', e => {
  if (!selected) return;
  if (['INPUT','TEXTAREA','SELECT'].includes(e.target.tagName)) return;
  const step = e.shiftKey ? 1.0 : 0.1;
  let moved = true;
  if      (e.key === 'ArrowLeft')  selected.x = Math.round((selected.x - step)*10)/10;
  else if (e.key === 'ArrowRight') selected.x = Math.round((selected.x + step)*10)/10;
  else if (e.key === 'ArrowUp')    selected.y = Math.round((selected.y - step)*10)/10;
  else if (e.key === 'ArrowDown')  selected.y = Math.round((selected.y + step)*10)/10;
  else moved = false;
  if (moved) {
    e.preventDefault();
    markDirty(selected);
    updateDetail(selected);
    renderOverlays();
    // Re-select to keep it highlighted
    const p = selected;
    selected = p;
    renderOverlays();
  }
});

// ── Select / Detail ────────────────────────────────────────────────────────
function selectPoint(p) {
  selected = p;
  renderOverlays();
  updateDetail(p);
  const li = fieldList.querySelector(`[data-id="${p.id}"]`);
  if (li) {
    fieldList.querySelectorAll('.fi').forEach(x => x.classList.remove('active'));
    li.classList.add('active');
    li.scrollIntoView({ block: 'nearest' });
  }
}

function updateDetail(p) {
  document.getElementById('detail-empty').style.display   = 'none';
  document.getElementById('detail-content').style.display = 'block';
  document.getElementById('detail-name').textContent = p.field;
  const valEl = document.getElementById('detail-value');
  if (p.value) { valEl.textContent = p.value; valEl.style.display = 'block'; }
  else { valEl.style.display = 'none'; }
  inpX.value = p.x;
  inpY.value = p.y;
}

[inpX, inpY].forEach(inp => inp.addEventListener('input', () => {
  if (!selected) return;
  selected.x = parseFloat(inpX.value) || selected.x;
  selected.y = parseFloat(inpY.value) || selected.y;
  markDirty(selected);
  renderOverlays();
}));

function markDirty(p) {
  dirty.add(p.id);
  saveBtn.disabled = false;
  dirtyBadge.classList.add('show');
  const li = fieldList.querySelector(`[data-id="${p.id}"]`);
  if (li) {
    li.classList.add('dirty');
    li.querySelector('.fi-coord').textContent = `${p.x}, ${p.y}`;
  }
}

// ── Field List ─────────────────────────────────────────────────────────────
function buildFieldList(filter = '') {
  fieldList.innerHTML = '';
  const q = filter.toLowerCase();
  const vis = points.filter(p =>
    p.field.toLowerCase().includes(q) ||
    (p.value  && p.value.toLowerCase().includes(q)) ||
    (p.label  && p.label.toLowerCase().includes(q))
  );
  document.getElementById('fields-count').textContent =
    `${vis.length} campo${vis.length !== 1 ? 's' : ''}`;
  vis.forEach(p => {
    const li = document.createElement('div');
    li.className = 'fi' + (p === selected ? ' active' : '') + (dirty.has(p.id) ? ' dirty' : '');
    li.dataset.id = p.id;
    const dv = (p.value !== null && p.value !== '') ? p.value : '—';
    li.innerHTML = `
      <div class="fi-dot"></div>
      <div style="min-width:0;flex:1">
        <div class="fi-name">${p.field}</div>
        <div class="fi-val" title="${dv}">${dv}</div>
      </div>
      <div class="fi-coord">${p.x}, ${p.y}</div>`;
    li.addEventListener('click', () => selectPoint(p));
    fieldList.appendChild(li);
  });
}
document.getElementById('search').addEventListener('input', e => buildFieldList(e.target.value));

// ── Load preview PDF (generated server-side) ───────────────────────────────
async function loadPreviewPdf(fId, reloadCoords = true) {
  const res = await fetch(`${PREVIEW_URL}?form_id=${fId}`);
  if (!res.ok) {
    toast('Error al generar vista previa', 'error'); return;
  }
  const buffer = await res.arrayBuffer();
  await renderPDF(buffer);
  if (reloadCoords) await loadCoords();
  else renderOverlays();
  if (prevBadge) { prevBadge.textContent = '● PDF real'; prevBadge.className = 'preview-badge real'; }
}

// ── Load template PDF ──────────────────────────────────────────────────────
async function loadTemplatePdf() {
  await renderPDF(TEMPLATE_URL);
  await loadCoords();
  if (prevBadge) { prevBadge.textContent = 'Plantilla'; prevBadge.className = 'preview-badge tmpl'; }
}

// ── Load coordinates ───────────────────────────────────────────────────────
async function loadCoords() {
  const url = formId ? `${COORDS_URL}?form_id=${formId}` : COORDS_URL;
  const res  = await fetch(url);
  if (!res.ok) { toast('Error al cargar coordenadas', 'error'); return; }
  points   = await res.json();
  selected = null;
  dirty.clear();
  saveBtn.disabled = true;
  dirtyBadge.classList.remove('show');
  document.getElementById('detail-empty').style.display   = 'block';
  document.getElementById('detail-content').style.display = 'none';
  buildFieldList(document.getElementById('search').value);
  renderOverlays();
}

// ── Form selector ──────────────────────────────────────────────────────────
if (formSel) {
  formSel.addEventListener('change', async () => {
    if (dirty.size > 0 && !confirm('¿Cambiar formulario? Perderás los cambios sin guardar.')) {
      formSel.value = formId; return;
    }
    formId = formSel.value;
    if (formId) {
      await loadPreviewPdf(formId, true);
    } else {
      await loadTemplatePdf();
    }
  });
}

// ── Save ───────────────────────────────────────────────────────────────────
saveBtn.addEventListener('click', async () => {
  const payload = [...dirty].map(id => {
    const p = points.find(x => x.id === id);
    return { line: p.line, x: p.x, y: p.y };
  });
  saveBtn.disabled = true;
  try {
    const res  = await fetch(SAVE_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    if (data.success) {
      dirty.clear();
      dirtyBadge.classList.remove('show');
      toast(data.message, 'success');
      buildFieldList(document.getElementById('search').value);
      // Reload the real PDF to confirm the saved positions look correct
      if (formId) {
        await loadPreviewPdf(formId, false);
      }
    } else {
      saveBtn.disabled = false;
      toast('❌ ' + (data.message || 'Error al guardar'), 'error');
    }
  } catch (err) {
    saveBtn.disabled = false;
    toast('❌ ' + err.message, 'error');
  }
});

// ── Zoom ──────────────────────────────────────────────────────────────────
const ZOOM_STEPS = [0.5, 0.7, 0.85, 1.0, 1.2, 1.4, 1.6, 2.0, 2.5];
let zoomIdx = ZOOM_STEPS.indexOf(scale);
async function applyZoom(idx) {
  zoomIdx = Math.max(0, Math.min(ZOOM_STEPS.length-1, idx));
  scale   = ZOOM_STEPS[zoomIdx];
  document.getElementById('zoom-label').textContent = Math.round(scale*100)+'%';
  if (formId) {
    const res = await fetch(`${PREVIEW_URL}?form_id=${formId}`);
    const buf = await res.arrayBuffer();
    await renderPDF(buf);
  } else {
    await renderPDF(TEMPLATE_URL);
  }
  renderOverlays();
}
document.getElementById('zoom-in') .addEventListener('click', () => applyZoom(zoomIdx+1));
document.getElementById('zoom-out').addEventListener('click', () => applyZoom(zoomIdx-1));

// ── Toast ──────────────────────────────────────────────────────────────────
let toastTimer;
function toast(msg, type='') {
  const el = document.getElementById('toast');
  el.textContent = msg; el.className = 'show ' + type;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => { el.className = ''; }, 3500);
}

// ── Init ───────────────────────────────────────────────────────────────────
(async () => {
  document.getElementById('zoom-label').textContent = Math.round(scale*100)+'%';
  await loadTemplatePdf();
})();
</script>
</body>
</html>
