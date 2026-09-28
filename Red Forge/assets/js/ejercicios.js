
const productosBajoStock = productosPrueba.filter(producto => producto.stock < 20);
console.log("Productos con stock bajo:", productosBajoStock);


const nombresProductos = productosPrueba.map(producto => producto.nombre.toUpperCase());
console.log("Nombres de productos:", nombresProductos);

const valorTotalInventario = productosPrueba.reduce((acumulador, producto) => {
    return acumulador + (producto.precio * producto.stock);
}, 0);

console.log("Valor total del inventario: $" + valorTotalInventario);