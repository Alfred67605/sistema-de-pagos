@extends('layouts.app')

@section('title', 'Compra y Venta de Minerales')

@section('content')
<style>
/* ══════════════════════════════════════════════════════════════
   SISTEMA ERP PREMIUM — MÓDULO DE MINERALES v3.0
   Inspirado en: SAP Fiori · Oracle Fusion · Stripe Dashboard
   ══════════════════════════════════════════════════════════════ */

:root {
    --m-bg: #0a0f1e;
    --m-card: rgba(15,23,42,0.7);
    --m-border: rgba(148,163,184,0.08);
    --m-border-hover: rgba(148,163,184,0.18);
    --m-text: #f1f5f9;
    --m-muted: #64748b;
    --m-amber: #f59e0b;
    --m-emerald: #10b981;
    --m-indigo: #6366f1;
    --m-cyan: #06b6d4;
    --m-rose: #f43f5e;
}

/* ─── Base Container ─── */
.m-container { font-family: 'Inter', system-ui, sans-serif; }

/* ─── Cards ─── */
.m-card {
    background: var(--m-card);
    border: 1px solid var(--m-border);
    border-radius: 16px;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    transition: all 0.2s ease;
}
.m-card-hover:hover {
    border-color: var(--m-border-hover);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    transform: translateY(-1px);
}

/* ─── Section Header ─── */
.m-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid var(--m-border);
    background: rgba(255,255,255,0.015);
    border-radius: 16px 16px 0 0;
}

/* ─── Tab Navigation ─── */
.m-tabs {
    display: flex;
    gap: 4px;
    padding: 6px;
    background: rgba(15,23,42,0.8);
    border: 1px solid var(--m-border);
    border-radius: 14px;
    backdrop-filter: blur(12px);
}
.m-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--m-muted);
    border: none;
    cursor: pointer;
    background: transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.m-tab:hover { color: var(--m-text); background: rgba(255,255,255,0.05); }
