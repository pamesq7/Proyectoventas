-- Insertar Opciones
INSERT INTO opcions (nombre, descripcion, estado, created_at, updated_at) VALUES
('Fútbol', 'Productos relacionados con fútbol', 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('Vestir', 'Productos de vestir', 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('Corto', 'Productos de corte corto', 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('Chamarra', 'Productos tipo chamarra', 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('Pretina', 'Productos con pretina', 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('Puño', 'Productos con puño', 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('Buzo', 'Productos tipo buzo', 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('Tela', 'Productos de tela', 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00');

-- Insertar Características
INSERT INTO caracteristicas (nombre, descripcion, idOpcion, estado, created_at, updated_at) VALUES
-- Fútbol (idOpcion = 1)
('v', 'P.futbol, P.basket', 1, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('redondo', NULL, 1, 1, NULL, NULL),
('semicadete', 'P.futbol, P.basket', 1, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),

-- Vestir (idOpcion = 2)
('larga', 'P.futbol, P.vestir', 2, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('corta', 'P.futbol, P.vestir', 2, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),

-- Corto (idOpcion = 3)
('full sublimado', 'corto', 3, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('semisublimado', 'corto', 3, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),

-- Chamarra (idOpcion = 4)
('sin capucha', 'chamarra', 4, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),

-- Pretina (idOpcion = 5)
('pretina normal', 'chamarra', 5, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('pretina tejido', 'chamarra', 5, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),

-- Puño (idOpcion = 6)
('puño normal', 'chamarra', 6, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('puño tejido', 'chamarra', 6, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('sin puño', 'chamarra', 6, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),

-- Buzo (idOpcion = 7)
('bordado', 'buzo', 7, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('dif', 'buzo', 7, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),

-- Tela (idOpcion = 8)
('drifit', 'tela', 8, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('win', 'tela', 8, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00'),
('impala', 'tela', 8, 1, '2025-07-29 17:00:00', '2025-07-29 17:00:00');
