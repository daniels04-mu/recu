-- Inserción de usuario administrador de prueba
INSERT INTO usuarios (correo, password, rol) VALUES 
('admin@redforge.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Inserción de productos iniciales
INSERT INTO productos (nombre, precio, stock) VALUES 
('Cable UTP Cat6', 45000.00, 100),
('Conector RJ45 (Paquete x50)', 15000.00, 50),
('Switch 8 Puertos Gigabit', 120000.00, 15);