.m-tab-caja.active    { background: linear-gradient(135deg,#3b82f6,#1d4ed8); color:#fff; box-shadow:0 4px 12px rgba(59,130,246,0.35); }
.m-tab-compras.active { background: linear-gradient(135deg,#f59e0b,#d97706); color:#fff; box-shadow:0 4px 12px rgba(245,158,11,0.35); }
.m-tab-ventas.active  { background: linear-gradient(135deg,#10b981,#059669); color:#fff; box-shadow:0 4px 12px rgba(16,185,129,0.35); }
.m-tab-stock.active   { background: linear-gradient(135deg,#06b6d4,#0891b2); color:#fff; box-shadow:0 4px 12px rgba(6,182,212,0.35); }
.m-tab-reportes.active{ background: linear-gradient(135deg,#6366f1,#4f46e5); color:#fff; box-shadow:0 4px 12px rgba(99,102,241,0.35); }

/* ─── Table Premium ─── */
.m-table { width:100%; border-collapse:collapse; font-size:12px; }
.m-table thead tr {
    background: rgba(15,23,42,0.9);
    border-bottom: 1px solid var(--m-border);
}
.m-table thead th {
    padding: 12px 16px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--m-muted);
}
.m-table tbody tr {
    border-bottom: 1px solid rgba(148,163,184,0.05);
    transition: background 0.15s ease;
}
.m-table tbody tr:hover { background: rgba(255,255,255,0.03); }
.m-table tbody td { padding: 14px 16px; color: var(--m-text); vertical-align: middle; }

/* ─── Badges ─── */
.m-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border: 1px solid transparent;
}
.m-badge-amber  { background:rgba(245,158,11,0.12);  color:#f59e0b;  border-color:rgba(245,158,11,0.25); }
.m-badge-emerald{ background:rgba(16,185,129,0.12);  color:#10b981;  border-color:rgba(16,185,129,0.25); }
.m-badge-rose   { background:rgba(244,63,94,0.12);   color:#f43f5e;  border-color:rgba(244,63,94,0.25); }
.m-badge-cyan   { background:rgba(6,182,212,0.12);   color:#06b6d4;  border-color:rgba(6,182,212,0.25); }
.m-badge-indigo { background:rgba(99,102,241,0.12);  color:#818cf8;  border-color:rgba(99,102,241,0.25); }
.m-badge-slate  { background:rgba(100,116,139,0.12); color:#94a3b8;  border-color:rgba(100,116,139,0.2); }

/* ─── Buttons ─── */
.m-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
}
.m-btn:active { transform: scale(0.97); }
.m-btn-amber  { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; box-shadow:0 4px 12px rgba(245,158,11,0.25); }
.m-btn-amber:hover  { background:linear-gradient(135deg,#d97706,#b45309); }
.m-btn-emerald{ background:linear-gradient(135deg,#10b981,#059669); color:#fff; box-shadow:0 4px 12px rgba(16,185,129,0.25); }
.m-btn-emerald:hover{ background:linear-gradient(135deg,#059669,#047857); }
.m-btn-ghost  { background:rgba(255,255,255,0.05); color:#94a3b8; border:1px solid var(--m-border); }
.m-btn-ghost:hover  { background:rgba(255,255,255,0.09); color:#f1f5f9; }
.m-btn-rose   { background:rgba(244,63,94,0.1); color:#f43f5e; border:1px solid rgba(244,63,94,0.2); }
.m-btn-rose:hover { background:rgba(244,63,94,0.2); }
.m-btn-icon { padding:7px; border-radius:8px; }

/* ─── Form Controls ─── */
.m-label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--m-muted);
    margin-bottom: 6px;
}
.m-input {
    width: 100%;
    padding: 10px 14px;
    background: rgba(15,23,42,0.6);
    border: 1px solid var(--m-border);
    border-radius: 10px;
    color: var(--m-text);
    font-size: 13px;
    font-weight: 500;
    outline: none;
    transition: all 0.15s ease;
    appearance: none;
}
.m-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
.m-input::placeholder { color: #475569; }
.m-input-amber:focus { border-color:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,0.12); }
.m-input-emerald:focus { border-color:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,0.12); }
.m-input option { background:#0f172a; color:#f1f5f9; }

/* ─── KPI Cards ─── */
.m-kpi {
    position: relative;
    overflow: hidden;
    padding: 22px;
    border-radius: 16px;
    color: #fff;
    transition: all 0.25s ease;
}
.m-kpi:hover { transform: translateY(-2px); }
.m-kpi::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -10%;
    width: 120px;
    height: 120px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    pointer-events: none;
}
.m-kpi::after {
    content: '';
    position: absolute;
    bottom: -20%;
    right: 15%;
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
    pointer-events: none;
}

/* ─── Step Badge ─── */
.m-step {
    width: 22px; height: 22px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 900;
    flex-shrink: 0;
}

/* ─── Modal ─── */
.m-modal-overlay {
    position: fixed; inset: 0; z-index: 50;
    display: flex; align-items: center; justify-content: center;
    padding: 16px;
    background: rgba(2,6,23,0.85);
    backdrop-filter: blur(8px);
}
.m-modal {
    width: 100%;
    max-width: 900px;
    background: #0d1527;
    border: 1px solid rgba(148,163,184,0.12);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0,0,0,0.5);
}
.m-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid rgba(148,163,184,0.08);
}
.m-modal-body { max-height: 78vh; overflow-y: auto; }
.m-modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    padding: 16px 24px;
    border-top: 1px solid rgba(148,163,184,0.08);
    background: rgba(15,23,42,0.5);
}
.m-modal-section {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(148,163,184,0.06);
}
.m-modal-section:last-child { border-bottom: none; }

/* ─── Stock progress bar ─── */
.m-stock-bar { height: 5px; background: rgba(255,255,255,0.06); border-radius:99px; overflow:hidden; }
.m-stock-bar-fill { height:100%; border-radius:99px; transition: width 0.5s ease; }

/* ─── Scrollbar ─── */
.m-modal-body::-webkit-scrollbar { width: 5px; }
.m-modal-body::-webkit-scrollbar-track { background: transparent; }
.m-modal-body::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }

/* ─── Animations ─── */
@keyframes fadeIn { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:none; } }
.m-fade-in { animation: fadeIn 0.25s ease forwards; }

/* ─── Responsive ─── */
@media (max-width: 640px) {
    .m-tab span.tab-label { display: none; }
    .m-tab { padding: 10px 12px; }
}

/* ─── Light Theme overrides ─── */
.light-theme .m-card    { background: #ffffff; border-color: #e2e8f0; }
.light-theme .m-tabs    { background: #f8fafc; border-color: #e2e8f0; }
.light-theme .m-tab:hover { background: rgba(0,0,0,0.03); }
.light-theme .m-table thead tr { background: #f8fafc; }
.light-theme .m-table tbody tr:hover { background: rgba(0,0,0,0.02); }
.light-theme .m-input   { background: #f8fafc; border-color: #e2e8f0; color: #0f172a; }
.light-theme .m-input option { background: #ffffff; color: #0f172a; }
.light-theme .m-modal   { background: #ffffff; border-color: #e2e8f0; }
.light-theme .m-modal-header { border-color: #e2e8f0; }
.light-theme .m-modal-section { border-color: #f1f5f9; }
.light-theme .m-modal-footer { background: #f8fafc; border-color: #e2e8f0; }
.light-theme .m-section-header { background: rgba(0,0,0,0.01); }

/* ─── Backwards compat ─── */
.erp-card  { background:var(--m-card,rgba(15,23,42,0.7)); border:1px solid var(--m-border,rgba(148,163,184,0.08)); border-radius:16px; }
.erp-input { width:100%; padding:10px 14px; background:rgba(15,23,42,0.6); border:1px solid rgba(148,163,184,0.08); border-radius:10px; color:#f1f5f9; font-size:13px; outline:none; transition:border-color 0.15s; appearance:none; }
.erp-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.12); }
.erp-input-select { cursor:pointer; }
.erp-input option { background:#0f172a; color:#f1f5f9; }
.erp-label { display:block; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#64748b; margin-bottom:6px; }
.erp-container { font-family:'Inter',system-ui,sans-serif; }
.premium-filter-input { width:100%; padding:10px 14px; background:rgba(15,23,42,0.6); border:1px solid rgba(148,163,184,0.08); border-radius:10px; color:#f1f5f9; font-size:12px; font-weight:500; outline:none; transition:all 0.15s; appearance:none; cursor:pointer; }
.premium-filter-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.12); }
.premium-filter-input option { background:#0f172a; color:#f1f5f9; }
.light-theme .erp-input { background:#f8fafc; border-color:#e2e8f0; color:#0f172a; }
.light-theme .erp-input option { background:#fff; color:#0f172a; }
.light-theme .premium-filter-input { background:#f8fafc; border-color:#e2e8f0; color:#0f172a; }
.light-theme .premium-filter-input option { background:#fff; color:#0f172a; }
</style>

<div class="m-container erp-container space-y-5"
     x-data="{
        activeTab: '{{ request('tab', 'compras') }}',
        openCompraModal: false,
        openVentaModal: false,
        openDetailModal: false,
        openRecargaModal: false,
        isEditMode: false,
        actionUrl: '',

        // Movimientos Caja Filters
        searchMov: '',
        filterTipo: '',
        filterRange: 'all',
        matchesFilter(glosa, monto, tipo, fechaStr) {
            if (this.filterTipo && tipo !== this.filterTipo) return false;
            if (this.searchMov) {
                const q = this.searchMov.toLowerCase();
                if (!glosa.toLowerCase().includes(q) && !String(monto).includes(q)) return false;
            }
            if (this.filterRange === 'today') {
                const today = '{{ now()->toDateString() }}';
                if (fechaStr !== today) return false;
            }
            if (this.filterRange === 'month') {
                const currentMonth = '{{ now()->format('Y-m') }}';
                if (!fechaStr.startsWith(currentMonth)) return false;
            }
            return true;
        },

        // Stock Filters
        searchStock: '',
        filterStockEstado: '',
        matchesStockFilter(loteId, proveedor, presentacion, estado) {
            if (this.filterStockEstado && estado !== this.filterStockEstado) return false;
            if (this.searchStock) {
                const q = this.searchStock.toLowerCase();
                if (!String(loteId).toLowerCase().includes(q) && !proveedor.toLowerCase().includes(q) && !presentacion.toLowerCase().includes(q)) return false;
            }
            return true;
        },

        // Form Compra (Lote) Fields
        compraId: '',
        compraFecha: '{{ now()->toDateString() }}',
        compraProveedor: '',
        compraBocaminaId: '',
        compraPresentacion: 'Sacos',
        compraPresentacionOtro: '',
        compraCantidad: '',
        compraPeso: '',
        compraPrecio: '',
        compraPesoToneladas: '',
        compraPrecioToneladas: '',
        compraTotal: 0,
        compraObservacion: '',
        compraAnalisis: [],

        // Form Venta Fields
        ventaId: '',
        ventaFecha: '{{ now()->toDateString() }}',
        ventaCliente: '',
        ventaDestino: '',
        ventaObservacion: '',
        ventaLotes: [],

        ventaLoteId: '',
        ventaCantidad: '',
        ventaPeso: '',
        ventaPrecio: '',
        ventaTotal: 0,
        loteInfo: null,
        fichaLote: null,

        // Methods Compra
        initCompra() {
            this.isEditMode = false;
            this.actionUrl = '{{ route('transacciones-minerales.store') }}';
            this.compraId = '';
            this.compraFecha = '{{ now()->toDateString() }}';
            this.compraProveedor = '';
            this.compraBocaminaId = '';
            this.compraPresentacion = 'Sacos';
            this.compraPresentacionOtro = '';
            this.compraCantidad = '';
            this.compraPesoBruto = '';
            this.compraPrecio = '';
            this.compraPesoToneladas = '';
            this.compraPrecioToneladas = '';
            this.compraHumedadPorcentaje = 0;
            this.compraDescuentoHumedadPeso = 0;
            this.compraDescuentoHumedadDinero = 0;
            this.compraSubtotalBruto = 0;
            this.compraPesoNetoSeco = 0;
            this.compraTotal = 0;
            this.compraObservacion = '';
            this.compraAnalisis = [
                { mineral: 'Zinc', mineral_custom: '', ley: 48.50 },
                { mineral: 'Plomo', mineral_custom: '', ley: 12.30 },
                { mineral: 'Plata', mineral_custom: '', ley: 1.80 }
            ];
            this.openCompraModal = true;
        },

        editCompra(item) {
            this.isEditMode = true;
            this.actionUrl = '/transacciones-minerales/' + item.id;
            this.compraId = item.id;
            this.compraFecha = item.fecha.split('T')[0];
            this.compraProveedor = item.cliente_proveedor;
            this.compraBocaminaId = item.bocamina_id || '';
            this.compraPresentacion = item.presentacion;
            this.compraPresentacionOtro = item.presentacion_otro || '';
            this.compraCantidad = item.cantidad;
            this.compraPesoBruto = item.peso_bruto || item.peso_neto_seco;
            this.compraPrecio = item.precio_unidad;
            this.compraHumedadPorcentaje = item.humedad_porcentaje !== null ? item.humedad_porcentaje : 0;

            if (item.presentacion === 'Volqueta') {
                let pBruto = parseFloat(item.peso_bruto || item.peso_neto_seco);
                this.compraPesoToneladas = (pBruto / 1000).toFixed(3);
                this.compraPrecioToneladas = (parseFloat(item.precio_unidad) * 1000).toFixed(2);
            } else {
                this.compraPesoToneladas = '';
                this.compraPrecioToneladas = '';
            }

            this.calcCompraTotal();
            this.compraObservacion = item.observacion || '';

            const stdMinerals = ['Zinc', 'Plomo', 'Plata', 'Cobre', 'Estaño'];
            this.compraAnalisis = item.analisis.map(a => {
                const isStd = stdMinerals.includes(a.mineral);
                return {
                    mineral: isStd ? a.mineral : 'Otro',
                    mineral_custom: isStd ? '' : a.mineral,
                    ley: parseFloat(a.ley)
                };
            });
            this.openCompraModal = true;
        },

        addMineral() { this.compraAnalisis.push({ mineral: '', mineral_custom: '', ley: '' }); },
        removeMineral(index) { this.compraAnalisis.splice(index, 1); },

        get calcPesoBruto() {
            if (this.compraPresentacion === 'Volqueta') {
                let valT = parseFloat(this.compraPesoToneladas) || 0;
                return valT > 500 ? valT : valT * 1000;
            }
            return parseFloat(this.compraPesoBruto) || 0;
        },

        get calcPrecioUnit() {
            if (this.compraPresentacion === 'Volqueta') {
                let prT = parseFloat(this.compraPrecioToneladas) || 0;
                return prT / 1000;
            }
            return parseFloat(this.compraPrecio) || 0;
        },

        get calcHumedadPct() {
            let h = parseFloat(this.compraHumedadPorcentaje);
            if (isNaN(h) || h < 0) return 0;
            if (h > 100) return 100;
            return h;
        },

        get calcDescuentoPeso() {
            return 0;
        },

        get calcPesoNetoSeco() {
            return this.calcPesoBruto;
        },

        get calcSubtotalBruto() {
            return this.calcPesoBruto * this.calcPrecioUnit;
        },

        get calcDescuentoDinero() {
            return this.calcSubtotalBruto * (this.calcHumedadPct / 100);
        },

        get calcTotalLiquidado() {
            return this.calcSubtotalBruto - this.calcDescuentoDinero;
        },

        calcCompraTotal() {
            this.compraDescuentoHumedadPeso = this.calcDescuentoPeso.toFixed(2);
            this.compraPesoNetoSeco = this.calcPesoNetoSeco.toFixed(2);
            this.compraSubtotalBruto = this.calcSubtotalBruto.toFixed(2);
            this.compraDescuentoHumedadDinero = this.calcDescuentoDinero.toFixed(2);
            this.compraTotal = this.calcTotalLiquidado.toFixed(2);
        },

        numberFormat(n) {
            let num = parseFloat(n) || 0;
            return num.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        // Methods Venta
        initVenta() {
            this.isEditMode = false;
            this.actionUrl = '{{ route('transacciones-minerales.store') }}';
            this.ventaId = '';
            this.ventaFecha = '{{ now()->toDateString() }}';
            this.ventaCliente = '';
            this.ventaDestino = '';
            this.ventaObservacion = '';
            this.ventaHumedadPorcentaje = 0;
            this.ventaLotes = [{ lote_id: '', cantidad: '', peso_neto_seco: '', precio_unidad: '', humedad_porcentaje: 0, monto_total: 0, info: null, analisis: [] }];
            this.openVentaModal = true;
        },

        editVenta(item) {
            this.isEditMode = true;
            this.actionUrl = '/transacciones-minerales/' + item.id;
            this.ventaId = item.id;
            this.ventaFecha = item.fecha.split('T')[0];
            this.ventaCliente = item.cliente_proveedor;
            this.ventaDestino = item.destino || '';
            this.ventaLoteId = item.lote_id;
            this.ventaCantidad = item.cantidad;
            this.ventaPeso = item.peso_neto_seco;
            this.ventaPrecio = item.precio_unidad;
            this.ventaHumedadPorcentaje = item.humedad_porcentaje !== null ? item.humedad_porcentaje : 0;
            this.ventaTotal = item.monto_total;
            this.ventaObservacion = item.observacion || '';
            if (item.lote) {
                this.loteInfo = {
                    presentacion: item.lote.presentacion,
                    presentacion_otro: item.lote.presentacion_otro,
                    peso_disponible: parseFloat(item.lote.peso_disponible) + parseFloat(item.peso_neto_seco),
                    cantidad_disponible: parseFloat(item.lote.cantidad_disponible) + parseFloat(item.cantidad),
                    analisis: item.lote.analisis
                };
            }
            this.openVentaModal = true;
        },

        addVentaLote() {
            this.ventaLotes.push({ lote_id: '', cantidad: '', peso_neto_seco: '', precio_unidad: '', humedad_porcentaje: 0, monto_total: 0, info: null, analisis: [] });
        },
        removeVentaLote(index) { this.ventaLotes.splice(index, 1); },

        addVentaAnalisis(index) {
            if (!this.ventaLotes[index].analisis) this.ventaLotes[index].analisis = [];
            this.ventaLotes[index].analisis.push({ mineral: '', ley: '' });
        },
        removeVentaAnalisis(index, anIdx) {
            if (this.ventaLotes[index].analisis) {
                this.ventaLotes[index].analisis.splice(anIdx, 1);
            }
        },

        onVentaLoteSelected(index) {
            const lotId = this.ventaLotes[index].lote_id;
            if (!lotId) {
                this.ventaLotes[index].info = null;
                this.ventaLotes[index].analisis = [];
                return;
            }
            fetch('/transacciones-minerales/' + lotId)
                .then(r => r.json())
                .then(data => {
                    this.ventaLotes[index].info = {
                        presentacion: data.presentacion,
                        presentacion_otro: data.presentacion_otro,
                        peso_disponible: parseFloat(data.peso_disponible),
                        cantidad_disponible: parseFloat(data.cantidad_disponible),
                        analisis: data.analisis
                    };

                    // Auto-fill disponible values as defaults
                    this.ventaLotes[index].peso_neto_seco = parseFloat(data.peso_disponible).toFixed(2);
                    this.ventaLotes[index].cantidad = parseFloat(data.cantidad_disponible).toFixed(2);
                    this.ventaLotes[index].humedad_porcentaje = data.humedad_porcentaje || 0;
                    if (data.precio_unidad) {
                        this.ventaLotes[index].precio_unidad = parseFloat(data.precio_unidad).toFixed(2);
                    }

                    if (data.presentacion === 'Volqueta') {
                        this.ventaLotes[index].pesoToneladas = (parseFloat(data.peso_disponible) / 1000).toFixed(3);
                        if (data.precio_unidad) {
                            this.ventaLotes[index].precioToneladas = (parseFloat(data.precio_unidad) * 1000).toFixed(2);
                        }
                    } else {
                        this.ventaLotes[index].pesoToneladas = '';
                        this.ventaLotes[index].precioToneladas = '';
                    }

                    // Copy editable lab analysis for the sale
                    this.ventaLotes[index].analisis = (data.analisis || []).map(a => ({
                        mineral: a.mineral,
                        ley: parseFloat(a.ley)
                    }));

                    this.calcVentaItemTotal(index);
                });
        },

        calcVentaItemTotal(index) {
            const item = this.ventaLotes[index];
            let bruto = 0;
            if (item.info && item.info.presentacion === 'Volqueta') {
                let t = parseFloat(item.pesoToneladas) || 0;
                let prT = parseFloat(item.precioToneladas) || 0;
                item.peso_neto_seco = (t * 1000).toFixed(2);
                item.precio_unidad = (prT / 1000).toFixed(4);
                bruto = t * prT;
            } else {
                let p = parseFloat(item.peso_neto_seco) || 0;
                let pr = parseFloat(item.precio_unidad) || 0;
                bruto = p * pr;
            }
            let h = parseFloat(item.humedad_porcentaje) || 0;
            if (h < 0) h = 0; if (h > 100) h = 100;
            item.monto_total = (bruto - (bruto * (h/100))).toFixed(2);
        },
        calcVentaTotal() {
            let p = parseFloat(this.ventaPeso) || 0;
            let pr = parseFloat(this.ventaPrecio) || 0;
            let h = parseFloat(this.ventaHumedadPorcentaje) || 0;
            if (h < 0) h = 0; if (h > 100) h = 100;
            let bruto = p * pr;
            this.ventaTotal = (bruto - (bruto * (h/100))).toFixed(2);
        },

        showFicha(id) {
            fetch('/transacciones-minerales/' + id)
                .then(r => r.json())
                .then(data => { this.fichaLote = data; this.openDetailModal = true; });
        }
     }"
     x-init="
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('create') === '1' || urlParams.get('open_modal') === '1') {
            if (activeTab === 'compras') initCompra();
            if (activeTab === 'ventas') initVenta();
        }
     ">

    {{-- ══════════ HEADER PREMIUM ══════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg"
                 style="background:linear-gradient(135deg,#6366f1,#4338ca);">
                <i class="fa-solid fa-gem text-white text-base"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-black text-slate-100 leading-tight">
                        <span x-show="activeTab === 'caja'">Caja del Módulo de Minerales</span>
                        <span x-show="activeTab === 'compras'">Compras — Ingreso de Lotes</span>
                        <span x-show="activeTab === 'ventas'">Ventas — Despacho de Mineral</span>
                        <span x-show="activeTab === 'stock'">Stock e Inventario</span>
                        <span x-show="activeTab === 'reportes'">Reportes de Comercialización</span>
                    </h1>
                    <span class="m-badge m-badge-indigo text-[9px]">Módulo 2</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    <span x-show="activeTab === 'caja'">Control financiero operativo del módulo de minerales</span>
                    <span x-show="activeTab === 'compras'">Registra compras, genera lotes y análisis de laboratorio</span>
                    <span x-show="activeTab === 'ventas'">Despacha mineral disponible en almacén a clientes</span>
                    <span x-show="activeTab === 'stock'">Consulta disponibilidad, peso y valor de cada lote</span>
                    <span x-show="activeTab === 'reportes'">Analiza volumen, ingresos, egresos y balances</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════ ERRORES ══════════ --}}
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/25 text-rose-400 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $err)
                <div class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- ══════════ TAB BAR ══════════ --}}
    <div class="m-tabs">
        <button class="m-tab m-tab-caja" :class="activeTab === 'caja' ? 'active' : ''" @click="activeTab='caja'">
            <i class="fa-solid fa-vault text-sm"></i>
            <span class="tab-label">Caja</span>
        </button>
        <button class="m-tab m-tab-compras" :class="activeTab === 'compras' ? 'active' : ''" @click="activeTab='compras'">
            <i class="fa-solid fa-boxes-stacked text-sm"></i>
            <span class="tab-label">Compras</span>
        </button>
        <button class="m-tab m-tab-ventas" :class="activeTab === 'ventas' ? 'active' : ''" @click="activeTab='ventas'">
            <i class="fa-solid fa-truck-loading text-sm"></i>
            <span class="tab-label">Ventas</span>
        </button>
        <button class="m-tab m-tab-stock" :class="activeTab === 'stock' ? 'active' : ''" @click="activeTab='stock'">
            <i class="fa-solid fa-warehouse text-sm"></i>
            <span class="tab-label">Stock</span>
        </button>
        <button class="m-tab m-tab-reportes" :class="activeTab === 'reportes' ? 'active' : ''" @click="activeTab='reportes'">
            <i class="fa-solid fa-chart-column text-sm"></i>
            <span class="tab-label">Reportes</span>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════
         TAB 0: CAJA DEL MÓDULO
    ══════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'caja'" class="space-y-5 m-fade-in">

        {{-- 4 KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="m-kpi shadow-xl border border-cyan-400/20" style="background:linear-gradient(135deg,#0ea5e9,#1d4ed8)">
                <div class="flex items-center justify-between mb-3 relative z-10">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-white/80 flex items-center gap-1.5">
                        <i class="fa-solid fa-crown text-amber-300"></i> Saldo Actual
                    </span>
                    <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center"><i class="fa-solid fa-wallet"></i></div>
                </div>
                <div class="text-2xl font-black font-mono relative z-10">Bs. {{ number_format($saldoCajaModulo, 2) }}</div>
                <div class="mt-2 text-[10px] text-white/70 font-semibold relative z-10">
                    <i class="fa-solid {{ $saldoCajaModulo >= 0 ? 'fa-circle-check text-emerald-300' : 'fa-circle-exclamation text-rose-300' }} mr-1"></i>
                    {{ $saldoCajaModulo >= 0 ? 'Efectivo disponible' : 'Caja en déficit' }}
                </div>
            </div>
            <div class="m-kpi shadow-xl border border-indigo-400/20" style="background:linear-gradient(135deg,#6366f1,#4338ca)">
                <div class="flex items-center justify-between mb-3 relative z-10">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-white/80">Total Recargado</span>
                    <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                </div>
                <div class="text-2xl font-black font-mono relative z-10">Bs. {{ number_format($totalRecargadoCaja, 2) }}</div>
                <div class="mt-2 text-[10px] text-white/70 font-semibold relative z-10">Capital de control inyectado</div>
            </div>
            <div class="m-kpi shadow-xl border border-rose-400/20" style="background:linear-gradient(135deg,#f43f5e,#be123c)">
                <div class="flex items-center justify-between mb-3 relative z-10">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-white/80">Total Compras</span>
                    <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center"><i class="fa-solid fa-cart-shopping"></i></div>
                </div>
                <div class="text-2xl font-black font-mono relative z-10">Bs. {{ number_format($totalComprasModulo, 2) }}</div>
                <div class="mt-2 text-[10px] text-white/70 font-semibold relative z-10">Egresos por lotes ingresados</div>
            </div>
            <div class="m-kpi shadow-xl border border-emerald-400/20" style="background:linear-gradient(135deg,#10b981,#047857)">
                <div class="flex items-center justify-between mb-3 relative z-10">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-white/80">Total Ventas</span>
                    <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center"><i class="fa-solid fa-chart-line-up"></i></div>
                </div>
                <div class="text-2xl font-black font-mono relative z-10">Bs. {{ number_format($totalVentasModulo, 2) }}</div>
                <div class="mt-2 text-[10px] text-white/70 font-semibold relative z-10">Ingresos por venta de mineral</div>
            </div>
        </div>

        {{-- Gauge --}}
        @php
            $barColor = $porcentajeUsoCaja >= 90 ? 'from-rose-500 to-rose-600' : ($porcentajeUsoCaja >= 70 ? 'from-amber-500 to-orange-500' : 'from-emerald-500 to-teal-500');
            $txtColor = $porcentajeUsoCaja >= 90 ? 'text-rose-400' : ($porcentajeUsoCaja >= 70 ? 'text-amber-400' : 'text-emerald-400');
            $badgeStatus = $porcentajeUsoCaja >= 90 ? '🔴 Crítico' : ($porcentajeUsoCaja >= 70 ? '🟠 Moderado' : '🟢 Saludable');
            $fondosTotales = $totalRecargadoCaja + $totalVentasModulo;
        @endphp
        <div class="m-card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/25 flex items-center justify-center text-amber-400">
                        <i class="fa-solid fa-gauge-high text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-200">Indicador de Utilización de Fondos</h3>
                        <p class="text-[10px] text-slate-500">Proporción de capital gastado en compras vs fondos totales</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="m-badge {{ $porcentajeUsoCaja >= 90 ? 'm-badge-rose' : ($porcentajeUsoCaja >= 70 ? 'm-badge-amber' : 'm-badge-emerald') }}">{{ $badgeStatus }}</span>
                    <span class="text-lg font-black font-mono {{ $txtColor }}">{{ $porcentajeUsoCaja }}%</span>
                </div>
            </div>
            <div class="h-2.5 w-full bg-slate-900 rounded-full overflow-hidden border border-slate-800">
                <div class="h-full bg-gradient-to-r {{ $barColor }} rounded-full transition-all duration-700" style="width:{{ $porcentajeUsoCaja }}%"></div>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-slate-800/60">
                <div class="text-[11px] text-slate-400 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>Disponible: <strong class="text-emerald-400 font-mono">Bs.{{ number_format($saldoCajaModulo,2) }}</strong></div>
                <div class="text-[11px] text-slate-400 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span>Utilizado: <strong class="text-rose-400 font-mono">Bs.{{ number_format($totalComprasModulo,2) }}</strong></div>
                <div class="text-[11px] text-slate-400 flex items-center gap-1.5 sm:justify-end"><span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>Total Fondos: <strong class="text-indigo-400 font-mono">Bs.{{ number_format($fondosTotales,2) }}</strong></div>
            </div>
        </div>

        {{-- Grid: Recarga + Historial --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Recarga Form --}}
            <div class="m-card overflow-hidden border border-amber-500/15">
                <div class="m-section-header" style="background:rgba(245,158,11,0.05);border-color:rgba(245,158,11,0.1)">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                            <i class="fa-solid fa-plus-circle text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-200">Inyectar a Caja</h3>
                            <p class="text-[10px] text-slate-500">Registrar recarga operativa</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('caja-minerales.store-recarga') }}" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="m-label"><i class="fa-regular fa-calendar mr-1 text-amber-500"></i>Fecha de Inyección</label>
                        <input name="fecha" type="date" required value="{{ now()->toDateString() }}" class="m-input m-input-amber font-mono">
                    </div>
                    <div>
                        <label class="m-label" style="color:#f59e0b"><i class="fa-solid fa-coins mr-1"></i>Monto a Recargar (Bs.)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-black text-amber-400 font-mono pointer-events-none">Bs.</span>
                            <input name="monto" type="number" step="0.01" required min="0.01" placeholder="0.00" class="m-input m-input-amber !pl-11 text-base font-black">
                        </div>
                    </div>
                    <div>
                        <label class="m-label"><i class="fa-solid fa-note-sticky mr-1 text-amber-500"></i>Concepto / Origen</label>
                        <textarea name="observacion" rows="2" placeholder="Ej. Inyección de capital operativo..." class="m-input text-xs resize-none"></textarea>
                    </div>
                    <button type="submit" class="m-btn m-btn-amber w-full justify-center py-3">
                        <i class="fa-solid fa-check-circle"></i> Confirmar Recarga
                    </button>
                    <div class="flex gap-2 bg-slate-900/50 border border-slate-800 rounded-xl px-3 py-2.5 text-[10px] text-slate-500">
                        <i class="fa-solid fa-shield-halved text-amber-500 flex-shrink-0 mt-0.5"></i>
                        <span>Las recargas aumentan el saldo disponible en tiempo real y quedan en el libro diario.</span>
                    </div>
                </form>
            </div>

            {{-- Historial --}}
            <div class="m-card overflow-hidden lg:col-span-2">
                <div class="m-section-header">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/25 flex items-center justify-center text-indigo-400">
                            <i class="fa-solid fa-book-journal-whills text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-200">Libro Diario de Caja</h3>
                            <p class="text-[10px] text-slate-500">Historial completo de Recargas, Compras y Ventas</p>
                        </div>
                    </div>
                    <span class="m-badge m-badge-indigo">{{ count($movimientosCaja) }} registros</span>
                </div>

                <div class="px-5 py-4 border-b border-slate-800/50 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-500"></i>
                        <input type="text" x-model="searchMov" placeholder="Buscar..." class="m-input !pl-8 text-xs">
                    </div>
                    <select x-model="filterTipo" class="m-input text-xs">
                        <option value="">Todos los tipos</option>
                        <option value="recarga">📥 Recargas</option>
                        <option value="compra">🛒 Compras</option>
                        <option value="venta">📈 Ventas</option>
                    </select>
                    <select x-model="filterRange" class="m-input text-xs">
                        <option value="all">Todas las fechas</option>
                        <option value="today">Hoy</option>
                        <option value="month">Este Mes</option>
                    </select>
                </div>

                @if(count($movimientosCaja) === 0)
                    <div class="py-16 text-center text-slate-600">
                        <i class="fa-solid fa-vault text-4xl block mb-3 opacity-30"></i>
                        <p class="text-sm text-slate-500">Sin movimientos registrados aún</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="m-table">
                            <thead><tr>
                                <th class="text-center w-10">#</th>
                                <th>Fecha</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-right">Monto</th>
                                <th class="text-right">Saldo</th>
                                <th>Concepto</th>
                                <th class="text-center w-16"></th>
                            </tr></thead>
                            <tbody>
                                @foreach($movimientosCaja as $i => $mov)
                                <tr x-show="matchesFilter('{{ str_replace("'", "\'", $mov->glosa) }}','{{ number_format($mov->monto,2) }}','{{ $mov->tipo }}','{{ $mov->fecha->format('Y-m-d') }}')">
                                    <td class="text-center font-mono text-slate-600 font-bold text-[10px]">{{ str_pad(count($movimientosCaja) - $i, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="font-mono font-bold text-slate-300 whitespace-nowrap text-xs">{{ $mov->fecha->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        @if($mov->tipo === 'recarga')
                                            <span class="m-badge m-badge-cyan">📥 Recarga</span>
                                        @elseif($mov->tipo === 'venta')
                                            <span class="m-badge m-badge-emerald">📈 Venta</span>
                                        @else
                                            <span class="m-badge m-badge-rose">🛒 Compra</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-mono font-black text-xs whitespace-nowrap {{ $mov->es_ingreso ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $mov->es_ingreso ? '+' : '-' }}Bs. {{ number_format($mov->monto, 2) }}
                                    </td>
                                    <td class="text-right font-mono font-black text-xs whitespace-nowrap {{ $mov->saldo_resultante >= 0 ? 'text-amber-400' : 'text-rose-400' }}">
                                        Bs. {{ number_format($mov->saldo_resultante, 2) }}
                                    </td>
                                    <td class="text-slate-400 text-xs">{{ $mov->glosa }}</td>
                                    <td class="text-center">
                                        @if($mov->delete_route)
                                            <form action="{{ $mov->delete_route }}" method="POST" onsubmit="return confirm('¿Eliminar esta recarga?');" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="m-btn m-btn-rose m-btn-icon cursor-pointer">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] text-slate-700 font-mono">Auto</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TAB 1: COMPRAS (LOTES DE MINERAL)
    ══════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'compras'" class="m-fade-in">
        <div class="m-card overflow-hidden">
            <div class="m-section-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/25 flex items-center justify-center text-amber-400">
                        <i class="fa-solid fa-boxes-stacked text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-200">Lotes Ingresados al Almacén</h3>
                        <p class="text-[10px] text-slate-500">Historial de minerales comprados · Haz clic en el ID para ver la Ficha Técnica</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="m-badge m-badge-amber">{{ count($transacciones->where('tipo', 'compra')) }} lotes</span>
                    <button @click="initCompra()" type="button" class="m-btn m-btn-amber">
                        <i class="fa-solid fa-plus text-xs"></i> Nueva Compra
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="m-table">
                    <thead><tr>
                        <th class="text-center">Lote ID</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th class="text-center">Presentación</th>
                        <th>Leyes</th>
                        <th class="text-right">Peso Original</th>
                        <th class="text-center" style="min-width:130px">Stock Restante</th>
                        <th class="text-right">Total Pagado</th>
                        <th class="text-center">Acciones</th>
                    </tr></thead>
                    <tbody>
                        @forelse($transacciones->where('tipo', 'compra') as $item)
                        @php
                            $pct = $item->peso_neto_seco > 0 ? ($item->peso_disponible / $item->peso_neto_seco) * 100 : 0;
                            $barCls = $pct > 50 ? 'bg-emerald-500' : ($pct > 20 ? 'bg-amber-500' : 'bg-rose-500');
                        @endphp
                        <tr>
                            <td class="text-center">
                                <button @click="showFicha({{ $item->id }})" class="m-badge m-badge-indigo font-mono hover:bg-indigo-500/25 transition cursor-pointer">
                                    LOT-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                </button>
                            </td>
                            <td class="font-mono text-slate-300 font-bold whitespace-nowrap text-xs">{{ $item->fecha->format('d/m/Y') }}</td>
                            <td class="font-bold text-slate-100 uppercase text-xs whitespace-nowrap">{{ $item->cliente_proveedor }}</td>
                            <td class="text-center">
                                <span class="m-badge m-badge-slate">
                                    {{ $item->presentacion === 'Otro' ? ($item->presentacion_otro ?: 'Otro') : $item->presentacion }}
                                </span>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($item->analisis as $an)
                                        <span class="m-badge m-badge-amber font-mono">{{ $an->mineral }}: {{ number_format($an->ley, 2) }}%</span>
                                    @empty
                                        <span class="text-xs italic text-slate-600">Sin lab.</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-right font-mono font-bold text-slate-300 text-xs whitespace-nowrap">{{ number_format($item->peso_neto_seco, 2) }} Kg</td>
                            <td class="px-4">
                                <div class="w-28 mx-auto">
                                    <div class="flex justify-between text-[10px] font-mono mb-1">
                                        <span class="font-bold text-slate-200">{{ number_format($item->peso_disponible, 2) }} Kg</span>
                                        <span class="text-slate-500">{{ round($pct) }}%</span>
                                    </div>
                                    <div class="m-stock-bar">
                                        <div class="m-stock-bar-fill {{ $barCls }}" style="width:{{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right font-mono font-black text-amber-400 text-xs whitespace-nowrap">Bs. {{ number_format($item->monto_total, 2) }}</td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1.5">
                                    <button @click="showFicha({{ $item->id }})" class="m-btn m-btn-ghost m-btn-icon cursor-pointer" title="Ficha Técnica">
                                        <i class="fa-solid fa-eye text-indigo-400 text-xs"></i>
                                    </button>
                                    <a :href="'/transacciones-minerales/' + {{ $item->id }} + '/ticket'" target="_blank" onclick="window.open(this.href, 'Ticket80mm', 'width=450,height=720,scrollbars=yes,noopener'); return false;" class="m-btn m-btn-ghost m-btn-icon cursor-pointer text-emerald-400 hover:text-emerald-300" title="Imprimir Ticket Térmico (80mm / 100x148)">
                                        <i class="fa-solid fa-print text-sm"></i>
                                    </a>
                                    <button @click="editCompra({{ $item }})" class="m-btn m-btn-ghost m-btn-icon cursor-pointer" title="Editar">
                                        <i class="fa-solid fa-pen text-amber-400 text-xs"></i>
                                    </button>
                                    <form action="{{ route('transacciones-minerales.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este lote?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="m-btn m-btn-rose m-btn-icon cursor-pointer" title="Eliminar">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="py-16 text-center text-slate-600">
                            <i class="fa-solid fa-scale-balanced text-4xl block mb-3 opacity-20"></i>
                            <p class="text-slate-500">No hay lotes registrados aún</p>
                            <button @click="initCompra()" class="m-btn m-btn-amber mt-4 cursor-pointer">
                                <i class="fa-solid fa-plus text-xs"></i> Registrar Primera Compra
                            </button>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TAB 2: VENTAS (DESPACHOS)
    ══════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'ventas'" class="m-fade-in">
        <div class="m-card overflow-hidden">
            <div class="m-section-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center text-emerald-400">
                        <i class="fa-solid fa-truck-loading text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-200">Despachos y Salidas de Mineral</h3>
                        <p class="text-[10px] text-slate-500">Ventas realizadas a clientes usando lotes disponibles en almacén</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="m-badge m-badge-emerald">{{ count($transacciones->where('tipo', 'venta')) }} despachos</span>
                    <button @click="initVenta()" type="button" class="m-btn m-btn-emerald">
                        <i class="fa-solid fa-paper-plane text-xs"></i> Registrar Venta
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="m-table">
                    <thead><tr>
                        <th class="text-center">Venta ID</th>
                        <th>Fecha</th>
                        <th>Cliente / Empresa</th>
                        <th class="text-center">Lote Origen</th>
                        <th>Destino</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-right">Peso (Kg)</th>
                        <th class="text-center">Humedad</th>
                        <th class="text-right">Total Venta</th>
                        <th class="text-center">Acciones</th>
                    </tr></thead>
                    <tbody>
                        @forelse($transacciones->where('tipo', 'venta') as $item)
                        <tr>
                            <td class="text-center">
                                <span class="m-badge m-badge-emerald font-mono">
                                    SLD-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td class="font-mono text-slate-300 font-bold whitespace-nowrap text-xs">{{ $item->fecha->format('d/m/Y') }}</td>
                            <td class="font-bold text-slate-100 uppercase text-xs whitespace-nowrap">{{ $item->cliente_proveedor }}</td>
                            <td class="text-center">
                                @if($item->lote)
                                    <button @click="showFicha({{ $item->lote_id }})" class="m-badge m-badge-indigo font-mono hover:bg-indigo-500/25 transition cursor-pointer">
                                        LOT-{{ str_pad($item->lote_id, 5, '0', STR_PAD_LEFT) }}
                                    </button>
                                @else
                                    <span class="text-slate-600 text-[10px] font-mono">Eliminado</span>
                                @endif
                            </td>
                            <td class="text-slate-400 text-xs">{{ $item->destino ?: '—' }}</td>
                            <td class="text-center font-mono text-slate-300 font-bold text-xs">{{ number_format($item->cantidad, 2) }}</td>
                            <td class="text-right font-mono font-bold text-slate-200 text-xs whitespace-nowrap">{{ number_format($item->peso_neto_seco, 2) }} Kg</td>
                            <td class="text-center">
                                @if($item->humedad_porcentaje > 0)
                                    <span class="m-badge m-badge-cyan text-[10px] font-mono">{{ number_format($item->humedad_porcentaje, 2) }}%</span>
                                @else
                                    <span class="text-slate-600 text-xs">—</span>
                                @endif
                            </td>
                            <td class="text-right font-mono font-black text-emerald-400 text-xs whitespace-nowrap">Bs. {{ number_format($item->monto_total, 2) }}</td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1.5">
                                    <a :href="'/transacciones-minerales/' + {{ $item->id }} + '/ticket'" target="_blank" onclick="window.open(this.href, 'Ticket80mm', 'width=450,height=720,scrollbars=yes,noopener'); return false;" class="m-btn m-btn-ghost m-btn-icon cursor-pointer text-emerald-400 hover:text-emerald-300" title="Imprimir Ticket Térmico (80mm / 100x148)">
                                        <i class="fa-solid fa-print text-sm"></i>
                                    </a>
                                    <button @click="editVenta({{ $item }})" class="m-btn m-btn-ghost m-btn-icon cursor-pointer" title="Editar">
                                        <i class="fa-solid fa-pen text-amber-400 text-xs"></i>
                                    </button>
                                    <form action="{{ route('transacciones-minerales.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Cancelar esta venta? Se restituirá el stock del lote.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="m-btn m-btn-rose m-btn-icon cursor-pointer" title="Eliminar">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="py-16 text-center">
                            <i class="fa-solid fa-truck-loading text-4xl block mb-3 text-slate-700 opacity-30"></i>
                            <p class="text-slate-500">No se han registrado ventas aún</p>
                            <button @click="initVenta()" class="m-btn m-btn-emerald mt-4 cursor-pointer">
                                <i class="fa-solid fa-paper-plane text-xs"></i> Registrar Primera Venta
                            </button>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TAB 3: STOCK (INVENTARIO)
    ══════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'stock'" class="space-y-5 m-fade-in">

        {{-- 5 KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="m-kpi border border-cyan-400/20" style="background:linear-gradient(135deg,#0891b2,#164e63)">
                <div class="flex justify-between mb-3 relative z-10">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-cyan-100/80">Lotes Disponibles</span>
                    <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center"><i class="fa-solid fa-boxes-stacked text-xs"></i></div>
                </div>
                <div class="text-3xl font-black font-mono relative z-10">{{ $lotesDisponiblesCount }}</div>
                <p class="text-[10px] text-cyan-100/70 font-semibold mt-1 relative z-10">Con stock activo</p>
            </div>
            <div class="m-kpi border border-emerald-400/20" style="background:linear-gradient(135deg,#059669,#064e3b)">
                <div class="flex justify-between mb-3 relative z-10">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-100/80">Peso Disponible</span>
                    <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center"><i class="fa-solid fa-weight-hanging text-xs"></i></div>
                </div>
                <div class="text-xl font-black font-mono relative z-10">{{ number_format($pesoDisponibleSum, 0) }} <span class="text-sm font-bold">Kg</span></div>
                <p class="text-[10px] text-emerald-100/70 font-semibold mt-1 relative z-10">En almacén actual</p>
            </div>
            <div class="m-kpi border border-amber-400/20" style="background:linear-gradient(135deg,#d97706,#78350f)">
                <div class="flex justify-between mb-3 relative z-10">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-amber-100/80">Valor del Stock</span>
                    <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center"><i class="fa-solid fa-vault text-xs"></i></div>
                </div>
                <div class="text-lg font-black font-mono relative z-10">Bs. {{ number_format($valorTotalStock, 0) }}</div>
                <p class="text-[10px] text-amber-100/70 font-semibold mt-1 relative z-10">Valorización estimada</p>
            </div>
            <div class="m-kpi border border-rose-400/20" style="background:linear-gradient(135deg,#be123c,#4c0519)">
                <div class="flex justify-between mb-3 relative z-10">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-rose-100/80">Compras del Mes</span>
                    <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center"><i class="fa-solid fa-cart-shopping text-xs"></i></div>
                </div>
                <div class="text-3xl font-black font-mono relative z-10">{{ $comprasDelMes }}</div>
                <p class="text-[10px] text-rose-100/70 font-semibold mt-1 relative z-10">Lotes recibidos</p>
            </div>
            <div class="m-kpi border border-indigo-400/20" style="background:linear-gradient(135deg,#4f46e5,#1e1b4b)">
                <div class="flex justify-between mb-3 relative z-10">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-100/80">Ventas del Mes</span>
                    <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center"><i class="fa-solid fa-chart-line-up text-xs"></i></div>
                </div>
                <div class="text-3xl font-black font-mono relative z-10">{{ $ventasDelMes }}</div>
                <p class="text-[10px] text-indigo-100/70 font-semibold mt-1 relative z-10">Despachos realizados</p>
            </div>
        </div>

        {{-- Stock Table --}}
        <div class="m-card overflow-hidden">
            <div class="m-section-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/25 flex items-center justify-center text-cyan-400">
                        <i class="fa-solid fa-warehouse text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-200">Inventario General de Lotes</h3>
                        <p class="text-[10px] text-slate-500">Control de estados · 🟢 Disponible · 🟡 Parcial · 🔴 Vendido</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="m-badge m-badge-cyan">{{ count($todosLosLotes) }} lotes</span>
                    <button @click="initCompra()" type="button" class="m-btn m-btn-amber">
                        <i class="fa-solid fa-plus text-xs"></i> Nueva Compra
                    </button>
                    <button @click="initVenta()" type="button" class="m-btn m-btn-emerald">
                        <i class="fa-solid fa-paper-plane text-xs"></i> Registrar Venta
                    </button>
                </div>
            </div>

            <div class="px-5 py-4 border-b border-slate-800/50 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-500"></i>
                    <input type="text" x-model="searchStock" placeholder="Buscar por Lote ID, Proveedor o Presentación..." class="m-input !pl-8 text-xs">
                </div>
                <select x-model="filterStockEstado" class="m-input text-xs">
                    <option value="">Todos los Estados</option>
                    <option value="Disponible">🟢 Disponible</option>
                    <option value="Parcialmente Vendido">🟡 Parcialmente Vendido</option>
                    <option value="Vendido">🔴 Vendido</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="m-table">
                    <thead><tr>
                        <th class="text-center">Lote</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th class="text-center">Presentación</th>
                        <th class="text-right">Peso Disponible</th>
                        <th class="text-right">Valor Estimado</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Detalle</th>
                    </tr></thead>
                    <tbody>
                        @forelse($todosLosLotes as $lote)
                        @php
                            $hasSales = $lote->ventas->count() > 0;
                            if ($lote->peso_disponible <= 0) {
                                $estado = 'Vendido'; $badgeCls = 'm-badge-rose'; $dot = '🔴';
                            } elseif ($hasSales && $lote->peso_disponible < $lote->peso_neto_seco) {
                                $estado = 'Parcialmente Vendido'; $badgeCls = 'm-badge-amber'; $dot = '🟡';
                            } else {
                                $estado = 'Disponible'; $badgeCls = 'm-badge-emerald'; $dot = '🟢';
                            }
                            $valorEst = $lote->peso_disponible * $lote->precio_unidad;
                        @endphp
                        <tr x-show="matchesStockFilter('{{ $lote->id }}','{{ str_replace("'","\'", $lote->cliente_proveedor) }}','{{ str_replace("'","\'", $lote->presentacion) }}','{{ $estado }}')">
                            <td class="text-center">
                                <button @click="showFicha({{ $lote->id }})" class="m-badge m-badge-cyan font-mono hover:bg-cyan-500/25 transition cursor-pointer">
                                    LOT-{{ str_pad($lote->id, 5, '0', STR_PAD_LEFT) }}
                                </button>
                            </td>
                            <td class="font-mono text-slate-300 font-bold whitespace-nowrap text-xs">{{ $lote->fecha->format('d/m/Y') }}</td>
                            <td class="font-bold text-slate-100 uppercase text-xs whitespace-nowrap">{{ $lote->cliente_proveedor }}</td>
                            <td class="text-center">
                                <span class="m-badge m-badge-slate">{{ $lote->presentacion === 'Otro' ? ($lote->presentacion_otro ?: 'Otro') : $lote->presentacion }}</span>
                            </td>
                            <td class="text-right font-mono font-bold text-xs">
                                <span class="text-slate-100">{{ number_format($lote->peso_disponible, 2) }} Kg</span>
                                <span class="block text-[10px] text-slate-600 font-normal">/ {{ number_format($lote->peso_neto_seco, 2) }} Kg orig.</span>
                            </td>
                            <td class="text-right font-mono font-black text-emerald-400 text-xs whitespace-nowrap">Bs. {{ number_format($valorEst, 2) }}</td>
                            <td class="text-center">
                                <span class="m-badge {{ $badgeCls }}">{{ $dot }} {{ $estado }}</span>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1.5">
                                    <button @click="showFicha({{ $lote->id }})" class="m-btn m-btn-ghost m-btn-icon cursor-pointer" title="Ver Detalle">
                                        <i class="fa-solid fa-eye text-cyan-400 text-xs"></i>
                                    </button>
                                    <a :href="'/transacciones-minerales/' + {{ $lote->id }} + '/ticket'" target="_blank" onclick="window.open(this.href, 'Ticket80mm', 'width=450,height=720,scrollbars=yes,noopener'); return false;" class="m-btn m-btn-ghost m-btn-icon cursor-pointer text-emerald-400 hover:text-emerald-300" title="Imprimir Ticket Térmico (80mm / 100x148)">
                                        <i class="fa-solid fa-print text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="py-16 text-center text-slate-600">
                            <i class="fa-solid fa-boxes-stacked text-4xl block mb-3 opacity-20"></i>
                            <p class="text-slate-500">No hay lotes registrados en el inventario</p>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TAB 4: REPORTES
    ══════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'reportes'" class="space-y-5 m-fade-in">

        {{-- Filtros --}}
        <div class="m-card p-5">
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-800/60">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/25 flex items-center justify-center text-indigo-400">
                        <i class="fa-solid fa-sliders text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-200">Filtros de Búsqueda</h3>
                        <p class="text-[10px] text-slate-500">Los resultados se actualizan automáticamente</p>
                    </div>
                </div>
                <button type="button"
                        onclick="const f = document.getElementById('reportes-form'); if(f){ Array.from(f.elements).forEach(e=>{ if(e.type!=='hidden') e.value=''; }); submitFilterRealTime(f); }"
                        class="m-btn m-btn-ghost">
                    <i class="fa-solid fa-rotate-left text-amber-500 text-xs"></i> Limpiar Filtros
                </button>
            </div>

            <form id="reportes-form" action="{{ route('transacciones-minerales.index') }}" method="GET"
                  onchange="submitFilterRealTime(this)"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <input type="hidden" name="tab" value="reportes">

                <div>
                    <label class="m-label">Tipo de Movimiento</label>
                    <select name="tipo" class="m-input text-xs">
                        <option value="">Todos los movimientos</option>
                        <option value="compra" {{ request('tipo') === 'compra' ? 'selected' : '' }}>📥 Compras (Entradas)</option>
                        <option value="venta" {{ request('tipo') === 'venta' ? 'selected' : '' }}>📤 Ventas (Salidas)</option>
                    </select>
                </div>

                <div>
                    <label class="m-label">Proveedor / Cliente</label>
                    <input type="text" name="cliente_proveedor" value="{{ request('cliente_proveedor') }}"
                           placeholder="Buscar por nombre..." class="m-input text-xs">
                </div>

                <div>
                    <label class="m-label">Mineral</label>
                    <select name="mineral" class="m-input text-xs">
                        <option value="">Cualquier mineral</option>
                        <option value="Zinc" {{ request('mineral') === 'Zinc' ? 'selected' : '' }}>⚡ Zinc (Zn)</option>
                        <option value="Plomo" {{ request('mineral') === 'Plomo' ? 'selected' : '' }}>🔘 Plomo (Pb)</option>
                        <option value="Plata" {{ request('mineral') === 'Plata' ? 'selected' : '' }}>✨ Plata (Ag)</option>
                        <option value="Cobre" {{ request('mineral') === 'Cobre' ? 'selected' : '' }}>🟠 Cobre (Cu)</option>
                        <option value="Estaño" {{ request('mineral') === 'Estaño' ? 'selected' : '' }}>⬜ Estaño (Sn)</option>
                    </select>
                </div>

                <div>
                    <label class="m-label">Ley Mínima (%)</label>
                    <input type="number" step="0.01" name="ley" value="{{ request('ley') }}"
                           placeholder="Ej. 10.00" class="m-input text-xs font-mono">
                </div>

                <div>
                    <label class="m-label">Bocamina</label>
                    <select name="bocamina_id" class="m-input text-xs">
                        <option value="">Todas las Bocaminas</option>
                        @foreach($bocaminas as $boc)
                            <option value="{{ $boc->id }}" {{ request('bocamina_id') == $boc->id ? 'selected' : '' }}>{{ $boc->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="m-label">Presentación</label>
                    <select name="presentacion" class="m-input text-xs">
                        <option value="">Todas</option>
                        <option value="Sacos" {{ request('presentacion') === 'Sacos' ? 'selected' : '' }}>📦 Sacos</option>
                        <option value="Volqueta" {{ request('presentacion') === 'Volqueta' ? 'selected' : '' }}>🚛 Volqueta</option>
                        <option value="Concentrado" {{ request('presentacion') === 'Concentrado' ? 'selected' : '' }}>⚗️ Concentrado</option>
                        <option value="Otro" {{ request('presentacion') === 'Otro' ? 'selected' : '' }}>📋 Otro</option>
                    </select>
                </div>

                <div x-data="{ rango: '{{ request('rango_fecha', (request('fecha_desde') || request('fecha_hasta')) ? 'personalizado' : '') }}' }" class="sm:col-span-2">
                    <label class="m-label">Período de Fecha</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <select name="rango_fecha" x-model="rango" class="m-input text-xs">
                            <option value="">Todas las Fechas</option>
                            <option value="hoy">Hoy</option>
                            <option value="semanal">Esta Semana</option>
                            <option value="mensual">Este Mes</option>
                            <option value="personalizado">Personalizado...</option>
                        </select>
                        <div x-show="rango === 'personalizado'" x-cloak class="flex gap-2">
                            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="m-input text-xs flex-1">
                            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="m-input text-xs flex-1">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Resumen Financiero --}}
        @php
            $rptCompras = $transacciones->where('tipo', 'compra')->sum('monto_total');
            $rptVentas  = $transacciones->where('tipo', 'venta')->sum('monto_total');
            $rptBalance = $rptVentas - $rptCompras;
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="m-card p-5 border border-amber-500/15">
                <span class="m-label" style="color:#f59e0b"><i class="fa-solid fa-arrow-down-left mr-1"></i>Total Compras Filtradas</span>
                <div class="text-2xl font-black font-mono text-amber-400 mt-2">Bs. {{ number_format($rptCompras, 2) }}</div>
            </div>
            <div class="m-card p-5 border border-emerald-500/15">
                <span class="m-label" style="color:#10b981"><i class="fa-solid fa-arrow-up-right mr-1"></i>Total Ventas Filtradas</span>
                <div class="text-2xl font-black font-mono text-emerald-400 mt-2">Bs. {{ number_format($rptVentas, 2) }}</div>
            </div>
            <div class="m-card p-5 {{ $rptBalance >= 0 ? 'border border-indigo-500/15' : 'border border-rose-500/15' }}">
                <span class="m-label {{ $rptBalance >= 0 ? 'text-indigo-400' : 'text-rose-400' }}"><i class="fa-solid fa-scale-balanced mr-1"></i>Balance Neto</span>
                <div class="text-2xl font-black font-mono {{ $rptBalance >= 0 ? 'text-indigo-300' : 'text-rose-400' }} mt-2">Bs. {{ number_format($rptBalance, 2) }}</div>
            </div>
        </div>

        {{-- Tabla de Reporte --}}
        <div id="report-output" class="m-card overflow-hidden">
            <div class="m-section-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/25 flex items-center justify-center text-indigo-400">
                        <i class="fa-solid fa-list-check text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-200">Reporte Filtrado de Operaciones</h3>
                        <p class="text-[10px] text-slate-500">{{ count($transacciones) }} registros encontrados</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="m-btn m-btn-ghost cursor-pointer" onclick="window.doExportExcel()">
                        <i class="fa-solid fa-file-excel text-emerald-400"></i> Excel
                    </button>
                    <button class="m-btn m-btn-ghost cursor-pointer" onclick="window.doExportPDF()">
                        <i class="fa-solid fa-file-pdf text-rose-400"></i> PDF
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="m-table" id="mineral-reports-table">
                    <thead><tr>
                        <th>Fecha</th>
                        <th class="text-center">Tipo</th>
                        <th>Cliente / Proveedor</th>
                        <th>Bocamina</th>
                        <th class="text-center">Presentación</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Peso</th>
                        <th>Leyes</th>
                        <th class="text-right">Monto Total</th>
                    </tr></thead>
                    <tbody>
                        @forelse($transacciones as $item)
                        <tr>
                            <td class="font-mono text-xs text-slate-300 whitespace-nowrap">{{ $item->fecha->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="m-badge {{ $item->tipo === 'compra' ? 'm-badge-amber' : 'm-badge-emerald' }} font-mono">
                                    <i class="fa-solid {{ $item->tipo === 'compra' ? 'fa-arrow-down-left' : 'fa-arrow-up-right' }}"></i>
                                    {{ $item->tipo === 'compra' ? 'Compra' : 'Venta' }}
                                </span>
                            </td>
                            <td class="font-bold text-slate-100 text-xs">{{ $item->cliente_proveedor }}</td>
                            <td class="text-xs text-slate-400">{{ $item->bocamina->nombre ?? 'N/A' }}</td>
                            <td class="text-center text-xs text-slate-400">{{ $item->presentacion === 'Otro' ? ($item->presentacion_otro ?: 'Otro') : $item->presentacion }}</td>
                            <td class="text-right font-mono text-xs text-slate-300">{{ number_format($item->cantidad, 2) }}</td>
                            <td class="text-right font-mono text-xs text-slate-300 whitespace-nowrap">{{ number_format($item->peso_neto_seco, 2) }} Kg</td>
                            <td>
                                @php $analisisList = $item->tipo === 'compra' ? $item->analisis : ($item->lote ? $item->lote->analisis : []); @endphp
                                <div class="flex flex-wrap gap-1">
                                    @forelse($analisisList as $an)
                                        <span class="m-badge m-badge-amber font-mono">{{ $an->mineral }}: {{ number_format($an->ley, 2) }}%</span>
                                    @empty
                                        <span class="text-slate-600 text-xs">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-right font-mono font-black text-amber-400 text-xs whitespace-nowrap">Bs. {{ number_format($item->monto_total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="py-16 text-center text-slate-600">
                            <i class="fa-solid fa-file-invoice text-4xl block mb-3 opacity-20"></i>
                            <p class="text-slate-500">No se encontraron registros con los filtros seleccionados</p>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         MODAL: COMPRA (INGRESO DE LOTE)
    ══════════════════════════════════════════════════ --}}
    <div x-show="openCompraModal" x-cloak style="display:none" :style="{ display: openCompraModal ? 'flex' : 'none' }"
         @click.self="openCompraModal = false"
         class="m-modal-overlay">
        <div class="m-modal" style="max-height:95vh;">
            {{-- Header --}}
            <div class="m-modal-header" style="background:rgba(245,158,11,0.05);border-color:rgba(245,158,11,0.12)">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400">
                        <i class="fa-solid fa-boxes-stacked text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-100" x-text="isEditMode ? 'Editar Lote de Mineral' : 'Registrar Ingreso de Lote'"></h3>
                        <p class="text-[10px] text-slate-500" x-text="isEditMode ? 'Modifica los datos del lote seleccionado' : 'Nuevo ingreso al almacén — Lote generado automáticamente'"></p>
                    </div>
                </div>
                <button @click="openCompraModal = false" class="m-btn m-btn-ghost m-btn-icon cursor-pointer">
                    <i class="fa-solid fa-xmark text-slate-400"></i>
                </button>
            </div>

            <form :action="actionUrl" method="POST">
                @csrf
                <template x-if="isEditMode"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="tipo" value="compra">

                <div class="m-modal-body">

                    {{-- ── Sección 1: Información General ── --}}
                    <div class="m-modal-section">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="m-step bg-amber-500/20 text-amber-400">1</div>
                            <h4 class="text-xs font-black uppercase tracking-widest text-amber-400">Información General del Lote</h4>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="m-label">Fecha de Compra <span class="text-rose-400">*</span></label>
                                <input type="date" name="fecha" required x-model="compraFecha" class="m-input m-input-amber font-mono">
                            </div>
                            <div>
                                <label class="m-label">Número de Lote</label>
                                <div class="m-input flex items-center gap-2 opacity-60 cursor-not-allowed">
                                    <i class="fa-solid fa-tag text-amber-500/70 text-xs"></i>
                                    <span class="font-mono text-xs text-slate-400" x-show="!isEditMode">Se genera automáticamente al guardar</span>
                                    <span class="font-mono text-xs text-amber-400 font-bold" x-show="isEditMode" x-text="'LOT-' + String(compraId).padStart(5,'0')"></span>
                                </div>
                            </div>
                            <div>
                                <label class="m-label">Proveedor / Persona que vende <span class="text-rose-400">*</span></label>
                                <input type="text" name="cliente_proveedor" required x-model="compraProveedor"
                                       placeholder="Nombre del Proveedor o Cooperativa" class="m-input m-input-amber">
                            </div>
                            <div>
                                <label class="m-label">Bocamina de Origen <span class="text-slate-600 font-normal normal-case">(Opcional)</span></label>
                                <select name="bocamina_id" x-model="compraBocaminaId" class="m-input">
                                    <option value="">— Sin bocamina específica —</option>
                                    @foreach($bocaminas as $boc)
                                        <option value="{{ $boc->id }}">{{ $boc->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="m-label">Tipo de Presentación <span class="text-rose-400">*</span></label>
                                <select name="presentacion" x-model="compraPresentacion" class="m-input">
                                    <option value="Sacos">📦 Sacos</option>
                                    <option value="Volqueta">🚛 Volqueta</option>
                                    <option value="Concentrado">⚗️ Concentrado</option>
                                    <option value="Otro">📋 Otro</option>
                                </select>
                            </div>
                            <div x-show="compraPresentacion === 'Otro'">
                                <label class="m-label">Especificar Presentación <span class="text-rose-400">*</span></label>
                                <input type="text" name="presentacion_otro" x-model="compraPresentacionOtro"
                                       placeholder="Describe el tipo de presentación..." class="m-input">
                            </div>
                        </div>
                    </div>

                    {{-- ── Sección 2: Datos de la Compra ── --}}
                    <div class="m-modal-section">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="m-step bg-cyan-500/20 text-cyan-400">2</div>
                            <h4 class="text-xs font-black uppercase tracking-widest text-cyan-400">Datos de la Compra y Liquidación</h4>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="m-label">
                                    <span x-show="compraPresentacion === 'Sacos'">Cantidad de Sacos <span class="text-rose-400">*</span></span>
                                    <span x-show="compraPresentacion === 'Volqueta'">Cantidad de Volquetas <span class="text-rose-400">*</span></span>
                                    <span x-show="compraPresentacion === 'Concentrado'">Cantidad <span class="text-rose-400">*</span></span>
                                    <span x-show="compraPresentacion === 'Otro'">Cantidad <span class="text-rose-400">*</span></span>
                                </label>
                                <input type="number" step="0.01" name="cantidad" required x-model="compraCantidad"
                                       :placeholder="compraPresentacion === 'Sacos' ? 'Ej. 100 sacos' : compraPresentacion === 'Volqueta' ? 'Ej. 3 volquetadas' : 'Ej. 500'"
                                       class="m-input font-mono">
                                <p class="text-[10px] text-slate-500 mt-1">
                                    <span x-show="compraPresentacion === 'Sacos'">Número total de sacos recibidos</span>
                                    <span x-show="compraPresentacion === 'Volqueta'">Número de volquetadas entregadas</span>
                                    <span x-show="compraPresentacion === 'Concentrado'">Unidades de concentrado</span>
                                    <span x-show="compraPresentacion === 'Otro'">Cantidad según la presentación</span>
                                </p>
                            </div>

                            {{-- PESO BRUTO --}}
                            <div>
                                <template x-if="compraPresentacion === 'Volqueta'">
                                    <div>
                                        <label class="m-label">Peso Bruto Total (Toneladas - T) <span class="text-rose-400">*</span></label>
                                        <input type="number" step="0.001" required x-model="compraPesoToneladas"
                                               @input="calcCompraTotal()" placeholder="Ej. 3 (o 3000 Kg)"
                                               class="m-input font-mono m-input-amber">
                                        <input type="hidden" name="peso_bruto" :value="compraPesoBruto">
                                        <p class="text-[10px] text-slate-400 mt-1 flex items-center justify-between font-mono">
                                            <span>Peso bruto ingresado:</span>
                                            <span class="text-amber-400 font-bold" x-show="compraPesoBruto > 0" x-text="numberFormat(compraPesoBruto) + ' Kg (' + (compraPesoBruto/1000).toFixed(3) + ' T)'"></span>
                                        </p>
                                    </div>
                                </template>
                                <template x-if="compraPresentacion !== 'Volqueta'">
                                    <div>
                                        <label class="m-label">Peso Bruto Total (Kg) <span class="text-rose-400">*</span></label>
                                        <input type="number" step="0.01" name="peso_bruto" required x-model="compraPesoBruto"
                                               @input="calcCompraTotal()" placeholder="Ej. 1000 Kg"
                                               class="m-input font-mono">
                                        <p class="text-[10px] text-slate-500 mt-1">Peso bruto total antes del descuento por humedad</p>
                                    </div>
                                </template>
                            </div>

                            {{-- HUMEDAD (%) - OBLIGATORIO --}}
                            <div>
                                <label class="m-label font-bold text-sky-400 flex items-center justify-between">
                                    <span><i class="fa-solid fa-droplet text-sky-400 mr-1.5"></i>Humedad Total (%) <span class="text-rose-400">*</span></span>
                                    <span class="text-[10px] font-normal text-slate-400">(Obligatorio)</span>
                                </label>
                                <div class="relative">
                                    <input type="number" step="0.01" min="0" max="100" name="humedad_porcentaje" required x-model="compraHumedadPorcentaje"
                                           @input="calcCompraTotal()" placeholder="0.00"
                                           class="m-input font-mono m-input-sky pr-8">
                                    <span class="absolute right-3 top-2.5 text-xs text-sky-400 font-bold font-mono">%</span>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1 flex items-center justify-between font-mono" x-show="parseFloat(compraHumedadPorcentaje || 0) > 0">
                                    <span>Descuento en costo total:</span>
                                    <span class="text-rose-400 font-bold" x-text="'-Bs. ' + numberFormat(calcDescuentoDinero)"></span>
                                </p>
                            </div>

                            {{-- PRECIO DE COMPRA --}}
                            <div>
                                <template x-if="compraPresentacion === 'Volqueta'">
                                    <div>
                                        <label class="m-label">Precio de Compra (Bs. por Tonelada) <span class="text-rose-400">*</span></label>
                                        <input type="number" step="0.01" min="0" required x-model="compraPrecioToneladas"
                                               @input="if ($el.value < 0) $el.value = 0;"
                                               placeholder="Ej. 1500.00 (Bs/T)"
                                               class="m-input font-mono m-input-amber">
                                        <input type="hidden" name="precio_unidad" :value="calcPrecioUnit.toFixed(4)">
                                        <p class="text-[10px] text-slate-400 mt-1 flex items-center justify-between font-mono" x-show="compraPrecioToneladas > 0">
                                            <span>Precio por Kg:</span>
                                            <span class="text-amber-400 font-bold" x-text="'Bs. ' + calcPrecioUnit.toFixed(4) + ' / Kg'"></span>
                                        </p>
                                    </div>
                                </template>
                                <template x-if="compraPresentacion !== 'Volqueta'">
                                    <div>
                                        <label class="m-label">Precio de Compra (Bs. por Kg) <span class="text-rose-400">*</span></label>
                                        <input type="number" step="0.01" min="0" name="precio_unidad" required x-model="compraPrecio"
                                               @input="if ($el.value < 0) $el.value = 0;"
                                               placeholder="Ej. 1.50" class="m-input font-mono">
                                        <p class="text-[10px] text-slate-500 mt-1">Precio unitario en bolivianos por Kilogramo</p>
                                    </div>
                                </template>
                            </div>

                            {{-- TARJETAS ERP PREMIUM DE LIQUIDACIÓN Y CÁLCULO EN TIEMPO REAL --}}
                            <div class="sm:col-span-2 space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    {{-- Subtotal Bruto --}}
                                    <div class="rounded-xl p-3.5 bg-slate-900/90 border border-slate-800/80 shadow-md">
                                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">
                                            <span>Subtotal Bruto</span>
                                            <i class="fa-solid fa-calculator text-slate-600"></i>
                                        </div>
                                        <div class="text-base font-black font-mono text-slate-200" x-text="'Bs. ' + numberFormat(calcSubtotalBruto)"></div>
                                        <div class="text-[10px] text-slate-500 font-mono mt-0.5" x-text="numberFormat(calcPesoBruto) + ' Kg sin desc.'"></div>
                                    </div>

                                    {{-- Descuento por Humedad --}}
                                    <div class="rounded-xl p-3.5 bg-sky-950/30 border border-sky-500/25 shadow-md">
                                        <div class="flex items-center justify-between text-[10px] text-sky-400 font-bold uppercase tracking-wider mb-1">
                                            <span>Desc. Humedad (<span x-text="calcHumedadPct"></span>%)</span>
                                            <i class="fa-solid fa-droplet text-sky-400"></i>
                                        </div>
                                        <div class="text-base font-black font-mono text-rose-400" x-text="'-Bs. ' + numberFormat(calcDescuentoDinero)"></div>
                                        <div class="text-[10px] text-sky-400/80 font-mono mt-0.5" x-text="'- ' + numberFormat(calcDescuentoPeso) + ' Kg en peso'"></div>
                                    </div>

                                    {{-- Peso Neto Seco --}}
                                    <div class="rounded-xl p-3.5 bg-emerald-950/30 border border-emerald-500/25 shadow-md">
                                        <div class="flex items-center justify-between text-[10px] text-emerald-400 font-bold uppercase tracking-wider mb-1">
                                            <span>Peso Neto Seco</span>
                                            <i class="fa-solid fa-box text-emerald-400"></i>
                                        </div>
                                        <div class="text-base font-black font-mono text-emerald-400" x-text="numberFormat(calcPesoNetoSeco) + ' Kg'"></div>
                                        <div class="text-[10px] text-emerald-400/80 font-mono mt-0.5">Peso neto almacén</div>
                                    </div>
                                </div>

                                {{-- Tarjeta Principal: Total Liquidado Final --}}
                                <div class="p-4 rounded-2xl border border-amber-500/40 bg-gradient-to-r from-amber-950/40 via-slate-900 to-amber-950/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xl">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 text-lg shadow-inner">
                                            <i class="fa-solid fa-coins"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-black uppercase tracking-wider text-amber-400">Total Liquidado a Pagar</h4>
                                            <p class="text-[10px] text-slate-400 font-mono">
                                                Cálculo: <strong class="text-slate-200" x-text="numberFormat(calcPesoNetoSeco) + ' Kg'"></strong> × <strong class="text-slate-200" x-text="'Bs. ' + calcPrecioUnit.toFixed(4)"></strong>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right font-mono">
                                        <div class="text-2xl font-black text-amber-400 tracking-tight" x-text="'Bs. ' + numberFormat(calcTotalLiquidado)"></div>
                                        <div class="text-[9px] text-emerald-400 font-bold uppercase tracking-widest flex items-center justify-end gap-1">
                                            <i class="fa-solid fa-circle-check"></i> Descuento de Humedad Incluido
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="peso_neto_seco" :value="calcPesoNetoSeco.toFixed(2)">
                                <input type="hidden" name="monto_total" :value="calcTotalLiquidado.toFixed(2)">
                            </div>
                        </div>
                    </div>

                    {{-- ── Sección 3: Análisis Químico de Laboratorio (Leyes) ── --}}
                    <div class="m-modal-section">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <div class="m-step bg-indigo-500/20 text-indigo-400">3</div>
                                <h4 class="text-xs font-black uppercase tracking-widest text-indigo-400">
                                    <i class="fa-solid fa-flask mr-1.5"></i>Análisis Químico de Laboratorio (Leyes)
                                </h4>
                            </div>
                            <button type="button" @click="addMineral()"
                                    class="m-btn m-btn-ghost cursor-pointer" style="border-color:rgba(99,102,241,0.3);color:#818cf8">
                                <i class="fa-solid fa-plus text-xs"></i> Agregar Ley
                            </button>
                        </div>

                        <div class="rounded-xl border border-slate-800/80 overflow-hidden">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-slate-900/80 border-b border-slate-800">
                                        <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-wider text-slate-500">Mineral</th>
                                        <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-500">Ley (%)</th>
                                        <th class="px-4 py-3 text-center text-[10px] w-16">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(an, index) in compraAnalisis" :key="index">
                                        <tr class="border-b border-slate-800/50 hover:bg-slate-800/20 transition">
                                            <td class="px-4 py-2.5">
                                                <input type="hidden" :name="'analisis['+index+'][mineral]'" :value="an.mineral === 'Otro' ? an.mineral_custom : an.mineral">
                                                <select x-model="an.mineral" class="m-input text-xs py-2" required>
                                                    <option value="">— Seleccionar —</option>
                                                    <option value="Zinc">⚡ Zinc (Zn)</option>
                                                    <option value="Plomo">🔘 Plomo (Pb)</option>
                                                    <option value="Plata">✨ Plata (Ag)</option>
                                                    <option value="Cobre">🟠 Cobre (Cu)</option>
                                                    <option value="Estaño">⬜ Estaño (Sn)</option>
                                                    <option value="Otro">✏️ Otro (Escribir)...</option>
                                                </select>
                                                <div x-show="an.mineral === 'Otro'" class="mt-1.5">
                                                    <input type="text" x-model="an.mineral_custom"
                                                           placeholder="Escribe el mineral (ej. Oro, Bismuto...)"
                                                           class="m-input text-xs py-1.5 font-bold text-amber-400 border-amber-500/30"
                                                           :required="an.mineral === 'Otro'">
                                                </div>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <div class="relative">
                                                    <input type="number" step="0.01" min="0" max="100"
                                                           :name="'analisis['+index+'][ley]'" x-model="an.ley"
                                                           placeholder="48.50" class="m-input font-mono text-xs py-2 text-center pr-8" required>
                                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500 font-bold">%</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2.5 text-center">
                                                <button type="button" @click="removeMineral(index)"
                                                        class="m-btn m-btn-rose m-btn-icon cursor-pointer">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="compraAnalisis.length === 0">
                                        <tr>
                                            <td colspan="3" class="px-4 py-5 text-center text-slate-600 text-xs italic">
                                                <i class="fa-solid fa-flask opacity-30 mr-1"></i>
                                                Agrega al menos un análisis de laboratorio
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2">
                            <i class="fa-solid fa-circle-info mr-1 text-indigo-500/60"></i>
                            Por defecto incluye Zinc, Plomo y Plata. Puedes seleccionar un mineral estándar o elegir <strong>"Otro"</strong> para escribir un mineral personalizado.
                        </p>
                    </div>

                    {{-- ── Sección 4: Observaciones ── --}}
                    <div class="m-modal-section">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="m-step bg-slate-500/20 text-slate-400"><i class="fa-solid fa-comment text-xs"></i></div>
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400">Observaciones y Notas Adicionales</h4>
                        </div>
                        <textarea name="observacion" x-model="compraObservacion" rows="2" class="m-input resize-none text-xs"
                                  placeholder="Notas adicionales sobre el lote, transporte, entrega o proveedor..."></textarea>
                    </div>
                </div>

                <div class="m-modal-footer">
                    <button type="button" @click="openCompraModal = false" class="m-btn m-btn-ghost cursor-pointer">Cancelar</button>
                    <button type="submit" class="m-btn m-btn-amber cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Lote
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         MODAL: VENTA (DESPACHO DE MINERAL)
    ══════════════════════════════════════════════════ --}}
    <div x-show="openVentaModal" x-cloak style="display:none" :style="{ display: openVentaModal ? 'flex' : 'none' }"
         @click.self="openVentaModal = false"
         class="m-modal-overlay">
        <div class="m-modal" style="max-height:95vh;">
            {{-- Header --}}
            <div class="m-modal-header" style="background:rgba(16,185,129,0.05);border-color:rgba(16,185,129,0.12)">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-100" x-text="isEditMode ? 'Editar Registro de Venta' : 'Registrar Despacho de Mineral'"></h3>
                        <p class="text-[10px] text-slate-500" x-text="isEditMode ? 'Modifica los datos de la venta' : 'Selecciona cliente y lotes a despachar'"></p>
                    </div>
                </div>
                <button @click="openVentaModal = false" class="m-btn m-btn-ghost m-btn-icon cursor-pointer">
                    <i class="fa-solid fa-xmark text-slate-400"></i>
                </button>
            </div>

            <form :action="actionUrl" method="POST">
                @csrf
                <template x-if="isEditMode"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="tipo" value="venta">

                <div class="m-modal-body">

                    {{-- ── Paso 1: Cliente y Destino ── --}}
                    <div class="m-modal-section">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="m-step bg-emerald-500/20 text-emerald-400">1</div>
                            <h4 class="text-xs font-black uppercase tracking-widest text-emerald-400">Cliente y Destino del Despacho</h4>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="m-label">Fecha de Venta <span class="text-rose-400">*</span></label>
                                <input type="date" name="fecha" required x-model="ventaFecha" class="m-input m-input-emerald font-mono">
                            </div>
                            <div>
                                <label class="m-label">Cliente / Empresa Compradora <span class="text-rose-400">*</span></label>
                                <input type="text" name="cliente_proveedor" required x-model="ventaCliente"
                                       placeholder="Nombre del comprador o empresa" class="m-input m-input-emerald">
                            </div>
                            <div>
                                <label class="m-label">Destino del Despacho <span class="text-slate-600 font-normal normal-case">(Opcional)</span></label>
                                <input type="text" name="destino" x-model="ventaDestino"
                                       placeholder="Ej. Fundición Vinto, Puerto..." class="m-input">
                            </div>
                        </div>
                    </div>

                    {{-- ── Paso 2: Lotes a Despachar (Modo Creación) ── --}}
                    <template x-if="!isEditMode">
                        <div class="m-modal-section">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <div class="m-step bg-cyan-500/20 text-cyan-400">2</div>
                                    <h4 class="text-xs font-black uppercase tracking-widest text-cyan-400">
                                        <i class="fa-solid fa-layer-group mr-1.5"></i>Lotes a Despachar
                                    </h4>
                                </div>
                                <button type="button" @click="addVentaLote()"
                                        class="m-btn m-btn-ghost cursor-pointer" style="border-color:rgba(16,185,129,0.3);color:#34d399">
                                    <i class="fa-solid fa-plus text-xs"></i> Agregar Lote
                                </button>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(item, index) in ventaLotes" :key="index">
                                    <div class="rounded-xl border border-slate-700/50 overflow-hidden bg-slate-900/30">
                                        {{-- Lote Header --}}
                                        <div class="flex items-center justify-between px-4 py-2.5 bg-slate-800/50 border-b border-slate-700/40">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                                                <i class="fa-solid fa-box text-emerald-400"></i>
                                                <span x-text="'Lote #' + (index + 1)"></span>
                                            </span>
                                            <button type="button" @click="removeVentaLote(index)"
                                                    x-show="ventaLotes.length > 1"
                                                    class="text-[10px] text-rose-400 hover:text-rose-300 transition flex items-center gap-1 cursor-pointer">
                                                <i class="fa-solid fa-trash-can text-[9px]"></i> Quitar
                                            </button>
                                        </div>

                                        <div class="p-4 space-y-3">
                                            {{-- Selector de Lote --}}
                                            <div>
                                                <label class="m-label text-[9px]">Seleccionar Lote con Stock Disponible <span class="text-rose-400">*</span></label>
                                                <select :name="'lotes['+index+'][lote_id]'" x-model="item.lote_id"
                                                        @change="onVentaLoteSelected(index)" class="m-input text-xs" required>
                                                    <option value="">— Seleccionar Lote Disponible —</option>
                                                    @foreach($lotesDisponibles as $lot)
                                                        <option value="{{ $lot->id }}">
                                                            LOT-{{ str_pad($lot->id, 5, '0', STR_PAD_LEFT) }} · {{ $lot->cliente_proveedor }} · Stock: {{ number_format($lot->peso_disponible, 2) }} Kg
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Info del Lote Seleccionado --}}
                                            <div x-show="item.info" class="rounded-lg bg-slate-800/60 border border-slate-700/50 p-3">
                                                <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">Datos del Lote</p>
                                                <div class="grid grid-cols-2 gap-2 text-[11px]">
                                                    <div><span class="text-slate-500">Presentación:</span>
                                                        <span class="text-slate-200 font-bold ml-1" x-text="item.info?.presentacion === 'Otro' ? (item.info?.presentacion_otro || 'Otro') : item.info?.presentacion"></span>
                                                    </div>
                                                    <div><span class="text-slate-500">Disponible:</span>
                                                        <span class="text-emerald-400 font-black ml-1" x-text="parseFloat(item.info?.peso_disponible || 0).toFixed(2) + ' Kg'"></span>
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap gap-1 mt-2">
                                                    <template x-for="an in (item.info ? item.info.analisis : [])">
                                                        <span class="m-badge m-badge-amber font-mono text-[9px]">
                                                            <span x-text="an.mineral"></span>: <span x-text="parseFloat(an.ley).toFixed(2)"></span>%
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>

                                            {{-- Campos de cantidad, peso, precio, total --}}
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" x-show="item.lote_id">
                                                <div>
                                                    <label class="m-label text-[9px]">Cantidad <span class="text-rose-400">*</span></label>
                                                    <input type="number" step="0.01" :name="'lotes['+index+'][cantidad]'"
                                                           x-model="item.cantidad" class="m-input font-mono text-xs py-2" required placeholder="Ej. 20">
                                                    <p class="text-[9px] text-rose-400 mt-0.5 font-bold" x-show="item.info && parseFloat(item.cantidad) > parseFloat(item.info.cantidad_disponible)">⚠ Supera stock!</p>
                                                </div>

                                                {{-- PESO VENTA --}}
                                                <div>
                                                    <template x-if="item.info && item.info.presentacion === 'Volqueta'">
                                                        <div>
                                                            <label class="m-label text-[9px]">Peso a Vender (Toneladas - T) <span class="text-rose-400">*</span></label>
                                                            <input type="number" step="0.001" x-model="item.pesoToneladas"
                                                                   @input="calcVentaItemTotal(index)" class="m-input font-mono text-xs py-2 m-input-amber" required placeholder="Ej. 10.5 T">
                                                            <p class="text-[9px] text-amber-400 font-mono font-bold mt-0.5" x-show="item.peso_neto_seco > 0" x-text="'= ' + item.peso_neto_seco + ' Kg'"></p>
                                                        </div>
                                                    </template>
                                                    <template x-if="!item.info || item.info.presentacion !== 'Volqueta'">
                                                        <div>
                                                            <label class="m-label text-[9px]">Peso a Vender (Kg) <span class="text-rose-400">*</span></label>
                                                            <input type="number" step="0.01"
                                                                   x-model="item.peso_neto_seco" @input="calcVentaItemTotal(index)"
                                                                   class="m-input font-mono text-xs py-2" required placeholder="Ej. 1000">
                                                        </div>
                                                    </template>
                                                    <input type="hidden" :name="'lotes['+index+'][peso_neto_seco]'" :value="item.peso_neto_seco">
                                                    <p class="text-[9px] text-rose-400 mt-0.5 font-bold" x-show="item.info && parseFloat(item.peso_neto_seco) > parseFloat(item.info.peso_disponible)">⚠ Supera stock!</p>
                                                </div>

                                                {{-- PRECIO VENTA --}}
                                                <div>
                                                    <template x-if="item.info && item.info.presentacion === 'Volqueta'">
                                                        <div>
                                                            <label class="m-label text-[9px]">Precio Venta (Bs/T) <span class="text-rose-400">*</span></label>
                                                            <input type="number" step="0.01" x-model="item.precioToneladas"
                                                                   @input="calcVentaItemTotal(index)" class="m-input font-mono text-xs py-2 m-input-amber" required placeholder="Ej. 1500.00">
                                                        </div>
                                                    </template>
                                                    <template x-if="!item.info || item.info.presentacion !== 'Volqueta'">
                                                        <div>
                                                            <label class="m-label text-[9px]">Precio Venta (Bs/Kg) <span class="text-rose-400">*</span></label>
                                                            <input type="number" step="0.01"
                                                                   x-model="item.precio_unidad" @input="calcVentaItemTotal(index)"
                                                                   class="m-input font-mono text-xs py-2" required placeholder="Ej. 2.10">
                                                        </div>
                                                    </template>
                                                    <input type="hidden" :name="'lotes['+index+'][precio_unidad]'" :value="item.precio_unidad">
                                                </div>

                                                <div>
                                                    <label class="m-label text-[9px] font-bold text-sky-400">Humedad (%) <span class="text-rose-400">*</span></label>
                                                    <div class="relative">
                                                        <input type="number" step="0.01" min="0" max="100"
                                                               :name="'lotes['+index+'][humedad_porcentaje]'"
                                                               x-model="item.humedad_porcentaje" @input="calcVentaItemTotal(index)"
                                                               class="m-input font-mono text-xs py-2 m-input-sky pr-8" required placeholder="0.00">
                                                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-sky-400 font-bold font-mono">%</span>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="m-label text-[9px] font-black" style="color:#10b981">Total (Bs.) — Auto</label>
                                                    <input type="number" step="0.01" :name="'lotes['+index+'][monto_total]'"
                                                           x-model="item.monto_total"
                                                           class="m-input font-mono text-xs py-2 font-black" readonly
                                                           style="color:#10b981;border-color:rgba(16,185,129,0.3);background:rgba(16,185,129,0.05)">
                                                </div>
                                            </div>

                                            {{-- Indicadores Disponible / A Vender / Restante --}}
                                            <div x-show="item.info && item.lote_id" class="grid grid-cols-3 gap-2">
                                                <div class="rounded-lg px-3 py-2 text-center bg-slate-800/40 border border-emerald-500/20">
                                                    <p class="text-[9px] text-emerald-500/70 font-bold uppercase mb-0.5">Disponible</p>
                                                    <p class="text-xs font-black text-emerald-400 font-mono"
                                                       x-text="item.info?.presentacion === 'Volqueta' ? (parseFloat(item.info?.peso_disponible || 0)/1000).toFixed(3) + ' T' : parseFloat(item.info?.peso_disponible || 0).toFixed(2) + ' Kg'"></p>
                                                </div>
                                                <div class="rounded-lg px-3 py-2 text-center bg-slate-800/40 border border-amber-500/20">
                                                    <p class="text-[9px] text-amber-500/70 font-bold uppercase mb-0.5">A Vender</p>
                                                    <p class="text-xs font-black text-amber-400 font-mono"
                                                       x-text="item.info?.presentacion === 'Volqueta' ? (parseFloat(item.peso_neto_seco || 0)/1000).toFixed(3) + ' T' : (parseFloat(item.peso_neto_seco || 0)).toFixed(2) + ' Kg'"></p>
                                                </div>
                                                <div class="rounded-lg px-3 py-2 text-center bg-slate-800/40"
                                                     :class="(parseFloat(item.info?.peso_disponible||0) - parseFloat(item.peso_neto_seco||0)) < 0 ? 'border border-rose-500/30' : 'border border-cyan-500/20'">
                                                    <p class="text-[9px] font-bold uppercase mb-0.5"
                                                       :class="(parseFloat(item.info?.peso_disponible||0) - parseFloat(item.peso_neto_seco||0)) < 0 ? 'text-rose-500/70' : 'text-cyan-500/70'">Restante</p>
                                                    <p class="text-xs font-black font-mono"
                                                       :class="(parseFloat(item.info?.peso_disponible||0) - parseFloat(item.peso_neto_seco||0)) < 0 ? 'text-rose-400' : 'text-cyan-400'"
                                                       x-text="item.info?.presentacion === 'Volqueta' ? ((parseFloat(item.info?.peso_disponible||0) - parseFloat(item.peso_neto_seco||0))/1000).toFixed(3) + ' T' : (parseFloat(item.info?.peso_disponible||0) - parseFloat(item.peso_neto_seco||0)).toFixed(2) + ' Kg'"></p>
                                                </div>
                                            </div>

                                            {{-- Leyes Químicas Editables para la Venta --}}
                                            <div x-show="item.lote_id" class="mt-3 pt-3 border-t border-slate-700/40">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
                                                        <i class="fa-solid fa-flask text-amber-400"></i> Leyes de Laboratorio (Venta)
                                                    </span>
                                                    <button type="button" @click="addVentaAnalisis(index)"
                                                            class="text-[10px] text-indigo-400 hover:text-indigo-300 font-bold transition flex items-center gap-1 cursor-pointer">
                                                        <i class="fa-solid fa-plus text-[9px]"></i> Agregar Ley
                                                    </button>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                                    <template x-for="(an, anIdx) in (item.analisis || [])" :key="anIdx">
                                                        <div class="flex items-center gap-1.5 bg-slate-800/80 p-1.5 rounded-lg border border-slate-700/60">
                                                            <input type="text" x-model="an.mineral" placeholder="Mineral"
                                                                   :name="'lotes['+index+'][analisis]['+anIdx+'][mineral]'"
                                                                   class="m-input text-[11px] py-1 px-2 font-bold text-amber-300 flex-1" required>
                                                            <div class="relative w-20">
                                                                <input type="number" step="0.01" x-model="an.ley" placeholder="0.00"
                                                                       :name="'lotes['+index+'][analisis]['+anIdx+'][ley]'"
                                                                       class="m-input font-mono text-[11px] py-1 px-1.5 text-center font-bold" required>
                                                                <span class="absolute right-1.5 top-1/2 -translate-y-1/2 text-[9px] text-slate-500 font-bold">%</span>
                                                            </div>
                                                            <button type="button" @click="removeVentaAnalisis(index, anIdx)"
                                                                    class="text-rose-400 hover:text-rose-300 p-1 cursor-pointer">
                                                                <i class="fa-solid fa-xmark text-xs"></i>
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <template x-if="!item.analisis || item.analisis.length === 0">
                                                        <div class="col-span-full text-[10px] text-slate-500 italic py-1">
                                                            Sin leyes asignadas a este despacho. Haz clic en <strong>+ Agregar Ley</strong> para incluir una.
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- ── Paso 2 (Modo Edición): Datos de la Venta ── --}}
                    <template x-if="isEditMode">
                        <div class="m-modal-section">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="m-step bg-amber-500/20 text-amber-400">2</div>
                                <h4 class="text-xs font-black uppercase tracking-widest text-amber-400">Datos de la Venta</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="m-label">Lote Origen</label>
                                    <select name="lote_id" x-model="ventaLoteId" class="m-input" required disabled>
                                        @foreach($lotesDisponibles as $lot)
                                            <option value="{{ $lot->id }}">LOT-{{ str_pad($lot->id, 5, '0', STR_PAD_LEFT) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="loteInfo" class="rounded-xl p-3 bg-emerald-500/5 border border-emerald-500/20 text-xs space-y-1">
                                    <div>Presentación: <span class="text-slate-200 font-bold" x-text="loteInfo?.presentacion"></span></div>
                                    <div>Stock Disponible: <span class="text-emerald-400 font-black" x-text="(loteInfo?.peso_disponible || 0) + ' Kg'"></span></div>
                                </div>
                                <div>
                                    <label class="m-label">Cantidad a Vender <span class="text-rose-400">*</span></label>
                                    <input type="number" step="0.01" name="cantidad" required x-model="ventaCantidad" class="m-input font-mono">
                                </div>
                                <div>
                                    <label class="m-label">Peso Neto Seco (Kg) <span class="text-rose-400">*</span></label>
                                    <input type="number" step="0.01" name="peso_neto_seco" required x-model="ventaPeso" @input="calcVentaTotal()" class="m-input font-mono">
                                </div>
                                <div>
                                    <label class="m-label">Precio Unitario Venta (Bs/Kg) <span class="text-rose-400">*</span></label>
                                    <input type="number" step="0.01" name="precio_unidad" required x-model="ventaPrecio" @input="calcVentaTotal()" class="m-input font-mono">
                                </div>
                                <div>
                                    <label class="m-label font-bold text-sky-400">Humedad (%) <span class="text-rose-400">*</span></label>
                                    <div class="relative">
                                        <input type="number" step="0.01" min="0" max="100" name="humedad_porcentaje" required x-model="ventaHumedadPorcentaje" @input="calcVentaTotal()" class="m-input font-mono m-input-sky pr-8">
                                        <span class="absolute right-3 top-2.5 text-xs text-sky-400 font-bold font-mono">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="m-label font-black" style="color:#10b981">💰 Total Cobrado (Bs.)</label>
                                    <input type="number" step="0.01" name="monto_total" required x-model="ventaTotal"
                                           class="m-input font-mono font-black" style="color:#10b981;border-color:rgba(16,185,129,0.3);background:rgba(16,185,129,0.05)" readonly>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Observaciones --}}
                    <div class="m-modal-section">
                        <label class="m-label">Observaciones <span class="text-slate-600 font-normal normal-case">(Opcional)</span></label>
                        <textarea name="observacion" x-model="ventaObservacion" rows="2" class="m-input resize-none text-xs"
                                  placeholder="Notas adicionales sobre el despacho..."></textarea>
                    </div>
                </div>

                <div class="m-modal-footer">
                    <button type="button" @click="openVentaModal = false" class="m-btn m-btn-ghost cursor-pointer">Cancelar</button>
                    <button type="submit" class="m-btn m-btn-emerald cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i> Registrar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         MODAL: FICHA TÉCNICA DEL LOTE
    ══════════════════════════════════════════════════ --}}
    <div x-show="openDetailModal" x-cloak style="display:none" :style="{ display: openDetailModal ? 'flex' : 'none' }"
         class="m-modal-overlay" @click.self="openDetailModal = false">
        <div class="m-modal" style="max-width:620px;max-height:92vh;">
            <div class="m-modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400">
                        <i class="fa-solid fa-cube text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-100">Ficha Técnica del Lote</h3>
                        <p class="text-[10px] text-slate-500 font-mono" x-text="fichaLote ? 'LOT-' + String(fichaLote.id).padStart(5,'0') : ''"></p>
                    </div>
                </div>
                <button @click="openDetailModal = false" class="m-btn m-btn-ghost m-btn-icon cursor-pointer">
                    <i class="fa-solid fa-xmark text-slate-400"></i>
                </button>
            </div>

            <div class="m-modal-body" x-show="fichaLote">

                {{-- Info General --}}
                <div class="m-modal-section">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-4">
                        <i class="fa-solid fa-circle-info mr-1.5 text-amber-500/70"></i>Información General
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="m-label">Código de Lote</span>
                            <span class="text-lg font-black text-amber-400 font-mono block" x-text="fichaLote ? 'LOT-' + String(fichaLote.id).padStart(5,'0') : ''"></span>
                        </div>
                        <div>
                            <span class="m-label">Fecha de Registro</span>
                            <span class="text-sm font-bold text-slate-200 font-mono block" x-text="fichaLote ? new Date(fichaLote.fecha).toLocaleDateString('es-BO') : ''"></span>
                        </div>
                        <div>
                            <span class="m-label">Proveedor</span>
                            <span class="text-sm font-bold text-slate-100 uppercase block" x-text="fichaLote ? fichaLote.cliente_proveedor : ''"></span>
                        </div>
                        <div>
                            <span class="m-label">Bocamina de Origen</span>
                            <span class="text-sm text-slate-300 block" x-text="fichaLote?.bocamina ? fichaLote.bocamina.nombre : 'No asignada'"></span>
                        </div>
                    </div>
                </div>

                {{-- Inventario de Peso --}}
                <div class="m-modal-section">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-4">
                        <i class="fa-solid fa-warehouse mr-1.5 text-cyan-500/70"></i>Stock en Almacén
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="rounded-xl p-3 bg-slate-800/50 border border-slate-700/40 text-center">
                            <p class="m-label mb-1">Peso Inicial</p>
                            <p class="text-sm font-black font-mono text-slate-200" x-text="fichaLote ? parseFloat(fichaLote.peso_neto_seco).toFixed(2) + ' Kg' : ''"></p>
                        </div>
                        <div class="rounded-xl p-3 bg-slate-800/50 border border-rose-500/20 text-center">
                            <p class="m-label mb-1">Peso Vendido</p>
                            <p class="text-sm font-black font-mono text-rose-400"
                               x-text="fichaLote ? (parseFloat(fichaLote.peso_neto_seco) - parseFloat(fichaLote.peso_disponible)).toFixed(2) + ' Kg' : ''"></p>
                        </div>
                        <div class="rounded-xl p-3 bg-slate-800/50 border border-emerald-500/20 text-center">
                            <p class="m-label mb-1">Peso Disponible</p>
                            <p class="text-sm font-black font-mono text-emerald-400" x-text="fichaLote ? parseFloat(fichaLote.peso_disponible).toFixed(2) + ' Kg' : ''"></p>
                        </div>
                        <div class="rounded-xl p-3 bg-slate-800/50 border border-amber-500/20 text-center">
                            <p class="m-label mb-1">Valor Disponible</p>
                            <p class="text-sm font-black font-mono text-amber-400"
                               x-text="fichaLote ? 'Bs. ' + (parseFloat(fichaLote.peso_disponible) * parseFloat(fichaLote.precio_unidad)).toFixed(2) : ''"></p>
                        </div>
                    </div>
                </div>

                {{-- Análisis de Laboratorio --}}
                <div class="m-modal-section">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-4">
                        <i class="fa-solid fa-flask mr-1.5 text-indigo-500/70"></i>Análisis Original de Laboratorio
                    </h4>
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="an in (fichaLote ? fichaLote.analisis : [])">
                            <div class="rounded-xl p-3 bg-slate-800/50 border border-slate-700/40 text-center">
                                <span class="m-label mb-1" x-text="an.mineral"></span>
                                <strong class="text-base text-amber-400 font-mono block" x-text="parseFloat(an.ley).toFixed(2) + '%'"></strong>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Historial de Ventas --}}
                <div class="m-modal-section">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-4">
                        <i class="fa-solid fa-clock-rotate-left mr-1.5 text-indigo-500/70"></i>Historial Completo del Lote
                    </h4>
                    <div class="rounded-xl border border-slate-800 overflow-hidden">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-900/80 border-b border-slate-800">
                                    <th class="px-3 py-2.5 text-left text-[10px] font-black uppercase tracking-wider text-slate-500">Venta ID</th>
                                    <th class="px-3 py-2.5 text-left text-[10px] font-black uppercase tracking-wider text-slate-500">Fecha</th>
                                    <th class="px-3 py-2.5 text-left text-[10px] font-black uppercase tracking-wider text-slate-500">Cliente</th>
                                    <th class="px-3 py-2.5 text-right text-[10px] font-black uppercase tracking-wider text-slate-500">Peso</th>
                                    <th class="px-3 py-2.5 text-right text-[10px] font-black uppercase tracking-wider text-slate-500">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="vt in (fichaLote ? fichaLote.ventas : [])">
                                    <tr class="border-t border-slate-800/50 hover:bg-slate-800/20 transition">
                                        <td class="px-3 py-3 font-mono">
                                            <span class="m-badge m-badge-emerald">SLD-<span x-text="String(vt.id).padStart(5,'0')"></span></span>
                                        </td>
                                        <td class="px-3 py-3 font-mono text-slate-300" x-text="new Date(vt.fecha).toLocaleDateString('es-BO')"></td>
                                        <td class="px-3 py-3 font-bold text-slate-200 uppercase text-[11px]" x-text="vt.cliente_proveedor"></td>
                                        <td class="px-3 py-3 font-mono text-slate-300 text-right" x-text="parseFloat(vt.peso_neto_seco).toFixed(2) + ' Kg'"></td>
                                        <td class="px-3 py-3 font-mono font-black text-emerald-400 text-right" x-text="'Bs. ' + parseFloat(vt.monto_total).toFixed(2)"></td>
                                    </tr>
                                </template>
                                <template x-if="!fichaLote || !fichaLote.ventas || !fichaLote.ventas.length">
                                    <tr>
                                        <td colspan="5" class="px-4 py-5 text-center text-slate-600 text-xs italic">
                                            <i class="fa-solid fa-clock-rotate-left opacity-30 mr-1"></i>
                                            No se han realizado ventas a partir de este lote
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="m-modal-section" x-show="fichaLote?.observacion">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Observaciones</h4>
                    <p class="text-xs text-slate-400 leading-relaxed" x-text="fichaLote?.observacion"></p>
                </div>
            </div>

            <div class="m-modal-footer flex items-center justify-between">
                <a :href="fichaLote ? '/transacciones-minerales/' + fichaLote.id + '/ticket' : '#'" target="_blank" onclick="window.open(this.href, 'Ticket80mm', 'width=420,height=700,scrollbars=yes'); return false;" class="m-btn inline-flex items-center gap-2 text-xs font-bold px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl shadow-md transition">
                    <i class="fa-solid fa-print text-sm"></i> Ticket (80mm / 100x148)
                </a>
                <button type="button" @click="openDetailModal = false" class="m-btn m-btn-ghost cursor-pointer">
                    <i class="fa-solid fa-xmark text-xs"></i> Cerrar Ficha
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ─── Real-time AJAX Filter para Reportes ─────────────────────────────────────
function submitFilterRealTime(form) {
    const url = new URL(form.action || window.location.href, window.location.origin);
    url.search = '';
    new FormData(form).forEach((v, k) => { if (v && String(v).trim()) url.searchParams.set(k, v); });
    window.history.pushState({}, '', url.toString());

    const container = document.getElementById('report-output');
    if (!container) { form.submit(); return; }

    container.style.opacity = '0.4';
    container.style.pointerEvents = 'none';

    fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const fresh = doc.getElementById('report-output');
            if (fresh) container.innerHTML = fresh.innerHTML;
            else window.location.reload();
        })
        .catch(() => form.submit())
        .finally(() => { container.style.opacity = '1'; container.style.pointerEvents = ''; });
}

// ─── Helper para cargar librerías dinámicamente ───────────────────────────────
function ensureLibrary(checkFn, src, callback) {
    if (checkFn()) { callback(); return; }
    const script = document.createElement('script');
    script.src = src;
    script.onload = () => callback();
    script.onerror = () => callback(new Error('Failed to load script'));
    document.head.appendChild(script);
}

// ─── PDF Export ───────────────────────────────────────────────────────────────
window.doExportPDF = function() {
    const tableContainer = document.getElementById('report-output');
    if (!tableContainer) return;
    const table = tableContainer.querySelector('table');
    if (!table) return;

    const now = new Date();
    const dateStr = now.toLocaleDateString('es-BO', { day:'2-digit', month:'long', year:'numeric' });
    const timeStr = now.toLocaleTimeString('es-BO', { hour:'2-digit', minute:'2-digit' });

    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.replace(/\s+/g, ' ').trim());
    const rows = Array.from(table.querySelectorAll('tbody tr')).map(tr => {
        return Array.from(tr.querySelectorAll('td')).map(td => td.textContent.replace(/\s+/g, ' ').trim());
    });

    const pdfHtml = `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte_Minerales_${now.toISOString().slice(0,10)}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 24px; color: #0f172a; background: #ffffff; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #0f172a; padding-bottom: 14px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: 900; color: #0f172a; margin: 0; text-transform: uppercase; letter-spacing: -0.02em; }
        .subtitle { font-size: 11px; color: #64748b; margin: 4px 0 0 0; font-weight: 500; }
        .badge { font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: 700; text-align: right; letter-spacing: 0.05em; }
        .company { font-size: 12px; font-weight: 800; color: #0f172a; margin-top: 2px; text-align: right; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 10px; }
        th { background: #1e293b; color: #ffffff; padding: 9px 10px; text-align: left; font-weight: 700; text-transform: uppercase; border: 1px solid #1e293b; letter-spacing: 0.05em; }
        td { padding: 8px 10px; border: 1px solid #cbd5e1; color: #334155; vertical-align: middle; }
        tr:nth-child(even) { background: #f8fafc; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; text-align: right; font-size: 9px; color: #94a3b8; font-weight: 600; }
        @page { size: landscape; margin: 12mm; }
        @media print {
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1 class="title">Reporte de Compra y Venta de Minerales</h1>
            <p class="subtitle">Generado el ${dateStr} a las ${timeStr} · Módulo 2 (Comercialización)</p>
        </div>
        <div>
            <div class="badge">Documento Oficial</div>
            <div class="company">SISTEMA DE PAGOS Y CONTROL MINERO</div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                ${headers.map(h => `<th>${h}</th>`).join('')}
            </tr>
        </thead>
        <tbody>
            ${rows.map(r => `
                <tr>
                    ${r.map(cell => `<td>${cell}</td>`).join('')}
                </tr>
            `).join('')}
        </tbody>
    </table>
    <div class="footer">
        Página 1 / 1 · Reporte de Comercialización de Minerales
    </div>
</body>
</html>`;

    const oldIframe = document.getElementById('mineral-print-iframe');
    if (oldIframe) oldIframe.remove();

    const iframe = document.createElement('iframe');
    iframe.id = 'mineral-print-iframe';
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    iframe.style.visibility = 'hidden';
    document.body.appendChild(iframe);

    iframe.contentDocument.open();
    iframe.contentDocument.write(pdfHtml);
    iframe.contentDocument.close();

    setTimeout(() => {
        try {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } catch (e) {
            window.print();
        }
        setTimeout(() => {
            if (iframe.parentNode) iframe.remove();
        }, 2500);
    }, 350);
};

// ─── Excel Export ─────────────────────────────────────────────────────────────
window.doExportExcel = function() {
    const tableContainer = document.getElementById('report-output');
    if (!tableContainer) return;
    const table = tableContainer.querySelector('table');
    if (!table) return;

    // Build data matrix using textContent so hidden CSS doesn't create blank sheets
    const aoa = [];
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.replace(/\s+/g, ' ').trim());
    aoa.push(headers);

    Array.from(table.querySelectorAll('tbody tr')).forEach(tr => {
        const rowData = Array.from(tr.querySelectorAll('td')).map(td => td.textContent.replace(/\s+/g, ' ').trim());
        if (rowData.length > 0) {
            aoa.push(rowData);
        }
    });

    const filename = 'Reporte_Minerales_' + new Date().toLocaleDateString('es-BO').replace(/\//g,'-') + '.csv';

    // Direct, infallible CSV export with UTF-8 BOM so Excel opens it with full formatting & zero blanks
    const csvLines = aoa.map(row => row.map(cell => `"${cell.replace(/"/g, '""')}"`).join(","));
    const csvContent = "\uFEFF" + csvLines.join("\r\n");
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>
@endpush
