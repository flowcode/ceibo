CREATE  TABLE `ovni` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE  TABLE `weapon` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE  TABLE `ovni_weapon` (
  `id_ovni` INT NOT NULL ,
  `id_weapon` INT NOT NULL ,
  PRIMARY KEY (`id_ovni`, `id_weapon`) )
ENGINE = InnoDB;
