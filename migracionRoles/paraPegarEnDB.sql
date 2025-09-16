INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'ver_cargas', 'web', NULL, NULL),
(2, 'ver_carga', 'web', NULL, NULL),
(3, 'ver_lugares_descargas_empresa', 'web', NULL, NULL);

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Master', 'web', NULL, NULL),
(2, 'Customer', 'web', NULL, NULL),
(3, 'Traffic', 'web', NULL, NULL),
(4, 'Transport', 'web', NULL, NULL),
(5, 'ClienteEmpresa', 'web', NULL, '2025-07-24 17:58:00'),
(6, 'Desactivar', 'web', NULL, NULL);

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5);

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 44),
(1, 'App\\Models\\User', 66),
(1, 'App\\Models\\User', 77),
(2, 'App\\Models\\User', 49),
(2, 'App\\Models\\User', 78),
(2, 'App\\Models\\User', 82),
(2, 'App\\Models\\User', 86),
(3, 'App\\Models\\User', 62),
(3, 'App\\Models\\User', 79),
(5, 'App\\Models\\User', 110),
(5, 'App\\Models\\User', 111);

const dbConfig = {
  host: process.env.DB_HOST || "193.203.175.171",
  port: Number.parseInt(process.env.DB_PORT || "3306"),
  user: process.env.DB_USER || "u101685278_chat_ttl",
  password: process.env.DB_PASSWORD || "Rail2025$",
  database: process.env.DB_NAME || "u101685278_chat_ttl",
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  acquireTimeout: 60000,
  timeout: 60000,
}
