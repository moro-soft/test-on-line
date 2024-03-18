-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 24 2023 г., 12:19
-- Версия сервера: 5.1.73
-- Версия PHP: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- База данных: `opros`
--

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'код',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user` int(10) NOT NULL,
  `test` int(10) DEFAULT '0' COMMENT 'код теста',
  `sec` int(7) DEFAULT '0' COMMENT 'затрачено секунд',
  `start` datetime NOT NULL COMMENT 'начало теста',
  `verno` int(1) NOT NULL DEFAULT '0' COMMENT 'верно',
  `vopros` int(20) DEFAULT '0' COMMENT 'код вопроса',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Сессии тестирования' AUTO_INCREMENT=240 ;

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `session_view_rez`
--
CREATE TABLE IF NOT EXISTS `session_view_rez` (
`id_sess` int(10)
,`time` timestamp
,`ip` varchar(20)
,`user` int(10)
,`test` int(10)
,`sec` int(7)
,`start` datetime
,`verno_otv` int(1)
,`fio` varchar(30)
,`email` varchar(30)
,`last` datetime
,`result` float
,`gde` varchar(50)
,`id` int(10)
,`vopros` int(10)
,`kategoria` int(10)
,`spec` int(10)
,`tema` int(10)
,`tema_` text
,`vopros_` text
,`otvet_` text
,`kategoria_` text
,`spec_` text
);
-- --------------------------------------------------------

--
-- Дублирующая структура для представления `session_view_rez_tema`
--
CREATE TABLE IF NOT EXISTS `session_view_rez_tema` (
`time` timestamp
,`ip` varchar(20)
,`user` int(10)
,`start` datetime
,`fio` varchar(30)
,`email` varchar(30)
,`tema` int(10)
,`tema_` text
,`kategoria` int(10)
,`kategoria_` text
,`spec` int(10)
,`spec_` text
,`sec` int(7)
,`verno_otv` decimal(32,0)
,`ne_verno_otv` decimal(33,0)
,`voprosov_otv` decimal(23,0)
);
-- --------------------------------------------------------

--
-- Структура таблицы `tests`
--

CREATE TABLE IF NOT EXISTS `tests` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'код',
  `vopros` int(10) NOT NULL DEFAULT '0' COMMENT 'код вопроса',
  `otvet` int(10) DEFAULT '0' COMMENT 'вариант ответа',
  `verno` int(1) DEFAULT '0' COMMENT '1-правильный',
  `kategoria` int(10) DEFAULT '0' COMMENT 'Категория',
  `spec` int(10) DEFAULT '0' COMMENT 'Специализация',
  `tema` int(10) DEFAULT '0' COMMENT 'Тема',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Тесты' AUTO_INCREMENT=385 ;

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `tests_view`
--
CREATE TABLE IF NOT EXISTS `tests_view` (
`id` int(10)
,`vopros` int(10)
,`otvet` int(10)
,`verno` int(1)
,`kategoria` int(10)
,`spec` int(10)
,`tema` int(10)
,`tema_` text
,`vopros_` text
,`otvet_` text
,`kategoria_` text
,`spec_` text
);
-- --------------------------------------------------------

--
-- Дублирующая структура для представления `tests_view_verno_otvetov_na_vopros`
--
CREATE TABLE IF NOT EXISTS `tests_view_verno_otvetov_na_vopros` (
`kategoria` int(10)
,`spec` int(10)
,`tema` int(10)
,`vopros` int(10)
,`verno` decimal(32,0)
);
-- --------------------------------------------------------

--
-- Дублирующая структура для представления `tests_view_voprosov_v_teme`
--
CREATE TABLE IF NOT EXISTS `tests_view_voprosov_v_teme` (
`spec` int(10)
,`kategoria` int(10)
,`tema` int(10)
,`spec_` text
,`kategoria_` text
,`tema_` text
,`voprosov` bigint(21)
);
-- --------------------------------------------------------

--
-- Структура таблицы `texts`
--

CREATE TABLE IF NOT EXISTS `texts` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ключ',
  `vid` varchar(1) COLLATE utf8_unicode_ci NOT NULL COMMENT 'вид',
  `text` text COLLATE utf8_unicode_ci COMMENT 'значение для вида : Вопрос Ответ Категория Специализация Тема ',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `vid` (`vid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Справочник текстов' AUTO_INCREMENT=122 ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'номе',
  `fio` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  `email` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'почта',
  `last` datetime NOT NULL COMMENT 'последний',
  `result` float NOT NULL COMMENT 'средний уровень',
  `gde` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'подразделение',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`fio`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Тестируемые' AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура для представления `session_view_rez`
