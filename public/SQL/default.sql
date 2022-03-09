create database fox;

use fox;

create table interfaces(
    nome varchar(10),
    tipo varchar(10),
    funcao varchar(10),
    mac varchar(17),
    addressing varchar(10) default 'dhcp',
    ipv4 varchar(15),
    ip4_mask varchar(15),
    ip4_gw varchar(15),
    ip4_net varchar(15),
    ip4_cidr varchar(3),
    ip4_bcast varchar(15),
    ip4_dns varchar(15),
    ip6_mask varchar(15),
    ip6_gw varchar(15),
    ip6_net varchar(15),
    ip6_cidr varchar(3),
    ip6_bcast varchar(15),
    ip6_dns varchar(15),
    status varchar(10) default 'ativo',
    id_proxy INT,
    PRIMARY KEY (nome),
    FOREIGN KEY (id_proxy) REFERENCES proxy(id_proxy)
);

INSERT INTO interfaces_fisicas VALUES('eth1', 'aa:bb:cc:dd:ee:ff', 'lan', 'estatico', 'ativo', NULL);
INSERT INTO interfaces_virtuais VALUES('eth1:0', 'lan', 'estatico', 'ativo', 'eth1', NULL);
INSERT INTO interfaces_virtuais VALUES('eth1:1', 'lan', 'estatico', 'ativo', 'eth1', NULL);
INSERT INTO interfaces_virtuais VALUES('eth2:0', 'wan', 'estatico', 'ativo', 'eth2', NULL);



create table ipv4(
    ipv4 varchar(15),
    ip4_mask varchar(15),
    ip4_gw varchar(15),
    ip4_net varchar(15),
    ip4_cidr varchar(3),
    ip4_bcast varchar(15),
    interface varchar(10),
    PRIMARY KEY (ip),
    FOREIGN KEY (interface) REFERENCES interfaces(nome)
);

create table ipv6(
    ip varchar(50),
    mask varchar(50),
    gw varchar(50),
    net varchar(50),
    mask_cidr varchar(10),
    bcast varchar(50),
    interface varchar(10),
    PRIMARY KEY (ip),
    FOREIGN KEY (interface) REFERENCES interfaces(interface)
);


create table proxy(
    id_proxy int,
    modo BINARY,
    ip4_net varchar(20),
    ip6_net varchar(60),
    PRIMARY KEY (id_proxy)
);

CREATE TABLE `dhcp_sett` (
  id_dhcp INT,
  descri varchar(10),
  fqdn varchar(30),
  ip4_start varchar(15),
  ip4_end varchar(15),
  ip4_dns varchar(50),
  ip4_gw varchar(15),
  ip4_wpad varchar(50),
  ip4_status BINARY,
  ip6_start varchar(50),
  ip6_end varchar(50),
  ip6_gw varchar(50),
  ip6_dns varchar(50),
  ip6_wpad varchar(50),
  ip6_status BIN,
  interface varchar,
  PRIMARY KEY
);


