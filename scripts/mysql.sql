CREATE TABLE galette_helloasso_types_cotisation_prices (
	id_type_cotis int(10) unsigned NOT NULL,
	amount double NULL,
	can_change_amount tinyint(1) NOT NULL,
	PRIMARY KEY (id_type_cotis),
	CONSTRAINT helloasso_types_cotisations_fk FOREIGN KEY (id_type_cotis) REFERENCES galette_types_cotisation (id_type_cotis) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE galette_helloasso_intents (
	id_intent int(10) unsigned NOT NULL autoincrement,
	id_adh int(10) unsigned NOT NULL,
	id_helloasso int unsigned NOT NULL,
	amount double NOT NULL,
	expires_at datetime NOT NULL,
	state ENUM('pending', 'done', 'cancelled'),
	PRIMARY KEY (id_intent),
	CONSTRAINT helloasso_adherent_fk FOREIGN KEY (id_adh) REFERENCES galette_adherents (id_adh) ON DELETE CASCADE ON UPDATE CASCADE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE galette_helloasso_intent_line (
	id_intent int(10) unsigned NOT NULL,
	id_type_cotis int(10) unsigned NOT NULL,
	amount double NOT NULL,
	PRIMARY KEY (id_intent, id_type_cotis)
	CONSTRAINT helloasso_intent_line_fk FOREIGN KEY (id_intent) REFERENCES galette_helloasso_intents (id_intent) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT helloasso_intent_line_type_cotisation_fk FOREIGN KEY (id_type_cotis) REFERENCES galette_types_cotisation (id_type_cotis) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE galette_helloasso_settings (
	id_setting tinyint(1) NOT NULL default 0,
	org_slug varchar(100) NOT NULL,
	client_id text,
	client_secret text,
	access_token text,
	access_expires_at datetime,
	refresh_token text,
	refresh_expires_at datetime,
	test_mode tinyint(1) default 1,
	PRIMARY KEY (id_setting),
	CONSTRAINT helloasso_settings_singleton CHECK (id_setting=0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
