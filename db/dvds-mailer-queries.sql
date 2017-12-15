SELECT * FROM USER WHERE mail_sent='1'

SELECT COUNT(*) FROM USER WHERE mail_sent='0'

SELECT * FROM USER WHERE mail_sent='0'


SELECT * FROM USER WHERE mail_sent='0' 
UNION 
SELECT * FROM USER WHERE failure='1' AND no_of_attempts <= 5 


SELECT * FROM USER WHERE failure='1' ORDER BY no_of_attempts DESC


SELECT COUNT(*) FROM USER WHERE failure='1'

SELECT COUNT(*) FROM USER WHERE failure='1' AND no_of_attempts <= 5


SELECT COUNT(*) FROM USER WHERE no_of_attempts > 5

