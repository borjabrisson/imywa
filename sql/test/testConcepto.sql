-- Estructura de tabla para la tabla `concepto`
--

CREATE TABLE IF NOT EXISTS `concepto` (
  `idconcepto` varchar(20) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `caption` varchar(30) NOT NULL,
  `tipo` enum('editable','fijo','calculado') NOT NULL DEFAULT 'editable',
  `formato` enum('numérico','moneda','booleano','opción') NOT NULL DEFAULT 'numérico',
  `opcionstring` varchar(250) DEFAULT NULL,
  `imagenstring` varchar(80) DEFAULT NULL,
  `datomultiple` int(11) NOT NULL DEFAULT '0',
  `valor` double DEFAULT NULL,
  `calcorden` int(11) DEFAULT NULL,
  `distribuirvalor` int(11) DEFAULT NULL,
  `sumarizeoper` enum('media','suma','mínimo','máximo','moda') DEFAULT NULL,
  `fechavalor` enum('fecha inicio periodo','fecha factura') DEFAULT NULL,
  `alerta` int(11) NOT NULL DEFAULT '0',
  `alertimage` int(11) NOT NULL DEFAULT '0',
  `alertmincount` int(11) NOT NULL DEFAULT '1',
  `alertrepe` int(11) DEFAULT NULL,
  `dynclass` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`idconcepto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `concepto`
--

INSERT INTO `concepto` (`idconcepto`, `descripcion`, `caption`, `tipo`, `formato`, `opcionstring`, `imagenstring`, `datomultiple`, `valor`, `calcorden`, `distribuirvalor`, `sumarizeoper`, `fechavalor`, `alerta`, `alertimage`, `alertmincount`, `alertrepe`, `dynclass`) VALUES
('ALREXCESOCONS', 'Indica si existe exceso de consumo en base al consumo medio del punto de suministro', 'Alarma ''Exceso Consumo''', 'calculado', 'booleano', NULL, '1,3', 0, NULL, 38, 0, 'máximo', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('ALREXCESOREACT', 'Indica si existe penalización por exceso de energía reactiva', 'Alarma ''Exceso Reactiva''', 'calculado', 'booleano', NULL, '1,3', 0, NULL, 35, 0, 'máximo', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('ALREXCESOREACT_GRAVE', 'Indica si es viable la adquisición de equipos para correguir el exceso de energía reactiva', 'Inversión en Equipos', 'calculado', 'booleano', NULL, '1,3', 0, NULL, 43, 0, 'máximo', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('ALRPOTCONTR', 'Indica si existe penalización por defecto/exceso de potencia contratada', 'Alarma ''Potencia Contratada''', 'calculado', 'booleano', NULL, '1,3', 0, NULL, 36, 0, 'máximo', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('COCIENTEMT', NULL, 'Cociente del Termino Temporal ', 'calculado', 'numérico', NULL, NULL, 0, NULL, 10, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('CONSDISCRIM', NULL, 'Consumo Discrim horaria (0)', 'fijo', 'numérico', NULL, NULL, 1, NULL, NULL, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('CONSDISCRIMEDIT', NULL, 'Consumo Discrim Horaria', 'editable', 'moneda', NULL, NULL, 1, NULL, NULL, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('CONSMAXMEDIODIARIO', NULL, 'Consumo Medio Máximo Diario', 'fijo', 'numérico', NULL, NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('CONSUMO', NULL, 'Consumo (kW·h)', 'editable', 'moneda', NULL, NULL, 1, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('CONSUMOGRAL', 'Indica el valor total de kW·h ', 'Consumo Total (kW·h)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 1, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('COSPHI', NULL, 'Coseno de Phi', 'calculado', 'numérico', NULL, NULL, 1, NULL, 6, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('COSPHITOT', 'Indica la relación entre la energía activa (kW·h) y la energía reactiva (kVAr·h): un valor menor de 0.95 supone penalización en la facturación por exceso de energía reactiva', 'Coseno(Phi) Total (0...1)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 19, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('CUADREFACT', 'Indica si la facturación ha sido correcta, errónea o ha habido ajuste temporal en conpeto de potencia contratada (0-Cuadrada,1-Ajuste temporal,2-Error de factura)', 'Alarma ''Cuadre Factura''', 'calculado', 'opción', 'Cuadrada,Ajuste temporal,Error de factura', '1,5,6', 0, NULL, 34, 0, 'moda', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('DELTATOTFACT', 'Indica la diferencia entre lo que se ha pagado y lo que se debería haber pagado: una cantidad negativa indica que se ha pagado menos y una positiva indica que se ha pagado de más', 'Dif [Fact Emit - Fact] (€)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 23, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, 'errorrow'),
('DELTATOTFACTAUX', NULL, 'Dif. factura tt1 - calculada', 'calculado', 'moneda', NULL, NULL, 0, NULL, 30, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('EQUIPMED', NULL, 'Equipos de medida (€)', 'editable', 'moneda', NULL, NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('EURCONSUMO', NULL, 'Euros por consumo', 'fijo', 'numérico', NULL, NULL, 1, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('EURDISCRIM', NULL, 'Euros discriminación', 'fijo', 'numérico', NULL, NULL, 1, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('EUREXCESOPOT', NULL, 'Euros por exceso de potencia', 'fijo', 'numérico', NULL, NULL, 1, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('EURPOT', NULL, 'Euros por potencia', 'fijo', 'numérico', NULL, NULL, 1, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('EURREACT', NULL, 'Euros por reactiva', 'calculado', 'numérico', NULL, NULL, 1, NULL, 7, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('EXCESOPOT', NULL, 'Exceso de potencia', 'calculado', 'numérico', NULL, NULL, 1, NULL, 2, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('EXCESOREACT', 'Indica la cantidad de energía reactiva (KVAr·h) a facturar', 'Exceso de reactiva', 'calculado', 'numérico', NULL, NULL, 1, NULL, 8, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('EXTRACONS', NULL, 'Extra consumo (€)', 'editable', 'moneda', NULL, NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('LECTURAESTIMADA', NULL, 'Lectura Estimada', 'editable', 'booleano', NULL, NULL, 0, NULL, NULL, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('MODFACTPOT', '0-Diario, 1-Mes', 'Modo facturación potencia', 'fijo', 'opción', 'Diario,Mes', NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('MODPOTFACT', '1-Ajustada,0-No ajustada', 'Modo Potencia Facturada', 'fijo', 'opción', 'No ajustada,Ajustada', NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('OTRCONCEP', NULL, 'Otros Equipos de Medida (€)', 'editable', 'moneda', NULL, NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('PORCENIGIC', NULL, 'IGIC Normalizado', 'fijo', 'numérico', NULL, NULL, 0, NULL, NULL, 0, 'media', 'fecha factura', 0, 0, 1, NULL, NULL),
('PORCENIGICREDUC', NULL, 'IGIC Reducido', 'fijo', 'numérico', NULL, NULL, 0, NULL, NULL, 0, 'media', 'fecha factura', 0, 0, 1, NULL, NULL),
('PORCENIVA', NULL, 'Porcentaje IVA', 'fijo', 'numérico', NULL, NULL, 0, NULL, NULL, 0, 'media', 'fecha factura', 0, 0, 1, NULL, NULL),
('PORCENTCONSUMO1', 'Indica el porcentaje de kW·h en P1 respecto del total', 'Porcentaje Consumo P1 (%)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 40, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('PORCENTCONSUMO2', 'Indica el porcentaje de kW·h en P2 respecto del total', 'Porcentaje Consumo P2 (%)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 41, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('PORCENTCONSUMO3', 'Indica el porcentaje de kW·h en P3 respecto del total', 'Porcentaje Consumo P3 (%)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 42, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('PORCENTCONSUMO4', 'Indica el porcentaje de kW·h en P4 respecto del total', 'Porcentaje Consumo P4 (%)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 44, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('PORCENTCONSUMO5', 'Indica el porcentaje de kW·h en P5 respecto del total', 'Porcentaje Consumo P5 (%)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 45, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('PORCENTCONSUMO6', 'Indica el porcentaje de kW·h en P6 respecto del total', 'Porcentaje Consumo P6 (%)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 46, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('PORCENTIMP', 'Indica el porcentaje de los impuestos y términos auxiliares respecto al total de la facturada emitida', 'Porcentaje Impuestos (%)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 52, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('PORCENTOTCONSGRAL', 'Indica el porcentaje de la facturación del consumo respecto del total de la factura emitida', 'Porcentaje Consumo (%)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 48, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('PORCENTOTPOTGRAL', 'Indica el porcentaje de la potencia facturada respecto al total de la factura emitida', 'Porcentaje Potencia (%)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 49, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('PORCENTOTREACTGRAL', 'Indica el porcentaje de la reactiva facturada respecto al total de la factura emitida', 'Porcentaje Reactiva (%)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 50, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('POTCONTR', NULL, 'Potencia contratada', 'fijo', 'numérico', NULL, NULL, 1, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('POTCONTRMEDIA', 'Indica el valor de la Potencia Contratada Media', 'Potencia Contratada (kW)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 51, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('POTFACT', NULL, 'Potencia facturada', 'calculado', 'numérico', NULL, NULL, 1, NULL, 5, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('POTFACT0', NULL, 'Potencia facturada no ajustada', 'calculado', 'numérico', NULL, NULL, 1, NULL, 3, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('POTFACT1', NULL, 'Potencia facturada ajustada', 'calculado', 'numérico', NULL, NULL, 1, NULL, 4, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('POTREG', NULL, 'Potencia registrada (kW)', 'editable', 'moneda', NULL, NULL, 1, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('RATIOCONSGRAL', 'Indica el consumo medio diario en kW·h', 'Ratio Consumo (kW·h/día)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 37, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('RATIOFACT', 'Indica el valor en € facturado cada día del periodo', 'Ratio Económico (€/día)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 39, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('REACT', NULL, 'Reactiva (kVAr·h)', 'editable', 'moneda', NULL, NULL, 1, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('REACT80', NULL, 'Reactiva tramo hasta 0.80 ', 'fijo', 'numérico', NULL, NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('REACT85', NULL, 'Reactiva tramo 0.80 - 0.85', 'fijo', 'numérico', NULL, NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('REACT90', NULL, 'Reactiva tramo 0.85 - 0.90', 'fijo', 'numérico', NULL, NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('REACT95', NULL, 'Reactiva tramo 0.90 - 0.95', 'fijo', 'numérico', NULL, NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('RECARGOEXCESOPOT61', NULL, 'Recargo exceso de potencia MT', 'editable', 'moneda', NULL, NULL, 0, 0, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('RECARGOTUR', NULL, 'Penalización por M.R.', 'editable', 'booleano', NULL, NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('REDONDEOMT', 'Termino que procede al redondeo del termino temporal en MT', 'Rendondeo Termino Temporal MT', 'calculado', 'numérico', NULL, NULL, 0, NULL, 11, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTCONS', NULL, 'Total consumo periodo', 'calculado', 'numérico', NULL, NULL, 1, NULL, 9, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTCONSGRAL', 'Indica el valor en € facturado en concepto de consumo (KW·h)', 'Total Consumo (€)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 12, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTFACTAJUST', NULL, 'Total factura ajustado', 'calculado', 'numérico', NULL, NULL, 0, NULL, 32, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTFACTAUXGRAL', NULL, 'Tot. Fact termino temporal 1', 'calculado', 'numérico', NULL, NULL, 0, NULL, 29, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTFACTAUXGRALCC', NULL, 'Total fac. tt1 con consumo', 'calculado', 'numérico', NULL, NULL, 0, NULL, 27, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTFACTAUXGRALSC', NULL, 'Tot. Fact. tt1 sin consumo', 'calculado', 'numérico', NULL, NULL, 0, NULL, 28, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTFACTCC', NULL, 'Total factura con consumo', 'calculado', 'numérico', NULL, NULL, 0, NULL, 21, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTFACTCORRECTO', NULL, 'Total de factura correcto', 'calculado', 'numérico', NULL, NULL, 0, NULL, 31, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTFACTDESCUADRE', NULL, 'Descuadre en total factura', 'calculado', 'numérico', NULL, NULL, 0, NULL, 33, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTFACTGRAL', 'Indica el valor total en € que se debería haber facturado según contrato y normas ITC de aplicación, incluyendo impuestos ', 'Total Factura (€)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 22, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTFACTREAL', 'Indica el valor en € facturado por la compañía comercializadora eléctrica', 'Total Factura Emitida (€)', 'editable', 'moneda', NULL, NULL, 0, NULL, NULL, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTFACTSC', NULL, 'Total factura sin consumo', 'calculado', 'numérico', NULL, NULL, 0, NULL, 20, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTPOTAUX', NULL, 'Tot pot termino temporal 1', 'calculado', 'numérico', NULL, NULL, 1, NULL, 24, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTPOTAUXGRAL', NULL, 'Total potencia termino t 1', 'calculado', 'numérico', NULL, NULL, 0, NULL, 25, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTPOTGRAL', 'Indica el valor en € en concepto de potencia contratada', 'Total Potencia (€)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 15, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTPOTPERDIA', NULL, 'Total potencia per días ', 'calculado', 'numérico', NULL, NULL, 1, NULL, 13, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTPOTPERMES', NULL, 'Total potencia per mes', 'calculado', 'numérico', NULL, NULL, 1, NULL, 14, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTREACT', NULL, 'Total reactiva periodo', 'calculado', 'numérico', NULL, NULL, 1, NULL, 16, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTREACTGRAL', 'Indical el valor en € facturado en concepto de energía reactiva (KVAr·h)', 'Total Reactiva (€)', 'calculado', 'moneda', NULL, NULL, 0, NULL, 17, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTRECARGO', NULL, 'Total recargo', 'calculado', 'numérico', NULL, NULL, 0, NULL, 18, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('TOTRECARGOAUX', NULL, 'Total recargo potencia auxilia', 'calculado', 'numérico', NULL, NULL, 0, NULL, 26, 1, 'suma', 'fecha inicio periodo', 0, 0, 1, NULL, NULL),
('VALORMEDIO_EUROCONS', 'Indica el valor medio en € del término de energía facturado, teniendo en cuenta la proporción del consumo en cada periodo', 'Precio Medio T.E. (€/kW·h)', 'calculado', 'numérico', NULL, NULL, 0, NULL, 47, 0, 'media', 'fecha inicio periodo', 0, 0, 1, NULL, NULL);
