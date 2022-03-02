create database fox;

use fox;

create table proxy(
    id_proxy int,
    modo BINARY,
    ip4_net varchar(20),
    ip6_net varchar(60),
    PRIMARY KEY (id_proxy)
);
create table interfaces(
    nome varchar(10),
    tipo SET('fisica', 'virtual'),
    funcao SET('lan', 'wan'),
    mac varchar(17),
    addressing SET('dhcp', 'estatico') default 'dhcp',
    status SET('ativo', 'inativo') default 'ativo',
    id_proxy INT,
    PRIMARY KEY (nome),
    FOREIGN KEY (id_proxy) REFERENCES proxy(id_proxy)
);

create table ipv4(
    ipv4 varchar(15),
    ipv4_mask varchar(15),
    ipv4_gw varchar(15),
    ipv4_net varchar(15),
    ipv4_cidr varchar(3),
    ipv4_bcast varchar(15),
    ipv4_interface varchar(10),
    PRIMARY KEY (ipv4),
    FOREIGN KEY (ipv4_interface) REFERENCES interfaces(nome)
);

create table ipv6(
    ipv6 varchar(50),
    ipv6_mask varchar(50),
    ipv6_gw varchar(50),
    ipv6_net varchar(50),
    ipv6_cidr varchar(10),
    ipv6_bcast varchar(50),
    ipv6_interface varchar(10),
    PRIMARY KEY (ipv6),
    FOREIGN KEY (ipv6_interface) REFERENCES interfaces(nome)
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