--
DROP TABLE IF EXISTS `session_view_rez`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `test`.`session_view_rez` AS select `A`.`id` AS `id_sess`,`A`.`time` AS `time`,`A`.`ip` AS `ip`,`A`.`user` AS `user`,`A`.`test` AS `test`,`A`.`sec` AS `sec`,`A`.`start` AS `start`,`A`.`verno` AS `verno_otv`,`U`.`fio` AS `fio`,`U`.`email` AS `email`,`U`.`last` AS `last`,`U`.`result` AS `result`,`U`.`gde` AS `gde`,`B`.`id` AS `id`,`B`.`vopros` AS `vopros`,`B`.`kategoria` AS `kategoria`,`B`.`spec` AS `spec`,`B`.`tema` AS `tema`,`T`.`text` AS `tema_`,`V`.`text` AS `vopros_`,`O`.`text` AS `otvet_`,`K`.`text` AS `kategoria_`,`S`.`text` AS `spec_` from ((`test`.`sessions` `A` left join (((((`test`.`tests` `B` left join `test`.`texts` `T` on((`B`.`tema` = `T`.`id`))) left join `test`.`texts` `V` on((`B`.`vopros` = `V`.`id`))) left join `test`.`texts` `O` on((`B`.`otvet` = `O`.`id`))) left join `test`.`texts` `K` on((`B`.`kategoria` = `K`.`id`))) left join `test`.`texts` `S` on((`B`.`spec` = `S`.`id`))) on((`A`.`test` = `B`.`id`))) left join `test`.`users` `U` on((`A`.`user` = `U`.`id`))) order by 1;

-- --------------------------------------------------------

--
-- Структура для представления `session_view_rez_tema`
--
DROP TABLE IF EXISTS `session_view_rez_tema`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `test`.`session_view_rez_tema` AS select `V`.`time` AS `time`,`V`.`ip` AS `ip`,`V`.`user` AS `user`,`V`.`start` AS `start`,`V`.`fio` AS `fio`,`V`.`email` AS `email`,`V`.`tema` AS `tema`,`V`.`tema_` AS `tema_`,`V`.`kategoria` AS `kategoria`,`V`.`kategoria_` AS `kategoria_`,`V`.`spec` AS `spec`,`V`.`spec_` AS `spec_`,`V`.`sec` AS `sec`,sum(`V`.`verno_otv`) AS `verno_otv`,sum((1 - `V`.`verno_otv`)) AS `ne_verno_otv`,sum(1) AS `voprosov_otv` from `test`.`session_view_rez` `V` group by 1,2,3,4,5,6,7,8,9,10,11,12,13;

-- --------------------------------------------------------

