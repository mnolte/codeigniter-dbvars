codeigniter-dbvars
==================

Store vars in a database

### Structure
```SQL
CREATE TABLE IF NOT EXISTS `vars` (
  `var_key` varchar(255) NOT NULL,
  `var_value` text,
  PRIMARY KEY (`var_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `vars_translations` (
  `var_key` varchar(255) NOT NULL,
  `language` enum('en','nl') NOT NULL DEFAULT 'en',
  `var_value` text NOT NULL,
  PRIMARY KEY (`var_key`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `vars_translations`
  ADD CONSTRAINT `vars_translations_ibfk_1` FOREIGN KEY (`var_key`) REFERENCES `vars` (`var_key`) ON DELETE CASCADE ON UPDATE CASCADE;
```
