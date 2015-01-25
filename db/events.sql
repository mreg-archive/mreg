DELIMITER //

SET NAMES 'utf8'//

DROP EVENT IF EXISTS `clear_session`//
CREATE EVENT `clear_session`
	ON SCHEDULE EVERY 5 MINUTE
	COMMENT 'Remove hour old sessions, logout 30 min old.'
	DO
	BEGIN
		-- DROP HOUR OLD SESSIONS
		DELETE FROM `mreg`.`sys__Session`
			WHERE UNIX_TIMESTAMP() - 36000 > `updated`;
		
		-- LOGOUT 30 MINUTES OLD SESSIONS
		UPDATE `mreg`.`sys__Session`
			SET `error` = 'Du loggades ut på grund av över 30 minuters inaktivitet'
			WHERE UNIX_TIMESTAMP() - 1800 > `updated`;
	END//

DELIMITER ;
