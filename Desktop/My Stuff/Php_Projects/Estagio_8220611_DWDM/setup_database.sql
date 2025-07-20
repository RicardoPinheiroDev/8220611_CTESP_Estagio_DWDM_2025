-- departments table
CREATE TABLE IF NOT EXISTS departments (
    id TEXT PRIMARY KEY,
    department TEXT NOT NULL,
    status INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- teams table
CREATE TABLE IF NOT EXISTS teams (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    department_id TEXT,
    status INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- clients table
CREATE TABLE IF NOT EXISTS clients (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password TEXT NOT NULL,
    phone TEXT NULL,
    billing_name TEXT NULL,
    billing_address TEXT NULL,
    vat_number TEXT NULL,
    is_active INTEGER DEFAULT 1,
    additional_contacts TEXT NULL,
    remember_token TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- registrars table
CREATE TABLE IF NOT EXISTS registrars (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    contact_info TEXT NULL,
    api_details TEXT NULL,
    status INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- servers table
CREATE TABLE IF NOT EXISTS servers (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    ip_address TEXT NOT NULL,
    location TEXT NULL,
    type TEXT DEFAULT 'shared',
    status INTEGER DEFAULT 1,
    specs TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- hosting_plans table
CREATE TABLE IF NOT EXISTS hosting_plans (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    billing_cycle TEXT DEFAULT 'monthly',
    features TEXT NULL,
    status INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- domains table
CREATE TABLE IF NOT EXISTS domains (
    id TEXT PRIMARY KEY,
    client_id TEXT NOT NULL,
    name TEXT UNIQUE NOT NULL,
    registered_at TIMESTAMP NULL,
    expires_at TIMESTAMP NOT NULL,
    registrar_id TEXT NOT NULL,
    is_managed INTEGER DEFAULT 0,
    server_id TEXT NULL,
    status TEXT DEFAULT 'active',
    payment_status TEXT DEFAULT 'pending',
    next_renewal_price DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (registrar_id) REFERENCES registrars(id) ON DELETE CASCADE,
    FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE SET NULL
);

-- hostings table
CREATE TABLE IF NOT EXISTS hostings (
    id TEXT PRIMARY KEY,
    client_id TEXT NOT NULL,
    account_name TEXT NOT NULL,
    domain_id TEXT NULL,
    plan_id INTEGER NOT NULL,
    server_id TEXT NOT NULL,
    starts_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    status TEXT DEFAULT 'active',
    payment_status TEXT DEFAULT 'pending',
    next_renewal_price DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE SET NULL,
    FOREIGN KEY (plan_id) REFERENCES hosting_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE CASCADE
);

--renewals table
CREATE TABLE IF NOT EXISTS renewals (
    id TEXT PRIMARY KEY,
    renewable_type TEXT NOT NULL,
    renewable_id TEXT NOT NULL,
    renewal_date TIMESTAMP NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status TEXT DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE users ADD COLUMN status INTEGER DEFAULT 1;
ALTER TABLE users ADD COLUMN type TEXT DEFAULT 'client';


CREATE TABLE IF NOT EXISTS migrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    migration TEXT NOT NULL,
    batch INTEGER NOT NULL
);

--migration records
INSERT OR IGNORE INTO migrations (migration, batch) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2025_06_20_191031_new_user_table', 1),
('2025_06_20_191812_create_departments_table', 1),
('2025_06_20_193521_create_teams_table', 1),
('2025_06_20_193612_create_clients_table', 1),
('2025_06_20_221159_create_domains_table', 1),
('2025_06_20_221223_create_hostings_table', 1),
('2025_06_20_221551_create_renewals_table', 1),
('2025_06_20_221606_create_servers_table', 1),
('2025_06_20_221621_create_registrars_table', 1),
('2025_06_20_221640_create_hosting_plans_table', 1);

SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;