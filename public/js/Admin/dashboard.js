// public/js/admin/dashboard.js
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", initDashboard);

  function initDashboard() {
    cargarKpisReales();
    cargarGraficasReales();  // ← AQUÍ SE CARGAN TODAS LAS GRÁFICAS
    cargarActividadLideres();
  }

  // ============================================================
  // 1. KPIs reales
  // ============================================================
  function cargarKpisReales() {
    fetch("/admin/dashboard/stats")
      .then(r => r.json())
      .then(s => {
        setText("kpiSemilleros", s.semilleros ?? "--");
        setText("kpiLideres", s.lideres ?? "--");
        setText("kpiAprendices", s.aprendices ?? "--");
        setText("kpiProyectos", s.proyectos ?? "--");
        setText("kpiRecursos", s.recursos ?? "--");
      })
      .catch(console.error);
  }

  function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
  }

  // ============================================================
  // 2. TODAS LAS GRÁFICAS (datos reales)
  // ============================================================
  function cargarGraficasReales() {
    fetch("/admin/dashboard/charts")
      .then(async r => {
        const data = await r.json().catch(() => ({}));
        if (!r.ok || data.error) {
          console.error("/admin/dashboard/charts error:", data.message || r.statusText);
          return {};
        }
        return data;
      })
      .then(d => {
        if (!d) return;
        renderChartAprendices(d.tablaAprendicesSem || []);
        renderChartProyectos(d.tablaProyectosSem || []);
        renderChartEstadoProyectos(d.proyectosEstado || []);
        renderChartTopSemilleros(d.topSemilleros || []);
      })
      .catch(console.error);
  }

  // -----------------------------
  // GRÁFICA 1 — Aprendices por semillero (BARRAS)
  // -----------------------------
  function renderChartAprendices(data) {
    if (!data) return;

    const canvas = document.getElementById("chartAprendicesSem");
    if (!canvas) return;

    new Chart(canvas, {
      type: "bar",
      data: {
        labels: data.map(r => r.semillero),
        datasets: [{
          label: "Aprendices",
          data: data.map(r => r.total_aprendices),
          backgroundColor: "rgba(11, 46, 77, 0.7)",
          borderColor: "rgba(11, 46, 77, 1)",
          borderWidth: 1,
          borderRadius: 6,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });
  }

  // -----------------------------
  // GRÁFICA 2 — Proyectos recientes (DONUT)
  // -----------------------------
  function renderChartProyectos(data) {
    if (!data) return;

    const canvas = document.getElementById("chartProyectosSem");
    if (!canvas) return;

    new Chart(canvas, {
      type: "doughnut",
      data: {
        labels: data.map(r => r.semillero),
        datasets: [{
          data: data.map(r => r.proyectos.length),
          backgroundColor: [
            "#0b2e4d", "#1663b3", "#4a9a78",
            "#f7b538", "#b438f7", "#ff6384"
          ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: "60%",
        plugins: { legend: { position: "bottom" } }
      }
    });
  }

  // -----------------------------
  // GRÁFICA 3 — Estado de proyectos (DONUT)
  // -----------------------------
  function renderChartEstadoProyectos(data) {
    if (!data) return;

    const canvas = document.getElementById("chartEstadoProyectos");
    if (!canvas) return;

    new Chart(canvas, {
      type: "doughnut",
      data: {
        labels: data.map(x => x.estado ?? "Sin estado"),
        datasets: [{
          data: data.map(x => x.total),
          backgroundColor: [
            "#0D6EFD", "#198754", "#FFC107",
            "#DC3545", "#6F42C1"
          ]
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: "bottom" } }
      }
    });
  }

  // -----------------------------
  // GRÁFICA 4 — TOP 5 Semilleros (BARRAS HORIZONTALES)
  // -----------------------------
  function renderChartTopSemilleros(data) {
    if (!data) return;

    const canvas = document.getElementById("chartTopSemilleros");
    if (!canvas) return;

    new Chart(canvas, {
      type: "bar",
      data: {
        labels: data.map(x => x.nombre),
        datasets: [{
          label: "Total proyectos",
          data: data.map(x => x.total_proyectos),
          backgroundColor: "#0b2e4d"
        }]
      },
      options: {
        indexAxis: "y",
        responsive: true,
        plugins: { legend: { display: false } }
      }
    });
  }

  // ============================================================
  // 3. Tabla Actividad Líderes (REAL)
  // ============================================================
  function cargarActividadLideres() {
    fetch("/admin/dashboard/charts")
      .then(async r => {
        const data = await r.json().catch(() => ({}));
        if (!r.ok || data.error) {
          console.error("/admin/dashboard/charts error:", data.message || r.statusText);
          return {};
        }
        return data;
      })
      .then(d => {
        const tbody = document.getElementById("tablaActividadLideres");
        if (!tbody || !d || !Array.isArray(d.actividadLideres)) return;

        tbody.innerHTML = d.actividadLideres.map(l => `
          <tr>
            <td>${l.lider}</td>
            <td>${l.linea ?? '-'}</td>
            <td>${l.last_login ?? '<em>Sin registro</em>'}</td>
            <td>${l.last_login_humano}</td>
          </tr>
        `).join('');
      });
  }

})();
