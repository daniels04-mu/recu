/**
 * Lógica de gráficos y procesamiento de datos - Red Forge
 * Uso de métodos avanzados de arreglos (Map, Filter, Reduce)
 */

document.addEventListener("DOMContentLoaded", () => {
    // Datos simulados o extraídos del sistema de inventario industrial
    const productosRedForge = [
        { nombre: "Tornillería Industrial", stock: 120, precio: 1500 },
        { nombre: "Válvulas de Acero", stock: 4, precio: 45000 },
        { nombre: "Mangueras de Alta Presión", stock: 15, precio: 25000 },
        { nombre: "Llaves de Impacto", stock: 2, precio: 120000 },
        { nombre: "Manómetros Digitales", stock: 45, precio: 85000 }
    ];

    // 1. FILTER: Identificar productos con stock crítico (menor o igual a 5 unidades)
    const stockCritico = productosRedForge.filter(item => item.stock <= 5);
    console.warn("⚠️ Alerta de Stock Crítico:", stockCritico);

    // 2. MAP: Extraer únicamente los nombres de los productos para listados rápidos
    const nombresProductos = productosRedForge.map(item => item.nombre);
    console.log("📋 Listado de referencias:", nombresProductos);

    // 3. REDUCE: Calcular el valor económico total del inventario actual
    const valorInventarioTotal = productosRedForge.reduce((acumulador, item) => {
        return acumulador + (item.precio * item.stock);
    }, 0);

    console.log("💰 Valor total del inventario:", `$${valorInventarioTotal.toLocaleString()}`);

    // Inyectar un resumen rápido en el DOM si el elemento existe en el dashboard
    const contenedorMetricas = document.getElementById("metricas-js");
    if (contenedorMetricas) {
        contenedorMetricas.innerHTML = `
            <div class="card-metrica">
                <h4>Valor Total en Bodega</h4>
                <p>$${valorInventarioTotal.toLocaleString()}</p>
            </div>
            <div class="card-metrica">
                <h4>Alertas Críticas</h4>
                <p>${stockCritico.length} productos por reabastecer</p>
            </div>
        `;
    }
});