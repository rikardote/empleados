<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>FM1 Visual — Llenado directo</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --bg: #0a0a0f; --surface: #12121a; --panel: #16161f;
  --border: rgba(255,255,255,0.07);
  --text: #e8e8f0; --muted: #6b6b85;
  --accent: #6366f1; --green: #22c55e; --amber: #f59e0b; --rose: #f43f5e;
}
html, body { height: 100%; font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text); overflow: hidden; }
.app { display: grid; grid-template-rows: 52px 1fr; height: 100vh; }

/* Topbar */
.topbar { display: flex; align-items: center; gap: 10px; padding: 0 16px; background: var(--surface); border-bottom: 1px solid var(--border); }
.logo { font-weight: 800; font-size: 15px; }
.logo span { color: var(--accent); }
.sep { width: 1px; height: 24px; background: var(--border); }
.zoom-controls { display: flex; align-items: center; gap: 4px; }
.zbtn { background: var(--surface); border: 1px solid var(--border); color: var(--text); width: 28px; height: 28px; border-radius: 7px; cursor: pointer; font-size: 15px; display: flex; align-items: center; justify-content: center; }
.zbtn:hover { border-color: rgba(255,255,255,.2); }
.zlabel { font-size: 12px; color: var(--muted); min-width: 40px; text-align: center; font-family: monospace; }
.progress-bar { flex: 1; height: 4px; background: rgba(255,255,255,.06); border-radius: 2px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, var(--accent), #a855f7); border-radius: 2px; transition: width .3s; }
.section-indicator { font-size: 12px; color: var(--muted); white-space: nowrap; }
.section-indicator b { color: var(--text); }
.btn { display:inline-flex;align-items:center;gap:6px;padding:7px 15px;border-radius:9px;font-size:12px;font-weight:600;font-family:'Outfit',sans-serif;cursor:pointer;border:none;transition:all .2s; }
.btn-primary { background: var(--accent); color: #fff; }
.btn-primary:hover { background: #4f52d9; }
.btn-ghost { background: transparent; color: var(--muted); border: 1px solid var(--border); }
.btn-ghost:hover { color: var(--text); border-color: rgba(255,255,255,.2); }
.btn-green { background: #166534; color: var(--green); border: 1px solid rgba(34,197,94,.3); }
.btn-green:hover { background: #14532d; }
.btn-amber { background: rgba(245,158,11,.12); color: var(--amber); border: 1px solid rgba(245,158,11,.25); }
.btn-amber:hover { background: rgba(245,158,11,.2); }

/* Canvas area */
.canvas-area { overflow: auto; background: #05050a; display: flex; justify-content: center; align-items: flex-start; padding: 20px; }
.pdf-wrap { position: relative; display: inline-block; box-shadow: 0 20px 60px rgba(0,0,0,.6); }
#pdf-canvas { display: block; }
.fields-layer { position: absolute; top: 0; left: 0; pointer-events: none; width: 100%; height: 100%; }

/* Field inputs overlay */
.f {
  position: absolute;
  pointer-events: all;
  background: transparent;
  border: none;
  outline: none;
  font-family: Helvetica, Arial, sans-serif;
  color: #1a1aff;
  line-height: 1;
  padding: 0 1px;
  white-space: nowrap;
  overflow: hidden;
  transition: background .15s, box-shadow .15s;
  appearance: none;
  -webkit-appearance: none;
}
.f:focus {
  background: rgba(99,102,241,.08);
  box-shadow: 0 0 0 1.5px rgba(99,102,241,.5);
  border-radius: 2px;
  outline: none;
  z-index: 10;
}
.f:not(:placeholder-shown):not(:focus) {
  background: transparent;
}
.f.f-select {
  cursor: pointer;
  padding-right: 0;
}
.f::placeholder { color: rgba(99,102,241,.35); font-size: .9em; }

/* Section ghost cards (visual guide) */
.section-guide {
  position: absolute;
  border: 1px dashed rgba(99,102,241,.2);
  border-radius: 4px;
  background: rgba(99,102,241,.02);
  pointer-events: none;
}
.section-guide-label {
  position: absolute;
  top: -14px; left: 4px;
  font-family: 'Outfit', sans-serif;
  font-size: 9px; font-weight: 700;
  color: rgba(99,102,241,.5);
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Loading */
#loading { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; background: #05050a; z-index: 20; }
.spinner { width: 32px; height: 32px; border: 3px solid rgba(99,102,241,.2); border-top-color: var(--accent); border-radius: 50%; animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
#loading p { font-size: 13px; color: var(--muted); }

/* Toast */
#toast { position: fixed; bottom: 22px; left: 50%; transform: translateX(-50%) translateY(6px); background: var(--surface); border: 1px solid var(--border); color: var(--text); padding: 9px 18px; border-radius: 12px; font-size: 13px; font-weight: 600; z-index: 9999; opacity: 0; transition: all .3s; pointer-events: none; }
#toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
#toast.success { border-color: var(--green); color: var(--green); }
#toast.error   { border-color: var(--rose); color: var(--rose); }

/* Sidebar panel (field navigator) */
.sidebar {
  position: fixed; right: 0; top: 52px; bottom: 0;
  width: 260px; background: var(--panel); border-left: 1px solid var(--border);
  display: flex; flex-direction: column;
  transform: translateX(100%); transition: transform .3s;
  z-index: 15;
}
.sidebar.open { transform: translateX(0); }
.sb-head { padding: 14px; border-bottom: 1px solid var(--border); }
.sb-head h2 { font-size: 13px; font-weight: 700; }
.sb-section { padding: 8px 12px; }
.sb-section-title { font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
.sb-field { padding: 5px 8px; border-radius: 7px; cursor: pointer; display: flex; align-items: center; gap: 7px; transition: background .1s; }
.sb-field:hover { background: rgba(255,255,255,.04); }
.sb-field.filled { }
.sb-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--border); flex-shrink: 0; }
.sb-field.filled .sb-dot { background: var(--green); }
.sb-fname { font-size: 11px; flex: 1; truncate; }
.sb-fval  { font-size: 10px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80px; }
</style>
</head>
<body>
<form id="main-form" method="POST" action="{{ route('pdf.process') }}">
@csrf

<div class="app">
  <!-- TOPBAR -->
  <header class="topbar">
    <div class="logo">FM1 <span>Visual</span></div>
    <div class="sep"></div>
    <div class="zoom-controls">
      <button type="button" class="zbtn" id="zoom-out">−</button>
      <span class="zlabel" id="zoom-label">100%</span>
      <button type="button" class="zbtn" id="zoom-in">+</button>
    </div>
    <div class="sep"></div>
    <div class="progress-bar"><div class="progress-fill" id="progress-fill" style="width:0%"></div></div>
    <div class="section-indicator">Completado: <b id="filled-count">0</b> / <b id="total-count">0</b></div>
    <div class="sep"></div>
    <button type="button" class="btn btn-ghost" id="toggle-nav">☰ Campos</button>
    <button type="button" class="btn btn-ghost" onclick="location.href='{{ url('/') }}'">← Salir</button>
    <button type="submit" class="btn btn-primary" id="submit-btn">
      <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      Generar PDF
    </button>
  </header>

  <!-- CANVAS -->
  <div class="canvas-area" id="canvas-area">
    <div class="pdf-wrap" id="pdf-wrap">
      <canvas id="pdf-canvas"></canvas>
      <div class="fields-layer" id="fields-layer">
        <!-- Inputs se inyectan via JS después de saber el scale -->
      </div>
      <div id="loading"><div class="spinner"></div><p>Cargando formulario…</p></div>
    </div>
  </div>
</div>

<!-- Sidebar navigator -->
<div class="sidebar" id="sidebar">
  <div class="sb-head">
    <h2>Navegador de campos</h2>
  </div>
  <div style="flex:1;overflow-y:auto;padding:8px 0" id="sb-body"></div>
</div>

</form><!-- /main-form -->

<div id="toast"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc =
  'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

// ── Existing data (edit mode) ──────────────────────────────────────────────
const EXISTING = @json($selectedForm ? $selectedForm->toArray() : []);

// ── Scale & page dimensions ────────────────────────────────────────────────
let scale    = 1.5;
let pageW_mm = 216;
let pageH_mm = 279;

const canvas = document.getElementById('pdf-canvas');
const layer  = document.getElementById('fields-layer');

const mm2px = (mm, axis) => mm * (axis==='x' ? canvas.width/pageW_mm : canvas.height/pageH_mm);

// ── Field definitions ─────────────────────────────────────────────────────
// Format: { name, label, x, y, w, type, options?, placeholder?, section }
// x, y in mm from top-left (matches Fm1PdfService writeText coords)
// w in mm (estimated field width)
const FIELDS = [
  // ── Sección 1: Datos del Trabajador ──
  { name:'nombre',        label:'Nombre completo',      x:29,    y:34.5, w:80,  type:'text',   placeholder:'Nombre(s) apellido paterno materno', section:'Datos del trabajador' },
  { name:'num_empleado',  label:'Nº Empleado',          x:112,   y:27.5, w:25,  type:'text',   placeholder:'000000',                            section:'Datos del trabajador' },
  { name:'rfc',           label:'RFC',                  x:20,    y:39,   w:35,  type:'text',   placeholder:'RFC',                               section:'Datos del trabajador' },
  { name:'curp',          label:'CURP',                 x:58,    y:39,   w:48,  type:'text',   placeholder:'CURP',                              section:'Datos del trabajador' },
  { name:'sexo',          label:'Sexo',                 x:108,   y:39,   w:12,  type:'select', options:['','M','F'],                            section:'Datos del trabajador' },
  { name:'nacionalidad',  label:'Nacionalidad',         x:78,    y:43,   w:35,  type:'text',   placeholder:'Mexicana',                          section:'Datos del trabajador' },
  { name:'escolaridad',   label:'Escolaridad',          x:30,    y:49,   w:55,  type:'text',   placeholder:'Licenciatura',                      section:'Datos del trabajador' },
  { name:'cedula',        label:'Cédula prof.',         x:93,    y:51,   w:30,  type:'text',   placeholder:'0000000',                           section:'Datos del trabajador' },
  { name:'domicilio',     label:'Domicilio',            x:30,    y:53,   w:108, type:'text',   placeholder:'Calle, número, colonia, municipio', section:'Datos del trabajador' },

  // ── Sección 2: Movimiento ──
  { name:'tipo_mov',              label:'Tipo de movimiento',   x:137,   y:39,   w:55, type:'text', placeholder:'Descripción del movimiento', section:'Movimiento' },
  { name:'cod_tipo_movimiento',   label:'Código movimiento',    x:193,   y:39,   w:10, type:'text', placeholder:'00',                        section:'Movimiento' },
  { name:'fecha_movimiento',      label:'Fecha movimiento',     x:137,   y:59.5, w:30, type:'date', section:'Movimiento' },
  { name:'fecha_final',           label:'Fecha final',          x:173,   y:59.5, w:30, type:'date', section:'Movimiento' },

  // ── Sección 3: Plaza ──
  { name:'codigo_puesto',           label:'Código/nivel puesto',  x:40,    y:71.5, w:65,  type:'text', placeholder:'000-000 / N-S', section:'Datos de la plaza' },
  { name:'denominacion_puesto',     label:'Denominación puesto',  x:110,   y:71.5, w:55,  type:'text', placeholder:'Nombre del puesto', section:'Datos de la plaza' },
  { name:'numero_plaza',            label:'Número de plaza',      x:28,    y:77,   w:35,  type:'text', placeholder:'000000', section:'Datos de la plaza' },
  { name:'tipo_plaza',              label:'Tipo de plaza',        x:175,   y:77,   w:28,  type:'text', placeholder:'BASE', section:'Datos de la plaza' },
  { name:'ocupacion',               label:'Ocupación',            x:28,    y:82,   w:43,  type:'text', placeholder:'Descripción', section:'Datos de la plaza' },
  { name:'estatus_plaza',           label:'Estatus plaza',        x:72,    y:82,   w:30,  type:'text', placeholder:'OCUPADA', section:'Datos de la plaza' },

  // Ubicación
  { name:'unidad_administrativa',             label:'UA clave',          x:53.3,  y:89.8,  w:22, type:'text', placeholder:'Clave', section:'Ubicación' },
  { name:'unidad_administrativa_denominacion',label:'UA denominación',   x:78.5,  y:90.5,  w:75, type:'text', placeholder:'Nombre unidad', section:'Ubicación' },
  { name:'adscripcion',                       label:'Adscripción clave', x:51.4,  y:95.1,  w:24, type:'text', placeholder:'Clave', section:'Ubicación' },
  { name:'adscripcion_denominacion',          label:'Adscripción nombre',x:78.2,  y:95.4,  w:75, type:'text', placeholder:'Nombre', section:'Ubicación' },
  { name:'adscripcion_fisica',                label:'A.Física clave',    x:51.3,  y:99.9,  w:24, type:'text', placeholder:'Clave', section:'Ubicación' },
  { name:'adscripcion_fisica_denominacion',   label:'A.Física nombre',   x:78.5,  y:100.5, w:75, type:'text', placeholder:'Nombre', section:'Ubicación' },
  { name:'servicio',                          label:'Servicio clave',    x:51.0,  y:104.6, w:24, type:'text', placeholder:'Clave', section:'Ubicación' },
  { name:'servicio_denominacion',             label:'Servicio nombre',   x:78.9,  y:104.8, w:75, type:'text', placeholder:'Nombre', section:'Ubicación' },

  // Jornada
  { name:'codigo_turno',             label:'Turno código',     x:22,    y:113,  w:8,  type:'text', placeholder:'T', section:'Jornada' },
  { name:'codigo_turno_descripcion', label:'Turno descripción',x:28,    y:113,  w:70, type:'text', placeholder:'Descripción del turno', section:'Jornada' },
  { name:'jornada',                  label:'Jornada',          x:100,   y:113,  w:28, type:'text', placeholder:'Jornada', section:'Jornada' },
  { name:'horario_codigo',           label:'Horario código',   x:130,   y:113,  w:12, type:'text', placeholder:'H', section:'Jornada' },
  { name:'horario_entrada1',         label:'Entrada 1',        x:146,   y:113,  w:14, type:'text', placeholder:'08:00', section:'Jornada' },
  { name:'horario_salida1',          label:'Salida 1',         x:162,   y:113,  w:14, type:'text', placeholder:'16:00', section:'Jornada' },
  { name:'horario_entrada2',         label:'Entrada 2',        x:176,   y:113,  w:14, type:'text', placeholder:'17:00', section:'Jornada' },
  { name:'horario_salida2',          label:'Salida 2',         x:192,   y:113,  w:14, type:'text', placeholder:'21:00', section:'Jornada' },

  // Observaciones
  { name:'observaciones', label:'Observaciones', x:119.8, y:125, w:85, type:'text', placeholder:'Observaciones', section:'Jornada' },

  // ── Sección 4: Antecedentes ──
  { name:'nombre_ant',     label:'Nombre anterior',     x:28,    y:162,  w:90, type:'text', placeholder:'Nombre anterior', section:'Antecedentes' },
  { name:'num_empleado_ant',label:'Nº Empleado ant.',   x:43,    y:167,  w:25, type:'text', placeholder:'000000', section:'Antecedentes' },
  { name:'cod_movi_ant',   label:'Código mov. ant.',    x:40,    y:172,  w:20, type:'text', placeholder:'00', section:'Antecedentes' },
  { name:'tipo_mov_ant',   label:'Tipo mov. ant.',      x:86.4,  y:171.7,w:45, type:'text', placeholder:'Movimiento anterior', section:'Antecedentes' },
  { name:'fecha_inicio_ant',label:'F. inicio ant.',     x:137,   y:176,  w:30, type:'date', section:'Antecedentes' },
  { name:'fecha_fin_ant',  label:'F. fin ant.',         x:173,   y:176,  w:30, type:'date', section:'Antecedentes' },
  { name:'nombre_trab_ant',label:'Trabajador anterior', x:43,    y:218,  w:120,type:'text', placeholder:'Nombre del trabajador anterior', section:'Antecedentes' },

  // ── Sección 5: Firmas ──
  { name:'titular_area',          label:'Titular del área',         x:7.5,   y:253.5, w:65, type:'text', placeholder:'Nombre', section:'Firmas' },
  { name:'cargo_titular_area',    label:'Cargo titular área',       x:9.1,   y:257.2, w:65, type:'text', placeholder:'Cargo', section:'Firmas' },
  { name:'responsable_admvo',     label:'Responsable Admvo.',       x:75,    y:254.2, w:65, type:'text', placeholder:'Nombre', section:'Firmas' },
  { name:'cargo_responsable_admvo',label:'Cargo resp. admvo.',      x:75,    y:258.5, w:65, type:'text', placeholder:'Cargo', section:'Firmas' },
  { name:'titular_centro',        label:'Titular del centro',       x:142.5, y:253.4, w:65, type:'text', placeholder:'Nombre', section:'Firmas' },
  { name:'cargo_titular_centro',  label:'Cargo titular centro',     x:142.5, y:257,   w:65, type:'text', placeholder:'Cargo', section:'Firmas' },
];

// ── Render PDF ─────────────────────────────────────────────────────────────
async function renderPDF() {
  const pdfDoc = await pdfjsLib.getDocument('/fm1_v1_4.pdf').promise;
  const page   = await pdfDoc.getPage(1);
  const vp     = page.getViewport({ scale });
  canvas.width  = vp.width;
  canvas.height = vp.height;

  const rawVp = page.getViewport({ scale: 1 });
  pageW_mm = rawVp.width  * 25.4 / 72;
  pageH_mm = rawVp.height * 25.4 / 72;

  await page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise;
  document.getElementById('loading').style.display = 'none';
  buildInputs();
  buildSidebar();
  updateProgress();
}

// ── Build inputs ───────────────────────────────────────────────────────────
function buildInputs() {
  layer.innerHTML = '';
  const fontPx = Math.max(8, scale * 5.8); // ≈ matches FPDI 9pt at this scale

  FIELDS.forEach(f => {
    const el = f.type === 'select' ? document.createElement('select') : document.createElement('input');
    el.className = 'f' + (f.type === 'select' ? ' f-select' : '');
    el.name      = f.name;
    el.id        = 'field-' + f.name;

    if (f.type !== 'select') {
      el.type = f.type === 'date' ? 'date' : 'text';
      el.placeholder = f.placeholder || '';
      el.autocomplete = 'off';
    } else {
      f.options.forEach(o => {
        const opt = document.createElement('option');
        opt.value = o; opt.textContent = o || '—';
        el.appendChild(opt);
      });
    }

    // Pre-fill from existing record (edit mode)
    if (EXISTING && EXISTING[f.name] !== undefined && EXISTING[f.name] !== null) {
      const v = EXISTING[f.name];
      // Dates: Laravel returns 'YYYY-MM-DD' strings or datetime strings
      if (f.type === 'date') {
        el.value = v ? String(v).substring(0, 10) : '';
      } else {
        el.value = v;
      }
    }

    // Position & size
    const pxX = mm2px(f.x, 'x');
    const pxY = mm2px(f.y, 'y');
    const pxW = mm2px(f.w, 'x');

    el.style.left     = pxX + 'px';
    el.style.top      = pxY + 'px';
    el.style.width    = pxW + 'px';
    el.style.height   = (fontPx * 1.55) + 'px';
    el.style.fontSize = fontPx + 'px';

    el.addEventListener('input', updateProgress);
    el.addEventListener('focus', () => highlightSbField(f.name));

    layer.appendChild(el);
  });
}

// ── Sidebar ────────────────────────────────────────────────────────────────
function buildSidebar() {
  const body = document.getElementById('sb-body');
  body.innerHTML = '';
  const sections = {};
  FIELDS.forEach(f => {
    if (!sections[f.section]) sections[f.section] = [];
    sections[f.section].push(f);
  });
  Object.entries(sections).forEach(([sec, fields]) => {
    const title = document.createElement('div');
    title.className = 'sb-section-title';
    title.style.padding = '10px 14px 4px';
    title.textContent = sec;
    body.appendChild(title);
    fields.forEach(f => {
      const row = document.createElement('div');
      row.className = 'sb-field';
      row.id = 'sb-' + f.name;
      row.innerHTML = `<div class="sb-dot"></div><div class="sb-fname">${f.label}</div><div class="sb-fval" id="sbv-${f.name}"></div>`;
      row.addEventListener('click', () => {
        const inp = document.getElementById('field-' + f.name);
        if (inp) { inp.scrollIntoView({ behavior:'smooth', block:'center' }); inp.focus(); }
      });
      body.appendChild(row);
    });
  });
}

function highlightSbField(name) {
  document.querySelectorAll('.sb-field').forEach(el => el.style.background = '');
  const row = document.getElementById('sb-' + name);
  if (row) row.style.background = 'rgba(99,102,241,.1)';
}

// ── Progress ───────────────────────────────────────────────────────────────
function updateProgress() {
  let filled = 0;
  FIELDS.forEach(f => {
    const el = document.getElementById('field-' + f.name);
    const v  = el ? el.value.trim() : '';
    const isF = v !== '' && v !== '—';
    if (isF) filled++;
    // Update sidebar dot and value
    const row = document.getElementById('sb-' + f.name);
    const valEl = document.getElementById('sbv-' + f.name);
    if (row) row.classList.toggle('filled', isF);
    if (valEl) valEl.textContent = v ? v.substring(0, 12) : '';
  });
  const total = FIELDS.length;
  document.getElementById('filled-count').textContent = filled;
  document.getElementById('total-count').textContent  = total;
  document.getElementById('progress-fill').style.width = Math.round(filled/total*100) + '%';
}

// ── Zoom ──────────────────────────────────────────────────────────────────
const ZOOM_STEPS = [0.8, 1.0, 1.2, 1.5, 1.8, 2.2, 2.8];
let zoomIdx = ZOOM_STEPS.indexOf(scale);
async function applyZoom(idx) {
  zoomIdx = Math.max(0, Math.min(ZOOM_STEPS.length-1, idx));
  scale   = ZOOM_STEPS[zoomIdx];
  document.getElementById('zoom-label').textContent = Math.round(scale*100) + '%';
  // Save current values
  const vals = {};
  FIELDS.forEach(f => { const el = document.getElementById('field-' + f.name); if(el) vals[f.name] = el.value; });
  layer.innerHTML = '';
  document.getElementById('loading').style.display = 'flex';
  await renderPDF();
  // Restore values
  FIELDS.forEach(f => { const el = document.getElementById('field-' + f.name); if(el && vals[f.name]) el.value = vals[f.name]; });
  updateProgress();
}
document.getElementById('zoom-in') .addEventListener('click', () => applyZoom(zoomIdx+1));
document.getElementById('zoom-out').addEventListener('click', () => applyZoom(zoomIdx-1));

// ── Toggle sidebar ────────────────────────────────────────────────────────
document.getElementById('toggle-nav').addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
});

// ── Toast ──────────────────────────────────────────────────────────────────
let tt;
function toast(msg, type='') {
  const el = document.getElementById('toast');
  el.textContent = msg; el.className = 'show ' + type;
  clearTimeout(tt);
  tt = setTimeout(() => { el.className = ''; }, 3500);
}

// ── Form submit ───────────────────────────────────────────────────────────
document.getElementById('main-form').addEventListener('submit', function(e) {
  // Basic validation: nombre is required
  const nombre = document.getElementById('field-nombre')?.value?.trim();
  if (!nombre) {
    e.preventDefault();
    toast('⚠ El nombre del trabajador es requerido', 'error');
    document.getElementById('field-nombre')?.focus();
    return;
  }
  document.getElementById('submit-btn').disabled = true;
  document.getElementById('submit-btn').textContent = 'Generando PDF…';
});

// ── Init ───────────────────────────────────────────────────────────────────
document.getElementById('zoom-label').textContent = Math.round(scale*100) + '%';
renderPDF();
</script>
</body>
</html>