--
-- Структура для представления `tests_view`
--
DROP TABLE IF EXISTS `tests_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `test`.`tests_view` AS select `B`.`id` AS `id`,`B`.`vopros` AS `vopros`,`B`.`otvet` AS `otvet`,`B`.`verno` AS `verno`,`B`.`kategoria` AS `kategoria`,`B`.`spec` AS `spec`,`B`.`tema` AS `tema`,`T`.`text` AS `tema_`,`V`.`text` AS `vopros_`,`O`.`text` AS `otvet_`,`K`.`text` AS `kategoria_`,`S`.`text` AS `spec_` from (((((`test`.`tests` `B` left join `test`.`texts` `T` on((`B`.`tema` = `T`.`id`))) left join `test`.`texts` `V` on((`B`.`vopros` = `V`.`id`))) left join `test`.`texts` `O` on((`B`.`otvet` = `O`.`id`))) left join `test`.`texts` `K` on((`B`.`kategoria` = `K`.`id`))) left join `test`.`texts` `S` on((`B`.`spec` = `S`.`id`))) order by `B`.`id`;

-- --------------------------------------------------------

--
-- Структура для представления `tests_view_verno_otvetov_na_vopros`
--
DROP TABLE IF EXISTS `tests_view_verno_otvetov_na_vopros`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `test`.`tests_view_verno_otvetov_na_vopros` AS select `test`.`tests`.`kategoria` AS `kategoria`,`test`.`tests`.`spec` AS `spec`,`test`.`tests`.`tema` AS `tema`,`test`.`tests`.`vopros` AS `vopros`,sum(`test`.`tests`.`verno`) AS `verno` from `test`.`tests` group by 1,2,3,4;

-- --------------------------------------------------------

--
-- Структура для представления `tests_view_voprosov_v_teme`
--
DROP TABLE IF EXISTS `tests_view_voprosov_v_teme`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `test`.`tests_view_voprosov_v_teme` AS select `tests_view`.`spec` AS `spec`,`tests_view`.`kategoria` AS `kategoria`,`tests_view`.`tema` AS `tema`,`tests_view`.`spec_` AS `spec_`,`tests_view`.`kategoria_` AS `kategoria_`,`tests_view`.`tema_` AS `tema_`,count(distinct `tests_view`.`vopros`) AS `voprosov` from `test`.`tests_view` group by 1,2,3,4,5,6;


-- --------------------------------------------------------

--
-- Структура таблицы `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test` int(10) DEFAULT NULL COMMENT 'код ответа пользователя',
  `verno` int(1) DEFAULT '0' COMMENT 'верно отвечено',
  `user` int(10) DEFAULT NULL COMMENT 'код ответившего',
  `session` int(10) NOT NULL COMMENT 'код сессии',
  `rating` tinyint(1) DEFAULT '0' COMMENT 'рейтинг',
  `mess` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'комментарий',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=251 ;


--
-- Дублирующая структура для представления `history_view`
--
CREATE TABLE IF NOT EXISTS `history_view` (
`rating` tinyint(1)
,`mess` varchar(30)
,`test_otvet` int(10)
,`verno_otvet` int(1)
,`id_sess` int(10)
,`time` timestamp
,`ip` varchar(20)
,`user` int(10)
,`test` int(10)
,`sec` int(7)
,`start` datetime
,`verno_otv` int(1)
,`fio` varchar(30)
,`email` varchar(30)
,`last` datetime
,`result` float
,`id` int(10)
,`vopros` int(10)
,`kategoria` int(10)
,`spec` int(10)
,`tema` int(10)
,`tema_` text
,`vopros_` text
,`otvet_` text
,`kategoria_` text
,`spec_` text
,`gde` varchar(50)
);
-- --------------------------------------------------------

--
-- Структура для представления `history_view`
--
DROP TABLE IF EXISTS `history_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `test`.`history_view` AS select `A`.`rating` AS `rating`,`A`.`mess` AS `mess`,`A`.`test` AS `test_otvet`,`A`.`verno` AS `verno_otvet`,`B`.`id_sess` AS `id_sess`,`B`.`time` AS `time`,`B`.`ip` AS `ip`,`B`.`user` AS `user`,`B`.`test` AS `test`,`B`.`sec` AS `sec`,`B`.`start` AS `start`,`B`.`verno_otv` AS `verno_otv`,`B`.`fio` AS `fio`,`B`.`email` AS `email`,`B`.`last` AS `last`,`B`.`result` AS `result`,`B`.`id` AS `id`,`B`.`vopros` AS `vopros`,`B`.`kategoria` AS `kategoria`,`B`.`spec` AS `spec`,`B`.`tema` AS `tema`,`B`.`tema_` AS `tema_`,`B`.`vopros_` AS `vopros_`,`T`.`otvet_` AS `otvet_`,`B`.`kategoria_` AS `kategoria_`,`B`.`spec_` AS `spec_`,`B`.`gde` AS `gde` from ((`test`.`history` `A` left join `test`.`session_view_rez` `B` on((`A`.`session` = `B`.`id_sess`))) left join `test`.`tests_view` `T` on((`A`.`test` = `T`.`id`)));
