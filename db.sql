CREATE DATABASE barberia_BART;
USE barberia_BART;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'barbero', 'cliente') DEFAULT 'cliente',
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de servicios
CREATE TABLE servicios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    duracion INT NOT NULL COMMENT 'Duración en minutos',
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de citas
CREATE TABLE citas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    barbero_id INT NOT NULL,
    servicio_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada') DEFAULT 'pendiente',
    notas TEXT,
    precio_final DECIMAL(10,2),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (barbero_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE
);

-- Tabla de horarios de barberos
CREATE TABLE horarios_barberos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    barbero_id INT NOT NULL,
    dia_semana ENUM('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'),
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (barbero_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Insertar datos de ejemplo
INSERT INTO usuarios (nombre, email, telefono, password, rol) VALUES
('Administrador', 'admin@barberia.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador'),
('Juan Pérez', 'barbero@barberia.com', '1234567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'barbero'),
('Carlos Cliente', 'cliente@email.com', '1234567892', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente');

INSERT INTO servicios (nombre, descripcion, precio, duracion) VALUES
('Corte Clásico', 'Corte de cabello tradicional', 15000, 30),
('Corte y Barba', 'Corte de cabello más arreglo de barba', 25000, 45),
('Afeitado Completo', 'Afeitado tradicional con navaja', 20000, 30),
('Corte Moderno', 'Cortes modernos y con estilo', 18000, 40);

CREATE VIEW vista_servicios_completos AS
SELECT 
    sr.id,
    sr.fecha_servicio,
    sr.precio_cobrado,
    sr.comision_barbero,
    c.nombre as cliente_nombre,
    c.telefono as cliente_telefono,
    b.nombre as barbero_nombre,
    s.nombre as servicio_nombre,
    sr.observaciones
FROM servicios_realizados sr
JOIN usuarios c ON sr.cliente_id = c.id
JOIN usuarios b ON sr.barbero_id = b.id
JOIN servicios s ON sr.servicio_id = s.id
ORDER BY sr.fecha_servicio DESC, sr.id DESC;

-- Vista de comisiones pendientes por barbero
CREATE VIEW vista_comisiones_pendientes AS
SELECT 
    b.id as barbero_id,
    b.nombre as barbero_nombre,
    COUNT(sr.id) as servicios_realizados,
    SUM(sr.comision_barbero) as total_comisiones,
    MIN(sr.fecha_servicio) as fecha_inicio,
    MAX(sr.fecha_servicio) as fecha_fin
FROM usuarios b
LEFT JOIN servicios_realizados sr ON b.id = sr.barbero_id
LEFT JOIN pagos_barberos pb ON b.id = pb.barbero_id 
    AND sr.fecha_servicio BETWEEN pb.periodo_inicio AND pb.periodo_fin
WHERE b.rol = 'barbero' 
    AND b.activo = TRUE
    AND pb.id IS NULL
    AND sr.id IS NOT NULL
GROUP BY b.id, b.nombre
HAVING total_comisiones > 0;